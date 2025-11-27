<?php
session_start();
include 'DBcon.php';

// 1. FIX: Turn OFF display_errors so warnings don't break the JSON response
ini_set('display_errors', 0); 
ini_set('log_errors', 1);
ini_set('error_log', 'logs/php_errors.log'); 
error_reporting(E_ALL); // Report all errors to the log file

header('Content-Type: application/json');

// Function to send JSON response
function sendResponse($success, $message = '', $data = []) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ], JSON_THROW_ON_ERROR);
    exit;
}

// GET: Fetch single transporter by ID
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_transporter' && isset($_GET['transporter_id'])) {
    $transporter_id = intval($_GET['transporter_id']);
    
    // Fixed: Removed duplicate 't.notes'
    $sql = "SELECT t.transporter_id, t.cbn_user_id, t.company_name, t.contact_person, t.email, t.phone, 
                   t.vehicle_type, t.vehicle_capacity, t.license_number, t.operating_areas, 
                   t.address, t.state_id, t.city_id, t.is_verified, t.fees, t.availability, t.notes
            FROM transporters t
            WHERE t.transporter_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed for get_transporter: " . $conn->error);
        sendResponse(false, 'Database error: Unable to prepare statement.');
    }
    $stmt->bind_param('i', $transporter_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        sendResponse(false, 'Transporter details not found.');
    }

    $transporter = $result->fetch_assoc();
    sendResponse(true, '', $transporter);
}

// GET: Fetch all states
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_states') {
    $sql = "SELECT state_id, state_name FROM states ORDER BY state_name";
    $result = $conn->query($sql);
    if (!$result) {
        error_log("Query failed for get_states: " . $conn->error);
        sendResponse(false, 'Database error: Unable to fetch states.');
    }
    $states = [];
    while ($row = $result->fetch_assoc()) {
        $states[] = $row;
    }
    sendResponse(true, '', $states);
}

// GET: Fetch cities for a state_id
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_cities' && isset($_GET['state_id'])) {
    $state_id = intval($_GET['state_id']);
    $sql = "SELECT city_id, city_name FROM cities WHERE state_id = ? ORDER BY city_name";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed for get_cities: " . $conn->error);
        sendResponse(false, 'Database error: Unable to prepare statement.');
    }
    $stmt->bind_param('i', $state_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cities = [];
    while ($row = $result->fetch_assoc()) {
        $cities[] = $row;
    }
    sendResponse(true, '', $cities);
}

// Export Functionality
if (isset($_GET['action']) && $_GET['action'] === 'export') {
    // Note: We do not disable output buffering here as we are streaming a file
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment;filename="transporters_export_' . date('Y-m-d') . '.csv"');
    header('Cache-Control: max-age=0');

    $output = fopen('php://output', 'w');
    fputcsv($output, [
        'Transporter ID', 'Company Name', 'Contact Person', 'Email', 'Phone', 'Vehicle Type',
        'Vehicle Capacity', 'License Number', 'Operating Areas', 'Address', 'Fee Structure', 'Status', 'Reason', 'Added Date'
    ]);

    $sql = "SELECT t.transporter_id, t.company_name, t.contact_person, t.email, t.phone, 
                   t.vehicle_type, t.vehicle_capacity, t.license_number, t.operating_areas, 
                   t.address, t.fees, t.is_verified, t.notes, t.created_at
            FROM transporters t
            ORDER BY t.created_at DESC";
    $result = $conn->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $formattedFees = $row['fees'] ? '₦' . number_format(floatval(preg_replace('/[^\d.]/', '', $row['fees'])), 2) : '';
            
            fputcsv($output, [
                '#' . $row['transporter_id'],
                $row['company_name'],
                $row['contact_person'],
                $row['email'],
                $row['phone'],
                $row['vehicle_type'],
                $row['vehicle_capacity'],
                $row['license_number'] ?? '',
                $row['operating_areas'] ?? '',
                $row['address'] ?? '',
                $formattedFees,
                $row['is_verified'] ? 'Active' : ($row['notes'] ? 'Deactivated' : 'Not Active'),
                $row['notes'] ?? '',
                date('d M Y', strtotime($row['created_at']))
            ]);
        }
    }
    fclose($output);
    exit;
}

