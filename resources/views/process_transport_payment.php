<?php
// process_transport_payment.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once '../DBcon.php';
require_once __DIR__ . '/../../load_env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../phpmailer/vendor/autoload.php';

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['buyer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (!isset($_POST['order_id'], $_POST['transporter_id'], $_POST['reference'], $_POST['amount'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit;
}

$order_id       = (int)$_POST['order_id'];
$transporter_id = (int)$_POST['transporter_id'];
$reference      = trim($_POST['reference']);
$amount_paid    = (float)$_POST['amount'];
$buyer_id       = (int)$_SESSION['buyer_id'];

if ($order_id <= 0 || $transporter_id <= 0 || $amount_paid <= 0 || empty($reference)) {
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

// Verify amount
$paid_kobo = $paystack['data']['amount'];
$expected_kobo = round($amount_paid * 100);
if ($paid_kobo < ($expected_kobo - 500)) {
    echo json_encode(['success' => false, 'message' => 'Amount mismatch']);
    exit;
}

$conn->autocommit(false);

try {
    // Create transport booking
    $stmt = $conn->prepare("
        INSERT INTO transport_bookings 
        (order_id, transporter_id, buyer_id, booking_date, payment_status, payment_method, 
         payment_reference, payment_amount, booking_status)
        VALUES (?, ?, ?, NOW(), 'Paid', 'Online', ?, ?, 'Confirmed')
    ");
    $stmt->bind_param("iiisd", $order_id, $transporter_id, $buyer_id, $reference, $amount_paid);
    $stmt->execute();
    
    if ($stmt->affected_rows === 0) {
        throw new Exception("Failed to create booking");
    }
    $booking_id = $conn->insert_id;
    $stmt->close();

    // Get booking details for email
    $stmt = $conn->prepare("
        SELECT tb.*, 
               b.email AS buyer_email, b.firstname AS buyer_firstname, b.lastname AS buyer_lastname,
               t.company_name, t.contact_person, t.email AS transporter_email, t.phone AS transporter_phone,
               o.delivery_address, o.delivery_date
        FROM transport_bookings tb
        JOIN buyers b ON tb.buyer_id = b.buyer_id
        JOIN transporters t ON tb.transporter_id = t.transporter_id
        JOIN orders o ON tb.order_id = o.order_id
        WHERE tb.booking_id = ?
    ");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$booking) {
        throw new Exception("Booking details not found");
    }

    // Send emails
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
    $mail->addAddress($booking['buyer_email']);
    $mail->Subject = "Transport Booking Confirmed – Booking #{$booking_id}";
    $mail->Body    = "
        <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='background-color: #2E8B57; padding: 20px; text-align: center; color: white;'>
                <h2 style='margin: 0;'>🎉 New Transport Booking! 🎉</h2>
            </div>
            <div style='padding: 20px; border: 1px solid #eee; border-top: none;'>
                <p>Hello <strong style='color: #2E8B57;'>{$booking['contact_person']}</strong>,</p>
                <p>You have received a new transport booking through FarmConnect. Please find the details below:</p>
                <table style='width: 100%; border-collapse: collapse; margin-top: 20px;'>
                    <tr>
                        <td style='padding: 10px; background-color: #f9f9f9; width: 30%; font-weight: bold; color: #555;'>Booking ID:</td>
                        <td style='padding: 10px; background-color: #f9f9f9; color: #333;'>#<strong style='color: #2E8B57;'>{$booking_id}</strong></td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; width: 30%; font-weight: bold; color: #555;'>Buyer Name:</td>
                        <td style='padding: 10px; color: #333;'>{$booking['buyer_firstname']} {$booking['buyer_lastname']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; background-color: #f9f9f9; width: 30%; font-weight: bold; color: #555;'>Delivery Address:</td>
                        <td style='padding: 10px; background-color: #f9f9f9; color: #333;'>{$booking['delivery_address']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; width: 30%; font-weight: bold; color: #555;'>Expected Delivery Date:</td>
                        <td style='padding: 10px; color: #333;'><strong style='color: #FF5733;'>{$booking['delivery_date']}</strong></td>
                    </tr>
                </table>
                <p style='margin-top: 30px;'>Please log in to your FarmConnect account to view full booking details and confirm your availability.</p>
                <p>Thank you for your service!</p>
            </div>
            <div style='background-color: #f0f0f0; padding: 15px; text-align: center; font-size: 12px; color: #777;'>
                <p>&copy; " . date('Y') . " FarmConnect. All rights reserved.</p>
            </div>
        </div>
    ";
    $mail->send();
    $mail->clearAddresses();

    // Email to Transporter
    if (!empty($booking['transporter_email'])) {
        $mail->addAddress($booking['transporter_email']);
        $mail->Subject = "New Booking Received – Booking #{$booking_id}";
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; font-size: 14px; color: #333; line-height: 1.6;'>
                <div style='background-color: #f8f8f8; padding: 20px; border-bottom: 1px solid #eee;'>
                    <h2 style='color: #2E8B57; margin: 0; font-size: 22px;'>🚚 New Transport Booking!</h2>
                </div>
                <div style='padding: 20px;'>
                    <p style='font-size: 16px;'>Hello <strong style='color: #000;'>{$booking['contact_person']}</strong>,</p>
                    <p>You have received a new paid transport booking worth <strong style='color: #FF5733; font-size: 18px;'>💰 ₦" . number_format($amount_paid, 0) . "</strong>.</p>
                    <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                    <p style='margin-bottom: 10px;'><strong style='color: #555;'>👤 Buyer:</strong> <span style='color: #000;'>{$booking['buyer_firstname']} {$booking['buyer_lastname']}</span></p>
                    <p style='margin-bottom: 10px;'><strong style='color: #555;'>📍 Delivery Address:</strong> <span style='color: #000;'>{$booking['delivery_address']}</span></p>
                    <p style='margin-bottom: 20px;'><strong style='color: #555;'>📅 Expected Delivery:</strong> <span style='color: #000;'>{$booking['delivery_date']}</span></p>
                    <p style='font-size: 16px; color: #2E8B57; font-weight: bold;'>📦 Please prepare for the delivery.</p>
                    <p style='margin-top: 30px;'>Thank you for your service!</p>
                </div>
                <div style='background-color: #f0f0f0; padding: 15px; text-align: center; font-size: 12px; color: #777;'>
                    <p>&copy; " . date('Y') . " FarmConnect. All rights reserved.</p>
                </div>
            </div>
        ";
        $mail->send();
    }

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Transport booking successful!',
        'booking_id' => $booking_id
    ]);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Transport payment error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>
