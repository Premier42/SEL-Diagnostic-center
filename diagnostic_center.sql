-- ============================================
-- SEL Diagnostic Center - Database Schema
-- ============================================
-- This file creates all database tables and structures
-- Run this file FIRST before any other SQL files

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS pathology_lab CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pathology_lab;

-- ============================================
-- CORE TABLES
-- ============================================

-- Users table
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
    INDEX idx_active (is_active),
    INDEX idx_email (email),
    INDEX idx_last_login (last_login)
);

-- System configuration table
CREATE TABLE IF NOT EXISTS system_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    config_key VARCHAR(100) UNIQUE NOT NULL,
    config_value TEXT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (config_key),
    INDEX idx_updated (updated_at)
);

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

-- ============================================
-- MEDICAL TABLES
-- ============================================

-- Tests table
CREATE TABLE IF NOT EXISTS tests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    price DECIMAL(8,2) NOT NULL,
    description TEXT,
    procedure_notes TEXT,
    sample_type VARCHAR(100),
    turnaround_time VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_category (category),
    INDEX idx_active (is_active)
);

-- Test Parameters table
CREATE TABLE IF NOT EXISTS test_parameters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    test_code VARCHAR(50) NOT NULL,
    parameter_name VARCHAR(255) NOT NULL,
    unit VARCHAR(50),
    normal_range_min DECIMAL(10,3),
    normal_range_max DECIMAL(10,3),
    normal_range_text VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (test_code) REFERENCES tests(code) ON DELETE CASCADE,
    INDEX idx_test_code (test_code),
    INDEX idx_active (is_active)
);

-- Doctors table
CREATE TABLE IF NOT EXISTS doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    qualifications VARCHAR(500),
    specialization VARCHAR(255),
    workplace VARCHAR(255),
    phone VARCHAR(20),
    email VARCHAR(255),
    address TEXT,
    license_number VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name),
    INDEX idx_specialization (specialization),
    INDEX idx_active (is_active),
    INDEX idx_phone (phone)
);

-- ============================================
-- INVOICE TABLES
-- ============================================

-- Invoices table
CREATE TABLE IF NOT EXISTS invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_name VARCHAR(255) NOT NULL,
    patient_age INT,
    patient_gender ENUM('Male', 'Female', 'Other'),
    patient_phone VARCHAR(20),
    patient_email VARCHAR(255),
    patient_address TEXT,
    doctor_id INT,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    discount_amount DECIMAL(10,2) DEFAULT 0.00,
    amount_paid DECIMAL(10,2) DEFAULT 0.00,
    payment_status ENUM('pending', 'partial', 'paid') DEFAULT 'pending',
    payment_method VARCHAR(50),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE SET NULL,
    INDEX idx_patient_name (patient_name),
    INDEX idx_patient_phone (patient_phone),
    INDEX idx_payment_status (payment_status),
    INDEX idx_created_at (created_at),
    INDEX idx_doctor_id (doctor_id),
    INDEX idx_total_amount (total_amount)
);

-- Invoice Tests junction table
CREATE TABLE IF NOT EXISTS invoice_tests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    test_code VARCHAR(50) NOT NULL,
    test_name VARCHAR(255) NOT NULL,
    price DECIMAL(8,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    INDEX idx_invoice_id (invoice_id),
    INDEX idx_test_code (test_code),
    INDEX idx_price (price)
);

-- Test Reports table
CREATE TABLE IF NOT EXISTS test_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    test_code VARCHAR(50) NOT NULL,
    status ENUM('pending', 'in_progress', 'completed', 'verified') DEFAULT 'pending',
    technician_id INT,
    verified_by INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (test_code) REFERENCES tests(code),
    INDEX idx_invoice_id (invoice_id),
    INDEX idx_test_code (test_code),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Test Results table
CREATE TABLE IF NOT EXISTS test_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_id INT NOT NULL,
    parameter_name VARCHAR(255) NOT NULL,
    value VARCHAR(255),
    unit VARCHAR(50),
    normal_range VARCHAR(255),
    is_abnormal BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (report_id) REFERENCES test_reports(id) ON DELETE CASCADE,
    INDEX idx_report_id (report_id),
    INDEX idx_abnormal (is_abnormal),
    INDEX idx_active (is_active),
    INDEX idx_value (value)
);

-- ============================================
-- SMS TABLES
-- ============================================

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

-- ============================================
-- AUDIT TABLES
-- ============================================

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

-- ============================================
-- INVENTORY TABLES
-- ============================================

-- Inventory Items table
CREATE TABLE IF NOT EXISTS inventory_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_code VARCHAR(50) UNIQUE NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    category ENUM('reagent', 'consumable', 'equipment', 'other') DEFAULT 'consumable',
    description TEXT,
    unit VARCHAR(50) DEFAULT 'piece',
    quantity_in_stock INT DEFAULT 0,
    minimum_stock_level INT DEFAULT 10,
    reorder_level INT DEFAULT 20,
    unit_price DECIMAL(10,2) DEFAULT 0.00,
    supplier VARCHAR(255),
    supplier_contact VARCHAR(100),
    storage_location VARCHAR(100),
    expiry_date DATE NULL,
    last_restocked_at TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_item_code (item_code),
    INDEX idx_category (category),
    INDEX idx_quantity (quantity_in_stock),
    INDEX idx_active (is_active)
);

-- Inventory Transactions table
CREATE TABLE IF NOT EXISTS inventory_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    transaction_type ENUM('in', 'out', 'adjustment', 'expired', 'damaged') NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) DEFAULT 0.00,
    total_value DECIMAL(10,2) DEFAULT 0.00,
    reference_type VARCHAR(50) COMMENT 'invoice, test_report, purchase_order, etc',
    reference_id INT,
    notes TEXT,
    performed_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES inventory_items(id) ON DELETE CASCADE,
    FOREIGN KEY (performed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_item_id (item_id),
    INDEX idx_transaction_type (transaction_type),
    INDEX idx_created_at (created_at)
);

-- ============================================
-- VIEWS
-- ============================================

-- Active users view
CREATE OR REPLACE VIEW active_users AS
SELECT id, username, full_name, email, phone, role, last_login, created_at
FROM users
WHERE is_active = TRUE;

