-- ============================================
-- Disaster Relief Management System Database
-- ============================================
-- Created for XAMPP/phpMyAdmin
-- Database: disaster_relief
-- ============================================

-- Create Database
CREATE DATABASE IF NOT EXISTS disaster_relief;
USE disaster_relief;

-- ============================================
-- Table: users
-- Stores all system users with role-based access
-- ============================================
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'staff', 'volunteer', 'donor') NOT NULL DEFAULT 'donor',
    status ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Table: disasters
-- Tracks disaster events and their status
-- ============================================
CREATE TABLE disasters (
    disaster_id INT AUTO_INCREMENT PRIMARY KEY,
    disaster_name VARCHAR(150) NOT NULL,
    disaster_type ENUM('earthquake', 'flood', 'hurricane', 'wildfire', 'tornado', 'tsunami', 'drought', 'other') NOT NULL,
    location VARCHAR(200) NOT NULL,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    severity ENUM('minor', 'moderate', 'severe', 'catastrophic') NOT NULL,
    description TEXT,
    affected_population INT DEFAULT 0,
    casualties INT DEFAULT 0,
    status ENUM('active', 'ongoing', 'resolved', 'monitoring') NOT NULL DEFAULT 'active',
    start_date DATETIME NOT NULL,
    end_date DATETIME NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_type (disaster_type),
    INDEX idx_status (status),
    INDEX idx_severity (severity),
    INDEX idx_location (location)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Table: relief_camps
-- Manages relief camp locations and capacity
-- ============================================
CREATE TABLE relief_camps (
    camp_id INT AUTO_INCREMENT PRIMARY KEY,
    disaster_id INT NOT NULL,
    camp_name VARCHAR(150) NOT NULL,
    location VARCHAR(200) NOT NULL,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    capacity INT NOT NULL,
    current_occupancy INT DEFAULT 0,
    facilities TEXT,
    status ENUM('operational', 'full', 'closed', 'under_construction') NOT NULL DEFAULT 'operational',
    manager_id INT,
    established_date DATE NOT NULL,
    closed_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (disaster_id) REFERENCES disasters(disaster_id) ON DELETE CASCADE,
    FOREIGN KEY (manager_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_disaster (disaster_id),
    INDEX idx_status (status),
    CHECK (current_occupancy <= capacity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Table: resources
-- Tracks inventory of relief supplies
-- ============================================
CREATE TABLE resources (
    resource_id INT AUTO_INCREMENT PRIMARY KEY,
    camp_id INT,
    resource_name VARCHAR(100) NOT NULL,
    resource_type ENUM('food', 'water', 'medicine', 'shelter', 'clothing', 'hygiene', 'other') NOT NULL,
    quantity DECIMAL(10, 2) NOT NULL,
    unit VARCHAR(20) NOT NULL,
    minimum_threshold DECIMAL(10, 2) DEFAULT 0,
    status ENUM('adequate', 'low', 'critical', 'out_of_stock') NOT NULL DEFAULT 'adequate',
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    notes TEXT,
    FOREIGN KEY (camp_id) REFERENCES relief_camps(camp_id) ON DELETE CASCADE,
    INDEX idx_camp (camp_id),
    INDEX idx_type (resource_type),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Table: donations
-- Records monetary and material donations
-- ============================================
CREATE TABLE donations (
    donation_id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT,
    disaster_id INT,
    donation_type ENUM('monetary', 'material') NOT NULL,
    amount DECIMAL(10, 2) NULL,
    currency VARCHAR(3) DEFAULT 'USD',
    material_description TEXT NULL,
    material_quantity DECIMAL(10, 2) NULL,
    material_unit VARCHAR(20) NULL,
    payment_method ENUM('credit_card', 'debit_card', 'bank_transfer', 'cash', 'other') NULL,
    transaction_id VARCHAR(100) NULL,
    status ENUM('pending', 'completed', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    donation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    receipt_number VARCHAR(50) UNIQUE,
    notes TEXT,
    FOREIGN KEY (donor_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (disaster_id) REFERENCES disasters(disaster_id) ON DELETE SET NULL,
    INDEX idx_donor (donor_id),
    INDEX idx_disaster (disaster_id),
    INDEX idx_type (donation_type),
    INDEX idx_status (status),
    INDEX idx_date (donation_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Table: volunteers
-- Stores volunteer information and skills
-- ============================================
CREATE TABLE volunteers (
    volunteer_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NOT NULL,
    skills TEXT,
    availability ENUM('full_time', 'part_time', 'weekends', 'on_call') NOT NULL,
    emergency_contact_name VARCHAR(100),
    emergency_contact_phone VARCHAR(20),
    blood_type VARCHAR(5),
    medical_conditions TEXT,
    verification_status ENUM('pending', 'verified', 'rejected') NOT NULL DEFAULT 'pending',
    background_check_date DATE NULL,
    total_hours DECIMAL(10, 2) DEFAULT 0,
    rating DECIMAL(3, 2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_status (verification_status),
    INDEX idx_availability (availability)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Table: volunteer_assignments
-- Tracks volunteer assignments to relief camps
-- ============================================
CREATE TABLE volunteer_assignments (
    assignment_id INT AUTO_INCREMENT PRIMARY KEY,
    volunteer_id INT NOT NULL,
    camp_id INT NOT NULL,
    assigned_by INT,
    role VARCHAR(100),
    start_date DATE NOT NULL,
    end_date DATE NULL,
    hours_worked DECIMAL(10, 2) DEFAULT 0,
    status ENUM('assigned', 'active', 'completed', 'cancelled') NOT NULL DEFAULT 'assigned',
    performance_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (volunteer_id) REFERENCES volunteers(volunteer_id) ON DELETE CASCADE,
    FOREIGN KEY (camp_id) REFERENCES relief_camps(camp_id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_volunteer (volunteer_id),
    INDEX idx_camp (camp_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Table: resource_requests
-- Tracks resource requests from camps
-- ============================================
CREATE TABLE resource_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    camp_id INT NOT NULL,
    requested_by INT NOT NULL,
    resource_type ENUM('food', 'water', 'medicine', 'shelter', 'clothing', 'hygiene', 'other') NOT NULL,
    resource_name VARCHAR(100) NOT NULL,
    quantity_requested DECIMAL(10, 2) NOT NULL,
    unit VARCHAR(20) NOT NULL,
    priority ENUM('low', 'medium', 'high', 'urgent') NOT NULL DEFAULT 'medium',
    status ENUM('pending', 'approved', 'fulfilled', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
    reason TEXT,
    requested_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fulfilled_date TIMESTAMP NULL,
    fulfilled_by INT NULL,
    FOREIGN KEY (camp_id) REFERENCES relief_camps(camp_id) ON DELETE CASCADE,
    FOREIGN KEY (requested_by) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (fulfilled_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_camp (camp_id),
    INDEX idx_status (status),
    INDEX idx_priority (priority)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Table: reports
-- Stores system-generated reports
-- ============================================
CREATE TABLE reports (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    report_type ENUM('disaster_summary', 'resource_inventory', 'donation_summary', 'volunteer_activity', 'camp_status', 'custom') NOT NULL,
    report_title VARCHAR(200) NOT NULL,
    generated_by INT NOT NULL,
    disaster_id INT NULL,
    camp_id INT NULL,
    report_data JSON,
    file_path VARCHAR(255) NULL,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (generated_by) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (disaster_id) REFERENCES disasters(disaster_id) ON DELETE CASCADE,
    FOREIGN KEY (camp_id) REFERENCES relief_camps(camp_id) ON DELETE CASCADE,
    INDEX idx_type (report_type),
    INDEX idx_generated_at (generated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Table: notifications
-- System notifications for users
-- ============================================
CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    notification_type ENUM('alert', 'info', 'warning', 'success') NOT NULL DEFAULT 'info',
    title VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    related_entity_type ENUM('disaster', 'camp', 'donation', 'volunteer', 'resource', 'other') NULL,
    related_entity_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_read (is_read),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Insert Sample Data
-- ============================================

-- Sample Admin User (password: admin123)
INSERT INTO users (username, email, password_hash, full_name, phone, role, status) VALUES
('admin', 'admin@disasterrelief.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', '+1234567890', 'admin', 'active'),
('staff1', 'staff@disasterrelief.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Smith', '+1234567891', 'staff', 'active'),
('volunteer1', 'volunteer@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Doe', '+1234567892', 'volunteer', 'active'),
('donor1', 'donor@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Robert Johnson', '+1234567893', 'donor', 'active');

-- Sample Disasters
INSERT INTO disasters (disaster_name, disaster_type, location, latitude, longitude, severity, description, affected_population, casualties, status, start_date, created_by) VALUES
('Hurricane Maria 2024', 'hurricane', 'Puerto Rico', 18.2208, -66.5901, 'catastrophic', 'Category 5 hurricane causing widespread destruction', 50000, 150, 'ongoing', '2024-12-01 08:00:00', 1),
('California Wildfire', 'wildfire', 'Los Angeles, CA', 34.0522, -118.2437, 'severe', 'Massive wildfire threatening residential areas', 25000, 12, 'active', '2024-12-10 14:30:00', 1),
('Midwest Flooding', 'flood', 'Missouri River Basin', 38.5767, -92.1735, 'moderate', 'Heavy rainfall causing river overflow', 15000, 3, 'monitoring', '2024-11-15 06:00:00', 2);

-- Sample Relief Camps
INSERT INTO relief_camps (disaster_id, camp_name, location, latitude, longitude, capacity, current_occupancy, facilities, status, manager_id, established_date) VALUES
(1, 'San Juan Emergency Shelter', 'San Juan, Puerto Rico', 18.4655, -66.1057, 500, 350, 'Medical tent, Food distribution, Sleeping quarters, Sanitation facilities', 'operational', 2, '2024-12-02'),
(2, 'LA County Relief Center', 'Los Angeles, CA', 34.0522, -118.2437, 300, 180, 'Medical care, Food services, Temporary housing', 'operational', 2, '2024-12-11'),
(3, 'Missouri Flood Relief', 'Jefferson City, MO', 38.5767, -92.1735, 200, 75, 'Basic shelter, Food, Water distribution', 'operational', 2, '2024-11-16');

-- Sample Resources
INSERT INTO resources (camp_id, resource_name, resource_type, quantity, unit, minimum_threshold, status) VALUES
(1, 'Bottled Water', 'water', 5000, 'bottles', 1000, 'adequate'),
(1, 'Ready-to-Eat Meals', 'food', 3000, 'meals', 500, 'adequate'),
(1, 'First Aid Kits', 'medicine', 150, 'kits', 50, 'adequate'),
(1, 'Blankets', 'shelter', 400, 'units', 100, 'adequate'),
(2, 'Bottled Water', 'water', 800, 'bottles', 1000, 'low'),
(2, 'Canned Food', 'food', 1200, 'cans', 500, 'adequate'),
(2, 'Medical Supplies', 'medicine', 50, 'boxes', 20, 'adequate'),
(3, 'Bottled Water', 'water', 300, 'bottles', 500, 'critical'),
(3, 'Emergency Rations', 'food', 400, 'packs', 200, 'adequate');

-- Sample Donations
INSERT INTO donations (donor_id, disaster_id, donation_type, amount, currency, payment_method, status, receipt_number) VALUES
(4, 1, 'monetary', 5000.00, 'USD', 'credit_card', 'completed', 'RCP-2024-001'),
(4, 2, 'monetary', 2500.00, 'USD', 'bank_transfer', 'completed', 'RCP-2024-002');

INSERT INTO donations (donor_id, disaster_id, donation_type, material_description, material_quantity, material_unit, status, receipt_number) VALUES
(4, 1, 'material', 'Bottled Water (24-pack)', 100, 'cases', 'completed', 'RCP-2024-003'),
(4, 3, 'material', 'Blankets and Sleeping Bags', 50, 'units', 'completed', 'RCP-2024-004');

-- Sample Volunteers
INSERT INTO volunteers (user_id, skills, availability, emergency_contact_name, emergency_contact_phone, blood_type, verification_status, total_hours, rating) VALUES
(3, 'Medical training, First aid, CPR certified', 'full_time', 'John Doe', '+1234567899', 'O+', 'verified', 120.5, 4.8);

-- Sample Volunteer Assignments
INSERT INTO volunteer_assignments (volunteer_id, camp_id, assigned_by, role, start_date, hours_worked, status) VALUES
(1, 1, 2, 'Medical Assistant', '2024-12-03', 45.5, 'active'),
(1, 2, 2, 'General Support', '2024-12-12', 12.0, 'active');

-- Sample Resource Requests
INSERT INTO resource_requests (camp_id, requested_by, resource_type, resource_name, quantity_requested, unit, priority, status, reason) VALUES
(2, 2, 'water', 'Bottled Water', 500, 'bottles', 'high', 'pending', 'Current stock below minimum threshold'),
(3, 2, 'water', 'Bottled Water', 1000, 'bottles', 'urgent', 'approved', 'Critical shortage - immediate need');

-- Sample Notifications
INSERT INTO notifications (user_id, notification_type, title, message, related_entity_type, related_entity_id) VALUES
(2, 'warning', 'Low Resource Alert', 'Water supply at LA County Relief Center is below minimum threshold', 'resource', 5),
(2, 'alert', 'Critical Resource Shortage', 'Water at Missouri Flood Relief is critically low', 'resource', 8),
(1, 'info', 'New Donation Received', 'A donation of $5,000 has been received for Hurricane Maria relief', 'donation', 1);

-- ============================================
-- Views for Common Queries
-- ============================================

-- Active disasters with camp count
CREATE VIEW active_disasters_summary AS
SELECT 
    d.disaster_id,
    d.disaster_name,
    d.disaster_type,
    d.location,
    d.severity,
    d.affected_population,
    d.status,
    COUNT(rc.camp_id) as total_camps,
    SUM(rc.current_occupancy) as total_occupancy
FROM disasters d
LEFT JOIN relief_camps rc ON d.disaster_id = rc.disaster_id
WHERE d.status IN ('active', 'ongoing')
GROUP BY d.disaster_id;

-- Resource inventory summary
CREATE VIEW resource_inventory_summary AS
SELECT 
    rc.camp_name,
    r.resource_type,
    COUNT(r.resource_id) as item_count,
    SUM(CASE WHEN r.status = 'critical' THEN 1 ELSE 0 END) as critical_items,
    SUM(CASE WHEN r.status = 'low' THEN 1 ELSE 0 END) as low_items
FROM relief_camps rc
LEFT JOIN resources r ON rc.camp_id = r.camp_id
GROUP BY rc.camp_id, r.resource_type;

-- Donation summary by disaster
CREATE VIEW donation_summary AS
SELECT 
    d.disaster_name,
    COUNT(CASE WHEN don.donation_type = 'monetary' THEN 1 END) as monetary_donations,
    SUM(CASE WHEN don.donation_type = 'monetary' THEN don.amount ELSE 0 END) as total_amount,
    COUNT(CASE WHEN don.donation_type = 'material' THEN 1 END) as material_donations
FROM disasters d
LEFT JOIN donations don ON d.disaster_id = don.disaster_id
WHERE don.status = 'completed'
GROUP BY d.disaster_id;

-- ============================================
-- Stored Procedures
-- ============================================

DELIMITER //

-- Procedure to update resource status based on quantity
CREATE PROCEDURE update_resource_status(IN res_id INT)
BEGIN
    DECLARE qty DECIMAL(10,2);
    DECLARE threshold DECIMAL(10,2);
    
    SELECT quantity, minimum_threshold INTO qty, threshold
    FROM resources WHERE resource_id = res_id;
    
    IF qty = 0 THEN
        UPDATE resources SET status = 'out_of_stock' WHERE resource_id = res_id;
    ELSEIF qty <= threshold * 0.5 THEN
        UPDATE resources SET status = 'critical' WHERE resource_id = res_id;
    ELSEIF qty <= threshold THEN
        UPDATE resources SET status = 'low' WHERE resource_id = res_id;
    ELSE
        UPDATE resources SET status = 'adequate' WHERE resource_id = res_id;
    END IF;
END //

-- Procedure to calculate volunteer total hours
CREATE PROCEDURE calculate_volunteer_hours(IN vol_id INT)
BEGIN
    UPDATE volunteers v
    SET total_hours = (
        SELECT COALESCE(SUM(hours_worked), 0)
        FROM volunteer_assignments
        WHERE volunteer_id = vol_id
    )
    WHERE volunteer_id = vol_id;
END //

DELIMITER ;

-- ============================================
-- Triggers
-- ============================================

DELIMITER //

-- Trigger to update camp occupancy status
CREATE TRIGGER update_camp_status AFTER UPDATE ON relief_camps
FOR EACH ROW
BEGIN
    IF NEW.current_occupancy >= NEW.capacity THEN
        UPDATE relief_camps SET status = 'full' WHERE camp_id = NEW.camp_id;
    ELSEIF NEW.current_occupancy < NEW.capacity AND NEW.status = 'full' THEN
        UPDATE relief_camps SET status = 'operational' WHERE camp_id = NEW.camp_id;
    END IF;
END //

-- Trigger to generate receipt number for donations
CREATE TRIGGER generate_receipt_number BEFORE INSERT ON donations
FOR EACH ROW
BEGIN
    IF NEW.receipt_number IS NULL THEN
        SET NEW.receipt_number = CONCAT('RCP-', YEAR(NOW()), '-', LPAD(FLOOR(RAND() * 99999), 5, '0'));
    END IF;
END //

DELIMITER ;

-- ============================================
-- End of Database Schema
-- ============================================
