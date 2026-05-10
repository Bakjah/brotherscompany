<?php include '../includes/config.php'; requireAdmin(); ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Marketplace</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav class="navbar" style="background: linear-gradient(135deg, #1e40af, #3b82f6);">
  <div class="navbar-container">
    <div class="navbar-logo">🛒 Marketplace Posts</div>
    <a href="index.php" class="navbar-btn" style="background: white; color: #1e40af;">Back</a>
  </div>
</nav>

<section class="section">
  <div class="container">
    <h1>🛒 Manage Marketplace Posts</h1>

    <?php
    if (isset($_GET['delete'])) {
      $id = (int)$_GET['delete'];
      mysqli_query($conn, "DELETE FROM marketplace_posts WHERE id=$id");
      echo '<p style="color: green; background: #dcfce7; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">✅ Post deleted!</p>';
    }
    ?>

    <div class="grid-2">
      <?php
      $result = mysqli_query($conn, "SELECT * FROM marketplace_posts ORDER BY created_at DESC");
      if (mysqli_num_rows($result) > 0):
        while ($row = mysqli_fetch_assoc($result)):
      ?>
      <div class="card">
        <h3><?php echo $row['nama_barang']; ?></h3>
        <p><strong>Jenis:</strong> <?php echo $row['jenis_barang']; ?></p>
        <p><strong>No Telpon:</strong> <?php echo $row['no_telpon']; ?></p>
        <p><strong>Discord:</strong> <?php echo $row['discord_id']; ?></p>
        <p style="background: #f0f9ff; padding: 0.5rem; border-radius: 0.5rem; margin: 1rem 0;">
          <?php echo substr($row['keterangan'], 0, 100) . '...'; ?>
        </p>
        <small>📅 <?php echo date('d M Y', strtotime($row['created_at'])); ?></small>
        <div style="margin-top: 1rem;">
          <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Hapus post ini?')" class="btn btn-red">Delete</a>
        </div>
      </div>
      <?php endwhile; else: ?>
      <p style="grid-column: 1/-1; text-align: center;">Tidak ada posts</p>
      <?php endif; ?>
    </div>
  </div>
</section>

</body>
</html>
