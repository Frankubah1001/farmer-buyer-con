-- ============================================
-- PERMISSIONS SYSTEM SETUP
-- ============================================

-- 1. Create permissions table
CREATE TABLE IF NOT EXISTS `permissions` (
    `permission_id` INT AUTO_INCREMENT PRIMARY KEY,
    `permission_name` VARCHAR(100) UNIQUE NOT NULL,
    `permission_label` VARCHAR(150) NOT NULL,
    `module` VARCHAR(50) NOT NULL,
    `description` TEXT,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_module` (`module`),
    INDEX `idx_permission_name` (`permission_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Create role_permissions junction table
CREATE TABLE IF NOT EXISTS `role_permissions` (
    `role_permission_id` INT AUTO_INCREMENT PRIMARY KEY,
    `role_id` INT NOT NULL,
    `permission_id` INT NOT NULL,
    `granted` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`role_id`) REFERENCES `admin_roles`(`role_id`) ON DELETE CASCADE,
    FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`permission_id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_role_permission` (`role_id`, `permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Create user_permissions table for user-specific overrides
CREATE TABLE IF NOT EXISTS `user_permissions` (
    `user_permission_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `permission_id` INT NOT NULL,
    `granted` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `cbn_users`(`cbn_user_id`) ON DELETE CASCADE,
    FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`permission_id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_user_permission` (`user_id`, `permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- INSERT ALL PERMISSIONS
-- ============================================

-- DASHBOARD PERMISSIONS
INSERT INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
('view_dashboard', 'View Dashboard', 'dashboard', 'View dashboard statistics and charts'),
('view_analytics', 'View Analytics', 'dashboard', 'View detailed analytics and reports'),
('export_dashboard_data', 'Export Dashboard Data', 'dashboard', 'Export dashboard data');

-- FARMERS MANAGEMENT PERMISSIONS
INSERT INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
('view_farmers', 'View Farmers', 'farmers', 'View farmers list'),
('view_farmer_details', 'View Farmer Details', 'farmers', 'View individual farmer details'),
('add_farmer', 'Add Farmer', 'farmers', 'Add new farmer'),
('edit_farmer', 'Edit Farmer', 'farmers', 'Edit farmer information'),
('delete_farmer', 'Delete Farmer', 'farmers', 'Delete/deactivate farmer'),
('approve_farmer', 'Approve Farmer', 'farmers', 'Approve farmer registration'),
('reject_farmer', 'Reject Farmer', 'farmers', 'Reject farmer registration'),
('verify_farmer', 'Verify Farmer', 'farmers', 'Verify farmer documents'),
('manage_farmer_produce', 'Manage Farmer Produce', 'farmers', 'Manage farmer produce listings'),
('manage_farmer_withdrawals', 'Manage Farmer Withdrawals', 'farmers', 'Approve/reject withdrawal requests'),
('export_farmers', 'Export Farmers', 'farmers', 'Export farmers data');

-- BUYERS MANAGEMENT PERMISSIONS
INSERT INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
('view_buyers', 'View Buyers', 'buyers', 'View buyers list'),
('view_buyer_details', 'View Buyer Details', 'buyers', 'View individual buyer details'),
('add_buyer', 'Add Buyer', 'buyers', 'Add new buyer'),
('edit_buyer', 'Edit Buyer', 'buyers', 'Edit buyer information'),
('delete_buyer', 'Delete Buyer', 'buyers', 'Delete/deactivate buyer'),
('approve_buyer', 'Approve Buyer', 'buyers', 'Approve buyer registration'),
('reject_buyer', 'Reject Buyer', 'buyers', 'Reject buyer registration'),
('verify_buyer', 'Verify Buyer', 'buyers', 'Verify buyer documents'),
('export_buyers', 'Export Buyers', 'buyers', 'Export buyers data');

-- ORDERS MANAGEMENT PERMISSIONS
INSERT INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
('view_orders', 'View Orders', 'orders', 'View orders list'),
('view_order_details', 'View Order Details', 'orders', 'View individual order details'),
('create_order', 'Create Order', 'orders', 'Create new order'),
('edit_order', 'Edit Order', 'orders', 'Edit order information'),
('delete_order', 'Delete Order', 'orders', 'Delete/cancel order'),
('update_order_status', 'Update Order Status', 'orders', 'Update order status'),
('process_payment', 'Process Payment', 'orders', 'Process order payments'),
('manage_delivery', 'Manage Delivery', 'orders', 'Manage order delivery'),
('export_orders', 'Export Orders', 'orders', 'Export orders data');

-- PRICES MANAGEMENT PERMISSIONS
INSERT INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
('view_prices', 'View Prices', 'prices', 'View price ranges'),
('add_price', 'Add Price', 'prices', 'Add new price range'),
('edit_price', 'Edit Price', 'prices', 'Edit price range'),
('delete_price', 'Delete Price', 'prices', 'Delete price range'),
('approve_price', 'Approve Price', 'prices', 'Approve price changes'),
('export_prices', 'Export Prices', 'prices', 'Export price data');

-- REPORTS MANAGEMENT PERMISSIONS
INSERT INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
('view_reports', 'View Reports', 'reports', 'View reports list'),
('view_report_details', 'View Report Details', 'reports', 'View individual report details'),
('resolve_report', 'Resolve Report', 'reports', 'Resolve/close reports'),
('delete_report', 'Delete Report', 'reports', 'Delete reports'),
('export_reports', 'Export Reports', 'reports', 'Export reports data');

-- FARM TOOLS APPLICATIONS PERMISSIONS
INSERT INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
('view_farm_tools_applications', 'View Farm Tools Applications', 'farm_tools', 'View applications list'),
('view_farm_tools_details', 'View Farm Tools Details', 'farm_tools', 'View application details'),
('approve_farm_tools', 'Approve Farm Tools', 'farm_tools', 'Approve applications'),
('reject_farm_tools', 'Reject Farm Tools', 'farm_tools', 'Reject applications'),
('export_farm_tools', 'Export Farm Tools', 'farm_tools', 'Export applications data');

-- GRANT APPLICATIONS PERMISSIONS
INSERT INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
('view_grant_applications', 'View Grant Applications', 'grants', 'View applications list'),
('view_grant_details', 'View Grant Details', 'grants', 'View application details'),
('approve_grant', 'Approve Grant', 'grants', 'Approve applications'),
('reject_grant', 'Reject Grant', 'grants', 'Reject applications'),
('export_grants', 'Export Grants', 'grants', 'Export applications data');

-- LOAN APPLICATIONS PERMISSIONS
INSERT INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
('view_loan_applications', 'View Loan Applications', 'loans', 'View applications list'),
('view_loan_details', 'View Loan Details', 'loans', 'View application details'),
('approve_loan', 'Approve Loan', 'loans', 'Approve applications'),
('reject_loan', 'Reject Loan', 'loans', 'Reject applications'),
('export_loans', 'Export Loans', 'loans', 'Export applications data');

-- INCENTIVES MANAGEMENT PERMISSIONS
INSERT INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
('view_incentives', 'View Incentives', 'incentives', 'View incentives list'),
('add_incentive', 'Add Incentive', 'incentives', 'Add new incentive'),
('edit_incentive', 'Edit Incentive', 'incentives', 'Edit incentive'),
('delete_incentive', 'Delete Incentive', 'incentives', 'Delete incentive'),
('export_incentives', 'Export Incentives', 'incentives', 'Export incentives data');

-- TRANSPORT MANAGEMENT PERMISSIONS
INSERT INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
('view_transport', 'View Transport', 'transport', 'View transport requests'),
('view_transport_details', 'View Transport Details', 'transport', 'View transport details'),
('assign_transport', 'Assign Transport', 'transport', 'Assign transport'),
('update_transport_status', 'Update Transport Status', 'transport', 'Update transport status'),
('export_transport', 'Export Transport', 'transport', 'Export transport data');

-- AUDIT LOGS PERMISSIONS
INSERT INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
('view_audit_logs', 'View Audit Logs', 'audit', 'View system audit logs'),
('export_audit_logs', 'Export Audit Logs', 'audit', 'Export audit logs');

-- ADMIN MANAGEMENT PERMISSIONS
INSERT INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
('view_admins', 'View Admins', 'admins', 'View admins list'),
('view_admin_details', 'View Admin Details', 'admins', 'View admin details'),
('add_admin', 'Add Admin', 'admins', 'Add new admin'),
('edit_admin', 'Edit Admin', 'admins', 'Edit admin information'),
('delete_admin', 'Delete Admin', 'admins', 'Delete/deactivate admin'),
('manage_admin_roles', 'Manage Admin Roles', 'admins', 'Manage admin roles'),
('view_admin_logs', 'View Admin Logs', 'admins', 'View admin activity logs');

-- SETTINGS PERMISSIONS
INSERT INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
('view_settings', 'View Settings', 'settings', 'View system settings'),
('edit_general_settings', 'Edit General Settings', 'settings', 'Edit general settings'),
('edit_notification_settings', 'Edit Notification Settings', 'settings', 'Edit notification settings'),
('manage_produce_categories', 'Manage Produce Categories', 'settings', 'Manage produce categories'),
('manage_business_types', 'Manage Business Types', 'settings', 'Manage business types'),
('manage_roles_settings', 'Manage Roles in Settings', 'settings', 'Manage admin roles in settings page'),
('manage_designations', 'Manage Designations', 'settings', 'Manage designations'),
('manage_order_statuses', 'Manage Order Statuses', 'settings', 'Manage order statuses');

-- ============================================
-- ASSIGN PERMISSIONS TO EXISTING ROLES
-- ============================================

-- Note: This assumes you have roles in admin_roles table
-- Adjust role names based on your actual data

-- Get all permission IDs for Super Admin (grant all permissions)
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT 
    (SELECT role_id FROM admin_roles WHERE role_name = 'Super Admin' LIMIT 1),
    permission_id,
    1
FROM permissions
WHERE NOT EXISTS (
    SELECT 1 FROM role_permissions rp 
    WHERE rp.role_id = (SELECT role_id FROM admin_roles WHERE role_name = 'Super Admin' LIMIT 1)
    AND rp.permission_id = permissions.permission_id
);

-- Operations Manager permissions
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT 
    (SELECT role_id FROM admin_roles WHERE role_name = 'Operations Manager' LIMIT 1),
    permission_id,
    1
FROM permissions
WHERE permission_name IN (
    'view_dashboard', 'view_analytics',
    'view_farmers', 'view_farmer_details', 'add_farmer', 'edit_farmer', 'approve_farmer', 'reject_farmer', 'verify_farmer', 'manage_farmer_produce', 'manage_farmer_withdrawals', 'export_farmers',
    'view_buyers', 'view_buyer_details', 'add_buyer', 'edit_buyer', 'approve_buyer', 'reject_buyer', 'verify_buyer', 'export_buyers',
    'view_orders', 'view_order_details', 'create_order', 'edit_order', 'delete_order', 'update_order_status', 'process_payment', 'manage_delivery', 'export_orders',
    'view_prices', 'edit_price',
    'view_reports', 'view_report_details', 'resolve_report', 'delete_report', 'export_reports',
    'view_farm_tools_applications', 'view_farm_tools_details', 'approve_farm_tools', 'reject_farm_tools', 'export_farm_tools',
    'view_grant_applications', 'view_grant_details',
    'view_loan_applications', 'view_loan_details',
    'view_incentives',
    'view_transport', 'view_transport_details', 'assign_transport', 'update_transport_status', 'export_transport',
    'view_audit_logs',
    'view_admins', 'view_admin_details',
    'view_settings'
)
AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp 
    WHERE rp.role_id = (SELECT role_id FROM admin_roles WHERE role_name = 'Operations Manager' LIMIT 1)
    AND rp.permission_id = permissions.permission_id
);

-- Viewer permissions (read-only)
INSERT INTO role_permissions (role_id, permission_id, granted)
SELECT 
    (SELECT role_id FROM admin_roles WHERE role_name = 'Viewer' LIMIT 1),
    permission_id,
    1
FROM permissions
WHERE permission_name IN (
    'view_dashboard',
    'view_farmers', 'view_farmer_details',
    'view_buyers', 'view_buyer_details',
    'view_orders', 'view_order_details',
    'view_prices',
    'view_reports', 'view_report_details',
    'view_farm_tools_applications', 'view_farm_tools_details',
    'view_grant_applications', 'view_grant_details',
    'view_loan_applications', 'view_loan_details',
    'view_incentives',
    'view_transport', 'view_transport_details',
    'view_settings'
)
AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp 
    WHERE rp.role_id = (SELECT role_id FROM admin_roles WHERE role_name = 'Viewer' LIMIT 1)
    AND rp.permission_id = permissions.permission_id
);

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Count total permissions
SELECT COUNT(*) as total_permissions FROM permissions;

-- Count permissions by module
SELECT module, COUNT(*) as permission_count 
FROM permissions 
GROUP BY module 
ORDER BY permission_count DESC;

-- View role permissions summary
SELECT 
    ar.role_name,
    COUNT(rp.permission_id) as granted_permissions
FROM admin_roles ar
LEFT JOIN role_permissions rp ON ar.role_id = rp.role_id AND rp.granted = 1
GROUP BY ar.role_id, ar.role_name
ORDER BY granted_permissions DESC;
