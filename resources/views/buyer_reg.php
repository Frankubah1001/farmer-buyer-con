<?php
include 'DBcon.php';
require_once __DIR__ . '/../../load_env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';
require_once __DIR__ . '/../../phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../../phpmailer/vendor/phpmailer/phpmailer/src/SMTP.php';

// ---------- VALIDATION ----------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo '<div class="alert alert-danger">Invalid request method.</div>';
    exit;
}

$first   = trim($_POST['firstname'] ?? '');
$last    = trim($_POST['lastname'] ?? '');
$email   = trim($_POST['email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$gender  = trim($_POST['gender'] ?? '');
$address = trim($_POST['address'] ?? '');
$state   = trim($_POST['state'] ?? '');
$city    = trim($_POST['city'] ?? '');
$buyer_type = trim($_POST['buyer_type'] ?? '');
$password = $_POST['password'] ?? '';
$repeat_password = $_POST['repeat_password'] ?? '';

if (empty($first) || empty($last) || empty($email) || empty($phone) || empty($password) || empty($buyer_type)) {
    echo '<div class="alert alert-danger">All required fields are mandatory.</div>'; exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo '<div class="alert alert-danger">Invalid e-mail address.</div>'; exit;
}
if ($password !== $repeat_password) {
    echo '<div class="alert alert-danger">Passwords do not match.</div>'; exit;
}
if ((!empty($state) && !is_numeric($state)) || (!empty($city) && !is_numeric($city))) {
    echo '<div class="alert alert-danger">Invalid state/city selection.</div>'; exit;
}

// ---------- CHECK DUPLICATE ----------
$check = $conn->prepare("SELECT 1 FROM buyers WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    echo '<div class="alert alert-danger">E-mail already registered.</div>'; exit;
}
$check->close();

// ---------- INSERT ----------
$hashed = password_hash($password, PASSWORD_DEFAULT);
$token  = bin2hex(random_bytes(32));
$is_verify = 0;               // 0 = new, needs activation
$remember_token = null;
$profile_picture = null;
$disable_reason = null;

$sql = "INSERT INTO buyers 
        (firstname, lastname, email, phone, gender, address, state_id, city_id, buyer_type, password,
         verification_token, remember_token, is_verify, profile_picture, disable_reason)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssssssiissssiss",
    $first, $last, $email, $phone, $gender, $address,
    $state, $city, $buyer_type, $hashed,
    $token, $remember_token, $is_verify, $profile_picture, $disable_reason
);

if (!$stmt->execute()) {
    echo '<div class="alert alert-danger">DB error: ' . $stmt->error . '</div>';
    $stmt->close(); exit;
}
$stmt->close();

// ---------- SEND ACTIVATION EMAIL ---------- // Corrected path
$activationLink = "http://localhost/farmerBuyerCon/resources/buyer_activate.php?token=$token";

$mail = new PHPMailer(true);
try {
    // ----- SMTP CONFIG -----
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['SMTP_USER'] ?? '';
    $mail->Password   = $_ENV['SMTP_PASS'] ?? '';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // ----- VALIDATE CREDENTIALS -----
    if (empty($mail->Username) || empty($mail->Password)) {
        throw new Exception('SMTP credentials are missing in .env');
    }

    // ----- DEBUG (remove in production) -----
    $mail->SMTPDebug = 2;                     // 2 = show client messages
    $mail->Debugoutput = function($str, $level) {
        error_log("PHPMailer [$level]: $str");
    };

    // ----- EMAIL CONTENT -----
    $mail->setFrom($mail->Username, 'FarmerBuyerCon');
    $mail->addAddress($email, $first);
    $mail->isHTML(true);
    $mail->Subject = 'Activate your FarmerBuyerCon account';
    $mail->Body = '
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Activate Your FarmerBuyerCon Account</title>
  <style>
    body {
      margin: 0; padding: 0; background: #f4f7f9; font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }
    .container {
      max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 12px;
      overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }
    .header {
      background: linear-gradient(135deg, #2f855a, #38a169); color: white; padding: 30px 20px; text-align: center;
    }
    .header h1 {
      margin: 0; font-size: 28px; font-weight: 600;
    }
    .content {
      padding: 35px 40px; color: #333333; line-height: 1.7;
    }
    .content p {
      margin: 0 0 16px; font-size: 16px;
    }
    .highlight {
      color: #2f855a; font-weight: 600;
    }
    .btn {
      display: inline-block; background: #38a169; color: white !important; text-decoration: none;
      font-weight: 600; font-size: 17px; padding: 14px 32px; border-radius: 8px; margin: 20px 0;
      box-shadow: 0 4px 12px rgba(56, 161, 105, 0.3);
      transition: all 0.3s ease;
    }
    .btn:hover {
      background: #2f855a; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(56, 161, 105, 0.4);
    }
    .footer {
      background: #f8f9fa; padding: 25px; text-align: center; font-size: 13px; color: #6c757d;
      border-top: 1px solid #e9ecef;
    }
    .footer a {
      color: #38a169; text-decoration: none; font-weight: 500;
    }
    .footer a:hover { text-decoration: underline; }
    @media (max-width: 600px) {
      .content { padding: 25px 20px; }
      .header h1 { font-size: 24px; }
    }
  </style>
</head>
<body>
  <div class="container">

    <!-- Header: Logo & Welcome -->
    <div class="header">
      <h1>FarmerBuyerCon</h1>
    </div>

    <!-- Main Content -->
    <div class="content">
      <p>Hi <span class="highlight">' . htmlspecialchars($first) . '</span>,</p>

      <p>Welcome to <strong>FarmerBuyerCon</strong> – the trusted marketplace connecting farmers and buyers!</p>

      <p>You’ve successfully registered as a <span class="highlight">' . htmlspecialchars($buyer_type) . '</span>. To start exploring fresh produce, placing orders, and connecting with farmers, please <strong>activate your account</strong> by clicking the button below:</p>

      <div style="text-align: center;">
        <a href="' . $activationLink . '" class="btn">Activate My Account</a>
      </div>

      <p><strong>Why activate?</strong></p>
      <ul style="margin: 16px 0; padding-left: 20px;">
        <li>Access your personalized buyer dashboard</li>
        <li>Browse real-time farm listings</li>
        <li>Place secure orders with confidence</li>
        <li>Get notified about new harvests</li>
      </ul>

      <p><em>This link expires in 24 hours for your security.</em></p>

      <p>If you didn’t create this account, please ignore this email – no action is needed.</p>
    </div>

    <!-- Footer -->
    <div class="footer">
      <p><strong>FarmerBuyerCon</strong> – Fresh from Farm to You</p>
      <p>
        Need help? <a href="mailto:support@farmerbuyercon.com">Contact Support</a> • 
        <a href="http://localhost/farmerBuyerCon/privacy.php">Privacy Policy</a>
      </p>
      <p>&copy; ' . date('Y') . ' FarmerBuyerCon. All rights reserved.</p>
    </div>

  </div>
</body>
</html>';

    $mail->send();

    echo '<div class="alert alert-success text-center" style="font-size:1.1rem;">
            <strong>Registration successful!</strong><br>
            <strong style="color:#28a745;">Check your inbox</strong> – activation link sent.
          </div>';

} catch (Exception $e) {
    // ----- LOG FULL ERROR -----
    error_log("PHPMailer Error: {$mail->ErrorInfo} | Exception: {$e->getMessage()}");

    // ----- USER-FRIENDLY FALLBACK -----
    echo '<div class="alert alert-warning text-center" style="font-size:1.1rem;">
            <strong>Registration successful!</strong><br>
            <strong style="color:#e67e22;">Could not send e-mail.</strong><br>
            <small>Please contact support with this code: <code>' . substr(md5($e->getMessage()),0,8) . '</code></small>
          </div>';
}

$conn->close();
?>