// Handle POST and JSON requests
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = file_get_contents('php://input');
        if (empty($input)) {
            throw new Exception('Empty request body');
        }
        $data = json_decode($input, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON input: ' . json_last_error_msg());
        }
    }

    // GET: Fetch paginated transporters
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10;
        $offset = ($page - 1) * $limit;
        $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
        $status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';

        // Count Query
        $count_sql = "SELECT COUNT(*) as total FROM transporters WHERE 1=1";
        if ($search) {
            $count_sql .= " AND (company_name LIKE ? OR contact_person LIKE ?)";
        }
        if ($status) {
            $count_sql .= $status === 'active' ? " AND is_verified = 1" : " AND is_verified = 0";
        }

        $count_stmt = $conn->prepare($count_sql);
        if ($search) {
            $like = "%$search%";
            $count_stmt->bind_param('ss', $like, $like);
        }
        $count_stmt->execute();
        $total_records = $count_stmt->get_result()->fetch_assoc()['total'];
        $total_pages = ceil($total_records / $limit);

        // Fetch Query - Fixed Duplicate Columns
        $sql = "SELECT t.transporter_id, t.company_name, t.contact_person, t.email, t.phone, 
                       t.vehicle_type, t.vehicle_capacity, t.license_number, t.operating_areas, 
                       t.address, t.is_verified, t.created_at, t.updated_at, t.fees, t.availability, t.notes
                FROM transporters t
                WHERE 1=1";
        
        if ($search) {
            $sql .= " AND (company_name LIKE ? OR contact_person LIKE ?)";
        }
        if ($status) {
            $sql .= $status === 'active' ? " AND is_verified = 1" : " AND is_verified = 0";
        }
        $sql .= " ORDER BY t.created_at DESC LIMIT ? OFFSET ?";

        $stmt = $conn->prepare($sql);
        if ($search) {
            $like = "%$search%";
            $stmt->bind_param('ssii', $like, $like, $limit, $offset);
        } else {
            $stmt->bind_param('ii', $limit, $offset);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $transporters = [];
        while ($row = $result->fetch_assoc()) {
            $transporters[] = [
                'transporter_id' => $row['transporter_id'],
                'company_name' => $row['company_name'],
                'contact_person' => $row['contact_person'],
                'email' => $row['email'],
                'phone' => $row['phone'],
                'vehicle_type' => $row['vehicle_type'],
                'vehicle_capacity' => $row['vehicle_capacity'],
                'license_number' => $row['license_number'] ?? '',
                'operating_areas' => $row['operating_areas'] ?? '',
                'address' => $row['address'] ?? '',
                'is_verified' => $row['is_verified'],
                // FIX: 'reason' was trying to access undefined array key. Map it to 'notes'
                'reason' => $row['notes'] ?? '', 
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at'],
                'fees' => $row['fees'] ?? '',
                'availability' => $row['availability'] ?? '',
                'notes' => $row['notes'] ?? ''
            ];
        }

        sendResponse(true, '', [
            'transporters' => $transporters,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $total_pages,
                'total_records' => $total_records,
                'limit' => $limit
            ]
        ]);
    }

    // POST: Add new transporter
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['action']) && $data['action'] === 'add') {
        if (!isset($_SESSION['cbn_user_id'])) {
            sendResponse(false, 'Authentication required: Please log in.');
        }
        $cbn_user_id = intval($_SESSION['cbn_user_id']);

        // Data gathering and simple validation
        $company_name = trim($data['company_name'] ?? '');
        $contact_person = trim($data['contact_person'] ?? '');
        $email = trim($data['email'] ?? '');
        $phone = $data['phone'] ?? '';
        $vehicle_type = $data['vehicle_type'] ?? '';
        $vehicle_capacity = $data['vehicle_capacity'] ?? '';
        $license_number = $data['license_number'] ?? null;
        $operating_areas = $data['operating_areas'] ?? '';
        $address = $data['address'] ?? null;
        $state_id = !empty($data['state_id']) ? intval($data['state_id']) : null;
        $city_id = !empty($data['city_id']) ? intval($data['city_id']) : null;
        $fees = $data['fees'] ?? '';
        $availability = $data['availability'] ?? '';
        $notes = $data['notes'] ?? '';
        $is_verified = 0;

        if (!$company_name || !$email || !$phone) {
            sendResponse(false, 'Company Name, Email, and Phone are required.');
        }

        // Check Duplicate Email
        $check_sql = "SELECT transporter_id FROM transporters WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param('s', $email);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows > 0) {
            sendResponse(false, 'Email already exists.');
        }

        $sql = "INSERT INTO transporters (cbn_user_id, company_name, contact_person, email, phone, 
                vehicle_type, vehicle_capacity, license_number, operating_areas, address, 
                state_id, city_id, is_verified, fees, availability, notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
             throw new Exception("Database error: " . $conn->error);
        }

        // FIX: Corrected Type String. 13th param (is_verified) is 'i' (integer), not 's'
        $stmt->bind_param(
            'isssssssssiiisss', 
            $cbn_user_id, $company_name, $contact_person, $email, $phone, 
            $vehicle_type, $vehicle_capacity, $license_number, $operating_areas, 
            $address, $state_id, $city_id, $is_verified, $fees, $availability, $notes
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to add transporter: " . $stmt->error);
        }

        sendResponse(true, 'Transporter added successfully.');
    }

    // POST: Update transporter
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['action']) && $data['action'] === 'update') {
        $transporter_id = isset($data['transporter_id']) ? intval($data['transporter_id']) : 0;
        
        if (!$transporter_id) {
            sendResponse(false, 'Transporter ID is missing.');
        }

        $company_name = trim($data['company_name'] ?? '');
        $contact_person = trim($data['contact_person'] ?? '');
        $email = trim($data['email'] ?? '');
        $phone = $data['phone'] ?? '';
        $vehicle_type = $data['vehicle_type'] ?? '';
        $vehicle_capacity = $data['vehicle_capacity'] ?? '';
        $license_number = $data['license_number'] ?? null;
        $operating_areas = $data['operating_areas'] ?? '';
        $address = $data['address'] ?? null;
        $state_id = !empty($data['state_id']) ? intval($data['state_id']) : null;
        $city_id = !empty($data['city_id']) ? intval($data['city_id']) : null;
        $fees = $data['fees'] ?? '';
        $availability = $data['availability'] ?? '';
        $notes = $data['notes'] ?? '';

        // Check Duplicate Email (Excluding current ID)
        $check_sql = "SELECT transporter_id FROM transporters WHERE email = ? AND transporter_id != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param('si', $email, $transporter_id);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows > 0) {
            sendResponse(false, 'Email already exists for another transporter.');
        }

        $sql = "UPDATE transporters SET 
                company_name = ?, contact_person = ?, email = ?, phone = ?, 
                vehicle_type = ?, vehicle_capacity = ?, license_number = ?, 
                operating_areas = ?, address = ?, state_id = ?, city_id = ?, 
                fees = ?, availability = ?, notes = ?, 
                updated_at = CURRENT_TIMESTAMP 
                WHERE transporter_id = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
             throw new Exception("Database error: " . $conn->error);
        }

        $stmt->bind_param(
            'sssssssssiiissi',
            $company_name, $contact_person, $email, $phone, 
            $vehicle_type, $vehicle_capacity, $license_number, 
            $operating_areas, $address, $state_id, $city_id, 
            $fees, $availability, $notes, $transporter_id
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to update transporter: " . $stmt->error);
        }

        sendResponse(true, 'Transporter updated successfully.');
    }

    // POST: Toggle status
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['action']) && $data['action'] === 'toggle_status') {
        $transporter_id = $data['transporter_id'] ?? 0;
        $is_verified = isset($data['is_verified']) ? (int)$data['is_verified'] : 0;
        $reason = $data['reason'] ?? null;

        if (!$transporter_id) {
            sendResponse(false, 'Transporter ID is required.');
        }

        $sql = "UPDATE transporters SET is_verified = ?, notes = ?, updated_at = CURRENT_TIMESTAMP WHERE transporter_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('isi', $is_verified, $reason, $transporter_id);

        if (!$stmt->execute()) {
            throw new Exception("Failed to update status: " . $stmt->error);
        }

        $message = $is_verified ? 'Transporter activated successfully.' : 'Transporter deactivated successfully.';
        sendResponse(true, $message);
    }

} catch (Exception $e) {
    error_log("API error: " . $e->getMessage());
    sendResponse(false, $e->getMessage());
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>