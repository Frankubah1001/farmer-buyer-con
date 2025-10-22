<?php
header('Content-Type: application/json');
session_start();
include 'DBcon.php';

if (!isset($_SESSION['buyer_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$order_id = $_POST['order_id'];
$new_status = $_POST['new_status'];

try {
    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Order status updated']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update status']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn->close();
?>