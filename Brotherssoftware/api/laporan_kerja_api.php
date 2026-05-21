<?php
// Laporan Kerja API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Set timezone to Indonesia West Java (WIB = UTC+7)
date_default_timezone_set('Asia/Jakarta');

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
        $divisi = $_GET['divisi'] ?? null;
        $bulan = $_GET['bulan'] ?? null;
        $tanggal = $_GET['tanggal'] ?? null;

        $sql = "SELECT * FROM laporan_mechanic WHERE 1=1";
        $params = [];

        if ($divisi) {
            $sql .= " AND divisi = ?";
            $params[] = $divisi;
        }
        if ($bulan) {
            $sql .= " AND DATE_FORMAT(tanggal, '%Y-%m') = ?";
            $params[] = $bulan;
        }
        if ($tanggal) {
            $sql .= " AND tanggal = ?";
            $params[] = $tanggal;
        }

        $sql .= " ORDER BY tanggal DESC, id DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $data, 'total' => count($data)]);
        break;

    case 'get_employees':
        $divisi = $_GET['divisi'] ?? null;
        if ($divisi) {
            $stmt = $pdo->prepare("SELECT id, nama, divisi FROM employees WHERE divisi = ? ORDER BY nama");
            $stmt->execute([$divisi]);
        } else {
            $stmt = $pdo->query("SELECT id, nama, divisi FROM employees ORDER BY divisi, nama");
        }
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'add':
        $divisi = $_POST['divisi'] ?? '';
        $nama_karyawan = $_POST['nama_karyawan'] ?? '';
        $compo_used = intval($_POST['compo_used'] ?? 0);
        $money_stored = floatval($_POST['money_stored'] ?? 0);
        $keterangan = $_POST['keterangan'] ?? '';
        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');

        if (empty($divisi) || empty($nama_karyawan)) {
            echo json_encode(['success' => false, 'message' => 'Divisi dan Nama harus diisi']);
            exit;
        }

        // Validate nama exists in employees with matching divisi
        $stmt = $pdo->prepare("SELECT id, nama, divisi FROM employees WHERE nama = ? AND divisi = ?");
        $stmt->execute([$nama_karyawan, $divisi]);
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$employee) {
            echo json_encode(['success' => false, 'message' => 'Nama tidak ditemukan atau tidak sesuai dengan divisi ' . ucfirst($divisi)]);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO laporan_mechanic (divisi, nama_karyawan, compo_used, money_stored, keterangan, tanggal) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$divisi, $nama_karyawan, $compo_used, $money_stored, $keterangan, $tanggal]);
        echo json_encode(['success' => true, 'message' => 'Laporan berhasil ditambahkan', 'id' => $pdo->lastInsertId()]);
        break;

    case 'get_stats':
        $bulan = $_GET['bulan'] ?? date('Y-m');
        $stmt = $pdo->prepare("
            SELECT
                divisi,
                COUNT(*) as total_laporan,
                SUM(compo_used) as total_compo,
                SUM(money_stored) as total_money
            FROM laporan_mechanic
            WHERE DATE_FORMAT(tanggal, '%Y-%m') = ?
            GROUP BY divisi
        ");
        $stmt->execute([$bulan]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'delete':
        $id = $_POST['id'] ?? 0;
        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM laporan_mechanic WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Laporan dihapus']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
}