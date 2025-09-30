-- Invoice Management Database Schema
-- Part 2: Patient & Invoice Management

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
    INDEX idx_patient_name (patient_name),
    INDEX idx_payment_status (payment_status),
    INDEX idx_created_at (created_at),
    INDEX idx_doctor_id (doctor_id)
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
    INDEX idx_test_code (test_code)
);

-- Sample invoice data
INSERT INTO invoices (patient_name, patient_age, patient_gender, patient_phone, patient_email, doctor_id, total_amount, discount_amount, amount_paid, payment_status, notes) VALUES
('আব্দুর রহমান', 45, 'Male', '+8801712345678', 'rahman@email.com', 1, 2500.00, 100.00, 2400.00, 'paid', 'Regular checkup tests'),
('ফাতিমা বেগম', 32, 'Female', '+8801812345679', 'fatima@email.com', 2, 1800.00, 0.00, 900.00, 'partial', 'Pregnancy related tests'),
('করিম আহমেদ', 28, 'Male', '+8801912345680', 'karim@email.com', 1, 1200.00, 50.00, 0.00, 'pending', 'Blood work required'),
('সালমা খাতুন', 55, 'Female', '+8801612345681', 'salma@email.com', 3, 3200.00, 200.00, 3000.00, 'paid', 'Comprehensive health package'),
('নাসির উদ্দিন', 38, 'Male', '+8801512345682', 'nasir@email.com', 2, 1500.00, 0.00, 750.00, 'partial', 'Diabetes monitoring'),
('রাশিদা আক্তার', 42, 'Female', '+8801412345683', 'rashida@email.com', 1, 2200.00, 100.00, 0.00, 'pending', 'Thyroid function tests'),
('মোহাম্মদ আলী', 60, 'Male', '+8801312345684', 'ali@email.com', 3, 2800.00, 150.00, 2650.00, 'paid', 'Cardiac screening'),
('রোকেয়া বেগম', 35, 'Female', '+8801212345685', 'rokeya@email.com', 2, 1600.00, 0.00, 800.00, 'partial', 'Routine blood tests');

-- Sample invoice tests data
INSERT INTO invoice_tests (invoice_id, test_code, test_name, price) VALUES
(1, 'CBC', 'Complete Blood Count', 800.00),
(1, 'LFT', 'Liver Function Test', 1200.00),
(1, 'KFT', 'Kidney Function Test', 600.00),
(2, 'CBC', 'Complete Blood Count', 800.00),
(2, 'TSH', 'Thyroid Stimulating Hormone', 500.00),
(2, 'HbA1c', 'Glycated Hemoglobin', 600.00),
(3, 'CBC', 'Complete Blood Count', 800.00),
(3, 'ESR', 'Erythrocyte Sedimentation Rate', 400.00),
(4, 'CBC', 'Complete Blood Count', 800.00),
(4, 'LFT', 'Liver Function Test', 1200.00),
(4, 'KFT', 'Kidney Function Test', 600.00),
(4, 'LIPID', 'Lipid Profile', 700.00),
(5, 'HbA1c', 'Glycated Hemoglobin', 600.00),
(5, 'FBS', 'Fasting Blood Sugar', 300.00),
(5, 'PPBS', 'Post Prandial Blood Sugar', 350.00),
(6, 'TSH', 'Thyroid Stimulating Hormone', 500.00),
(6, 'T3', 'Triiodothyronine', 600.00),
(6, 'T4', 'Thyroxine', 650.00),
(6, 'CBC', 'Complete Blood Count', 800.00),
(7, 'ECG', 'Electrocardiogram', 1000.00),
(7, 'ECHO', 'Echocardiogram', 1500.00),
(7, 'LIPID', 'Lipid Profile', 700.00),
(8, 'CBC', 'Complete Blood Count', 800.00),
(8, 'ESR', 'Erythrocyte Sedimentation Rate', 400.00),
(8, 'CRP', 'C-Reactive Protein', 500.00);

-- Create indexes for better performance
CREATE INDEX idx_invoices_patient_phone ON invoices (patient_phone);
CREATE INDEX idx_invoices_total_amount ON invoices (total_amount);
CREATE INDEX idx_invoice_tests_price ON invoice_tests (price);
