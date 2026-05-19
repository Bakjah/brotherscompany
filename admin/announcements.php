<?php include '../includes/config.php'; requireAdmin(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Announcements | Brothers Company</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    :root {
      --primary: #1e40af;
      --accent: #3b82f6;
      --bg: #f1f5f9;
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
    }
    .navbar-container { 
      max-width: 1000px; margin: 0 auto; padding: 0 20px; 
      display: flex; justify-content: space-between; align-items: center; 
    }
    .navbar-logo { font-weight: 800; font-size: 1.2rem; text-transform: uppercase; letter-spacing: 1px; }
    .navbar-btn { 
      text-decoration: none; padding: 0.5rem 1.2rem; border-radius: 0.5rem; 
      font-size: 0.9rem; font-weight: 700; transition: 0.3s;
    }

    /* --- LAYOUT --- */
    .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
    h1 { font-weight: 800; color: var(--text-dark); margin-bottom: 0.5rem; }
    .subtitle { color: var(--text-gray); margin-bottom: 2rem; }

    /* --- CARD & ANNOUNCEMENT LIST --- */
    .card { 
      background: white; border-radius: 1rem; padding: 2rem; 
      border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
      margin-bottom: 1.5rem;
    }

    .ann-card {
      background: white; border-radius: 1rem; padding: 1.5rem;
      border: 1px solid #e2e8f0; margin-bottom: 1rem;
      transition: 0.2s; position: relative;
    }
    .ann-card:hover { border-color: var(--accent); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
    
    .ann-card h3 { margin: 0 0 0.5rem 0; font-size: 1.1rem; color: var(--primary); display: flex; align-items: center; gap: 8px; }
    
    .ann-content { 
      font-size: 0.95rem; color: var(--text-gray); margin-bottom: 1rem;
      line-height: 1.6;
      word-wrap: break-word; /* Mencegah teks panjang merusak layout */
      overflow-wrap: break-word;
    }

    .meta-info { font-size: 0.8rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; display: flex; gap: 15px; }
    .badge { background: #eff6ff; color: var(--accent); padding: 2px 10px; border-radius: 20px; }

    /* --- FORMS --- */
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 8px; color: var(--text-dark); }
    .form-group input, .form-group textarea, .form-group select {
      width: 100%; padding: 12px; border-radius: 0.6rem; border: 1px solid #cbd5e1;
      font-family: inherit; font-size: 1rem; box-sizing: border-box; transition: 0.3s;
    }
    .form-group input:focus, .form-group textarea:focus { outline: none; border-color: var(--accent); ring: 3px rgba(59, 130, 246, 0.1); }
    .form-group textarea { height: 150px; resize: vertical; }

    /* --- BUTTONS --- */
    .btn { 
      display: inline-block; padding: 10px 20px; border-radius: 0.6rem; 
      font-weight: 700; text-decoration: none; cursor: pointer; border: none; font-size: 0.9rem;
      transition: 0.3s; text-align: center;
    }
    .btn-primary { background: var(--primary); color: white; }
    .btn-primary:hover { background: var(--accent); transform: translateY(-2px); }
    .btn-red { background: #fee2e2; color: #ef4444; }
    .btn-red:hover { background: #ef4444; color: white; }
    
    .alert-success { 
      background: #dcfce7; color: #166534; padding: 1rem; border-radius: 0.75rem; 
      margin-bottom: 1.5rem; font-weight: 600; border-left: 5px solid #22c55e;
    }
  </style>
</head>
<body>

<nav class="navbar">
  <div class="navbar-container">
    <div class="navbar-logo">📢 Admin Panel</div>
    <a href="index.php" class="navbar-btn" style="background: white; color: var(--primary);">← Dashboard</a>
  </div>
</nav>

<section class="container">
  <?php if (!isset($_GET['action']) || $_GET['action'] !== 'create'): ?>
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem;">
      <div>
        <h1>Pengumuman</h1>
        <p class="subtitle">Kelola informasi untuk semua pengguna.</p>
      </div>
      <a href="?action=create" class="btn btn-primary">+ Buat Baru</a>
    </div>

    <?php
    if (isset($_GET['delete'])) {
      $id = (int)$_GET['delete'];
      mysqli_query($conn, "DELETE FROM announcements WHERE id=$id");
      echo '<div class="alert-success">✅ Berhasil! Pengumuman telah dihapus selamanya.</div>';
    }
    ?>

    <div class="announcement-list">
      <?php
      $result = mysqli_query($conn, "SELECT * FROM announcements ORDER BY pinned DESC, created_at DESC");
      if(mysqli_num_rows($result) > 0):
        while ($row = mysqli_fetch_assoc($result)):
      ?>
      <div class="ann-card">
        <h3>
          <?php echo htmlspecialchars($row['title']); ?> 
          <?php if($row['pinned']) echo '<span title="Diprioritaskan">📌</span>'; ?>
        </h3>
        <div class="ann-content"><?php echo nl2br(htmlspecialchars($row['content'])); ?></div>
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <div class="meta-info">
            <span class="badge"><?php echo htmlspecialchars($row['category']); ?></span>
            <span>📅 <?php echo date('d M Y', strtotime($row['created_at'])); ?></span>
          </div>
          <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Hapus pengumuman ini?')" class="btn btn-red" style="padding: 6px 12px; font-size: 0.8rem;">Hapus</a>
        </div>
      </div>
      <?php endwhile; else: ?>
        <div class="card" style="text-align: center; color: var(--text-gray);">Belum ada pengumuman.</div>
      <?php endif; ?>
    </div>

  <?php else: ?>
    <h1>Tulis Pengumuman</h1>
    <p class="subtitle">Gunakan bahasa yang jelas dan informatif.</p>
    
    <div class="card">
      <form method="POST">
        <div class="form-group">
          <label>Judul Pengumuman *</label>
          <input type="text" name="title" placeholder="Masukkan judul..." required>
        </div>
        <div class="form-group">
          <label>Isi Pesan *</label>
          <textarea name="content" placeholder="Tuliskan detail informasi di sini..." required></textarea>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
          <div class="form-group">
            <label>Kategori</label>
            <select name="category">
              <option>Company</option>
              <option>Marketplace</option>
              <option>Workshop</option>
              <option>Farm</option>
              <option>Asian Food</option>
            </select>
          </div>
          <div class="form-group">
            <label>Opsi Tambahan</label>
            <label style="font-weight: 500; text-transform: none; margin-top: 10px; cursor: pointer;">
                <input type="checkbox" name="pinned" style="width: auto; margin-right: 10px;"> Sematkan di paling atas (Pin)
            </label>
          </div>
        </div>
        
        <div style="margin-top: 1rem; display: flex; flex-direction: column; gap: 10px;">
          <button class="btn btn-primary" style="font-size: 1rem; padding: 15px;">🚀 Publikasikan Sekarang</button>
          <a href="announcements.php" class="btn" style="background: transparent; color: var(--text-gray);">Batalkan</a>
        </div>
      </form>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $title = sanitize($_POST['title']);
      $content = sanitize($_POST['content']);
      $category = sanitize($_POST['category']);
      $pinned = isset($_POST['pinned']) ? 1 : 0;
      
      mysqli_query($conn, "INSERT INTO announcements (title, content, category, pinned, created_by) VALUES ('$title', '$content', '$category', $pinned, {$_SESSION['user_id']})");
      echo "<script>window.location.href='announcements.php';</script>";
    }
    ?>
  <?php endif; ?>
</section>

</body>
</html>