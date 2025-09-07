-- Medical Operations Database Schema
-- Part 3: Medical Operations (Tests, Doctors, Reports)

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
    INDEX idx_active (is_active)
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
    INDEX idx_status (status)
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
    INDEX idx_active (is_active)
);

-- Sample tests data
INSERT INTO tests (code, name, category, price, description, sample_type, turnaround_time) VALUES
('CBC', 'Complete Blood Count', 'Hematology', 800.00, 'Complete blood count with differential', 'Whole Blood', '4-6 hours'),
('LFT', 'Liver Function Test', 'Biochemistry', 1200.00, 'Comprehensive liver function panel', 'Serum', '6-8 hours'),
('KFT', 'Kidney Function Test', 'Biochemistry', 600.00, 'Kidney function assessment', 'Serum', '4-6 hours'),
('LIPID', 'Lipid Profile', 'Biochemistry', 700.00, 'Complete lipid panel', 'Serum', '6-8 hours'),
('TSH', 'Thyroid Stimulating Hormone', 'Endocrinology', 500.00, 'TSH level measurement', 'Serum', '8-12 hours'),
('HbA1c', 'Glycated Hemoglobin', 'Biochemistry', 600.00, 'Diabetes monitoring test', 'Whole Blood', '4-6 hours'),
('ESR', 'Erythrocyte Sedimentation Rate', 'Hematology', 400.00, 'Inflammation marker', 'Whole Blood', '2-4 hours'),
('CRP', 'C-Reactive Protein', 'Immunology', 500.00, 'Acute phase reactant', 'Serum', '4-6 hours'),
('FBS', 'Fasting Blood Sugar', 'Biochemistry', 300.00, 'Fasting glucose level', 'Serum', '2-4 hours'),
('PPBS', 'Post Prandial Blood Sugar', 'Biochemistry', 350.00, 'Post-meal glucose level', 'Serum', '2-4 hours');

-- Sample test parameters
INSERT INTO test_parameters (test_code, parameter_name, unit, normal_range_min, normal_range_max, normal_range_text) VALUES
('CBC', 'Hemoglobin', 'g/dL', 12.0, 16.0, '12.0-16.0 g/dL'),
('CBC', 'RBC Count', 'million/μL', 4.2, 5.4, '4.2-5.4 million/μL'),
('CBC', 'WBC Count', 'thousand/μL', 4.0, 11.0, '4.0-11.0 thousand/μL'),
('CBC', 'Platelet Count', 'thousand/μL', 150.0, 450.0, '150-450 thousand/μL'),
('LFT', 'ALT', 'U/L', 7.0, 35.0, '7-35 U/L'),
('LFT', 'AST', 'U/L', 8.0, 40.0, '8-40 U/L'),
('LFT', 'Bilirubin Total', 'mg/dL', 0.2, 1.2, '0.2-1.2 mg/dL'),
('KFT', 'Creatinine', 'mg/dL', 0.6, 1.2, '0.6-1.2 mg/dL'),
('KFT', 'BUN', 'mg/dL', 7.0, 20.0, '7-20 mg/dL'),
('LIPID', 'Total Cholesterol', 'mg/dL', 0.0, 200.0, '<200 mg/dL'),
('LIPID', 'HDL Cholesterol', 'mg/dL', 40.0, 999.0, '>40 mg/dL'),
('LIPID', 'LDL Cholesterol', 'mg/dL', 0.0, 100.0, '<100 mg/dL'),
('TSH', 'TSH', 'mIU/L', 0.4, 4.0, '0.4-4.0 mIU/L'),
('HbA1c', 'HbA1c', '%', 4.0, 5.6, '4.0-5.6%'),
('ESR', 'ESR', 'mm/hr', 0.0, 20.0, '0-20 mm/hr'),
('CRP', 'CRP', 'mg/L', 0.0, 3.0, '<3.0 mg/L'),
('FBS', 'Glucose', 'mg/dL', 70.0, 100.0, '70-100 mg/dL'),
('PPBS', 'Glucose', 'mg/dL', 70.0, 140.0, '70-140 mg/dL');

-- Sample doctors data
INSERT INTO doctors (name, qualifications, specialization, workplace, phone, email, license_number) VALUES
('ডা. মোহাম্মদ রহমান', 'MBBS, MD (Internal Medicine)', 'Internal Medicine', 'ঢাকা মেডিকেল কলেজ হাসপাতাল', '+8801712345001', 'dr.rahman@dmc.gov.bd', 'BMA-12345'),
('ডা. ফাতিমা খাতুন', 'MBBS, FCPS (Gynecology)', 'Gynecology & Obstetrics', 'বঙ্গবন্ধু শেখ মুজিব মেডিকেল বিশ্ববিদ্যালয়', '+8801812345002', 'dr.fatima@bsmmu.edu.bd', 'BMA-12346'),
('ডা. আব্দুল করিম', 'MBBS, MS (Surgery)', 'General Surgery', 'চট্টগ্রাম মেডিকেল কলেজ হাসপাতাল', '+8801912345003', 'dr.karim@cmc.gov.bd', 'BMA-12347'),
('ডা. নাসরিন আক্তার', 'MBBS, DCH (Pediatrics)', 'Pediatrics', 'শিশু হাসপাতাল, ঢাকা', '+8801612345004', 'dr.nasrin@shishu.gov.bd', 'BMA-12348'),
('ডা. তারিক হাসান', 'MBBS, DCard (Cardiology)', 'Cardiology', 'জাতীয় হৃদরোগ ইনস্টিটিউট', '+8801512345005', 'dr.tarik@nhf.gov.bd', 'BMA-12349');

-- Sample test reports
INSERT INTO test_reports (invoice_id, test_code, status, technician_id, notes) VALUES
(1, 'CBC', 'completed', 1, 'Normal blood count parameters'),
(1, 'LFT', 'verified', 1, 'Liver function within normal limits'),
(2, 'TSH', 'in_progress', 2, 'Processing thyroid function test'),
(3, 'CBC', 'pending', NULL, 'Awaiting sample processing'),
(4, 'LIPID', 'completed', 1, 'Lipid profile shows elevated cholesterol');

-- Sample test results
INSERT INTO test_results (report_id, parameter_name, value, unit, normal_range, is_abnormal) VALUES
(1, 'Hemoglobin', '14.2', 'g/dL', '12.0-16.0 g/dL', FALSE),
(1, 'RBC Count', '4.8', 'million/μL', '4.2-5.4 million/μL', FALSE),
(1, 'WBC Count', '7.2', 'thousand/μL', '4.0-11.0 thousand/μL', FALSE),
(1, 'Platelet Count', '280', 'thousand/μL', '150-450 thousand/μL', FALSE),
(2, 'ALT', '25', 'U/L', '7-35 U/L', FALSE),
(2, 'AST', '30', 'U/L', '8-40 U/L', FALSE),
(2, 'Bilirubin Total', '0.8', 'mg/dL', '0.2-1.2 mg/dL', FALSE),
(5, 'Total Cholesterol', '220', 'mg/dL', '<200 mg/dL', TRUE),
(5, 'HDL Cholesterol', '35', 'mg/dL', '>40 mg/dL', TRUE),
(5, 'LDL Cholesterol', '150', 'mg/dL', '<100 mg/dL', TRUE);

-- Create additional indexes for performance
CREATE INDEX IF NOT EXISTS idx_test_reports_created_at ON test_reports (created_at);
CREATE INDEX IF NOT EXISTS idx_test_results_value ON test_results (value);
CREATE INDEX IF NOT EXISTS idx_doctors_phone ON doctors (phone);
