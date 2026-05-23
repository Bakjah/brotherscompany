<?php
// Delivery Order API
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
        $jenis = $_GET['jenis'] ?? null;
        $status = $_GET['status'] ?? null;
        $tanggal = $_GET['tanggal'] ?? null;

        $sql = "SELECT * FROM delivery_order WHERE 1=1";
        $params = [];

        if ($jenis) {
            $sql .= " AND jenis_delivery = ?";
            $params[] = $jenis;
        }
        if ($status) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
        if ($tanggal) {
            $sql .= " AND DATE(tanggal_input) = ?";
            $params[] = $tanggal;
        }

        $sql .= " ORDER BY tanggal_input DESC, id DESC LIMIT 200";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $data, 'total' => count($data)]);
        break;

    case 'get_pending':
        $stmt = $pdo->query("SELECT * FROM delivery_order WHERE status = 'pending' ORDER BY tanggal_input DESC LIMIT 50");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $data, 'total' => count($data)]);
        break;

    case 'get_by_driver':
        $driver_id = $_GET['driver_id'] ?? 0;
        $stmt = $pdo->prepare("SELECT * FROM delivery_order WHERE driver_id = ? ORDER BY tanggal_input DESC");
        $stmt->execute([$driver_id]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'add':
        $jenis_delivery = $_POST['jenis_delivery'] ?? '';
        $alamat_tujuan = $_POST['alamat_tujuan'] ?? '';
        $nama_penerima = $_POST['nama_penerima'] ?? '';
        $no_telepon = $_POST['no_telepon'] ?? '';
        $jumlah_crate = intval($_POST['jumlah_crate'] ?? 1);
        $catatan = $_POST['catatan'] ?? '';

        if (empty($jenis_delivery) || empty($alamat_tujuan) || empty($nama_penerima)) {
            echo json_encode(['success' => false, 'message' => 'Field wajib belum diisi']);
            exit;
        }

        if ($jumlah_crate < 1 || $jumlah_crate > 50) {
            echo json_encode(['success' => false, 'message' => 'Jumlah crate minimal 1, maksimal 50']);
            exit;
        }

        // Check max 50 records
        $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM delivery_order WHERE status = 'pending'");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($count['cnt'] >= 50) {
            echo json_encode(['success' => false, 'message' => 'Maksimal 50 order pending. Selesaikan order lama terlebih dahulu.']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO delivery_order (jenis_delivery, alamat_tujuan, nama_penerima, no_telepon, jumlah_crate, catatan) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$jenis_delivery, $alamat_tujuan, $nama_penerima, $no_telepon, $jumlah_crate, $catatan]);
        echo json_encode(['success' => true, 'message' => 'Order berhasil ditambahkan', 'id' => $pdo->lastInsertId()]);
        break;

    case 'ambildelivery':
        $id = $_POST['id'] ?? 0;
        $driver_id = $_POST['driver_id'] ?? 0;
        $driver_nama = $_POST['driver_nama'] ?? '';

        if (empty($id) || empty($driver_id) || empty($driver_nama)) {
            echo json_encode(['success' => false, 'message' => 'Data driver tidak valid']);
            exit;
        }

        // Verify driver exists in employees table with correct divisi
        $stmt = $pdo->prepare("SELECT id, nama, divisi FROM employees WHERE id = ? AND divisi = 'Cargo Driver'");
        $stmt->execute([$driver_id]);
        $driver = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$driver) {
            echo json_encode(['success' => false, 'message' => 'Driver tidak ditemukan atau bukan Cargo Driver']);
            exit;
        }

        // Update delivery order
        $stmt = $pdo->prepare("UPDATE delivery_order SET status = 'diambil', driver_id = ?, driver_nama = ?, tanggal_ambil = NOW() WHERE id = ? AND status = 'pending'");
        $stmt->execute([$driver_id, $driver_nama, $id]);

        if ($stmt->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'Order tidak ditemukan atau sudah diambil']);
        } else {
            echo json_encode(['success' => true, 'message' => 'Delivery berhasil diambil']);
        }
        break;

    case 'selesaikan':
        $id = $_POST['id'] ?? 0;

        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
            exit;
        }

        // Get delivery info for price snapshot
        $stmt = $pdo->prepare("SELECT jenis_delivery FROM delivery_order WHERE id = ? AND status = 'diambil'");
        $stmt->execute([$id]);
        $delivery = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$delivery) {
            echo json_encode(['success' => false, 'message' => 'Order tidak ditemukan atau belum diambil']);
            exit;
        }

        // Get harga_snapshot from farm_price_config based on jenis_delivery
        $harga_snapshot = null;
        $config_key = '';
        $jenis = $delivery['jenis_delivery'];

        if ($jenis === 'farmer_jual') {
            $config_key = 'farm_harga_jual_buah';
        } elseif ($jenis === 'compo') {
            $config_key = 'mechanic_harga_component';
        } elseif ($jenis === 'farmer' || $jenis === 'farmer_beli') {
            $config_key = 'farm_harga_bibit';
        }

        if (!empty($config_key)) {
            $stmt = $pdo->prepare("SELECT config_value FROM farm_price_config WHERE config_key = ?");
            $stmt->execute([$config_key]);
            $config = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($config) {
                $harga_snapshot = floatval($config['config_value']);
            }
        }

        $stmt = $pdo->prepare("UPDATE delivery_order SET status = 'selesai', tanggal_selesai = NOW(), harga_snapshot = ? WHERE id = ? AND status = 'diambil'");
        $stmt->execute([$harga_snapshot, $id]);

        echo json_encode(['success' => true, 'message' => 'Delivery berhasil diselesaikan', 'harga_snapshot' => $harga_snapshot]);
        break;

    case 'batal':
        $id = $_POST['id'] ?? 0;

        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE delivery_order SET status = 'batal' WHERE id = ? AND status = 'pending'");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Order dibatalkan']);
        break;

    case 'delete':
        $id = $_POST['id'] ?? 0;

        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM delivery_order WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Order dihapus']);
        break;

    case 'get_employees_cargo':
        $stmt = $pdo->query("SELECT id, nama, telepon, divisi FROM employees WHERE divisi = 'Cargo Driver' ORDER BY nama");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $data]);
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

        // Update delivery_order where harga_snapshot is NULL
        $stmt = $pdo->query("SELECT id, jenis_delivery FROM delivery_order WHERE harga_snapshot IS NULL");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $harga = null;
            $jenis = $row['jenis_delivery'];

            if ($jenis === 'farmer_jual') {
                $harga = $configMap['farm_harga_jual_buah'] ?? null;
            } elseif ($jenis === 'compo') {
                $harga = $configMap['mechanic_harga_component'] ?? null;
            } elseif ($jenis === 'farmer' || $jenis === 'farmer_beli') {
                $harga = $configMap['farm_harga_bibit'] ?? null;
            }

            if ($harga !== null) {
                $update = $pdo->prepare("UPDATE delivery_order SET harga_snapshot = ? WHERE id = ?");
                $update->execute([$harga, $row['id']]);
                $updated++;
            }
        }

        echo json_encode(['success' => true, 'message' => "Migrated $updated delivery_order records", 'updated' => $updated]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
}