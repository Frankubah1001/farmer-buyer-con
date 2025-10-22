<?php
session_start();
require_once 'DBcon.php'; // Your database connection file

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Invalid request.'];

// Check if CBN user is logged in
if (!isset($_SESSION['cbn_user_id'])) {
    $response['message'] = 'Unauthorized access.';
    echo json_encode($response);
    exit();
}

$cbn_user_id = $_SESSION['cbn_user_id']; // The CBN user performing the action

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $action = $_GET['action'] ?? '';

    if ($action === 'get_farmers') {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $statusFilter = isset($_GET['status']) ? $_GET['status'] : 'pending'; // Default to pending
        $itemsPerPage = 10;
        $offset = ($page - 1) * $itemsPerPage;

        $cbn_approved_value = 0; // Default for 'pending'

        // Map status string to tinyint value
        switch ($statusFilter) {
            case 'pending': $cbn_approved_value = 0; break;
            case 'approved': $cbn_approved_value = 1; break;
            case 'rejected': $cbn_approved_value = 2; break;
            default: $cbn_approved_value = 0; // Fallback to pending
        }

        // Count total farmers
        $countSql = "SELECT COUNT(*) AS total_farmers FROM users WHERE cbn_approved = ?";
        $countStmt = mysqli_prepare($conn, $countSql);
        if ($countStmt) {
            mysqli_stmt_bind_param($countStmt, "i", $cbn_approved_value);
            mysqli_stmt_execute($countStmt);
            $totalFarmers = mysqli_fetch_assoc(mysqli_stmt_get_result($countStmt))['total_farmers'];
            mysqli_stmt_close($countStmt);
        } else {
            $response['message'] = 'Database error counting farmers.';
            error_log("CBN Farmers API: Error counting farmers: " . mysqli_error($conn));
            echo json_encode($response);
            exit();
        }

        $totalPages = ceil($totalFarmers / $itemsPerPage);

        // Fetch farmers data
        $sql = "SELECT user_id, first_name, last_name, email, phone, address, cbn_approved
                FROM users
                WHERE cbn_approved = ?
                ORDER BY created_at ASC
                LIMIT ?, ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "iii", $cbn_approved_value, $offset, $itemsPerPage);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $farmers = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $farmers[] = $row;
            }
            mysqli_stmt_close($stmt);

            $response['status'] = 'success';
            $response['message'] = 'Farmers data fetched successfully.';
            $response['data'] = $farmers;
            $response['total_pages'] = $totalPages;
        } else {
            $response['message'] = 'Database error fetching farmers.';
            error_log("CBN Farmers API: Error fetching farmers: " . mysqli_error($conn));
        }
    } elseif ($action === 'get_counts') {
        // Fetch counts for dashboard cards
        $pendingFarmersSql = "SELECT COUNT(*) AS count FROM users WHERE cbn_approved = 0";
        $approvedFarmersSql = "SELECT COUNT(*) AS count FROM users WHERE cbn_approved = 1";
        $regulatedProduceSql = "SELECT COUNT(*) AS count FROM produce_prices_cbn";

        $pendingCount = mysqli_fetch_assoc(mysqli_query($conn, $pendingFarmersSql))['count'];
        $approvedCount = mysqli_fetch_assoc(mysqli_query($conn, $approvedFarmersSql))['count'];
        $regulatedCount = mysqli_fetch_assoc(mysqli_query($conn, $regulatedProduceSql))['count'];

        $response['status'] = 'success';
        $response['message'] = 'Counts fetched successfully.';
        $response['pending_farmers'] = (int)$pendingCount;
        $response['approved_farmers'] = (int)$approvedCount;
        $response['regulated_produce_types'] = (int)$regulatedCount;

    } else {
        $response['message'] = 'Unknown GET action.';
    }

} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_status') {
        $user_id_to_update = $_POST['user_id'] ?? 0;
        $new_status = $_POST['status'] ?? 0; // 0=Pending, 1=Approved, 2=Rejected
        $cbn_user_id_performing_action = $_POST['cbn_user_id'] ?? $_SESSION['cbn_user_id']; // Fallback to session

        if ($user_id_to_update <= 0 || !in_array($new_status, [0, 1, 2])) {
            $response['message'] = 'Invalid user ID or status.';
            echo json_encode($response);
            exit();
        }

        $updateSql = "UPDATE users SET cbn_approved = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $updateSql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ii", $new_status, $user_id_to_update);
            if (mysqli_stmt_execute($stmt)) {
                $response['status'] = 'success';
                $status_text = '';
                switch($new_status) {
                    case 1: $status_text = 'approved'; break;
                    case 2: $status_text = 'rejected'; break;
                    default: $status_text = 'status updated'; break;
                }
                $response['message'] = "Farmer (ID: {$user_id_to_update}) has been {$status_text}.";
                // Log the action (optional but good practice)
                error_log("CBN User {$cbn_user_id_performing_action} {$status_text} farmer {$user_id_to_update}.");
            } else {
                $response['message'] = 'Failed to update farmer status.';
                error_log("CBN Farmers API: Error updating farmer status: " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
        } else {
            $response['message'] = 'Database error preparing update statement.';
            error_log("CBN Farmers API: Error preparing update statement: " . mysqli_error($conn));
        }
    } else {
        $response['message'] = 'Unknown POST action.';
    }
}

mysqli_close($conn);
echo json_encode($response);
?>
