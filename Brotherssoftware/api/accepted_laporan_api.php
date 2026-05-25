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
            $sql .= " AND LOWER(divisi) = ?";
            $params[] = strtolower($divisi);
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
        // Handle tanggal_laporan - if empty, null, or 'null' string, use current date
        $tanggal_laporan_input = $_POST['tanggal_laporan'] ?? '';
        if (empty($tanggal_laporan_input) || $tanggal_laporan_input === 'null' || $tanggal_laporan_input === 'undefined') {
            $tanggal_laporan = date('Y-m-d'); // Default to current date
        } else {
            $tanggal_laporan = $tanggal_laporan_input;
        }
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

        // Get harga_rate from farm_price_config based on divisi
        $harga_rate = null;
        $config_key = '';
        if (strtolower($divisi) === 'mechanic') {
            $config_key = 'mechanic_gaji_dasar';
        } elseif (strtolower($divisi) === 'farmer') {
            $config_key = 'farm_gaji_per_bibit';
        } elseif (strtolower($divisi) === 'cargo driver') {
            $config_key = 'cargo_gaji_per_crate';
        }

        if (!empty($config_key)) {
            $stmt = $pdo->prepare("SELECT config_value FROM farm_price_config WHERE config_key = ?");
            $stmt->execute([$config_key]);
            $config = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($config) {
                $harga_rate = floatval($config['config_value']);
            }
        }

        // Insert with tanggal_laporan
        $stmt = $pdo->prepare("INSERT INTO accepted_laporan (source_type, source_id, nama_karyawan, divisi, jumlah_used, jumlah_value, keterangan, tanggal_laporan, accepted_by, harga_rate, tanggal_accept) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$source_type, $source_id, $nama_karyawan, $divisi, $jumlah_used, $jumlah_value, $keterangan, $tanggal_laporan, $accepted_by, $harga_rate]);

        echo json_encode(['success' => true, 'message' => 'Laporan berhasil di-accept!', 'harga_rate' => $harga_rate, 'tanggal_laporan' => $tanggal_laporan]);
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
        $deleted = $stmt->rowCount();
        echo json_encode(['success' => true, 'message' => "Berhasil hapus $deleted data", 'deleted' => $deleted]);
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

    // Migrate old data with current config prices
    case 'migrate_prices':
        $updated = 0;

        // Get all config values
        $stmt = $pdo->query("SELECT config_key, config_value FROM farm_price_config");
        $configMap = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $configMap[$row['config_key']] = floatval($row['config_value']);
        }

        // Update accepted_laporan where harga_rate is NULL
        $stmt = $pdo->query("SELECT id, divisi FROM accepted_laporan WHERE harga_rate IS NULL");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $harga = null;
            $divisi = strtolower($row['divisi']);

            if ($divisi === 'mechanic') {
                $harga = $configMap['mechanic_gaji_dasar'] ?? null;
            } elseif ($divisi === 'farmer') {
                $harga = $configMap['farm_gaji_per_bibit'] ?? null;
            } elseif ($divisi === 'cargo driver') {
                $harga = $configMap['cargo_gaji_per_crate'] ?? null;
            }

            if ($harga !== null) {
                $update = $pdo->prepare("UPDATE accepted_laporan SET harga_rate = ? WHERE id = ?");
                $update->execute([$harga, $row['id']]);
                $updated++;
            }
        }

        echo json_encode(['success' => true, 'message' => "Migrated $updated accepted_laporan records", 'updated' => $updated]);
        break;

    // Fix incorrect dates (0000-00-00 or null)
    case 'fix_dates':
        $updated = 0;
        try {
            // Update records where tanggal_laporan is invalid
            $stmt = $pdo->prepare("UPDATE accepted_laporan SET tanggal_laporan = DATE(tanggal_accept) WHERE tanggal_laporan IS NULL OR tanggal_laporan = '0000-00-00' OR tanggal_laporan = ''");
            $stmt->execute();
            $updated = $stmt->rowCount();
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to fix dates: ' . $e->getMessage()]);
            exit;
        }
        echo json_encode(['success' => true, 'message' => "Fixed $updated records with invalid dates", 'updated' => $updated]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action. Available: get_all, get_by_bulan, accept, delete, get_stats']);
}