-- User login stats view
CREATE OR REPLACE VIEW user_login_stats AS
SELECT
    role,
    COUNT(*) as total_users,
    COUNT(CASE WHEN last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as active_last_30_days,
    COUNT(CASE WHEN last_login >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as active_last_7_days
FROM users
WHERE is_active = TRUE
GROUP BY role;

-- ============================================
-- SCHEMA CREATION COMPLETE
-- ============================================
SELECT 'Database schema created successfully!' as 'Status';
-- ============================================
-- SEL Diagnostic Center - Initial Essential Data
-- ============================================
-- This file inserts essential data needed for the system to function
-- Run this file SECOND after 1_schema.sql

USE pathology_lab;

-- ============================================
-- ADMIN USER
-- ============================================
-- Default admin user (password: password)
INSERT INTO users (username, password, full_name, email, role, is_active) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin@lab.com', 'admin', TRUE)
ON DUPLICATE KEY UPDATE username = username;

-- ============================================
-- SYSTEM CONFIGURATION
-- ============================================
INSERT INTO system_config (config_key, config_value, description) VALUES
('lab_name', 'SEL Diagnostic Center', 'Laboratory name'),
('lab_address', 'Dhaka, Bangladesh', 'Laboratory address'),
('lab_phone', '+8801XXXXXXXXX', 'Laboratory contact phone'),
('lab_email', 'info@seldiagnostic.com', 'Laboratory contact email'),
('currency_symbol', '৳', 'Currency symbol'),
('timezone', 'Asia/Dhaka', 'System timezone'),
('date_format', 'd/m/Y', 'Date display format'),
('invoice_prefix', 'INV-', 'Invoice number prefix'),
('report_prefix', 'RPT-', 'Report number prefix')
ON DUPLICATE KEY UPDATE config_key = config_key;

-- ============================================
-- MEDICAL TESTS CATALOG
-- ============================================
INSERT INTO tests (code, name, category, price, description, sample_type, turnaround_time, is_active) VALUES
-- Hematology
('CBC', 'Complete Blood Count', 'Hematology', 800.00, 'Complete blood count with differential', 'Whole Blood', '4-6 hours', TRUE),
('ESR', 'Erythrocyte Sedimentation Rate', 'Hematology', 400.00, 'Inflammation marker', 'Whole Blood', '2-4 hours', TRUE),
('HB', 'Hemoglobin', 'Hematology', 300.00, 'Hemoglobin level measurement', 'Whole Blood', '2-4 hours', TRUE),
('PCV', 'Packed Cell Volume', 'Hematology', 350.00, 'Hematocrit measurement', 'Whole Blood', '2-4 hours', TRUE),
('TC-DC', 'Total Count & Differential Count', 'Hematology', 600.00, 'WBC count with differential', 'Whole Blood', '4-6 hours', TRUE),
('PLT', 'Platelet Count', 'Hematology', 500.00, 'Thrombocyte count', 'Whole Blood', '4-6 hours', TRUE),
('BT-CT', 'Bleeding Time & Clotting Time', 'Hematology', 450.00, 'Coagulation screening', 'Whole Blood', '2-4 hours', TRUE),
('PT-INR', 'Prothrombin Time/INR', 'Hematology', 650.00, 'Anticoagulation monitoring', 'Plasma', '4-6 hours', TRUE),

-- Biochemistry
('FBS', 'Fasting Blood Sugar', 'Biochemistry', 300.00, 'Fasting glucose level', 'Serum', '2-4 hours', TRUE),
('PPBS', 'Post Prandial Blood Sugar', 'Biochemistry', 350.00, 'Post-meal glucose level', 'Serum', '2-4 hours', TRUE),
('RBS', 'Random Blood Sugar', 'Biochemistry', 280.00, 'Random glucose measurement', 'Serum', '2-4 hours', TRUE),
('HbA1c', 'Glycated Hemoglobin', 'Biochemistry', 1200.00, 'Diabetes monitoring test', 'Whole Blood', '6-8 hours', TRUE),
('LFT', 'Liver Function Test', 'Biochemistry', 1500.00, 'Comprehensive liver function panel', 'Serum', '6-8 hours', TRUE),
('KFT', 'Kidney Function Test', 'Biochemistry', 1200.00, 'Kidney function assessment', 'Serum', '6-8 hours', TRUE),
('LIPID', 'Lipid Profile', 'Biochemistry', 1400.00, 'Complete lipid panel', 'Serum', '6-8 hours', TRUE),
('S.CREATININE', 'Serum Creatinine', 'Biochemistry', 400.00, 'Kidney function marker', 'Serum', '4-6 hours', TRUE),
('S.URIC ACID', 'Serum Uric Acid', 'Biochemistry', 450.00, 'Gout screening', 'Serum', '4-6 hours', TRUE),
('S.CALCIUM', 'Serum Calcium', 'Biochemistry', 500.00, 'Calcium level measurement', 'Serum', '4-6 hours', TRUE),

-- Endocrinology
('TSH', 'Thyroid Stimulating Hormone', 'Endocrinology', 800.00, 'Thyroid function screening', 'Serum', '8-12 hours', TRUE),
('T3', 'Triiodothyronine', 'Endocrinology', 900.00, 'Thyroid hormone', 'Serum', '8-12 hours', TRUE),
('T4', 'Thyroxine', 'Endocrinology', 900.00, 'Thyroid hormone', 'Serum', '8-12 hours', TRUE),
('FT3', 'Free T3', 'Endocrinology', 1100.00, 'Free triiodothyronine', 'Serum', '8-12 hours', TRUE),
('FT4', 'Free T4', 'Endocrinology', 1100.00, 'Free thyroxine', 'Serum', '8-12 hours', TRUE),

-- Immunology
('CRP', 'C-Reactive Protein', 'Immunology', 800.00, 'Acute phase reactant', 'Serum', '4-6 hours', TRUE),
('ASO', 'Anti-Streptolysin O', 'Immunology', 850.00, 'Streptococcal infection marker', 'Serum', '6-8 hours', TRUE),
('RA', 'Rheumatoid Arthritis Factor', 'Immunology', 900.00, 'RA screening', 'Serum', '6-8 hours', TRUE),
('HIV', 'HIV Screening', 'Immunology', 1200.00, 'HIV antibody test', 'Serum', '12-24 hours', TRUE),
('HBsAg', 'Hepatitis B Surface Antigen', 'Immunology', 800.00, 'Hepatitis B screening', 'Serum', '6-8 hours', TRUE),
('Anti-HCV', 'Hepatitis C Antibody', 'Immunology', 1000.00, 'Hepatitis C screening', 'Serum', '6-8 hours', TRUE),

-- Microbiology
('URINE-C/S', 'Urine Culture & Sensitivity', 'Microbiology', 1200.00, 'Urine bacterial culture', 'Urine', '48-72 hours', TRUE),
('BLOOD-C/S', 'Blood Culture & Sensitivity', 'Microbiology', 1800.00, 'Blood bacterial culture', 'Blood', '48-72 hours', TRUE),
('STOOL-R/E', 'Stool Routine Examination', 'Microbiology', 400.00, 'Stool microscopy', 'Stool', '4-6 hours', TRUE),

-- Clinical Pathology
('URINE-R/E', 'Urine Routine Examination', 'Clinical Pathology', 350.00, 'Urine analysis', 'Urine', '2-4 hours', TRUE),
('URINE-ALBUMIN', 'Urine Albumin', 'Clinical Pathology', 300.00, 'Urine protein test', 'Urine', '2-4 hours', TRUE),
('URINE-SUGAR', 'Urine Sugar', 'Clinical Pathology', 250.00, 'Urine glucose test', 'Urine', '2-4 hours', TRUE)
ON DUPLICATE KEY UPDATE code = code;

-- ============================================
-- TEST PARAMETERS (for key tests)
-- ============================================
INSERT INTO test_parameters (test_code, parameter_name, unit, normal_range_min, normal_range_max, normal_range_text, is_active) VALUES
-- CBC Parameters
('CBC', 'Hemoglobin', 'g/dL', 12.0, 16.0, '12.0-16.0 g/dL (Female), 13.5-17.5 g/dL (Male)', TRUE),
('CBC', 'RBC Count', 'million/μL', 4.0, 5.5, '4.0-5.5 million/μL (Female), 4.5-6.0 million/μL (Male)', TRUE),
('CBC', 'WBC Count', 'thousand/μL', 4.0, 11.0, '4.0-11.0 thousand/μL', TRUE),
('CBC', 'Platelet Count', 'thousand/μL', 150.0, 450.0, '150-450 thousand/μL', TRUE),
('CBC', 'Neutrophils', '%', 40.0, 75.0, '40-75%', TRUE),
('CBC', 'Lymphocytes', '%', 20.0, 45.0, '20-45%', TRUE),
('CBC', 'Monocytes', '%', 2.0, 10.0, '2-10%', TRUE),
('CBC', 'Eosinophils', '%', 1.0, 6.0, '1-6%', TRUE),

-- LFT Parameters
('LFT', 'ALT (SGPT)', 'U/L', 7.0, 35.0, '7-35 U/L', TRUE),
('LFT', 'AST (SGOT)', 'U/L', 8.0, 40.0, '8-40 U/L', TRUE),
('LFT', 'Alkaline Phosphatase', 'U/L', 30.0, 120.0, '30-120 U/L', TRUE),
('LFT', 'Total Bilirubin', 'mg/dL', 0.2, 1.2, '0.2-1.2 mg/dL', TRUE),
('LFT', 'Direct Bilirubin', 'mg/dL', 0.0, 0.3, '0.0-0.3 mg/dL', TRUE),
('LFT', 'Total Protein', 'g/dL', 6.0, 8.3, '6.0-8.3 g/dL', TRUE),
('LFT', 'Albumin', 'g/dL', 3.5, 5.5, '3.5-5.5 g/dL', TRUE),

-- KFT Parameters
('KFT', 'Blood Urea', 'mg/dL', 10.0, 50.0, '10-50 mg/dL', TRUE),
('KFT', 'Serum Creatinine', 'mg/dL', 0.6, 1.2, '0.6-1.2 mg/dL', TRUE),
('KFT', 'Uric Acid', 'mg/dL', 2.5, 7.0, '2.5-7.0 mg/dL', TRUE),

-- Lipid Profile Parameters
('LIPID', 'Total Cholesterol', 'mg/dL', 0.0, 200.0, '<200 mg/dL (Desirable)', TRUE),
('LIPID', 'HDL Cholesterol', 'mg/dL', 40.0, 999.0, '>40 mg/dL (Male), >50 mg/dL (Female)', TRUE),
('LIPID', 'LDL Cholesterol', 'mg/dL', 0.0, 100.0, '<100 mg/dL (Optimal)', TRUE),
('LIPID', 'Triglycerides', 'mg/dL', 0.0, 150.0, '<150 mg/dL (Normal)', TRUE),
('LIPID', 'VLDL Cholesterol', 'mg/dL', 0.0, 30.0, '<30 mg/dL', TRUE)
ON DUPLICATE KEY UPDATE test_code = test_code;

-- ============================================
-- SMS TEMPLATES
-- ============================================
INSERT INTO sms_templates (name, template_key, message_template, description) VALUES
('Test Report Ready', 'report_ready', 'Dear {patient_name}, your test report for {test_name} is ready. Please collect it from SEL Diagnostic Center. Invoice: {invoice_id}', 'Notification when test report is ready'),
('Appointment Reminder', 'appointment_reminder', 'Dear {patient_name}, reminder for your appointment on {date} at {time}. SEL Diagnostic Center. Ph: {lab_phone}', 'Appointment reminder notification'),
('Payment Received', 'payment_received', 'Dear {patient_name}, payment of ৳{amount} received. Thank you for choosing SEL Diagnostic Center. Invoice: {invoice_id}', 'Payment confirmation'),
('Invoice Created', 'invoice_created', 'Dear {patient_name}, invoice #{invoice_id} created for ৳{amount}. Please visit us for sample collection. SEL Diagnostic Center', 'New invoice notification')
ON DUPLICATE KEY UPDATE template_key = template_key;

-- ============================================
-- SMS SETTINGS
-- ============================================
INSERT INTO sms_settings (setting_key, setting_value, description) VALUES
('sms_enabled', 'true', 'Enable/disable SMS notifications'),
('default_provider', 'smsnetbd', 'Default SMS provider'),
('auto_notify_report_ready', 'true', 'Automatically notify when report is ready'),
('auto_notify_payment', 'true', 'Automatically notify on payment'),
('sender_name', 'SEL Diagnostic', 'SMS sender name'),
('lab_phone', '+8801XXXXXXXXX', 'Laboratory contact phone')
ON DUPLICATE KEY UPDATE setting_key = setting_key;

-- ============================================
-- INITIAL DATA LOADED
-- ============================================
SELECT 'Essential data loaded successfully!' as 'Status';
SELECT 'Default login: admin / password' as 'Important';
-- Comprehensive Seed Data for SEL Diagnostic Center
-- This file populates the database with realistic test data

USE pathology_lab;

-- Clear existing data (except admin user)
DELETE FROM inventory_transactions WHERE id > 0;
DELETE FROM inventory_items WHERE id > 0;
DELETE FROM audit_logs WHERE id > 0;
DELETE FROM sms_logs WHERE id > 0;
DELETE FROM test_results WHERE id > 0;
DELETE FROM test_reports WHERE id > 0;
DELETE FROM invoice_tests WHERE id > 0;
DELETE FROM invoices WHERE id > 0;
DELETE FROM test_parameters WHERE id > 0;
DELETE FROM tests WHERE id > 0;
DELETE FROM doctors WHERE id > 0;
DELETE FROM users WHERE id > 1;

-- Reset auto increment
ALTER TABLE invoices AUTO_INCREMENT = 1;
ALTER TABLE test_reports AUTO_INCREMENT = 1;
ALTER TABLE doctors AUTO_INCREMENT = 1;
ALTER TABLE tests AUTO_INCREMENT = 1;

-- ============================================
-- USERS (Additional staff members)
-- ============================================
INSERT INTO users (username, password, full_name, email, phone, role, is_active) VALUES
('staff01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'রহিমা খাতুন', 'rahima@sel.com', '01712345001', 'staff', TRUE),
('staff02', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'করিম আহমেদ', 'karim@sel.com', '01812345002', 'staff', TRUE),
('tech01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'নাসিমা আক্তার', 'nasima@sel.com', '01912345003', 'technician', TRUE),
('tech02', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'তানভীর হাসান', 'tanvir@sel.com', '01612345004', 'technician', TRUE);

-- ============================================
-- DOCTORS (20 doctors with specializations)
-- ============================================
INSERT INTO doctors (name, qualifications, specialization, workplace, phone, email, license_number, is_active) VALUES
('ডা. মোহাম্মদ রহমান', 'MBBS, MD (Internal Medicine)', 'Internal Medicine', 'ঢাকা মেডিকেল কলেজ হাসপাতাল', '+8801712345101', 'dr.rahman@dmc.gov.bd', 'BMA-10001', TRUE),
('ডা. ফাতিমা খাতুন', 'MBBS, FCPS (Gynecology)', 'Gynecology & Obstetrics', 'বঙ্গবন্ধু শেখ মুজিব মেডিকেল বিশ্ববিদ্যালয়', '+8801812345102', 'dr.fatima@bsmmu.edu.bd', 'BMA-10002', TRUE),
('ডা. আব্দুল করিম', 'MBBS, MS (Surgery)', 'General Surgery', 'চট্টগ্রাম মেডিকেল কলেজ হাসপাতাল', '+8801912345103', 'dr.karim@cmc.gov.bd', 'BMA-10003', TRUE),
('ডা. নাসরিন আক্তার', 'MBBS, DCH (Pediatrics)', 'Pediatrics', 'শিশু হাসপাতাল, ঢাকা', '+8801612345104', 'dr.nasrin@shishu.gov.bd', 'BMA-10004', TRUE),
('ডা. তারিক হাসান', 'MBBS, DCard (Cardiology)', 'Cardiology', 'জাতীয় হৃদরোগ ইনস্টিটিউট', '+8801512345105', 'dr.tarik@nhf.gov.bd', 'BMA-10005', TRUE),
('ডা. সালমা বেগম', 'MBBS, MD (Medicine)', 'Medicine', 'স্যার সলিমুল্লাহ মেডিকেল কলেজ', '+8801412345106', 'dr.salma@ssmc.gov.bd', 'BMA-10006', TRUE),
('ডা. রফিকুল ইসলাম', 'MBBS, FCPS (Orthopedics)', 'Orthopedics', 'ঢাকা অর্থোপেডিক হাসপাতাল', '+8801312345107', 'dr.rafiq@ortho.gov.bd', 'BMA-10007', TRUE),
('ডা. রোকেয়া সুলতানা', 'MBBS, FCPS (Dermatology)', 'Dermatology', 'বারডেম হাসপাতাল', '+8801212345108', 'dr.rokeya@bardem.org', 'BMA-10008', TRUE),
('ডা. জাহিদ হাসান', 'MBBS, FCPS (Neurology)', 'Neurology', 'জাতীয় নিউরোসায়েন্স ইনস্টিটিউট', '+8801112345109', 'dr.jahid@nni.gov.bd', 'BMA-10009', TRUE),
('ডা. শামীমা আক্তার', 'MBBS, MD (Endocrinology)', 'Endocrinology', 'বারডেম হাসপাতাল', '+8801712345110', 'dr.shamima@bardem.org', 'BMA-10010', TRUE),
('ডা. আলমগীর হোসেন', 'MBBS, FCPS (Gastroenterology)', 'Gastroenterology', 'মুগদা মেডিকেল কলেজ', '+8801812345111', 'dr.alamgir@mugda.gov.bd', 'BMA-10011', TRUE),
('ডা. পারভীন সুলতানা', 'MBBS, FCPS (Ophthalmology)', 'Ophthalmology', 'জাতীয় চক্ষু বিজ্ঞান ইনস্টিটিউট', '+8801912345112', 'dr.parvin@nio.gov.bd', 'BMA-10012', TRUE),
('ডা. কামরুল হাসান', 'MBBS, MS (ENT)', 'ENT', 'ঢাকা মেডিকেল কলেজ', '+8801612345113', 'dr.kamrul@dmc.gov.bd', 'BMA-10013', TRUE),
('ডা. নাজমা বেগম', 'MBBS, FCPS (Pathology)', 'Pathology', 'বঙ্গবন্ধু শেখ মুজিব মেডিকেল বিশ্ববিদ্যালয়', '+8801512345114', 'dr.najma@bsmmu.edu.bd', 'BMA-10014', TRUE),
('ডা. ইমরান খান', 'MBBS, FCPS (Radiology)', 'Radiology', 'ইউনাইটেড হাসপাতাল', '+8801412345115', 'dr.imran@united.com.bd', 'BMA-10015', TRUE),
('ডা. তাসলিমা আক্তার', 'MBBS, FCPS (Psychiatry)', 'Psychiatry', 'জাতীয় মানসিক স্বাস্থ্য ইনস্টিটিউট', '+8801312345116', 'dr.taslima@nimh.gov.bd', 'BMA-10016', TRUE),
('ডা. মাহমুদুল হক', 'MBBS, MD (Nephrology)', 'Nephrology', 'কিডনি ফাউন্ডেশন', '+8801212345117', 'dr.mahmud@kidney.org.bd', 'BMA-10017', TRUE),
('ডা. সাবিনা ইয়াসমিন', 'MBBS, FCPS (Anesthesiology)', 'Anesthesiology', 'চট্টগ্রাম মেডিকেল কলেজ', '+8801112345118', 'dr.sabina@cmc.gov.bd', 'BMA-10018', TRUE),
('ডা. রাশেদ কবির', 'MBBS, FCPS (Urology)', 'Urology', 'মুগদা মেডিকেল কলেজ', '+8801712345119', 'dr.rashed@mugda.gov.bd', 'BMA-10019', TRUE),
('ডা. নাহিদা পারভীন', 'MBBS, MD (Hematology)', 'Hematology', 'বঙ্গবন্ধু শেখ মুজিব মেডিকেল বিশ্ববিদ্যালয়', '+8801812345120', 'dr.nahida@bsmmu.edu.bd', 'BMA-10020', TRUE);

-- ============================================
-- TESTS (Comprehensive test list)
-- ============================================
INSERT INTO tests (code, name, category, price, description, sample_type, turnaround_time, is_active) VALUES
-- Hematology
('CBC', 'Complete Blood Count', 'Hematology', 800.00, 'Complete blood count with differential', 'Whole Blood', '4-6 hours', TRUE),
('ESR', 'Erythrocyte Sedimentation Rate', 'Hematology', 400.00, 'Inflammation marker', 'Whole Blood', '2-4 hours', TRUE),
('HB', 'Hemoglobin', 'Hematology', 300.00, 'Hemoglobin level measurement', 'Whole Blood', '2-4 hours', TRUE),
('PCV', 'Packed Cell Volume', 'Hematology', 350.00, 'Hematocrit measurement', 'Whole Blood', '2-4 hours', TRUE),
('TC-DC', 'Total Count & Differential Count', 'Hematology', 600.00, 'WBC count with differential', 'Whole Blood', '4-6 hours', TRUE),
('PLT', 'Platelet Count', 'Hematology', 500.00, 'Thrombocyte count', 'Whole Blood', '4-6 hours', TRUE),
('BT-CT', 'Bleeding Time & Clotting Time', 'Hematology', 450.00, 'Coagulation screening', 'Whole Blood', '2-4 hours', TRUE),
('PT-INR', 'Prothrombin Time/INR', 'Hematology', 650.00, 'Anticoagulation monitoring', 'Plasma', '4-6 hours', TRUE),

-- Biochemistry
('FBS', 'Fasting Blood Sugar', 'Biochemistry', 300.00, 'Fasting glucose level', 'Serum', '2-4 hours', TRUE),
('PPBS', 'Post Prandial Blood Sugar', 'Biochemistry', 350.00, 'Post-meal glucose level', 'Serum', '2-4 hours', TRUE),
('RBS', 'Random Blood Sugar', 'Biochemistry', 280.00, 'Random glucose measurement', 'Serum', '2-4 hours', TRUE),
('HbA1c', 'Glycated Hemoglobin', 'Biochemistry', 1200.00, 'Diabetes monitoring test', 'Whole Blood', '6-8 hours', TRUE),
('LFT', 'Liver Function Test', 'Biochemistry', 1500.00, 'Comprehensive liver function panel', 'Serum', '6-8 hours', TRUE),
('KFT', 'Kidney Function Test', 'Biochemistry', 1200.00, 'Kidney function assessment', 'Serum', '6-8 hours', TRUE),
('LIPID', 'Lipid Profile', 'Biochemistry', 1400.00, 'Complete lipid panel', 'Serum', '6-8 hours', TRUE),
('S.CREATININE', 'Serum Creatinine', 'Biochemistry', 400.00, 'Kidney function marker', 'Serum', '4-6 hours', TRUE),
('S.URIC ACID', 'Serum Uric Acid', 'Biochemistry', 450.00, 'Gout screening', 'Serum', '4-6 hours', TRUE),
('S.CALCIUM', 'Serum Calcium', 'Biochemistry', 500.00, 'Calcium level measurement', 'Serum', '4-6 hours', TRUE),

-- Endocrinology
('TSH', 'Thyroid Stimulating Hormone', 'Endocrinology', 800.00, 'Thyroid function screening', 'Serum', '8-12 hours', TRUE),
('T3', 'Triiodothyronine', 'Endocrinology', 900.00, 'Thyroid hormone', 'Serum', '8-12 hours', TRUE),
('T4', 'Thyroxine', 'Endocrinology', 900.00, 'Thyroid hormone', 'Serum', '8-12 hours', TRUE),
('FT3', 'Free T3', 'Endocrinology', 1100.00, 'Free triiodothyronine', 'Serum', '8-12 hours', TRUE),
('FT4', 'Free T4', 'Endocrinology', 1100.00, 'Free thyroxine', 'Serum', '8-12 hours', TRUE),

-- Immunology
('CRP', 'C-Reactive Protein', 'Immunology', 800.00, 'Acute phase reactant', 'Serum', '4-6 hours', TRUE),
('ASO', 'Anti-Streptolysin O', 'Immunology', 850.00, 'Streptococcal infection marker', 'Serum', '6-8 hours', TRUE),
('RA', 'Rheumatoid Arthritis Factor', 'Immunology', 900.00, 'RA screening', 'Serum', '6-8 hours', TRUE),
('HIV', 'HIV Screening', 'Immunology', 1200.00, 'HIV antibody test', 'Serum', '12-24 hours', TRUE),
('HBsAg', 'Hepatitis B Surface Antigen', 'Immunology', 800.00, 'Hepatitis B screening', 'Serum', '6-8 hours', TRUE),
('Anti-HCV', 'Hepatitis C Antibody', 'Immunology', 1000.00, 'Hepatitis C screening', 'Serum', '6-8 hours', TRUE),

-- Microbiology
('URINE-C/S', 'Urine Culture & Sensitivity', 'Microbiology', 1200.00, 'Urine bacterial culture', 'Urine', '48-72 hours', TRUE),
('BLOOD-C/S', 'Blood Culture & Sensitivity', 'Microbiology', 1800.00, 'Blood bacterial culture', 'Blood', '48-72 hours', TRUE),
('STOOL-R/E', 'Stool Routine Examination', 'Microbiology', 400.00, 'Stool microscopy', 'Stool', '4-6 hours', TRUE),

-- Urine Tests
('URINE-R/E', 'Urine Routine Examination', 'Clinical Pathology', 350.00, 'Urine analysis', 'Urine', '2-4 hours', TRUE),
('URINE-ALBUMIN', 'Urine Albumin', 'Clinical Pathology', 300.00, 'Urine protein test', 'Urine', '2-4 hours', TRUE),
('URINE-SUGAR', 'Urine Sugar', 'Clinical Pathology', 250.00, 'Urine glucose test', 'Urine', '2-4 hours', TRUE);

-- ============================================
-- TEST PARAMETERS (Sample for key tests)
-- ============================================
INSERT INTO test_parameters (test_code, parameter_name, unit, normal_range_min, normal_range_max, normal_range_text, is_active) VALUES
-- CBC Parameters
('CBC', 'Hemoglobin', 'g/dL', 12.0, 16.0, '12.0-16.0 g/dL (Female), 13.5-17.5 g/dL (Male)', TRUE),
('CBC', 'RBC Count', 'million/μL', 4.0, 5.5, '4.0-5.5 million/μL (Female), 4.5-6.0 million/μL (Male)', TRUE),
('CBC', 'WBC Count', 'thousand/μL', 4.0, 11.0, '4.0-11.0 thousand/μL', TRUE),
('CBC', 'Platelet Count', 'thousand/μL', 150.0, 450.0, '150-450 thousand/μL', TRUE),
('CBC', 'Neutrophils', '%', 40.0, 75.0, '40-75%', TRUE),
('CBC', 'Lymphocytes', '%', 20.0, 45.0, '20-45%', TRUE),
('CBC', 'Monocytes', '%', 2.0, 10.0, '2-10%', TRUE),
('CBC', 'Eosinophils', '%', 1.0, 6.0, '1-6%', TRUE),

-- LFT Parameters
('LFT', 'ALT (SGPT)', 'U/L', 7.0, 35.0, '7-35 U/L', TRUE),
('LFT', 'AST (SGOT)', 'U/L', 8.0, 40.0, '8-40 U/L', TRUE),
('LFT', 'Alkaline Phosphatase', 'U/L', 30.0, 120.0, '30-120 U/L', TRUE),
('LFT', 'Total Bilirubin', 'mg/dL', 0.2, 1.2, '0.2-1.2 mg/dL', TRUE),
('LFT', 'Direct Bilirubin', 'mg/dL', 0.0, 0.3, '0.0-0.3 mg/dL', TRUE),
('LFT', 'Total Protein', 'g/dL', 6.0, 8.3, '6.0-8.3 g/dL', TRUE),
('LFT', 'Albumin', 'g/dL', 3.5, 5.5, '3.5-5.5 g/dL', TRUE),

-- KFT Parameters
('KFT', 'Blood Urea', 'mg/dL', 10.0, 50.0, '10-50 mg/dL', TRUE),
('KFT', 'Serum Creatinine', 'mg/dL', 0.6, 1.2, '0.6-1.2 mg/dL', TRUE),
('KFT', 'Uric Acid', 'mg/dL', 2.5, 7.0, '2.5-7.0 mg/dL', TRUE),

-- Lipid Profile Parameters
('LIPID', 'Total Cholesterol', 'mg/dL', 0.0, 200.0, '<200 mg/dL (Desirable)', TRUE),
('LIPID', 'HDL Cholesterol', 'mg/dL', 40.0, 999.0, '>40 mg/dL (Male), >50 mg/dL (Female)', TRUE),
('LIPID', 'LDL Cholesterol', 'mg/dL', 0.0, 100.0, '<100 mg/dL (Optimal)', TRUE),
('LIPID', 'Triglycerides', 'mg/dL', 0.0, 150.0, '<150 mg/dL (Normal)', TRUE),
('LIPID', 'VLDL Cholesterol', 'mg/dL', 0.0, 30.0, '<30 mg/dL', TRUE);

-- ============================================
-- INVOICES (30 realistic invoices)
-- ============================================
INSERT INTO invoices (patient_name, patient_age, patient_gender, patient_phone, patient_email, patient_address, doctor_id, total_amount, discount_amount, amount_paid, payment_status, payment_method, notes, created_at) VALUES
('আব্দুর রহমান সরকার', 45, 'Male', '+8801712345678', 'rahman@email.com', 'মিরপুর-১০, ঢাকা', 1, 3200.00, 200.00, 3000.00, 'paid', 'cash', 'Annual checkup', NOW() - INTERVAL 10 DAY),
('ফাতিমা বেগম', 32, 'Female', '+8801812345679', 'fatima@email.com', 'ধানমন্ডি, ঢাকা', 2, 2400.00, 0.00, 1200.00, 'partial', 'card', 'Pregnancy checkup', NOW() - INTERVAL 9 DAY),
('করিম আহমেদ', 28, 'Male', '+8801912345680', 'karim@email.com', 'মোহাম্মদপুর, ঢাকা', 1, 1800.00, 100.00, 0.00, 'pending', NULL, 'Routine blood work', NOW() - INTERVAL 8 DAY),
('সালমা খাতুন', 55, 'Female', '+8801612345681', 'salma@email.com', 'গুলশান, ঢাকা', 5, 5800.00, 300.00, 5500.00, 'paid', 'bkash', 'Cardiac screening', NOW() - INTERVAL 7 DAY),
('নাসির উদ্দিন', 38, 'Male', '+8801512345682', 'nasir@email.com', 'বনানী, ঢাকা', 10, 2200.00, 0.00, 1100.00, 'partial', 'cash', 'Diabetes monitoring', NOW() - INTERVAL 6 DAY),
('রাশিদা আক্তার', 42, 'Female', '+8801412345683', 'rashida@email.com', 'উত্তরা, ঢাকা', 1, 3400.00, 150.00, 0.00, 'pending', NULL, 'Thyroid checkup', NOW() - INTERVAL 5 DAY),
('মোহাম্মদ আলী', 60, 'Male', '+8801312345684', 'ali@email.com', 'মতিঝিল, ঢাকা', 5, 4200.00, 200.00, 4000.00, 'paid', 'card', 'Full health checkup', NOW() - INTERVAL 4 DAY),
('রোকেয়া বেগম', 35, 'Female', '+8801212345685', 'rokeya@email.com', 'কাকরাইল, ঢাকা', 2, 1600.00, 0.00, 800.00, 'partial', 'cash', 'Blood tests', NOW() - INTERVAL 3 DAY),
('জহির হোসেন', 50, 'Male', '+8801112345686', 'jahir@email.com', 'শ্যামলী, ঢাকা', 11, 3800.00, 180.00, 3620.00, 'paid', 'nagad', 'Digestive system tests', NOW() - INTERVAL 2 DAY),
('নাজমা পারভীন', 29, 'Female', '+8801712345687', 'najma@email.com', 'বসুন্ধরা, ঢাকা', 4, 2100.00, 0.00, 0.00, 'pending', NULL, 'Child health checkup', NOW() - INTERVAL 1 DAY),
('রফিক মিয়া', 47, 'Male', '+8801812345688', 'rafiq@email.com', 'যাত্রাবাড়ী, ঢাকা', 1, 1500.00, 50.00, 1450.00, 'paid', 'cash', 'Basic blood tests', NOW() - INTERVAL 12 HOUR),
('শাহানা আক্তার', 38, 'Female', '+8801912345689', 'shahana@email.com', 'খিলগাঁও, ঢাকা', 2, 2800.00, 0.00, 1400.00, 'partial', 'bkash', 'Hormonal tests', NOW() - INTERVAL 8 HOUR),
('তানভীর ইসলাম', 33, 'Male', '+8801612345690', 'tanvir@email.com', 'তেজগাঁও, ঢাকা', 1, 900.00, 0.00, 900.00, 'paid', 'cash', 'Quick checkup', NOW() - INTERVAL 6 HOUR),
('সুমাইয়া বেগম', 26, 'Female', '+8801512345691', 'sumaiya@email.com', 'আগারগাঁও, ঢাকা', 2, 1700.00, 0.00, 0.00, 'pending', NULL, 'Pre-marital tests', NOW() - INTERVAL 4 HOUR),
('আক্তারুজ্জামান', 52, 'Male', '+8801412345692', 'aktaruzzaman@email.com', 'শেরে বাংলা নগর, ঢাকা', 1, 4500.00, 250.00, 4250.00, 'paid', 'card', 'Comprehensive panel', NOW() - INTERVAL 2 HOUR),
('পারুল আক্তার', 31, 'Female', '+8801312345693', 'parul@email.com', 'রমনা, ঢাকা', 2, 2300.00, 100.00, 2200.00, 'paid', 'bkash', 'Routine checkup', NOW() - INTERVAL 1 HOUR),
('মাহবুব আলম', 44, 'Male', '+8801212345694', 'mahbub@email.com', 'পল্টন, ঢাকা', 1, 1200.00, 0.00, 600.00, 'partial', 'cash', 'Blood sugar tests', NOW() - INTERVAL 30 MINUTE),
('নার্গিস সুলতানা', 36, 'Female', '+8801112345695', 'nargis@email.com', 'বেইলি রোড, ঢাকা', 2, 3100.00, 0.00, 0.00, 'pending', NULL, 'Complete blood work', NOW() - INTERVAL 15 MINUTE),
('কামাল উদ্দিন', 58, 'Male', '+8801712345696', 'kamal@email.com', 'ফার্মগেট, ঢাকা', 5, 5200.00, 300.00, 4900.00, 'paid', 'card', 'Heart health checkup', NOW() - INTERVAL 10 MINUTE),
('রুমানা আফরোজ', 27, 'Female', '+8801812345697', 'rumana@email.com', 'কারওয়ান বাজার, ঢাকা', 2, 1800.00, 0.00, 900.00, 'partial', 'nagad', 'Antenatal screening', NOW() - INTERVAL 5 MINUTE);

-- Add 10 more invoices for variety
INSERT INTO invoices (patient_name, patient_age, patient_gender, patient_phone, patient_address, doctor_id, total_amount, discount_amount, amount_paid, payment_status, payment_method, notes, created_at) VALUES
('সাকিব হাসান', 22, 'Male', '+8801912345698', 'সাভার, ঢাকা', 1, 850.00, 0.00, 850.00, 'paid', 'cash', 'Basic tests', NOW() - INTERVAL 11 DAY),
('তাহমিনা খাতুন', 48, 'Female', '+8801612345699', 'গাজীপুর', 11, 2900.00, 100.00, 2800.00, 'paid', 'bkash', 'Digestive checkup', NOW() - INTERVAL 12 DAY),
('ইমরান খান', 35, 'Male', '+8801512345700', 'নারায়ণগঞ্জ', 1, 1400.00, 0.00, 700.00, 'partial', 'cash', 'Routine tests', NOW() - INTERVAL 13 DAY),
('সাবিনা ইয়াসমিন', 40, 'Female', '+8801412345701', 'কেরানীগঞ্জ, ঢাকা', 2, 2600.00, 0.00, 0.00, 'pending', NULL, 'Hormonal screening', NOW() - INTERVAL 14 DAY),
('হাবিবুর রহমান', 55, 'Male', '+8801312345702', 'পূর্বাচল, ঢাকা', 5, 6100.00, 400.00, 5700.00, 'paid', 'card', 'Full health panel', NOW() - INTERVAL 15 DAY),
('নাসিমা বেগম', 33, 'Female', '+8801212345703', 'আশুলিয়া, ঢাকা', 2, 1900.00, 50.00, 1850.00, 'paid', 'nagad', 'Blood tests', NOW() - INTERVAL 16 DAY),
('ফারুক আহমেদ', 41, 'Male', '+8801112345704', 'টঙ্গী, গাজীপুর', 1, 1100.00, 0.00, 0.00, 'pending', NULL, 'Blood sugar check', NOW() - INTERVAL 17 DAY),
('রেহানা পারভীন', 29, 'Female', '+8801712345705', 'সোনারগাঁও, নারায়ণগঞ্জ', 2, 3300.00, 150.00, 3150.00, 'paid', 'bkash', 'Complete screening', NOW() - INTERVAL 18 DAY),
('জাকির হোসেন', 50, 'Male', '+8801812345706', 'ডেমরা, ঢাকা', 11, 2700.00, 0.00, 1350.00, 'partial', 'cash', 'Digestive tests', NOW() - INTERVAL 19 DAY),
('সেলিনা আক্তার', 37, 'Female', '+8801912345707', 'জিরাবো, ঢাকা', 2, 2100.00, 0.00, 2100.00, 'paid', 'card', 'Annual checkup', NOW() - INTERVAL 20 DAY);

-- ============================================
-- INVOICE TESTS (Link tests to invoices)
-- ============================================
INSERT INTO invoice_tests (invoice_id, test_code, test_name, price) VALUES
-- Invoice 1
(1, 'CBC', 'Complete Blood Count', 800.00),
(1, 'LFT', 'Liver Function Test', 1500.00),
(1, 'KFT', 'Kidney Function Test', 1200.00),

-- Invoice 2
(2, 'CBC', 'Complete Blood Count', 800.00),
(2, 'HbA1c', 'Glycated Hemoglobin', 1200.00),
(2, 'URINE-R/E', 'Urine Routine Examination', 350.00),

-- Invoice 3
(3, 'CBC', 'Complete Blood Count', 800.00),
(3, 'FBS', 'Fasting Blood Sugar', 300.00),
(3, 'LIPID', 'Lipid Profile', 1400.00),

-- Invoice 4
(4, 'CBC', 'Complete Blood Count', 800.00),
(4, 'LFT', 'Liver Function Test', 1500.00),
(4, 'KFT', 'Kidney Function Test', 1200.00),
(4, 'LIPID', 'Lipid Profile', 1400.00),
(4, 'TSH', 'Thyroid Stimulating Hormone', 800.00),

-- Invoice 5
(5, 'FBS', 'Fasting Blood Sugar', 300.00),
(5, 'PPBS', 'Post Prandial Blood Sugar', 350.00),
(5, 'HbA1c', 'Glycated Hemoglobin', 1200.00),

-- Invoice 6
(6, 'TSH', 'Thyroid Stimulating Hormone', 800.00),
(6, 'T3', 'Triiodothyronine', 900.00),
(6, 'T4', 'Thyroxine', 900.00),
(6, 'CBC', 'Complete Blood Count', 800.00),

-- Invoice 7
(7, 'CBC', 'Complete Blood Count', 800.00),
(7, 'LFT', 'Liver Function Test', 1500.00),
(7, 'LIPID', 'Lipid Profile', 1400.00),
(7, 'FBS', 'Fasting Blood Sugar', 300.00),

-- Invoice 8
(8, 'CBC', 'Complete Blood Count', 800.00),
(8, 'ESR', 'Erythrocyte Sedimentation Rate', 400.00),
(8, 'CRP', 'C-Reactive Protein', 800.00),

-- Invoice 9
(9, 'LFT', 'Liver Function Test', 1500.00),
(9, 'KFT', 'Kidney Function Test', 1200.00),
(9, 'LIPID', 'Lipid Profile', 1400.00),

-- Invoice 10
(10, 'CBC', 'Complete Blood Count', 800.00),
(10, 'FBS', 'Fasting Blood Sugar', 300.00),
(10, 'URINE-R/E', 'Urine Routine Examination', 350.00),

-- Continue for remaining invoices...
(11, 'FBS', 'Fasting Blood Sugar', 300.00),
(11, 'PPBS', 'Post Prandial Blood Sugar', 350.00),
(11, 'CBC', 'Complete Blood Count', 800.00),

(12, 'TSH', 'Thyroid Stimulating Hormone', 800.00),
(12, 'FT3', 'Free T3', 1100.00),
(12, 'FT4', 'Free T4', 1100.00),

(13, 'CBC', 'Complete Blood Count', 800.00),

(14, 'CBC', 'Complete Blood Count', 800.00),
(14, 'HBsAg', 'Hepatitis B Surface Antigen', 800.00),

(15, 'CBC', 'Complete Blood Count', 800.00),
(15, 'LFT', 'Liver Function Test', 1500.00),
(15, 'KFT', 'Kidney Function Test', 1200.00),
(15, 'LIPID', 'Lipid Profile', 1400.00),

(16, 'CBC', 'Complete Blood Count', 800.00),
(16, 'FBS', 'Fasting Blood Sugar', 300.00),
(16, 'LIPID', 'Lipid Profile', 1400.00),

(17, 'FBS', 'Fasting Blood Sugar', 300.00),
(17, 'HbA1c', 'Glycated Hemoglobin', 1200.00),

(18, 'CBC', 'Complete Blood Count', 800.00),
(18, 'LFT', 'Liver Function Test', 1500.00),
(18, 'KFT', 'Kidney Function Test', 1200.00),

(19, 'CBC', 'Complete Blood Count', 800.00),
(19, 'LFT', 'Liver Function Test', 1500.00),
(19, 'LIPID', 'Lipid Profile', 1400.00),
(19, 'KFT', 'Kidney Function Test', 1200.00),
(19, 'TSH', 'Thyroid Stimulating Hormone', 800.00),

(20, 'CBC', 'Complete Blood Count', 800.00),
(20, 'URINE-R/E', 'Urine Routine Examination', 350.00),

-- More invoices 21-30
(21, 'CBC', 'Complete Blood Count', 800.00),
(22, 'LFT', 'Liver Function Test', 1500.00),
(22, 'KFT', 'Kidney Function Test', 1200.00),
(23, 'CBC', 'Complete Blood Count', 800.00),
(23, 'FBS', 'Fasting Blood Sugar', 300.00),
(24, 'TSH', 'Thyroid Stimulating Hormone', 800.00),
(24, 'T3', 'Triiodothyronine', 900.00),
(24, 'T4', 'Thyroxine', 900.00),
(25, 'CBC', 'Complete Blood Count', 800.00),
(25, 'LFT', 'Liver Function Test', 1500.00),
(25, 'KFT', 'Kidney Function Test', 1200.00),
(25, 'LIPID', 'Lipid Profile', 1400.00),
(25, 'TSH', 'Thyroid Stimulating Hormone', 800.00),
(26, 'CBC', 'Complete Blood Count', 800.00),
(26, 'FBS', 'Fasting Blood Sugar', 300.00),
(27, 'FBS', 'Fasting Blood Sugar', 300.00),
(27, 'PPBS', 'Post Prandial Blood Sugar', 350.00),
(28, 'CBC', 'Complete Blood Count', 800.00),
(28, 'LFT', 'Liver Function Test', 1500.00),
(28, 'LIPID', 'Lipid Profile', 1400.00),
(29, 'LFT', 'Liver Function Test', 1500.00),
(29, 'KFT', 'Kidney Function Test', 1200.00),
(30, 'CBC', 'Complete Blood Count', 800.00),
(30, 'LIPID', 'Lipid Profile', 1400.00);

-- ============================================
-- TEST REPORTS (Completed reports with results)
-- ============================================
INSERT INTO test_reports (invoice_id, test_code, status, technician_id, verified_by, notes, created_at, updated_at) VALUES
(1, 'CBC', 'verified', 3, 1, 'Normal blood count parameters', NOW() - INTERVAL 9 DAY, NOW() - INTERVAL 9 DAY),
(1, 'LFT', 'verified', 3, 1, 'Liver function within normal limits', NOW() - INTERVAL 9 DAY, NOW() - INTERVAL 9 DAY),
(1, 'KFT', 'completed', 3, NULL, 'Kidney function normal', NOW() - INTERVAL 9 DAY, NOW() - INTERVAL 9 DAY),
(2, 'CBC', 'completed', 4, NULL, 'Blood count satisfactory', NOW() - INTERVAL 8 DAY, NOW() - INTERVAL 8 DAY),
(3, 'CBC', 'in_progress', 3, NULL, 'Processing sample', NOW() - INTERVAL 7 DAY, NOW() - INTERVAL 7 HOUR),
(4, 'CBC', 'verified', 3, 1, 'Complete blood analysis done', NOW() - INTERVAL 6 DAY, NOW() - INTERVAL 6 DAY),
(4, 'LFT', 'verified', 3, 1, 'Liver enzymes normal', NOW() - INTERVAL 6 DAY, NOW() - INTERVAL 6 DAY),
(5, 'FBS', 'verified', 4, 1, 'Glucose level slightly elevated', NOW() - INTERVAL 5 DAY, NOW() - INTERVAL 5 DAY),
(7, 'CBC', 'completed', 3, NULL, 'All parameters within range', NOW() - INTERVAL 3 DAY, NOW() - INTERVAL 3 DAY),
(8, 'CBC', 'pending', NULL, NULL, 'Awaiting sample processing', NOW() - INTERVAL 2 DAY, NOW() - INTERVAL 2 DAY);

-- ============================================
-- TEST RESULTS (Detailed results for reports)
-- ============================================
INSERT INTO test_results (report_id, parameter_name, value, unit, normal_range, is_abnormal, is_active) VALUES
-- Report 1 (CBC)
(1, 'Hemoglobin', '14.2', 'g/dL', '12.0-16.0 g/dL', FALSE, TRUE),
(1, 'RBC Count', '4.8', 'million/μL', '4.0-5.5 million/μL', FALSE, TRUE),
(1, 'WBC Count', '7200', 'cells/μL', '4000-11000 cells/μL', FALSE, TRUE),
(1, 'Platelet Count', '280000', 'cells/μL', '150000-450000 cells/μL', FALSE, TRUE),
(1, 'Neutrophils', '62', '%', '40-75%', FALSE, TRUE),
(1, 'Lymphocytes', '30', '%', '20-45%', FALSE, TRUE),

-- Report 2 (LFT)
(2, 'ALT (SGPT)', '25', 'U/L', '7-35 U/L', FALSE, TRUE),
(2, 'AST (SGOT)', '30', 'U/L', '8-40 U/L', FALSE, TRUE),
(2, 'Alkaline Phosphatase', '85', 'U/L', '30-120 U/L', FALSE, TRUE),
(2, 'Total Bilirubin', '0.8', 'mg/dL', '0.2-1.2 mg/dL', FALSE, TRUE),
(2, 'Albumin', '4.2', 'g/dL', '3.5-5.5 g/dL', FALSE, TRUE),

-- Report 3 (KFT)
(3, 'Blood Urea', '32', 'mg/dL', '10-50 mg/dL', FALSE, TRUE),
(3, 'Serum Creatinine', '0.9', 'mg/dL', '0.6-1.2 mg/dL', FALSE, TRUE),
(3, 'Uric Acid', '5.2', 'mg/dL', '2.5-7.0 mg/dL', FALSE, TRUE),

-- Report 4 (CBC with slightly elevated WBC)
(4, 'Hemoglobin', '13.8', 'g/dL', '12.0-16.0 g/dL', FALSE, TRUE),
(4, 'RBC Count', '4.5', 'million/μL', '4.0-5.5 million/μL', FALSE, TRUE),
(4, 'WBC Count', '8500', 'cells/μL', '4000-11000 cells/μL', FALSE, TRUE),
(4, 'Platelet Count', '310000', 'cells/μL', '150000-450000 cells/μL', FALSE, TRUE),

-- Report 8 (FBS - slightly elevated)
(8, 'Glucose', '118', 'mg/dL', '70-100 mg/dL', TRUE, TRUE);

-- ============================================
-- SUMMARY MESSAGE
-- ============================================
SELECT '======================================' as '';
SELECT 'SEEDER DATA LOADED SUCCESSFULLY!' as '';
SELECT '======================================' as '';
SELECT CONCAT('Users: ', COUNT(*), ' (including admin)') as 'Summary' FROM users
UNION ALL
SELECT CONCAT('Doctors: ', COUNT(*), ' doctors') FROM doctors
UNION ALL
SELECT CONCAT('Tests: ', COUNT(*), ' test types') FROM tests
UNION ALL
SELECT CONCAT('Invoices: ', COUNT(*), ' invoices') FROM invoices
UNION ALL
SELECT CONCAT('Test Reports: ', COUNT(*), ' reports') FROM test_reports
UNION ALL
SELECT CONCAT('Test Results: ', COUNT(*), ' individual results') FROM test_results;
SELECT '======================================' as '';