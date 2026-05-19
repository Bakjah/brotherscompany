<?php 
include '../includes/config.php'; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Asian Food Division | Brothers Company</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    :root {
      --primary-red: #ef4444;
      --dark-red: #b91c1c;
      --bg-light: #fef2f2;
      --text-dark: #111827;
      --text-gray: #4b5563;
      /* Variabel untuk transisi hero overlay */
      --hero-overlay: rgba(0, 0, 0, 0.5); 
    }

    body {
      font-family: 'Inter', sans-serif;
      color: var(--text-dark);
      background-color: #ffffff;
      margin: 0;
      line-height: 1.6;
    }

    .container { max-width: 1100px; margin: 0 auto; padding: 0 1.5rem; }

    /* --- NAVBAR (Konsisten Total) --- */
    .navbar {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(8px);
      border-bottom: 1px solid #fee2e2;
      padding: 1rem 0;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .navbar-container { display: flex; justify-content: space-between; align-items: center; }
    .navbar-logo { font-weight: 800; font-size: 1.25rem; color: var(--primary-red); text-transform: uppercase; flex: 1; }
    
    .navbar-menu { 
      list-style: none; 
      display: flex; 
      gap: 1.5rem; 
      margin: 0; 
      padding: 0; 
      flex: 2; 
      justify-content: center;
      position: relative;
    }

    .navbar-menu a { 
      text-decoration: none; 
      color: var(--text-gray); 
      font-weight: 500; 
      transition: 0.2s; 
      white-space: nowrap; 
      padding: 5px 0;
    }

    .navbar-menu a:hover { color: var(--primary-red); }
    .navbar-menu a.active { color: var(--primary-red); border-bottom: 2px solid var(--primary-red); padding-bottom: 5px; font-weight: 700; }


    /* Indikator Garis Bawah Meluncur */
    .nav-indicator {
      position: absolute;
      bottom: -8px;
      height: 3px;
      background-color: var(--primary-red);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      border-radius: 10px;
      pointer-events: none;
    }

    .auth-buttons { display: flex; gap: 0.75rem; align-items: center; flex: 1; justify-content: flex-end; }

    .navbar-btn {
      text-decoration: none; 
      padding: 0.6rem 1.25rem; 
      border-radius: 0.5rem;
      font-size: 0.85rem; 
      font-weight: 600; 
      background: var(--primary-red); 
      color: white;
      display: inline-block; 
      transition: 0.2s; 
      cursor: pointer; 
      border: none; 
      white-space: nowrap;
    }

    /* --- HERO SECTION (Konsisten Besar Foto & Style) --- */
    .hero { 
      padding: 10rem 0; 
      color: #ffffff; 
      text-align: center;
      min-height: 400px;
      display: flex;
      align-items: center;
      justify-content: center;
      /* Pastikan file gambar ada di path ini */
      background-image: linear-gradient(var(--hero-overlay), var(--hero-overlay)), 
                        url('asianfood_hero.jpg') !important; 
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
      color: #ffffff; 
    }

    .hero p {
      font-size: 1.25rem;
      max-width: 800px;
      margin: 0 auto;
      text-shadow: 1px 1px 5px rgba(0,0,0,0.6);
      color: #ffffff;
    }

    /* Section Styles */
    .section { padding: 6rem 0; }
    .section-title { text-align: center; margin-bottom: 4rem; }
    .section-title h2 { font-size: 2.25rem; color: var(--primary-red); margin-bottom: 1rem; }
    
    .grid-3 { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; }
    .grid-2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 3rem; align-items: center; }

    /* Cards */
    .card {
      background: white; padding: 2.5rem; border-radius: 1rem; border: 1px solid #fee2e2;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: 0.3s; text-align: center;
    }
    .card:hover { transform: translateY(-10px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
    
    .footer { padding: 4rem 0; text-align: center; background: #0f172a; color: #94a3b8; }
  </style>
</head>
<body data-theme="asianfood">

<nav class="navbar">
  <div class="navbar-container container">
    <div class="navbar-logo">Coming Soon</div>
    
    <ul class="navbar-menu">
      <li><a href="../index.php">Home</a></li>
      <li><a href="../workshop/">Workshop</a></li>
      <li><a href="../farm/">Farm</a></li>
      <li><a href="index.php" class="active">Restaurant</a></li>
      <li><a href="../marketplace/">Marketplace</a></li>
      <div class="nav-indicator"></div>
    </ul>
    
    <div class="auth-buttons">
      <?php if(isLoggedIn()): ?>
        <span style="color: #4b5563; font-size: 0.9rem;">👤 <b><?php echo $_SESSION['username']; ?></b></span>
        
        <?php if(isAdmin()): ?>
          <a href="../admin/" class="navbar-btn" style="background: #fbbf24; color: #78350f; padding: 0.4rem 0.8rem;">Admin</a>
        <?php endif; ?>

        <a href="../auth/logout.php" class="navbar-btn" style="background: #e2e8f0; color: #475569;">Keluar</a>
      <?php else: ?>
        <a href="../login.php" class="navbar-btn" style="background:transparent; color:var(--primary-red); border:1.5px solid var(--primary-red);">Login</a>
        <a href="../register.php" class="navbar-btn">Daftar</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<section class="hero">
  <div class="container">
    <h1>Coming Soon!</h1>
    <p>Coming Soon!</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid-2">
      <div style="background: linear-gradient(135deg, #ef4444, #fecaca); height: 350px; border-radius: 2rem;"></div>
      <div>
        <h2 style="color: var(--primary-red); font-size: 2.25rem;">Tentang Coming Soon!</h2>
        <p>Coming Soon!</p>
        <p>Coming Soon!</p>
      </div>
    </div>
  </div>
</section>

<section class="section" style="background: var(--bg-light);">
  <div class="container">
    <div class="section-title"><h2>Menu Pilihan Kami</h2></div>
    <div class="grid-3">
      <div class="card">
        <h3 style="color: var(--primary-red);">Coming Soon!</h3>
        <p>Coming Soon!</p>
      </div>
      <div class="card">
        <h3 style="color: var(--primary-red);">Coming Soon!</h3>
        <p>Coming Soon!</p>
      </div>
      <div class="card">
        <h3 style="color: var(--primary-red);">Coming Soon!</h3>
        <p>Coming Soon!</p>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-title"><h2>Hubungi & Reservasi</h2></div>
    <div class="grid-3">
      <div class="card">
        <h4 style="color: var(--primary-red);">📍 Lokasi</h4>
        <p>Coming Soon</p>
      </div>
      <div class="card">
        <h4 style="color: var(--primary-red);">⏰ Jam Operasional</h4>
        <p>Coming Soon</p>
      </div>
      <div class="card">
        <h4 style="color: var(--primary-red);">📞 Telepon</h4>
        <p>Coming Soon</p>
      </div>
    </div>
  </div>
</section>

<footer class="footer">
  <div class="container"><p>&copy; 2026 Brothers Company - Asian Food Division. Authentic Taste.</p></div>
</footer>

</body>
</html>