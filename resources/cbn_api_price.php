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

    if ($action === 'get_prices') {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $itemsPerPage = 10;
        $offset = ($page - 1) * $itemsPerPage;

        // Count total prices
        $countSql = "SELECT COUNT(*) AS total_prices FROM produce_prices_cbn";
        $totalPrices = mysqli_fetch_assoc(mysqli_query($conn, $countSql))['total_prices'];
        $totalPages = ceil($totalPrices / $itemsPerPage);

        // Fetch prices data
        $sql = "SELECT price_id, produce_name, min_price_per_unit, max_price_per_unit, updated_at
                FROM produce_prices_cbn
                ORDER BY produce_name ASC
                LIMIT ?, ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ii", $offset, $itemsPerPage);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $prices = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $prices[] = $row;
            }
            mysqli_stmt_close($stmt);

            $response['status'] = 'success';
            $response['message'] = 'Prices data fetched successfully.';
            $response['data'] = $prices;
            $response['total_pages'] = $totalPages;
        } else {
            $response['message'] = 'Database error fetching prices.';
            error_log("CBN Prices API: Error fetching prices: " . mysqli_error($conn));
        }
    } else {
        $response['message'] = 'Unknown GET action.';
    }

} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_price') {
        $produceName = $_POST['produce_name'] ?? '';
        $minPrice = floatval($_POST['min_price'] ?? 0);
        $maxPrice = floatval($_POST['max_price'] ?? 0);

        if (empty($produceName) || $minPrice < 0 || $maxPrice < 0 || $minPrice > $maxPrice) {
            $response['message'] = 'Invalid input for adding price.';
            echo json_encode($response);
            exit();
        }

        // Check if produce name already exists
        $checkSql = "SELECT COUNT(*) FROM produce_prices_cbn WHERE produce_name = ?";
        $checkStmt = mysqli_prepare($conn, $checkSql);
        mysqli_stmt_bind_param($checkStmt, "s", $produceName);
        mysqli_stmt_execute($checkStmt);
        $exists = mysqli_fetch_assoc(mysqli_stmt_get_result($checkStmt))['COUNT(*)'];
        mysqli_stmt_close($checkStmt);

        if ($exists > 0) {
            $response['message'] = 'Produce name already has a regulated price. Use edit to update.';
            echo json_encode($response);
            exit();
        }

        $insertSql = "INSERT INTO produce_prices_cbn (produce_name, min_price_per_unit, max_price_per_unit, last_updated_by) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insertSql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sddi", $produceName, $minPrice, $maxPrice, $cbn_user_id);
            if (mysqli_stmt_execute($stmt)) {
                $response['status'] = 'success';
                $response['message'] = "Price for {$produceName} added successfully.";
            } else {
                $response['message'] = 'Failed to add price.';
                error_log("CBN Prices API: Error adding price: " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
        } else {
            $response['message'] = 'Database error preparing add price statement.';
            error_log("CBN Prices API: Error preparing add price statement: " . mysqli_error($conn));
        }

    } elseif ($action === 'update_price') {
        $priceId = $_POST['price_id'] ?? 0;
        $produceName = $_POST['produce_name'] ?? '';
        $minPrice = floatval($_POST['min_price'] ?? 0);
        $maxPrice = floatval($_POST['max_price'] ?? 0);

        if ($priceId <= 0 || empty($produceName) || $minPrice < 0 || $maxPrice < 0 || $minPrice > $maxPrice) {
            $response['message'] = 'Invalid input for updating price.';
            echo json_encode($response);
            exit();
        }

        // Check if produce name already exists for a DIFFERENT price_id
        $checkSql = "SELECT COUNT(*) FROM produce_prices_cbn WHERE produce_name = ? AND price_id != ?";
        $checkStmt = mysqli_prepare($conn, $checkSql);
        mysqli_stmt_bind_param($checkStmt, "si", $produceName, $priceId);
        mysqli_stmt_execute($checkStmt);
        $exists = mysqli_fetch_assoc(mysqli_stmt_get_result($checkStmt))['COUNT(*)'];
        mysqli_stmt_close($checkStmt);

        if ($exists > 0) {
            $response['message'] = 'Produce name already exists for another regulation. Please use a unique name.';
            echo json_encode($response);
            exit();
        }

        $updateSql = "UPDATE produce_prices_cbn SET produce_name = ?, min_price_per_unit = ?, max_price_per_unit = ?, last_updated_by = ? WHERE price_id = ?";
        $stmt = mysqli_prepare($conn, $updateSql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sddii", $produceName, $minPrice, $maxPrice, $cbn_user_id, $priceId);
            if (mysqli_stmt_execute($stmt)) {
                $response['status'] = 'success';
                $response['message'] = "Price for {$produceName} updated successfully.";
            } else {
                $response['message'] = 'Failed to update price.';
                error_log("CBN Prices API: Error updating price: " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
        } else {
            $response['message'] = 'Database error preparing update price statement.';
            error_log("CBN Prices API: Error preparing update price statement: " . mysqli_error($conn));
        }

    } elseif ($action === 'delete_price') {
        $priceId = $_POST['price_id'] ?? 0;

        if ($priceId <= 0) {
            $response['message'] = 'Invalid price ID for deletion.';
            echo json_encode($response);
            exit();
        }

        $deleteSql = "DELETE FROM produce_prices_cbn WHERE price_id = ?";
        $stmt = mysqli_prepare($conn, $deleteSql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $priceId);
            if (mysqli_stmt_execute($stmt)) {
                $response['status'] = 'success';
                $response['message'] = "Price regulation (ID: {$priceId}) deleted successfully.";
            } else {
                $response['message'] = 'Failed to delete price.';
                error_log("CBN Prices API: Error deleting price: " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
        } else {
            $response['message'] = 'Database error preparing delete price statement.';
            error_log("CBN Prices API: Error preparing delete price statement: " . mysqli_error($conn));
        }

    } else {
        $response['message'] = 'Unknown POST action.';
    }
}

mysqli_close($conn);
echo json_encode($response);
?>
