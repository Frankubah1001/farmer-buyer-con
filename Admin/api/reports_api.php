<?php
// reports_api.php - API for managing buyer reports
header('Content-Type: application/json');
require_once 'DBcon.php';
// Start session to get admin user
session_start();

// Check if admin is logged in
if (!isset($_SESSION['cbn_user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// $database = new Database();
// $conn = $database->getConnection();

$input = file_get_contents('php://input');
$jsonData = json_decode($input, true);

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : (isset($jsonData['action']) ? $jsonData['action'] : 'get_reports'));

try {
    switch ($action) {
        case 'get_reports':
            getReports($conn);
            break;
        
        case 'get_report_details':
            getReportDetails($conn);
            break;
        
        case 'get_stats':
            getStats($conn);
            break;
        
        case 'resolve_report':
            resolveReport($conn, $jsonData);
            break;
        
        case 'delete_report':
            deleteReport($conn, $jsonData);
            break;
        
        case 'get_farmers':
            getFarmers($conn);
            break;
        
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

function getReports($conn) {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;
    
    $status = isset($_GET['status']) ? trim($_GET['status']) : '';
    $urgency = isset($_GET['urgency']) ? trim($_GET['urgency']) : '';
    $farmer_id = isset($_GET['farmer_id']) ? $_GET['farmer_id'] : '';
    $date = isset($_GET['date']) ? trim($_GET['date']) : '';
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    // Build base query
    $sql = "SELECT 
                r.report_id,
                r.buyer_id,
                r.cbn_user_id as farmer_id,
                r.order_number,
                r.produce_name,
                r.issue_type,
                r.reason,
                r.description,
                r.evidence,
                r.urgency_level,
                r.status,
                r.resolution_action,
                r.resolution_notes,
                r.created_at,
                r.updated_at,
                CONCAT(b.firstname, ' ', b.lastname) as buyer_name,
                b.email as buyer_email,
                CONCAT(f.first_name, ' ', f.last_name) as farmer_name,
                f.email as farmer_email
            FROM reports r
            LEFT JOIN buyers b ON r.buyer_id = b.buyer_id
            LEFT JOIN users f ON r.cbn_user_id = f.user_id
            WHERE 1=1";
    
    $countSql = "SELECT COUNT(*) as total 
                 FROM reports r
                 LEFT JOIN buyers b ON r.buyer_id = b.buyer_id
                 LEFT JOIN users f ON r.cbn_user_id = f.user_id
                 WHERE 1=1";
    
    $whereConditions = [];
    $params = [];
    $types = '';
    
    // Apply filters
    if (!empty($status)) {
        $whereConditions[] = "r.status = ?";
        $params[] = $status;
        $types .= 's';
    }
    
    if (!empty($urgency)) {
        $whereConditions[] = "r.urgency_level = ?";
        $params[] = $urgency;
        $types .= 's';
    }
    
    if (!empty($farmer_id)) {
        $whereConditions[] = "r.cbn_user_id = ?";
        $params[] = $farmer_id;
        $types .= 'i';
    }
    
    if (!empty($date)) {
        $whereConditions[] = "DATE(r.created_at) = ?";
        $params[] = $date;
        $types .= 's';
    }
    
    if (!empty($search)) {
        $whereConditions[] = "(CONCAT(b.firstname, ' ', b.lastname) LIKE ? 
                              OR CONCAT(f.first_name, ' ', f.last_name) LIKE ? 
                              OR r.issue_type LIKE ? 
                              OR r.order_number LIKE ? 
                              OR r.produce_name LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= 'sssss';
    }
    
    // Add WHERE conditions to both queries
    if (!empty($whereConditions)) {
        $whereClause = " AND " . implode(" AND ", $whereConditions);
        $sql .= $whereClause;
        $countSql .= $whereClause;
    }
    
    // Add ordering and pagination to main query
    $sql .= " ORDER BY r.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
    
    // Get total count
    $countStmt = $conn->prepare($countSql);
    if (!empty($whereConditions)) {
        $countParams = array_slice($params, 0, count($params) - 2); // Exclude limit and offset
        $countTypes = substr($types, 0, -2); // Exclude limit and offset types
        if (!empty($countParams)) {
            $countStmt->bind_param($countTypes, ...$countParams);
        }
    }
    
    if (!$countStmt->execute()) {
        error_log("Count query failed: " . $countStmt->error);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $countStmt->error]);
        return;
    }
    
    $countResult = $countStmt->get_result();
    $totalRow = $countResult->fetch_assoc();
    $totalRecords = $totalRow ? $totalRow['total'] : 0;
    $countStmt->close();
    
    // Get paginated data
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        return;
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
        $stmt->close();
        return;
    }
    
    $result = $stmt->get_result();
    $reports = [];
    
    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
    }
    
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'data' => [
            'reports' => $reports,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($totalRecords / $limit),
                'total_records' => (int)$totalRecords,
                'per_page' => $limit
            ]
        ]
    ]);
}

