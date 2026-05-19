<?php 
include '../includes/config.php'; 
requireLogin(); 

// Cek jumlah posts user
$user_id = $_SESSION['user_id'];
$countResult = mysqli_query($conn, "SELECT COUNT(*) as total FROM marketplace_posts WHERE user_id = $user_id");
$countRow = mysqli_fetch_assoc($countResult);
$totalPosts = $countRow['total'];
$canPost = $totalPosts < 5;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Post Barang | Brothers Company</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    :root {
      --m-purple: #8b5cf6;
      --m-dark: #1e293b;
      --m-slate: #64748b;
    }

    body { 
      background-color: #f8fafc; 
      margin: 0; 
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
    }

    /* --- NAVBAR --- */
    .navbar { background: white; border-bottom: 1px solid #e2e8f0; padding: 1rem 0; position: sticky; top: 0; z-index: 100; }
    .navbar-container { max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; }

    /* --- UI ELEMENTS --- */
    .post-container { max-width: 700px; margin: 40px auto; padding: 0 20px; }
    
    .card {
      background: white; border-radius: 24px; padding: 40px;
      box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
      border: 1px solid #e2e8f0;
    }

    .slot-info { 
      padding: 1.5rem; border-radius: 16px; margin-bottom: 1.5rem; 
      display: flex; align-items: center; justify-content: space-between;
      border: 1px solid transparent;
    }
    .slot-info.available { background: #f5f3ff; color: var(--m-purple); border-color: #ddd6fe; }
    .slot-info.full { background: #fef2f2; color: #991b1b; border-color: #fee2e2; }
    
    .slot-bar { display: flex; gap: 8px; }
    .slot { width: 14px; height: 14px; border-radius: 4px; background: #e2e8f0; }
    .slot.used { background: var(--m-purple); }

    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-size: 12px; font-weight: 700; color: var(--m-dark); margin-bottom: 8px; text-transform: uppercase; }
    
    .form-group input, .form-group textarea {
      width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid #e2e8f0;
      font-size: 15px; transition: 0.3s; box-sizing: border-box;
    }
    .form-group input:focus, .form-group textarea:focus {
      outline: none; border-color: var(--m-purple); box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
    }
    
    .btn-submit {
      background: var(--m-purple); color: white; border: none; padding: 15px;
      width: 100%; border-radius: 12px; font-weight: 700; cursor: pointer;
      transition: 0.3s; font-size: 16px;
    }
    .btn-submit:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(139, 92, 246, 0.4); }
    .btn-submit:disabled { background: #cbd5e1; cursor: not-allowed; }

    .alert { padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-size: 14px; }
    .alert-warning { background: #fffbeb; color: #92400e; border: 1px solid #fef3c7; }
    .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #dcfce7; margin-top: 20px; }
  </style>
</head>
<body>

<nav class="navbar">
  <div class="navbar-container">
    <div style="font-weight:900; color:var(--m-purple); font-size:1.4rem; letter-spacing:-1px;">📝 POSTING</div>
    <div>
      <a href="my-posts.php" style="text-decoration:none; color:var(--m-slate); font-weight:600; margin-right:20px; font-size:14px;">📋 Post Saya</a>
      <a href="index.php" style="text-decoration:none; background: #f1f5f9; color:var(--m-slate); padding:8px 16px; border-radius:8px; font-weight:700; font-size:13px;">Kembali</a>
    </div>
  </div>
</nav>

<section class="post-container">
  <h1 style="color: var(--m-dark); font-weight: 800; font-size: 2rem; margin-bottom: 10px;">Post Barang Baru</h1>
  <p style="color: var(--m-slate); margin-bottom: 30px;">Halaman posting tanpa ribet.</p>

  <div class="slot-info <?php echo $canPost ? 'available' : 'full'; ?>">
    <div>
      <?php if($canPost): ?>
        <strong>✅ Slot Tersedia:</strong> <?php echo 5 - $totalPosts; ?>/5
      <?php else: ?>
        <strong>❌ Slot Penuh!</strong>
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
      ⚠️ <b>Batas Maksimal:</b> Kamu sudah punya 5 post. Hapus yang lama untuk menambah baru.
    </div>
  <?php endif; ?>

  <div class="card <?php echo !$canPost ? 'opacity: 0.6; pointer-events: none;' : ''; ?>">
    <form method="POST" enctype="multipart/form-data">
      <?php echo getCSRFField(); ?>

      <div class="form-group">
        <label>Nama Barang</label>
        <input type="text" name="nama_barang" placeholder="Contoh: Laptop Gaming" required>
      </div>

      <div class="form-group">
        <label>Kategori</label>
        <input type="text" name="jenis_barang" placeholder="Elektronik, Fashion, dll" required>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <div class="form-group">
          <label>No Telf.</label>
          <input type="tel" name="no_telpon" placeholder="12..." required>
        </div>
        <div class="form-group">
          <label>Discord ID</label>
          <input type="text" name="discord_id" placeholder="user#0000" required>
        </div>
      </div>

      <div class="form-group">
        <label>Foto Produk</label>
        <input type="file" name="foto_barang" accept="image/*">
      </div>

      <div class="form-group">
        <label>Keterangan</label>
        <textarea name="keterangan" rows="4" placeholder="Kondisi barang, harga, dll..." required></textarea>
      </div>

      <button type="submit" class="btn-submit" <?php echo !$canPost ? 'disabled' : ''; ?>>
        🚀 Tayangkan Sekarang
      </button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canPost) {
      if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        die('Invalid Token');
      }

      $nama = sanitize($_POST['nama_barang']);
      $jenis = sanitize($_POST['jenis_barang']);
      $telpon = sanitize($_POST['no_telpon']);
      $discord = sanitize($_POST['discord_id']);
      $keterangan = sanitize($_POST['keterangan']);
      $foto = '';

      if (isset($_FILES['foto_barang']) && $_FILES['foto_barang']['error'] === UPLOAD_ERR_OK) {
        $filename = time() . '_' . basename($_FILES['foto_barang']['name']);
        if (move_uploaded_file($_FILES['foto_barang']['tmp_name'], '../assets/uploads/' . $filename)) {
          $foto = $filename;
        }
      }

      mysqli_query($conn, "INSERT INTO marketplace_posts (user_id, nama_barang, jenis_barang, no_telpon, discord_id, foto_barang, keterangan) 
        VALUES ({$_SESSION['user_id']}, '$nama', '$jenis', '$telpon', '$discord', '$foto', '$keterangan')");
      
      echo '<div class="alert alert-success">✅ Berhasil! <a href="index.php">Cek Marketplace</a></div>';
    }
    ?>
  </div>
</section>

</body>
</html>