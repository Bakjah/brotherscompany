<?php
// Laporan Mechanic API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

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

switch ($action) {
    case 'get_all':
        $bulan = $_GET['bulan'] ?? null;
        $tanggal = $_GET['tanggal'] ?? null;

        $sql = "SELECT * FROM laporan WHERE 1=1";
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
        $stmt = $pdo->prepare("SELECT id, nama, divisi FROM employees WHERE divisi = 'Mechanic' ORDER BY nama");
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'add':
        $nama_karyawan = $_POST['nama_karyawan'] ?? '';
        $compo_used = intval($_POST['compo_used'] ?? 0);
        $money_stored = floatval($_POST['money_stored'] ?? 0);
        $keterangan = $_POST['keterangan'] ?? '';
        $tanggal_input = $_POST['tanggal'] ?? '';
        // Use provided tanggal, or fallback to current date
        $tanggal = (!empty($tanggal_input)) ? $tanggal_input : date('Y-m-d');

        if (empty($nama_karyawan)) {
            echo json_encode(['success' => false, 'message' => 'Nama harus diisi']);
            exit;
        }

        // Validate nama exists in employees with matching divisi
        $stmt = $pdo->prepare("SELECT id, nama, divisi FROM employees WHERE nama = ? AND divisi = 'Mechanic'");
        $stmt->execute([$nama_karyawan]);
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$employee) {
            echo json_encode(['success' => false, 'message' => 'Nama tidak ditemukan atau bukan Mechanic']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO laporan (nama_karyawan, compo_used, money_stored, keterangan, tanggal) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nama_karyawan, $compo_used, $money_stored, $keterangan, $tanggal]);
        echo json_encode(['success' => true, 'message' => 'Laporan berhasil ditambahkan', 'id' => $pdo->lastInsertId()]);
        break;

    case 'get_stats':
        $bulan = $_GET['bulan'] ?? date('Y-m');
        $stmt = $pdo->prepare("
            SELECT
                COUNT(*) as total_laporan,
                SUM(compo_used) as total_compo,
                SUM(money_stored) as total_money
            FROM laporan
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
        $stmt = $pdo->prepare("DELETE FROM laporan WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Laporan dihapus']);
        break;

    case 'delete_all':
        $stmt = $pdo->query("DELETE FROM laporan");
        echo json_encode(['success' => true, 'message' => 'Semua laporan mechanic dihapus']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
}