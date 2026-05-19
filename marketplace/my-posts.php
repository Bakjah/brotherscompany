<?php 
include '../includes/config.php'; 
requireLogin(); 

$user_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT * FROM marketplace_posts WHERE user_id = $user_id ORDER BY created_at DESC");
$totalPosts = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Post Saya | Brothers Company</title>
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

    /* --- CONTAINER --- */
    .my-posts-container { max-width: 900px; margin: 40px auto; padding: 0 20px; }

    /* SLOT USAGE CARD */
    .slot-usage-card {
      background: white; border-radius: 20px; padding: 25px;
      display: flex; align-items: center; justify-content: space-between;
      border: 1px solid #e2e8f0; margin-bottom: 30px;
      box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }
    .slot-bar { display: flex; gap: 8px; }
    .slot { width: 14px; height: 14px; border-radius: 4px; background: #e2e8f0; border: 1px solid #cbd5e1; }
    .slot.used { background: var(--m-purple); border-color: #7c3aed; }

    /* POST LIST CARD */
    .post-card {
      background: white; border-radius: 20px; padding: 15px;
      margin-bottom: 20px; display: grid; grid-template-columns: 140px 1fr auto;
      gap: 20px; align-items: center; border: 1px solid #e2e8f0;
      transition: 0.3s ease;
    }
    .post-card:hover { transform: translateX(5px); border-color: var(--m-purple); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }

    .post-image {
      width: 140px; height: 120px; object-fit: cover;
      border-radius: 12px; background: #f1f5f9;
    }
    .post-placeholder {
      width: 140px; height: 120px; background: #f1f5f9; border-radius: 12px;
      display: flex; align-items: center; justify-content: center; font-size: 32px;
    }

    .post-info h3 { margin: 0 0 5px 0; color: var(--m-dark); font-size: 18px; font-weight: 800; }
    .post-info p { margin: 2px 0; font-size: 13px; color: var(--m-slate); }
    .post-info .tag { 
        display: inline-block; padding: 3px 10px; background: #f5f3ff; 
        color: var(--m-purple); border-radius: 6px; font-size: 11px; font-weight: 700; margin-top: 5px;
    }

    .btn-delete {
      background: #fee2e2; color: #ef4444; border: none; padding: 10px 15px;
      border-radius: 10px; cursor: pointer; font-size: 13px; font-weight: 700;
      transition: 0.2s; text-decoration: none; display: flex; align-items: center; gap: 5px;
    }
    .btn-delete:hover { background: #ef4444; color: white; }

    .empty-state {
      text-align: center; padding: 60px 20px; background: white; border-radius: 24px;
      border: 2px dashed #e2e8f0;
    }

    @media (max-width: 650px) {
      .post-card { grid-template-columns: 1fr; text-align: center; justify-items: center; }
      .post-info { margin-bottom: 10px; }
    }
  </style>
</head>
<body>

<nav class="navbar">
  <div class="navbar-container">
    <div style="font-weight:900; color:var(--m-purple); font-size:1.4rem; letter-spacing:-1px;">📋 KELOLA POST</div>
    <div>
      <a href="post.php" style="text-decoration:none; background:var(--m-purple); color:white; padding:8px 16px; border-radius:10px; font-weight:700; font-size:13px; margin-right:10px;">+ Post Baru</a>
      <a href="index.php" style="text-decoration:none; color:var(--m-slate); font-weight:600; font-size:13px;">Kembali</a>
    </div>
  </div>
</nav>

<section class="my-posts-container">
  <h1 style="color: var(--m-dark); font-weight: 800; font-size: 2rem; margin-bottom: 10px;">Postingan Anda</h1>
  <p style="color: var(--m-slate); margin-bottom: 30px;">Kelola atau hapus barang yang sudah Anda iklankan.</p>

  <div class="slot-usage-card">
    <div style="font-size: 14px; color: var(--m-dark);">
      <strong>Penyimpanan Slot:</strong> <?php echo $totalPosts; ?> dari 5
    </div>
    <div class="slot-bar">
      <?php for($i = 0; $i < 5; $i++): ?>
        <div class="slot <?php echo $i < $totalPosts ? 'used' : ''; ?>"></div>
      <?php endfor; ?>
    </div>
  </div>

  <?php if($totalPosts > 0): ?>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
      <div class="post-card">
        <?php if($row['foto_barang']): ?>
          <img src="../assets/uploads/<?php echo $row['foto_barang']; ?>" class="post-image">
        <?php else: ?>
          <div class="post-placeholder">📸</div>
        <?php endif; ?>

        <div class="post-info">
          <span class="tag"><?php echo htmlspecialchars($row['jenis_barang']); ?></span>
          <h3><?php echo htmlspecialchars($row['nama_barang']); ?></h3>
          <p>📱 <?php echo htmlspecialchars($row['no_telpon']); ?></p>
          <p>💬 <?php echo htmlspecialchars($row['discord_id']); ?></p>
          <p style="font-size: 11px; margin-top: 8px; opacity: 0.7;">📅 <?php echo date('d M Y', strtotime($row['created_at'])); ?></p>
        </div>

        <div class="post-actions">
          <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Yakin hapus post ini?');">
            🗑️ Hapus
          </a>
        </div>
      </div>
    <?php endwhile; ?>

  <?php else: ?>
    <div class="empty-state">
      <div style="font-size: 50px; margin-bottom: 15px;">📭</div>
      <h3 style="color: var(--m-dark); font-weight: 800;">Belum Ada Barang</h3>
      <p style="color: var(--m-slate); margin-bottom: 20px;">Anda belum memposting apapun di Marketplace.</p>
      <a href="post.php" style="text-decoration:none; background:var(--m-purple); color:white; padding:12px 25px; border-radius:12px; font-weight:700; display: inline-block;">+ Mulai Jualan</a>
    </div>
  <?php endif; ?>
</section>

</body>
</html>