function getReportDetails($conn) {
    $report_id = isset($_GET['report_id']) ? (int)$_GET['report_id'] : 0;
    
    if ($report_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid report ID']);
        return;
    }
    
    // Using correct column names: buyers.firstname/lastname, users.first_name/last_name
    $sql = "SELECT 
                r.*,
                CONCAT(b.firstname, ' ', b.lastname) as buyer_name,
                b.email as buyer_email,
                b.phone as buyer_phone,
                CONCAT(f.first_name, ' ', f.last_name) as farmer_name,
                f.email as farmer_email,
                f.phone as farmer_phone
            FROM reports r
            LEFT JOIN buyers b ON r.buyer_id = b.buyer_id
            LEFT JOIN users f ON r.cbn_user_id = f.user_id
            WHERE r.report_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $report_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $report = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $report]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Report not found']);
    }
}

function getStats($conn) {
    // Total reports
    $totalSql = "SELECT COUNT(*) as total FROM reports";
    $totalResult = $conn->query($totalSql);
    $total = $totalResult->fetch_assoc()['total'];
    
    // Resolved reports
    $resolvedSql = "SELECT COUNT(*) as resolved FROM reports WHERE status = 'resolved'";
    $resolvedResult = $conn->query($resolvedSql);
    $resolved = $resolvedResult->fetch_assoc()['resolved'];
    
    // Pending reports
    $pendingSql = "SELECT COUNT(*) as pending FROM reports WHERE status = 'pending'";
    $pendingResult = $conn->query($pendingSql);
    $pending = $pendingResult->fetch_assoc()['pending'];
    
    // High urgency reports
    $highUrgencySql = "SELECT COUNT(*) as high_urgency FROM reports WHERE urgency_level = 'high'";
    $highUrgencyResult = $conn->query($highUrgencySql);
    $highUrgency = $highUrgencyResult->fetch_assoc()['high_urgency'];
    
    echo json_encode([
        'success' => true,
        'data' => [
            'total' => $total,
            'resolved' => $resolved,
            'pending' => $pending,
            'high_urgency' => $highUrgency
        ]
    ]);
}

