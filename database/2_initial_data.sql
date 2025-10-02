-- ============================================
-- SEL Diagnostic Center - Initial Essential Data
-- ============================================
-- This file inserts essential data needed for the system to function
-- Run this file SECOND after 1_schema.sql

USE pathology_lab;

-- ============================================
-- ADMIN USER
-- ============================================
-- Default admin user (password: admin123)
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
SELECT 'Default login: admin / admin123' as 'Important';
