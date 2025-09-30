-- Audit Logging Database Schema
-- System audit trail and activity logging

-- Audit Logs table
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    username VARCHAR(100),
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(100),
    record_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_table_name (table_name),
    INDEX idx_created_at (created_at)
);

-- Insert sample audit logs
INSERT INTO audit_logs (user_id, username, action, table_name, record_id, ip_address, created_at) VALUES
(1, 'admin', 'login', 'users', 1, '127.0.0.1', NOW() - INTERVAL 2 HOUR),
(1, 'admin', 'create', 'invoices', 1, '127.0.0.1', NOW() - INTERVAL 1 HOUR),
(1, 'admin', 'update', 'invoices', 1, '127.0.0.1', NOW() - INTERVAL 45 MINUTE),
(1, 'admin', 'create', 'test_reports', 1, '127.0.0.1', NOW() - INTERVAL 30 MINUTE),
(1, 'admin', 'view', 'reports', 1, '127.0.0.1', NOW() - INTERVAL 15 MINUTE),
(1, 'admin', 'login', 'users', 1, '127.0.0.1', NOW() - INTERVAL 5 MINUTE);