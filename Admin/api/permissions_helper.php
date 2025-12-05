<?php
/**
 * Permissions Helper
 * Functions for checking and managing user permissions
 */

/**
 * Check if a user has a specific permission
 * 
 * @param int $userId User ID
 * @param string $permissionName Permission name (e.g., 'view_farmers')
 * @return bool True if user has permission, false otherwise
 */
function hasPermission($userId, $permissionName) {
    global $conn;
    
    // Check user-specific permission override first
    $stmt = $conn->prepare("
        SELECT up.granted 
        FROM user_permissions up
        JOIN permissions p ON up.permission_id = p.permission_id
        WHERE up.user_id = ? AND p.permission_name = ? AND p.is_active = 1
    ");
    $stmt->bind_param('is', $userId, $permissionName);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc()['granted'] == 1;
    }
    
    // Check role-based permission
    $stmt = $conn->prepare("
        SELECT rp.granted 
        FROM role_permissions rp
        JOIN permissions p ON rp.permission_id = p.permission_id
        JOIN cbn_users u ON u.role_id = rp.role_id
        WHERE u.cbn_user_id = ? AND p.permission_name = ? AND p.is_active = 1
    ");
    $stmt->bind_param('is', $userId, $permissionName);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc()['granted'] == 1;
    }
    
    return false; // No permission found
}

/**
 * Check if user has any of the specified permissions
 * 
 * @param int $userId User ID
 * @param array $permissions Array of permission names
 * @return bool True if user has at least one permission
 */
function hasAnyPermission($userId, $permissions) {
    foreach ($permissions as $permission) {
        if (hasPermission($userId, $permission)) {
            return true;
        }
    }
    return false;
}

/**
 * Check if user has all specified permissions
 * 
 * @param int $userId User ID
 * @param array $permissions Array of permission names
 * @return bool True if user has all permissions
 */
function hasAllPermissions($userId, $permissions) {
    foreach ($permissions as $permission) {
        if (!hasPermission($userId, $permission)) {
            return false;
        }
    }
    return true;
}

/**
 * Get all permissions for a user
 * 
 * @param int $userId User ID
 * @return array Array of permission objects
 */
function getUserPermissions($userId) {
    global $conn;
    
    $permissions = [];
    
    // Get role-based permissions
    $stmt = $conn->prepare("
        SELECT DISTINCT p.permission_id, p.permission_name, p.permission_label, p.module, rp.granted
        FROM role_permissions rp
        JOIN permissions p ON rp.permission_id = p.permission_id
        JOIN cbn_users u ON u.role_id = rp.role_id
        WHERE u.cbn_user_id = ? AND p.is_active = 1
    ");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $permissions[$row['permission_name']] = $row;
    }
    
    // Override with user-specific permissions
    $stmt = $conn->prepare("
        SELECT p.permission_id, p.permission_name, p.permission_label, p.module, up.granted
        FROM user_permissions up
        JOIN permissions p ON up.permission_id = p.permission_id
        WHERE up.user_id = ? AND p.is_active = 1
    ");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $permissions[$row['permission_name']] = $row;
    }
    
    return array_values($permissions);
}

/**
 * Get permissions grouped by module
 * 
 * @param int $userId User ID
 * @return array Associative array with modules as keys
 */
function getUserPermissionsByModule($userId) {
    $permissions = getUserPermissions($userId);
    $grouped = [];
    
    foreach ($permissions as $permission) {
        $module = $permission['module'];
        if (!isset($grouped[$module])) {
            $grouped[$module] = [];
        }
        $grouped[$module][] = $permission;
    }
    
    return $grouped;
}

/**
 * Get all available permissions
 * 
 * @return array Array of all permissions
 */
function getAllPermissions() {
    global $conn;
    
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
    
    return $permissions;
}

/**
 * Get all permissions grouped by module
 * 
 * @return array Associative array with modules as keys
 */
function getAllPermissionsByModule() {
    $permissions = getAllPermissions();
    $grouped = [];
    
    foreach ($permissions as $permission) {
        $module = $permission['module'];
        if (!isset($grouped[$module])) {
            $grouped[$module] = [];
        }
        $grouped[$module][] = $permission;
    }
    
    return $grouped;
}

/**
 * Get permissions for a specific role
 * 
 * @param int $roleId Role ID
 * @return array Array of permission objects
 */
