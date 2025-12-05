<?php
/**
 * Farm Tools Applications API
 * Handles CRUD operations for farm tools applications
 */

require_once 'DBcon.php';
require_once 'farm_tools_email_helper.php';

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['cbn_user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'get_all':
            getAllApplications($conn);
            break;
        
        case 'get_details':
            getApplicationDetails($conn);
            break;
        
        case 'approve':
            approveApplication($conn);
            break;
        
        case 'reject':
            rejectApplication($conn);
            break;
        
        case 'delete':
            deleteApplication($conn);
            break;
        
        case 'get_stats':
            getStats($conn);
            break;
        
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

function getAllApplications($conn) {
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? max(1, min(100, intval($_GET['limit']))) : 10;
    $offset = ($page - 1) * $limit;
    
    $status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';
    $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
    
    $where = ["fta.application_status != 'Deleted'"];
    
    if (!empty($status)) {
        $where[] = "fta.application_status = '$status'";
    }
    
    if (!empty($search)) {
        $where[] = "(u.first_name LIKE '%$search%' OR u.last_name LIKE '%$search%' OR u.email LIKE '%$search%' OR fta.provider_name LIKE '%$search%')";
    }
    
    $whereClause = implode(' AND ', $where);
    
    $countSql = "SELECT COUNT(*) as total FROM farm_tools_applications fta JOIN users u ON fta.user_id = u.user_id WHERE $whereClause";
    $countResult = $conn->query($countSql);
    $total = $countResult->fetch_assoc()['total'];
    
    $sql = "SELECT 
                fta.application_id,
                fta.user_id,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                u.email as farmer_email,
                u.phone as farmer_phone,
                fta.provider_name,
                fta.tools_requested,
                fta.quantity_needed,
                fta.purpose,
                fta.farm_size,
                fta.application_status,
                fta.created_at,
                fta.updated_at
            FROM farm_tools_applications fta
            JOIN users u ON fta.user_id = u.user_id
            WHERE $whereClause
            ORDER BY fta.created_at DESC
            LIMIT $limit OFFSET $offset";
    
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Database error: " . $conn->error);
    }
    
    $applications = [];
    while ($row = $result->fetch_assoc()) {
        $applications[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $applications,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'pages' => ceil($total / $limit)
        ]
    ]);
}

function getApplicationDetails($conn) {
    $application_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($application_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid application ID']);
        return;
    }
    
    $sql = "SELECT 
                fta.*,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                u.email as farmer_email,
                u.phone as farmer_phone,
                u.address as farmer_address,
                u.farm_name,
                u.farm_size as user_farm_size
            FROM farm_tools_applications fta
            JOIN users u ON fta.user_id = u.user_id
            WHERE fta.application_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $application_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Application not found']);
        return;
    }
    
    $application = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'data' => $application
    ]);
}

function approveApplication($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $application_id = isset($data['id']) ? intval($data['id']) : 0;
    $admin_notes = isset($data['notes']) ? $conn->real_escape_string($data['notes']) : '';
    
    if ($application_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid application ID']);
        return;
    }
    
    // Get application details for email
    $detailsSql = "SELECT 
                    fta.*,
                    CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                    u.email as farmer_email
                FROM farm_tools_applications fta
                JOIN users u ON fta.user_id = u.user_id
                WHERE fta.application_id = ? AND fta.application_status = 'Pending'";
    
    $detailsStmt = $conn->prepare($detailsSql);
    $detailsStmt->bind_param('i', $application_id);
    $detailsStmt->execute();
    $detailsResult = $detailsStmt->get_result();
    
    if ($detailsResult->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Application not found or already processed']);
        return;
    }
    
    $applicationData = $detailsResult->fetch_assoc();
    
    // Update status
    $sql = "UPDATE farm_tools_applications 
            SET application_status = 'Approved',
                admin_notes = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE application_id = ? AND application_status = 'Pending'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $admin_notes, $application_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // Send approval email
            $emailSent = sendFarmToolsApprovalEmail(
                $applicationData['farmer_email'],
                $applicationData['farmer_name'],
                $applicationData,
                $admin_notes
            );
            
            $message = 'Farm tools application approved successfully';
            if ($emailSent) {
                $message .= ' and notification email sent to farmer';
            } else {
                $message .= ' but email notification failed';
            }
            
            echo json_encode(['success' => true, 'message' => $message]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Application not found or already processed']);
        }
    } else {
        throw new Exception('Failed to approve application: ' . $stmt->error);
    }
}

function rejectApplication($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $application_id = isset($data['id']) ? intval($data['id']) : 0;
    $rejection_reason = isset($data['reason']) ? $conn->real_escape_string($data['reason']) : '';
    
    if ($application_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid application ID']);
        return;
    }
    
    if (empty($rejection_reason)) {
        echo json_encode(['success' => false, 'message' => 'Rejection reason is required']);
        return;
    }
    
    // Get application details for email
    $detailsSql = "SELECT 
                    fta.*,
                    CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                    u.email as farmer_email
                FROM farm_tools_applications fta
                JOIN users u ON fta.user_id = u.user_id
                WHERE fta.application_id = ? AND fta.application_status = 'Pending'";
    
    $detailsStmt = $conn->prepare($detailsSql);
    $detailsStmt->bind_param('i', $application_id);
    $detailsStmt->execute();
    $detailsResult = $detailsStmt->get_result();
    
    if ($detailsResult->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Application not found or already processed']);
        return;
    }
    
    $applicationData = $detailsResult->fetch_assoc();
    
    // Update status
    $sql = "UPDATE farm_tools_applications 
            SET application_status = 'Rejected',
                admin_notes = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE application_id = ? AND application_status = 'Pending'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $rejection_reason, $application_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // Send rejection email
            $emailSent = sendFarmToolsRejectionEmail(
                $applicationData['farmer_email'],
                $applicationData['farmer_name'],
                $applicationData,
                $rejection_reason
            );
            
            $message = 'Farm tools application rejected';
            if ($emailSent) {
                $message .= ' and notification email sent to farmer';
            } else {
                $message .= ' but email notification failed';
            }
            
            echo json_encode(['success' => true, 'message' => $message]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Application not found or already processed']);
        }
    } else {
        throw new Exception('Failed to reject application: ' . $stmt->error);
    }
}

function deleteApplication($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $application_id = isset($data['id']) ? intval($data['id']) : 0;
    
    if ($application_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid application ID']);
        return;
    }
    
    $sql = "UPDATE farm_tools_applications 
            SET application_status = 'Deleted',
                updated_at = CURRENT_TIMESTAMP
            WHERE application_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $application_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Application deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Application not found']);
        }
    } else {
        throw new Exception('Failed to delete application: ' . $stmt->error);
    }
}

function getStats($conn) {
    $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN application_status = 'Pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN application_status = 'Approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN application_status = 'Rejected' THEN 1 ELSE 0 END) as rejected
            FROM farm_tools_applications
            WHERE application_status != 'Deleted'";
    
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Database error: " . $conn->error);
    }
    
    $stats = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'data' => $stats
    ]);
}

$conn->close();
?>
