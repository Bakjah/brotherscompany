<?php
// Tugas Harian API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Set timezone to Indonesia West Java (WIB = UTC+7)
date_default_timezone_set('Asia/Jakarta');

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
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Function to auto-update status based on time
function updateAutoStatus($pdo, $tanggal = null) {
    $now = date('H:i:s');
    $today = $tanggal ?? date('Y-m-d');

    // Update tasks where jam_selesai has passed -> selesai
    $stmt = $pdo->prepare("UPDATE tugas_harian SET status = 'selesai' WHERE tanggal = ? AND jam_selesai < ? AND status = 'proses'");
    $stmt->execute([$today, $now]);

    // Update tasks where jam_mulai has passed and still pending -> proses
    $stmt = $pdo->prepare("UPDATE tugas_harian SET status = 'proses' WHERE tanggal = ? AND jam_mulai <= ? AND jam_selesai > ? AND status = 'pending'");
    $stmt->execute([$today, $now, $now]);

    // Also check if today's tasks where jam_mulai has passed -> proses (if still pending and jam_selesai also passed, mark selesai)
    $stmt = $pdo->prepare("UPDATE tugas_harian SET status = 'selesai' WHERE tanggal = ? AND jam_selesai < ? AND status = 'pending'");
    $stmt->execute([$today, $now]);
}

switch ($action) {
    case 'get_today':
        updateAutoStatus($pdo);
        $stmt = $pdo->query("SELECT * FROM tugas_harian WHERE tanggal = CURRENT_DATE ORDER BY jam_mulai ASC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'get_all':
        $tanggal = $_GET['tanggal'] ?? null;
        updateAutoStatus($pdo);
        if ($tanggal) {
            $stmt = $pdo->prepare("SELECT * FROM tugas_harian WHERE tanggal = ? ORDER BY jam_mulai ASC");
            $stmt->execute([$tanggal]);
        } else {
            $stmt = $pdo->query("SELECT * FROM tugas_harian ORDER BY tanggal DESC, jam_mulai ASC");
        }
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'add':
        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        $jam_mulai = $_POST['jam_mulai'] ?? '08:00:00';
        $jam_selesai = $_POST['jam_selesai'] ?? '09:00:00';
        $tugas = $_POST['tugas'] ?? '';
        $status = $_POST['status'] ?? 'pending';

        if (empty($tugas)) {
            echo json_encode(['success' => false, 'message' => 'Tugas tidak boleh kosong']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO tugas_harian (tanggal, jam_mulai, jam_selesai, tugas, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$tanggal, $jam_mulai, $jam_selesai, $tugas, $status]);
        echo json_encode(['success' => true, 'message' => 'Tugas berhasil ditambahkan', 'id' => $pdo->lastInsertId()]);
        break;

    case 'update':
        $id = $_POST['id'] ?? 0;
        $jam_mulai = $_POST['jam_mulai'] ?? null;
        $jam_selesai = $_POST['jam_selesai'] ?? null;
        $tugas = $_POST['tugas'] ?? null;
        $status = $_POST['status'] ?? null;

        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID harus diisi']);
            exit;
        }

        $updates = [];
        $params = [];
        if ($jam_mulai !== null) { $updates[] = 'jam_mulai = ?'; $params[] = $jam_mulai; }
        if ($jam_selesai !== null) { $updates[] = 'jam_selesai = ?'; $params[] = $jam_selesai; }
        if ($tugas !== null) { $updates[] = 'tugas = ?'; $params[] = $tugas; }
        if ($status !== null) { $updates[] = 'status = ?'; $params[] = $status; }

        if (count($updates) === 0) {
            echo json_encode(['success' => false, 'message' => 'Tidak ada field yang diupdate']);
            exit;
        }

        $params[] = $id;
        $sql = "UPDATE tugas_harian SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode(['success' => true, 'message' => 'Tugas berhasil diupdate']);
        break;

    case 'update_status':
        $id = $_POST['id'] ?? 0;
        $status = $_POST['status'] ?? 'pending';

        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID harus diisi']);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE tugas_harian SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        echo json_encode(['success' => true, 'message' => 'Status berhasil diupdate']);
        break;

    case 'delete':
        $id = $_POST['id'] ?? 0;

        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID harus diisi']);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM tugas_harian WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Tugas berhasil dihapus']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
}