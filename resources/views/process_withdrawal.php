<?php
session_start(); // Start session FIRST

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../DBcon.php';

header('Content-Type: application/json');

// Get user ID from session
$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

// Validate POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Get form data
$amount = floatval($_POST['amount'] ?? 0);
$bank_name = trim($_POST['bank_name'] ?? '');
$account_number = trim($_POST['account_number'] ?? '');
$account_name = trim($_POST['account_name'] ?? '');

// Validate inputs
if ($amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid withdrawal amount.']);
    exit;
}

if (empty($bank_name) || empty($account_number) || empty($account_name)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if (!preg_match('/^\d{10}$/', $account_number)) {
    echo json_encode(['success' => false, 'message' => 'Account number must be 10 digits.']);
    exit;
}

// Calculate current wallet balance
$sql_total_sales = "SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE user_id = ? AND payment_status = 'Paid'";
$stmt_total_sales = $conn->prepare($sql_total_sales);
$stmt_total_sales->bind_param('i', $user_id);
$stmt_total_sales->execute();
$result_total_sales = $stmt_total_sales->get_result();
$total_sales = $result_total_sales->fetch_assoc()['total'];
$stmt_total_sales->close();

// Calculate net earnings (deduct 2% fees)
$net_earnings = $total_sales * (1 - 0.02);

// Get total withdrawn
$sql_withdrawn = "SELECT COALESCE(SUM(amount), 0) as total FROM withdrawals WHERE user_id = ? AND status != 'Rejected'";
$stmt_withdrawn = $conn->prepare($sql_withdrawn);
$stmt_withdrawn->bind_param('i', $user_id);
$stmt_withdrawn->execute();
$result_withdrawn = $stmt_withdrawn->get_result();
$total_withdrawn = $result_withdrawn->fetch_assoc()['total'];
$stmt_withdrawn->close();

$wallet_balance = $net_earnings - $total_withdrawn;

// Check if sufficient balance
if ($amount > $wallet_balance) {
    echo json_encode(['success' => false, 'message' => 'Insufficient wallet balance. Available: ₦' . number_format($wallet_balance, 2)]);
    exit;
}

// Insert withdrawal request
$sql_insert = "INSERT INTO withdrawals (user_id, amount, bank_name, account_number, account_name, status) VALUES (?, ?, ?, ?, ?, 'Pending')";
$stmt_insert = $conn->prepare($sql_insert);
$stmt_insert->bind_param('idsss', $user_id, $amount, $bank_name, $account_number, $account_name);

if ($stmt_insert->execute()) {
    echo json_encode(['success' => true, 'message' => 'Withdrawal request submitted successfully. It will be processed within 24-48 hours.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit withdrawal request. Please try again.']);
}

$stmt_insert->close();
$conn->close();
?>
