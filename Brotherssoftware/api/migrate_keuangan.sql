-- =====================================================
-- Database Migration for Keuangan Company - Component Price
-- Run this SQL in phpMyAdmin or MySQL CLI
-- =====================================================

USE brothers_company;

-- Table for storing mechanic component price (used in keuangan calculation)
CREATE TABLE IF NOT EXISTS mechanic_component_price (
    id INT AUTO_INCREMENT PRIMARY KEY,
    harga_per_component DECIMAL(15,2) DEFAULT 1.00,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default value if not exists
INSERT IGNORE INTO mechanic_component_price (id, harga_per_component) VALUES (1, 1.00);