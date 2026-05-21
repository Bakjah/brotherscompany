<?php
// Price Config API
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
    case 'get_all':
        $stmt = $pdo->query("SELECT * FROM price_config ORDER BY id");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'get':
        $jenis = $_GET['jenis'] ?? '';
        if (empty($jenis)) {
            echo json_encode(['success' => false, 'message' => 'Jenis layanan required']);
            exit;
        }
        $stmt = $pdo->prepare("SELECT * FROM price_config WHERE jenis_layanan = ?");
        $stmt->execute([$jenis]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Data not found']);
        }
        break;

    case 'update':
        $jenis = $_POST['jenis'] ?? '';
        $multiplier = $_POST['multiplier'] ?? 3.0;
        $nama_layanan = $_POST['nama_layanan'] ?? '';
        $deskripsi = $_POST['deskripsi'] ?? '';

        if (empty($jenis)) {
            echo json_encode(['success' => false, 'message' => 'Jenis layanan required']);
            exit;
        }

        // Check if exists
        $stmt = $pdo->prepare("SELECT id FROM price_config WHERE jenis_layanan = ?");
        $stmt->execute([$jenis]);

        if ($stmt->fetch()) {
            // Update
            $stmt = $pdo->prepare("UPDATE price_config SET multiplier = ?, nama_layanan = COALESCE(NULLIF(?, ''), nama_layanan), deskripsi = COALESCE(NULLIF(?, ''), deskripsi) WHERE jenis_layanan = ?");
            $stmt->execute([$multiplier, $nama_layanan, $deskripsi, $jenis]);
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO price_config (jenis_layanan, nama_layanan, multiplier, deskripsi) VALUES (?, ?, ?, ?)");
            $stmt->execute([$jenis, $nama_layanan ?: $jenis, $multiplier, $deskripsi]);
        }

        echo json_encode(['success' => true, 'message' => 'Price config updated successfully']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action not valid']);
}