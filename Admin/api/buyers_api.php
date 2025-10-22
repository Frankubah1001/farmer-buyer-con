<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// api/buyers_api.php - RESTful API for Buyers Management
header('Content-Type: application/json');
session_start();
require_once 'DBcon.php';

// Check admin session
if (!isset($_SESSION['cbn_user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

// Helper function to send JSON response
function sendResponse($success, $data = [], $error = '') {
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'error' => $error
    ]);
    exit();
}

// Fetch buyers with filters and pagination
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $status = isset($_GET['status']) ? trim($_GET['status']) : '';
    $location = isset($_GET['location']) ? trim($_GET['location']) : '';
    $offset = ($page - 1) * $limit;

    $sql = "SELECT b.*, s.state_name, c.city_name 
            FROM buyers b 
            LEFT JOIN states s ON b.state_id = s.state_id 
            LEFT JOIN cities c ON b.city_id = c.city_id 
            WHERE 1=1";
    $params = [];
    $types = '';

    if ($search) {
        $sql .= " AND (b.firstname LIKE ? OR b.lastname LIKE ? OR b.email LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= 'sss';
    }
    if ($status === 'active') {
        $sql .= " AND b.is_verify = 1";
    } elseif ($status === 'new') {
        $sql .= " AND b.is_verify = 0";
    } elseif ($status === 'disabled') {
        $sql .= " AND b.is_verify = 2";
    }
    if ($location) {
        $sql .= " AND (s.state_name LIKE ? OR c.city_name LIKE ?)";
        $locationTerm = "%$location%";
        $params[] = $locationTerm;
        $params[] = $locationTerm;
        $types .= 'ss';
    }

    $sql .= " ORDER BY b.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';

    $stmt = $conn->prepare($sql);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $buyers = [];
    while ($row = $result->fetch_assoc()) {
        $buyers[] = $row;
    }

    // Count total for pagination
    $countSql = "SELECT COUNT(*) as total FROM buyers b 
                 LEFT JOIN states s ON b.state_id = s.state_id 
                 LEFT JOIN cities c ON b.city_id = c.city_id 
                 WHERE 1=1";
    $countParams = [];
    $countTypes = '';
    if ($search) {
        $countSql .= " AND (b.firstname LIKE ? OR b.lastname LIKE ? OR b.email LIKE ?)";
        $countParams[] = $searchTerm;
        $countParams[] = $searchTerm;
        $countParams[] = $searchTerm;
        $countTypes .= 'sss';
    }
    if ($status === 'active') {
        $countSql .= " AND b.is_verify = 1";
    } elseif ($status === 'new') {
        $countSql .= " AND b.is_verify = 0";
    } elseif ($status === 'disabled') {
        $countSql .= " AND b.is_verify = 2";
    }
    if ($location) {
        $countSql .= " AND (s.state_name LIKE ? OR c.city_name LIKE ?)";
        $countParams[] = $locationTerm;
        $countParams[] = $locationTerm;
        $countTypes .= 'ss';
    }

    $countStmt = $conn->prepare($countSql);
    if ($countTypes) {
        $countStmt->bind_param($countTypes, ...$countParams);
    }
    $countStmt->execute();
    $total = $countStmt->get_result()->fetch_assoc()['total'];

    sendResponse(true, [
        'buyers' => $buyers,
        'total' => $total,
        'pages' => ceil($total / $limit),
        'current_page' => $page
    ]);
}

// Fetch all buyers for export (no pagination)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'export') {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $status = isset($_GET['status']) ? trim($_GET['status']) : '';
    $location = isset($_GET['location']) ? trim($_GET['location']) : '';

    $sql = "SELECT b.firstname, b.lastname, b.buyer_type, b.email, b.phone, b.gender, b.address, b.created_at, s.state_name, c.city_name,
            CASE 
                WHEN b.is_verify = 2 THEN 'Disabled'
                WHEN b.is_verify = 1 THEN 'Active'
                ELSE 'New Buyer'
            END as status
            FROM buyers b 
            LEFT JOIN states s ON b.state_id = s.state_id 
            LEFT JOIN cities c ON b.city_id = c.city_id 
            WHERE 1=1";
    $params = [];
    $types = '';

    if ($search) {
        $sql .= " AND (b.firstname LIKE ? OR b.lastname LIKE ? OR b.email LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= 'sss';
    }
    if ($status === 'active') {
        $sql .= " AND b.is_verify = 1";
    } elseif ($status === 'new') {
        $sql .= " AND b.is_verify = 0";
    } elseif ($status === 'disabled') {
        $sql .= " AND b.is_verify = 2";
    }
    if ($location) {
        $sql .= " AND (s.state_name LIKE ? OR c.city_name LIKE ?)";
        $locationTerm = "%$location%";
        $params[] = $locationTerm;
        $params[] = $locationTerm;
        $types .= 'ss';
    }

    $sql .= " ORDER BY b.created_at DESC";
    $stmt = $conn->prepare($sql);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $buyers = [];
    while ($row = $result->fetch_assoc()) {
        $buyers[] = [
            'Name' => $row['firstname'] . ' ' . $row['lastname'],
            'Buyer Type' => $row['buyer_type'] ?? 'N/A',
            'Email' => $row['email'],
            'Phone' => $row['phone'],
            'Gender' => $row['gender'],
            'Address' => $row['address'],
            'Registration Date' => date('d-M-Y', strtotime($row['created_at'])),
            'State' => $row['state_name'] ?? 'N/A',
            'City' => $row['city_name'] ?? 'N/A',
            'Status' => $row['status']
        ];
    }

    sendResponse(true, $buyers);
}

// Get single buyer details
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_details') {
    $buyer_id = (int)($_GET['id'] ?? 0);
    if (!$buyer_id) {
        sendResponse(false, [], 'Invalid buyer ID');
    }

    // Fetch buyer details
    $stmt = $conn->prepare("SELECT b.*, s.state_name, c.city_name 
                            FROM buyers b 
                            LEFT JOIN states s ON b.state_id = s.state_id 
                            LEFT JOIN cities c ON b.city_id = c.city_id 
                            WHERE b.buyer_id = ?");
    $stmt->bind_param("i", $buyer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($buyer = $result->fetch_assoc()) {
        sendResponse(true, $buyer);
    } else {
        sendResponse(false, [], 'Buyer not found');
    }
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'approve':
            $buyer_id = (int)($_POST['id'] ?? 0);
            if (!$buyer_id) {
                sendResponse(false, [], 'Invalid buyer ID');
            }
            $stmt = $conn->prepare("UPDATE buyers SET is_verify = 1, disable_reason = NULL WHERE buyer_id = ?");
            $stmt->bind_param("i", $buyer_id);
            if ($stmt->execute()) {
                sendResponse(true);
            } else {
                sendResponse(false, [], $stmt->error);
            }
            break;

        case 'disable':
            $buyer_id = (int)($_POST['id'] ?? 0);
            $disable_reason = trim($_POST['disable_reason'] ?? '');
            if (!$buyer_id) {
                sendResponse(false, [], 'Invalid buyer ID');
            }
            if (!$disable_reason) {
                sendResponse(false, [], 'Disable reason is required');
            }
            $stmt = $conn->prepare("UPDATE buyers SET is_verify = 2, disable_reason = ? WHERE buyer_id = ?");
            $stmt->bind_param("si", $disable_reason, $buyer_id);
            if ($stmt->execute()) {
                sendResponse(true);
            } else {
                sendResponse(false, [], $stmt->error);
            }
            break;
    }
}

http_response_code(400);
sendResponse(false, [], 'Invalid request');
?>