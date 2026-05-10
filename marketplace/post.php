<?php include '../includes/config.php'; requireLogin(); 

// Cek jumlah posts user
$user_id = $_SESSION['user_id'];
$countResult = mysqli_query($conn, "SELECT COUNT(*) as total FROM marketplace_posts WHERE user_id = $user_id");
$countRow = mysqli_fetch_assoc($countResult);
$totalPosts = $countRow['total'];
$canPost = $totalPosts < 5;

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Post Barang</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .slot-info { padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-weight: 500; display: flex; align-items: center; justify-content: space-between; }
    .slot-info.available { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
    .slot-info.full { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
    .slot-bar { display: flex; gap: 0.5rem; margin-left: 1rem; }
    .slot { width: 20px; height: 20px; border-radius: 0.25rem; background: #e5e7eb; border: 1px solid #9ca3af; }
    .slot.used { background: #8b5cf6; border-color: #7c3aed; }
    .disabled-form { opacity: 0.6; pointer-events: none; }
    .alert { padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; }
    .alert-warning { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
    .info-box { background: #f0f9ff; color: #0c2d4a; padding: 1rem; border-radius: 0.5rem; border-left: 4px solid #0284c7; margin-bottom: 1.5rem; }
  </style>
</head>
<body>

<nav class="navbar" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
  <div class="navbar-container">
    <div class="navbar-logo">🛒 Post Barang</div>
    <div>
      <a href="my-posts.php" class="navbar-btn" style="background: white; color: #7c3aed; margin-right: 0.5rem;">📋 Post Saya</a>
      <a href="index.php" class="navbar-btn" style="background: white; color: #7c3aed;">Back</a>
    </div>
  </div>
</nav>

<section class="section">
  <div class="container" style="max-width: 600px;">
    <h1>📝 Post Barang Baru</h1>

    <div class="slot-info <?php echo $canPost ? 'available' : 'full'; ?>">
      <div>
        <?php if($canPost): ?>
          ✅ Slot Tersedia: <strong><?php echo 5 - $totalPosts; ?>/5</strong>
        <?php else: ?>
          ❌ Slot Penuh! Anda sudah menggunakan 5/5 slot
        <?php endif; ?>
      </div>
      <div class="slot-bar">
        <?php for($i = 0; $i < 5; $i++): ?>
          <div class="slot <?php echo $i < $totalPosts ? 'used' : ''; ?>"></div>
        <?php endfor; ?>
      </div>
    </div>

    <?php if(!$canPost): ?>
      <div class="alert alert-warning">
        <strong>⚠️ Slot Penuh!</strong><br>
        Anda sudah mencapai batas maksimal 5 post. Hapus salah satu post di <a href="my-posts.php" style="text-decoration: underline;">Post Saya</a> untuk membuat post baru.
      </div>
    <?php endif; ?>

    <div class="info-box">
      📌 <strong>Tips:</strong> Setiap user hanya bisa membuat maksimal 5 post. Kelola post Anda di halaman <a href="my-posts.php" style="text-decoration: underline; color: #0284c7;">Post Saya</a>.
    </div>

    <div class="card <?php echo !$canPost ? 'disabled-form' : ''; ?>">
      <form method="POST" enctype="multipart/form-data" <?php echo !$canPost ? 'disabled' : ''; ?>>
        <!-- 🔐 CSRF TOKEN -->
        <?php echo getCSRFField(); ?>

        <div class="form-group">
          <label>Nama Barang *</label>
          <input type="text" name="nama_barang" placeholder="contoh: iPhone 13" required <?php echo !$canPost ? 'disabled' : ''; ?>>
        </div>

        <div class="form-group">
          <label>Jenis Barang *</label>
          <input type="text" name="jenis_barang" placeholder="contoh: Elektronik, Kendaraan, Furnitur" required <?php echo !$canPost ? 'disabled' : ''; ?>>
        </div>

        <div class="form-group">
          <label>No Telepon *</label>
          <input type="tel" name="no_telpon" placeholder="08123456789" required <?php echo !$canPost ? 'disabled' : ''; ?>>
        </div>

        <div class="form-group">
          <label>ID Discord *</label>
          <input type="text" name="discord_id" placeholder="username#1234" required <?php echo !$canPost ? 'disabled' : ''; ?>>
        </div>

        <div class="form-group">
          <label>Foto Barang</label>
          <input type="file" name="foto_barang" accept="image/*" <?php echo !$canPost ? 'disabled' : ''; ?>>
          <small style="color: #666; display: block; margin-top: 0.5rem;">
            📸 Format: JPG, PNG (Max: 5MB)
          </small>
        </div>

        <div class="form-group">
          <label>Keterangan *</label>
          <textarea name="keterangan" placeholder="Jelaskan kondisi, spesifikasi, atau detail barang..." required <?php echo !$canPost ? 'disabled' : ''; ?>></textarea>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%;" <?php echo !$canPost ? 'disabled' : ''; ?>>✅ Post Barang</button>
      </form>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canPost) {
      // 🔐 VALIDATE CSRF TOKEN
      if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        die('❌ CSRF token validation failed. Refresh halaman dan coba lagi.');
      }

      $nama = sanitize($_POST['nama_barang']);
      $jenis = sanitize($_POST['jenis_barang']);
      $telpon = sanitize($_POST['no_telpon']);
      $discord = sanitize($_POST['discord_id']);
      $keterangan = sanitize($_POST['keterangan']);
      $foto = '';

      // Handle file upload
      if (isset($_FILES['foto_barang']) && $_FILES['foto_barang']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['foto_barang'];
        
        // 🔐 VALIDATE FILE
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
          die('❌ File type tidak diizinkan');
        }
        
        $filename = time() . '_' . basename($file['name']);
        $filepath = '../assets/uploads/' . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
          $foto = $filename;
        }
      }

      mysqli_query($conn, "INSERT INTO marketplace_posts (user_id, nama_barang, jenis_barang, no_telpon, discord_id, foto_barang, keterangan) 
        VALUES ({$_SESSION['user_id']}, '$nama', '$jenis', '$telpon', '$discord', '$foto', '$keterangan')");
      
      echo '<div style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 0.5rem; margin-top: 1rem;">';
      echo '✅ Barang berhasil dipost! <a href="index.php">Lihat marketplace →</a> atau <a href="my-posts.php">Post Saya →</a>';
      echo '</div>';
    }
    ?>
  </div>
</section>

</body>
</html>
