<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../DBcon.php';

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
            
        case 'get_farmer_produce':
            getFarmerProduce($conn);
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

// Handle report submission
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

    // Validate farmer exists and is active/verified
    $check_farmer = $conn->prepare("SELECT user_id FROM users WHERE user_id = ? AND cbn_approved = 1 AND is_verified = 1");
    $check_farmer->bind_param("i", $farmer_id);
    $check_farmer->execute();
    $result = $check_farmer->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Selected farmer does not exist or is not active/verified']);
        return;
    }

    // Handle file upload
    $evidence_path = null;
    if (isset($_FILES['evidence']) && $_FILES['evidence']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/reports/';
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
                $evidence_path = 'uploads/reports/' . $file_name;
            }
        }
    }

    // Insert report into database
    $stmt = $conn->prepare("
        INSERT INTO reports 
        (buyer_id, cbn_user_id, reported_user_type, order_number, produce_name, issue_type, reason, description, evidence, urgency_level, status, created_at) 
        VALUES (?, ?, 'farmer', ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
    ");
    
    $stmt->bind_param("iisssssss", 
        $buyer_id, 
        $farmer_id,
        $order_number, 
        $produce_name, 
        $issue_type, 
        $issue_type, 
        $issue_description, 
        $evidence_path, 
        $urgency_level
    );

    if ($stmt->execute()) {
        $report_id = $conn->insert_id;
        error_log("Report submitted successfully. ID: $report_id");
        echo json_encode([
            'success' => true,
            'message' => 'Report submitted successfully! Our team will review it within 24 hours.',
            'report_id' => $report_id
        ]);
    } else {
        $error_msg = $stmt->error;
        error_log("SQL Error: " . $error_msg);
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to submit report. Please try again.',
            'sql_error' => $error_msg
        ]);
    }
    $stmt->close();
}

// Get reports history for the logged-in buyer
function getReportsHistory($conn, $buyer_id) {
    $stmt = $conn->prepare("
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
            u.address as farmer_location
        FROM reports r
        LEFT JOIN users u ON r.cbn_user_id = u.user_id
        WHERE r.buyer_id = ?
        ORDER BY r.created_at DESC
        LIMIT 50
    ");
    
    $stmt->bind_param("i", $buyer_id);
    
    if (!$stmt->execute()) {
        error_log("Query failed: " . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Database error']);
        return;
    }

    $result = $stmt->get_result();
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
            'issue_type' => ucfirst(str_replace('_', ' ', $report['issue_type'])),
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
    $stmt->close();
}

// Get report statistics
function getReportStats($conn, $buyer_id) {
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total_reports,
            SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_reports,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_reports
        FROM reports 
        WHERE buyer_id = ?
    ");
    
    $stmt->bind_param("i", $buyer_id);
    
    if (!$stmt->execute()) {
        error_log("Query failed: " . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Database error']);
        return;
    }

    $result = $stmt->get_result();
    $stats = $result->fetch_assoc();

    echo json_encode([
        'success' => true,
        'stats' => [
            'total' => (int)$stats['total_reports'],
            'resolved' => (int)$stats['resolved_reports'],
            'pending' => (int)$stats['pending_reports']
        ]
    ]);
    $stmt->close();
}

// Get verified and active farmers list for dropdown
function getFarmersList($conn) {
    $stmt = $conn->prepare("
        SELECT 
            user_id,
            first_name,
            last_name,
            address as location,
            cbn_approved
        FROM users 
        WHERE cbn_approved = 1 
        AND user_id IN (SELECT DISTINCT user_id FROM produce_listings)
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
    $stmt->close();
}

// Get produce by selected farmer
function getFarmerProduce($conn) {
    $farmer_id = $_GET['farmer_id'] ?? '';
    
    if (empty($farmer_id)) {
        echo json_encode(['success' => false, 'message' => 'Farmer ID is required']);
        return;
    }
    
    $stmt = $conn->prepare("
        SELECT 
            prod_id,
            produce,
            quantity,
            price
        FROM produce_listings 
        WHERE user_id = ?
        AND quantity > 0
        ORDER BY produce ASC
    ");
    
    $stmt->bind_param("i", $farmer_id);
    
    if (!$stmt->execute()) {
        error_log("Query failed: " . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Database error']);
        return;
    }
    
    $result = $stmt->get_result();
    $produce = $result->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode([
        'success' => true,
        'produce' => $produce
    ]);
    $stmt->close();
}
?>