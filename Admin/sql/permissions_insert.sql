-- ============================================
-- PERMISSIONS SYSTEM SETUP (SIMPLIFIED)
-- ============================================

-- Only insert permissions (tables already exist)
-- Skip role assignments for now

-- DASHBOARD PERMISSIONS
INSERT IGNORE INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
('view_dashboard', 'View Dashboard', 'dashboard', 'View dashboard statistics and charts'),
('view_analytics', 'View Analytics', 'dashboard', 'View detailed analytics and reports'),
('export_dashboard_data', 'Export Dashboard Data', 'dashboard', 'Export dashboard data');

-- FARMERS MANAGEMENT PERMISSIONS
INSERT IGNORE INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
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
INSERT IGNORE INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
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
INSERT IGNORE INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
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
INSERT IGNORE INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
('view_prices', 'View Prices', 'prices', 'View price ranges'),
('add_price', 'Add Price', 'prices', 'Add new price range'),
('edit_price', 'Edit Price', 'prices', 'Edit price range'),
('delete_price', 'Delete Price', 'prices', 'Delete price range'),
('approve_price', 'Approve Price', 'prices', 'Approve price changes'),
('export_prices', 'Export Prices', 'prices', 'Export price data');

-- REPORTS MANAGEMENT PERMISSIONS
INSERT IGNORE INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
('view_reports', 'View Reports', 'reports', 'View reports list'),
('view_report_details', 'View Report Details', 'reports', 'View individual report details'),
('resolve_report', 'Resolve Report', 'reports', 'Resolve/close reports'),
('delete_report', 'Delete Report', 'reports', 'Delete reports'),
('export_reports', 'Export Reports', 'reports', 'Export reports data');

-- FARM TOOLS APPLICATIONS PERMISSIONS
INSERT IGNORE INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
('view_farm_tools_applications', 'View Farm Tools Applications', 'farm_tools', 'View applications list'),
('view_farm_tools_details', 'View Farm Tools Details', 'farm_tools', 'View application details'),
('approve_farm_tools', 'Approve Farm Tools', 'farm_tools', 'Approve applications'),
('reject_farm_tools', 'Reject Farm Tools', 'farm_tools', 'Reject applications'),
('export_farm_tools', 'Export Farm Tools', 'farm_tools', 'Export applications data');

-- GRANT APPLICATIONS PERMISSIONS
INSERT IGNORE INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
('view_grant_applications', 'View Grant Applications', 'grants', 'View applications list'),
('view_grant_details', 'View Grant Details', 'grants', 'View application details'),
('approve_grant', 'Approve Grant', 'grants', 'Approve applications'),
('reject_grant', 'Reject Grant', 'grants', 'Reject applications'),
('export_grants', 'Export Grants', 'grants', 'Export applications data');

-- LOAN APPLICATIONS PERMISSIONS
INSERT IGNORE INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
('view_loan_applications', 'View Loan Applications', 'loans', 'View applications list'),
('view_loan_details', 'View Loan Details', 'loans', 'View application details'),
('approve_loan', 'Approve Loan', 'loans', 'Approve applications'),
('reject_loan', 'Reject Loan', 'loans', 'Reject applications'),
('export_loans', 'Export Loans', 'loans', 'Export applications data');

-- INCENTIVES MANAGEMENT PERMISSIONS
INSERT IGNORE INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
('view_incentives', 'View Incentives', 'incentives', 'View incentives list'),
('add_incentive', 'Add Incentive', 'incentives', 'Add new incentive'),
('edit_incentive', 'Edit Incentive', 'incentives', 'Edit incentive'),
('delete_incentive', 'Delete Incentive', 'incentives', 'Delete incentive'),
('export_incentives', 'Export Incentives', 'incentives', 'Export incentives data');

-- TRANSPORT MANAGEMENT PERMISSIONS
INSERT IGNORE INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
('view_transport', 'View Transport', 'transport', 'View transport requests'),
('view_transport_details', 'View Transport Details', 'transport', 'View transport details'),
('assign_transport', 'Assign Transport', 'transport', 'Assign transport'),
('update_transport_status', 'Update Transport Status', 'transport', 'Update transport status'),
('export_transport', 'Export Transport', 'transport', 'Export transport data');

-- AUDIT LOGS PERMISSIONS
INSERT IGNORE INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
('view_audit_logs', 'View Audit Logs', 'audit', 'View system audit logs'),
('export_audit_logs', 'Export Audit Logs', 'audit', 'Export audit logs');

-- ADMIN MANAGEMENT PERMISSIONS
INSERT IGNORE INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
('view_admins', 'View Admins', 'admins', 'View admins list'),
('view_admin_details', 'View Admin Details', 'admins', 'View admin details'),
('add_admin', 'Add Admin', 'admins', 'Add new admin'),
('edit_admin', 'Edit Admin', 'admins', 'Edit admin information'),
('delete_admin', 'Delete Admin', 'admins', 'Delete/deactivate admin'),
('manage_admin_roles', 'Manage Admin Roles', 'admins', 'Manage admin roles'),
('view_admin_logs', 'View Admin Logs', 'admins', 'View admin activity logs');

-- SETTINGS PERMISSIONS
INSERT IGNORE INTO `permissions` (`permission_name`, `permission_label`, `module`, `description`) VALUES
('view_settings', 'View Settings', 'settings', 'View system settings'),
('edit_general_settings', 'Edit General Settings', 'settings', 'Edit general settings'),
('edit_notification_settings', 'Edit Notification Settings', 'settings', 'Edit notification settings'),
('manage_produce_categories', 'Manage Produce Categories', 'settings', 'Manage produce categories'),
('manage_business_types', 'Manage Business Types', 'settings', 'Manage business types'),
('manage_roles_settings', 'Manage Roles in Settings', 'settings', 'Manage admin roles in settings page'),
('manage_designations', 'Manage Designations', 'settings', 'Manage designations'),
('manage_order_statuses', 'Manage Order Statuses', 'settings', 'Manage order statuses');

-- Assign ALL permissions to Super Admin
INSERT IGNORE INTO role_permissions (role_id, permission_id, granted)
SELECT 
    1, -- Super Admin role_id
    permission_id,
    1
FROM permissions;

-- Assign view-only permissions to Viewer
INSERT IGNORE INTO role_permissions (role_id, permission_id, granted)
SELECT 
    3, -- Viewer role_id
    permission_id,
    1
FROM permissions
WHERE permission_name LIKE 'view_%';

-- Verification
SELECT COUNT(*) as total_permissions FROM permissions;
SELECT module, COUNT(*) as permission_count FROM permissions GROUP BY module ORDER BY module;
