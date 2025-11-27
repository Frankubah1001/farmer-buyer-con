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

// Get withdrawal ID from query string
$withdrawal_id = intval($_GET['id'] ?? 0);

if ($withdrawal_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid withdrawal ID.']);
    exit;
}

// Fetch withdrawal details (only for the logged-in user)
$sql = "SELECT withdrawal_id, amount, bank_name, account_number, account_name, status, DATE_FORMAT(request_date, '%Y/%m/%d %H:%i') as request_date 
        FROM withdrawals 
        WHERE withdrawal_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $withdrawal_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $withdrawal = $result->fetch_assoc();
    echo json_encode(['success' => true, 'withdrawal' => $withdrawal]);
} else {
    echo json_encode(['success' => false, 'message' => 'Withdrawal not found.']);
}

$stmt->close();
$conn->close();
?>
