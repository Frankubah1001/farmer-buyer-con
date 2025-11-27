-- SQL Migration for Transport Booking System
-- Run this SQL to add necessary tables and columns

-- 1. Add availability column to transporters table (if not exists)
ALTER TABLE transporters 
ADD COLUMN IF NOT EXISTS availability ENUM('immediate', '1-3', '3-5', 'unavailable') DEFAULT 'immediate';

-- 2. Create transport_bookings table (if not exists)
CREATE TABLE IF NOT EXISTS transport_bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    transporter_id INT NOT NULL,
    buyer_id INT NOT NULL,
    booking_date DATETIME NOT NULL,
    payment_status ENUM('Pending', 'Paid') DEFAULT 'Pending',
    payment_method ENUM('Online', 'Delivery') NOT NULL,
    payment_reference VARCHAR(255) DEFAULT NULL,
    payment_amount DECIMAL(10, 2) NOT NULL,
    booking_status ENUM('Confirmed', 'In Transit', 'Delivered', 'Cancelled') DEFAULT 'Confirmed',
    delivery_status VARCHAR(100) DEFAULT 'Pending Pickup',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (transporter_id) REFERENCES transporters(transporter_id) ON DELETE CASCADE,
    FOREIGN KEY (buyer_id) REFERENCES buyers(buyer_id) ON DELETE CASCADE,
    INDEX idx_buyer (buyer_id),
    INDEX idx_transporter (transporter_id),
    INDEX idx_booking_status (booking_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Sample data: Update some transporters with availability
UPDATE transporters SET availability = 'immediate' WHERE is_verified = 1 LIMIT 3;
UPDATE transporters SET availability = '1-3' WHERE is_verified = 1 LIMIT 2 OFFSET 3;
UPDATE transporters SET availability = '3-5' LIMIT 1 OFFSET 5;

-- Note: After running this migration, you should have:
-- - availability column in transporters table
-- - transport_bookings table to store booking records
-- - Sample availability data populated
