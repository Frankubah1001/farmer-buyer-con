<?php
// payment_process.php - FINAL VERSION FOR YOUR DATABASE STRUCTURE
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
session_start();

include 'DBcon.php';
require_once __DIR__ . '/../load_env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../phpmailer/vendor/autoload.php';

if (!isset($_SESSION['buyer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (!isset($_POST['order_id']) || !isset($_POST['reference']) || !isset($_POST['amount'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$order_id     = (int)$_POST['order_id'];
$reference    = trim($_POST['reference']);
$amount_paid  = (float)$_POST['amount'];
$buyer_id     = (int)$_SESSION['buyer_id'];

if ($order_id <= 0 || $amount_paid <= 0 || empty($reference)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

// Verify with Paystack
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer sk_test_0f8b9fa3f9c0b2825cf5148bba5e4426f2ec0d2f",
        "Cache-Control: no-cache"
    ],
]);
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err || !$response) {
    echo json_encode(['success' => false, 'message' => 'Paystack connection failed']);
    exit;
}

$paystack = json_decode($response, true);
if (!$paystack['status'] || $paystack['data']['status'] !== 'success') {
    echo json_encode(['success' => false, 'message' => 'Payment not successful']);
    exit;
}

// Allow small difference (e.g. ₦5)
$paid_kobo = $paystack['data']['amount'];
$expected_kobo = round($amount_paid * 100);
if ($paid_kobo < ($expected_kobo - 500)) {
    echo json_encode(['success' => false, 'message' => 'Amount mismatch']);
    exit;
}

$conn->autocommit(false);

try {
    // Update main order
    $stmt = $conn->prepare("
        UPDATE orders 
        SET payment_status = 'Paid',
            order_status = 'Processing Produce For Delivery',
            payment_date = NOW(),
            paystack_reference = ?,
            payment_amount = ?
        WHERE order_id = ? AND buyer_id = ? AND payment_status != 'Paid'
    ");
    $stmt->bind_param("sdii", $reference, $amount_paid, $order_id, $buyer_id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        throw new Exception("Order not found or already paid");
    }
    $stmt->close();

    // Get order + buyer + farmer + produce name
    $stmt = $conn->prepare("
        SELECT o.*,
               b.email AS buyer_email, b.firstname AS buyer_firstname, b.lastname AS buyer_lastname,
               u.email AS farmer_email, u.first_name AS farmer_firstname, u.last_name AS farmer_lastname,
               pl.produce AS produce_name
        FROM orders o
        JOIN buyers b ON o.buyer_id = b.buyer_id
        LEFT JOIN users u ON o.user_id = u.user_id
        LEFT JOIN produce_listings pl ON o.produce_id = pl.prod_id
        WHERE o.order_id = ?
    ");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();

    if (!$order) {
        throw new Exception("Order details not found");
    }

    // Build item description (since it's single row)
    $item_desc = $order['produce_name'] . " - " . (int)$order['quantity'] . " Unit(s) @ ₦" . number_format($order['price_per_unit'], 0) . " each";

    // === SEND EMAILS ===
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['SMTP_USER'];
    $mail->Password   = $_ENV['SMTP_PASS'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->setFrom($_ENV['SMTP_USER'], 'FarmConnect');
    $mail->addReplyTo('no-reply@farmconnect.com', 'FarmConnect');
    $mail->isHTML(true);

    // Email to Buyer
    $mail->addAddress($order['buyer_email']);
    $mail->Subject = "Payment Confirmed – Order #ORD-{$order_id}";
    $mail->Body    = "
        <h2 style='color:#2E8B57'>Payment Successful!</h2>
        <p>Dear {$order['buyer_firstname']},</p>
        <p>Your payment of <strong>₦" . number_format($amount_paid, 0) . "</strong> has been received.</p>
        <p><strong>Order #ORD-{$order_id}</strong> is now being processed.</p>
        <hr>
        <p><strong>Item:</strong> {$item_desc}</p>
        <p><strong>Delivery:</strong> {$order['delivery_address']} on {$order['delivery_date']}</p>
        <p>Thank you!</p>
        <p><em>FarmConnect Team</em></p>
    ";
    $mail->send();
    $mail->clearAddresses();

    // Email to Farmer
    if (!empty($order['farmer_email'])) {
        $mail->addAddress($order['farmer_email']);
        $mail->Subject = "New Paid Order #ORD-{$order_id}";
        $mail->Body    = "
            <h2 style='color:#2E8B57'>Payment Received!</h2>
            <p>Hello {$order['farmer_firstname']},</p>
            <p>Buyer <strong>{$order['buyer_firstname']} {$order['buyer_lastname']}</strong> just paid <strong>₦" . number_format($amount_paid, 0) . "</strong>.</p>
            <hr>
            <p><strong>Item:</strong> {$item_desc}</p>
            <p><strong>Delivery Address:</strong> {$order['delivery_address']}</p>
            <p><strong>Date:</strong> {$order['delivery_date']}</p>
            <p>Please start processing immediately.</p>
            <p>Thank you!</p>
        ";
        $mail->send();
    }

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Payment successful and confirmed!'
    ]);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Payment error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>