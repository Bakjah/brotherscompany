<?php 
include '../includes/config.php'; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Workshop Division | Brothers Company</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    :root {
      --primary-yellow: #fbbf24;
      --accent-yellow: #f59e0b;
      --bg-light: #fffbeb;
      --text-dark: #111827;
      --text-gray: #4b5563;
      /* Variabel ini dikontrol oleh transition.js */
      --hero-overlay: rgba(0, 0, 0, 0.4); 
    }

    body {
      font-family: 'Inter', sans-serif;
      color: var(--text-dark);
      background-color: #ffffff;
      margin: 0;
      line-height: 1.6;
    }

    .container { max-width: 1100px; margin: 0 auto; padding: 0 1.5rem; }

    /* Navbar */
    .navbar {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(8px);
      border-bottom: 1px solid #fef3c7;
      padding: 1rem 0;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .navbar-container { 
      display: flex; 
      justify-content: space-between; 
      align-items: center; 
    }

    .navbar-logo { 
      font-weight: 800; 
      font-size: 1.25rem; 
      color: var(--accent-yellow); 
      text-transform: uppercase;
      flex: 1;
    }
    
    .navbar-menu { 
      list-style: none; 
      display: flex; 
      gap: 1.5rem; 
      margin: 0; 
      padding: 0; 
      flex: 2; 
      justify-content: center;
    }

    .navbar-menu a { 
      text-decoration: none; 
      color: var(--text-gray); 
      font-weight: 500; 
      transition: 0.2s; 
      white-space: nowrap; 
    }

    .navbar-menu a:hover { color: var(--accent-yellow); }

    .navbar-menu a.active { 
      color: var(--accent-yellow);
      border-bottom: 2px solid var(--accent-yellow); 
      padding-bottom: 5px; 
      font-weight: 700;
    }

    .auth-buttons { 
      display: flex; 
      gap: 0.75rem; 
      align-items: center; 
      flex: 1; 
      justify-content: flex-end; 
    }

    .navbar-btn {
      text-decoration: none; 
      padding: 0.6rem 1.25rem; 
      border-radius: 0.5rem;
      font-size: 0.85rem; 
      font-weight: 600; 
      background: var(--accent-yellow); 
      color: #78350f;
      display: inline-block; 
      transition: 0.2s; 
      cursor: pointer; 
      border: none; 
      white-space: nowrap;
    }

    /* Hero Section */
    .hero { 
      padding: 10rem 0; 
      color: #ffffff; 
      text-align: center;
      min-height: 400px;
      display: flex;
      align-items: center;
      justify-content: center;
      /* Menggunakan variabel agar JS bisa mengatur kepekatan overlay */
      background-image: linear-gradient(var(--hero-overlay), var(--hero-overlay)), 
                        url('wsb.png') !important; 
      background-size: cover !important;
      background-position: center !important;
      background-repeat: no-repeat !important;
    }

    .hero h1 { 
      font-size: 3.5rem; 
      margin-bottom: 1rem; 
      font-weight: 800; 
      letter-spacing: -1px;
      text-shadow: 2px 2px 10px rgba(0,0,0,0.6);
      /* Default putih, akan diubah smooth oleh JS */
      color: #ffffff; 
    }

    .hero p {
      font-size: 1.25rem;
      max-width: 800px;
      margin: 0 auto;
      text-shadow: 1px 1px 5px rgba(0,0,0,0.6);
      color: #ffffff;
    }

    .section { padding: 6rem 0; }
    .section-title { text-align: center; margin-bottom: 4rem; }
    .section-title h2 { font-size: 2.25rem; color: var(--accent-yellow); margin-bottom: 1rem; }
    
    .grid-3 { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; }
    .grid-2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 3rem; align-items: center; }

    .card {
      background: white; padding: 2.5rem; border-radius: 1rem; border: 1px solid #fef3c7;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: 0.3s; text-align: center;
    }
    .card:hover { transform: translateY(-10px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
    
    .footer { padding: 4rem 0; text-align: center; background: #0f172a; color: #94a3b8; }
  </style>
</head>
<body data-theme="workshop">

<nav class="navbar">
  <div class="navbar-container container">
    <div class="navbar-logo">Autojuice</div>
    
    <ul class="navbar-menu">
      <li><a href="../index.php">Home</a></li>
      <li><a href="index.php" class="active">Workshop</a></li>
      <li><a href="../farm/">Farm</a></li>
      <li><a href="../asianfood/">Restaurant</a></li>
      <li><a href="../marketplace/">Marketplace</a></li>
    </ul>
    
    <div class="auth-buttons">
      <?php if(isLoggedIn()): ?>
        <span style="color: #4b5563; font-size: 0.9rem;">👤 <b><?php echo $_SESSION['username']; ?></b></span>
        
        <?php if(isAdmin()): ?>
          <a href="../admin/" class="navbar-btn" style="padding: 0.4rem 0.8rem;">Admin</a>
        <?php endif; ?>

        <a href="../auth/logout.php" class="navbar-btn" style="background: #e2e8f0; color: #475569;">Keluar</a>
      <?php else: ?>
        <a href="../login.php" class="navbar-btn" style="background:transparent; color:var(--accent-yellow); border:1.5px solid var(--accent-yellow);">Login</a>
        <a href="../register.php" class="navbar-btn">Daftar</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<section class="hero">
  <div class="container">
    <h1>Solusi Otomotif Terpercaya</h1>
    <p>Pusat perawatan kendaraan dan modifikasi performa dengan standar kualitas tinggi.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid-2">
      <div style="background: linear-gradient(135deg, #fbbf24, #f59e0b); height: 350px; border-radius: 2rem;"></div>
      <div>
        <h2 style="color: var(--accent-yellow); font-size: 2.25rem;">Tentang Workshop</h2>
        <p>Workshop kami menyediakan layanan perbaikan dan perawatan kendaraan dengan teknisi berpengalaman dan bersertifikasi.</p>
        <p>Kami berkomitmen memberikan layanan terbaik dengan harga yang kompetitif, suku cadang asli, dan hasil kerja yang memuaskan untuk kenyamanan Anda.</p>
      </div>
    </div>
  </div>
</section>

<section class="section" style="background: var(--bg-light);">
  <div class="container">
    <div class="section-title"><h2>Layanan Unggulan Kami</h2></div>
    <div class="grid-3">
      <div class="card">
        <h3 style="color: var(--accent-yellow);">Perbaikan</h3>
        <p>Perbaikan mesin, sistem kelistrikan, dan komponen utama kendaraan secara menyeluruh.</p>
      </div>
      <div class="card">
        <h3>Modifikasi</h3>
        <p>Layanan modifikasi performa dan kustomisasi visual sesuai dengan keinginan Anda.</p>
      </div>
       <div class="card">
        <h3>Custom Livery</h3>
        <p>Layanan modifikasi pada body kendaraan dengan desain kustom yang unik.</p>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-title"><h2>Hubungi Kami</h2></div>
    <div class="grid-3">
      <div class="card">
        <h4 style="color: var(--accent-yellow);">📍 Lokasi</h4>
        <p>Los Santos, Mirror Park, Workshop#2</p>
      </div>
      <div class="card">
        <h4>📞 Hotline</h4>
        <p><a href="https://bakjah.github.io/mecharing/" target="_blank">Click here</a> - Our Hotline</p>
      </div>
      <div class="card">
        <h4>⏰ Jam Kerja</h4>
        <p>Senin - Sabtu: 08:00 - 17:00</p>
      </div>
    </div>
  </div>
</section>

<footer class="footer">
  <div class="container"><p>&copy; 2026 Brothers Company - Workshop Division.</p></div>
</footer>

</body>
</html>