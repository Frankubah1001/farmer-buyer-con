<?php
// Test script to verify report functionality
require_once '../DBcon.php';

header('Content-Type: application/json');

echo json_encode([
    'database_connected' => $conn->ping(),
    'test_queries' => [
        'farmers_count' => getFarmersCount($conn),
        'sample_farmer' => getSampleFarmer($conn),
        'sample_produce' => getSampleProduce($conn)
    ]
]);

function getFarmersCount($conn) {
    $result = $conn->query("
        SELECT COUNT(*) as count 
        FROM users 
        WHERE cbn_approved = 1 
        AND status = 'active'
        AND user_id IN (SELECT DISTINCT user_id FROM produce_listings)
    ");
    $row = $result->fetch_assoc();
    return $row['count'];
}

function getSampleFarmer($conn) {
    $result = $conn->query("
        SELECT user_id, first_name, last_name, address 
        FROM users 
        WHERE cbn_approved = 1 
        AND status = 'active'
        AND user_id IN (SELECT DISTINCT user_id FROM produce_listings)
        LIMIT 1
    ");
    return $result->fetch_assoc();
}

function getSampleProduce($conn) {
    $result = $conn->query("
        SELECT user_id, produce, quantity, price 
        FROM produce_listings 
        WHERE quantity > 0
        LIMIT 5
    ");
    return $result->fetch_all(MYSQLI_ASSOC);
}
?>
