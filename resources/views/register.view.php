<?php
include 'DBcon.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../load_env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

/* -------------------------------------------------
   FILE UPLOAD HELPER
   ------------------------------------------------- */
function handleFileUpload($file, $user_id, $type = 'profile')
{
    if (!isset($file) || $file['error'] == UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $allowed = ($type === 'profile')
        ? ['image/jpeg', 'image/png']
        : ['image/jpeg', 'image/png', 'application/pdf'];
    $max = ($type === 'profile') ? 2 * 1024 * 1024 : 5 * 1024 * 1024;

    if (!in_array($file['type'], $allowed)) {
        return ['error' => 'Invalid file type.'];
    }
    if ($file['size'] > $max) {
        return ['error' => 'File too large.'];
    }

    $dir = __DIR__ . "/../../uploads/farmers_docs/{$user_id}/";
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $ext  = pathinfo($file['name'], PATHINFO_EXTENSION);
    $name = $type . '_' . time() . '.' . $ext;
    $path = $dir . $name;

    if (move_uploaded_file($file['tmp_name'], $path)) {
        return "uploads/farmers_docs/{$user_id}/" . $name;
    }
    return ['error' => 'Upload failed.'];
}

/* -------------------------------------------------
   REQUEST CHECK
   ------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo '<div class="alert alert-danger">Invalid request.</div>';
    exit;
}

/* -------------------------------------------------
   COLLECT INPUT
   ------------------------------------------------- */
$first_name         = trim($_POST['first_name'] ?? '');
$last_name          = trim($_POST['last_name'] ?? '');
$email              = trim($_POST['email'] ?? '');
$phone              = trim($_POST['phone'] ?? '');
$gender             = $_POST['gender'] ?? '';
$address            = trim($_POST['address'] ?? '');
$state_id           = $_POST['state_id'] ?? '';
$city_id            = $_POST['city_id'] ?? '';
$password           = $_POST['password'] ?? '';
$confirm_password   = $_POST['repeat_password'] ?? '';
$nin                = trim($_POST['nin'] ?? '');
$cac_number         = trim($_POST['cac_number'] ?? '');
$farm_name          = trim($_POST['farm_name'] ?? '');
$farm_size          = !empty($_POST['farm_size']) ? (float)$_POST['farm_size'] : null;
$farm_full_address  = trim($_POST['farm_full_address'] ?? '');
$land_ownership     = $_POST['land_ownership_type'] ?? null;
$farming_experience = !empty($_POST['farming_experience']) ? (int)$_POST['farming_experience'] : null;

/* Combine farmer_type + primary_produce → crops_produced */
$crops_produced = '';
if (!empty($_POST['farmer_type'])) {
    $crops_produced = $_POST['farmer_type'];
    if (!empty($_POST['primary_produce'])) {
        $crops_produced .= ': ' . $_POST['primary_produce'];
    }
}

/* -------------------------------------------------
   VALIDATION
   ------------------------------------------------- */
$required = [
    'first_name','last_name','email','phone','gender',
    'address','state_id','city_id','password','confirm_password','nin'
];
$errors = [];
foreach ($required as $f) {
    if (empty($$f)) {
        $errors[] = "All required fields must be filled.";
    }
}
if ($password !== $confirm_password) {
    $errors[] = "Passwords do not match.";
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email.";
}
if (!in_array($gender, ['Male','Female'])) {
    $errors[] = "Invalid gender.";
}

// Email uniqueness
if (empty($errors)) {
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Email already registered.";
    }
    $stmt->close();
}

// Required files
if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] === UPLOAD_ERR_NO_FILE) {
    $errors[] = "Profile picture is required.";
}
if (!isset($_FILES['national_id_doc']) || $_FILES['national_id_doc']['error'] === UPLOAD_ERR_NO_FILE) {
    $errors[] = "NIN document is required.";
}

if (!empty($errors)) {
    echo '<div class="alert alert-danger">' . implode('<br>', $errors) . '</div>';
    exit;
}

/* -------------------------------------------------
   UPLOAD FILES (temp)
   ------------------------------------------------- */
$temp_id     = md5($email . microtime());
$profile_pic = handleFileUpload($_FILES['profile_picture'], $temp_id, 'profile');
$nin_doc     = handleFileUpload($_FILES['national_id_doc'], $temp_id, 'national_id');

if (is_array($profile_pic)) {
    echo '<div class="alert alert-danger">Profile: ' . $profile_pic['error'] . '</div>';
    exit;
}
if (is_array($nin_doc)) {
    echo '<div class="alert alert-danger">NIN Doc: ' . $nin_doc['error'] . '</div>';
    exit;
}

/* -------------------------------------------------
   INSERT INTO DB – 19 FIELDS
   ------------------------------------------------- */
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$token           = bin2hex(random_bytes(32));

$sql = "INSERT INTO users (
    first_name, last_name, email, phone, gender, address, city_id, state_id,
    password, profile_picture, nin, cac_number,
    farm_name, farm_size, farm_full_address,
    land_ownership_type, crops_produced, farming_experience,
    verification_token, is_verified, cbn_approved, info_completed, created_at
) VALUES (
    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, 1, NOW()
)";

