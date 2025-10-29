<?php
session_start();
include 'DBcon.php';

header('Content-Type: application/json');

function sendResponse($success, $message = '', $data = []) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ], JSON_THROW_ON_ERROR);
    exit;
}

try {
    // GET: Fetch single report by ID
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_report' && isset($_GET['report_id'])) {
        $report_id = intval($_GET['report_id']);
        
        $sql = "SELECT r.*, 
                       reporter.first_name as reporter_first_name, 
                       reporter.last_name as reporter_last_name,
                       reporter.email as reporter_email,
                       reporter.phone as reporter_phone,
                       reported.first_name as reported_first_name,
                       reported.last_name as reported_last_name,
                       reported.email as reported_email,
                       reported.phone as reported_phone,
                       reported.address as reported_address,
                       reported.cbn_approved as reported_status
                FROM reports r
                LEFT JOIN users reporter ON r.reporter_cbn_user_id = reporter.user_id
                LEFT JOIN users reported ON r.reported_cbn_user_id = reported.user_id
                WHERE r.report_id = ? AND r.reported_user_type = 'farmer'
                
                UNION ALL
                
                SELECT r.*, 
                       reporter.first_name as reporter_first_name, 
                       reporter.last_name as reporter_last_name,
                       reporter.email as reporter_email,
                       reporter.phone as reporter_phone,
                       reported.firstname as reported_first_name,
                       reported.lastname as reported_last_name,
                       reported.email as reported_email,
                       reported.phone as reported_phone,
                       reported.address as reported_address,
                       'Active' as reported_status
                FROM reports r
                LEFT JOIN users reporter ON r.reporter_cbn_user_id = reporter.user_id
                LEFT JOIN buyers reported ON r.reported_cbn_user_id = reported.buyer_id
                WHERE r.report_id = ? AND r.reported_user_type = 'buyer'";
                
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Database error: Unable to prepare statement.");
        }
        $stmt->bind_param('ii', $report_id, $report_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            sendResponse(false, 'Report details not found.');
        }

        $report = $result->fetch_assoc();
        
        // Format response
        $formattedReport = [
            'report_id' => $report['report_id'],
            'reporter_name' => $report['reporter_first_name'] . ' ' . $report['reporter_last_name'],
            'reporter_email' => $report['reporter_email'],
            'reporter_phone' => $report['reporter_phone'],
            'reported_user_name' => $report['reported_first_name'] . ' ' . $report['reported_last_name'],
            'reported_user_email' => $report['reported_email'],
            'reported_user_phone' => $report['reported_phone'],
            'reported_user_address' => $report['reported_address'],
            'reported_user_type' => $report['reported_user_type'],
            'reported_user_status' => $report['reported_status'] == 2 ? 'Disabled' : ($report['reported_status'] == 1 ? 'Active' : 'Pending'),
            'reason' => $report['reason'],
            'description' => $report['description'],
            'evidence' => $report['evidence'],
            'status' => $report['status'],
            'resolution_action' => $report['resolution_action'],
            'resolution_notes' => $report['resolution_notes'],
            'created_at' => $report['created_at'],
            'reported_cbn_user_id' => $report['reported_cbn_user_id']
        ];

        sendResponse(true, '', $formattedReport);
    }

    // Export to CSV functionality
    if (isset($_GET['action']) && $_GET['action'] === 'export') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment;filename="reports_export_' . date('Y-m-d') . '.csv"');
        header('Cache-Control: max-age=0');

        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'Report ID', 'Reported User', 'User Type', 'Reporter', 'Reason', 
            'Description', 'Status', 'Resolution Action', 'Resolution Notes', 'Created Date'
        ]);

        $sql = "SELECT r.report_id, 
                       CONCAT(u.first_name, ' ', u.last_name) as reported_user,
                       r.reported_user_type,
                       CONCAT(ur.first_name, ' ', ur.last_name) as reporter,
                       r.reason, r.description, r.status, r.resolution_action, 
                       r.resolution_notes, r.created_at
                FROM reports r
                LEFT JOIN users u ON r.reported_cbn_user_id = u.user_id AND r.reported_user_type = 'Farmer'
                LEFT JOIN users ur ON r.reporter_cbn_user_id = ur.user_id
                WHERE r.reported_user_type = 'Farmer'
                
                UNION ALL
                
                SELECT r.report_id, 
                       CONCAT(b.firstname, ' ', b.lastname) as reported_user,
                       r.reported_user_type,
                       CONCAT(ur.first_name, ' ', ur.last_name) as reporter,
                       r.reason, r.description, r.status, r.resolution_action, 
                       r.resolution_notes, r.created_at
                FROM reports r
                LEFT JOIN buyers b ON r.reported_cbn_user_id = b.buyer_id AND r.reported_user_type = 'Buyer'
                LEFT JOIN users ur ON r.reporter_cbn_user_id = ur.user_id
                WHERE r.reported_user_type = 'Buyer'
                
                ORDER BY created_at DESC";
                
        $result = $conn->query($sql);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                fputcsv($output, [
                    '#' . $row['report_id'],
                    $row['reported_user'],
                    ucfirst($row['reported_user_type']),
                    $row['reporter'],
                    $row['reason'],
                    $row['description'] ?? '',
                    ucfirst($row['status']),
                    ucfirst($row['resolution_action']),
                    $row['resolution_notes'] ?? '',
                    date('d M Y', strtotime($row['created_at']))
                ]);
            }
        }

        fclose($output);
        exit;
    }

    // GET: Fetch paginated reports
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10;
        $offset = ($page - 1) * $limit;
        $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
        $status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';
        $type = isset($_GET['type']) ? $conn->real_escape_string($_GET['type']) : '';

        // Count total records
        $count_sql = "SELECT COUNT(*) as total FROM (
                    SELECT r.report_id
                    FROM reports r
                    LEFT JOIN users u ON r.reported_cbn_user_id = u.user_id AND r.reported_user_type = 'farmer'
                    LEFT JOIN buyers b ON r.reported_cbn_user_id = b.buyer_id AND r.reported_user_type = 'buyer'
                    LEFT JOIN users ur ON r.reporter_cbn_user_id = ur.user_id
                    WHERE 1=1
        ";

        if ($search) {
            $count_sql .= " AND (r.report_id LIKE '%$search%' OR u.first_name LIKE '%$search%' OR u.last_name LIKE '%$search%' OR b.firstname LIKE '%$search%' OR b.lastname LIKE '%$search%')";
        }
        if ($status) {
            $count_sql .= " AND r.status = '$status'";
        }
        if ($type) {
            $count_sql .= " AND r.reported_user_type = '$type'";
        }
        $count_sql .= ") as total_reports";

        $count_result = $conn->query($count_sql);
        $total_records = $count_result->fetch_assoc()['total'];
        $total_pages = ceil($total_records / $limit);

        // Fetch reports
        $sql = "SELECT r.*, 
                       ur.first_name as reporter_first_name, 
                       ur.last_name as reporter_last_name,
                       CASE 
                           WHEN r.reported_user_type = 'farmer' THEN CONCAT(u.first_name, ' ', u.last_name)
                           WHEN r.reported_user_type = 'buyer' THEN CONCAT(b.firstname, ' ', b.lastname)
                       END as reported_user_name
                FROM reports r
                LEFT JOIN users ur ON r.reporter_cbn_user_id = ur.user_id
                LEFT JOIN users u ON r.reported_cbn_user_id = u.user_id AND r.reported_user_type = 'farmer'
                LEFT JOIN buyers b ON r.reported_cbn_user_id = b.buyer_id AND r.reported_user_type = 'buyer'
                WHERE 1=1";

        if ($search) {
            $sql .= " AND (r.report_id LIKE '%$search%' OR u.first_name LIKE '%$search%' OR u.last_name LIKE '%$search%' OR b.firstname LIKE '%$search%' OR b.lastname LIKE '%$search%')";
        }
        if ($status) {
            $sql .= " AND r.status = '$status'";
        }
        if ($type) {
            $sql .= " AND r.reported_user_type = '$type'";
        }
        $sql .= " ORDER BY r.created_at DESC LIMIT ? OFFSET ?";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Database error: Unable to prepare statement.");
        }

        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        $reports = [];
        while ($row = $result->fetch_assoc()) {
            $reporter_name = $row['reporter_first_name'] . ' ' . $row['reporter_last_name'];
            
            $reports[] = [
                'report_id' => $row['report_id'],
                'reported_user_name' => $row['reported_user_name'],
                'reported_user_type' => $row['reported_user_type'],
                'reporter_name' => $reporter_name,
                'reason' => $row['reason'],
                'description' => $row['description'],
                'evidence' => $row['evidence'],
                'status' => $row['status'],
                'resolution_action' => $row['resolution_action'],
                'resolution_notes' => $row['resolution_notes'],
                'created_at' => $row['created_at']
            ];
        }

        sendResponse(true, '', [
            'reports' => $reports,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $total_pages,
                'total_records' => $total_records,
                'limit' => $limit
            ]
        ]);
    }

    // Handle POST requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = file_get_contents('php://input');
        if (empty($input)) {
            throw new Exception('Empty request body');
        }
        $data = json_decode($input, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON input: ' . json_last_error_msg());
        }

        // POST: Resolve report (send warning or disable user)
        if (isset($data['action']) && $data['action'] === 'resolve_report') {
            $report_id = intval($data['report_id']);
            $resolution_action = $data['resolution_action'];
            $resolution_notes = $data['resolution_notes'];
            $reported_user_id = isset($data['reported_user_id']) ? intval($data['reported_user_id']) : null;
            $reported_user_type = isset($data['reported_user_type']) ? $data['reported_user_type'] : null;

            // Update report status
            $sql = "UPDATE reports SET 
                    status = 'resolved',
                    resolution_action = ?,
                    resolution_notes = ?,
                    updated_at = CURRENT_TIMESTAMP
                    WHERE report_id = ?";
                    
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Database error: Unable to prepare statement.");
            }

            $stmt->bind_param('ssi', $resolution_action, $resolution_notes, $report_id);

            if (!$stmt->execute()) {
                throw new Exception("Failed to resolve report: " . $stmt->error);
            }

            // If disabling user, update their status
            if ($resolution_action === 'disabled' && $reported_user_id && $reported_user_type) {
                if ($reported_user_type === 'farmer') {
                    $disable_sql = "UPDATE users SET cbn_approved = 2 WHERE user_id = ?";
                } else {
                    $disable_sql = "UPDATE buyers SET is_verify = 0 WHERE buyer_id = ?";
                }
                
                $disable_stmt = $conn->prepare($disable_sql);
                if ($disable_stmt) {
                    $disable_stmt->bind_param('i', $reported_user_id);
                    $disable_stmt->execute();
                }
            }

            sendResponse(true, 'Report resolved successfully.');
        }

        // POST: Create new report (if needed for testing)
        if (isset($data['action']) && $data['action'] === 'create_report') {
            $reporter_cbn_user_id = intval($data['reporter_cbn_user_id']);
            $reported_cbn_user_id = intval($data['reported_cbn_user_id']);
            $reported_user_type = $data['reported_user_type'];
            $reason = $conn->real_escape_string($data['reason']);
            $description = isset($data['description']) ? $conn->real_escape_string($data['description']) : null;
            $evidence = isset($data['evidence']) ? $conn->real_escape_string($data['evidence']) : null;

            $sql = "INSERT INTO reports (reporter_cbn_user_id, reported_cbn_user_id, reported_user_type, reason, description, evidence) 
                    VALUES (?, ?, ?, ?, ?, ?)";
                    
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Database error: Unable to prepare statement.");
            }

            $stmt->bind_param('iissss', $reporter_cbn_user_id, $reported_cbn_user_id, $reported_user_type, $reason, $description, $evidence);

            if (!$stmt->execute()) {
                throw new Exception("Failed to create report: " . $stmt->error);
            }

            sendResponse(true, 'Report created successfully.');
        }
    }

} catch (Exception $e) {
    error_log("Reports API error: " . $e->getMessage());
    sendResponse(false, $e->getMessage());
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>