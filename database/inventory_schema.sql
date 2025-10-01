-- Inventory Management Database Schema
-- Consumables, reagents, and equipment tracking

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

-- Sample inventory items
INSERT INTO inventory_items (item_code, item_name, category, description, unit, quantity_in_stock, minimum_stock_level, reorder_level, unit_price, supplier, storage_location) VALUES
-- Reagents
('REG001', 'Hematology Reagent Pack', 'reagent', 'Complete blood count reagent pack', 'pack', 15, 5, 10, 2500.00, 'MediChem Supplies Ltd', 'Cold Storage A'),
('REG002', 'Biochemistry Analyzer Solution', 'reagent', 'Multi-parameter chemistry reagent', 'liter', 8, 3, 8, 3500.00, 'MediChem Supplies Ltd', 'Cold Storage A'),
('REG003', 'Glucose Testing Reagent', 'reagent', 'Blood glucose measurement reagent', 'bottle', 20, 5, 12, 850.00, 'Lab Solutions BD', 'Shelf B2'),
('REG004', 'Urine Analysis Strips', 'reagent', '10-parameter urine test strips', 'box', 25, 10, 15, 650.00, 'Diagnostic Plus', 'Shelf C1'),
('REG005', 'Blood Culture Media', 'reagent', 'Aerobic and anaerobic culture bottles', 'set', 12, 4, 10, 1200.00, 'MicroLab International', 'Cold Storage B'),

-- Consumables
('CON001', 'Blood Collection Tubes (EDTA)', 'consumable', 'Vacuum tubes with EDTA anticoagulant', 'box', 150, 30, 50, 450.00, 'MedSupply Bangladesh', 'Storage Room 1'),
('CON002', 'Blood Collection Tubes (Plain)', 'consumable', 'Plain vacuum tubes for serum collection', 'box', 120, 30, 50, 380.00, 'MedSupply Bangladesh', 'Storage Room 1'),
('CON003', 'Disposable Syringes (5ml)', 'consumable', 'Sterile single-use syringes', 'box', 200, 40, 80, 320.00, 'SafeMed Supplies', 'Storage Room 1'),
('CON004', 'Disposable Gloves (Latex)', 'consumable', 'Medical examination gloves', 'box', 80, 20, 40, 280.00, 'SafeMed Supplies', 'Storage Room 2'),
('CON005', 'Cotton Swabs', 'consumable', 'Sterile cotton applicators', 'pack', 100, 25, 50, 150.00, 'General Supplies Co', 'Storage Room 2'),
('CON006', 'Alcohol Swabs', 'consumable', 'Isopropyl alcohol prep pads', 'box', 95, 20, 40, 220.00, 'SafeMed Supplies', 'Storage Room 2'),
('CON007', 'Microscope Slides', 'consumable', 'Glass microscope slides', 'box', 50, 10, 20, 180.00, 'Lab Solutions BD', 'Shelf A3'),
('CON008', 'Cover Slips', 'consumable', 'Glass cover slips for microscopy', 'box', 45, 10, 20, 120.00, 'Lab Solutions BD', 'Shelf A3'),
('CON009', 'Sample Cups (Urine)', 'consumable', 'Sterile urine collection cups', 'pack', 150, 30, 60, 280.00, 'MedSupply Bangladesh', 'Storage Room 1'),
('CON010', 'Pipette Tips (1000µl)', 'consumable', 'Disposable pipette tips', 'box', 60, 15, 30, 450.00, 'Lab Solutions BD', 'Shelf B1'),

-- Equipment
('EQP001', 'Digital Thermometer', 'equipment', 'Clinical digital thermometer', 'piece', 8, 2, 4, 850.00, 'Medical Instruments Ltd', 'Equipment Room'),
('EQP002', 'Blood Pressure Monitor', 'equipment', 'Digital BP apparatus', 'piece', 4, 1, 2, 3500.00, 'Medical Instruments Ltd', 'Equipment Room'),
('EQP003', 'Centrifuge Tubes (15ml)', 'equipment', 'Reusable centrifuge tubes', 'set', 20, 5, 10, 650.00, 'Lab Solutions BD', 'Equipment Room'),
('EQP004', 'Test Tube Racks', 'equipment', 'Plastic test tube holders', 'piece', 15, 3, 8, 220.00, 'Lab Solutions BD', 'Equipment Room'),
('EQP005', 'Biohazard Bags', 'equipment', 'Medical waste disposal bags', 'roll', 30, 8, 15, 380.00, 'SafeMed Supplies', 'Storage Room 3');

-- Sample inventory transactions
INSERT INTO inventory_transactions (item_id, transaction_type, quantity, unit_price, total_value, notes, performed_by, created_at) VALUES
-- Recent stock in
(1, 'in', 10, 2500.00, 25000.00, 'Monthly stock replenishment', 1, NOW() - INTERVAL 5 DAY),
(2, 'in', 5, 3500.00, 17500.00, 'Urgent restock - analyzer reagent', 1, NOW() - INTERVAL 3 DAY),
(6, 'in', 50, 450.00, 22500.00, 'Bulk purchase - blood tubes', 1, NOW() - INTERVAL 7 DAY),

-- Recent stock out (usage)
(3, 'out', 5, 850.00, 4250.00, 'Used for glucose tests', 1, NOW() - INTERVAL 2 DAY),
(6, 'out', 20, 450.00, 9000.00, 'Blood sample collection', 1, NOW() - INTERVAL 1 DAY),
(9, 'out', 15, 280.00, 4200.00, 'Urine sample collection', 1, NOW() - INTERVAL 1 DAY),
(4, 'out', 8, 280.00, 2240.00, 'Daily consumable usage', 1, NOW() - INTERVAL 12 HOUR),

-- Adjustments
(5, 'adjustment', -2, 1200.00, -2400.00, 'Stock count correction', 1, NOW() - INTERVAL 4 DAY),
(7, 'expired', -5, 180.00, -900.00, 'Expired stock disposal', 1, NOW() - INTERVAL 6 DAY);