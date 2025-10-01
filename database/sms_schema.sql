-- SMS Management Database Schema
-- SMS notifications and logs

-- SMS Logs table
CREATE TABLE IF NOT EXISTS sms_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipient_phone VARCHAR(20) NOT NULL,
    recipient_name VARCHAR(255),
    message TEXT NOT NULL,
    status ENUM('pending', 'sent', 'failed', 'delivered') DEFAULT 'pending',
    provider VARCHAR(50) DEFAULT 'textbelt',
    provider_response TEXT,
    error_message TEXT,
    cost DECIMAL(10,4) DEFAULT 0.0000,
    sent_by INT,
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sent_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_recipient_phone (recipient_phone),
    INDEX idx_status (status),
    INDEX idx_sent_at (sent_at),
    INDEX idx_created_at (created_at)
);

-- SMS Templates table
CREATE TABLE IF NOT EXISTS sms_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    template_key VARCHAR(50) UNIQUE NOT NULL,
    message_template TEXT NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_template_key (template_key),
    INDEX idx_active (is_active)
);

-- SMS Settings table
CREATE TABLE IF NOT EXISTS sms_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_setting_key (setting_key)
);

-- Insert default SMS templates
INSERT INTO sms_templates (name, template_key, message_template, description) VALUES
('Test Report Ready', 'report_ready', 'Dear {patient_name}, your test report for {test_name} is ready. Please collect it from SEL Diagnostic Center. Invoice: {invoice_id}', 'Notification when test report is ready'),
('Appointment Reminder', 'appointment_reminder', 'Dear {patient_name}, reminder for your appointment on {date} at {time}. SEL Diagnostic Center. Ph: {lab_phone}', 'Appointment reminder notification'),
('Payment Received', 'payment_received', 'Dear {patient_name}, payment of ৳{amount} received. Thank you for choosing SEL Diagnostic Center. Invoice: {invoice_id}', 'Payment confirmation'),
('Invoice Created', 'invoice_created', 'Dear {patient_name}, invoice #{invoice_id} created for ৳{amount}. Please visit us for sample collection. SEL Diagnostic Center', 'New invoice notification')
ON DUPLICATE KEY UPDATE name = name;

-- Insert default SMS settings
INSERT INTO sms_settings (setting_key, setting_value, description) VALUES
('sms_enabled', 'true', 'Enable/disable SMS notifications'),
('default_provider', 'textbelt', 'Default SMS provider'),
('auto_notify_report_ready', 'true', 'Automatically notify when report is ready'),
('auto_notify_payment', 'true', 'Automatically notify on payment'),
('sender_name', 'SEL Diagnostic', 'SMS sender name'),
('lab_phone', '+8801XXXXXXXXX', 'Laboratory contact phone')
ON DUPLICATE KEY UPDATE setting_key = setting_key;

-- Sample SMS logs (for testing)
INSERT INTO sms_logs (recipient_phone, recipient_name, message, status, sent_by, sent_at) VALUES
('+8801712345678', 'আব্দুর রহমান', 'Your test report is ready for collection.', 'sent', 1, NOW() - INTERVAL 2 DAY),
('+8801812345679', 'ফাতিমা বেগম', 'Payment received. Thank you!', 'delivered', 1, NOW() - INTERVAL 1 DAY),
('+8801912345680', 'করিম আহমেদ', 'Appointment reminder for tomorrow.', 'sent', 1, NOW() - INTERVAL 3 HOUR);