<?php
// Pekarja API - Connect to database
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Database connection
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

// Get action from request
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Handle different actions
switch ($action) {
    case 'get_all':
        // Get all workers/pekerja
        $stmt = $pdo->query("SELECT * FROM users WHERE role = 'employee' ORDER BY created_at DESC");
        $workers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $workers]);
        break;

    case 'search':
        // Search workers
        $query = $_GET['query'] ?? '';
        $searchTerm = "%{$query}%";
        $stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'employee' AND (username LIKE ? OR full_name LIKE ?) ORDER BY created_at DESC");
        $stmt->execute([$searchTerm, $searchTerm]);
        $workers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $workers]);
        break;

    case 'add':
        // Add new worker
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $full_name = $_POST['full_name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $jabatan = $_POST['jabatan'] ?? 'Mechanic';

        if (empty($username) || empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Username, email, dan password harus diisi']);
            exit;
        }

        // Check if username exists
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $checkStmt->execute([$username]);
        if ($checkStmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Username sudah digunakan!']);
            exit;
        }

        // Check if email exists
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$email]);
        if ($checkStmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Email sudah digunakan!']);
            exit;
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, phone, role) VALUES (?, ?, ?, ?, ?, 'employee')");
        $stmt->execute([$username, $email, $hashedPassword, $full_name ?: $username, $phone]);

        $newId = $pdo->lastInsertId();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$newId]);
        $newWorker = $stmt->fetch(PDO::FETCH_ASSOC);
        unset($newWorker['password']); // Remove password from response

        echo json_encode(['success' => true, 'message' => 'Pekerja berhasil ditambahkan', 'data' => $newWorker]);
        break;

    case 'update':
        // Update worker
        $id = $_POST['id'] ?? 0;
        $full_name = $_POST['full_name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $jabatan = $_POST['jabatan'] ?? '';
        $status = $_POST['status'] ?? '';

        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ? WHERE id = ? AND role = 'employee'");
        $stmt->execute([$full_name, $phone, $id]);

        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $worker = $stmt->fetch(PDO::FETCH_ASSOC);
        unset($worker['password']);

        echo json_encode(['success' => true, 'message' => 'Pekerja berhasil diupdate', 'data' => $worker]);
        break;

    case 'delete':
        // Delete worker (soft delete - change to inactive)
        $id = $_POST['id'] ?? 0;

        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
            exit;
        }

        // Don't actually delete, just mark as inactive or you can delete
        $stmt = $pdo->prepare("UPDATE users SET role = 'user' WHERE id = ? AND role = 'employee'");
        $stmt->execute([$id]);

        echo json_encode(['success' => true, 'message' => 'Pekerja berhasil dihapus']);
        break;

    case 'get':
        // Get single worker
        $id = $_GET['id'] ?? 0;

        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'employee'");
        $stmt->execute([$id]);
        $worker = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($worker) {
            unset($worker['password']);
            echo json_encode(['success' => true, 'data' => $worker]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Pekerja tidak ditemukan']);
        }
        break;

    case 'stats':
        // Get worker statistics
        $totalStmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'employee'");
        $total = $totalStmt->fetch()['total'];

        $activeStmt = $pdo->query("SELECT COUNT(*) as active FROM users WHERE role = 'employee' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
        $active = $activeStmt->fetch()['active'];

        echo json_encode(['success' => true, 'data' => [
            'total' => $total,
            'active_30_days' => $active
        ]]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
}