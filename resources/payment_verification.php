<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'DBcon.php'; 
require_once __DIR__ . '/../load_env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . '/../phpmailer/vendor/autoload.php';

// Verify PayStack payment
if (isset($_GET['reference'])) {
    $reference = $_GET['reference'];
    
    try {
        $paystack_secret_key = "sk_test_0f8b9fa3f9c0b2825cf5148bba5e4426f2ec0d2f";
        $url = "https://api.paystack.co/transaction/verify/" . rawurlencode($reference);
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "authorization: Bearer " . $paystack_secret_key,
                "cache-control: no-cache"
            ],
        ]);
        
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        
        if ($err) {
            throw new Exception("cURL Error: " . $err);
        }
        
        $result = json_decode($response, true);
        
        if (!$result || !isset($result['status']) || $result['data']['status'] !== 'success') {
            throw new Exception("Payment verification failed or not successful from Paystack.");
        }
        
        $metadata = $result['data']['metadata'];
        $order_id = $metadata['order_id'];
        $buyer_id = $metadata['buyer_id'];
        $amount_paid_naira = $result['data']['amount'] / 100; // Convert from kobo
        
        // Update the order in the database
        $conn->begin_transaction();

        try {
            // Check if the order has already been paid
            $check_stmt = $conn->prepare("SELECT payment_status FROM orders WHERE order_id = ? AND buyer_id = ?");
            $check_stmt->bind_param("ii", $order_id, $buyer_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows === 0) {
                throw new Exception("Order not found.");
            }
            
            $current_status = $check_result->fetch_assoc()['payment_status'];
            $check_stmt->close();

            if ($current_status === 'Paid') {
                throw new Exception("This order has already been processed.");
            }
            
            // Update the payment status in the database
            $update_stmt = $conn->prepare("UPDATE orders SET payment_status = 'Paid', payment_date = NOW(), paystack_reference = ?, payment_amount = ? WHERE order_id = ? AND buyer_id = ?");
            $update_stmt->bind_param("sdii", $reference, $amount_paid_naira, $order_id, $buyer_id);
            $update_stmt->execute();
            
            if ($update_stmt->affected_rows === 0) {
                throw new Exception("Order update failed. Check if order_id and buyer_id are correct.");
            }
            $update_stmt->close();
            
            // Get comprehensive order details for email
            $stmt = $conn->prepare("
                SELECT 
                    o.order_id, 
                    o.total_amount, 
                    o.payment_date, 
                    o.delivery_address, 
                    o.delivery_date,
                    o.order_status,
                    f.phone,
                    b.email AS buyer_email, 
                    b.firstname AS buyer_first_name, 
                    b.lastname AS buyer_last_name,
                    f.first_name AS farmer_firstname,
                    f.last_name AS farmer_lastname
                FROM orders o 
                JOIN buyers b ON o.buyer_id = b.buyer_id 
                LEFT JOIN users f ON o.user_id = f.user_id
                WHERE o.order_id = ? AND o.buyer_id = ?
            ");
            $stmt->bind_param("ii", $order_id, $buyer_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $order = $result->fetch_assoc();
            $stmt->close();

            if (!$order) {
                throw new Exception("Could not retrieve order details for email.");
            }
            
            // Fetch order items
            $order_items = [];
            $items_stmt = $conn->prepare("
                SELECT oi.quantity, oi.price_per_unit, pl.produce
                FROM order_items oi
                JOIN produce_listings pl ON oi.produce_id = pl.prod_id
                WHERE oi.order_id = ?
            ");
            $items_stmt->bind_param("i", $order_id);
            $items_stmt->execute();
            $items_result = $items_stmt->get_result();
            while ($item_row = $items_result->fetch_assoc()) {
                $order_items[] = $item_row;
            }
            $items_stmt->close();
            
            // Build email content
            $items_html = '';
            foreach ($order_items as $item) {
                $items_html .= "<li>" . htmlspecialchars($item['produce']) . " - " . htmlspecialchars($item['quantity']) . " Bags @ ₦" . number_format($item['price_per_unit'], 2) . " each</li>";
            }

            $email_content = "
                <html>
                <head>
                    <title>Payment Confirmation</title>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { width: 80%; margin: 20px auto; border: 1px solid #ddd; padding: 20px; border-radius: 8px; }
                        h2 { color: #28a745; }
                        ul { list-style: none; padding: 0; }
                        li { margin-bottom: 5px; }
                        .footer { margin-top: 20px; font-size: 0.9em; color: #666; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <h2>Payment Confirmation</h2>
                        <p>Dear " . htmlspecialchars($order['buyer_first_name'] . ' ' . $order['buyer_last_name']) . ",</p>
                        <p>Your payment for Order ID: <strong>ORD-" . htmlspecialchars($order['order_id']) . "</strong> has been successfully processed.</p>
                        
                        <h3>Payment Details:</h3>
                        <ul>
                            <li><strong>Amount Paid:</strong> ₦" . number_format($amount_paid_naira, 2) . "</li>
                            <li><strong>Paystack Reference:</strong> " . htmlspecialchars($reference) . "</li>
                            <li><strong>Payment Date:</strong> " . date('Y-m-d H:i:s', strtotime($order['payment_date'])) . "</li>
                        </ul>

                        <h3>Order Summary:</h3>
                        <ul>
                            <li><strong>Total Order Amount:</strong> ₦" . number_format($order['total_amount'], 2) . "</li>
                            <li><strong>Order Status:</strong> " . htmlspecialchars($order['order_status']) . "</li>
                            <li><strong>Farmer:</strong> " . htmlspecialchars($order['farmer_firstname'] . ' ' . $order['farmer_lastname'] ?? 'N/A') . "</li>
                            <li><strong>Farmer Contact:</strong> " . htmlspecialchars($order['phone']) . "</li>
                            <li><strong>Delivery Address:</strong> " . htmlspecialchars($order['delivery_address']) . "</li>
                            <li><strong>Delivery Date:</strong> " . htmlspecialchars($order['delivery_date'] ?? 'N/A') . "</li>
                        </ul>

                        <h3>Items Ordered:</h3>
                        <ul>
                            " . $items_html . "
                        </ul>

                        <p>Your order is now being processed and you will receive further updates soon.</p>
                        <p>Thank you for your business!</p>
                        <div class='footer'>
                            <p>Best regards,<br>Your FarmConnect Team</p>
                            <p><a href='http://yourwebsite.com'>FarmConnect Website</a></p>
                        </div>
                    </div>
                </body>
                </html>
            ";

            // Configure PHPMailer
            $mail = new PHPMailer(true);
            
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = $_ENV['SMTP_USER'];
                $mail->Password   = $_ENV['SMTP_PASS'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->SMTPDebug  = 0;

                // Recipients
                $mail->setFrom($_ENV['SMTP_USER'], 'FarmConnect');
                $mail->addAddress($order['buyer_email'], $order['buyer_first_name'] . ' ' . $order['buyer_last_name']);
                
                $mail->addReplyTo('noreply@farmconnect.com', 'No Reply');
                
                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Payment Confirmation for Order ID: ORD-' . $order['order_id'];
                $mail->Body    = $email_content;
                $mail->AltBody = strip_tags(str_replace(['<li>', '</li>'], ['- ', "\n"], $email_content));

                $mail->send();
                
                $conn->commit();
                header("Location: " . $_ENV['APP_URL'] . "/buyer/order_history.php?payment=success");
                exit;
            } catch (Exception $e) {
                error_log("Email sending failed: " . $mail->ErrorInfo);
                $conn->commit();
                header("Location: " . $_ENV['APP_URL'] . "/buyer/order_history.php?payment=success_no_email");
                exit;
            }
        } catch (Exception $e) {
            $conn->rollback();
            header("Location: " . $_ENV['APP_URL'] . "/buyer/order_history.php?payment=error&message=" . urlencode($e->getMessage()));
            exit;
        }
        
    } catch (Exception $e) {
        error_log("Payment verification error: " . $e->getMessage());
        header("Location: " . $_ENV['APP_URL'] . "/buyer/order_history.php?payment=error&message=" . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: " . $_ENV['APP_URL'] . "/buyer/order_history.php");
    exit;
}
?>