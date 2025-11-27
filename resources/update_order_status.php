<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// update_order_status.php
include 'DBcon.php';
require_once __DIR__ . '/../load_env.php'; // Fixed path

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$new_status = isset($_POST['status']) ? mysqli_real_escape_string($conn, $_POST['status']) : '';

if ($order_id <= 0 || empty($new_status)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid order ID or status']);
    exit();
}

// Verify Ownership and Get Order Details with Buyer Email
$check_ownership_sql = "SELECT 
                        pl.user_id as farmer_id,
                        o.buyer_id,
                        b.email as buyer_email,
                        b.firstname as buyer_name,
                        f.first_name as farmer_name,
                        f.crops_produced as produce_name,
                        o.quantity,
                        o.total_amount
                        FROM orders o
                        JOIN produce_listings pl ON o.produce_id = pl.prod_id
                        JOIN buyers b ON o.buyer_id = b.buyer_id
                        JOIN users f ON pl.user_id = f.user_id
                        WHERE o.order_id = $order_id";
$check_ownership_result = mysqli_query($conn, $check_ownership_sql);

if (!$check_ownership_result || mysqli_num_rows($check_ownership_result) == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Order not found']);
    exit();
}

$order_data = mysqli_fetch_assoc($check_ownership_result);
if ($order_data['farmer_id'] != $user_id) {
    echo json_encode(['status' => 'error', 'message' => 'You do not have permission to update this order']);
    exit();
}

// Update order status
$sql = "UPDATE orders SET order_status = '$new_status' WHERE order_id = $order_id";

if (mysqli_query($conn, $sql)) {
    // Update delivery_status in produce_listings
    $update_produce_sql = "UPDATE produce_listings pl
                           JOIN orders o ON pl.prod_id = o.produce_id
                           SET pl.order_status = '$new_status'
                           WHERE o.order_id = $order_id";
    mysqli_query($conn, $update_produce_sql);

    // Send email notification to buyer
    $emailSent = sendStatusUpdateEmail(
        $order_data['buyer_email'],
        $order_data['buyer_name'],
        $order_data['farmer_name'],
        $order_data['produce_name'],
        $new_status,
        $order_id,
        $order_data['quantity'],
        $order_data['total_amount']
    );

    $response = ['status' => 'success', 'message' => 'Order status updated successfully'];
    if (!$emailSent) {
        $response['email_warning'] = 'Status updated but email notification failed to send';
    }
    
    echo json_encode($response);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update order status: ' . mysqli_error($conn)]);
}

mysqli_close($conn);

/**
 * Send email notification to buyer about status update
 */
function sendStatusUpdateEmail($buyerEmail, $buyerName, $farmerName, $produce, $newStatus, $orderId, $quantity, $totalPrice) {
    // Check if SMTP credentials are available
    if (!isset($_ENV['SMTP_USER']) || !isset($_ENV['SMTP_PASS']) || empty($_ENV['SMTP_USER']) || empty($_ENV['SMTP_PASS'])) {
        error_log('SMTP credentials not found in environment variables');
        return false;
    }

    $mail = new PHPMailer(true);
    
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USER'];
        $mail->Password   = $_ENV['SMTP_PASS'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->SMTPDebug = 0;

        // Email content based on status
        $statusMessages = [
            'Make Payment' => [
                'subject' => 'Payment Request for Your Order - FarmerBuyerCon',
                'message' => 'Please proceed with the payment for your order. Your farmer is waiting to process your order upon payment confirmation.'
            ],
            'Processing Produce For Delivery' => [
                'subject' => 'Your Order is Being Processed - FarmerBuyerCon',
                'message' => 'Great news! Your farmer has started preparing your produce for delivery. We\'ll notify you once it\'s ready for shipment.'
            ],
            'Produce Transported' => [
                'subject' => 'Your Order is On the Way - FarmerBuyerCon',
                'message' => 'Your produce is now being transported. Track your delivery for real-time updates.'
            ],
            'Produce Delivered & Confirmed' => [
                'subject' => 'Order Delivered Successfully - FarmerBuyerCon',
                'message' => 'Your order has been delivered and confirmed. Thank you for choosing FarmerBuyerCon!'
            ],
            'Cancelled' => [
                'subject' => 'Order Cancellation Notice - FarmerBuyerCon',
                'message' => 'Your order has been cancelled. Please contact support if you have any questions.'
            ]
        ];

        $statusInfo = $statusMessages[$newStatus] ?? [
            'subject' => 'Order Status Updated - FarmerBuyerCon',
            'message' => 'The status of your order has been updated.'
        ];

        // Email content
        $mail->setFrom($_ENV['SMTP_USER'], 'FarmerBuyerCon');
        $mail->addAddress($buyerEmail, $buyerName);
        $mail->isHTML(true);
        $mail->Subject = $statusInfo['subject'];

        $mail->Body = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Order Status Update</title>
            <style>
                body { margin: 0; padding: 0; background: #f4f7f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
                .container { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
                .header { background: linear-gradient(135deg, #2f855a, #38a169); color: white; padding: 30px 20px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                .content { padding: 35px 40px; color: #333333; line-height: 1.7; }
                .status-badge { background: #e6f7ff; border: 2px solid #1890ff; color: #1890ff; padding: 8px 16px; border-radius: 20px; font-weight: bold; display: inline-block; margin: 10px 0; }
                .order-details { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .footer { background: #f8f9fa; padding: 25px; text-align: center; font-size: 13px; color: #6c757d; border-top: 1px solid #e9ecef; }
                .highlight { color: #2f855a; font-weight: 600; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>FarmerBuyerCon</h1>
                </div>
                
                <div class='content'>
                    <h2>Hello " . htmlspecialchars($buyerName) . ",</h2>
                    
                    <p>Your order status has been updated by the farmer.</p>
                    
                    <div class='status-badge'>New Status: " . htmlspecialchars($newStatus) . "</div>
                    
                    <p>" . $statusInfo['message'] . "</p>
                    
                    <div class='order-details'>
                        <h3>Order Details:</h3>
                        <p><strong>Order ID:</strong> #" . htmlspecialchars($orderId) . "</p>
                        <p><strong>Produce:</strong> " . htmlspecialchars($produce) . "</p>
                        <p><strong>Quantity:</strong> " . htmlspecialchars($quantity) . "</p>
                        <p><strong>Total Price:</strong> NGN" . htmlspecialchars(number_format($totalPrice, 2)) . "</p>
                        <p><strong>Farmer:</strong> " . htmlspecialchars($farmerName) . "</p>
                    </div>
                    
                    <p>If you have any questions about this status update, please don't hesitate to contact us.</p>
                    
                    <p>Best regards,<br>The FarmerBuyerCon Team</p>
                </div>
                
                <div class='footer'>
                    <p><strong>FarmerBuyerCon</strong> – Fresh from Farm to You</p>
                    <p>Need help? <a href='mailto:support@farmerbuyercon.com'>Contact Support</a></p>
                    <p>&copy; " . date('Y') . " FarmerBuyerCon. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";

        // Alternative text for non-HTML clients
        $mail->AltBody = "Hello $buyerName,\n\nYour order status has been updated to: $newStatus\n\nOrder ID: #$orderId\nProduce: $produce\nQuantity: $quantity\nTotal Price: NGN" . number_format($totalPrice, 2) . "\nFarmer: $farmerName\n\n$statusInfo[message]\n\nBest regards,\nThe FarmerBuyerCon Team";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Status Update Email Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>