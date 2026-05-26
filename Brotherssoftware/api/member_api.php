<?php
// MemberApp API - Connect to database
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Database connection
// Auto-detect: localhost = root, hosting = brothers_company
$is_localhost = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1', '::1']) ||
                strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false;

$host = 'localhost';
$dbname = 'brothers_company';
$username = $is_localhost ? 'root' : 'brothers_company';
$password = $is_localhost ? '' : '#?12jj16op';

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
        // Auto-cleanup members inactive for 30 days
        $cleanupDays = 30;
        $stmt = $pdo->prepare("DELETE FROM members WHERE last_compo_update IS NOT NULL AND last_compo_update < DATE_SUB(NOW(), INTERVAL ? DAY)");
        $stmt->execute([$cleanupDays]);
        $stmt = $pdo->prepare("DELETE FROM members WHERE last_compo_update IS NULL AND tanggal_daftar < DATE_SUB(CURDATE(), INTERVAL ? DAY)");
        $stmt->execute([$cleanupDays]);

        // Get all members
        $stmt = $pdo->query("SELECT * FROM members ORDER BY tanggal_daftar DESC");
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $members]);
        break;

    case 'search':
        // Auto-cleanup first
        $cleanupDays = 30;
        $stmt = $pdo->prepare("DELETE FROM members WHERE last_compo_update IS NOT NULL AND last_compo_update < DATE_SUB(NOW(), INTERVAL ? DAY)");
        $stmt->execute([$cleanupDays]);
        $stmt = $pdo->prepare("DELETE FROM members WHERE last_compo_update IS NULL AND tanggal_daftar < DATE_SUB(CURDATE(), INTERVAL ? DAY)");
        $stmt->execute([$cleanupDays]);

        // Search members by name or telepon
        $query = $_GET['query'] ?? '';
        $searchTerm = "%{$query}%";
        $stmt = $pdo->prepare("SELECT * FROM members WHERE nama LIKE ? OR telepon LIKE ? ORDER BY tanggal_daftar DESC");
        $stmt->execute([$searchTerm, $searchTerm]);
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $members]);
        break;

    case 'add':
        // Add new member
        $nama = $_POST['nama'] ?? '';
        $telepon = $_POST['telepon'] ?? '';

        if (empty($nama) || empty($telepon)) {
            echo json_encode(['success' => false, 'message' => 'Nama dan telepon harus diisi']);
            exit;
        }

        // Check if telepon already exists
        $checkStmt = $pdo->prepare("SELECT id FROM members WHERE telepon = ?");
        $checkStmt->execute([$telepon]);
        if ($checkStmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Nomor telepon sudah terdaftar!']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO members (nama, telepon, last_compo_update) VALUES (?, ?, NOW())");
        $stmt->execute([$nama, $telepon]);

        $newId = $pdo->lastInsertId();
        $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
        $stmt->execute([$newId]);
        $newMember = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'message' => 'Member berhasil ditambahkan', 'data' => $newMember]);
        break;

    case 'update_compo':
        // Update compo_used for a member
        $id = $_POST['id'] ?? 0;
        $jumlah = $_POST['jumlah'] ?? 0;

        if ($id <= 0 || $jumlah <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID dan jumlah harus valid']);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE members SET compo_used = compo_used + ?, last_compo_update = NOW() WHERE id = ?");
        $stmt->execute([$jumlah, $id]);

        $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
        $stmt->execute([$id]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'message' => 'Compo berhasil ditambahkan', 'data' => $member]);
        break;

    case 'cleanup':
        // Manual cleanup - delete members inactive for 30 days
        $cleanupDays = 30;
        $stmt = $pdo->prepare("DELETE FROM members WHERE last_compo_update IS NOT NULL AND last_compo_update < DATE_SUB(NOW(), INTERVAL ? DAY)");
        $stmt->execute([$cleanupDays]);
        $deleted1 = $stmt->rowCount();

        $stmt = $pdo->prepare("DELETE FROM members WHERE last_compo_update IS NULL AND tanggal_daftar < DATE_SUB(CURDATE(), INTERVAL ? DAY)");
        $stmt->execute([$cleanupDays]);
        $deleted2 = $stmt->rowCount();

        echo json_encode(['success' => true, 'message' => "Berhasil hapus $deleted1 + $deleted2 member tidak aktif (30 hari)"]);
        break;

    case 'get':
        // Get single member by ID
        $id = $_GET['id'] ?? 0;

        $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
        $stmt->execute([$id]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($member) {
            echo json_encode(['success' => true, 'data' => $member]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Member tidak ditemukan']);
        }
        break;

    case 'delete':
        // Delete member
        $id = $_POST['id'] ?? 0;

        $stmt = $pdo->prepare("DELETE FROM members WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['success' => true, 'message' => 'Member berhasil dihapus']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
}