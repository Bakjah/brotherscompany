<?php
// Mechanic Component Price API
// Untuk menyimpan harga 1 component untuk perhitungan keuangan company

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$host = 'localhost';
$dbname = 'brothers_company';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Create table if not exists
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS mechanic_component_price (
        id INT AUTO_INCREMENT PRIMARY KEY,
        harga_per_component DECIMAL(15,2) DEFAULT 1.00,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    // Insert default row if not exists
    $pdo->exec("INSERT IGNORE INTO mechanic_component_price (id, harga_per_component) VALUES (1, 1.00)");
} catch (PDOException $e) {
    // Table creation might fail on first run, continue anyway
}

$action = $_GET['action'] ?? $_POST['action'] ?? 'get';

switch ($action) {
    case 'get':
        // Get the component price
        try {
            $stmt = $pdo->query("SELECT * FROM mechanic_component_price WHERE id = 1");
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($data) {
                echo json_encode(['success' => true, 'data' => $data]);
            } else {
                echo json_encode(['success' => true, 'data' => ['id' => 1, 'harga_per_component' => '1.00']]);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => true, 'data' => ['id' => 1, 'harga_per_component' => '1.00']]);
        }
        break;

    case 'update':
        $harga = floatval($_POST['harga'] ?? $_GET['harga'] ?? 1.00);
        try {
            $stmt = $pdo->prepare("UPDATE mechanic_component_price SET harga_per_component = ? WHERE id = 1");
            $stmt->execute([$harga]);
            echo json_encode(['success' => true, 'message' => 'Harga component berhasil diupdate ke $' . number_format($harga, 2)]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Update failed: ' . $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
}
