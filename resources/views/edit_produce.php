<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'DBcon.php';
session_start();

header('Content-Type: application/json');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit();
}

$user_id   = $_SESSION['user_id']; 
$produce_id = isset($_POST['prod_id']) ? intval($_POST['prod_id']) : 0;

if ($produce_id <= 0) {
    echo json_encode(['error' => 'Invalid produce ID']);
    exit();
}

// Step 1: Check that this produce belongs to logged-in farmer
$check_sql = "SELECT user_id FROM produce_listings WHERE prod_id = $produce_id";
$check_result = mysqli_query($conn, $check_sql);

if (!$check_result || mysqli_num_rows($check_result) == 0) {
    echo json_encode(['error' => 'Produce listing not found']);
    exit();
}

$row = mysqli_fetch_assoc($check_result);
if ($row['user_id'] != $user_id) {
    echo json_encode(['error' => 'You do not own this produce listing']);
    exit();
}

// Step 2: Update only the latest order for this produce
$update_order_sql = "
    UPDATE orders 
    SET order_status = 'Sold'
    WHERE order_id = (
        SELECT order_id 
        FROM (
            SELECT order_id 
            FROM orders 
            WHERE produce_id = $produce_id 
            ORDER BY created_at DESC 
            LIMIT 1
        ) AS sub
    )
";

if (!mysqli_query($conn, $update_order_sql)) {
    echo json_encode(['error' => 'Failed to update latest order: ' . mysqli_error($conn)]);
    exit();
}

// Step 3: Optionally mark the produce itself as unavailable
$update_produce_sql = "UPDATE produce_listings 
                       SET available = 0 
                       WHERE prod_id = $produce_id";
mysqli_query($conn, $update_produce_sql);

echo json_encode([
    'success' => true,
    'message' => 'Latest order marked as Sold and button disabled'
]);

mysqli_close($conn);
?>
