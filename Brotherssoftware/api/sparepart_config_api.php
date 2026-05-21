<?php
// Sparepart Config API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

$host = 'localhost';
$dbname = 'brothers_company';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'get':
        $stmt = $pdo->query("SELECT * FROM sparepart_config ORDER BY id DESC LIMIT 1");
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            // Insert default if not exists
            $stmt = $pdo->prepare("INSERT INTO sparepart_config (nama, harga_per_unit) VALUES ('Sparepart Standard', 2.0)");
            $stmt->execute();
            echo json_encode(['success' => true, 'data' => ['harga_per_unit' => 2.0]]);
        }
        break;

    case 'update':
        $harga = $_POST['harga_per_unit'] ?? 2.0;
        $nama = $_POST['nama'] ?? 'Sparepart Standard';

        // Check if exists
        $stmt = $pdo->query("SELECT id FROM sparepart_config LIMIT 1");
        if ($stmt->fetch()) {
            // Update
            $stmt = $pdo->prepare("UPDATE sparepart_config SET harga_per_unit = ?, nama = ? ORDER BY id DESC LIMIT 1");
            $stmt->execute([$harga, $nama]);
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO sparepart_config (nama, harga_per_unit) VALUES (?, ?)");
            $stmt->execute([$nama, $harga]);
        }

        echo json_encode(['success' => true, 'message' => 'Sparepart config updated successfully']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action not valid']);
}