<?php
// update_order_status.php
include 'DBcon.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_POST['order_id']) ? mysqli_real_escape_string($conn, $_POST['order_id']) : 0;
$new_status = isset($_POST['status']) ? mysqli_real_escape_string($conn, $_POST['status']) : '';

if ($order_id <= 0 || empty($new_status)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid order ID or status']);
    exit();
}

// Verify Ownership (Ensure the user updating is the farmer)
$check_ownership_sql = "SELECT pl.user_id 
                        FROM orders o
                        JOIN produce_listings pl ON o.produce_id = pl.prod_id
                        WHERE o.order_id = $order_id";
$check_ownership_result = mysqli_query($conn, $check_ownership_sql);

if (!$check_ownership_result || mysqli_num_rows($check_ownership_result) == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Order not found']);
    exit();
}

$ownership_data = mysqli_fetch_assoc($check_ownership_result);
if ($ownership_data['user_id'] != $user_id) {
    echo json_encode(['status' => 'error', 'message' => 'You do not have permission to update this order']);
    exit();
}

$sql = "UPDATE orders SET order_status = '$new_status' WHERE order_id = $order_id";

if (mysqli_query($conn, $sql)) {
    // Update delivery_status in produce_listings
    $update_produce_sql = "UPDATE produce_listings pl
                           JOIN orders o ON pl.prod_id = o.produce_id
                           SET pl.order_status = '$new_status'
                           WHERE o.order_id = $order_id";
    mysqli_query($conn, $update_produce_sql); // Execute the update (no error checking here for brevity, add it!)

    echo json_encode(['status' => 'success', 'message' => 'Order status updated']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update order status: ' . mysqli_error($conn)]);
}

mysqli_close($conn);
?>