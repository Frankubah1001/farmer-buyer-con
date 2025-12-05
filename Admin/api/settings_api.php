<?php
/**
 * Settings API
 * Handles all settings-related operations for the admin panel
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
        // ============ GENERAL SETTINGS ============
        case 'get_general_settings':
            getGeneralSettings($conn);
            break;
        
        case 'save_general_settings':
            saveGeneralSettings($conn);
            break;
        
        // ============ NOTIFICATION SETTINGS ============
        case 'get_notification_settings':
            getNotificationSettings($conn);
            break;
        
        case 'save_notification_settings':
            saveNotificationSettings($conn);
            break;
        
        // ============ PRODUCE CATEGORIES ============
        case 'get_produce_categories':
            getProduceCategories($conn);
            break;
        
        case 'add_produce_category':
            addProduceCategory($conn);
            break;
        
        case 'update_produce_category':
            updateProduceCategory($conn);
            break;
        
        case 'delete_produce_category':
            deleteProduceCategory($conn);
            break;
        
        // ============ BUSINESS TYPES ============
        case 'get_business_types':
            getBusinessTypes($conn);
            break;
        
        case 'add_business_type':
            addBusinessType($conn);
            break;
        
        case 'update_business_type':
            updateBusinessType($conn);
            break;
        
        case 'delete_business_type':
            deleteBusinessType($conn);
            break;
        
        // ============ ADMIN ROLES ============
        case 'get_roles':
            getRoles($conn);
            break;
        
        case 'add_role':
            addRole($conn);
            break;
        
        case 'update_role':
            updateRole($conn);
            break;
        
        case 'delete_role':
            deleteRole($conn);
            break;
        
        // ============ ORDER STATUSES ============
        case 'get_order_statuses':
            getOrderStatuses($conn);
            break;
        
        case 'add_order_status':
            addOrderStatus($conn);
            break;
        
        case 'update_order_status':
            updateOrderStatus($conn);
            break;
        
        case 'delete_order_status':
            deleteOrderStatus($conn);
            break;
        
        // ============ DESIGNATIONS ============
        case 'get_designations':
            getDesignations($conn);
            break;
        
        case 'add_designation':
            addDesignation($conn);
            break;
        
        case 'update_designation':
            updateDesignation($conn);
            break;
        
        case 'delete_designation':
            deleteDesignation($conn);
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

// ==================== GENERAL SETTINGS FUNCTIONS ====================

function getGeneralSettings($conn) {
    $settings = [];
    $keys = ['site_name', 'currency', 'timezone', 'date_format'];
    
    $placeholders = str_repeat('?,', count($keys) - 1) . '?';
    $stmt = $conn->prepare("SELECT setting_key, setting_value FROM system_settings WHERE setting_key IN ($placeholders)");
    $stmt->bind_param(str_repeat('s', count($keys)), ...$keys);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    
    echo json_encode(['success' => true, 'data' => $settings]);
}

function saveGeneralSettings($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $settings = [
        'site_name' => $data['siteName'] ?? '',
        'currency' => $data['currency'] ?? '',
        'timezone' => $data['timezone'] ?? '',
        'date_format' => $data['dateFormat'] ?? ''
    ];
    
    $conn->begin_transaction();
    
    try {
        $stmt = $conn->prepare("INSERT INTO system_settings (setting_key, setting_value, setting_category) VALUES (?, ?, 'general') ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        
        foreach ($settings as $key => $value) {
            $stmt->bind_param('ss', $key, $value);
            $stmt->execute();
        }
        
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'General settings saved successfully']);
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
}

// ==================== NOTIFICATION SETTINGS FUNCTIONS ====================

function getNotificationSettings($conn) {
    $settings = [];
    $keys = ['smtp_host', 'smtp_port', 'smtp_email', 'smtp_password', 'enable_sms', 'sms_api_key'];
    
    $placeholders = str_repeat('?,', count($keys) - 1) . '?';
    $stmt = $conn->prepare("SELECT setting_key, setting_value FROM system_settings WHERE setting_key IN ($placeholders)");
    $stmt->bind_param(str_repeat('s', count($keys)), ...$keys);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        // Don't send password in plain text
        if ($row['setting_key'] === 'smtp_password') {
            $settings[$row['setting_key']] = $row['setting_value'] ? '********' : '';
        } else {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    echo json_encode(['success' => true, 'data' => $settings]);
}

function saveNotificationSettings($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $settings = [
        'smtp_host' => $data['smtpHost'] ?? '',
        'smtp_port' => $data['smtpPort'] ?? '',
        'smtp_email' => $data['smtpEmail'] ?? '',
        'enable_sms' => $data['enableSms'] ? '1' : '0',
        'sms_api_key' => $data['smsApiKey'] ?? ''
    ];
    
    // Only update password if it's not the masked value
    if (isset($data['smtpPassword']) && $data['smtpPassword'] !== '********' && !empty($data['smtpPassword'])) {
        $settings['smtp_password'] = $data['smtpPassword'];
    }
    
    $conn->begin_transaction();
    
    try {
        $stmt = $conn->prepare("INSERT INTO system_settings (setting_key, setting_value, setting_category) VALUES (?, ?, 'notification') ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        
        foreach ($settings as $key => $value) {
            $stmt->bind_param('ss', $key, $value);
            $stmt->execute();
        }
        
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Notification settings saved successfully']);
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
}

// ==================== PRODUCE CATEGORIES FUNCTIONS ====================

function getProduceCategories($conn) {
    $stmt = $conn->prepare("SELECT category_id, category_name, description, is_active FROM produce_categories WHERE is_active = 1 ORDER BY category_name");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $categories]);
}

function addProduceCategory($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = trim($data['name'] ?? '');
    
    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Category name is required']);
        return;
    }
    
    // Check if category already exists
    $stmt = $conn->prepare("SELECT category_id FROM produce_categories WHERE category_name = ?");
    $stmt->bind_param('s', $name);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Category already exists']);
        return;
    }
    
    $stmt = $conn->prepare("INSERT INTO produce_categories (category_name) VALUES (?)");
    $stmt->bind_param('s', $name);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Category added successfully', 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add category']);
    }
}

function updateProduceCategory($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? 0;
    $oldName = trim($data['oldName'] ?? '');
    $newName = trim($data['newName'] ?? '');
    
    if (empty($newName)) {
        echo json_encode(['success' => false, 'message' => 'Category name is required']);
        return;
    }
    
    // Check if new name already exists (excluding current category)
    if ($oldName !== $newName) {
        $stmt = $conn->prepare("SELECT category_id FROM produce_categories WHERE category_name = ? AND category_name != ?");
        $stmt->bind_param('ss', $newName, $oldName);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Category name already exists']);
            return;
        }
    }
    
    $stmt = $conn->prepare("UPDATE produce_categories SET category_name = ? WHERE category_name = ?");
    $stmt->bind_param('ss', $newName, $oldName);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Category updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update category']);
    }
}

function deleteProduceCategory($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = trim($data['name'] ?? '');
    
    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Category name is required']);
        return;
    }
    
    // Soft delete - set is_active to 0
    $stmt = $conn->prepare("UPDATE produce_categories SET is_active = 0 WHERE category_name = ?");
    $stmt->bind_param('s', $name);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete category']);
    }
}

// ==================== BUSINESS TYPES FUNCTIONS ====================

function getBusinessTypes($conn) {
    $stmt = $conn->prepare("SELECT type_id, type_name, description, is_active FROM business_types WHERE is_active = 1 ORDER BY type_name");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $types = [];
    while ($row = $result->fetch_assoc()) {
        $types[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $types]);
}

function addBusinessType($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = trim($data['name'] ?? '');
    
    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Business type name is required']);
        return;
    }
    
    // Check if type already exists
    $stmt = $conn->prepare("SELECT type_id FROM business_types WHERE type_name = ?");
    $stmt->bind_param('s', $name);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Business type already exists']);
        return;
    }
    
    $stmt = $conn->prepare("INSERT INTO business_types (type_name) VALUES (?)");
    $stmt->bind_param('s', $name);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Business type added successfully', 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add business type']);
    }
}

function updateBusinessType($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $oldName = trim($data['oldName'] ?? '');
    $newName = trim($data['newName'] ?? '');
    
    if (empty($newName)) {
        echo json_encode(['success' => false, 'message' => 'Business type name is required']);
        return;
    }
    
    // Check if new name already exists (excluding current type)
    if ($oldName !== $newName) {
        $stmt = $conn->prepare("SELECT type_id FROM business_types WHERE type_name = ? AND type_name != ?");
        $stmt->bind_param('ss', $newName, $oldName);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Business type name already exists']);
            return;
        }
    }
    
    $stmt = $conn->prepare("UPDATE business_types SET type_name = ? WHERE type_name = ?");
    $stmt->bind_param('ss', $newName, $oldName);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Business type updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update business type']);
    }
}

function deleteBusinessType($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = trim($data['name'] ?? '');
    
    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Business type name is required']);
        return;
    }
    
    // Soft delete - set is_active to 0
    $stmt = $conn->prepare("UPDATE business_types SET is_active = 0 WHERE type_name = ?");
    $stmt->bind_param('s', $name);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Business type deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete business type']);
    }
}

// ==================== ADMIN ROLES FUNCTIONS ====================

function getRoles($conn) {
    $stmt = $conn->prepare("SELECT role_id, role_name, description, permissions, is_active FROM admin_roles WHERE is_active = 1 ORDER BY role_name");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $roles = [];
    while ($row = $result->fetch_assoc()) {
        $roles[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $roles]);
}

function addRole($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = trim($data['name'] ?? '');
    $description = trim($data['description'] ?? '');
    $permissions = json_encode($data['permissions'] ?? ['view' => true]);
    
    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Role name is required']);
        return;
    }
    
    // Check if role already exists
    $stmt = $conn->prepare("SELECT role_id FROM admin_roles WHERE role_name = ?");
    $stmt->bind_param('s', $name);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Role already exists']);
        return;
    }
    
    $stmt = $conn->prepare("INSERT INTO admin_roles (role_name, description, permissions) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $name, $description, $permissions);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Role added successfully', 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add role']);
    }
}

function updateRole($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $oldName = trim($data['oldName'] ?? '');
    $newName = trim($data['newName'] ?? '');
    $description = trim($data['description'] ?? '');
    
    if (empty($newName)) {
        echo json_encode(['success' => false, 'message' => 'Role name is required']);
        return;
    }
    
    // Check if new name already exists (excluding current role)
    if ($oldName !== $newName) {
        $stmt = $conn->prepare("SELECT role_id FROM admin_roles WHERE role_name = ? AND role_name != ?");
        $stmt->bind_param('ss', $newName, $oldName);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Role name already exists']);
            return;
        }
    }
    
    $stmt = $conn->prepare("UPDATE admin_roles SET role_name = ?, description = ? WHERE role_name = ?");
    $stmt->bind_param('sss', $newName, $description, $oldName);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Role updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update role']);
    }
}

function deleteRole($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = trim($data['name'] ?? '');
    
    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Role name is required']);
        return;
    }
    
    // Soft delete - set is_active to 0
    $stmt = $conn->prepare("UPDATE admin_roles SET is_active = 0 WHERE role_name = ?");
    $stmt->bind_param('s', $name);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Role deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete role']);
    }
}

// ==================== ORDER STATUSES FUNCTIONS ====================

function getOrderStatuses($conn) {
    $stmt = $conn->prepare("SELECT status_id, status_name, status_order, description, color_code, is_active FROM order_statuses WHERE is_active = 1 ORDER BY status_order, status_name");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $statuses = [];
    while ($row = $result->fetch_assoc()) {
        $statuses[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $statuses]);
}

function addOrderStatus($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = trim($data['name'] ?? '');
    $description = trim($data['description'] ?? '');
    $colorCode = trim($data['colorCode'] ?? '#6c757d');
    
    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Status name is required']);
        return;
    }
    
    // Check if status already exists
    $stmt = $conn->prepare("SELECT status_id FROM order_statuses WHERE status_name = ?");
    $stmt->bind_param('s', $name);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Status already exists']);
        return;
    }
    
    // Get max order number
    $result = $conn->query("SELECT MAX(status_order) as max_order FROM order_statuses");
    $maxOrder = $result->fetch_assoc()['max_order'] ?? 0;
    $newOrder = $maxOrder + 1;
    
    $stmt = $conn->prepare("INSERT INTO order_statuses (status_name, status_order, description, color_code) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('siss', $name, $newOrder, $description, $colorCode);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Status added successfully', 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add status']);
    }
}

function updateOrderStatus($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $oldName = trim($data['oldName'] ?? '');
    $newName = trim($data['newName'] ?? '');
    $description = trim($data['description'] ?? '');
    
    if (empty($newName)) {
        echo json_encode(['success' => false, 'message' => 'Status name is required']);
        return;
    }
    
    // Check if new name already exists (excluding current status)
    if ($oldName !== $newName) {
        $stmt = $conn->prepare("SELECT status_id FROM order_statuses WHERE status_name = ? AND status_name != ?");
        $stmt->bind_param('ss', $newName, $oldName);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Status name already exists']);
            return;
        }
    }
    
    $stmt = $conn->prepare("UPDATE order_statuses SET status_name = ?, description = ? WHERE status_name = ?");
    $stmt->bind_param('sss', $newName, $description, $oldName);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }
}

function deleteOrderStatus($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = trim($data['name'] ?? '');
    
    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Status name is required']);
        return;
    }
    
    // Soft delete - set is_active to 0
    $stmt = $conn->prepare("UPDATE order_statuses SET is_active = 0 WHERE status_name = ?");
    $stmt->bind_param('s', $name);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Status deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete status']);
    }
}

// ==================== DESIGNATIONS FUNCTIONS ====================

function getDesignations($conn) {
    $stmt = $conn->prepare("SELECT designation_id, designation_name, description, is_active FROM designations WHERE is_active = 1 ORDER BY designation_name");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $designations = [];
    while ($row = $result->fetch_assoc()) {
        $designations[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $designations]);
}

function addDesignation($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = trim($data['name'] ?? '');
    $description = trim($data['description'] ?? '');
    
    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Designation name is required']);
        return;
    }
    
    // Check if designation already exists
    $stmt = $conn->prepare("SELECT designation_id FROM designations WHERE designation_name = ?");
    $stmt->bind_param('s', $name);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Designation already exists']);
        return;
    }
    
    $stmt = $conn->prepare("INSERT INTO designations (designation_name, description) VALUES (?, ?)");
    $stmt->bind_param('ss', $name, $description);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Designation added successfully', 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add designation']);
    }
}

function updateDesignation($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $oldName = trim($data['oldName'] ?? '');
    $newName = trim($data['newName'] ?? '');
    $description = trim($data['description'] ?? '');
    
    if (empty($newName)) {
        echo json_encode(['success' => false, 'message' => 'Designation name is required']);
        return;
    }
    
    // Check if new name already exists (excluding current designation)
    if ($oldName !== $newName) {
        $stmt = $conn->prepare("SELECT designation_id FROM designations WHERE designation_name = ? AND designation_name != ?");
        $stmt->bind_param('ss', $newName, $oldName);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Designation name already exists']);
            return;
        }
    }
    
    $stmt = $conn->prepare("UPDATE designations SET designation_name = ?, description = ? WHERE designation_name = ?");
    $stmt->bind_param('sss', $newName, $description, $oldName);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Designation updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update designation']);
    }
}

function deleteDesignation($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = trim($data['name'] ?? '');
    
    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Designation name is required']);
        return;
    }
    
    // Soft delete - set is_active to 0
    $stmt = $conn->prepare("UPDATE designations SET is_active = 0 WHERE designation_name = ?");
    $stmt->bind_param('s', $name);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Designation deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete designation']);
    }
}
?>
