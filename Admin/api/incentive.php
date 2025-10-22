<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// api/incentives_api.php - RESTful API for Incentives Management (Loans, Tools, Grants)
header('Content-Type: application/json');
session_start();
require_once 'DBcon.php'; 

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
    // Ensure the database connection is closed safely
    global $conn;
    if (isset($conn)) {
        close_db_connection($conn);
    }
    exit();
}

// Helper function to check required fields
function checkRequiredFields($fields, $post) {
    foreach ($fields as $field) {
        if (!isset($post[$field]) || trim($post[$field]) === '') {
            return "Missing required field: {$field}";
        }
    }
    return true; // All fields present
}

// --- GET REQUEST: FETCH ALL INCENTIVES DATA ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    $results = [];

    // 1. Fetch Loan Companies
    $sql_loans = "SELECT company_id, company_name, interest_rate, created_at, status 
                  FROM loan_companies 
                  ORDER BY created_at DESC";
    $loan_stmt = $conn->prepare($sql_loans);
    $loan_stmt->execute();
    $results['loan_companies'] = $loan_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $loan_stmt->close();
    
    // 2. Fetch Farm Tools
    $sql_tools = "SELECT tool_id, tool_name, description, created_at, status 
                  FROM farm_tools 
                  ORDER BY created_at DESC";
    $tool_stmt = $conn->prepare($sql_tools);
    $tool_stmt->execute();
    $results['farm_tools'] = $tool_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $tool_stmt->close();
    
    // 3. Fetch Grant Providers
    $sql_grants = "SELECT grant_id, provider_name, grant_amount, created_at, status 
                   FROM grant_providers 
                   ORDER BY created_at DESC";
    $grant_stmt = $conn->prepare($sql_grants);
    $grant_stmt->execute();
    $results['grant_providers'] = $grant_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $grant_stmt->close();

    sendResponse(true, $results);
}


// --- GET REQUEST: FETCH SINGLE ITEM DETAILS (FOR EDIT MODALS) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_details') {
    $type = $_GET['type'] ?? '';
    $id = (int)($_GET['id'] ?? 0);

    if (!$type || !$id) {
        sendResponse(false, [], 'Missing type or ID.');
    }

    $table = '';
    $id_column = '';
    
    switch ($type) {
        case 'loan':
            $table = 'loan_companies';
            $id_column = 'company_id';
            break;
        case 'tool':
            $table = 'farm_tools';
            $id_column = 'tool_id';
            break;
        case 'grant':
            $table = 'grant_providers';
            $id_column = 'grant_id';
            break;
        default:
            sendResponse(false, [], 'Invalid incentive type.');
    }

    $sql = "SELECT * FROM {$table} WHERE {$id_column} = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();

    if ($data) {
        sendResponse(true, $data);
    } else {
        sendResponse(false, [], ucfirst($type) . ' not found.');
    }
}


