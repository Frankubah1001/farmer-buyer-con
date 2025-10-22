<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../DBcon.php';
session_start();

header('Content-Type: application/json');

// Get user ID from session
$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id <= 0) {
    echo json_encode(['error' => 'Farmer not logged in.']);
    exit;
}

// Current date for filters
$today = date('Y-m-d');
$current_month = date('Y-m');
$current_year = date('Y');

// Total number of farm products
$sql_products = "SELECT COUNT(*) as total FROM produce_listings WHERE user_id = ? AND is_deleted = 0";
$stmt_products = $conn->prepare($sql_products);
$stmt_products->bind_param('i', $user_id);
$stmt_products->execute();
$result_products = $stmt_products->get_result();
$total_products = $result_products->fetch_assoc()['total'];
$stmt_products->close();

// Frequent produce (most ordered among paid orders)
$sql_frequent = "SELECT pl.produce, COUNT(o.order_id) as order_count 
                 FROM orders o 
                 JOIN produce_listings pl ON o.produce_id = pl.prod_id 
                 WHERE o.user_id = ? AND o.payment_status = 'Paid'
                 GROUP BY pl.produce 
                 ORDER BY order_count DESC 
                 LIMIT 1";
$stmt_frequent = $conn->prepare($sql_frequent);
$stmt_frequent->bind_param('i', $user_id);
$stmt_frequent->execute();
$result_frequent = $stmt_frequent->get_result();
$frequent_produce = $result_frequent->num_rows > 0 ? $result_frequent->fetch_assoc()['produce'] : 'None';
$stmt_frequent->close();

// Total orders (paid only)
$sql_total_orders = "SELECT COUNT(*) as total FROM orders WHERE user_id = ? AND payment_status = 'Paid'";
$stmt_total_orders = $conn->prepare($sql_total_orders);
$stmt_total_orders->bind_param('i', $user_id);
$stmt_total_orders->execute();
$result_total_orders = $stmt_total_orders->get_result();
$total_orders = $result_total_orders->fetch_assoc()['total'];
$stmt_total_orders->close();

// Monthly orders (paid only)
$sql_monthly_orders = "SELECT COUNT(*) as total FROM orders WHERE user_id = ? AND DATE_FORMAT(order_date, '%Y-%m') = ? AND payment_status = 'Paid'";
$stmt_monthly_orders = $conn->prepare($sql_monthly_orders);
$stmt_monthly_orders->bind_param('is', $user_id, $current_month);
$stmt_monthly_orders->execute();
$result_monthly_orders = $stmt_monthly_orders->get_result();
$monthly_orders = $result_monthly_orders->fetch_assoc()['total'];
$stmt_monthly_orders->close();

// Yearly orders (paid only)
$sql_yearly_orders = "SELECT COUNT(*) as total FROM orders WHERE user_id = ? AND YEAR(order_date) = ? AND payment_status = 'Paid'";
$stmt_yearly_orders = $conn->prepare($sql_yearly_orders);
$stmt_yearly_orders->bind_param('ii', $user_id, $current_year);
$stmt_yearly_orders->execute();
$result_yearly_orders = $stmt_yearly_orders->get_result();
$yearly_orders = $result_yearly_orders->fetch_assoc()['total'];
$stmt_yearly_orders->close();

// Monthly earnings (from paid orders)
$sql_monthly_earnings = "SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE user_id = ? AND DATE_FORMAT(order_date, '%Y-%m') = ? AND payment_status = 'Paid'";
$stmt_monthly_earnings = $conn->prepare($sql_monthly_earnings);
$stmt_monthly_earnings->bind_param('is', $user_id, $current_month);
$stmt_monthly_earnings->execute();
$result_monthly_earnings = $stmt_monthly_earnings->get_result();
$monthly_earnings = $result_monthly_earnings->fetch_assoc()['total'];
$stmt_monthly_earnings->close();

// Annual earnings (from paid orders)
$sql_annual_earnings = "SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE user_id = ? AND YEAR(order_date) = ? AND payment_status = 'Paid'";
$stmt_annual_earnings = $conn->prepare($sql_annual_earnings);
$stmt_annual_earnings->bind_param('ii', $user_id, $current_year);
$stmt_annual_earnings->execute();
$result_annual_earnings = $stmt_annual_earnings->get_result();
$annual_earnings = $result_annual_earnings->fetch_assoc()['total'];
$stmt_annual_earnings->close();

// Most recent transactions (paid only, ordered by recent date)
$sql_recent = "SELECT 
                  COALESCE(CONCAT(b.firstname, ' ', b.lastname), 'Unknown Buyer') AS buyer_name, 
                  COALESCE(b.address) AS buyer_location, 
                  pl.produce, 
                  o.quantity, 
                  o.total_amount AS amount, 
                  DATE_FORMAT(o.order_date, '%Y/%m/%d') AS order_date 
               FROM orders o 
               JOIN produce_listings pl ON o.produce_id = pl.prod_id 
               LEFT JOIN buyers b ON o.buyer_id = b.buyer_id 
               WHERE o.user_id = ? AND o.payment_status = 'Paid'
               ORDER BY o.order_date DESC 
               LIMIT 3";
$stmt_recent = $conn->prepare($sql_recent);
$stmt_recent->bind_param('i', $user_id);
$stmt_recent->execute();
$result_recent = $stmt_recent->get_result();
$recent_transactions = [];
while ($row = $result_recent->fetch_assoc()) {
    $recent_transactions[] = $row;
}
$stmt_recent->close();

echo json_encode([
    'total_products' => $total_products,
    'frequent_produce' => $frequent_produce,
    'total_orders' => $total_orders,
    'monthly_orders' => $monthly_orders,
    'yearly_orders' => $yearly_orders,
    'monthly_earnings' => $monthly_earnings,
    'annual_earnings' => $annual_earnings,
    'recent_transactions' => $recent_transactions
]);
?>