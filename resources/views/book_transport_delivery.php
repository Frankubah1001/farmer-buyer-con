<?php
// book_transport_delivery.php - Pay on Delivery
session_start();

require_once '../DBcon.php';

header('Content-Type: application/json');

if (!isset($_SESSION['buyer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (!isset($_POST['order_id'], $_POST['transporter_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit;
}

$order_id       = (int)$_POST['order_id'];
$transporter_id = (int)$_POST['transporter_id'];
$buyer_id       = (int)$_SESSION['buyer_id'];

try {
    // Get transport fee
    $stmt = $conn->prepare("SELECT fees FROM transporters WHERE transporter_id = ?");
    $stmt->bind_param("i", $transporter_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $trans = $result->fetch_assoc();
    $stmt->close();
    
    if (!$trans) {
        echo json_encode(['success' => false, 'message' => 'Transporter not found']);
        exit;
    }
    
    $amount = $trans['fees'];
    
    // Create booking
    $stmt = $conn->prepare("
        INSERT INTO transport_bookings 
        (order_id, transporter_id, buyer_id, booking_date, payment_status, payment_method, 
         payment_amount, booking_status)
        VALUES (?, ?, ?, NOW(), 'Pending', 'Delivery', ?, 'Confirmed')
    ");
    $stmt->bind_param("iiid", $order_id, $transporter_id, $buyer_id, $amount);
    $stmt->execute();
    
    if ($stmt->affected_rows === 0) {
        throw new Exception("Failed to create booking");
    }
    
    $booking_id = $conn->insert_id;
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'Transport booked successfully. Pay on delivery.',
        'booking_id' => $booking_id
    ]);

} catch (Exception $e) {
    error_log("Booking error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>
