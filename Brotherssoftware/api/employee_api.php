<?php
// Employee API
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
    case 'search':
        $query = $_GET['q'] ?? '';
        if (empty($query)) {
            $stmt = $pdo->query("SELECT * FROM employees ORDER BY divisi, nama");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $searchTerm = "%{$query}%";
            $stmt = $pdo->prepare("SELECT * FROM employees WHERE nama LIKE ? OR telepon LIKE ? ORDER BY divisi, nama");
            $stmt->execute([$searchTerm, $searchTerm]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'get_all':
        $stmt = $pdo->query("SELECT * FROM employees ORDER BY divisi, nama");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'add':
        $nama = $_POST['nama'] ?? '';
        $telepon = $_POST['telepon'] ?? '';
        $no_rekening = $_POST['no_rekening'] ?? '';
        $divisi = $_POST['divisi'] ?? '';

        if (empty($nama) || empty($divisi)) {
            echo json_encode(['success' => false, 'message' => 'Nama dan Divisi harus diisi']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO employees (nama, telepon, no_rekening, divisi) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nama, $telepon, $no_rekening, $divisi]);

        $newId = $pdo->lastInsertId();
        $stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
        $stmt->execute([$newId]);
        $newEmployee = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'message' => 'Employee berhasil ditambahkan', 'data' => $newEmployee]);
        break;

    case 'delete':
        $id = $_POST['id'] ?? 0;
        $stmt = $pdo->prepare("DELETE FROM employees WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Employee berhasil dihapus']);
        break;

    case 'get':
        $id = $_GET['id'] ?? 0;
        $stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
        $stmt->execute([$id]);
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($employee) {
            echo json_encode(['success' => true, 'data' => $employee]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Employee tidak ditemukan']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
}