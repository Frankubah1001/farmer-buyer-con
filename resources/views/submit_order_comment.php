<?php
header('Content-Type: application/json');
session_start();
include 'DBcon.php';
require_once __DIR__ . '/../../load_env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . '/../../phpmailer/vendor/autoload.php';

if (!isset($_SESSION['buyer_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$order_id = $_POST['order_id'];
$farmer_id = $_POST['farmer_id'];
$message = $_POST['message'];
$buyer_id = $_SESSION['buyer_id'];

try {
    // Get buyer details
    $buyer_stmt = $conn->prepare("SELECT firstname, lastname, email FROM buyers WHERE buyer_id = ?");
    $buyer_stmt->bind_param("i", $buyer_id);
    $buyer_stmt->execute();
    $buyer_result = $buyer_stmt->get_result();
    $buyer_data = $buyer_result->fetch_assoc();
    $buyer_name = $buyer_data['firstname'] . ' ' . $buyer_data['lastname'];
    $buyer_email = $buyer_data['email'];
    $buyer_stmt->close();

    // Get farmer details
    $farmer_stmt = $conn->prepare("SELECT first_name, last_name, email FROM users WHERE user_id = ?");
    $farmer_stmt->bind_param("i", $farmer_id);
    $farmer_stmt->execute();
    $farmer_result = $farmer_stmt->get_result();
    $farmer_data = $farmer_result->fetch_assoc();
    $farmer_name = $farmer_data['first_name'] . ' ' . $farmer_data['last_name'];
    $farmer_email = $farmer_data['email'];
    $farmer_stmt->close();

    // Get order details
    $order_stmt = $conn->prepare("SELECT pl.produce FROM orders o JOIN produce_listings pl ON o.produce_id = pl.prod_id WHERE o.order_id = ?");
    $order_stmt->bind_param("i", $order_id);
    $order_stmt->execute();
    $order_result = $order_stmt->get_result();
    $order_data = $order_result->fetch_assoc();
    $produce_name = $order_data['produce'];
    $order_stmt->close();

    // Insert the message
    $stmt = $conn->prepare("INSERT INTO order_messages (order_id, sender_id, receiver_id, message) 
                           VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $order_id, $buyer_id, $farmer_id, $message);
    
    if ($stmt->execute()) {
        // Initialize PHPMailer
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
            $mail->SMTPDebug  = 0; // Set to 2 for debugging

            // Recipients
            $mail->setFrom($_ENV['SMTP_USER'], 'FarmConnect');
            $mail->addAddress($farmer_email, $farmer_name);
            
            // Reply-to address
            $mail->addReplyTo($buyer_email, $buyer_name);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'New Message About Your Order #ORD-' . $order_id;
            
            // HTML email content
            $email_content = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background-color: #f8f9fa; padding: 15px; text-align: center; }
                        .content { padding: 20px; }
                        .message { background-color: #f1f1f1; padding: 15px; border-radius: 5px; }
                        .footer { margin-top: 20px; font-size: 0.9em; color: #6c757d; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h2>FarmConnect Order Message</h2>
                        </div>
                        <div class='content'>
                            <p>Dear $farmer_name,</p>
                            <p>You have received a new message from $buyer_name regarding the order of <strong>$produce_name</strong> (Order #ORD-$order_id):</p>
                            <div class='message'>
                                <p>$message</p>
                            </div>
                            <p>You can reply to this message through the FarmConnect platform.</p>
                        </div>
                        <div class='footer'>
                            <p>Best regards,<br>The FarmConnect Team</p>
                        </div>
                    </div>
                </body>
                </html>
            ";
            
            // Plain text version
            $plain_text = "Dear $farmer_name,\n\n";
            $plain_text .= "You have received a new message from $buyer_name regarding your order of $produce_name (Order #ORD-$order_id):\n\n";
            $plain_text .= "Message:\n$message\n\n";
            $plain_text .= "You can reply to this message through the FarmConnect platform.\n\n";
            $plain_text .= "Best regards,\nThe FarmConnect Team";
            
            $mail->Body = $email_content;
            $mail->AltBody = $plain_text;

            $mail->send();
            
            echo json_encode([
                'status' => 'success', 
                'message' => 'Message sent successfully and farmer notified via email'
            ]);
        } catch (Exception $e) {
            // Email failed but message was saved to database
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            echo json_encode([
                'status' => 'success', 
                'message' => 'Message sent successfully but email notification failed'
            ]);
        }
    } else {
        throw new Exception("Failed to send message");
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn->close();
?>