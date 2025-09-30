-- Core Database Schema for Base System
-- Part 1: Core System & Authentication (Project Lead)

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS pathology_lab CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pathology_lab;

-- Core users table (base structure)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(20),
    role ENUM('admin', 'staff', 'technician') DEFAULT 'staff',
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    password_reset_token VARCHAR(255) NULL,
    password_reset_expires TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_role (role),
    INDEX idx_active (is_active)
);

-- Insert default admin user
INSERT INTO users (username, password, full_name, email, role, is_active) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin@lab.com', 'admin', TRUE)
ON DUPLICATE KEY UPDATE username = username;

-- System configuration table
CREATE TABLE IF NOT EXISTS system_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    config_key VARCHAR(100) UNIQUE NOT NULL,
    config_value TEXT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (config_key)
);

-- Insert default system configuration
INSERT INTO system_config (config_key, config_value, description) VALUES
('lab_name', 'Pathology Laboratory Management System', 'Laboratory name'),
('lab_address', 'Dhaka, Bangladesh', 'Laboratory address'),
('lab_phone', '+8801XXXXXXXXX', 'Laboratory contact phone'),
('lab_email', 'info@lab.com', 'Laboratory contact email'),
('currency_symbol', '৳', 'Currency symbol'),
('timezone', 'Asia/Dhaka', 'System timezone'),
('date_format', 'd/m/Y', 'Date display format'),
('invoice_prefix', 'INV-', 'Invoice number prefix'),
('report_prefix', 'RPT-', 'Report number prefix')
ON DUPLICATE KEY UPDATE config_key = config_key;

-- Session management table
CREATE TABLE IF NOT EXISTS user_sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_last_activity (last_activity)
);

-- System logs table
CREATE TABLE IF NOT EXISTS system_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    level ENUM('DEBUG', 'INFO', 'WARNING', 'ERROR', 'CRITICAL') DEFAULT 'INFO',
    message TEXT NOT NULL,
    context JSON,
    user_id INT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_level (level),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
);

-- Create indexes for better performance
CREATE INDEX idx_users_email ON users (email);
CREATE INDEX idx_users_last_login ON users (last_login);
CREATE INDEX idx_system_config_updated ON system_config (updated_at);

-- Create views for common queries
CREATE OR REPLACE VIEW active_users AS
SELECT id, username, full_name, email, phone, role, last_login, created_at
FROM users 
WHERE is_active = TRUE;

CREATE OR REPLACE VIEW user_login_stats AS
SELECT 
    role,
    COUNT(*) as total_users,
    COUNT(CASE WHEN last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as active_last_30_days,
    COUNT(CASE WHEN last_login >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as active_last_7_days
FROM users 
WHERE is_active = TRUE
GROUP BY role;
