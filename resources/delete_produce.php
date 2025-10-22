<?php
include 'DBcon.php';
include 'session.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit();
}

if (!isset($_POST['prod_id']) || !is_numeric($_POST['prod_id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid listing ID']);
    exit();
}

$listingId = $_POST['prod_id'];
$userId = $_SESSION['user_id'];

// *** NEW CHECK: Verify listing exists ***
$checkSql = "SELECT prod_id FROM produce_listings WHERE prod_id = ? AND user_id = ?";
$checkStmt = mysqli_prepare($conn, $checkSql);
if ($checkStmt) {
    mysqli_stmt_bind_param($checkStmt, "ii", $listingId, $userId);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt); // Necessary before num_rows
    $numRows = mysqli_stmt_num_rows($checkStmt);
    mysqli_stmt_close($checkStmt);

    if ($numRows == 0) {
        echo json_encode(['success' => false, 'error' => 'Listing not found or does not belong to the user']);
        exit();
    }
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    exit();
}
// *** END NEW CHECK ***

$sql = "UPDATE produce_listings SET is_deleted = TRUE WHERE prod_id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ii", $listingId, $userId);
    if (mysqli_stmt_execute($stmt)) {
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Listing not found or does not belong to the user']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
}

mysqli_close($conn);
?>