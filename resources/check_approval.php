<?php
session_start();
require_once 'DBcon.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['approved' => false, 'message' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare("SELECT cbn_approved, rejection_reason FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!$user) {
        throw new Exception("User not found");
    }
    
    if ($user['cbn_approved'] === 1) {
        echo json_encode(['approved' => true]);
    } elseif ($user['cbn_approved'] === 2) {
        echo json_encode([
            'approved' => false,
            'rejected' => true,
            'message' => 'Your registration has been rejected. Reason: ' . ($user['rejection_reason'] ?? 'Not specified')
        ]);
    } else {
        echo json_encode([
            'approved' => false,
            'message' => 'Your registration is still under review. Please wait for approval.'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'approved' => false,
        'message' => 'Error checking approval status: ' . $e->getMessage()
    ]);
}
?>