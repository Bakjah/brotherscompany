<?php include '../includes/config.php'; requireAdmin(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Marketplace | Admin</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    :root {
      --primary: #1e40af;
      --accent: #3b82f6;
      --bg: #f8fafc;
      --text-dark: #0f172a;
      --text-gray: #64748b;
    }

    body { 
      background-color: var(--bg); 
      font-family: 'Inter', sans-serif; 
      color: var(--text-dark);
      margin: 0;
    }

    /* --- NAVBAR --- */
    .navbar { 
      background: linear-gradient(135deg, var(--primary), var(--accent)); 
      padding: 1rem 0; 
      color: white;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 100;
    }
    .navbar-container { 
      max-width: 1200px; margin: 0 auto; padding: 0 20px; 
      display: flex; justify-content: space-between; align-items: center; 
    }
    .navbar-logo { font-weight: 800; font-size: 1.2rem; text-transform: uppercase; letter-spacing: 1px; }
    .navbar-btn { 
      text-decoration: none; padding: 0.6rem 1.2rem; border-radius: 0.6rem; 
      font-size: 0.9rem; font-weight: 700; transition: 0.3s;
    }

    /* --- LAYOUT --- */
    .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
    h1 { font-weight: 800; color: var(--text-dark); margin-bottom: 0.5rem; }
    .subtitle { color: var(--text-gray); margin-bottom: 2rem; }

    /* --- GRID & CARDS --- */
    .grid-admin { 
      display: grid; 
      grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); 
      gap: 1.5rem; 
    }

    .card-post {
      background: white; border-radius: 1.25rem; padding: 1.5rem;
      border: 1px solid #e2e8f0; position: relative;
      transition: all 0.3s ease;
      display: flex; flex-direction: column;
    }
    .card-post:hover { transform: translateY(-5px); box-shadow: 0 12px 20px -5px rgba(0,0,0,0.05); border-color: var(--accent); }

    .card-post h3 { 
      margin: 0 0 12px 0; font-size: 1.2rem; font-weight: 800; color: var(--primary); 
      word-wrap: break-word; overflow-wrap: break-word;
    }
    
    .info-row { display: flex; font-size: 0.85rem; margin-bottom: 6px; align-items: center; gap: 8px; color: var(--text-gray); }
    .info-row strong { color: var(--text-dark); min-width: 80px; }

    .desc-box { 
      background: #f1f5f9; padding: 1rem; border-radius: 0.75rem; 
      margin: 1rem 0; font-size: 0.9rem; line-height: 1.5; color: #475569;
      flex-grow: 1; border-left: 4px solid #cbd5e1;
      word-wrap: break-word; overflow-wrap: break-word;
    }

    .date-tag { font-size: 0.75rem; color: #94a3b8; font-weight: 600; }

    /* --- BUTTONS --- */
    .btn { 
      display: inline-block; padding: 10px 20px; border-radius: 0.75rem; 
      font-weight: 700; text-decoration: none; cursor: pointer; border: none; font-size: 0.85rem;
      transition: 0.3s; text-align: center;
    }
    .btn-red { background: #fee2e2; color: #ef4444; width: 100%; margin-top: 10px; }
    .btn-red:hover { background: #ef4444; color: white; }
    
    .alert-success { 
      background: #dcfce7; color: #166534; padding: 1rem; border-radius: 0.75rem; 
      margin-bottom: 2rem; font-weight: 600; border-left: 5px solid #22c55e;
    }

    @media (max-width: 600px) {
      .grid-admin { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<nav class="navbar">
  <div class="navbar-container">
    <div class="navbar-logo">🛒 Marketplace Admin</div>
    <a href="index.php" class="navbar-btn" style="background: white; color: var(--primary);">← Kembali</a>
  </div>
</nav>

<section class="container">
  <div style="margin-bottom: 2.5rem;">
    <h1>Moderasi Postingan</h1>
    <p class="subtitle">Pantau dan kelola semua barang yang dijual oleh pengguna.</p>
  </div>

  <?php
  if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM marketplace_posts WHERE id=$id");
    echo '<div class="alert-success">✅ Berhasil! Postingan telah dihapus dari sistem.</div>';
  }
  ?>

  <div class="grid-admin">
    <?php
    $result = mysqli_query($conn, "SELECT * FROM marketplace_posts ORDER BY created_at DESC");
    if (mysqli_num_rows($result) > 0):
      while ($row = mysqli_fetch_assoc($result)):
    ?>
    <div class="card-post">
      <h3><?php echo htmlspecialchars($row['nama_barang']); ?></h3>
      
      <div class="info-row">
        <strong>🏷️ Jenis:</strong> <span><?php echo htmlspecialchars($row['jenis_barang']); ?></span>
      </div>
      <div class="info-row">
        <strong>📞 Kontak:</strong> <span><?php echo htmlspecialchars($row['no_telpon']); ?></span>
      </div>
      <div class="info-row">
        <strong>💬 Discord:</strong> <span><?php echo htmlspecialchars($row['discord_id']); ?></span>
      </div>

      <div class="desc-box">
        <?php 
          $desc = htmlspecialchars($row['keterangan']);
          echo (strlen($desc) > 120) ? substr($desc, 0, 120) . '...' : $desc; 
        ?>
      </div>

      <div style="display: flex; justify-content: space-between; align-items: center; margin-top: auto;">
        <span class="date-tag">📅 <?php echo date('d M Y', strtotime($row['created_at'])); ?></span>
      </div>

      <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus postingan ini secara permanen?')" class="btn btn-red">
        Hapus Postingan
      </a>
    </div>
    <?php endwhile; else: ?>
    <div style="grid-column: 1/-1; text-align: center; padding: 50px; background: white; border-radius: 20px; border: 2px dashed #e2e8f0;">
      <p style="color: var(--text-gray); font-weight: 600;">📭 Saat ini tidak ada postingan marketplace untuk dimoderasi.</p>
    </div>
    <?php endif; ?>
  </div>
</section>

</body>
</html>