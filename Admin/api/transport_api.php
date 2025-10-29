<?php
session_start(); // Start session to get logged-in user's cbn_user_id
include 'DBcon.php';

header('Content-Type: application/json');

// Disable displaying errors to prevent breaking JSON
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'logs/php_errors.log'); // Ensure this path is writable

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
    
    $sql = "SELECT t.transporter_id, t.cbn_user_id, t.company_name, t.contact_person, t.email, t.phone, 
                   t.vehicle_type, t.vehicle_capacity, t.license_number, t.operating_areas, 
                   t.address, t.state_id, t.city_id, t.is_verified, t.notes, t.fees, t.availability, t.notes
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

if (isset($_GET['action']) && $_GET['action'] === 'export') {
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
            // Format fees with Nigerian currency
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
    } else {
        error_log("Query failed for export: " . $conn->error);
    }

    fclose($output);
    exit;
}

// Handle requests
try {
    // Ensure JSON input for POST requests
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

        $count_sql = "SELECT COUNT(*) as total FROM transporters WHERE 1=1";
        if ($search) {
            $count_sql .= " AND (company_name LIKE ? OR contact_person LIKE ?)";
        }
        if ($status) {
            $count_sql .= $status === 'active' ? " AND is_verified = 1" : " AND is_verified = 0";
        }

        $count_stmt = $conn->prepare($count_sql);
        if (!$count_stmt) {
            error_log("Prepare failed for count transporters: " . $conn->error);
            throw new Exception("Database error: Unable to prepare statement.");
        }
        if ($search) {
            $like = "%$search%";
            $count_stmt->bind_param('ss', $like, $like);
        }
        $count_stmt->execute();
        $total_records = $count_stmt->get_result()->fetch_assoc()['total'];
        $total_pages = ceil($total_records / $limit);

        $sql = "SELECT t.transporter_id, t.company_name, t.contact_person, t.email, t.phone, 
                       t.vehicle_type, t.vehicle_capacity, t.license_number, t.operating_areas, 
                       t.address, t.is_verified, t.notes, t.created_at, t.updated_at, t.fees, t.availability, t.notes
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
        if (!$stmt) {
            error_log("Prepare failed for fetch transporters: " . $conn->error);
            throw new Exception("Database error: Unable to prepare statement.");
        }

        if ($search && $status) {
            $like = "%$search%";
            $stmt->bind_param('ssii', $like, $like, $limit, $offset);
        } elseif ($search) {
            $like = "%$search%";
            $stmt->bind_param('ssii', $like, $like, $limit, $offset);
        } else {
            $stmt->bind_param('ii', $limit, $offset);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) {
            error_log("Query failed for fetch transporters: " . $conn->error);
            throw new Exception("Database error: Unable to fetch transporters.");
        }

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
                'reason' => $row['reason'] ?? '',
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
        // Get cbn_user_id from session
        if (!isset($_SESSION['cbn_user_id']) || !is_numeric($_SESSION['cbn_user_id'])) {
            error_log("Add transporter failed: No logged-in user or invalid cbn_user_id in session");
            sendResponse(false, 'Authentication required: Please log in.');
        }
        $cbn_user_id = intval($_SESSION['cbn_user_id']);

        // Validate cbn_user_id
        $check_user_sql = "SELECT COUNT(*) as count FROM cbn_users WHERE cbn_user_id = ?";
        $check_user_stmt = $conn->prepare($check_user_sql);
        if (!$check_user_stmt) {
            error_log("Prepare failed for cbn_user_id check: " . $conn->error);
            throw new Exception("Database error: Unable to prepare statement.");
        }
        $check_user_stmt->bind_param('i', $cbn_user_id);
        $check_user_stmt->execute();
        if ($check_user_stmt->get_result()->fetch_assoc()['count'] == 0) {
            error_log("Invalid cbn_user_id: $cbn_user_id does not exist in cbn_users");
            throw new Exception('Invalid cbn_user_id: User does not exist.');
        }

        $company_name = mb_convert_encoding($data['company_name'] ?? '', 'UTF-8', 'auto');
        $contact_person = mb_convert_encoding($data['contact_person'] ?? '', 'UTF-8', 'auto');
        $email = mb_convert_encoding($data['email'] ?? '', 'UTF-8', 'auto');
        $phone = $data['phone'] ?? '';
        $vehicle_type = mb_convert_encoding($data['vehicle_type'] ?? '', 'UTF-8', 'auto');
        $vehicle_capacity = mb_convert_encoding($data['vehicle_capacity'] ?? '', 'UTF-8', 'auto');
        $license_number = $data['license_number'] ?? null;
        $operating_areas = mb_convert_encoding($data['operating_areas'] ?? '', 'UTF-8', 'auto');
        $address = $data['address'] ?? null;
        $state_id = isset($data['state_id']) ? ($data['state_id'] ? intval($data['state_id']) : null) : null;
        $city_id = isset($data['city_id']) ? ($data['city_id'] ? intval($data['city_id']) : null) : null;
        $fees = mb_convert_encoding($data['fees'] ?? '', 'UTF-8', 'auto');
        $availability = mb_convert_encoding($data['availability'] ?? '', 'UTF-8', 'auto');
        $notes = mb_convert_encoding($data['notes'] ?? '', 'UTF-8', 'auto');
        $is_verified = 0; // Default for new transporters (Not Active)

        // Validate required fields
        if (!$company_name || !$contact_person || !$email || !$phone || !$vehicle_type || !$vehicle_capacity || !$operating_areas || !$fees || !$availability) {
            sendResponse(false, 'All required fields (company_name, contact_person, email, phone, vehicle_type, vehicle_capacity, operating_areas, fees, availability) must be provided.');
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendResponse(false, 'Invalid email format.');
        }

        // Check for duplicate email
        $check_sql = "SELECT COUNT(*) as count FROM transporters WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        if (!$check_stmt) {
            error_log("Prepare failed for email check: " . $conn->error);
            throw new Exception("Database error: Unable to prepare statement.");
        }
        $check_stmt->bind_param('s', $email);
        $check_stmt->execute();
        if ($check_stmt->get_result()->fetch_assoc()['count'] > 0) {
            sendResponse(false, 'Email already exists.');
        }

        // Validate state_id and city_id if provided
        if ($state_id) {
            $check_state_sql = "SELECT COUNT(*) as count FROM states WHERE state_id = ?";
            $check_state_stmt = $conn->prepare($check_state_sql);
            if (!$check_state_stmt) {
                error_log("Prepare failed for state_id check: " . $conn->error);
                throw new Exception("Database error: Unable to prepare statement.");
            }
            $check_state_stmt->bind_param('i', $state_id);
            $check_state_stmt->execute();
            if ($check_state_stmt->get_result()->fetch_assoc()['count'] == 0) {
                throw new Exception('Invalid state_id: State does not exist.');
            }
        }
        if ($city_id) {
            $check_city_sql = "SELECT COUNT(*) as count FROM cities WHERE city_id = ? AND state_id = ?";
            $check_city_stmt = $conn->prepare($check_city_sql);
            if (!$check_city_stmt) {
                error_log("Prepare failed for city_id check: " . $conn->error);
                throw new Exception("Database error: Unable to prepare statement.");
            }
            $check_city_stmt->bind_param('ii', $city_id, $state_id);
            $check_city_stmt->execute();
            if ($check_city_stmt->get_result()->fetch_assoc()['count'] == 0) {
                throw new Exception('Invalid city_id: City does not exist or does not belong to the selected state.');
            }
        }

        // Log bound values for debugging
        error_log("Adding transporter: cbn_user_id=$cbn_user_id, company_name=$company_name, email=$email, state_id=" . ($state_id ?? 'NULL') . ", city_id=" . ($city_id ?? 'NULL') . ", is_verified=$is_verified");

        $sql = "INSERT INTO transporters (cbn_user_id, company_name, contact_person, email, phone, 
                vehicle_type, vehicle_capacity, license_number, operating_areas, address, 
                state_id, city_id, is_verified, reason, fees, availability, notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed for insert transporter: " . $conn->error);
            throw new Exception("Database error: Unable to prepare statement.");
        }

        $null_reason = null; // No reason for new transporters
        $stmt->bind_param(
            'isssssssssiissss',
            $cbn_user_id, $company_name, $contact_person, $email, $phone, 
            $vehicle_type, $vehicle_capacity, $license_number, $operating_areas, 
            $address, $state_id, $city_id, $is_verified, $null_reason, $fees, $availability, $notes
        );

        if (!$stmt->execute()) {
            error_log("Execute failed for insert transporter: " . $stmt->error);
            throw new Exception("Failed to add transporter: " . $stmt->error);
        }

        sendResponse(true, 'Transporter added successfully.');
    }

    // POST: Update transporter
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['action']) && $data['action'] === 'update') {
        $transporter_id = $data['transporter_id'] ?? 0;
        $company_name = mb_convert_encoding($data['company_name'] ?? '', 'UTF-8', 'auto');
        $contact_person = mb_convert_encoding($data['contact_person'] ?? '', 'UTF-8', 'auto');
        $email = mb_convert_encoding($data['email'] ?? '', 'UTF-8', 'auto');
        $phone = $data['phone'] ?? '';
        $vehicle_type = mb_convert_encoding($data['vehicle_type'] ?? '', 'UTF-8', 'auto');
        $vehicle_capacity = mb_convert_encoding($data['vehicle_capacity'] ?? '', 'UTF-8', 'auto');
        $license_number = $data['license_number'] ?? null;
        $operating_areas = mb_convert_encoding($data['operating_areas'] ?? '', 'UTF-8', 'auto');
        $address = $data['address'] ?? null;
        $state_id = isset($data['state_id']) ? ($data['state_id'] ? intval($data['state_id']) : null) : null;
        $city_id = isset($data['city_id']) ? ($data['city_id'] ? intval($data['city_id']) : null) : null;
        $fees = mb_convert_encoding($data['fees'] ?? '', 'UTF-8', 'auto');
        $availability = mb_convert_encoding($data['availability'] ?? '', 'UTF-8', 'auto');
        $notes = mb_convert_encoding($data['notes'] ?? '', 'UTF-8', 'auto');

        // Validate required fields
        if (!$transporter_id || !$company_name || !$contact_person || !$email || !$phone || !$vehicle_type || !$vehicle_capacity || !$operating_areas || !$fees || !$availability) {
            sendResponse(false, 'All required fields (transporter_id, company_name, contact_person, email, phone, vehicle_type, vehicle_capacity, operating_areas, fees, availability) must be provided.');
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendResponse(false, 'Invalid email format.');
        }

        // Check for duplicate email (excluding current transporter)
        $check_sql = "SELECT COUNT(*) as count FROM transporters WHERE email = ? AND transporter_id != ?";
        $check_stmt = $conn->prepare($check_sql);
        if (!$check_stmt) {
            error_log("Prepare failed for email check: " . $conn->error);
            throw new Exception("Database error: Unable to prepare statement.");
        }
        $check_stmt->bind_param('si', $email, $transporter_id);
        $check_stmt->execute();
        if ($check_stmt->get_result()->fetch_assoc()['count'] > 0) {
            sendResponse(false, 'Email already exists.');
        }

        // Validate state_id and city_id if provided
        if ($state_id) {
            $check_state_sql = "SELECT COUNT(*) as count FROM states WHERE state_id = ?";
            $check_state_stmt = $conn->prepare($check_state_sql);
            if (!$check_state_stmt) {
                error_log("Prepare failed for state_id check: " . $conn->error);
                throw new Exception("Database error: Unable to prepare statement.");
            }
            $check_state_stmt->bind_param('i', $state_id);
            $check_state_stmt->execute();
            if ($check_state_stmt->get_result()->fetch_assoc()['count'] == 0) {
                throw new Exception('Invalid state_id: State does not exist.');
            }
        }
        if ($city_id) {
            $check_city_sql = "SELECT COUNT(*) as count FROM cities WHERE city_id = ? AND state_id = ?";
            $check_city_stmt = $conn->prepare($check_city_sql);
            if (!$check_city_stmt) {
                error_log("Prepare failed for city_id check: " . $conn->error);
                throw new Exception("Database error: Unable to prepare statement.");
            }
            $check_city_stmt->bind_param('ii', $city_id, $state_id);
            $check_city_stmt->execute();
            if ($check_city_stmt->get_result()->fetch_assoc()['count'] == 0) {
                throw new Exception('Invalid city_id: City does not exist or does not belong to the selected state.');
            }
        }

        // Log bound values for debugging
        error_log("Updating transporter: transporter_id=$transporter_id, company_name=$company_name, email=$email");

        $sql = "UPDATE transporters SET 
                company_name = ?, contact_person = ?, email = ?, phone = ?, 
                vehicle_type = ?, vehicle_capacity = ?, license_number = ?, 
                operating_areas = ?, address = ?, state_id = ?, city_id = ?, 
                fees = ?, availability = ?, notes = ?, 
                updated_at = CURRENT_TIMESTAMP 
                WHERE transporter_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed for update transporter: " . $conn->error);
            throw new Exception("Database error: Unable to prepare statement.");
        }

        $stmt->bind_param(
            'sssssssssiiissi',
            $company_name, $contact_person, $email, $phone, 
            $vehicle_type, $vehicle_capacity, $license_number, 
            $operating_areas, $address, $state_id, $city_id, 
            $fees, $availability, $notes, $transporter_id
        );

        if (!$stmt->execute()) {
            error_log("Execute failed for update transporter: " . $stmt->error);
            throw new Exception("Failed to update transporter: " . $stmt->error);
        }

        sendResponse(true, 'Transporter updated successfully.');
    }

    // POST: Toggle transporter status (Activate/Deactivate)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['action']) && $data['action'] === 'toggle_status') {
        $transporter_id = $data['transporter_id'] ?? 0;
        $is_verified = isset($data['is_verified']) ? (int)$data['is_verified'] : 0;
        $reason = isset($data['reason']) ? mb_convert_encoding($data['reason'], 'UTF-8', 'auto') : null;

        if (!$transporter_id) {
            sendResponse(false, 'Transporter ID is required.');
        }

        // Validate reason for deactivation
        if ($is_verified === 0 && empty($reason)) {
            sendResponse(false, 'Reason is required for deactivation.');
        }

        $sql = "UPDATE transporters SET is_verified = ?, notes = ?, updated_at = CURRENT_TIMESTAMP WHERE transporter_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed for toggle_status: " . $conn->error);
            throw new Exception("Database error: Unable to prepare statement.");
        }

        $stmt->bind_param('isi', $is_verified, $reason, $transporter_id);

        if (!$stmt->execute()) {
            error_log("Execute failed for toggle_status: " . $stmt->error);
            throw new Exception("Failed to update transporter status: " . $stmt->error);
        }

        $message = $is_verified ? 'Transporter activated successfully.' : 'Transporter deactivated successfully.';
        sendResponse(true, $message, ['reason' => $reason ?? '']);
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