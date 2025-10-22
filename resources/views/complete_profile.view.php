<?php
session_start();
include '../DBcon.php'; // Adjust path if necessary
require_once __DIR__ . '/../../load_env.php'; // Adjust path if necessary

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit;
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phoneNumber = trim($_POST['phoneNumber']);
    $address = trim($_POST['address']);
    $farmLocation = trim($_POST['farmLocation']);

    // Input validation (add more robust validation as needed)
    if (empty($phoneNumber) || empty($address) || empty($farmLocation)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    // Handle document uploads
    $uploadDir = __DIR__ . '/../../uploads/documents/'; // Adjust path for your document storage
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
    }

    $uploadedFiles = [];
    $errors = [];

    if (isset($_FILES['documentUpload'])) {
        foreach ($_FILES['documentUpload']['name'] as $key => $name) {
            $fileName = basename($_FILES['documentUpload']['name'][$key]);
            $fileTmpName = $_FILES['documentUpload']['tmp_name'][$key];
            $fileSize = $_FILES['documentUpload']['size'][$key];
            $fileError = $_FILES['documentUpload']['error'][$key];
            $fileType = $_FILES['documentUpload']['type'][$key];

            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']; // Allowed document types

            if (in_array($fileExt, $allowed)) {
                if ($fileError === 0) {
                    if ($fileSize < 5000000) { // Max 5MB file size
                        $fileNameNew = uniqid('', true) . "." . $fileExt;
                        $fileDestination = $uploadDir . $fileNameNew;

                        if (move_uploaded_file($fileTmpName, $fileDestination)) {
                            $uploadedFiles[] = $fileDestination;
                        } else {
                            $errors[] = "Failed to upload $name.";
                        }
                    } else {
                        $errors[] = "$name is too large.";
                    }
                } else {
                    $errors[] = "Error uploading $name.";
                }
            } else {
                $errors[] = "You cannot upload files of type $fileExt for $name.";
            }
        }
    }

    if (!empty($errors)) {
        // If there are file upload errors, you might want to stop here or proceed
        // depending on whether documents are strictly required.
        // For now, let's assume they are critical.
        echo json_encode(['status' => 'error', 'message' => 'Document upload error: ' . implode(', ', $errors)]);
        exit;
    }

    // Save details to the database
    try {
        $conn->begin_transaction();

        // Update user's main details
        $stmt = $conn->prepare("UPDATE users SET phone_number = ?, address = ?, farm_location = ?, info_completed = 1 WHERE user_id = ?");
        $stmt->bind_param("sssi", $phoneNumber, $address, $farmLocation, $userId);
        $stmt->execute();

        // Insert uploaded document paths into a new table (e.g., `user_documents`)
        // You'll need to create this table:
        // CREATE TABLE user_documents (
        //     document_id INT AUTO_INCREMENT PRIMARY KEY,
        //     user_id INT,
        //     document_path VARCHAR(255),
        //     uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        //     FOREIGN KEY (user_id) REFERENCES users(user_id)
        // );
        $docStmt = $conn->prepare("INSERT INTO user_documents (user_id, document_path) VALUES (?, ?)");
        foreach ($uploadedFiles as $filePath) {
            $docStmt->bind_param("is", $userId, $filePath);
            $docStmt->execute();
        }
        $docStmt->close();

        $conn->commit();

        echo json_encode(['status' => 'success', 'message' => 'Profile details and documents submitted successfully. Please wait for CBN approval.']);

    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        error_log("Database error during profile completion: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
        $conn->close();
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>