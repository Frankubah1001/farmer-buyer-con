<?php
include 'session.php';

if (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])) {
    echo json_encode([
        'first_name' => $_SESSION['first_name'],
        'last_name' => $_SESSION['last_name']
    ]);
} else {
    echo json_encode(['error' => 'User not logged in']);
}
?>
