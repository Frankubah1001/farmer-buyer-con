<?php
session_start();
require_once 'DBcon.php';

header('Content-Type: application/json');

$response = ['success' => false, 'error' => ''];

try {
    if (!isset($_SESSION['cbn_user_id'])) {
        throw new Exception('Unauthorized access');
    }

    $admin_id = $_SESSION['cbn_user_id'];

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = $_GET['action'] ?? '';

        if ($action === 'get_reports') {
            // Pagination and filtering
            $page = max(1, intval($_GET['page'] ?? 1));
            $limit = 10;
            $offset = ($page - 1) * $limit;

            // Build WHERE clause
            $where = [];
            $params = [];
            $types = '';

            // Status filter
            if (!empty($_GET['status'])) {
                $where[] = "r.status = ?";
                $params[] = $_GET['status'];
                $types .= 's';
            }

            // Urgency filter
            if (!empty($_GET['urgency'])) {
                $where[] = "r.urgency_level = ?";
                $params[] = $_GET['urgency'];
                $types .= 's';
            }

            // Date filter
            if (!empty($_GET['date'])) {
                $where[] = "DATE(r.created_at) = ?";
                $params[] = $_GET['date'];
                $types .= 's';
            }

            $whereClause = $where ? "WHERE " . implode(" AND ", $where) : "";

            // Count total records
            $countSql = "SELECT COUNT(*) as total FROM reports r $whereClause";
            $countStmt = $conn->prepare($countSql);
            if ($where) {
                $countStmt->bind_param($types, ...$params);
            }
            $countStmt->execute();
            $totalResult = $countStmt->get_result()->fetch_assoc();
            $totalRecords = $totalResult['total'];
            $totalPages = ceil($totalRecords / $limit);
            $countStmt->close();

            // Fetch reports with buyer and farmer info
            $sql = "SELECT 
                    r.report_id,
                    r.buyer_id,
                    r.reported_user_type,
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
                    b.firstname as buyer_firstname,
                    b.lastname as buyer_lastname,
                    b.email as buyer_email,
                    b.phone as buyer_phone,
                    f.firstname as farmer_firstname,
                    f.lastname as farmer_lastname,
                    f.email as farmer_email
                FROM reports r
                LEFT JOIN buyers b ON r.buyer_id = b.buyer_id
                LEFT JOIN farmers f ON r.reported_user_type = 'farmer' 
                $whereClause
                ORDER BY r.created_at DESC
                LIMIT ? OFFSET ?";

            $stmt = $conn->prepare($sql);
            $params[] = $limit;
            $params[] = $offset;
            $types .= 'ii';

            if ($where) {
                $stmt->bind_param($types, ...$params);
            } else {
                $stmt->bind_param('ii', $limit, $offset);
            }

            $stmt->execute();
            $result = $stmt->get_result();
            $reports = [];

            while ($row = $result->fetch_assoc()) {
                $reports[] = $row;
            }

            $response['success'] = true;
            $response['data'] = [
                'reports' => $reports,
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $totalRecords
            ];

            $stmt->close();

        } elseif ($action === 'get_stats') {
            // Get report statistics
            $statsSql = "SELECT 
                    COUNT(*) as total_reports,
                    SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN urgency_level = 'high' THEN 1 ELSE 0 END) as high_urgency
                FROM reports";

            $stmt = $conn->prepare($statsSql);
            $stmt->execute();
            $stats = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            $response['success'] = true;
            $response['data'] = $stats;

        } elseif ($action === 'export') {
            // Export to Excel
            $where = [];
            $params = [];
            $types = '';

            // Apply same filters as listing
            if (!empty($_GET['status'])) {
                $where[] = "r.status = ?";
                $params[] = $_GET['status'];
                $types .= 's';
            }

            if (!empty($_GET['urgency'])) {
                $where[] = "r.urgency_level = ?";
                $params[] = $_GET['urgency'];
                $types .= 's';
            }

            if (!empty($_GET['date'])) {
                $where[] = "DATE(r.created_at) = ?";
                $params[] = $_GET['date'];
                $types .= 's';
            }

            $whereClause = $where ? "WHERE " . implode(" AND ", $where) : "";

            $exportSql = "SELECT 
                    r.report_id as 'Report ID',
                    CONCAT(b.firstname, ' ', b.lastname) as 'Buyer Name',
                    CONCAT(f.firstname, ' ', f.lastname) as 'Farmer Name',
                    r.order_number as 'Order Number',
                    r.produce_name as 'Produce',
                    r.issue_type as 'Issue Type',
                    r.reason as 'Reason',
                    r.description as 'Description',
                    r.urgency_level as 'Urgency Level',
                    r.status as 'Status',
                    r.resolution_action as 'Resolution Action',
                    r.resolution_notes as 'Resolution Notes',
                    r.created_at as 'Created Date',
                    r.updated_at as 'Updated Date'
                FROM reports r
                LEFT JOIN buyers b ON r.buyer_id = b.buyer_id
                LEFT JOIN farmers f ON r.reported_user_type = 'farmer'
                $whereClause
                ORDER BY r.created_at DESC";

            $stmt = $conn->prepare($exportSql);
            if ($where) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $exportData = [];

            while ($row = $result->fetch_assoc()) {
                $exportData[] = $row;
            }

            $response['success'] = true;
            $response['data'] = $exportData;
            $stmt->close();
        }

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'resolve_report') {
            $report_id = intval($_POST['report_id']);
            $resolution_notes = trim($_POST['resolution_notes'] ?? '');
            $resolution_action = $_POST['resolution_action'] ?? 'none';

            if (empty($resolution_notes)) {
                throw new Exception('Resolution notes are required');
            }

            $updateSql = "UPDATE reports SET 
                        status = 'resolved',
                        resolution_action = ?,
                        resolution_notes = ?,
                        updated_at = NOW()
                    WHERE report_id = ?";

            $stmt = $conn->prepare($updateSql);
            $stmt->bind_param('ssi', $resolution_action, $resolution_notes, $report_id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Report resolved successfully';
            } else {
                throw new Exception('Failed to resolve report');
            }
            $stmt->close();

        } elseif ($action === 'delete_report') {
            $report_id = intval($_POST['report_id']);

            $deleteSql = "DELETE FROM reports WHERE report_id = ?";
            $stmt = $conn->prepare($deleteSql);
            $stmt->bind_param('i', $report_id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Report deleted successfully';
            } else {
                throw new Exception('Failed to delete report');
            }
            $stmt->close();
        }
    }

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
} finally {
    echo json_encode($response);
    if (isset($conn)) {
        $conn->close();
    }
}
?>