-- =====================================================
-- Database Migration for Keuangan Company - Price Snapshot
-- Run this SQL in phpMyAdmin or MySQL CLI
-- =====================================================

USE brothers_company;

-- 1. Add harga_rate column to accepted_laporan
-- This stores the salary rate when a laporan is accepted
ALTER TABLE accepted_laporan
ADD COLUMN IF NOT EXISTS harga_rate DECIMAL(15,2) DEFAULT NULL AFTER jumlah_value;

-- 2. Add harga_snapshot column to delivery_order
-- This stores the price when a delivery is completed
ALTER TABLE delivery_order
ADD COLUMN IF NOT EXISTS harga_snapshot DECIMAL(15,2) DEFAULT NULL AFTER jumlah_crate;

-- 3. Migrate old data (optional - fill in prices from current config)
-- This updates existing records with current config prices

-- For accepted_laporan: Set harga_rate based on divisi
UPDATE accepted_laporan
SET harga_rate = (
    SELECT COALESCE(config_value, 0)
    FROM farm_price_config
    WHERE config_key = CASE
        WHEN LOWER(divisi) = 'mechanic' THEN 'mechanic_gaji_dasar'
        WHEN LOWER(divisi) = 'farmer' THEN 'farm_gaji_per_bibit'
        WHEN LOWER(divisi) = 'cargo driver' THEN 'cargo_gaji_per_crate'
        ELSE NULL
    END
    LIMIT 1
)
WHERE harga_rate IS NULL;

-- For delivery_order: Set harga_snapshot based on jenis_delivery
-- Note: This is a one-time migration, future records will be set by API

-- Migration complete message
SELECT 'Migration complete! Columns added to tables.' AS status;