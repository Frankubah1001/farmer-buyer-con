<?php
session_start();
include 'DBcon.php';
require_once __DIR__ . '/../../load_env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';
require __DIR__ . '/../../phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require __DIR__ . '/../../phpmailer/vendor/phpmailer/phpmailer/src/SMTP.php';

// ---------- RESEND ACTIVATION ----------
if (isset($_POST['resend_activation'])) {
    $email = trim($_POST['email']);
    $stmt  = $conn->prepare("SELECT buyer_id, firstname FROM buyers WHERE email = ? AND is_verify = 0");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user) {
        $newToken = bin2hex(random_bytes(32));
        $upd = $conn->prepare("UPDATE buyers SET verification_token = ? WHERE buyer_id = ?");
        $upd->bind_param("si", $newToken, $user['buyer_id']);
        $upd->execute();

        $activationLink = "http://".$_SERVER['HTTP_HOST']."/farmerBuyerCon/resources/buyer_activate.php?token=$newToken";

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USER'];
        $mail->Password   = $_ENV['SMTP_PASS'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->setFrom($_ENV['SMTP_USER'], 'FarmerBuyerCon');
        $mail->addAddress($email, $user['firstname']);
        $mail->isHTML(true);
        $mail->Subject = 'New Activation Link - FarmerBuyerCon';
        $mail->Body    = "
            <html><body style='font-family:Arial,Helvetica,sans-serif;'>
            <h2>Account Activation</h2>
            <p>Hello <strong>{$user['firstname']}</strong>,</p>
            <p>You requested a new activation link.</p>
            <p>Click the button below to activate your account:</p>
            <p style='text-align:center;'>
                <a href='$activationLink' style='background:#4CAF50;color:#fff;padding:10px 20px;text-decoration:none;border-radius:5px;display:inline-block;'>Activate Account</a>
            </p>
            <p>If you didn't request this, ignore this email.</p>
            <p>FarmerBuyerCon Team</p>
            </body></html>";

        echo $mail->send()
            ? json_encode(['status'=>'success','message'=>'Activation email sent! Check your inbox.'])
            : json_encode(['status'=>'error','message'=>'Failed to send activation email.']);
    } else {
        echo json_encode(['status'=>'error','message'=>'No unverified account found with this email.']);
    }
    exit;
}

// ---------- NORMAL LOGIN ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['resend_activation'])) {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['rememberMe']) && $_POST['rememberMe'] == 1;

    $stmt = $conn->prepare("SELECT * FROM buyers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user) {
        // ---- DISABLED ----
        if ($user['is_verify'] == 2) {
            $reason = $user['disable_reason'] ?? 'Not specified';
            echo json_encode([
                'status'  => 'disabled',
                'message' => "Your account is disabled.<br><strong>Reason: <em>$reason</em></strong><br>Note: You will have to register again as a new buyer."
            ]);
            exit;
        }

        // ---- NOT ACTIVATED ----
        if ($user['is_verify'] == 0) {
            echo json_encode([
                'status'  => 'unverified',
                'message' => '<strong>Your account is not activated yet.</strong><br>Please check your email for the activation link.',
                'email'   => $email
            ]);
            exit;
        }

        // ---- PASSWORD CHECK ----
        if (password_verify($password, $user['password'])) {
            $_SESSION['buyer_id']   = $user['buyer_id'];
            $_SESSION['firstname']  = $user['firstname'];
            $_SESSION['lastname']   = $user['lastname'];
            $_SESSION['email']      = $user['email'];
            $_SESSION['LAST_ACTIVITY'] = time();

            $hasCompletedInfo = !empty($user['info_completed']) && $user['info_completed'] == 1;

            if ($remember) {
                $token   = bin2hex(random_bytes(32));
                $expiry  = date('Y-m-d H:i:s', time() + 86400*30);
                $upd = $conn->prepare("UPDATE buyers SET remember_token = ?, token_expiry = ? WHERE buyer_id = ?");
                $upd->bind_param("ssi", $token, $expiry, $user['buyer_id']);
                $upd->execute();
                setcookie("remember_token", $token, time()+86400*30, "/");
            }

            // ---- LOGIN NOTIFICATION EMAIL (optional) ----
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
                $mail->addAddress($user['email'], $user['firstname']);
                $mail->isHTML(true);
                $mail->Subject = 'Login Confirmation';
                $mail->Body    = "<p>Hi <strong>{$user['firstname']}</strong>,</p><p>You have successfully logged in.</p><p>If this wasn't you, secure your account now.</p>";
                $mail->send();
            } catch (Exception $e) { /* silently ignore */ }

            echo json_encode([
                'status'           => 'success',
                'message'          => 'Login successful!',
                'hasCompletedInfo' => $hasCompletedInfo
            ]);
        } else {
            echo json_encode(['status'=>'error','message'=>'Invalid email or password.']);
        }
    } else {
        echo json_encode(['status'=>'error','message'=>'Invalid email or password.']);
    }
    exit;
}
?>