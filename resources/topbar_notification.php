<?php
header('Content-Type: application/json');
session_start();
include 'DBcon.php';

$response = ['debug' => []];
$limit = 5;

if (isset($_SESSION['email'])) {
    $userEmail = $_SESSION['email'];

    $sql_select_user = "SELECT user_id FROM users WHERE email = ?";
    $stmt_select_user = mysqli_prepare($conn, $sql_select_user);
    mysqli_stmt_bind_param($stmt_select_user, "s", $userEmail);
    mysqli_stmt_execute($stmt_select_user);
    $result_select_user = mysqli_stmt_get_result($stmt_select_user);

    if ($user = mysqli_fetch_assoc($result_select_user)) {
        $user_id = $user['user_id'];

        // Count unread orders
        $sql_count_unread = "SELECT COUNT(*) AS orderCount FROM orders WHERE user_id = ? AND notification_status = 'unread'";
        $stmt_count = mysqli_prepare($conn, $sql_count_unread);
        mysqli_stmt_bind_param($stmt_count, "i", $user_id);
        mysqli_stmt_execute($stmt_count);
        $result_count = mysqli_stmt_get_result($stmt_count);
        $row_count = mysqli_fetch_assoc($result_count);
        $response['orderCount'] = $row_count['orderCount'];

        // Get recent unread orders with produce details and buyer info
        $sql_recent = "SELECT o.order_id, o.order_date, o.total_amount, o.order_status, o.quantity, 
                       pl.produce, b.first_name as buyer_first_name, b.last_name as buyer_last_name
                FROM orders o
                LEFT JOIN produce_listings pl ON o.produce_id = pl.prod_id
                LEFT JOIN buyers b ON o.buyer_id = b.buyer_id
                WHERE o.user_id = ? 
                AND o.notification_status = 'unread' 
                ORDER BY o.order_date DESC 
                LIMIT ?";
        $stmt_recent = mysqli_prepare($conn, $sql_recent);
        mysqli_stmt_bind_param($stmt_recent, "ii", $user_id, $limit);
        mysqli_stmt_execute($stmt_recent);
        $result_recent = mysqli_stmt_get_result($stmt_recent);
        $recentOrders = [];
        while ($row = mysqli_fetch_assoc($result_recent)) {
            $recentOrders[] = $row;
        }
        $response['recentOrders'] = $recentOrders;

    } else {
        $response['orderCount'] = 0;
        $response['recentOrders'] = [];
        $response['error'] = "User not found";
    }
} else {
    $response['error'] = 'User not authenticated';
    http_response_code(401);
}

echo json_encode($response);
?>