// --- POST REQUEST: ADD, EDIT, TOGGLE STATUS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $type = $_POST['type'] ?? '';

    if (empty($action) || empty($type)) {
        sendResponse(false, [], 'Missing action or type parameter.');
    }

    switch ($type) {
        // --- LOAN COMPANIES LOGIC ---
        case 'loan':
            $required_add = ['company_name', 'interest_rate', 'terms', 'contact_details'];
            $required_edit = array_merge($required_add, ['company_id']);

            if ($action === 'add') {
                $check = checkRequiredFields($required_add, $_POST);
                if ($check !== true) { sendResponse(false, [], $check); }

                $stmt = $conn->prepare("INSERT INTO loan_companies (company_name, interest_rate, terms, contact_details) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sdss", $_POST['company_name'], $_POST['interest_rate'], $_POST['terms'], $_POST['contact_details']);

            } elseif ($action === 'edit') {
                $check = checkRequiredFields($required_edit, $_POST);
                if ($check !== true) { sendResponse(false, [], $check); }

                $stmt = $conn->prepare("UPDATE loan_companies SET company_name=?, interest_rate=?, terms=?, contact_details=? WHERE company_id=?");
                $stmt->bind_param("sdssi", $_POST['company_name'], $_POST['interest_rate'], $_POST['terms'], $_POST['contact_details'], $_POST['company_id']);
                
            } elseif ($action === 'toggle_status') {
                $id = (int)($_POST['id'] ?? 0);
                $new_status = $_POST['status'] === 'Active' ? 'Disabled' : 'Active';
                
                $stmt = $conn->prepare("UPDATE loan_companies SET status=? WHERE company_id=?");
                $stmt->bind_param("si", $new_status, $id);
            }
            break;

        // --- FARM TOOLS LOGIC ---
        case 'tool':
            $required_add = ['tool_name', 'description'];
            $required_edit = array_merge($required_add, ['tool_id']);

            if ($action === 'add') {
                $check = checkRequiredFields($required_add, $_POST);
                if ($check !== true) { sendResponse(false, [], $check); }

                $stmt = $conn->prepare("INSERT INTO farm_tools (tool_name, description) VALUES (?, ?)");
                $stmt->bind_param("ss", $_POST['tool_name'], $_POST['description']);
                
            } elseif ($action === 'edit') {
                $check = checkRequiredFields($required_edit, $_POST);
                if ($check !== true) { sendResponse(false, [], $check); }

                $stmt = $conn->prepare("UPDATE farm_tools SET tool_name=?, description=? WHERE tool_id=?");
                $stmt->bind_param("ssi", $_POST['tool_name'], $_POST['description'], $_POST['tool_id']);
                
            } elseif ($action === 'toggle_status') {
                $id = (int)($_POST['id'] ?? 0);
                // Note: Farm tools use 'Available'/'Deleted'
                $new_status = $_POST['status'] === 'Available' ? 'Deleted' : 'Available'; 
                
                $stmt = $conn->prepare("UPDATE farm_tools SET status=? WHERE tool_id=?");
                $stmt->bind_param("si", $new_status, $id);
            }
            break;

        // --- GRANT PROVIDERS LOGIC ---
        case 'grant':
            $required_add = ['provider_name', 'grant_amount', 'terms', 'contact_details'];
            $required_edit = array_merge($required_add, ['grant_id']);

            if ($action === 'add') {
                $check = checkRequiredFields($required_add, $_POST);
                if ($check !== true) { sendResponse(false, [], $check); }

                $stmt = $conn->prepare("INSERT INTO grant_providers (provider_name, grant_amount, terms, contact_details) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $_POST['provider_name'], $_POST['grant_amount'], $_POST['terms'], $_POST['contact_details']);
                
            } elseif ($action === 'edit') {
                $check = checkRequiredFields($required_edit, $_POST);
                if ($check !== true) { sendResponse(false, [], $check); }

                $stmt = $conn->prepare("UPDATE grant_providers SET provider_name=?, grant_amount=?, terms=?, contact_details=? WHERE grant_id=?");
                $stmt->bind_param("ssssi", $_POST['provider_name'], $_POST['grant_amount'], $_POST['terms'], $_POST['contact_details'], $_POST['grant_id']);
                
            } elseif ($action === 'toggle_status') {
                $id = (int)($_POST['id'] ?? 0);
                $new_status = $_POST['status'] === 'Active' ? 'Disabled' : 'Active';
                
                $stmt = $conn->prepare("UPDATE grant_providers SET status=? WHERE grant_id=?");
                $stmt->bind_param("si", $new_status, $id);
            }
            break;
            
        default:
            sendResponse(false, [], 'Invalid incentive type.');
    }

    // Execute the prepared statement if it exists
    if (isset($stmt)) {
        if ($stmt->execute()) {
            $message = ucfirst($type) . ' ' . ucfirst($action) . ' successfully.';
            sendResponse(true, [], $message);
        } else {
            sendResponse(false, [], 'Database error: ' . $stmt->error);
        }
        $stmt->close();
    } else {
        // This is a safeguard if the action/type combination didn't set a statement
        sendResponse(false, [], 'Invalid action or missing ID.');
    }
}

// Default response if no valid action is matched
sendResponse(false, [], 'No valid API action provided.');
?>
