<?php include '../includes/config.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Marketplace</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .featured-section {
      background: linear-gradient(135deg, #fbbf24, #f59e0b);
      padding: 2rem 0;
      margin-bottom: 2rem;
      border-radius: 0.5rem;
    }

    .featured-title {
      color: white;
      text-align: center;
      margin-bottom: 1.5rem;
      font-size: 24px;
      font-weight: 700;
    }

    .featured-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 1.5rem;
    }

    .featured-card {
      background: white;
      border-radius: 0.75rem;
      overflow: hidden;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .featured-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2);
    }

    .featured-badge {
      position: absolute;
      top: 10px;
      right: 10px;
      background: linear-gradient(135deg, #fbbf24, #f59e0b);
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 9999px;
      font-weight: 700;
      font-size: 12px;
      z-index: 10;
    }

    .featured-image {
      position: relative;
      width: 100%;
      height: 200px;
      overflow: hidden;
    }

    .featured-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .featured-content {
      padding: 1.5rem;
    }

    .featured-content h3 {
      margin: 0 0 0.75rem 0;
      font-size: 18px;
      color: #1f2937;
    }

    .featured-content p {
      margin: 0.5rem 0;
      font-size: 13px;
      color: #666;
    }

    .featured-content .truncated {
      color: #6b7280;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
      text-overflow: ellipsis;
      margin: 1rem 0;
      height: 2.8em;
      line-height: 1.4;
    }

    .featured-footer {
      display: flex;
      gap: 0.5rem;
      margin-top: 1rem;
    }

    .featured-footer button {
      flex: 1;
      padding: 0.75rem;
      background: #8b5cf6;
      color: white;
      border: none;
      border-radius: 0.375rem;
      cursor: pointer;
      font-weight: 600;
      transition: background 0.2s;
    }

    .featured-footer button:hover {
      background: #7c3aed;
    }

    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    .modal-content {
      background-color: white;
      margin: 5% auto;
      padding: 2rem;
      border-radius: 0.75rem;
      width: 90%;
      max-width: 600px;
      max-height: 90vh;
      overflow-y: auto;
      animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
      from {
        transform: translateY(-50px);
        opacity: 0;
      }
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .close-modal {
      color: #6b7280;
      float: right;
      font-size: 1.75rem;
      font-weight: bold;
      cursor: pointer;
      border: none;
      background: none;
    }

    .close-modal:hover {
      color: #1f2937;
    }

    .modal-image {
      width: 100%;
      max-height: 300px;
      object-fit: cover;
      border-radius: 0.5rem;
      margin-bottom: 1.5rem;
    }

    .modal-section {
      margin-bottom: 1.5rem;
    }

    .modal-label {
      font-weight: bold;
      color: #374151;
      margin-bottom: 0.5rem;
    }

    .modal-value {
      color: #6b7280;
      padding: 0.75rem;
      background: #f9fafb;
      border-radius: 0.5rem;
      word-break: break-word;
    }

    .detail-btn {
      background: #8b5cf6;
      color: white;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 0.375rem;
      cursor: pointer;
      margin-top: 0.5rem;
      transition: background 0.3s;
      width: 100%;
    }

    .detail-btn:hover {
      background: #7c3aed;
    }

    .text-truncated {
      color: #6b7280;
      line-height: 1.5;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
      text-overflow: ellipsis;
      height: 4.5em;
      word-wrap: break-word;
    }

    .divider {
      text-align: center;
      margin: 2rem 0;
      color: #999;
      font-size: 14px;
    }
  </style>
</head>
<body>

<nav class="navbar" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
  <div class="navbar-container">
    <div class="navbar-logo">🛒 Marketplace</div>
    <div>
      <a href="../index.php" class="navbar-btn" style="background: white; color: #7c3aed; margin-right: 0.5rem;">← Kembali</a>
      <?php if(isLoggedIn()): ?>
        <a href="my-posts.php" class="navbar-btn" style="background: white; color: #7c3aed; margin-right: 0.5rem;">📋 Post Saya</a>
        <a href="post.php" class="navbar-btn" style="background: white; color: #7c3aed; margin-right: 0.5rem;">+ Post Barang</a>
        <a href="../auth/logout.php" class="navbar-btn" style="background: white; color: #7c3aed;">Logout</a>
      <?php else: ?>
        <a href="../login.php" class="navbar-btn">Login</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<section class="hero" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
  <div class="container">
    <h1>🛒 Marketplace</h1>
    <p>Jual beli barang dengan mudah dan aman</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <!-- Featured Section -->
    <?php
    $featuredResult = mysqli_query($conn, "
      SELECT * FROM marketplace_posts 
      WHERE is_featured = 1 AND featured_expiry > NOW()
      ORDER BY featured_expiry ASC
    ");
    if(mysqli_num_rows($featuredResult) > 0):
    ?>
    <div class="featured-section">
      <div class="featured-title">⭐ FEATURED LISTINGS (VIP)</div>
      <div class="featured-grid">
        <?php while($row = mysqli_fetch_assoc($featuredResult)): 
          $keterangan = $row['keterangan'];
        ?>
        <div class="featured-card">
          <div class="featured-image">
            <?php if($row['foto_barang']): ?>
              <img src="../assets/uploads/<?php echo $row['foto_barang']; ?>" alt="<?php echo $row['nama_barang']; ?>">
            <?php else: ?>
              <div style="width: 100%; height: 100%; background: #e5e7eb; display: flex; align-items: center; justify-content: center; font-size: 48px;">📸</div>
            <?php endif; ?>
            <div class="featured-badge">⭐ VIP</div>
          </div>
          <div class="featured-content">
            <h3><?php echo $row['nama_barang']; ?></h3>
            <p><strong>Jenis:</strong> <?php echo $row['jenis_barang']; ?></p>
            <p class="truncated"><?php echo $keterangan; ?></p>
            <div class="featured-footer">
              <button onclick="openDetail(
                '<?php echo addslashes($row['nama_barang']); ?>',
                '<?php echo addslashes($row['jenis_barang']); ?>',
                '<?php echo $row['no_telpon']; ?>',
                '<?php echo addslashes($row['discord_id']); ?>',
                '<?php echo addslashes($keterangan); ?>',
                '<?php echo $row['foto_barang']; ?>',
                '<?php echo date('d M Y', strtotime($row['created_at'])); ?>'
              )">📄 Detail</button>
            </div>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
    </div>
    <div class="divider">↓ Regular Listings ↓</div>
    <?php endif; ?>

    <!-- Regular Listings -->
    <h2>Daftar Barang</h2>
    <div class="grid-3">
      <?php
      $result = mysqli_query($conn, "
        SELECT * FROM marketplace_posts 
        WHERE is_featured = 0 OR featured_expiry <= NOW()
        ORDER BY created_at DESC
      ");
      if (mysqli_num_rows($result) > 0):
        while ($row = mysqli_fetch_assoc($result)):
          $keterangan = $row['keterangan'];
      ?>
      <div class="card">
        <?php if($row['foto_barang']): ?>
          <img src="../assets/uploads/<?php echo $row['foto_barang']; ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: 0.5rem; margin-bottom: 1rem;">
        <?php else: ?>
          <div style="width: 100%; height: 200px; background: #e5e7eb; border-radius: 0.5rem; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center; color: #9ca3af;">📸 No Image</div>
        <?php endif; ?>
        <h3><?php echo $row['nama_barang']; ?></h3>
        <p><strong>Jenis:</strong> <?php echo $row['jenis_barang']; ?></p>
        <p><strong>No Telpon:</strong> <a href="tel:<?php echo $row['no_telpon']; ?>"><?php echo $row['no_telpon']; ?></a></p>
        <p><strong>Discord:</strong> <?php echo $row['discord_id']; ?></p>
        <p style="background: #f0f9ff; padding: 1rem; border-radius: 0.5rem; margin: 1rem 0;" class="text-truncated">
          <?php echo $keterangan; ?>
        </p>
        <small>📅 <?php echo date('d M Y', strtotime($row['created_at'])); ?></small>
        
        <button class="detail-btn" onclick="openDetail(
          '<?php echo addslashes($row['nama_barang']); ?>',
          '<?php echo addslashes($row['jenis_barang']); ?>',
          '<?php echo $row['no_telpon']; ?>',
          '<?php echo addslashes($row['discord_id']); ?>',
          '<?php echo addslashes($keterangan); ?>',
          '<?php echo $row['foto_barang']; ?>',
          '<?php echo date('d M Y', strtotime($row['created_at'])); ?>'
        )">📄 Lihat Detail</button>
      </div>
      <?php endwhile; else: ?>
        <p style="grid-column: 1/-1; text-align: center; padding: 2rem;">Belum ada barang. Jadilah yang pertama!</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<footer class="footer">
  <p>&copy; 2024 Brothers Company Marketplace</p>
</footer>

<div id="detailModal" class="modal">
  <div class="modal-content">
    <button class="close-modal" onclick="closeDetail()">&times;</button>
    
    <div id="modalImage" style="margin-bottom: 1.5rem;"></div>
    
    <h2 id="modalNamaBarang" style="margin-bottom: 1rem;"></h2>
    
    <div class="modal-section">
      <div class="modal-label">📦 Jenis Barang</div>
      <div class="modal-value" id="modalJenis"></div>
    </div>
    
    <div class="modal-section">
      <div class="modal-label">📱 No Telepon</div>
      <div class="modal-value">
        <a id="modalNoTelpon" href="#" style="color: #8b5cf6; text-decoration: none;"></a>
      </div>
    </div>
    
    <div class="modal-section">
      <div class="modal-label">💬 Discord ID</div>
      <div class="modal-value" id="modalDiscord"></div>
    </div>
    
    <div class="modal-section">
      <div class="modal-label">📝 Keterangan Lengkap</div>
      <div class="modal-value" style="line-height: 1.6; white-space: pre-wrap;" id="modalKeterangan"></div>
    </div>
    
    <div class="modal-section">
      <div class="modal-label">📅 Dipost Tanggal</div>
      <div class="modal-value" id="modalTanggal"></div>
    </div>
  </div>
</div>

<script>
function openDetail(nama, jenis, telepon, discord, keterangan, foto, tanggal) {
  const modal = document.getElementById('detailModal');
  
  document.getElementById('modalNamaBarang').textContent = nama;
  document.getElementById('modalJenis').textContent = jenis;
  document.getElementById('modalNoTelpon').textContent = telepon;
  document.getElementById('modalNoTelpon').href = 'tel:' + telepon;
  document.getElementById('modalDiscord').textContent = discord;
  document.getElementById('modalKeterangan').textContent = keterangan;
  document.getElementById('modalTanggal').textContent = tanggal;
  
  const imageDiv = document.getElementById('modalImage');
  if (foto) {
    imageDiv.innerHTML = '<img src="../assets/uploads/' + foto + '" class="modal-image" alt="' + nama + '">';
  } else {
    imageDiv.innerHTML = '<div style="width: 100%; height: 250px; background: #e5e7eb; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 3rem;">📸</div>';
  }
  
  modal.style.display = 'block';
}

function closeDetail() {
  document.getElementById('detailModal').style.display = 'none';
}

window.onclick = function(event) {
  const modal = document.getElementById('detailModal');
  if (event.target == modal) {
    modal.style.display = 'none';
  }
}

document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape') {
    closeDetail();
  }
});
</script>

</body>
</html>
