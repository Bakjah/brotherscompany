<?php include '../includes/config.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Announcements</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .featured-promo {
      background: linear-gradient(135deg, #fbbf24, #f59e0b);
      border-radius: 0.75rem;
      padding: 2rem;
      margin-bottom: 2rem;
      color: white;
    }

    .featured-promo h2 {
      color: white;
      margin: 0 0 1.5rem 0;
    }

    .featured-items {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
    }

    .featured-item {
      background: white;
      border-radius: 0.5rem;
      overflow: hidden;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
    }

    .featured-item-img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      background: #e5e7eb;
    }

    .featured-item-content {
      padding: 1rem;
      color: #1f2937;
    }

    .featured-item-content h4 {
      margin: 0 0 0.5rem 0;
      color: #1f2937;
    }

    .featured-item-content p {
      margin: 0.5rem 0;
      font-size: 13px;
      color: #666;
    }

    .featured-item-content .price {
      color: #f59e0b;
      font-weight: 700;
      font-size: 16px;
      margin-top: 0.75rem;
    }

    .announcement-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem;
    }

    .announcement-card {
      background: white;
      border: 0.5px solid #e5e7eb;
      border-radius: 0.5rem;
      padding: 1.5rem;
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .announcement-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .announcement-card.pinned {
      border-color: #fbbf24;
      border-left: 4px solid #fbbf24;
    }

    .announcement-card h3 {
      margin: 0 0 0.75rem 0;
      color: #1f2937;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .announcement-card p {
      margin: 0.5rem 0;
      color: #6b7280;
      line-height: 1.6;
    }

    .announcement-card small {
      color: #999;
    }

    .announcement-category {
      display: inline-block;
      background: #f0f9ff;
      color: #0284c7;
      padding: 0.25rem 0.75rem;
      border-radius: 9999px;
      font-size: 12px;
      font-weight: 600;
      margin-bottom: 1rem;
    }

    .pinned-badge {
      color: #fbbf24;
      font-weight: 700;
    }
  </style>
</head>
<body>

<nav class="navbar" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
  <div class="navbar-container">
    <div class="navbar-logo">📢 Announcements</div>
    <a href="../index.php" class="navbar-btn">← Back</a>
  </div>
</nav>

<section class="section">
  <div class="container">
    <h1>📢 Announcements & Promotions</h1>

    <!-- Featured Marketplace Section -->
    <?php
    $featuredResult = mysqli_query($conn, "
      SELECT mp.*, u.username 
      FROM marketplace_posts mp
      LEFT JOIN users u ON mp.user_id = u.id
      WHERE mp.is_featured = 1 AND mp.featured_expiry > NOW()
      ORDER BY mp.featured_expiry ASC
      LIMIT 6
    ");
    
    if(mysqli_num_rows($featuredResult) > 0):
    ?>
    <div class="featured-promo">
      <h2>⭐ FEATURED VIP LISTINGS</h2>
      <p style="margin: 0 0 1.5rem 0; opacity: 0.95;">
        Koleksi barang pilihan dari member VIP kami dengan kualitas terbaik
      </p>
      <div class="featured-items">
        <?php while($row = mysqli_fetch_assoc($featuredResult)): ?>
        <div class="featured-item">
          <?php if($row['foto_barang']): ?>
            <img src="../assets/uploads/<?php echo $row['foto_barang']; ?>" class="featured-item-img" alt="<?php echo $row['nama_barang']; ?>">
          <?php else: ?>
            <div class="featured-item-img" style="display: flex; align-items: center; justify-content: center; font-size: 48px;">📸</div>
          <?php endif; ?>
          <div class="featured-item-content">
            <h4><?php echo $row['nama_barang']; ?></h4>
            <p><strong><?php echo $row['jenis_barang']; ?></strong></p>
            <p><?php echo substr($row['keterangan'], 0, 60); ?>...</p>
            <p><strong>Penjual:</strong> <?php echo $row['username']; ?></p>
            <div style="margin-top: 0.75rem;">
              <a href="../marketplace/index.php" style="color: #f59e0b; text-decoration: none; font-weight: 600;">
                Lihat Detail →
              </a>
            </div>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Regular Announcements -->
    <h2 style="margin-top: 2rem;">📰 Latest Announcements</h2>
    <div class="announcement-grid">
      <?php
      $result = mysqli_query($conn, "SELECT * FROM announcements ORDER BY pinned DESC, created_at DESC");
      
      if(mysqli_num_rows($result) > 0):
        while($row = mysqli_fetch_assoc($result)):
      ?>
      <div class="announcement-card <?php echo $row['pinned'] ? 'pinned' : ''; ?>">
        <h3>
          <?php echo $row['title']; ?>
          <?php if($row['pinned']): ?>
            <span class="pinned-badge" style="font-size: 16px;">📌</span>
          <?php endif; ?>
        </h3>

        <?php if($row['category']): ?>
          <span class="announcement-category"><?php echo $row['category']; ?></span>
        <?php endif; ?>

        <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
        
        <small>
          📅 <?php echo date('d M Y, H:i', strtotime($row['created_at'])); ?>
        </small>
      </div>
      <?php 
        endwhile; 
      else: 
      ?>
      <p style="grid-column: 1/-1; text-align: center; padding: 2rem; color: #999;">
        Belum ada announcement
      </p>
      <?php endif; ?>
    </div>
  </div>
</section>

<footer class="footer">
  <p>&copy; 2024 Brothers Company - Stay Updated with Our Announcements</p>
</footer>

</body>
</html>
