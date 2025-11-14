<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'DBcon.php';

header('Content-Type: application/json');

// Start session and check authentication
session_start();
if (!isset($_SESSION['buyer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated. Please login.']);
    exit;
}

$buyer_id = $_SESSION['buyer_id'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'submit_report':
            handleReportSubmission($conn, $buyer_id);
            break;
        
        case 'get_reports_history':
            getReportsHistory($conn, $buyer_id);
            break;
            
        case 'get_report_stats':
            getReportStats($conn, $buyer_id);
            break;
            
        case 'get_farmers':
            getFarmersList($conn);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    error_log("Error in report_handler: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred. Please try again.',
        'error' => $e->getMessage()
    ]);
}

// Handle report submission - WITHOUT cbn_user_id dependency
function handleReportSubmission($conn, $buyer_id) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        return;
    }

    // Get form data
    $farmer_id = $_POST['farmerId'] ?? '';
    $order_number = $_POST['orderNumber'] ?? '';
    $produce_name = $_POST['produceName'] ?? '';
    $issue_type = $_POST['issueType'] ?? '';
    $issue_description = $_POST['issueDescription'] ?? '';
    $urgency_level = $_POST['urgencyLevel'] ?? 'medium';

    error_log("Received data - Buyer: $buyer_id, Farmer: $farmer_id, Order: $order_number");

    // Validate required fields
    if (empty($farmer_id) || empty($order_number) || empty($produce_name) || empty($issue_type) || empty($issue_description)) {
        echo json_encode([
            'success' => false, 
            'message' => 'All required fields must be filled'
        ]);
        return;
    }

    // Validate farmer exists
    $check_farmer = $conn->query("SELECT user_id FROM users WHERE user_id = '$farmer_id' AND cbn_approved = 1");
    if (!$check_farmer) {
        error_log("Farmer validation query failed: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Database error while validating farmer']);
        return;
    }
    
    if ($check_farmer->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Selected farmer does not exist or is not approved']);
        return;
    }

    // Handle file upload
    $evidence_path = null;
    if (isset($_FILES['evidence']) && $_FILES['evidence']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/reports/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = pathinfo($_FILES['evidence']['name'], PATHINFO_EXTENSION);
        $file_name = 'report_' . time() . '_' . uniqid() . '.' . $file_extension;
        $file_path = $upload_dir . $file_name;

        // Validate file type and size
        $allowed_types = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'txt'];
        if (in_array(strtolower($file_extension), $allowed_types) && $_FILES['evidence']['size'] <= 5 * 1024 * 1024) {
            if (move_uploaded_file($_FILES['evidence']['tmp_name'], $file_path)) {
                $evidence_path = $file_path;
            }
        }
    }

    // Escape all string values for safety
    $order_number_escaped = $conn->real_escape_string($order_number);
    $produce_name_escaped = $conn->real_escape_string($produce_name);
    $issue_type_escaped = $conn->real_escape_string($issue_type);
    $issue_description_escaped = $conn->real_escape_string($issue_description);
    $urgency_level_escaped = $conn->real_escape_string($urgency_level);
    $evidence_path_escaped = $evidence_path ? "'" . $conn->real_escape_string($evidence_path) . "'" : "NULL";

    // OPTION 1: Set reporter_cbn_user_id to NULL (if allowed by database)
    $sql = "
        INSERT INTO reports 
        (buyer_id, reported_user_type, order_number, produce_name, issue_type, reason, description, evidence, urgency_level, status, created_at) 
        VALUES (
            $buyer_id, 
            'farmer', 
            '$order_number_escaped', 
            '$produce_name_escaped', 
            '$issue_type_escaped', 
            '$issue_type_escaped', 
            '$issue_description_escaped', 
            $evidence_path_escaped, 
            '$urgency_level_escaped', 
            'pending', 
            NOW()
        )
    ";

    error_log("Executing SQL: " . $sql);

    if ($conn->query($sql)) {
        $report_id = $conn->insert_id;
        error_log("Report submitted successfully. ID: $report_id");
        echo json_encode([
            'success' => true,
            'message' => 'Report submitted successfully! Our team will review it within 24 hours.',
            'report_id' => $report_id
        ]);
    } else {
        $error_msg = $conn->error;
        error_log("SQL Error: " . $error_msg);
        
        // If setting to NULL doesn't work, we need to modify the table
        if (strpos($error_msg, 'NULL') !== false) {
            echo json_encode([
                'success' => false, 
                'message' => 'Database configuration issue. Please contact administrator.',
                'sql_error' => $error_msg
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Database error occurred. Please try again.',
                'sql_error' => $error_msg
            ]);
        }
    }
}

// Get reports history for the logged-in buyer
function getReportsHistory($conn, $buyer_id) {
    $sql = "
        SELECT 
            r.report_id,
            r.order_number,
            r.produce_name,
            r.issue_type,
            r.description,
            r.urgency_level,
            r.status,
            r.created_at,
            r.resolution_action,
            r.resolution_notes,
            u.first_name,
            u.last_name,
            u.farm_location_text as farmer_location
        FROM reports r
        JOIN users u ON r.reported_cbn_user_id = u.user_id
        ORDER BY r.created_at DESC
        LIMIT 50
    ";

    $result = $conn->query($sql);
    if (!$result) {
        error_log("Query failed: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Database error']);
        return;
    }

    $reports = [];
    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
    }

    // Format the response
    $formatted_reports = array_map(function($report) {
        return [
            'report_id' => '#REP' . str_pad($report['report_id'], 3, '0', STR_PAD_LEFT),
            'farmer_name' => $report['first_name'] . ' ' . $report['last_name'],
            'order_number' => $report['order_number'],
            'issue_type' => $report['issue_type'],
            'date_reported' => date('Y-m-d', strtotime($report['created_at'])),
            'status' => $report['status'],
            'urgency_level' => $report['urgency_level'],
            'resolution_action' => $report['resolution_action'],
            'farmer_location' => $report['farmer_location']
        ];
    }, $reports);

    echo json_encode([
        'success' => true,
        'reports' => $formatted_reports
    ]);
}

// Get report statistics
function getReportStats($conn, $buyer_id) {
    $sql = "
        SELECT 
            COUNT(*) as total_reports,
            SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_reports,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_reports
        FROM reports 
    ";

    $result = $conn->query($sql);
    if (!$result) {
        error_log("Query failed: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Database error']);
        return;
    }

    $stats = $result->fetch_assoc();

    echo json_encode([
        'success' => true,
        'stats' => [
            'total' => (int)$stats['total_reports'],
            'resolved' => (int)$stats['resolved_reports'],
            'pending' => (int)$stats['pending_reports']
        ]
    ]);
}

// Get farmers list for dropdown
function getFarmersList($conn) {
    $stmt = $conn->prepare("
        SELECT 
            user_id,
            first_name,
            last_name,
            farm_location_text as location,
            cbn_approved
        FROM users 
        WHERE cbn_approved = 1 
        ORDER BY first_name, last_name
    ");

    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Database error']);
        return;
    }

    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Database error']);
        return;
    }
    
    $result = $stmt->get_result();
    $farmers = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode([
        'success' => true,
        'farmers' => $farmers
    ]);
}
?>