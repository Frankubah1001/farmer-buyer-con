<?php
header('Content-Type: application/json');
include 'DBcon.php'; // Adjust the path to your database connection file

$sql = "SELECT DISTINCT order_status FROM orders";
$result = $conn->query($sql);

$statuses = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $statuses[] = $row['order_status'];
    }
}

$conn->close();

echo json_encode(['statuses' => $statuses]);
?>