function resolveReport($conn, $data) {
    // Log for debugging
    error_log("Resolve Report Input: " . json_encode($data));
    
    $report_id = isset($data['report_id']) ? (int)$data['report_id'] : 0;
    $resolution_action = isset($data['resolution_action']) ? trim($data['resolution_action']) : '';
    $resolution_notes = isset($data['resolution_notes']) ? trim($data['resolution_notes']) : '';
    
    if ($report_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid report ID']);
        return;
    }
    
    // First, get the report details including buyer and farmer emails
    $detailsSql = "SELECT 
                    r.*,
                    CONCAT(b.firstname, ' ', b.lastname) as buyer_name,
                    b.email as buyer_email,
                    CONCAT(f.first_name, ' ', f.last_name) as farmer_name,
                    f.email as farmer_email
                FROM reports r
                LEFT JOIN buyers b ON r.buyer_id = b.buyer_id
                LEFT JOIN users f ON r.cbn_user_id = f.user_id
                WHERE r.report_id = ?";
    
    $detailsStmt = $conn->prepare($detailsSql);
    $detailsStmt->bind_param('i', $report_id);
    $detailsStmt->execute();
    $reportDetails = $detailsStmt->get_result()->fetch_assoc();
    $detailsStmt->close();
    
    if (!$reportDetails) {
        echo json_encode(['success' => false, 'message' => 'Report not found']);
        return;
    }
    
    // Update the report
    $sql = "UPDATE reports 
            SET status = 'resolved', 
                resolution_action = ?, 
                resolution_notes = ?, 
                updated_at = NOW() 
            WHERE report_id = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        return;
    }
    
    $stmt->bind_param('ssi', $resolution_action, $resolution_notes, $report_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            error_log("Report $report_id resolved successfully");
            
            // Send email notifications
            require_once 'email_helper.php';
            
            // Prepare email data
            $emailData = [
                'report_id' => $report_id,
                'buyer_name' => $reportDetails['buyer_name'],
                'farmer_name' => $reportDetails['farmer_name'],
                'issue_type' => $reportDetails['issue_type'],
                'order_number' => $reportDetails['order_number'],
                'produce_name' => $reportDetails['produce_name'],
                'resolution_action' => $resolution_action,
                'resolution_notes' => $resolution_notes
            ];
            
            // Send email to buyer
            $buyerEmailSent = false;
            if (!empty($reportDetails['buyer_email'])) {
                $buyerEmailSent = sendReportResolvedEmail(
                    $reportDetails['buyer_email'],
                    $reportDetails['buyer_name'],
                    'buyer',
                    $emailData
                );
                error_log("Buyer email " . ($buyerEmailSent ? "sent" : "failed") . " to: " . $reportDetails['buyer_email']);
            }
            
            // Send email to farmer
            $farmerEmailSent = false;
            if (!empty($reportDetails['farmer_email'])) {
                $farmerEmailSent = sendReportResolvedEmail(
                    $reportDetails['farmer_email'],
                    $reportDetails['farmer_name'],
                    'farmer',
                    $emailData
                );
                error_log("Farmer email " . ($farmerEmailSent ? "sent" : "failed") . " to: " . $reportDetails['farmer_email']);
            }
            
            $message = 'Report resolved successfully';
            if ($buyerEmailSent && $farmerEmailSent) {
                $message .= '. Email notifications sent to both parties.';
            } elseif ($buyerEmailSent || $farmerEmailSent) {
                $message .= '. Email notification sent to ' . ($buyerEmailSent ? 'buyer' : 'farmer') . '.';
            } else {
                $message .= '. However, email notifications could not be sent.';
            }
            
            echo json_encode(['success' => true, 'message' => $message]);
        } else {
            error_log("No rows affected for report $report_id");
            echo json_encode(['success' => false, 'message' => 'Report not found or already resolved']);
        }
    } else {
        error_log("Execute failed: " . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Failed to resolve report: ' . $stmt->error]);
    }
    
    $stmt->close();
}

function deleteReport($conn, $data) {
    // Log for debugging
    error_log("Delete Report Input: " . json_encode($data));
    
    $report_id = isset($data['report_id']) ? (int)$data['report_id'] : 0;
    
    if ($report_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid report ID']);
        return;
    }
    
    $sql = "DELETE FROM reports WHERE report_id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        return;
    }
    
    $stmt->bind_param('i', $report_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            error_log("Report $report_id deleted successfully");
            echo json_encode(['success' => true, 'message' => 'Report deleted successfully']);
        } else {
            error_log("No rows affected for report $report_id");
            echo json_encode(['success' => false, 'message' => 'Report not found']);
        }
    } else {
        error_log("Execute failed: " . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Failed to delete report: ' . $stmt->error]);
    }
    
    $stmt->close();
}

function getFarmers($conn) {
    // Using users table with user_id as primary key
    $sql = "SELECT user_id, CONCAT(first_name, ' ', last_name) as farmer_name 
            FROM users 
            WHERE info_completed = 1 AND cbn_approved = 1
            ORDER BY first_name, last_name";
    
    $result = $conn->query($sql);
    $farmers = [];
    
    while ($row = $result->fetch_assoc()) {
        $farmers[] = [
            'cbn_user_id' => $row['user_id'],  // Return as cbn_user_id for consistency
            'farmer_name' => $row['farmer_name']
        ];
    }
    
    echo json_encode(['success' => true, 'data' => $farmers]);
}
?>