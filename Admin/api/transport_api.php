<?php
// transport_api.php
include 'DBcon.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to send JSON response
function sendResponse($success, $message = '', $data = []) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Export to CSV functionality
if (isset($_GET['action']) && $_GET['action'] === 'export') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment;filename="transporters_export_' . date('Y-m-d') . '.csv"');
    header('Cache-Control: max-age=0');

    // Create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');

    // Output CSV headers
    fputcsv($output, [
        'Transporter ID', 'Company Name', 'Contact Person', 'Email', 'Phone', 'Vehicle Type',
        'Vehicle Capacity', 'License Number', 'Operating Areas', 'Address', 'Status', 'Added Date'
    ]);

    // Fetch all transporters
    $sql = "SELECT t.transporter_id, t.company_name, t.contact_person, t.email, t.phone, 
                   t.vehicle_type, t.vehicle_capacity, t.license_number, t.operating_areas, 
                   t.address, t.is_verified, t.created_at
            FROM transporters t
            ORDER BY t.created_at DESC";
    $result = $conn->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
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
                $row['is_verified'] ? 'Active' : 'Disabled',
                date('d M Y', strtotime($row['created_at']))
            ]);
        }
    }

    fclose($output);
    exit;
}

// Handle requests
try {
    // GET: Fetch paginated transporters
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
        // Pagination parameters
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10;
        $offset = ($page - 1) * $limit;

        // Filter parameters
        $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
        $status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';

        // Build SQL query for counting total records
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

        // Build SQL query for paginated data
        $sql = "SELECT t.transporter_id, t.company_name, t.contact_person, t.email, t.phone, 
                       t.vehicle_type, t.vehicle_capacity, t.license_number, t.operating_areas, 
                       t.address, t.is_verified, t.created_at, t.updated_at
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
            throw new Exception("Prepare failed: " . $conn->error);
        }

        // Bind parameters dynamically
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
            throw new Exception("Database error: " . $conn->error);
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
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at']
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
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
        $data = json_decode(file_get_contents('php://input'), true);
        $company_name = $data['company_name'] ?? '';
        $routes = $data['routes'] ?? '';
        $fees = $data['fees'] ?? '';
        $availability = $data['availability'] ?? '';
        $contact = $data['contact'] ?? '';
        $notes = $data['notes'] ?? '';

        // Parse contact for email and phone
        list($email, $phone) = array_pad(explode('|', $contact), 2, '');
        $email = trim($email);
        $phone = trim($phone);

        // Basic validation
        if (!$company_name || !$routes || !$fees || !$availability || !$email || !$phone) {
            sendResponse(false, 'All required fields (company_name, routes, fees, availability, email, phone) must be provided.');
        }

        // Use default values for optional fields
        $contact_person = $company_name; // Use company name as contact person if not specified
        $user_id = 1; // Default user_id (adjust as needed)
        $vehicle_type = 'Unknown';
        $vehicle_capacity = 'Unknown';
        $license_number = '';
        $operating_areas = $routes; // Map routes to operating_areas
        $address = '';
        $state_id = null;
        $city_id = null;
        $is_verified = 1; // Default to active

        $sql = "INSERT INTO transporters (user_id, company_name, contact_person, email, phone, 
                vehicle_type, vehicle_capacity, license_number, operating_areas, address, 
                state_id, city_id, is_verified) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param(
            'isssssssssiib',
            $user_id, $company_name, $contact_person, $email, $phone, 
            $vehicle_type, $vehicle_capacity, $license_number, $operating_areas, 
            $address, $state_id, $city_id, $is_verified
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to add transporter: " . $stmt->error);
        }

        sendResponse(true, 'Transporter added successfully.');
    }

    // POST: Update transporter
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
        $data = json_decode(file_get_contents('php://input'), true);
        $transporter_id = $data['transporter_id'] ?? 0;
        $routes = $data['routes'] ?? '';
        $fees = $data['fees'] ?? '';
        $availability = $data['availability'] ?? '';
        $contact = $data['contact'] ?? '';
        $notes = $data['notes'] ?? '';

        // Parse contact for email and phone
        list($email, $phone) = array_pad(explode('|', $contact), 2, '');
        $email = trim($email);
        $phone = trim($phone);

        if (!$transporter_id || !$routes || !$fees || !$availability || !$email || !$phone) {
            sendResponse(false, 'All required fields (transporter_id, routes, fees, availability, email, phone) must be provided.');
        }

        $sql = "UPDATE transporters SET operating_areas = ?, email = ?, phone = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE transporter_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param('sssi', $routes, $email, $phone, $transporter_id);

        if (!$stmt->execute()) {
            throw new Exception("Failed to update transporter: " . $stmt->error);
        }

        sendResponse(true, 'Transporter updated successfully.');
    }

    // POST: Disable/Enable transporter
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
        $data = json_decode(file_get_contents('php://input'), true);
        $transporter_id = $data['transporter_id'] ?? 0;
        $is_verified = $data['is_verified'] ?? 0;
        $reason = $data['reason'] ?? '';

        if (!$transporter_id) {
            sendResponse(false, 'Transporter ID is required.');
        }

        $sql = "UPDATE transporters SET is_verified = ?, updated_at = CURRENT_TIMESTAMP WHERE transporter_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param('ii', $is_verified, $transporter_id);

        if (!$stmt->execute()) {
            throw new Exception("Failed to update transporter status: " . $stmt->error);
        }

        sendResponse(true, 'Transporter status updated successfully.', ['reason' => $reason]);
    }

} catch (Exception $e) {
    sendResponse(false, $e->getMessage());
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>