$type_string = "sssssssssssssdsssis";   // 19 types

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param(
    $stmt, $type_string,
    $first_name, $last_name, $email, $phone, $gender,
    $address, $city_id, $state_id,
    $hashed_password, $profile_pic, $nin, $cac_number,
    $farm_name, $farm_size, $farm_full_address,
    $land_ownership, $crops_produced, $farming_experience,
    $token
);

if (!mysqli_stmt_execute($stmt)) {
    echo '<div class="alert alert-danger">DB Error: ' . $stmt->error . '</div>';
    error_log("Insert failed: " . $stmt->error);
    mysqli_stmt_close($stmt);
    exit;
}

$user_id = mysqli_insert_id($conn);

// Move files to final folder
$old = __DIR__ . "/../../uploads/farmers_docs/{$temp_id}";
$new = __DIR__ . "/../../uploads/farmers_docs/{$user_id}";
if (is_dir($old)) {
    rename($old, $new);
}

/* -------------------------------------------------
   SUCCESS + EMAIL
   ------------------------------------------------- */
echo '<div class="alert alert-success">
    Registration successful! Please check your email to verify.
</div>';

/* ----- Build activation URL ----- */
$activation_link = (getenv('APP_URL') ?: 'http://localhost/farmerBuyerCon/resources') . "/activate.php?token={$token}";

/* ----- PHPMailer ----- */
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['SMTP_USER'] ?? '';
    $mail->Password   = $_ENV['SMTP_PASS'] ?? '';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    if (empty($mail->Username) || empty($mail->Password)) {
        throw new Exception('SMTP credentials missing in .env');
    }

    $mail->setFrom($mail->Username, 'FarmerBuyerCon');
    $mail->addAddress($email, $first_name);   // <-- $first_name is defined
    $mail->isHTML(true);
    $mail->Subject = 'Activate your FarmerBuyerCon account';

    $mail->Body = '
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Activate Your FarmerBuyerCon Account</title>
  <style>
    body {margin:0;padding:0;background:#f4f7f9;font-family:"Segoe UI",Tahoma,Geneva,Verdana,sans-serif;}
    .container {max-width:600px;margin:30px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 8px 25px rgba(0,0,0,.08);}
    .header {background:linear-gradient(135deg,#2f855a,#38a169);color:#fff;padding:30px 20px;text-align:center;}
    .header h1 {margin:0;font-size:28px;font-weight:600;}
    .content {padding:35px 40px;color:#333;line-height:1.7;}
    .content p {margin:0 0 16px;font-size:16px;}
    .highlight {color:#2f855a;font-weight:600;}
    .btn {display:inline-block;background:#38a169;color:#fff !important;text-decoration:none;
          font-weight:600;font-size:17px;padding:14px 32px;border-radius:8px;margin:20px 0;
          box-shadow:0 4px 12px rgba(56,161,105,.3);transition:all .3s ease;}
    .btn:hover {background:#2f855a;transform:translateY(-2px);box-shadow:0 6px 16px rgba(56,161,105,.4);}
    .footer {background:#f8f9fa;padding:25px;text-align:center;font-size:13px;color:#6c757d;border-top:1px solid #e9ecef;}
    .footer a {color:#38a169;text-decoration:none;font-weight:500;}
    .footer a:hover {text-decoration:underline;}
    @media (max-width:600px){.content{padding:25px 20px;}.header h1{font-size:24px;}}
  </style>
</head>
<body>
  <div class="container">
    <div class="header"><h1>FarmerBuyerCon</h1></div>
    <div class="content">
      <p>Hi <span class="highlight">' . htmlspecialchars($first_name, ENT_QUOTES, 'UTF-8') . '</span>,</p>
      <p>Welcome to <strong>FarmerBuyerCon</strong> – the trusted marketplace connecting farmers and buyers!</p>
      <p>You’ve successfully registered as a <span class="highlight">Farmer</span>. To start exploring fresh produce, placing orders, and connecting with farmers, please <strong>activate your account</strong> by clicking the button below:</p>
      <div style="text-align:center;">
        <a href="' . htmlspecialchars($activation_link, ENT_QUOTES, 'UTF-8') . '" class="btn">Activate My Account</a>
      </div>
      <p><strong>Why activate?</strong></p>
      <ul style="margin:16px 0;padding-left:20px;">
        <li>Access your personalized buyer dashboard</li>
        <li>Browse real-time farm listings</li>
        <li>Place secure orders with confidence</li>
        <li>Get notified about new harvests</li>
      </ul>
      <p><em>This link expires in 24 hours for your security.</em></p>
      <p>If you didn’t create this account, please ignore this email – no action is needed.</p>
    </div>
    <div class="footer">
      <p><strong>FarmerBuyerCon</strong> – Fresh from Farm to You</p>
      <p>Need help? <a href="mailto:support@farmerbuyercon.com">Contact Support</a> • 
         <a href="http://localhost/farmerBuyerCon/privacy.php">Privacy Policy</a></p>
      <p>&copy; ' . date('Y') . ' FarmerBuyerCon. All rights reserved.</p>
    </div>
  </div>
</body>
</html>
';

    $mail->send();
} catch (Exception $e) {
    error_log("PHPMailer error: " . $mail->ErrorInfo);
    // Registration already succeeded – email failure does **not** block the user
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>