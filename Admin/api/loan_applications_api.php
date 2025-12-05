<?php
/**
 * Loan Applications API
 * Handles fetching, viewing, approving, rejecting, and deleting loan applications
 */

require_once 'DBcon.php';
require_once 'loan_email_helper.php';

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
            getAllLoanApplications($conn);
            break;
        
        case 'get_details':
            getLoanApplicationDetails($conn);
            break;
        
        case 'approve':
            approveLoanApplication($conn);
            break;
        
        case 'reject':
            rejectLoanApplication($conn);
            break;
        
        case 'delete':
            deleteLoanApplication($conn);
            break;
        
        case 'get_stats':
            getLoanStats($conn);
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

// Get all loan applications with pagination and filtering
function getAllLoanApplications($conn) {
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? max(1, min(100, intval($_GET['limit']))) : 10;
    $offset = ($page - 1) * $limit;
    
    $status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';
    $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
    
    // Build WHERE clause
    $where = ["la.application_status != 'Deleted'"];
    
    if (!empty($status)) {
        $where[] = "la.application_status = '$status'";
    }
    
    if (!empty($search)) {
        $where[] = "(u.first_name LIKE '%$search%' OR u.last_name LIKE '%$search%' OR u.email LIKE '%$search%' OR la.loan_platform LIKE '%$search%')";
    }
    
    $whereClause = implode(' AND ', $where);
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total 
                 FROM loan_applications la 
                 JOIN users u ON la.user_id = u.user_id 
                 WHERE $whereClause";
    $countResult = $conn->query($countSql);
    $total = $countResult->fetch_assoc()['total'];
    
    // Get applications
    $sql = "SELECT 
                la.application_id,
                la.user_id,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                u.email as farmer_email,
                u.phone as farmer_phone,
                la.loan_platform,
                la.loan_amount,
                la.loan_purpose,
                la.repayment_period_months,
                la.bank_name,
                la.account_number,
                la.account_name,
                la.document_paths,
                la.application_status,
                la.created_at,
                la.updated_at
            FROM loan_applications la
            JOIN users u ON la.user_id = u.user_id
            WHERE $whereClause
            ORDER BY la.created_at DESC
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

// Get detailed information about a specific loan application
function getLoanApplicationDetails($conn) {
    $application_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($application_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid application ID']);
        return;
    }
    
    $sql = "SELECT 
                la.*,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                u.email as farmer_email,
                u.phone as farmer_phone,
                u.address as farmer_address,
                u.farm_name,
                u.farm_size,
                u.farming_experience,
                u.profile_picture
            FROM loan_applications la
            JOIN users u ON la.user_id = u.user_id
            WHERE la.application_id = ?";
    
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

// Approve a loan application
function approveLoanApplication($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $application_id = isset($data['id']) ? intval($data['id']) : 0;
    $admin_notes = isset($data['notes']) ? $conn->real_escape_string($data['notes']) : '';
    
    if ($application_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid application ID']);
        return;
    }
    
    // First, get the application details for email
    $detailsSql = "SELECT 
                    la.*,
                    CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                    u.email as farmer_email
                FROM loan_applications la
                JOIN users u ON la.user_id = u.user_id
                WHERE la.application_id = ? AND la.application_status = 'Pending'";
    
    $detailsStmt = $conn->prepare($detailsSql);
    $detailsStmt->bind_param('i', $application_id);
    $detailsStmt->execute();
    $detailsResult = $detailsStmt->get_result();
    
    if ($detailsResult->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Application not found or already processed']);
        return;
    }
    
    $applicationData = $detailsResult->fetch_assoc();
    
    // Update the application status
    $sql = "UPDATE loan_applications 
            SET application_status = 'Approved',
                admin_notes = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE application_id = ? AND application_status = 'Pending'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $admin_notes, $application_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // Send approval email to farmer
            $emailSent = sendLoanApprovalEmail(
                $applicationData['farmer_email'],
                $applicationData['farmer_name'],
                $applicationData,
                $admin_notes
            );
            
            $message = 'Loan application approved successfully';
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

// Reject a loan application
function rejectLoanApplication($conn) {
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
    
    // First, get the application details for email
    $detailsSql = "SELECT 
                    la.*,
                    CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                    u.email as farmer_email
                FROM loan_applications la
                JOIN users u ON la.user_id = u.user_id
                WHERE la.application_id = ? AND la.application_status = 'Pending'";
    
    $detailsStmt = $conn->prepare($detailsSql);
    $detailsStmt->bind_param('i', $application_id);
    $detailsStmt->execute();
    $detailsResult = $detailsStmt->get_result();
    
    if ($detailsResult->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Application not found or already processed']);
        return;
    }
    
    $applicationData = $detailsResult->fetch_assoc();
    
    // Update the application status
    $sql = "UPDATE loan_applications 
            SET application_status = 'Rejected',
                admin_notes = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE application_id = ? AND application_status = 'Pending'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $rejection_reason, $application_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // Send rejection email to farmer
            $emailSent = sendLoanRejectionEmail(
                $applicationData['farmer_email'],
                $applicationData['farmer_name'],
                $applicationData,
                $rejection_reason
            );
            
            $message = 'Loan application rejected';
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

// Soft delete a loan application (hide from frontend)
function deleteLoanApplication($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $application_id = isset($data['id']) ? intval($data['id']) : 0;
    
    if ($application_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid application ID']);
        return;
    }
    
    $sql = "UPDATE loan_applications 
            SET application_status = 'Deleted',
                updated_at = CURRENT_TIMESTAMP
            WHERE application_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $application_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Loan application deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Application not found']);
        }
    } else {
        throw new Exception('Failed to delete application: ' . $stmt->error);
    }
}

// Get loan application statistics
function getLoanStats($conn) {
    $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN application_status = 'Pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN application_status = 'Approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN application_status = 'Rejected' THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN application_status = 'Pending' THEN loan_amount ELSE 0 END) as pending_amount,
                SUM(CASE WHEN application_status = 'Approved' THEN loan_amount ELSE 0 END) as approved_amount
            FROM loan_applications
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
