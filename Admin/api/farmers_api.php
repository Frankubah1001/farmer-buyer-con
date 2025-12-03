<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// api/farmers_api.php - RESTful API for Farmers Management
header('Content-Type: application/json');
session_start();
require_once 'DBcon.php';

require_once __DIR__ . '/../../load_env.php'; // Adjust path based on your directory structure

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';
require __DIR__ . '/../../phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require __DIR__ . '/../../phpmailer/vendor/phpmailer/phpmailer/src/SMTP.php';

// Check admin session
if (!isset($_SESSION['cbn_user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

// Helper function to send JSON response
function sendResponse($success, $data = [], $error = '') {
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'error' => $error
    ]);
    exit();
}

// Helper function to handle file upload
function handleFileUpload($file, $user_id, $type = 'profile') {
    if (!isset($file) || $file['error'] == UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $allowed_types = ($type === 'profile') ? ['image/jpeg', 'image/png'] : ['image/jpeg', 'image/png', 'application/pdf'];
    $max_size = ($type === 'profile') ? 2 * 1024 * 1024 : 5 * 1024 * 1024; // 2MB for profile, 5MB for documents
    if (!in_array($file['type'], $allowed_types)) {
        return ['error' => 'Invalid file type. Only ' . ($type === 'profile' ? 'JPG, PNG' : 'JPG, PNG, PDF') . ' allowed.'];
    }
    if ($file['size'] > $max_size) {
        return ['error' => 'File size exceeds ' . ($type === 'profile' ? '2MB' : '5MB') . ' limit.'];
    }

    // Use absolute paths to avoid permission issues
    $base_dir = $_SERVER['DOCUMENT_ROOT'] . '/farmerBuyerCon/admin/';
    $upload_dir = ($type === 'profile') ? $base_dir . 'Uploads/' : $base_dir . 'Uploads/documents/';
    
    // Create directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            return ['error' => 'Failed to create upload directory. Please check permissions.'];
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
    return ['error' => 'Failed to upload file. Check directory permissions.'];
}

// Helper function to generate random password
function generateRandomPassword($length = 12) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}

// Helper function to send welcome email
function sendWelcomeEmail($email, $first_name, $password) {
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
        $mail->addAddress($email, $first_name);

        $mail->isHTML(true);
        $mail->Subject = 'Welcome to FarmerBuyerCon - Your Account Details';
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
              <p>Thank you for joining <strong>FarmerBuyerCon</strong>! Your account has been created. Below are your login details:</p>
              <p><strong>Email:</strong> $email</p>
              <p><strong>Password:</strong> $password</p>
              <p>Please use these credentials to log in at <a href='http://localhost/farmerBuyerCon/Admin/cbn_login.php'>FarmerBuyerCon</a>. Upon your first login, you will be prompted to change your password.</p>
              <p style='color: red;'><strong>Note:</strong> You will need to wait for 4 to 5 days for your registration to be approved. You will receive an email informing you whether you have been approved or rejected after your uploaded documents have been thoroughly reviewed.</p>
              <p>If you didn't request this account, please contact our support team.</p>
            </div>
            <div class='footer'>
              &copy; " . date('Y') . " FarmerBuyerCon. All rights reserved.
            </div>
          </div>
        </body>
        </html>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return ['error' => 'Failed to send email: ' . $mail->ErrorInfo];
    }
}

