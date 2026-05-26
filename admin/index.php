<?php include '../includes/config.php'; requireAdmin(); 

$featured_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM marketplace_posts WHERE is_featured = 1 AND featured_expiry > NOW()"))['total'];

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav class="navbar" style="background: linear-gradient(135deg, #1e40af, #3b82f6);">
  <div class="navbar-container">
    <div class="navbar-logo">🔑 Admin Panel</div>
    <div>
      <a href="featured-marketplace.php" class="navbar-btn" style="background: white; color: #1e40af; margin-right: 0.5rem;">⭐ Featured</a>
      <a href="announcements.php" class="navbar-btn" style="background: white; color: #1e40af; margin-right: 0.5rem;">📢 Announcements</a>
      <a href="marketplace.php" class="navbar-btn" style="background: white; color: #1e40af; margin-right: 0.5rem;">🛒 Marketplace</a>
      <a href="../" class="navbar-btn">Home</a>
    </div>
  </div>
</nav>

<section class="section">
  <div class="container">
    <h1>🔑 Admin Dashboard</h1>
    
    <div class="grid-3">
      <div class="card" style="text-align: center;">
        <h4>⭐ Featured Marketplace</h4>
        <p style="font-size: 2rem; color: #fbbf24; font-weight: bold;">
          <?php echo $featured_count; ?>
        </p>
        <a href="featured-marketplace.php" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Manage</a>
      </div>
      <div class="card" style="text-align: center;">
        <h4>📢 Announcements</h4>
        <p style="font-size: 2rem; color: #1e40af; font-weight: bold;">
          <?php echo mysqli_num_rows(mysqli_query($conn, "SELECT * FROM announcements")); ?>
        </p>
        <a href="announcements.php" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Manage</a>
      </div>
      <div class="card" style="text-align: center;">
        <h4>🛒 Marketplace Posts</h4>
        <p style="font-size: 2rem; color: #1e40af; font-weight: bold;">
          <?php echo mysqli_num_rows(mysqli_query($conn, "SELECT * FROM marketplace_posts")); ?>
        </p>
        <a href="marketplace.php" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Manage</a>
      </div>
      <div class="card" style="text-align: center;">
        <h4>👥 Users</h4>
        <p style="font-size: 2rem; color: #1e40af; font-weight: bold;">
          <?php echo mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE username NOT LIKE 'deleted_%'")); ?>
        </p>
        <a href="users.php" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Manage</a>
      </div>
      <div class="card" style="text-align: center;">
        <h4>🗂️ File Uploads</h4>
        <p style="font-size: 2rem; color: #1e40af; font-weight: bold;">
          <?php
          $uploads_dir = '../assets/uploads';
          $file_count = is_dir($uploads_dir) ? count(array_filter(scandir($uploads_dir), fn($f) => $f !== '.' && $f !== '..' && is_file($uploads_dir . '/' . $f))) : 0;
          echo $file_count;
          ?>
        </p>
        <a href="uploads.php" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Manage</a>
      </div>
    </div>

    <div class="card" style="margin-top: 2rem;">
      <h3>📋 Admin Functions</h3>
      <ul style="margin: 1rem 0; padding-left: 2rem;">
        <li><a href="users.php">👥 Manage Users (Roles & Delete)</a></li>
        <li><a href="uploads.php">🗂️ Manage File Uploads (Delete Images)</a></li>
        <li><a href="featured-marketplace.php">⭐ Manage Featured Marketplace (VIP)</a></li>
        <li><a href="announcements.php">📢 Manage Announcements (Create/Delete)</a></li>
        <li><a href="marketplace.php">🛒 Delete Marketplace Posts</a></li>
      </ul>
    </div>

    <div class="card" style="margin-top: 1rem; background: #fef3c7; border-left: 4px solid #fbbf24;">
      <h3 style="color: #92400e;">💰 VIP Featured Pricing Guide</h3>
      <p style="color: #92400e; margin: 0.5rem 0;">
		<strong>1 Hari:</strong> $2.000 (Standard)<br>
        <strong>7 Hari:</strong> $10.000 (Standard)<br>
        <strong>14 Hari:</strong> $20.000 (Popular)<br>
        <em>*Harga dapat disesuaikan sesuai negosiasi dengan member VIP</em>
      </p>
    </div>
  </div>
</section>

</body>
</html>
