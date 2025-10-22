<?php
session_start();
include 'DBcon.php'; 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../load_env.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';
require __DIR__ . '/../../phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require __DIR__ . '/../../phpmailer/vendor/phpmailer/phpmailer/src/SMTP.php';

// Function to send email notification
function sendSubmissionConfirmationEmail($email, $firstName) {
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
        $mail->addAddress($email, $firstName);

        $mail->isHTML(true);
        $mail->Subject = 'Farmer Registration Submitted - FarmerBuyerCon';
        $mail->Body = "
            <html>
            <body>
                <h2>Registration Submission Received</h2>
                <p>Hello $firstName,</p>
                <p>Thank you for submitting your detailed farmer registration information and documents.</p>
                <p>Your application is now under review by the CBN module of the system.</p>
                <p><strong>Important Note:</strong> You will need to wait for 4 to 5 days for your registration to be approved.
                You will receive an email informing you whether you have been approved or rejected after your uploaded documents have been thoroughly reviewed.</p>
                <p>Until your application is approved, you will not be able to access your dashboard.</p>
                <p>Thank you,<br>FarmerBuyerCon Team</p>
            </body>
            </html>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Submission Confirmation Email Error: {$mail->ErrorInfo}");
        return false;
    }
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$userId = $_SESSION['user_id'];
$userEmail = $_SESSION['email']; // Get email from session for sending email
$userFirstName = $_SESSION['first_name']; // Get first name from session for sending email

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect all form data
    $fullName = trim($_POST['fullName'] ?? '');
    $phoneNumber = trim($_POST['phoneNumber'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $nin = trim($_POST['nin'] ?? '');
    $cacNumber = trim($_POST['cacNumber'] ?? '');
    $farmName = trim($_POST['farmName'] ?? '');
    $farmSize = floatval($_POST['farmSize'] ?? 0);
    $farmLocation = trim($_POST['farmLocation'] ?? '');
    $farmState = trim($_POST['farmState'] ?? '');
    $farmAddress = trim($_POST['farmAddress'] ?? '');
    $landOwnership = trim($_POST['landOwnership'] ?? '');
    $cropsProduced = trim($_POST['cropsProduced'] ?? '');
    $farmingExperience = intval($_POST['farmingExperience'] ?? 0);
    $organicCertified = isset($_POST['organicCertified']) ? 1 : 0;
    $additionalInfo = trim($_POST['additionalInfo'] ?? '');
    $termsAgreement = isset($_POST['termsAgreement']) ? 1 : 0;

    // Basic Validation (add more robust validation as needed)
    if (empty($phoneNumber) || empty($dob) || empty($nin) || empty($farmName) ||
        empty($farmSize) || empty($farmLocation) || empty($farmState) ||
        empty($farmAddress) || empty($landOwnership) || !$termsAgreement) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields and agree to terms.']);
        exit;
    }

    // Handle document uploads
    $uploadBaseDir = 'viewuploads/farmerDocs/';
    $userUploadDir = $uploadBaseDir . $userId . '/'; // Create a unique folder for each user

    if (!is_dir($userUploadDir)) {
        if (!mkdir($userUploadDir, 0777, true)) {
            echo json_encode(['success' => false, 'message' => 'Failed to create upload directory.']);
            exit;
        }
    }

    $maxFileSize = 5 * 1024 * 1024; // 5MB
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
    $uploadedDocs = [];
    $fileErrors = [];

    // Helper function to handle a single file upload
    $handleFileUpload = function($fileKey, $required = false) use ($userUploadDir, $maxFileSize, $allowedExtensions, &$fileErrors, &$uploadedDocs, $userId) {
        if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] === UPLOAD_ERR_NO_FILE) {
            if ($required) {
                $fileErrors[] = "The file for '$fileKey' is required.";
            }
            return null;
        }

        $file = $_FILES[$fileKey];
        $fileName = basename($file['name']);
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];

        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($fileError === 0) {
            if (in_array($fileExt, $allowedExtensions)) {
                if ($fileSize <= $maxFileSize) {
                    $fileNameNew = $fileKey . '_' . uniqid('', true) . "." . $fileExt;
                    $fileDestination = $userUploadDir . $fileNameNew;

                    if (move_uploaded_file($fileTmpName, $fileDestination)) {
                        $uploadedDocs[] = [
                            'user_id' => $userId,
                            'document_path' => $fileDestination,
                            'document_name' => $fileName // Original name
                        ];
                        return $fileDestination;
                    } else {
                        $fileErrors[] = "Failed to move uploaded file for $fileKey.";
                    }
                } else {
                    $fileErrors[] = "$fileName for $fileKey is too large (max 5MB).";
                }
            } else {
                $fileErrors[] = "Invalid file type for $fileName ($fileExt). Allowed: " . implode(', ', $allowedExtensions) . ".";
            }
        } else {
            $fileErrors[] = "Error uploading $fileKey: " . $fileError;
        }
        return null;
    };

    // Process each document upload
    $ninDocumentPath = $handleFileUpload('ninDocument', true);
    $cacDocumentPath = $handleFileUpload('cacDocument', false); // CAC document is optional
    $landDocumentPath = $handleFileUpload('landDocument', true);

    if (!empty($fileErrors)) {
        echo json_encode(['success' => false, 'message' => 'Upload errors: ' . implode('; ', $fileErrors)]);
        exit;
    }

    // Begin database transaction
    try {
        $conn->begin_transaction();

        // 1. Update user's general information in the 'users' table
        // Note: 'phone' and 'address' from the users table are updated
        // New columns are added for specific farmer details
        $stmt_users = $conn->prepare("UPDATE users SET
            phone = ?,
            dob = ?,
            nin = ?,
            cac_number = ?,
            farm_name = ?,
            farm_size = ?,
            farm_location_text = ?,
            farm_state_text = ?,
            farm_full_address = ?,
            land_ownership_type = ?,
            crops_produced = ?,
            farming_experience = ?,
            organic_certified = ?,
            additional_info = ?,
            info_completed = 1,
            cbn_approved = 0, -- Set to pending for CBN review
            rejection_reason = NULL -- Clear any previous rejection reason
            WHERE user_id = ?"
        );

        $stmt_users->bind_param("sssssdsssssisii", // Data types: s=string, d=double, i=integer
            $phoneNumber,
            $dob,
            $nin,
            $cacNumber,
            $farmName,
            $farmSize,
            $farmLocation,
            $farmState,
            $farmAddress,
            $landOwnership,
            $cropsProduced,
            $farmingExperience,
            $organicCertified,
            $additionalInfo,
            $userId
        );
        $stmt_users->execute();

        // 2. Insert document paths into 'user_documents' table
        $stmt_docs = $conn->prepare("INSERT INTO user_documents (user_id, document_path, document_name) VALUES (?, ?, ?)");
        foreach ($uploadedDocs as $doc) {
            $stmt_docs->bind_param("iss", $doc['user_id'], $doc['document_path'], $doc['document_name']);
            $stmt_docs->execute();
        }
        $stmt_docs->close();

        // Commit transaction
        $conn->commit();

        // Update session variables to reflect changes
        $_SESSION['info_completed'] = 1;
        $_SESSION['cbn_approved'] = 0; // Set to 0 (pending)

        // Send email notification
        sendSubmissionConfirmationEmail($userEmail, $userFirstName);

        echo json_encode(['success' => true, 'message' => 'Your information and documents have been submitted for CBN review. You will be redirected shortly.']);

    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        // Log the error for debugging
        error_log("Database error during farmer profile submission: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred while saving your data. Please try again.']);
    } finally {
        if (isset($stmt_users)) {
            $stmt_users->close();
        }
        $conn->close();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>