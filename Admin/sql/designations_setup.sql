-- Create designations table
CREATE TABLE IF NOT EXISTS `designations` (
  `designation_id` int(11) NOT NULL AUTO_INCREMENT,
  `designation_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`designation_id`),
  UNIQUE KEY `designation_name` (`designation_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add designation_id, phone, address, notes, and profile_pic fields to cbn_users if they don't exist
ALTER TABLE `cbn_users` 
ADD COLUMN IF NOT EXISTS `designation_id` int(11) DEFAULT NULL AFTER `role_id`,
ADD COLUMN IF NOT EXISTS `phone` varchar(20) DEFAULT NULL AFTER `designation_id`,
ADD COLUMN IF NOT EXISTS `address` text DEFAULT NULL AFTER `phone`,
ADD COLUMN IF NOT EXISTS `notes` text DEFAULT NULL AFTER `address`,
ADD COLUMN IF NOT EXISTS `profile_pic` text DEFAULT NULL AFTER `notes`,
ADD FOREIGN KEY IF NOT EXISTS (`designation_id`) REFERENCES `designations`(`designation_id`) ON DELETE SET NULL;

-- Insert some default designations
INSERT INTO `designations` (`designation_name`, `description`) VALUES
('Platform Owner', 'Owner and administrator of the platform'),
('Operations Manager', 'Manages day-to-day operations'),
('Finance Manager', 'Handles financial transactions and reports'),
('Support Staff', 'Provides customer support'),
('Data Analyst', 'Analyzes platform data and generates reports'),
('Content Manager', 'Manages platform content and communications')
ON DUPLICATE KEY UPDATE `designation_name` = VALUES(`designation_name`);
