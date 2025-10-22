<?php
session_start();
require_once 'DBcon.php'; // Include your database connection file

// Enable error reporting for debugging (REMOVE IN PRODUCTION)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json'); // Set header to indicate JSON response

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

// Define upload directory and allowed file types/size
$uploadDir = 'uploads/loan_documents/'; // Make sure this directory exists and is writable!
$allowedFileTypes = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
$maxFileSize = 5 * 1024 * 1024; // 5 MB

// Ensure upload directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
}

// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        $response['message'] = 'User not logged in. Please log in to apply for a loan.';
        echo json_encode($response);
        exit();
    }

    $userId = $_SESSION['user_id'];

    // Sanitize and retrieve form data
    $loanPlatform = filter_input(INPUT_POST, 'loan_platform');
    $loanAmount = filter_input(INPUT_POST, 'loan_amount');
    $loanPurpose = filter_input(INPUT_POST, 'loan_purpose');
    $repaymentPeriod = filter_input(INPUT_POST, 'repayment_period');

    // New fields for bank details
    $bankName = filter_input(INPUT_POST, 'bank_name');
    $accountNumber = filter_input(INPUT_POST, 'account_number');
    $accountName = filter_input(INPUT_POST, 'account_name');


    // --- Server-side Validation ---
    if (empty($loanPlatform) || $loanAmount === false || empty($loanPurpose) || $repaymentPeriod === false) {
        $response['message'] = 'All required fields must be filled correctly.';
        echo json_encode($response);
        exit();
    }

    if ($loanAmount < 10000) {
        $response['message'] = 'Loan amount must be at least NGN 10,000.';
        echo json_encode($response);
        exit();
    }

    if ($repaymentPeriod < 1 || $repaymentPeriod > 60) {
        $response['message'] = 'Repayment period must be between 1 and 60 months.';
        echo json_encode($response);
        exit();
    }

    // Validate new bank details fields (assuming they are required)
    if (empty($bankName) || empty($accountNumber) || empty($accountName)) {
        $response['message'] = 'Bank account details are required for loan disbursement.';
        echo json_encode($response);
        exit();
    }
    // Basic account number validation (e.g., numeric and length)
    if (!ctype_digit($accountNumber) || strlen($accountNumber) < 8 || strlen($accountNumber) > 15) { // Adjust length as per Nigerian bank standards
        $response['message'] = 'Invalid account number format.';
        echo json_encode($response);
        exit();
    }


    // --- File Upload Handling ---
    $uploadedFilePaths = [];
    if (isset($_FILES['supporting_documents']) && is_array($_FILES['supporting_documents']['name'])) {
        $totalFiles = count($_FILES['supporting_documents']['name']);

        if ($totalFiles > 5) {
            $response['message'] = 'You can upload a maximum of 5 supporting documents.';
            echo json_encode($response);
            exit();
        }

        for ($i = 0; $i < $totalFiles; $i++) {
            $fileName = $_FILES['supporting_documents']['name'][$i];
            $fileTmpName = $_FILES['supporting_documents']['tmp_name'][$i];
            $fileSize = $_FILES['supporting_documents']['size'][$i];
            $fileError = $_FILES['supporting_documents']['error'][$i];
            $fileType = $_FILES['supporting_documents']['type'][$i];

            if ($fileError === UPLOAD_ERR_OK) {
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if (in_array($fileExt, $allowedFileTypes)) {
                    if ($fileSize < $maxFileSize) {
                        $newFileName = uniqid('', true) . '.' . $fileExt;
                        $fileDestination = $uploadDir . $newFileName;

                        if (move_uploaded_file($fileTmpName, $fileDestination)) {
                            $uploadedFilePaths[] = $fileDestination;
                        } else {
                            $response['message'] = 'Failed to move uploaded file: ' . htmlspecialchars($fileName);
                            echo json_encode($response);
                            exit();
                        }
                    } else {
                        $response['message'] = 'File size too large: ' . htmlspecialchars($fileName) . '. Max ' . ($maxFileSize / (1024 * 1024)) . 'MB.';
                        echo json_encode($response);
                        exit();
                    }
                } else {
                    $response['message'] = 'Invalid file type: ' . htmlspecialchars($fileName) . '. Allowed types: ' . implode(', ', $allowedFileTypes) . '.';
                    echo json_encode($response);
                    exit();
                }
            } elseif ($fileError != UPLOAD_ERR_NO_FILE) {
                // Handle other upload errors (e.g., UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE)
                $response['message'] = 'File upload error: ' . htmlspecialchars($fileName) . ' (Code: ' . $fileError . ').';
                echo json_encode($response);
                exit();
            }
        }
    }

    $documentPathsJson = !empty($uploadedFilePaths) ? json_encode($uploadedFilePaths) : NULL;

    // --- Insert into Database ---
    $sql = "INSERT INTO loan_applications (user_id, loan_platform, loan_amount, loan_purpose, repayment_period_months, document_paths, bank_name, account_number, account_name)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        $response['message'] = 'Database error: Could not prepare statement for loan application.';
        error_log("Failed to prepare statement for loan application: " . mysqli_error($conn));
        echo json_encode($response);
        exit();
    }

    mysqli_stmt_bind_param($stmt, "isdsissss",
        $userId,
        $loanPlatform,
        $loanAmount,
        $loanPurpose,
        $repaymentPeriod,
        $documentPathsJson,
        $bankName,        // New parameter
        $accountNumber,   // New parameter
        $accountName      // New parameter
    );

    if (mysqli_stmt_execute($stmt)) {
        $response['status'] = 'success';
        $response['message'] = 'Your loan application has been submitted successfully!';
    } else {
        $response['message'] = 'Failed to submit loan application. Please try again.';
        error_log("Error submitting loan application: " . mysqli_stmt_error($stmt));
    }

    mysqli_stmt_close($stmt);

} else {
    $response['message'] = 'Invalid request method. Form must be submitted via POST.';
}

mysqli_close($conn);
echo json_encode($response);
?>
