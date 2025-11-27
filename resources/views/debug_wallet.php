<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../DBcon.php';

// Get user ID from session
$user_id = $_SESSION['user_id'] ?? 0;

echo "<h2>Wallet Balance Debug Information</h2>";
echo "<p><strong>User ID from session:</strong> " . $user_id . "</p>";

if ($user_id <= 0) {
    echo "<p style='color: red;'>ERROR: No user logged in!</p>";
    echo "<p>Session data: <pre>" . print_r($_SESSION, true) . "</pre></p>";
    exit;
}

$current_year = date('Y');

// Check total orders
$sql_check = "SELECT order_id, total_amount, payment_status, order_date FROM orders WHERE user_id = ? LIMIT 10";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<h3>Orders for User ID: $user_id</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Order ID</th><th>Amount</th><th>Payment Status</th><th>Date</th></tr>";

$total_found = 0;
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['order_id'] . "</td>";
    echo "<td>₦" . number_format($row['total_amount'], 2) . "</td>";
    echo "<td>" . $row['payment_status'] . "</td>";
    echo "<td>" . $row['order_date'] . "</td>";
    echo "</tr>";
    $total_found++;
}
echo "</table>";
echo "<p><strong>Total orders found:</strong> $total_found</p>";

// Calculate annual earnings
$sql_annual = "SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE user_id = ? AND YEAR(order_date) = ? AND payment_status = 'Paid'";
$stmt_annual = $conn->prepare($sql_annual);
$stmt_annual->bind_param('ii', $user_id, $current_year);
$stmt_annual->execute();
$result_annual = $stmt_annual->get_result();
$annual_earnings = $result_annual->fetch_assoc()['total'];

echo "<h3>Wallet Calculation:</h3>";
echo "<p><strong>Annual Earnings (Year $current_year, Paid orders):</strong> ₦" . number_format($annual_earnings, 2) . "</p>";

$platform_fee = $annual_earnings * 0.005;
$admin_fee = $annual_earnings * 0.015;
$total_deductions = $platform_fee + $admin_fee;
$net_earnings = $annual_earnings - $total_deductions;

echo "<p><strong>Platform Fee (0.5%):</strong> ₦" . number_format($platform_fee, 2) . "</p>";
echo "<p><strong>Admin Fee (1.5%):</strong> ₦" . number_format($admin_fee, 2) . "</p>";
echo "<p><strong>Total Deductions:</strong> ₦" . number_format($total_deductions, 2) . "</p>";
echo "<p><strong>Net Earnings:</strong> ₦" . number_format($net_earnings, 2) . "</p>";

// Check withdrawals
$sql_withdrawn = "SELECT COALESCE(SUM(amount), 0) as total FROM withdrawals WHERE user_id = ? AND status != 'Rejected'";
$stmt_withdrawn = $conn->prepare($sql_withdrawn);
$stmt_withdrawn->bind_param('i', $user_id);
$stmt_withdrawn->execute();
$result_withdrawn = $stmt_withdrawn->get_result();
$total_withdrawn = $result_withdrawn->fetch_assoc()['total'];

echo "<p><strong>Total Withdrawn:</strong> ₦" . number_format($total_withdrawn, 2) . "</p>";

$wallet_balance = $net_earnings - $total_withdrawn;
echo "<h3 style='color: green;'><strong>Final Wallet Balance:</strong> ₦" . number_format($wallet_balance, 2) . "</h3>";

// Check if there are any paid orders at all
$sql_all_paid = "SELECT COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total FROM orders WHERE user_id = ? AND payment_status = 'Paid'";
$stmt_all = $conn->prepare($sql_all_paid);
$stmt_all->bind_param('i', $user_id);
$stmt_all->execute();
$result_all = $stmt_all->get_result();
$all_paid = $result_all->fetch_assoc();

echo "<hr>";
echo "<h3>All Paid Orders (Any Year):</h3>";
echo "<p><strong>Count:</strong> " . $all_paid['count'] . "</p>";
echo "<p><strong>Total Amount:</strong> ₦" . number_format($all_paid['total'], 2) . "</p>";

?>
