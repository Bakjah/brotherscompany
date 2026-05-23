<?php
// Farm Price Config API
// Price & Salary Configuration untuk Cargo, Farm, dan Mechanic

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

    // Ambil semua config (flat list)
    case 'get_all':
        $stmt = $pdo->query("SELECT * FROM farm_price_config ORDER BY category, id");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    // Ambil semua config grouped by category (untuk hitung gaji)
    case 'get_all_grouped':
        $stmt = $pdo->query("SELECT * FROM farm_price_config ORDER BY category, id");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $grouped = [];
        foreach ($data as $row) {
            $cat = $row['category'] ?: 'other';
            if (!isset($grouped[$cat])) $grouped[$cat] = [];
            $grouped[$cat][] = $row;
        }
        echo json_encode(['success' => true, 'data' => $grouped]);
        break;

    // Ambil config berdasarkan category
    case 'get_by_category':
        $category = $_GET['category'] ?? '';
        if (empty($category)) {
            echo json_encode(['success' => false, 'message' => 'Category required']);
            exit;
        }
        $stmt = $pdo->prepare("SELECT * FROM farm_price_config WHERE category = ? ORDER BY id");
        $stmt->execute([$category]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    // Ambil satu config berdasarkan key
    case 'get':
        $key = $_GET['key'] ?? '';
        if (empty($key)) {
            echo json_encode(['success' => false, 'message' => 'Config key required']);
            exit;
        }
        $stmt = $pdo->prepare("SELECT * FROM farm_price_config WHERE config_key = ?");
        $stmt->execute([$key]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Config not found']);
        }
        break;

    // Update satu config berdasarkan key
    case 'update':
        $key = $_POST['key'] ?? null;
        $value = $_POST['value'] ?? null;

        // Handle JSON body (application/json)
        if ($key === null && isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
            $input = json_decode(file_get_contents('php://input'), true);
            $key = $input['key'] ?? null;
            $value = $input['value'] ?? null;
        }

        if (empty($key)) {
            echo json_encode(['success' => false, 'message' => 'Config key required']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE farm_price_config SET config_value = ? WHERE config_key = ?");
            $stmt->execute([$value, $key]);
            echo json_encode(['success' => true, 'message' => 'Config updated successfully', 'key' => $key, 'value' => $value]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to update config: ' . $e->getMessage()]);
        }
        break;

    // Update multiple config dalam satu request
    case 'update_batch':
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['configs'])) {
            $input = $_POST;
        }

        if (!isset($input['configs']) || empty($input['configs'])) {
            echo json_encode(['success' => false, 'message' => 'Configs array required']);
            exit;
        }

        $updated = 0;
        $errors = [];

        try {
            $pdo->beginTransaction();
            foreach ($input['configs'] as $config) {
                $key = $config['key'] ?? null;
                $value = $config['value'] ?? null;

                if (empty($key)) {
                    $errors[] = 'Missing key for config';
                    continue;
                }

                $stmt = $pdo->prepare("UPDATE farm_price_config SET config_value = ? WHERE config_key = ?");
                $stmt->execute([$value, $key]);
                $updated++;
            }
            $pdo->commit();
            echo json_encode([
                'success' => true,
                'message' => "Updated $updated configuration(s)",
                'updated_count' => $updated,
                'errors' => $errors
            ]);
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Failed to update: ' . $e->getMessage()]);
        }
        break;

    // Insert new config
    case 'insert':
        $key = $_POST['key'] ?? '';
        $label = $_POST['label'] ?? '';
        $value = $_POST['value'] ?? 0;
        $unit = $_POST['unit'] ?? '';
        $category = $_POST['category'] ?? '';
        $deskripsi = $_POST['deskripsi'] ?? '';

        if (empty($key) || empty($label) || empty($category)) {
            echo json_encode(['success' => false, 'message' => 'Key, label, and category are required']);
            exit;
        }

        // Check if key exists
        $stmt = $pdo->prepare("SELECT id FROM farm_price_config WHERE config_key = ?");
        $stmt->execute([$key]);

        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Config key already exists']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO farm_price_config (config_key, config_label, config_value, config_unit, category, deskripsi) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$key, $label, $value, $unit, $category, $deskripsi]);

        echo json_encode(['success' => true, 'message' => 'Config created successfully']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action. Available: get_all, get, get_by_category, update, update_batch, insert']);
}