<?php
include 'DBcon.php';
require_once __DIR__ . '/../../load_env.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['valid' => false, 'message' => 'Invalid request method']);
    exit;
}

$nin = trim($_POST['nin'] ?? '');
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');

// Basic Format Validation
if (!preg_match('/^\d{11}$/', $nin)) {
    echo json_encode(['valid' => false, 'message' => 'Invalid NIN format. Must be 11 digits.']);
    exit;
}

// 1. Local Database Check (Uniqueness)
$stmt = $conn->prepare("SELECT user_id FROM users WHERE nin = ?");
if ($stmt) {
    $stmt->bind_param("s", $nin);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo json_encode(['valid' => false, 'message' => 'This NIN is already registered.']);
        $stmt->close();
        exit;
    }
    $stmt->close();
}

// 2. External API Verification
// Using a generic structure compatible with providers like Dojah, IdentityPass, etc.
// Configure these in your .env file
$api_key = $_ENV['NIN_API_KEY'] ?? ''; 
$api_url = $_ENV['NIN_API_URL'] ?? 'https://api.dojah.io/api/v1/kyc/nin'; // Default to Dojah structure
$app_id  = $_ENV['NIN_APP_ID'] ?? '';

// MOCK MODE: If no API key is provided, simulate success for testing
if (empty($api_key)) {
    // In a real production environment, you should enforce API usage.
    // For now, we return valid to allow testing without keys.
    echo json_encode(['valid' => true, 'message' => 'NIN Verified (Mock Mode)']);
    exit;
}

// Prepare API Request
$ch = curl_init();
// Note: Different providers pass NIN differently (query param vs body). 
// This example assumes Dojah style: GET /api/v1/kyc/nin?nin=...
curl_setopt($ch, CURLOPT_URL, "$api_url?nin=$nin");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: $api_key",
    "AppId: $app_id",
    "Content-Type: application/json",
    "Accept: application/json"
]);

$response = curl_exec($ch);
$err = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($err) {
    echo json_encode(['valid' => false, 'message' => 'Verification service error: ' . $err]);
    exit;
}

$data = json_decode($response, true);

// 3. Process API Response
// Adjust this logic based on your specific provider's response structure.
// Dojah Example: { "entity": { "first_name": "John", "last_name": "Doe" }, ... }
if ($http_code === 200 && isset($data['entity'])) {
    $api_first_name = $data['entity']['first_name'] ?? '';
    $api_last_name  = $data['entity']['last_name'] ?? '';
    
    // Normalize names for comparison
    $input_fn = strtolower($first_name);
    $input_ln = strtolower($last_name);
    $api_fn   = strtolower($api_first_name);
    $api_ln   = strtolower($api_last_name);

    // Check for match (allowing for some flexibility, e.g., contains)
    // Strict check:
    if ($input_fn === $api_fn && $input_ln === $api_ln) {
        echo json_encode(['valid' => true, 'message' => 'NIN Verified Successfully']);
    } else {
        // Fuzzy check or partial match could be implemented here
        echo json_encode([
            'valid' => false, 
            'message' => "Name mismatch. You entered: $first_name $last_name. NIN Record: $api_first_name $api_last_name"
        ]);
    }
} else {
    // Handle API errors (e.g., NIN not found)
    $error_msg = $data['error'] ?? 'NIN not found or invalid.';
    echo json_encode(['valid' => false, 'message' => $error_msg]);
}
?>
