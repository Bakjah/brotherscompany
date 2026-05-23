<?php
// Database Migration - Add Price Snapshot Columns
// Run this script ONCE to add price tracking to Keuangan Company

$host = 'localhost';
$dbname = 'brothers_company';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

echo "Connected to database: $dbname\n\n";

try {
    // 1. Add harga_rate column to accepted_laporan
    $sql1 = "ALTER TABLE accepted_laporan ADD COLUMN harga_rate DECIMAL(15,2) DEFAULT NULL AFTER jumlah_value";
    $pdo->exec($sql1);
    echo "✓ Added 'harga_rate' column to accepted_laporan table\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "✓ Column 'harga_rate' already exists in accepted_laporan (skipped)\n";
    } else {
        echo "✗ Error adding harga_rate: " . $e->getMessage() . "\n";
    }
}

try {
    // 2. Add harga_snapshot column to delivery_order
    $sql2 = "ALTER TABLE delivery_order ADD COLUMN harga_snapshot DECIMAL(15,2) DEFAULT NULL AFTER jumlah_crate";
    $pdo->exec($sql2);
    echo "✓ Added 'harga_snapshot' column to delivery_order table\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "✓ Column 'harga_snapshot' already exists in delivery_order (skipped)\n";
    } else {
        echo "✗ Error adding harga_snapshot: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Migration Complete ===\n";
echo "Now update the APIs to store prices when data is created.\n";