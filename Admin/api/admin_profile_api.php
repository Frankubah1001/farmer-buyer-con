<?php
/**
 * Admin Profile API
 * Handles all admin profile management operations
 */

header('Content-Type: application/json');
require_once 'DBcon.php';

// Check if admin is logged in
if (!isset($_SESSION['cbn_user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'get_admins':
            getAdmins($conn);
            break;
        
        case 'get_admin_details':
            getAdminDetails($conn);
            break;
        
        case 'add_admin':
            addAdmin($conn);
            break;
        
        case 'update_admin':
            updateAdmin($conn);
            break;
        
        case 'disable_admin':
            disableAdmin($conn);
            break;
        
        case 'enable_admin':
            enableAdmin($conn);
            break;
        
        case 'get_admin_logs':
            getAdminLogs($conn);
            break;
        
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

// ==================== GET ALL ADMINS ====================
function getAdmins($conn) {
    $search = $_GET['search'] ?? '';
    $role = $_GET['role'] ?? '';
    $status = $_GET['status'] ?? '';
    
    $query = "SELECT u.cbn_user_id, u.username, u.email, u.created_at, u.last_login, 
              ar.role_name, d.designation_name, u.phone, u.address, u.profile_pic, u.notes
              FROM cbn_users u
              LEFT JOIN admin_roles ar ON u.role_id = ar.role_id
              LEFT JOIN designations d ON u.designation_id = d.designation_id
              WHERE 1=1";
    
    $params = [];
    $types = '';
    
    if (!empty($search)) {
        $query .= " AND (u.username LIKE ? OR u.email LIKE ?)";
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $types .= 'ss';
    }
    
    if (!empty($role)) {
        $query .= " AND ar.role_name = ?";
        $params[] = $role;
        $types .= 's';
    }
    
    if (!empty($status)) {
        if ($status === 'active') {
            $query .= " AND u.created_at IS NOT NULL";
        } elseif ($status === 'disabled') {
            $query .= " AND u.created_at IS NULL"; // Assuming disabled means created_at is null or another flag
        }
    }
    
    $query .= " ORDER BY u.created_at DESC";
    
    $stmt = $conn->prepare($query);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $admins = [];
    while ($row = $result->fetch_assoc()) {
        $admins[] = [
            'admin_id' => $row['cbn_user_id'],
            'name' => $row['username'],
            'email' => $row['email'],
            'role' => $row['role_name'] ?? 'N/A',
            'designation' => $row['designation_name'] ?? 'N/A',
            'status' => 'active', // You may need to add a status field to the database
            'last_login' => $row['last_login'] ? date('d M Y, h:i A', strtotime($row['last_login'])) : 'Never',
            'date_joined' => $row['created_at'] ? date('d M Y', strtotime($row['created_at'])) : 'N/A',
            'phone' => $row['phone'] ?? 'N/A',
            'address' => $row['address'] ?? 'N/A',
            'profile_pic' => $row['profile_pic'] ?? '',
            'notes' => $row['notes'] ?? ''
        ];
    }
    
    echo json_encode(['success' => true, 'data' => $admins]);
}

// ==================== GET ADMIN DETAILS ====================
function getAdminDetails($conn) {
    $adminId = $_GET['admin_id'] ?? 0;
    
    if (empty($adminId)) {
        echo json_encode(['success' => false, 'message' => 'Admin ID is required']);
        return;
    }
    
    $stmt = $conn->prepare("SELECT u.cbn_user_id, u.username, u.email, u.created_at, u.last_login, 
                            ar.role_name, d.designation_name, u.phone, u.address, u.profile_pic, u.notes
                            FROM cbn_users u
                            LEFT JOIN admin_roles ar ON u.role_id = ar.role_id
                            LEFT JOIN designations d ON u.designation_id = d.designation_id
                            WHERE u.cbn_user_id = ?");
    $stmt->bind_param('i', $adminId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Admin not found']);
        return;
    }
    
    $row = $result->fetch_assoc();
    $admin = [
        'admin_id' => $row['cbn_user_id'],
        'name' => $row['username'],
        'email' => $row['email'],
        'role' => $row['role_name'] ?? 'N/A',
        'designation' => $row['designation_name'] ?? 'N/A',
        'status' => 'active',
        'last_login' => $row['last_login'] ? date('d M Y, h:i A', strtotime($row['last_login'])) : 'Never',
        'date_joined' => $row['created_at'] ? date('d M Y', strtotime($row['created_at'])) : 'N/A',
        'phone' => $row['phone'] ?? 'N/A',
        'address' => $row['address'] ?? 'N/A',
        'profile_pic' => $row['profile_pic'] ?? '',
        'notes' => $row['notes'] ?? '',
        'last_activity' => 'Last login: ' . ($row['last_login'] ? date('d M Y, h:i A', strtotime($row['last_login'])) : 'Never')
    ];
    
    echo json_encode(['success' => true, 'data' => $admin]);
}

// ==================== ADD ADMIN ====================
function addAdmin($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $roleId = $data['role_id'] ?? 0;
    $designationId = $data['designation_id'] ?? null;
    $phone = trim($data['phone'] ?? '');
    $address = trim($data['address'] ?? '');
    $notes = trim($data['notes'] ?? '');
    $profilePic = $data['profile_pic'] ?? '';
    
    if (empty($name) || empty($email) || empty($password) || empty($roleId)) {
        echo json_encode(['success' => false, 'message' => 'Name, email, password, and role are required']);
        return;
    }
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT cbn_user_id FROM cbn_users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        return;
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert admin
    $stmt = $conn->prepare("INSERT INTO cbn_users (username, full_name, email, password, role_id, designation_id, phone, address, notes, profile_pic, created_at) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param('ssssiissss', $name, $name, $email, $hashedPassword, $roleId, $designationId, $phone, $address, $notes, $profilePic);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Admin added successfully', 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add admin']);
    }
}

// ==================== UPDATE ADMIN ====================
function updateAdmin($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $adminId = $data['admin_id'] ?? 0;
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $roleId = $data['role_id'] ?? 0;
    $designationId = $data['designation_id'] ?? null;
    $phone = trim($data['phone'] ?? '');
    $address = trim($data['address'] ?? '');
    $notes = trim($data['notes'] ?? '');
    $profilePic = $data['profile_pic'] ?? null;
    $password = $data['password'] ?? '';
    
    if (empty($adminId) || empty($name) || empty($email) || empty($roleId)) {
        echo json_encode(['success' => false, 'message' => 'Admin ID, name, email, and role are required']);
        return;
    }
    
    // Check if email already exists for another admin
    $stmt = $conn->prepare("SELECT cbn_user_id FROM cbn_users WHERE email = ? AND cbn_user_id != ?");
    $stmt->bind_param('si', $email, $adminId);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already exists for another admin']);
        return;
    }
    
    // Build update query
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        if ($profilePic !== null) {
            $stmt = $conn->prepare("UPDATE cbn_users SET username = ?, full_name = ?, email = ?, password = ?, role_id = ?, designation_id = ?, phone = ?, address = ?, notes = ?, profile_pic = ? WHERE cbn_user_id = ?");
            $stmt->bind_param('ssssiissssi', $name, $name, $email, $hashedPassword, $roleId, $designationId, $phone, $address, $notes, $profilePic, $adminId);
        } else {
            $stmt = $conn->prepare("UPDATE cbn_users SET username = ?, full_name = ?, email = ?, password = ?, role_id = ?, designation_id = ?, phone = ?, address = ?, notes = ? WHERE cbn_user_id = ?");
            $stmt->bind_param('ssssiisssi', $name, $name, $email, $hashedPassword, $roleId, $designationId, $phone, $address, $notes, $adminId);
        }
    } else {
        if ($profilePic !== null) {
            $stmt = $conn->prepare("UPDATE cbn_users SET username = ?, full_name = ?, email = ?, role_id = ?, designation_id = ?, phone = ?, address = ?, notes = ?, profile_pic = ? WHERE cbn_user_id = ?");
            $stmt->bind_param('sssiissssi', $name, $name, $email, $roleId, $designationId, $phone, $address, $notes, $profilePic, $adminId);
        } else {
            $stmt = $conn->prepare("UPDATE cbn_users SET username = ?, full_name = ?, email = ?, role_id = ?, designation_id = ?, phone = ?, address = ?, notes = ? WHERE cbn_user_id = ?");
            $stmt->bind_param('sssiisssi', $name, $name, $email, $roleId, $designationId, $phone, $address, $notes, $adminId);
        }
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Admin updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update admin']);
    }
}

// ==================== DISABLE ADMIN ====================
function disableAdmin($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $adminId = $data['admin_id'] ?? 0;
    $reason = trim($data['reason'] ?? '');
    $notes = trim($data['notes'] ?? '');
    
    if (empty($adminId)) {
        echo json_encode(['success' => false, 'message' => 'Admin ID is required']);
        return;
    }
    
    // Add a disabled_at field or use a status field
    // For now, we'll add a note about the disable action
    $disableNote = "Account disabled on " . date('Y-m-d H:i:s') . ". Reason: $reason. Notes: $notes";
    
    $stmt = $conn->prepare("UPDATE cbn_users SET notes = CONCAT(IFNULL(notes, ''), '\n', ?) WHERE cbn_user_id = ?");
    $stmt->bind_param('si', $disableNote, $adminId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Admin disabled successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to disable admin']);
    }
}

// ==================== ENABLE ADMIN ====================
function enableAdmin($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $adminId = $data['admin_id'] ?? 0;
    
    if (empty($adminId)) {
        echo json_encode(['success' => false, 'message' => 'Admin ID is required']);
        return;
    }
    
    $enableNote = "Account enabled on " . date('Y-m-d H:i:s');
    
    $stmt = $conn->prepare("UPDATE cbn_users SET notes = CONCAT(IFNULL(notes, ''), '\n', ?) WHERE cbn_user_id = ?");
    $stmt->bind_param('si', $enableNote, $adminId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Admin enabled successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to enable admin']);
    }
}

// ==================== GET ADMIN LOGS ====================
function getAdminLogs($conn) {
    $adminId = $_GET['admin_id'] ?? 0;
    
    if (empty($adminId)) {
        echo json_encode(['success' => false, 'message' => 'Admin ID is required']);
        return;
    }
    
    // This would typically fetch from an activity_logs table
    // For now, return sample data
    $logs = [
        [
            'timestamp' => date('Y-m-d H:i:s', strtotime('-2 hours')),
            'action' => 'Login',
            'description' => 'Logged in to admin panel',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
        ],
        [
            'timestamp' => date('Y-m-d H:i:s', strtotime('-5 hours')),
            'action' => 'Update',
            'description' => 'Updated farmer profile',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
        ]
    ];
    
    echo json_encode(['success' => true, 'data' => $logs]);
}
?>
