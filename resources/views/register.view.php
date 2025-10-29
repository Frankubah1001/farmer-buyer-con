<?php
include '../DBcon.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../load_env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Assuming you have a full autoloader setup for PHPMailer
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
        return ['error' => 'File size exceeds ' . ($type === 'profile' ? '2MB' : '5MB') . '.'];
    }

    $upload_dir = __DIR__ . "/../../uploads/farmers_docs/{$user_id}/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_file_name = $type . '_' . time() . '.' . $ext;
    $upload_path = $upload_dir . $new_file_name;
    
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        // Return the path relative to the uploads folder for DB storage
        return "uploads/farmers_docs/{$user_id}/" . $new_file_name;
    } else {
        return ['error' => 'Failed to move uploaded file.'];
    }
}

// Ensure the user_id is passed if you intend to use it for file handling, 
// otherwise generate one or use the auto-increment ID after insertion.
// For this code, we'll use a temporary hash for the folder name before getting the real user_id.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $state_id = (int)($_POST['state'] ?? 0);
    $city_id = (int)($_POST['city'] ?? 0);
    $address = trim($_POST['address'] ?? '');
    $national_id = trim($_POST['national_id'] ?? '');
    $farm_location = trim($_POST['farm_location'] ?? '');
    $farm_size = trim($_POST['farm_size'] ?? '');

    $errors = [];

    // Basic Validation (Expanded for context)
    if (empty($first_name) || empty($last_name) || empty($email) || empty($phone_number) || empty($password) || empty($confirm_password) || empty($national_id)) {
        $errors[] = "All required fields must be filled.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    
    // Check if email already exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "This email is already registered.";
        }
        $stmt->close();
    }
    
    // Check if files were uploaded and are valid (minimal check)
    if (empty($_FILES['profile_picture']['name'])) {
         $errors[] = "Profile picture is required.";
    }
    if (empty($_FILES['national_id_doc']['name'])) {
         $errors[] = "National ID document is required.";
    }


    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $verification_token = bin2hex(random_bytes(32));
        
        // Use a temporary folder name (e.g., hash of email) before getting the real user_id
        $temp_folder_id = md5($email);

        // Upload files using the temporary ID
        $profile_picture_result = handleFileUpload($_FILES['profile_picture'], $temp_folder_id, 'profile');
        $national_id_result = handleFileUpload($_FILES['national_id_doc'], $temp_folder_id, 'national_id');
        $other_document_result = handleFileUpload($_FILES['other_document'], $temp_folder_id, 'other_doc'); // Optional file
        
        // Check for file upload errors
        if (isset($profile_picture_result['error'])) $errors[] = "Profile Picture: " . $profile_picture_result['error'];
        if (isset($national_id_result['error'])) $errors[] = "National ID Doc: " . $national_id_result['error'];
        if (isset($other_document_result['error'])) $errors[] = "Other Document: " . $other_document_result['error'];

        if (!empty($errors)) {
            echo '<div class="alert alert-danger">' . implode('<br>', $errors) . '</div>';
            exit;
        }

        $profile_picture_path = is_array($profile_picture_result) ? null : $profile_picture_result;
        $national_id_path = is_array($national_id_result) ? null : $national_id_result;
        $other_document_path = is_array($other_document_result) ? null : $other_document_result;


        // Prepare SQL statement to insert user data
        // *** CRITICAL UPDATE: Setting cbn_approved = 0 and rejection_reason = NULL ***
        $sql = "INSERT INTO users (
                    first_name, last_name, email, phone_number, password, user_type, 
                    state_id, city_id, address, profile_picture, national_id, farm_location, farm_size, other_document,
                    is_verified, cbn_approved, rejection_reason, verification_token, date_registered
                ) VALUES (
                    ?, ?, ?, ?, ?, 'farmer', 
                    ?, ?, ?, ?, ?, ?, ?, ?, 
                    0, 0, NULL, ?, NOW()
                )";
                
        $stmt = mysqli_prepare($conn, $sql);

        // Bind parameters
        $bind_success = mysqli_stmt_bind_param(
            $stmt, 
            "sssssssssssssss", 
            $first_name, $last_name, $email, $phone_number, $hashed_password, 
            $state_id, $city_id, $address, $profile_picture_path, $national_id, 
            $farm_location, $farm_size, $other_document_path, $verification_token
        );
        
        if (!$bind_success) {
            echo '<div class="alert alert-danger">Internal error: Parameter binding failed.</div>';
            error_log("Parameter binding failed: " . $stmt->error);
            mysqli_stmt_close($stmt);
            exit;
        }


        if (mysqli_stmt_execute($stmt)) {
            $new_user_id = mysqli_insert_id($conn);
            
            // --- File Upload Finalization: Rename temporary folder to user_id ---
            $old_dir = __DIR__ . "/../../uploads/farmers_docs/{$temp_folder_id}";
            $new_dir = __DIR__ . "/../../uploads/farmers_docs/{$new_user_id}";
            if (is_dir($old_dir)) {
                rename($old_dir, $new_dir);
            }
            // --- End File Upload Finalization ---
            
            
            echo '<div class="alert alert-success">Success! Your account has been registered. Please check your email to activate your account. You will receive a separate email once your registration has been **approved by the Admin**.</div>';
            
            // --- PHPMailer Logic (Adjust SMTP settings as needed) ---
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host       = getenv('MAIL_HOST');
                $mail->SMTPAuth   = true;
                $mail->Username   = getenv('MAIL_USERNAME');
                $mail->Password   = getenv('MAIL_PASSWORD');
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = getenv('MAIL_PORT');

                // Recipients
                $mail->setFrom(getenv('MAIL_FROM_ADDRESS'), getenv('MAIL_FROM_NAME'));
                $mail->addAddress($email, $first_name . ' ' . $last_name);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Account Activation & Approval Notice';
                
                // Construct the activation link based on your site structure
                $activationLink = getenv('APP_URL') . "/activate.php?token={$verification_token}";

                $mail->Body    = "
                <html>
                <head>
                    <style>
                        .email-container { max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif; border: 1px solid #ccc; padding: 20px; }
                        .header { background-color: #2f855a; color: white; padding: 10px; text-align: center; }
                        .content { padding: 20px 0; line-height: 1.6; }
                        .button { display: inline-block; padding: 10px 20px; background-color: #38a169; color: white !important; text-decoration: none; border-radius: 5px; }
                        .footer { font-size: 0.8em; text-align: center; color: #666; padding-top: 15px; border-top: 1px solid #eee; }
                    </style>
                </head>
                <body>
                    <div class='email-container'>
                        <div class='header'>
                            <h2>Farmer Registration</h2>
                        </div>
                        <div class='content'>
                            <p>Hi $first_name,</p>
                            <p>Thank you for signing up! Your final account access is dependent on **two steps**: email activation and Admin approval.</p>
                            
                            <p><strong>STEP 1: Email Activation</strong></p>
                            <p>Please confirm your email address by clicking the button below:</p>
                            <p style='text-align: center;'>
                                <a class='button' href='$activationLink'>Activate My Account</a>
                            </p>

                            <p><strong>STEP 2: Admin Approval</strong></p>
                            <p style='color: red;'><strong>Note:</strong> After activation, your account status will be **Pending**. You will need to wait for your uploaded documents to be thoroughly reviewed by an Admin. You will receive a separate email informing you whether you have been **approved or rejected**.</p>
                            
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
        echo '<div class="alert alert-danger">' . implode('<br>', $errors) . '</div>';
    }

} else {
    echo '<div class="alert alert-danger">Invalid request method.</div>';
}

mysqli_close($conn);
?>