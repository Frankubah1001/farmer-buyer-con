<?php
session_start();
include 'DBcon.php';

header('Content-Type: text/html'); // Set content type for HTML response

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo '<div class="alert alert-danger">Invalid request method</div>';
    exit;
}

if (!isset($_SESSION['email'])) {
    echo '<div class="alert alert-danger">User not logged in</div>';
    exit;
}

// Get user ID
$email = $_SESSION['email'];
$stmt = mysqli_prepare($conn, "SELECT user_id FROM users WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo '<div class="alert alert-danger">User not found</div>';
    exit;
}

$user_id = $user['user_id'];

// Process file uploads
$photoPaths = [];
if (!empty($_FILES['photos'])) {
    $uploadDir = 'views/uploads/';
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['photos']['error'][$key] === UPLOAD_ERR_OK) {
            $filename = basename($_FILES['photos']['name'][$key]);
            $safeFilename = time() . '_' . preg_replace("/[^A-Za-z0-9\._-]/", "_", $filename);
            $targetPath = $uploadDir . $safeFilename;

            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = mime_content_type($tmp_name);
            
            if (in_array($fileType, $allowedTypes)) {
                if (move_uploaded_file($tmp_name, $targetPath)) {
                    $photoPaths[] = $targetPath;
                }
            }
        }
    }
}

// Prepare data for database
$produce = mysqli_real_escape_string($conn, $_POST['produce'] ?? '');
$quantity = mysqli_real_escape_string($conn, $_POST['quantity'] ?? '');
$price = mysqli_real_escape_string($conn, $_POST['price'] ?? '');
$date = mysqli_real_escape_string($conn, $_POST['date'] ?? '');
$address = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
$condition = mysqli_real_escape_string($conn, $_POST['condition'] ?? '');
$visit = mysqli_real_escape_string($conn, $_POST['visit'] ?? '');
$visit_time = mysqli_real_escape_string($conn, $_POST['visit_time'] ?? '');
$delivery = mysqli_real_escape_string($conn, $_POST['delivery'] ?? '');
$delivery_area = mysqli_real_escape_string($conn, $_POST['delivery_area'] ?? '');
$notes = mysqli_real_escape_string($conn, $_POST['notes'] ?? '');
$image_path = implode(',', $photoPaths);

// Insert into database
$stmt = mysqli_prepare($conn, "INSERT INTO produce_listings 
    (user_id, produce, quantity, price, available_date, image_path, address, conditions, visit_allowed, visit_time, delivery_offered, delivery_areas, notes) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

mysqli_stmt_bind_param($stmt, "issssssssssss", 
    $user_id, $produce, $quantity, $price, $date, $image_path, $address, $condition, 
    $visit, $visit_time, $delivery, $delivery_area, $notes);

if (mysqli_stmt_execute($stmt)) {
    echo '<div class="alert alert-success">Produce listing submitted successfully!</div>';

    // Update the users table
    $updateStmt = mysqli_prepare($conn, "UPDATE users SET info_completed = 1 WHERE user_id = ?");
    mysqli_stmt_bind_param($updateStmt, "i", $user_id);
    mysqli_stmt_execute($updateStmt);
    mysqli_stmt_close($updateStmt); // Close the update statement
    
} else {
    echo '<div class="alert alert-danger">Error: ' . mysqli_error($conn) . '</div>';
}

mysqli_close($conn);
?>