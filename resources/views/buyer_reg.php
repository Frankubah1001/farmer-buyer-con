<?php
// Enable error display for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('error_log', __DIR__ . '/../../logs/error.log');

header('Content-Type: text/html; charset=UTF-8');

include 'DBcon.php';
require_once __DIR__ . '/../../load_env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';
require_once __DIR__ . '/../../phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../../phpmailer/vendor/phpmailer/phpmailer/src/SMTP.php';

// Check database connection
if (!isset($conn) || $conn->connect_error) {
    http_response_code(500);
    echo '<div class="alert alert-danger">Database connection failed: ' . ($conn->connect_error ?? 'Unknown') . '</div>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Input validation
    $first = trim($_POST['firstname'] ?? '');
    $last = trim($_POST['lastname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $buyer_type = trim($_POST['buyer_type'] ?? '');
    $password = $_POST['password'] ?? '';
    $repeat_password = $_POST['repeat_password'] ?? '';

    // Set defaults for optional fields
    if (empty($gender)) $gender = '';
    if (empty($address)) $address = '';
    if (empty($state)) $state = null;
    if (empty($city)) $city = null;
    if (empty($buyer_type)) $buyer_type = 'Individual';

    // Validate required fields
    if (empty($first) || empty($last) || empty($email) || empty($phone) || empty($password) || empty($buyer_type)) {
        http_response_code(400);
        echo '<div class="alert alert-danger">Required fields (firstname, lastname, email, phone, buyer type, password) cannot be empty.</div>';
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo '<div class="alert alert-danger">Invalid email format.</div>';
        exit;
    }

    // Validate state and city are numeric only if provided
    if ((!empty($state) && !is_numeric($state)) || (!empty($city) && !is_numeric($city))) {
        http_response_code(400);
        echo '<div class="alert alert-danger">Invalid state or city selection.</div>';
        exit;
    }

    // Validate password match
    if ($password !== $repeat_password) {
        http_response_code(400);
        echo '<div class="alert alert-danger">Passwords do not match.</div>';
        exit;
    }

    // Check for existing email
    $check_sql = "SELECT 1 FROM buyers WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    if (!$check_stmt) {
        http_response_code(500);
        echo '<div class="alert alert-danger">Database error: ' . $conn->error . '</div>';
        exit;
    }
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    if ($check_result->num_rows > 0) {
        http_response_code(400);
        echo '<div class="alert alert-danger">Email already registered.</div>';
        $check_stmt->close();
        exit;
    }
    $check_stmt->close();

    // Hash password and generate tokens
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $token = bin2hex(random_bytes(32));
    $remember_token = null;
    $is_verify = 0; // Default to "New Buyer" status (0)
    $profile_picture = null;
    $disable_reason = null; // No disable reason for new registrations

    // Insert user
    $sql = "INSERT INTO buyers (firstname, lastname, email, phone, gender, address, state_id, city_id, buyer_type, password, verification_token, remember_token, is_verify, profile_picture, disable_reason) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        http_response_code(500);
        echo '<div class="alert alert-danger">Database error: ' . $conn->error . '</div>';
        exit;
    }
    $stmt->bind_param("ssssssiissssiss", $first, $last, $email, $phone, $gender, $address, $state, $city, $buyer_type, $hashedPassword, $token, $remember_token, $is_verify, $profile_picture, $disable_reason);

    if ($stmt->execute()) {
        $stmt->close();

        // Send activation email
        try {
            $activationLink = "http://localhost/farmerBuyerCon/resources/buyer_activate.php?token=$token";

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USER'] ?? '';
            $mail->Password = $_ENV['SMTP_PASS'] ?? '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Validate SMTP credentials
            if (empty($mail->Username) || empty($mail->Password)) {
                error_log("SMTP credentials missing");
                echo '<div class="alert alert-success text-center" style="font-size: 1.1rem;">
                    <strong>Registration successful! 🎉</strong><br>
                    Email configuration issue. Please contact support to activate your account.
                </div>';
                exit;
            }

            $mail->setFrom($mail->Username, 'FarmerBuyerCon');
            $mail->addAddress($email, $first);
            $mail->isHTML(true);
            $mail->Subject = 'Activate your FarmerBuyerCon account';
            $mail->Body = "
                <html>
                <head>
                  <style>
                    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f9f9f9; margin: 0; padding: 0; }
                    .container { max-width: 600px; margin: 30px auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                    .header { text-align: center; padding-bottom: 20px; }
                    .header h2 { color: #2f855a; }
                    .content { font-size: 16px; line-height: 1.6; color: #333333; }
                    .button { display: inline-block; margin-top: 20px; padding: 12px 20px; background-color: #38a169; color: #ffffff !important; text-decoration: none; font-weight: bold; border-radius: 6px; }
                    .footer { margin-top: 30px; font-size: 13px; color: #888888; text-align: center; }
                  </style>
                </head>
                <body>
                  <div class='container'>
                    <div class='header'>
                      <h2>Welcome to FarmerBuyerCon 👋</h2>
                    </div>
                    <div class='content'>
                      <p>Hi $first,</p>
                      <p>Thank you for signing up on <strong>FarmerBuyerCon</strong> as a <strong>$buyer_type</strong>! To start using your account, please confirm your email address by clicking the button below:</p>
                      <p style='text-align: center;'>
                        <a class='button' href='$activationLink'>Activate My Account</a>
                      </p>
                      <p>If you didn't request this account, you can safely ignore this email.</p>
                    </div>
                    <div class='footer'>
                      &copy; " . date('Y') . " FarmerBuyerCon. All rights reserved.
                    </div>
                  </div>
                </body>
                </html>
            ";

            $mail->send();
            echo '<div class="alert alert-success text-center" style="font-size: 1.1rem;">
                <strong>Registration successful! 🎉</strong><br>
                Please check your email to activate your account.
            </div>';
        } catch (Exception $e) {
            error_log("Email could not be sent. Error: {$mail->ErrorInfo}");
            echo '<div class="alert alert-success text-center" style="font-size: 1.1rem;">
                <strong>Registration successful! 🎉</strong><br>
                Email could not be sent. Please contact support to activate your account.
            </div>';
        }
    } else {
        http_response_code(500);
        echo '<div class="alert alert-danger">Error: ' . $stmt->error . '</div>';
        $stmt->close();
    }
} else {
    http_response_code(405);
    echo '<div class="alert alert-danger">Invalid request method.</div>';
}

$conn->close();
?>