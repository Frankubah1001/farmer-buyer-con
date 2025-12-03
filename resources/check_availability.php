<?php
include 'DBcon.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

$field = $_POST['field'] ?? '';
$value = trim($_POST['value'] ?? '');

// Whitelist allowed fields to prevent SQL injection via column name
$allowed_fields = ['email', 'phone', 'nin', 'cac_number'];

if (!in_array($field, $allowed_fields)) {
    echo json_encode(['error' => 'Invalid field']);
    exit;
}

if (empty($value)) {
    echo json_encode(['exists' => false]); // Empty value doesn't exist (or we don't check it here)
    exit;
}

// Prepare statement
$sql = "SELECT user_id FROM users WHERE $field = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['error' => 'Database error']);
    exit;
}

$stmt->bind_param("s", $value);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['exists' => true]);
} else {
    echo json_encode(['exists' => false]);
}

$stmt->close();
$conn->close();
?>
