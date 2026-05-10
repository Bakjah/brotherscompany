<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'includes/config.php';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Brothers Company</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar">
  <div class="navbar-container">
    <div class="navbar-logo">🏢 Brothers Company</div>

    <ul class="navbar-menu">
      <li><a href="index.php">Home</a></li>
      <li><a href="workshop/">Workshop</a></li>
      <li><a href="farm/">Farm</a></li>
      <li><a href="asianfood/">Asian Food</a></li>
      <li><a href="marketplace/">Marketplace</a></li>
    </ul>

    <div>
      <?php if(isLoggedIn()): ?>
        <span style="margin-right: 1rem;">
          👤 <?php echo $_SESSION['username']; ?>
        </span>

        <?php if(isAdmin()): ?>
          <a href="admin/" class="navbar-btn"
             style="background: #fbbf24; color: white; margin-right: 0.5rem;">
             🔑 Admin
          </a>
        <?php endif; ?>

        <a href="auth/logout.php" class="navbar-btn">Logout</a>

      <?php else: ?>

        <a href="login.php" class="navbar-btn"
           style="margin-right: 0.5rem;">
           Login
        </a>

        <a href="register.php" class="navbar-btn"
           style="background: #10b981;">
           Register
        </a>

      <?php endif; ?>
    </div>
  </div>
</nav>

<section class="hero">
  <div class="container">
    <h1>Together, Building Better Tomorrow</h1>
    <p>Brothers Company - 3 Divisi dalam 1 Platform</p>
  </div>
</section>

<section class="section">
  <div class="container">

    <div class="section-title">
      <h2>Divisi Kami</h2>
    </div>

    <div class="grid-3">

      <div class="card">
        <h3>Autojuice Workshop</h3>
        <p>Solusi otomotif terpercaya</p>
        <a href="workshop/" class="btn btn-yellow">Kunjungi →</a>
      </div>

      <div class="card">
        <h3>GreenLaunders Farm</h3>
        <p>Pertanian modern berkelanjutan</p>
        <a href="farm/" class="btn btn-green">Kunjungi →</a>
      </div>

      <div class="card">
        <h3>Mosuban Asian Food</h3>
        <p>Cita rasa autentik Asia</p>
        <a href="asianfood/" class="btn btn-red">Kunjungi →</a>
      </div>

    </div>
  </div>
</section>

<section class="section" style="background: #f0f9ff;">
  <div class="container">

    <div class="section-title">
      <h2>📢 Pengumuman Terbaru</h2>
    </div>

    <div class="grid-2">

      <?php
      $result = mysqli_query(
          $conn,
          "SELECT * FROM announcements
           ORDER BY pinned DESC, created_at DESC
           LIMIT 6"
      );

      while($row = mysqli_fetch_assoc($result)):
      ?>

      <div class="card">
        <h4>
          <?php echo $row['title']; ?>

          <?php
          if($row['pinned']) echo '📌';
          ?>
        </h4>

        <p>
          <?php
          echo substr($row['content'], 0, 100) . '...';
          ?>
        </p>

        <small>
          <?php echo $row['category']; ?>
          •
          <?php echo date('d M Y', strtotime($row['created_at'])); ?>
        </small>
      </div>

      <?php endwhile; ?>

    </div>
  </div>
</section>

<section class="section">
  <div class="container">

    <h2>Tentang Kami</h2>

    <div class="grid-2">

      <div>
        <p>
          Brothers Company adalah holding company
          dengan 3 divisi bisnis di berbagai industri.
        </p>

        <p>
          Kami berkomitmen memberikan nilai terbaik
          kepada pelanggan dengan kualitas dan inovasi
          berkelanjutan.
        </p>
      </div>

      <div style="
        background: linear-gradient(135deg, #1e40af, #3b82f6);
        border-radius: 1rem;
        height: 250px;
      "></div>

    </div>
  </div>
</section>

<footer class="footer">
  <p>&copy; 2024 Brothers Company. All rights reserved.</p>
</footer>

</body>
</html>