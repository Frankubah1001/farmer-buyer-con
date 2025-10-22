<?php
include '../DBcon.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../load_env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

// Helper function to handle file upload
function handleFileUpload($file, $user_id, $type = 'profile') {
    if (!isset($file) || $file['error'] == UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $allowed_types = ($type === 'profile') ? ['image/jpeg', 'image/png'] : ['image/jpeg', 'image/png', 'application/pdf'];
    $max_size = ($type === 'profile') ? 2 * 1024 * 1024 : 5 * 1024 * 1024;
    
    if (!in_array($file['type'], $allowed_types)) {
        return ['error' => 'Invalid file type. Only ' . ($type === 'profile' ? 'JPG, PNG' : 'JPG, PNG, PDF') . ' allowed.'];
    }
    
    if ($file['size'] > $max_size) {
        return ['error' => 'File size exceeds ' . ($type === 'profile' ? '2MB' : '5MB') . ' limit.'];
    }

    // Use absolute paths
    $base_dir = $_SERVER['DOCUMENT_ROOT'] . '/farmerBuyerCon/';
    $upload_dir = ($type === 'profile') ? $base_dir . 'admin/Uploads/' : $base_dir . 'admin/Uploads/documents/';
    
    // Create directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            return ['error' => 'Failed to create upload directory.'];
        }
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $prefix = ($type === 'profile') ? 'user_' : $type . '_';
    $filename = $prefix . $user_id . '_' . time() . '.' . $ext;
    $destination = $upload_dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // Return relative paths for database storage
        return ($type === 'profile') ? 'admin/Uploads/' . $filename : [
            'document_path' => 'admin/Uploads/documents/' . $filename,
            'document_name' => $file['name']
        ];
    }
    return ['error' => 'Failed to upload file.'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Personal Information
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $state_id = $_POST['state_id'];
    $city_id = $_POST['city_id'];
    $password = $_POST['password'];
    $repeat_password = $_POST['repeat_password'];

    // Farm Information
    $farm_name = $_POST['farm_name'] ?? '';
    $farm_size = $_POST['farm_size'] ?? '';
    $farm_full_address = $_POST['farm_full_address'] ?? '';
    $land_ownership_type = $_POST['land_ownership_type'] ?? '';
    $farming_experience = $_POST['farming_experience'] ?? '';
    
    // Identification
    $cac_number = $_POST['cac_number'] ?? '';
    $nin = $_POST['nin'] ?? '';

    // Get state and city names for text fields
    $state_name = '';
    $city_name = '';
    
    $location_stmt = $conn->prepare("SELECT s.state_name, c.city_name 
                                    FROM states s 
                                    LEFT JOIN cities c ON s.state_id = c.state_id 
                                    WHERE s.state_id = ? AND c.city_id = ?");
    $location_stmt->bind_param("ii", $state_id, $city_id);
    $location_stmt->execute();
    $location_result = $location_stmt->get_result();
    if ($location_row = $location_result->fetch_assoc()) {
        $state_name = $location_row['state_name'];
        $city_name = $location_row['city_name'];
    }
    $location_stmt->close();
    
    // Create farm location text
    $farm_location_text = $city_name . ', ' . $state_name;
    $farm_state_text = $state_name;

    // Validation
    if ($password !== $repeat_password) {
        echo '<div class="alert alert-danger">Passwords do not match.</div>';
        exit;
    }

    // Check if email already exists
    $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if (mysqli_num_rows($check_result) > 0) {
        echo '<div class="alert alert-danger">Email already registered.</div>';
        exit;
    }
    mysqli_stmt_close($check_stmt);

    // Hash password and generate token
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $token = bin2hex(random_bytes(32));
    $cbn_approved = 0;
    $is_verified = 0;
    $info_completed = 1;

    // Handle profile picture upload
    $profile_picture = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] != UPLOAD_ERR_NO_FILE) {
        $file_result = handleFileUpload($_FILES['profile_picture'], 'temp', 'profile');
        if (is_array($file_result) && isset($file_result['error'])) {
            echo '<div class="alert alert-danger">Profile Picture: ' . $file_result['error'] . '</div>';
            exit;
        }
        $profile_picture = $file_result;
    }

    // Insert user into database with ALL fields
    $sql = "INSERT INTO users (
        first_name, last_name, email, phone, gender, address, state_id, city_id, password, 
        verification_token, cbn_approved, is_verified, info_completed, 
        cac_number, nin, farm_name, farm_size, farm_full_address, farm_location_text, farm_state_text,
        land_ownership_type, farming_experience, profile_picture
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        echo '<div class="alert alert-danger">Database error: ' . $conn->error . '</div>';
        exit;
    }

    // Convert empty strings to NULL for numeric fields
    $farm_size = ($farm_size === '') ? null : $farm_size;
    $farming_experience = ($farming_experience === '') ? null : $farming_experience;
    
    // Debug: Check values before binding
    error_log("Password: " . $password);
    error_log("Hashed Password: " . $hashedPassword);
    error_log("Farm Location Text: " . $farm_location_text);
    error_log("Farm State Text: " . $farm_state_text);
    error_log("Farm Full Address: " . $farm_full_address);

    // Bind parameters - make sure the types match your database schema
    // s = string, i = integer, d = double
    $stmt->bind_param("ssssssiisssiisssdssssis", 
        $first_name,       // s
        $last_name,        // s
        $email,            // s
        $phone,            // s
        $gender,           // s
        $address,          // s
        $state_id,         // i
        $city_id,          // i
        $hashedPassword,   // s
        $token,            // s
        $cbn_approved,     // i
        $is_verified,      // i
        $info_completed,   // i
        $cac_number,       // s
        $nin,              // s
        $farm_name,        // s
        $farm_size,        // d (double for decimal)
        $farm_full_address, // s
        $farm_location_text, // s
        $farm_state_text,  // s
        $land_ownership_type, // s
        $farming_experience, // i
        $profile_picture   // s
    );

    if ($stmt->execute()) {
        $user_id = mysqli_insert_id($conn);
        
        // Debug: Check if user was inserted
        error_log("User ID inserted: " . $user_id);

        // Rename profile picture with actual user_id
        if ($profile_picture) {
            $base_dir = $_SERVER['DOCUMENT_ROOT'] . '/farmerBuyerCon/';
            $old_path = $base_dir . $profile_picture;
            $ext = pathinfo($profile_picture, PATHINFO_EXTENSION);
            $new_filename = 'user_' . $user_id . '_' . time() . '.' . $ext;
            
            $documents_dir = $base_dir . 'admin/Uploads/documents/';
            if (!is_dir($documents_dir)) {
                mkdir($documents_dir, 0755, true);
            }
            
            $new_path = $documents_dir . $new_filename;
            
            if (file_exists($old_path) && rename($old_path, $new_path)) {
                $new_profile_picture = 'admin/Uploads/documents/' . $new_filename;
                $update_stmt = mysqli_prepare($conn, "UPDATE users SET profile_picture = ? WHERE user_id = ?");
                mysqli_stmt_bind_param($update_stmt, "si", $new_profile_picture, $user_id);
                mysqli_stmt_execute($update_stmt);
                mysqli_stmt_close($update_stmt);
            }
        }

        // Handle document uploads
        $document_types = ['ninDocument', 'cacDocument', 'landDocument'];
        foreach ($document_types as $doc_type) {
            if (isset($_FILES[$doc_type]) && $_FILES[$doc_type]['error'] != UPLOAD_ERR_NO_FILE) {
                $file_result = handleFileUpload($_FILES[$doc_type], $user_id, $doc_type);
                if (is_array($file_result) && isset($file_result['error'])) {
                    // Log error but don't stop registration
                    error_log("Document upload error: " . $file_result['error']);
                    continue;
                }
                if ($file_result) {
                    $doc_stmt = mysqli_prepare($conn, "INSERT INTO user_documents (user_id, document_path, document_name) VALUES (?, ?, ?)");
                    mysqli_stmt_bind_param($doc_stmt, "iss", $user_id, $file_result['document_path'], $file_result['document_name']);
                    mysqli_stmt_execute($doc_stmt);
                    mysqli_stmt_close($doc_stmt);
                }
            }
        }

        // Send activation email
        echo '<div class="alert alert-success text-center" style="font-size: 1.1rem;">
            <strong>Registration successful! 🎉</strong><br>
            Please check your email to activate your account.
        </div>';

        try {
            $activationLink = "http://localhost/farmerBuyerCon/resources/activate.php?token=$token";

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USER'];
            $mail->Password = $_ENV['SMTP_PASS'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom($_ENV['SMTP_USER'], 'FarmerBuyerCon');
            $mail->addAddress($email, $first_name);

            $mail->isHTML(true);
            $mail->Subject = 'Activate your FarmerBuyerCon account';
            $mail->Body = "
            <html>
            <head>
              <style>
                body {
                  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                  background-color: #f9f9f9;
                  margin: 0;
                  padding: 0;
                }
                .container {
                  max-width: 600px;
                  margin: 30px auto;
                  background-color: #ffffff;
                  padding: 30px;
                  border-radius: 8px;
                  box-shadow: 0 0 10px rgba(0,0,0,0.1);
                }
                .header {
                  text-align: center;
                  padding-bottom: 20px;
                }
                .header h2 {
                  color: #2f855a;
                }
                .content {
                  font-size: 16px;
                  line-height: 1.6;
                  color: #333333;
                }
                .button {
                  display: inline-block;
                  margin-top: 20px;
                  padding: 12px 20px;
                  background-color: #38a169;
                  color: #ffffff !important;
                  text-decoration: none;
                  font-weight: bold;
                  border-radius: 6px;
                }
                .footer {
                  margin-top: 30px;
                  font-size: 13px;
                  color: #888888;
                  text-align: center;
                }
              </style>
            </head>
            <body>
              <div class='container'>
                <div class='header'>
                  <h2>Welcome to FarmerBuyerCon 👋</h2>
                </div>
                <div class='content'>
                  <p>Hi $first_name,</p>
                  <p>Thank you for signing up on <strong>FarmerBuyerCon</strong>! To start using your account, please confirm your email address by clicking the button below:</p>
                  <p style='text-align: center;'>
                    <a class='button' href='$activationLink'>Activate My Account</a>
                  </p>
                  <p style='color: red;'><strong>Note:</strong> You will need to wait for 4 to 5 days for your registration to be approved. 
                  You will receive an email informing you whether you have been approved or 
                  rejected after your uploaded documents have been thoroughly reviewed.</p>
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
        } catch (Exception $e) {
            error_log("Email could not be sent. Error: {$mail->ErrorInfo}");
        }

    } else {
        echo '<div class="alert alert-danger">Error: ' . $stmt->error . '</div>';
        error_log("Registration error: " . $stmt->error);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo '<div class="alert alert-danger">Invalid request method.</div>';
}
?>