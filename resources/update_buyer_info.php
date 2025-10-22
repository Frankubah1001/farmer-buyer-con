<?php
session_start();
include 'DBcon.php';


if (!isset($_SESSION['email'])) {
    http_response_code(403); // Forbidden
    echo "User not logged in";
    exit;
}

$email = $_SESSION['email'];
$firstname = mysqli_real_escape_string($conn, $_POST['firstname'] ?? '');
$lastname = mysqli_real_escape_string($conn, $_POST['lastname'] ?? '');
$phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
$address = mysqli_real_escape_string($conn, $_POST['address'] ?? '');

$sql = "UPDATE buyers SET firstname = ?, lastname = ?, phone = ?, address = ? WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sssss", $firstname, $lastname, $phone, $address, $email); // Note the order and number of 's'


if (mysqli_stmt_execute($stmt)) {
    $profilePicturePath = '';
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'views/uploads/profile_pics';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $filename = time() . '_' . basename($_FILES['profile_picture']['name']);
        $targetPath = $uploadDir . $filename;
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['profile_picture']['tmp_name']);

        if (in_array($fileType, $allowedTypes) && $_FILES['profile_picture']['size'] <= 2 * 1024 * 1024) { // 2MB limit
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetPath)) {
                // Update user's profile_picture path in the database
                $updatePicSql = "UPDATE buyers SET profile_picture = ? WHERE email = ?";
                $updatePicStmt = mysqli_prepare($conn, $updatePicSql);
                mysqli_stmt_bind_param($updatePicStmt, "ss", $targetPath, $email);
                mysqli_stmt_execute($updatePicStmt);
                mysqli_stmt_close($updatePicStmt);
                $profilePicturePath = $targetPath;
            } else {
                echo '<div class="alert alert-warning">Profile updated, but failed to upload profile picture.</div>';
            }
        } else {
            echo '<div class="alert alert-warning">Profile updated, but invalid profile picture format or size.</div>';
        }
    }

    echo '<div class="alert alert-success">Profile updated successfully!</div>';
} else {
    echo '<div class="alert alert-danger">Error updating profile: ' . mysqli_error($conn) . '</div>';
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>