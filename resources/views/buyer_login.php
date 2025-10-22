<?php
session_start();
include 'DBcon.php';
require_once __DIR__ . '/../../load_env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';
require __DIR__ . '/../../phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require __DIR__ . '/../../phpmailer/vendor/phpmailer/phpmailer/src/SMTP.php';

// Handle resend activation email request
if (isset($_POST['resend_activation'])) {
    $email = trim($_POST['email']);
    
    $stmt = $conn->prepare("SELECT buyer_id, firstname FROM buyers WHERE email = ? AND is_verify = 0");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user) {
        // Generate new token
        $newToken = bin2hex(random_bytes(32));
        
        // Update token in database
        $updateStmt = $conn->prepare("UPDATE buyers SET verification_token = ? WHERE buyer_id = ?");
        $updateStmt->bind_param("si", $newToken, $user['buyer_id']);
        $updateStmt->execute();
        
        // Send new activation email
        try {
            $activationLink = "http://".$_SERVER['HTTP_HOST']."/farmerBuyerCon/resources/buyer_activate.php?token=$newToken";
            
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USER'];
            $mail->Password = $_ENV['SMTP_PASS'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom($_ENV['SMTP_USER'], 'FarmerBuyerCon');
            $mail->addAddress($email, $user['firstname']);

            $mail->isHTML(true);
            $mail->Subject = 'New Activation Link - FarmerBuyerCon';
            $mail->Body = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .button {
                            display: inline-block;
                            padding: 10px 20px;
                            background-color: #4CAF50;
                            color: white !important;
                            text-decoration: none;
                            border-radius: 5px;
                            margin: 15px 0;
                        }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <h2>Account Activation</h2>
                        <p>Hello {$user['firstname']},</p>
                        <p>You requested a new activation link for your FarmerBuyerCon account.</p>
                        <p>Please click the button below to activate your account:</p>
                        <p>
                            <a href='$activationLink' class='button'>Activate Account</a>
                        </p>
                        <p>If you didn't request this, please ignore this email.</p>
                        <p>Thank you,<br>FarmerBuyerCon Team</p>
                    </div>
                </body>
                </html>
            ";
            
            if ($mail->send()) {
                echo json_encode(['status' => 'success', 'message' => 'Activation email sent! Check your inbox.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to send activation email.']);
            }
        } catch (Exception $e) {
            error_log("Resend Email Error: {$mail->ErrorInfo}");
            echo json_encode(['status' => 'error', 'message' => 'Failed to send activation email. Please try again later.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No unverified account found with this email.']);
    }
    exit;
}

// Handle regular login request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['resend_activation'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['rememberMe']) && $_POST['rememberMe'] == 1;

    $stmt = $conn->prepare("SELECT * FROM buyers WHERE email = ?");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database error.']);
        exit;
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Check if account is verified
        if ($user['is_verify'] != 1) {
            echo json_encode([
                'status' => 'unverified',
                'message' => 'Account not activated. Please check your email for the activation link.',
                'email' => $email
            ]);
            exit;
        }

        if (password_verify($password, $user['password'])) {
            $_SESSION['buyer_id'] = $user['buyer_id'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['LAST_ACTIVITY'] = time(); // ✅ Track session activity time

            
            // Check if user has completed additional info
            $hasCompletedInfo = isset($user['info_completed']) && $user['info_completed'] == 1;
            
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $expiry = time() + (86400 * 30); // 30 days
                $expiry_date = date('Y-m-d H:i:s', $expiry);
                $stmt = $conn->prepare("UPDATE buyers SET remember_token = ?, token_expiry = ? WHERE buyer_id = ?");
                $stmt->bind_param("ssi", $token, $expiry_date, $user['buyer_id']);
                $stmt->execute();
                setcookie("remember_token", $token, $expiry, "/");
            }

            // Send login confirmation email
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = $_ENV['SMTP_USER'];
                $mail->Password = $_ENV['SMTP_PASS'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom($_ENV['SMTP_USER'], 'FarmerBuyerCon');
                $mail->addAddress($user['email'], $user['firstname']);

                $mail->isHTML(true);
                $mail->Subject = 'Login Confirmation - FarmerBuyerCon';
                $mail->Body = "
                    <html>
                    <body>
                        <h2>Login Notification</h2>
                        <p>Hi {$user['firstname']},</p>
                        <p>You have successfully logged in to your FarmerBuyerCon account.</p>
                        <p>If this wasn't you, please secure your account by changing your password immediately.</p>
                        <p>Thank you,<br>FarmerBuyerCon Team</p>
                    </body>
                    </html>
                ";

                $mail->send();
            } catch (Exception $e) {
                error_log("Login Notification Email Error: {$mail->ErrorInfo}");
            }

            echo json_encode([
                'status' => 'success', 
                'message' => 'Login successful!',
                'hasCompletedInfo' => $hasCompletedInfo
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']);
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>