<?php
session_start();
require_once __DIR__ . '/../../load_env.php';  // For SMTP credentials

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';
require __DIR__ . '/../../phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require __DIR__ . '/../../phpmailer/vendor/phpmailer/phpmailer/src/SMTP.php';

include 'DBcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ---------- RESEND ACTIVATION LINK FOR FARMER ----------
    if (isset($_POST['resend_activation'])) {
        $email = trim($_POST['email']);

        $stmt = $conn->prepare("SELECT user_id, first_name FROM users WHERE email = ? AND is_verified = 0");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user) {
            // Generate new token
            $newToken = bin2hex(random_bytes(32));

            // Update token in database
            $upd = $conn->prepare("UPDATE users SET verification_token = ? WHERE user_id = ?");
            $upd->bind_param("si", $newToken, $user['user_id']);
            $upd->execute();
            $upd->close();

            // Activation link (adjust path if your farmer activation file has a different name/location)
            $activationLink = "http://" . $_SERVER['HTTP_HOST'] . "/farmerBuyerCon/resources/activate.php?token=" . $newToken;

            // Send email using PHPMailer
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = $_ENV['SMTP_USER'];
                $mail->Password   = $_ENV['SMTP_PASS'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom($_ENV['SMTP_USER'], 'FarmerBuyerCon');
                $mail->addAddress($email, $user['first_name']);
                $mail->isHTML(true);
                $mail->Subject = 'New Activation Link - FarmerBuyerCon';

                $mail->Body = "
                <html>
                <body style='font-family: Arial, Helvetica, sans-serif; line-height: 1.6; color: #333;'>
                    <div style='max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;'>
                        <h2 style='color: #4CAF50;'>Account Activation Required</h2>
                        <p>Hello <strong>{$user['first_name']}</strong>,</p>
                        <p>You requested a new activation link for your FarmerBuyerCon farmer account.</p>
                        <p>Click the button below to activate your account:</p>
                        <p style='text-align: center; margin: 30px 0;'>
                            <a href='$activationLink' 
                               style='background:#4CAF50; color:#ffffff; padding:12px 30px; text-decoration:none; border-radius:5px; font-weight:bold; display:inline-block;'>
                               Activate My Account
                            </a>
                        </p>
                        <p><small>If you didn't request this, please ignore this email.</small></p>
                        <hr style='border: 0; border-top: 1px solid #eee; margin: 30px 0;'>
                        <p style='font-size:12px; color:#999;'>Thank you,<br><strong>FarmerBuyerCon Team</strong></p>
                    </div>
                </body>
                </html>";

                $mail->send();
                echo json_encode([
                    'status' => 'success',
                    'message' => 'New activation link sent! Please check your inbox (and spam folder).'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to send email. Mailer Error: ' . $e->getMessage()
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'No unverified farmer account found with this email.'
            ]);
        }

        $conn->close();
        exit;
    }

    // ================================
    // Regular Farmer Login (unchanged)
    // ================================

    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['rememberMe']) && $_POST['rememberMe'] == 1;

    $stmt = $conn->prepare("SELECT user_id, first_name, last_name, email, password, is_verified, info_completed, cbn_approved, rejection_reason FROM users WHERE email = ?");
    
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database error preparing statement.']);
        exit;
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        if ($user['is_verified'] != 1) {
            echo json_encode([
                'status' => 'unverified',
                'message' => 'Account not activated. Please check your email for the activation link.',
                'email' => $email
            ]);
            exit;
        }

        if ($user['cbn_approved'] == 2) {
            echo json_encode([
                'status' => 'rejected',
                'message' => 'Your account has been disabled by Admin. Reason: ' . ($user['rejection_reason'] ? htmlspecialchars($user['rejection_reason']) : 'No reason provided.')
            ]);
            exit;
        }

        if ($user['cbn_approved'] == 0) {
            echo json_encode([
                'status' => 'pending',
                'message' => 'Your account is pending approval. Please wait for admin approval.'
            ]);
            exit;
        }

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['info_completed'] = $user['info_completed'];
            $_SESSION['cbn_approved'] = $user['cbn_approved'];
            $_SESSION['LAST_ACTIVITY'] = time();

            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $expiry = time() + (86400 * 30);
                $expiry_date = date('Y-m-d H:i:s', $expiry);

                $updateTokenStmt = $conn->prepare("UPDATE users SET remember_token = ?, token_expiry = ? WHERE user_id = ?");
                $updateTokenStmt->bind_param("ssi", $token, $expiry_date, $user['user_id']);
                $updateTokenStmt->execute();
                $updateTokenStmt->close();

                setcookie("remember_token", $token, $expiry, "/");
            }

            echo json_encode([
                'status' => 'success',
                'redirect' => 'farmersDashboard.php',
                'message' => 'Login successful! Redirecting...'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']);
    }

    $conn->close();
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
?>