// Fetch farmers with filters and pagination
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $status = isset($_GET['status']) ? trim($_GET['status']) : '';
    $date = isset($_GET['date']) ? trim($_GET['date']) : '';
    $location = isset($_GET['location']) ? trim($_GET['location']) : '';
    $offset = ($page - 1) * $limit;
    $info_completed = 1;

    $sql = "SELECT u.*, s.state_name, c.city_name 
            FROM users u 
            LEFT JOIN states s ON u.state_id = s.state_id 
            LEFT JOIN cities c ON u.city_id = c.city_id 
            WHERE u.info_completed = ?";
    $params = [$info_completed];
    $types = 'i';

    if ($search) {
        $sql .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= 'sss';
    }
    if ($status) {
        $sql .= " AND u.cbn_approved = ?";
        $statusValue = ($status == 'approved') ? 1 : (($status == 'pending') ? 0 : 2);
        $params[] = $statusValue;
        $types .= 'i';
    }
    if ($date) {
        $sql .= " AND DATE(u.created_at) = ?";
        $params[] = $date;
        $types .= 's';
    }
    if ($location) {
        $sql .= " AND u.farm_full_address LIKE ?";
        $locationTerm = "%$location%";
        $params[] = $locationTerm;
        $types .= 's';
    }

    $sql .= " ORDER BY u.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';

    $stmt = $conn->prepare($sql);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $farmers = [];
    while ($row = $result->fetch_assoc()) {
        $farmers[] = $row;
    }

    // Count total for pagination
    $countSql = "SELECT COUNT(*) as total FROM users WHERE info_completed = ?";
    $countParams = [$info_completed];
    $countTypes = 'i';
    if ($search) {
        $countSql .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)";
        $countParams[] = $searchTerm;
        $countParams[] = $searchTerm;
        $countParams[] = $searchTerm;
        $countTypes .= 'sss';
    }
    if ($status) {
        $countSql .= " AND cbn_approved = ?";
        $countParams[] = $statusValue;
        $countTypes .= 'i';
    }
    if ($date) {
        $countSql .= " AND DATE(created_at) = ?";
        $countParams[] = $date;
        $countTypes .= 's';
    }
    if ($location) {
        $countSql .= " AND farm_full_address LIKE ?";
        $countParams[] = $locationTerm;
        $countTypes .= 's';
    }

    $countStmt = $conn->prepare($countSql);
    if ($countTypes) {
        $countStmt->bind_param($countTypes, ...$countParams);
    }
    $countStmt->execute();
    $total = $countStmt->get_result()->fetch_assoc()['total'];

    sendResponse(true, [
        'farmers' => $farmers,
        'total' => $total,
        'pages' => ceil($total / $limit),
        'current_page' => $page
    ]);
}

// Fetch all farmers for export (no pagination)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'export') {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $status = isset($_GET['status']) ? trim($_GET['status']) : '';
    $date = isset($_GET['date']) ? trim($_GET['date']) : '';
    $location = isset($_GET['location']) ? trim($_GET['location']) : '';
    $info_completed = 1;

    $sql = "SELECT u.first_name, u.last_name, u.crops_produced, u.email, u.phone, u.created_at, u.farm_full_address, u.cbn_approved, s.state_name, c.city_name 
            FROM users u 
            LEFT JOIN states s ON u.state_id = s.state_id 
            LEFT JOIN cities c ON u.city_id = c.city_id 
            WHERE u.info_completed = ?";
    $params = [$info_completed];
    $types = 'i';

    if ($search) {
        $sql .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= 'sss';
    }
    if ($status) {
        $sql .= " AND u.cbn_approved = ?";
        $statusValue = ($status == 'approved') ? 1 : (($status == 'pending') ? 0 : 2);
        $params[] = $statusValue;
        $types .= 'i';
    }
    if ($date) {
        $sql .= " AND DATE(u.created_at) = ?";
        $params[] = $date;
        $types .= 's';
    }
    if ($location) {
        $sql .= " AND u.farm_full_address LIKE ?";
        $locationTerm = "%$location%";
        $params[] = $locationTerm;
        $types .= 's';
    }

    $sql .= " ORDER BY u.created_at DESC";
    $stmt = $conn->prepare($sql);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $farmers = [];
    while ($row = $result->fetch_assoc()) {
        $farmers[] = [
            'Name' => $row['first_name'] . ' ' . $row['last_name'],
            'Produce' => $row['crops_produced'] ?? 'N/A',
            'Email' => $row['email'],
            'Phone' => $row['phone'],
            'Registration Date' => date('d-M-Y', strtotime($row['created_at'])),
            'Location' => $row['farm_full_address'] ?? 'N/A',
            'Status' => $row['cbn_approved'] == 1 ? 'Approved' : ($row['cbn_approved'] == 2 ? 'Disabled' : 'Pending'),
            'State' => $row['state_name'] ?? 'N/A',
            'City' => $row['city_name'] ?? 'N/A'
        ];
    }

    sendResponse(true, $farmers);
}

