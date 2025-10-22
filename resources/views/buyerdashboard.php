<?php
header('Content-Type: application/json');
session_start();
include 'DBcon.php';

// Check for buyer ID
if (!isset($_SESSION['buyer_id'])) {
    echo json_encode(['error' => 'Buyer not authenticated']);
    exit;
}
$buyer_id = $_SESSION['buyer_id'];

// 1. Most Recent Orders (Fetching last 3)
$recent_order_query = "SELECT o.order_id, o.total_amount, o.order_date, pl.produce, o.quantity, o.order_status
                      FROM orders o
                      JOIN produce_listings pl ON o.produce_id = pl.prod_id
                      WHERE o.buyer_id = ?
                      ORDER BY o.created_at DESC
                      LIMIT 3";
$recent_order_stmt = $conn->prepare($recent_order_query);
$recent_order_stmt->bind_param("i", $buyer_id);
$recent_order_stmt->execute();
$recent_order_result = $recent_order_stmt->get_result();

$recent_orders = [];
while ($recent_order_data = $recent_order_result->fetch_assoc()) {
    $recent_orders[] = [
        'order_id' => 'ORD-' . $recent_order_data['order_id'],
        'total_amount' => '₦' . number_format($recent_order_data['total_amount'], 2),
        'order_date' => $recent_order_data['order_date'],
        'produce' => $recent_order_data['produce'],
        'quantity' => $recent_order_data['quantity'],
        'status' => $recent_order_data['order_status']
    ];
}
$recent_order_stmt->close();

// 2. Most Farm Produce Ordered
$most_produce_query = "SELECT pl.produce, SUM(o.quantity) AS total_quantity
                      FROM orders o
                      JOIN produce_listings pl ON o.produce_id = pl.prod_id
                      WHERE o.buyer_id = ?
                      GROUP BY pl.produce
                      ORDER BY total_quantity DESC
                      LIMIT 1";
$most_produce_stmt = $conn->prepare($most_produce_query);
$most_produce_stmt->bind_param("i", $buyer_id);
$most_produce_stmt->execute();
$most_produce_result = $most_produce_stmt->get_result();

if ($most_produce_result->num_rows > 0) {
    $most_produce_data = $most_produce_result->fetch_assoc();
    $most_purchased_produce = [
        'produce' => $most_produce_data['produce'],
        'quantity' => $most_produce_data['total_quantity'],
    ];
} else {
    $most_purchased_produce = null;
}
$most_produce_stmt->close();

// 3. Farmers Info (Limited to those the buyer has ordered from)
$farmers_query = "SELECT u.user_id, u.first_name, u.last_name, u.address, AVG(r.rating) AS average_rating
                  FROM users u
                  LEFT JOIN ratings r ON u.user_id = r.user_id
                  WHERE u.user_id IN (SELECT DISTINCT o.user_id FROM orders o WHERE o.buyer_id = ?)
                  GROUP BY u.user_id
                  LIMIT 3";
$farmers_stmt = $conn->prepare($farmers_query);
$farmers_stmt->bind_param("i", $buyer_id);
$farmers_stmt->execute();
$farmers_result = $farmers_stmt->get_result();

$farmers = [];
while ($farmer_data = $farmers_result->fetch_assoc()) {
    $farmers[] = [
        'user_id' => $farmer_data['user_id'],
        'first_name' => $farmer_data['first_name'],
        'last_name' => $farmer_data['last_name'],
        'location' => $farmer_data['address'],
        'rating' => round($farmer_data['average_rating'], 1) ?? 0,
    ];
}
$farmers_stmt->close();

// 4. Fetch all farmers for the rating dropdown
$all_farmers_query = "SELECT user_id, first_name, last_name FROM users WHERE user_id IN (SELECT DISTINCT user_id FROM produce_listings)";
$all_farmers_stmt = $conn->prepare($all_farmers_query);
$all_farmers_stmt->execute();
$all_farmers_result = $all_farmers_stmt->get_result();
$all_farmers = $all_farmers_result->fetch_all(MYSQLI_ASSOC);
$all_farmers_stmt->close();

// 5. Fetch the most recent order with status
$order_status_query = "SELECT o.order_id, o.order_date, pl.produce, o.order_status, o.user_id as farmer_id
                      FROM orders o
                      JOIN produce_listings pl ON o.produce_id = pl.prod_id
                      WHERE o.buyer_id = ?
                      ORDER BY o.created_at DESC
                      LIMIT 1";
$order_status_stmt = $conn->prepare($order_status_query);
$order_status_stmt->bind_param("i", $buyer_id);
$order_status_stmt->execute();
$order_status_result = $order_status_stmt->get_result();

$order_status = null;
if ($order_status_result->num_rows > 0) {
    $order_status_data = $order_status_result->fetch_assoc();
    $order_status = [
        'order_id' => 'ORD-' . $order_status_data['order_id'],
        'produce' => $order_status_data['produce'],
        'status' => $order_status_data['order_status'],
        'order_date' => $order_status_data['order_date'],
        'farmer_id' => $order_status_data['farmer_id']
    ];
}
$order_status_stmt->close();

$conn->close();

echo json_encode([
    'recent_orders' => $recent_orders,
    'most_purchased_produce' => $most_purchased_produce,
    'farmers' => $farmers,
    'all_farmers' => $all_farmers,
    'order_status' => $order_status
]);
?>