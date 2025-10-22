<?php
require_once __DIR__ . '/../../load_env.php';

$host = $_ENV['DB_HOST'] ?? 'localhost';
$dbname = $_ENV['DB_NAME'] ?? 'farmerbuyercon';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    // In a production environment, you should log this error instead of dying
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Start session to store login status
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Function to safely close connection (THIS IS WHAT WAS MISSING IN SCOPE)
function close_db_connection($conn) {
    if ($conn) {
        $conn->close();
    }
}
?>