function getRolePermissions($roleId) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT p.permission_id, p.permission_name, p.permission_label, p.module, rp.granted
        FROM role_permissions rp
        JOIN permissions p ON rp.permission_id = p.permission_id
        WHERE rp.role_id = ? AND p.is_active = 1
        ORDER BY p.module, p.permission_label
    ");
    $stmt->bind_param('i', $roleId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $permissions = [];
    while ($row = $result->fetch_assoc()) {
        $permissions[] = $row;
    }
    
    return $permissions;
}

/**
 * Assign permission to role
 * 
 * @param int $roleId Role ID
 * @param int $permissionId Permission ID
 * @param bool $granted Whether permission is granted
 * @return bool Success status
 */
function assignPermissionToRole($roleId, $permissionId, $granted = true) {
    global $conn;
    
    $grantedInt = $granted ? 1 : 0;
    
    $stmt = $conn->prepare("
        INSERT INTO role_permissions (role_id, permission_id, granted)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE granted = VALUES(granted)
    ");
    $stmt->bind_param('iii', $roleId, $permissionId, $grantedInt);
    
    return $stmt->execute();
}

/**
 * Assign permission to user (override)
 * 
 * @param int $userId User ID
 * @param int $permissionId Permission ID
 * @param bool $granted Whether permission is granted
 * @return bool Success status
 */
function assignPermissionToUser($userId, $permissionId, $granted = true) {
    global $conn;
    
    $grantedInt = $granted ? 1 : 0;
    
    $stmt = $conn->prepare("
        INSERT INTO user_permissions (user_id, permission_id, granted)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE granted = VALUES(granted)
    ");
    $stmt->bind_param('iii', $userId, $permissionId, $grantedInt);
    
    return $stmt->execute();
}

/**
 * Remove permission from role
 * 
 * @param int $roleId Role ID
 * @param int $permissionId Permission ID
 * @return bool Success status
 */
function removePermissionFromRole($roleId, $permissionId) {
    global $conn;
    
    $stmt = $conn->prepare("DELETE FROM role_permissions WHERE role_id = ? AND permission_id = ?");
    $stmt->bind_param('ii', $roleId, $permissionId);
    
    return $stmt->execute();
}

/**
 * Remove permission from user
 * 
 * @param int $userId User ID
 * @param int $permissionId Permission ID
 * @return bool Success status
 */
function removePermissionFromUser($userId, $permissionId) {
    global $conn;
    
    $stmt = $conn->prepare("DELETE FROM user_permissions WHERE user_id = ? AND permission_id = ?");
    $stmt->bind_param('ii', $userId, $permissionId);
    
    return $stmt->execute();
}

/**
 * Require permission - redirect if not authorized
 * 
 * @param string $permissionName Permission name
 * @param string $redirectUrl URL to redirect to if unauthorized
 */
function requirePermission($permissionName, $redirectUrl = 'dashboard.php') {
    if (!isset($_SESSION['cbn_user_id'])) {
        header("Location: cbn_login.php");
        exit;
    }
    
    if (!hasPermission($_SESSION['cbn_user_id'], $permissionName)) {
        $_SESSION['error_message'] = "You don't have permission to access this page.";
        header("Location: $redirectUrl");
        exit;
    }
}

/**
 * Check permission and return JSON error if not authorized (for API)
 * 
 * @param string $permissionName Permission name
 * @return bool True if authorized, exits with JSON error if not
 */
function requirePermissionAPI($permissionName) {
    if (!isset($_SESSION['cbn_user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    if (!hasPermission($_SESSION['cbn_user_id'], $permissionName)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Permission denied']);
        exit;
    }
    
    return true;
}

/**
 * Get module display name
 * 
 * @param string $module Module name
 * @return string Display name
 */
function getModuleDisplayName($module) {
    $moduleNames = [
        'dashboard' => 'Dashboard',
        'farmers' => 'Farmers Management',
        'buyers' => 'Buyers Management',
        'orders' => 'Orders Management',
        'prices' => 'Prices Management',
        'reports' => 'Reports Management',
        'farm_tools' => 'Farm Tools Applications',
        'grants' => 'Grant Applications',
        'loans' => 'Loan Applications',
        'incentives' => 'Incentives Management',
        'transport' => 'Transport Management',
        'audit' => 'Audit Logs',
        'admins' => 'Admin Management',
        'settings' => 'Settings'
    ];
    
    return $moduleNames[$module] ?? ucfirst($module);
}
?>
