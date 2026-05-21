<?php
// Laporan Farmer API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

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
        $bulan = $_GET['bulan'] ?? null;
        $tanggal = $_GET['tanggal'] ?? null;

        $sql = "SELECT * FROM laporan_farmer WHERE 1=1";
        $params = [];

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
        $stmt = $pdo->prepare("SELECT id, nama, divisi FROM employees WHERE divisi = 'Farmer' ORDER BY nama");
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'add':
        $nama_karyawan = $_POST['nama_karyawan'] ?? '';
        $bibit_used = intval($_POST['bibit_used'] ?? 0);
        $panen_hasil = floatval($_POST['panen_hasil'] ?? 0);
        $keterangan = $_POST['keterangan'] ?? '';
        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');

        if (empty($nama_karyawan)) {
            echo json_encode(['success' => false, 'message' => 'Nama harus diisi']);
            exit;
        }

        // Validate nama exists in employees with matching divisi
        $stmt = $pdo->prepare("SELECT id, nama, divisi FROM employees WHERE nama = ? AND divisi = 'Farmer'");
        $stmt->execute([$nama_karyawan]);
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$employee) {
            echo json_encode(['success' => false, 'message' => 'Nama tidak ditemukan atau bukan Farmer']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO laporan_farmer (nama_karyawan, bibit_used, panen_hasil, keterangan, tanggal) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nama_karyawan, $bibit_used, $panen_hasil, $keterangan, $tanggal]);
        echo json_encode(['success' => true, 'message' => 'Laporan berhasil ditambahkan', 'id' => $pdo->lastInsertId()]);
        break;

    case 'get_stats':
        $bulan = $_GET['bulan'] ?? date('Y-m');
        $stmt = $pdo->prepare("
            SELECT
                COUNT(*) as total_laporan,
                SUM(bibit_used) as total_bibit,
                SUM(panen_hasil) as total_panen
            FROM laporan_farmer
            WHERE DATE_FORMAT(tanggal, '%Y-%m') = ?
        ");
        $stmt->execute([$bulan]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'delete':
        $id = $_POST['id'] ?? 0;
        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM laporan_farmer WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Laporan dihapus']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
}