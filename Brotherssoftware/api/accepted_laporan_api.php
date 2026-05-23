<?php
// Accepted Laporan API
// Untuk menyimpan laporan yang sudah di-accept oleh admin

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

    // Ambil semua accepted laporan (dengan filter opsional)
    case 'get_all':
        $bulan = $_GET['bulan'] ?? '';
        $divisi = $_GET['divisi'] ?? '';

        $sql = "SELECT * FROM accepted_laporan WHERE 1=1";
        $params = [];

        if (!empty($bulan)) {
            $sql .= " AND DATE_FORMAT(tanggal_laporan, '%Y-%m') = ?";
            $params[] = $bulan;
        }

        if (!empty($divisi)) {
            $sql .= " AND divisi = ?";
            $params[] = $divisi;
        }

        $sql .= " ORDER BY id DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    // Accept laporan baru
    case 'accept':
        $source_type = $_POST['source_type'] ?? '';
        $source_id = $_POST['source_id'] ?? '';
        $nama_karyawan = $_POST['nama_karyawan'] ?? '';
        $divisi = $_POST['divisi'] ?? '';
        $jumlah_used = $_POST['jumlah_used'] ?? 0;
        $jumlah_value = $_POST['jumlah_value'] ?? 0;
        $keterangan = $_POST['keterangan'] ?? '';
        $tanggal_laporan = $_POST['tanggal_laporan'] ?? date('Y-m-d');
        $accepted_by = $_POST['accepted_by'] ?? 'Admin';

        if (empty($source_type) || empty($source_id)) {
            echo json_encode(['success' => false, 'message' => 'Source type and ID required']);
            exit;
        }

        // Check if already accepted
        $stmt = $pdo->prepare("SELECT id FROM accepted_laporan WHERE source_type = ? AND source_id = ?");
        $stmt->execute([$source_type, $source_id]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Laporan sudah di-accept sebelumnya']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO accepted_laporan (source_type, source_id, nama_karyawan, divisi, jumlah_used, jumlah_value, keterangan, tanggal_laporan, accepted_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$source_type, $source_id, $nama_karyawan, $divisi, $jumlah_used, $jumlah_value, $keterangan, $tanggal_laporan, $accepted_by]);

        echo json_encode(['success' => true, 'message' => 'Laporan berhasil di-accept!']);
        break;

    // Get completed cargo deliveries for gaji calculation
    case 'get_cargo_deliveries':
        $bulan = $_GET['bulan'] ?? '';
        $stmt = $pdo->query("SELECT * FROM delivery_order WHERE status = 'selesai' ORDER BY tanggal_selesai DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    // Delete accepted laporan
    case 'delete':
        $id = $_POST['id'] ?? $_GET['id'] ?? '';

        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID required']);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM accepted_laporan WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['success' => true, 'message' => 'Accepted laporan berhasil dihapus!']);
        break;

    // Delete all accepted laporan
    case 'delete_all':
        $stmt = $pdo->query("DELETE FROM accepted_laporan");
        echo json_encode(['success' => true, 'message' => 'Semua accepted laporan berhasil dihapus!']);
        break;

    // Get statistics
    case 'get_stats':
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM accepted_laporan");
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        $stmt = $pdo->query("SELECT SUM(jumlah_used) as total_used FROM accepted_laporan");
        $total_used = $stmt->fetch(PDO::FETCH_ASSOC)['total_used'] ?? 0;

        $stmt = $pdo->query("SELECT SUM(jumlah_value) as total_value FROM accepted_laporan");
        $total_value = $stmt->fetch(PDO::FETCH_ASSOC)['total_value'] ?? 0;

        echo json_encode([
            'success' => true,
            'data' => [
                'total' => (int)$total,
                'total_used' => (int)$total_used,
                'total_value' => (float)$total_value
            ]
        ]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action. Available: get_all, get_by_bulan, accept, delete, get_stats']);
}