<?php
/**
 * Permissions API
 * Handles fetching permissions for admin management
 */

header('Content-Type: application/json');
require_once 'DBcon.php';

// Check if admin is logged in
if (!isset($_SESSION['cbn_user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_all_permissions':
            getAllPermissions($conn);
            break;
        
        case 'get_user_permissions':
            getUserPermissions($conn);
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

// Get all permissions
function getAllPermissions($conn) {
    $stmt = $conn->prepare("
        SELECT permission_id, permission_name, permission_label, module, description
        FROM permissions
        WHERE is_active = 1
        ORDER BY module, permission_label
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $permissions = [];
    while ($row = $result->fetch_assoc()) {
        $permissions[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $permissions]);
}

// Get permissions for a specific user
function getUserPermissions($conn) {
    $userId = $_GET['user_id'] ?? 0;
    
    if (empty($userId)) {
        echo json_encode(['success' => false, 'message' => 'User ID is required']);
        return;
    }
    
    // Get user-specific permissions (overrides)
    $userPerms = [];
    $stmt = $conn->prepare("
        SELECT permission_id
        FROM user_permissions
        WHERE user_id = ? AND granted = 1
    ");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $userPerms[] = $row['permission_id'];
    }
    
    // Get role-based permissions
    $rolePerms = [];
    $stmt = $conn->prepare("
        SELECT rp.permission_id
        FROM role_permissions rp
        JOIN cbn_users u ON u.role_id = rp.role_id
        WHERE u.cbn_user_id = ? AND rp.granted = 1
    ");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $rolePerms[] = $row['permission_id'];
    }
    
    // Merge permissions (user-specific overrides role-based)
    $allPermissionIds = array_unique(array_merge($userPerms, $rolePerms));
    
    echo json_encode(['success' => true, 'data' => $allPermissionIds]);
}
?>
