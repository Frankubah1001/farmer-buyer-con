<?php
session_start();
include 'DBcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_SESSION['email'])) {
    $order_id = intval($_POST['order_id']);
    $userEmail = $_SESSION['email'];

    // Get user ID
    $stmt_user = mysqli_prepare($conn, "SELECT buyer_id FROM buyers WHERE email = ?");
    mysqli_stmt_bind_param($stmt_user, "s", $userEmail);
    mysqli_stmt_execute($stmt_user);
    $result = mysqli_stmt_get_result($stmt_user);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        $buyer_id = $user['buyer_id'];

        // Update order status to read
        $stmt_update = mysqli_prepare($conn, "UPDATE orders SET notification_status = 'read' WHERE order_id = ? AND buyer_id = ?");
        mysqli_stmt_bind_param($stmt_update, "ii", $order_id, $buyer_id);
        mysqli_stmt_execute($stmt_update);

        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "User not found"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Invalid request"]);
}
?>