// Get single farmer details with documents
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_details') {
    $user_id = (int)($_GET['id'] ?? 0);
    $info_completed = 1;
    if (!$user_id) {
        sendResponse(false, [], 'Invalid farmer ID');
    }

    // Fetch farmer details
    $stmt = $conn->prepare("SELECT u.*, s.state_name, c.city_name 
                            FROM users u 
                            LEFT JOIN states s ON u.state_id = s.state_id 
                            LEFT JOIN cities c ON u.city_id = c.city_id 
                            WHERE u.user_id = ? AND u.info_completed = ?");
    $stmt->bind_param("ii", $user_id, $info_completed);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($farmer = $result->fetch_assoc()) {
        // Fetch documents
        $stmt = $conn->prepare("SELECT document_id, document_path, document_name 
                                FROM user_documents 
                                WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $documents = [];
        while ($doc = $result->fetch_assoc()) {
            $documents[] = $doc;
        }
        $farmer['documents'] = $documents;
        sendResponse(true, $farmer);
    } else {
        sendResponse(false, [], 'Farmer not found');
    }
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'add':
            $first_name = trim($_POST['first_name'] ?? '');
            $last_name = trim($_POST['last_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $gender = trim($_POST['gender'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $city_id = (int)($_POST['city_id'] ?? 0);
            $state_id = (int)($_POST['state_id'] ?? 0);
            $cac_number = trim($_POST['cac_number'] ?? '') ?: null;
            $nin = trim($_POST['nin'] ?? '') ?: null;
            $farm_name = trim($_POST['farm_name'] ?? '') ?: null;
            $farm_size = trim($_POST['farm_size'] ?? '') ?: null;
            $farm_full_address = trim($_POST['farm_full_address'] ?? '') ?: null;
            $land_ownership_type = trim($_POST['land_ownership_type'] ?? '') ?: null;
            $farming_experience = trim($_POST['farming_experience'] ?? '') ? (int)$_POST['farming_experience'] : null;
            $password = generateRandomPassword();
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $verification_token = bin2hex(random_bytes(32));
            $is_verified = 0;
            $cbn_approved = 0;
            $is_initial_password = 1;
            $profile_picture = null;

            if (!$first_name || !$last_name || !$email || !$phone || !$gender || !$address || !$city_id || !$state_id) {
                sendResponse(false, [], 'All required fields must be filled');
            }

            // Check if email exists
            $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            if ($stmt->get_result()->fetch_assoc()) {
                sendResponse(false, [], 'Email already exists');
            }

            // Handle profile picture upload
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] != UPLOAD_ERR_NO_FILE) {
                $file_result = handleFileUpload($_FILES['profile_picture'], 'temp', 'profile');
                if (is_array($file_result) && isset($file_result['error'])) {
                    sendResponse(false, [], $file_result['error']);
                }
                $profile_picture = $file_result;
            }

            // Insert user
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone, gender, address, city_id, state_id, password, cac_number, nin, farm_name, farm_size, farm_full_address, land_ownership_type, farming_experience, profile_picture, verification_token, is_verified, cbn_approved, is_initial_password, info_completed) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
            $stmt->bind_param("ssssssiiissssssisssii", $first_name, $last_name, $email, $phone, $gender, $address, $city_id, $state_id, $hashed_password, $cac_number, $nin, $farm_name, $farm_size, $farm_full_address, $land_ownership_type, $farming_experience, $profile_picture, $verification_token, $is_verified, $cbn_approved, $is_initial_password);
            if ($stmt->execute()) {
                $user_id = $stmt->insert_id;

                // Rename profile picture with actual user_id - FIXED PATH ISSUE
                if ($profile_picture) {
                    $base_dir = $_SERVER['DOCUMENT_ROOT'] . '/farmerBuyerCon/';
                    $old_path = $base_dir . $profile_picture;
                    $ext = pathinfo($profile_picture, PATHINFO_EXTENSION);
                    $new_filename = 'user_' . $user_id . '_' . time() . '.' . $ext;
                    
                    // Ensure documents directory exists
                    $documents_dir = $base_dir . 'admin/Uploads/documents/';
                    if (!is_dir($documents_dir)) {
                        if (!mkdir($documents_dir, 0755, true)) {
                            error_log("Failed to create documents directory: $documents_dir");
                        }
                    }
                    
                    $new_path = $documents_dir . $new_filename;
                    
                    if (file_exists($old_path) && rename($old_path, $new_path)) {
                        // Store with correct path for frontend access
                        $new_profile_picture = 'admin/Uploads/documents/' . $new_filename;
                        $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE user_id = ?");
                        $stmt->bind_param("si", $new_profile_picture, $user_id);
                        $stmt->execute();
                    } else {
                        // Log error but don't fail the entire operation
                        error_log("Failed to move profile picture from $old_path to $new_path");
                    }
                }

                // Handle document uploads
                $document_types = ['ninDocument', 'cacDocument', 'landDocument'];
                foreach ($document_types as $doc_type) {
                    if (isset($_FILES[$doc_type]) && $_FILES[$doc_type]['error'] != UPLOAD_ERR_NO_FILE) {
                        $file_result = handleFileUpload($_FILES[$doc_type], $user_id, $doc_type);
                        if (is_array($file_result) && isset($file_result['error'])) {
                            sendResponse(false, [], $file_result['error']);
                        }
                        if ($file_result) {
                            $stmt = $conn->prepare("INSERT INTO user_documents (user_id, document_path, document_name) VALUES (?, ?, ?)");
                            $stmt->bind_param("iss", $user_id, $file_result['document_path'], $file_result['document_name']);
                            $stmt->execute();
                        }
                    }
                }

                // Send welcome email
                $email_result = sendWelcomeEmail($email, $first_name, $password);
                if (is_array($email_result) && isset($email_result['error'])) {
                    sendResponse(false, [], $email_result['error']);
                }

                sendResponse(true, ['user_id' => $user_id]);
            } else {
                sendResponse(false, [], $stmt->error);
            }
            break;

        case 'edit':
            $user_id = (int)($_POST['user_id'] ?? 0);
            $first_name = trim($_POST['first_name'] ?? '');
            $last_name = trim($_POST['last_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $gender = trim($_POST['gender'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $city_id = (int)($_POST['city_id'] ?? 0);
            $state_id = (int)($_POST['state_id'] ?? 0);
            $cac_number = trim($_POST['cac_number'] ?? '') ?: null;
            $nin = trim($_POST['nin'] ?? '') ?: null;
            $farm_name = trim($_POST['farm_name'] ?? '') ?: null;
            $farm_size = trim($_POST['farm_size'] ?? '') ?: null;
            $farm_full_address = trim($_POST['farm_full_address'] ?? '') ?: null;
            $land_ownership_type = trim($_POST['land_ownership_type'] ?? '') ?: null;
            $farming_experience = trim($_POST['farming_experience'] ?? '') ? (int)$_POST['farming_experience'] : null;

            if (!$user_id || !$first_name || !$last_name || !$email || !$phone || !$gender || !$address || !$city_id || !$state_id) {
                sendResponse(false, [], 'Required fields are missing');
            }

            // Check if email exists for another user
            $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
            $stmt->bind_param("si", $email, $user_id);
            $stmt->execute();
            if ($stmt->get_result()->fetch_assoc()) {
                sendResponse(false, [], 'Email already in use by another user');
            }

            // Handle profile picture
            $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $current_picture = $result->fetch_assoc()['profile_picture'];
            $profile_picture = $current_picture;

            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] != UPLOAD_ERR_NO_FILE) {
                $file_result = handleFileUpload($_FILES['profile_picture'], $user_id, 'profile');
                if (is_array($file_result) && isset($file_result['error'])) {
                    sendResponse(false, [], $file_result['error']);
                }
                $profile_picture = $file_result;
                // Delete old profile picture if it exists
                if ($current_picture && file_exists($_SERVER['DOCUMENT_ROOT'] . '/farmerBuyerCon/' . $current_picture)) {
                    unlink($_SERVER['DOCUMENT_ROOT'] . '/farmerBuyerCon/' . $current_picture);
                }
            }

            // Handle document uploads
            $document_types = ['ninDocument', 'cacDocument', 'landDocument'];
            foreach ($document_types as $doc_type) {
                if (isset($_FILES[$doc_type]) && $_FILES[$doc_type]['error'] != UPLOAD_ERR_NO_FILE) {
                    $file_result = handleFileUpload($_FILES[$doc_type], $user_id, $doc_type);
                    if (is_array($file_result) && isset($file_result['error'])) {
                        sendResponse(false, [], $file_result['error']);
                    }
                    if ($file_result) {
                        // Delete existing document of the same type
                        $stmt = $conn->prepare("SELECT document_path FROM user_documents WHERE user_id = ? AND document_path LIKE ?");
                        $like_pattern = "%$doc_type%";
                        $stmt->bind_param("is", $user_id, $like_pattern);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                            $old_file_path = $_SERVER['DOCUMENT_ROOT'] . '/farmerBuyerCon/' . $row['document_path'];
                            if (file_exists($old_file_path)) {
                                unlink($old_file_path);
                            }
                        }
                        $stmt = $conn->prepare("DELETE FROM user_documents WHERE user_id = ? AND document_path LIKE ?");
                        $stmt->bind_param("is", $user_id, $like_pattern);
                        $stmt->execute();

                        // Insert new document
                        $stmt = $conn->prepare("INSERT INTO user_documents (user_id, document_path, document_name) VALUES (?, ?, ?)");
                        $stmt->bind_param("iss", $user_id, $file_result['document_path'], $file_result['document_name']);
                        $stmt->execute();
                    }
                }
            }

            // Update user
            $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, gender = ?, address = ?, city_id = ?, state_id = ?, cac_number = ?, nin = ?, farm_name = ?, farm_size = ?, farm_full_address = ?, land_ownership_type = ?, farming_experience = ?, profile_picture = ? WHERE user_id = ?");
            $stmt->bind_param("ssssssiiisssssisi", $first_name, $last_name, $email, $phone, $gender, $address, $city_id, $state_id, $cac_number, $nin, $farm_name, $farm_size, $farm_full_address, $land_ownership_type, $farming_experience, $profile_picture, $user_id);
            if ($stmt->execute()) {
                sendResponse(true);
            } else {
                sendResponse(false, [], $stmt->error);
            }
            break;

        case 'approve':
            $user_id = (int)($_POST['id'] ?? 0);
            if (!$user_id) {
                sendResponse(false, [], 'Invalid farmer ID');
            }
            $stmt = $conn->prepare("UPDATE users SET cbn_approved = 1, rejection_reason = NULL WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            if ($stmt->execute()) {
                sendResponse(true);
            } else {
                sendResponse(false, [], $stmt->error);
            }
            break;

        case 'disable':
            $user_id = (int)($_POST['id'] ?? 0);
            $rejection_reason = trim($_POST['rejection_reason'] ?? '');
            if (!$user_id) {
                sendResponse(false, [], 'Invalid farmer ID');
            }
            if (!$rejection_reason) {
                sendResponse(false, [], 'Rejection reason is required');
            }
            $stmt = $conn->prepare("UPDATE users SET cbn_approved = 2, rejection_reason = ? WHERE user_id = ?");
            $stmt->bind_param("si", $rejection_reason, $user_id);
            if ($stmt->execute()) {
                sendResponse(true);
            } else {
                sendResponse(false, [], $stmt->error);
            }
            break;
    }
}

http_response_code(400);
sendResponse(false, [], 'Invalid request');
?>