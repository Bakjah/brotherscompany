<?php include '../includes/config.php'; requireLogin(); 

$user_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT * FROM marketplace_posts WHERE user_id = $user_id ORDER BY created_at DESC");
$totalPosts = mysqli_num_rows($result);

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Post Saya</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .post-card {
      background: white;
      border: 0.5px solid #e5e7eb;
      border-radius: 0.5rem;
      padding: 1rem;
      margin-bottom: 1rem;
      display: grid;
      grid-template-columns: 120px 1fr auto;
      gap: 1rem;
      align-items: start;
    }
    .post-image {
      width: 120px;
      height: 100px;
      object-fit: cover;
      border-radius: 0.5rem;
      background: #e5e7eb;
    }
    .post-info h3 {
      margin: 0 0 0.5rem 0;
      font-size: 16px;
    }
    .post-info p {
      margin: 0.25rem 0;
      font-size: 13px;
      color: #666;
    }
    .post-info small {
      color: #999;
    }
    .post-actions {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }
    .btn-delete {
      background: #ef4444;
      color: white;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 0.375rem;
      cursor: pointer;
      font-size: 13px;
      transition: background 0.2s;
    }
    .btn-delete:hover {
      background: #dc2626;
    }
    .slot-usage {
      padding: 1rem;
      background: #f0f9ff;
      border: 1px solid #0284c7;
      border-radius: 0.5rem;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .slot-bar {
      display: flex;
      gap: 0.5rem;
      margin-left: 1rem;
    }
    .slot {
      width: 20px;
      height: 20px;
      border-radius: 0.25rem;
      background: #e5e7eb;
      border: 1px solid #9ca3af;
    }
    .slot.used {
      background: #8b5cf6;
      border-color: #7c3aed;
    }
    .empty-state {
      text-align: center;
      padding: 2rem;
      color: #666;
    }
    .empty-state h3 {
      margin-bottom: 1rem;
    }
    @media (max-width: 768px) {
      .post-card {
        grid-template-columns: 1fr;
      }
      .post-image {
        width: 100%;
        height: 200px;
      }
    }
  </style>
</head>
<body>

<nav class="navbar" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
  <div class="navbar-container">
    <div class="navbar-logo">📋 Post Saya</div>
    <div>
      <a href="post.php" class="navbar-btn" style="background: white; color: #7c3aed; margin-right: 0.5rem;">+ Post Barang</a>
      <a href="index.php" class="navbar-btn" style="background: white; color: #7c3aed;">← Kembali</a>
    </div>
  </div>
</nav>

<section class="section">
  <div class="container">
    <h1>📋 Post Saya</h1>

    <!-- Slot Usage Info -->
    <div class="slot-usage">
      <div>
        <strong>Slot Usage: <?php echo $totalPosts; ?>/5</strong>
      </div>
      <div class="slot-bar">
        <?php for($i = 0; $i < 5; $i++): ?>
          <div class="slot <?php echo $i < $totalPosts ? 'used' : ''; ?>"></div>
        <?php endfor; ?>
      </div>
    </div>

    <?php if($totalPosts > 0): ?>
      <div style="margin-bottom: 1.5rem;">
        <p style="color: #666;">Total post: <strong><?php echo $totalPosts; ?></strong> dari 5 slot</p>
      </div>

      <?php while($row = mysqli_fetch_assoc($result)): ?>
        <div class="post-card">
          <?php if($row['foto_barang']): ?>
            <img src="../assets/uploads/<?php echo $row['foto_barang']; ?>" alt="<?php echo $row['nama_barang']; ?>" class="post-image">
          <?php else: ?>
            <div class="post-image" style="display: flex; align-items: center; justify-content: center; font-size: 36px; color: #ccc;">
              📸
            </div>
          <?php endif; ?>

          <div class="post-info">
            <h3><?php echo $row['nama_barang']; ?></h3>
            <p><strong>Jenis:</strong> <?php echo $row['jenis_barang']; ?></p>
            <p><strong>No Telpon:</strong> <a href="tel:<?php echo $row['no_telpon']; ?>"><?php echo $row['no_telpon']; ?></a></p>
            <p><strong>Discord:</strong> <?php echo $row['discord_id']; ?></p>
            <small>📅 <?php echo date('d M Y H:i', strtotime($row['created_at'])); ?></small>
          </div>

          <div class="post-actions">
            <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Yakin hapus post ini?');">🗑️ Hapus</a>
          </div>
        </div>
      <?php endwhile; ?>

    <?php else: ?>
      <div class="empty-state">
        <h3>📭 Belum ada post</h3>
        <p>Anda belum membuat post apapun.</p>
        <a href="post.php" class="btn btn-primary" style="display: inline-block; margin-top: 1rem;">+ Buat Post Baru</a>
      </div>
    <?php endif; ?>
  </div>
</section>

</body>
</html>
