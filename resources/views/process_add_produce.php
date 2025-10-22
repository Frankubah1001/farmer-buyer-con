<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'DBcon.php';

if (!isset($_SESSION['user_id'])) {
    $response = array('status' => 'error', 'message' => 'Farmer not authenticated.');
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $farmer_id = $_SESSION['user_id'];
    $produce = mysqli_real_escape_string($conn, $_POST['produce']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $price = floatval(str_replace(',', '', mysqli_real_escape_string($conn, $_POST['price'])));
    $available_date = !empty($_POST['available_date']) ? mysqli_real_escape_string($conn, $_POST['available_date']) : null;
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $condition = mysqli_real_escape_string($conn, $_POST['condition']);
    $has_order = 0;
    $available = 1; // Available by default
    $allow_visit = mysqli_real_escape_string($conn, $_POST['allow_visit']);
    $visit_time = mysqli_real_escape_string($conn, $_POST['visit_time']);
    $offer_delivery = mysqli_real_escape_string($conn, $_POST['offer_delivery']);
    $delivery_location = mysqli_real_escape_string($conn, $_POST['delivery_location']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    $Is_filled = 1;
    $order_status = 'Pending';

    // Define the absolute and relative paths
    $documentRoot = $_SERVER['DOCUMENT_ROOT']; // e.g., /Applications/XAMPP/xamppfiles/htdocs
    $baseUploadDir = '/farmerBuyerCon/resources/views/Farm_Produce_Images/'; // Relative to document root
    $absoluteUploadDir = $documentRoot . $baseUploadDir; 
    // Validate and format available_date
    $formatted_date = null;
    if ($available_date) {
        $date = DateTime::createFromFormat('Y-m-d', $available_date);
        if ($date && $date->format('Y-m-d') === $available_date) {
            $formatted_date = $available_date;
        } else {
            $response = array('status' => 'error', 'message' => 'Invalid date format for availability date. Use YYYY-MM-DD.');
            echo json_encode($response);
            exit();
        }
    }

    // Basic validation
    $errors = [];
    if (empty($produce)) $errors[] = 'Produce type is required.';
    if (empty($quantity)) $errors[] = 'Quantity available is required.';
    if (empty($price) || !is_numeric($price) || $price <= 0) $errors[] = 'Valid price per unit is required.';
    if (empty($location)) $errors[] = 'Location of farm produce is required.';

    if (!empty($errors)) {
        $response = array('status' => 'error', 'message' => implode('<br>', $errors));
        echo json_encode($response);
        exit();
    }

    // Handle single image upload
    $uploadResult = uploadImage($_FILES['images'], $absoluteUploadDir, ['image/jpeg', 'image/png', 'image/gif'], 2 * 1024 * 1024);

    if ($uploadResult['status'] === 'error') {
        $response = array('status' => 'error', 'message' => $uploadResult['message']);
        echo json_encode($response);
        mysqli_close($conn);
        exit();
    }

    // Get the relative image path for database storage
    $imagePath = $uploadResult['path'] ? $baseUploadDir . basename($uploadResult['path']) : null;

    $sql = "INSERT INTO produce_listings (
        user_id, produce, quantity, price, available_date, 
        `address`, `conditions`, `visit_allowed`, `visit_time`, 
        `delivery_offered`, `delivery_areas`, `notes`, `image_path`, 
        has_order, available, is_filled, order_status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "issdsssssssssiiis",
            $farmer_id,
            $produce,
            $quantity,
            $price,
            $formatted_date,
            $location,
            $condition,
            $allow_visit,
            $visit_time,
            $offer_delivery,
            $delivery_location,
            $comment,
            $imagePath,
            $has_order,
            $available,
            $Is_filled,
            $order_status
        );

        $insert_result = mysqli_stmt_execute($stmt);

        if ($insert_result) {
            $response = array('status' => 'success', 'message' => 'Product listing added successfully!');
        } else {
            $response = array('status' => 'error', 'message' => 'Error adding product listing: ' . mysqli_stmt_error($stmt));
        }

        mysqli_stmt_close($stmt);
    } else {
        $response = array('status' => 'error', 'message' => 'Error preparing SQL statement: ' . mysqli_error($conn));
    }

    mysqli_close($conn);
    echo json_encode($response);

} else {
    $response = array('status' => 'error', 'message' => 'Invalid request method.');
    echo json_encode($response);
}

function uploadImage(array $file, string $uploadDir, array $allowedTypes, int $maxSize): array
{
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            return ['status' => 'error', 'message' => 'Failed to create upload directory: ' . $uploadDir];
        }
    } elseif (!is_writable($uploadDir)) {
        return ['status' => 'error', 'message' => 'Upload directory is not writable: ' . $uploadDir];
    }

    $tmpName = $file['tmp_name'][0] ?? '';
    $fileName = basename($file['name'][0] ?? '');
    $fileType = $file['type'][0] ?? '';
    $fileSize = $file['size'][0] ?? 0;
    $fileError = $file['error'][0] ?? UPLOAD_ERR_NO_FILE;

    if ($fileError === UPLOAD_ERR_OK && !empty($tmpName)) {
        $uniqueFileName = uniqid() . '_' . $fileName;
        $targetPath = $uploadDir . $uniqueFileName;

        if (in_array($fileType, $allowedTypes)) {
            if ($fileSize <= $maxSize) {
                if (move_uploaded_file($tmpName, $targetPath)) {
                    error_log("Successfully moved file to: " . $targetPath);
                    return ['status' => 'success', 'path' => $targetPath];
                } else {
                    return ['status' => 'error', 'message' => 'Failed to move uploaded file: ' . $fileName];
                }
            } else {
                return ['status' => 'error', 'message' => 'File too large: ' . $fileName];
            }
        } else {
            return ['status' => 'error', 'message' => 'Invalid file type for: ' . $fileName];
        }
    } elseif ($fileError !== UPLOAD_ERR_NO_FILE) {
        return ['status' => 'error', 'message' => 'Upload error (' . $fileError . ') for: ' . $fileName];
    }

    return ['status' => 'success', 'path' => null]; // No image uploaded
}