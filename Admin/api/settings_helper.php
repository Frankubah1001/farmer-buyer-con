<?php
/**
 * Settings Helper Functions
 * Provides easy access to system settings throughout the application
 */

require_once __DIR__ . '/DBcon.php';

/**
 * Get a specific setting value
 * @param string $key The setting key
 * @param string $default Default value if setting not found
 * @return string The setting value
 */
function getSetting($key, $default = '') {
    global $conn;
    
    $stmt = $conn->prepare("SELECT setting_value FROM system_settings WHERE setting_key = ?");
    $stmt->bind_param('s', $key);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['setting_value'];
    }
    
    return $default;
}

/**
 * Get multiple settings at once
 * @param array $keys Array of setting keys
 * @return array Associative array of key => value
 */
function getSettings($keys) {
    global $conn;
    
    $settings = [];
    $placeholders = str_repeat('?,', count($keys) - 1) . '?';
    $stmt = $conn->prepare("SELECT setting_key, setting_value FROM system_settings WHERE setting_key IN ($placeholders)");
    $stmt->bind_param(str_repeat('s', count($keys)), ...$keys);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    
    return $settings;
}

/**
 * Update or create a setting
 * @param string $key The setting key
 * @param string $value The setting value
 * @param string $category The setting category (general, notification, system)
 * @return bool Success status
 */
function updateSetting($key, $value, $category = 'general') {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO system_settings (setting_key, setting_value, setting_category) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    $stmt->bind_param('sss', $key, $value, $category);
    
    return $stmt->execute();
}

/**
 * Get all active produce categories
 * @return array Array of category objects
 */
function getProduceCategories() {
    global $conn;
    
    $stmt = $conn->prepare("SELECT category_id, category_name FROM produce_categories WHERE is_active = 1 ORDER BY category_name");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    
    return $categories;
}

/**
 * Get all active business types
 * @return array Array of business type objects
 */
function getBusinessTypes() {
    global $conn;
    
    $stmt = $conn->prepare("SELECT type_id, type_name FROM business_types WHERE is_active = 1 ORDER BY type_name");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $types = [];
    while ($row = $result->fetch_assoc()) {
        $types[] = $row;
    }
    
    return $types;
}

/**
 * Get all active admin roles
 * @return array Array of role objects
 */
function getAdminRoles() {
    global $conn;
    
    $stmt = $conn->prepare("SELECT role_id, role_name, permissions FROM admin_roles WHERE is_active = 1 ORDER BY role_name");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $roles = [];
    while ($row = $result->fetch_assoc()) {
        $row['permissions'] = json_decode($row['permissions'], true);
        $roles[] = $row;
    }
    
    return $roles;
}

/**
 * Get all active order statuses
 * @return array Array of status objects
 */
function getOrderStatuses() {
    global $conn;
    
    $stmt = $conn->prepare("SELECT status_id, status_name, status_order, color_code FROM order_statuses WHERE is_active = 1 ORDER BY status_order, status_name");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $statuses = [];
    while ($row = $result->fetch_assoc()) {
        $statuses[] = $row;
    }
    
    return $statuses;
}

/**
 * Get currency symbol
 * @return string Currency symbol
 */
function getCurrency() {
    return getSetting('currency', '₦');
}

/**
 * Get site name
 * @return string Site name
 */
function getSiteName() {
    return getSetting('site_name', 'AgriAdmin Platform');
}

/**
 * Get timezone
 * @return string Timezone
 */
function getTimezone() {
    return getSetting('timezone', 'Africa/Lagos');
}

/**
 * Get date format
 * @return string Date format
 */
function getDateFormat() {
    return getSetting('date_format', 'dd/mm/yyyy');
}

/**
 * Format date according to system settings
 * @param string $date Date string
 * @return string Formatted date
 */
function formatDate($date) {
    $format = getDateFormat();
    $timestamp = strtotime($date);
    
    switch ($format) {
        case 'dd/mm/yyyy':
            return date('d/m/Y', $timestamp);
        case 'mm/dd/yyyy':
            return date('m/d/Y', $timestamp);
        case 'yyyy-mm-dd':
            return date('Y-m-d', $timestamp);
        default:
            return date('d/m/Y', $timestamp);
    }
}

/**
 * Check if SMS notifications are enabled
 * @return bool
 */
function isSmsEnabled() {
    return getSetting('enable_sms', '0') === '1';
}

/**
 * Get SMTP configuration
 * @return array SMTP configuration
 */
function getSmtpConfig() {
    return [
        'host' => getSetting('smtp_host', 'smtp.gmail.com'),
        'port' => getSetting('smtp_port', '587'),
        'email' => getSetting('smtp_email', ''),
        'password' => getSetting('smtp_password', '')
    ];
}
?>
