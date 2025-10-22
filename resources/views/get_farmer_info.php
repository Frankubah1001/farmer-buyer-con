<?php
session_start();
include 'DBcon.php';

header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$email = $_SESSION['email'];

$query = "SELECT CONCAT(first_name, ' ', last_name) AS fullname, phone, states.state_name AS state, cities.city_name AS lga
          FROM users
          JOIN states ON users.state_id = states.state_id
          JOIN cities ON users.city_id = cities.city_id
          WHERE email = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($user = mysqli_fetch_assoc($result)) {
    echo json_encode($user);
} else {
    echo json_encode(['error' => 'User not found']);
}

mysqli_close($conn);
?>