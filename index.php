<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'includes/config.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Brothers Company | Holding Company</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    :root {
      --primary-blue: #1e40af;
      --accent-blue: #3b82f6;
      --bg-light: #f8fafc;
      --color-workshop: #fbbf24;
      --color-farm: #10b981;
      --color-food: #ef4444;
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

    /* Navbar */
    .navbar {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(8px);
      border-bottom: 1px solid #dbeafe;
      padding: 1rem 0;
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    
    .navbar-container { display: flex; justify-content: space-between; align-items: center; }
    .navbar-logo { font-weight: 800; font-size: 1.25rem; color: var(--primary-blue); text-transform: uppercase; flex: 1; }
    .navbar-menu { list-style: none; display: flex; gap: 1.5rem; margin: 0; padding: 0; flex: 2; justify-content: center; }
    .navbar-menu a { text-decoration: none; color: var(--text-gray); font-weight: 500; transition: 0.2s; white-space: nowrap; }
    .navbar-menu a:hover { color: var(--primary-blue); }
    .navbar-menu a.active { color: var(--primary-blue); border-bottom: 2px solid var(--primary-blue); padding-bottom: 5px; font-weight: 700; }

    .auth-buttons { display: flex; gap: 0.75rem; align-items: center; flex: 1; justify-content: flex-end; }
    .navbar-btn {
      text-decoration: none; padding: 0.6rem 1.25rem; border-radius: 0.5rem;
      font-size: 0.85rem; font-weight: 600; background: var(--primary-blue); 
      color: white; display: inline-block; transition: 0.2s; border: none; white-space: nowrap;
    }

    /* --- HERO SECTION (Sama dengan Workshop) --- */
    .hero { 
      padding: 10rem 0; /* Tinggi padding sama dengan WS */
      color: #ffffff; 
      text-align: center;
      min-height: 400px;
      display: flex;
      align-items: center;
      justify-content: center;
      /* Pastikan file 'home_bg.jpg' atau nama file gambar kamu benar */
      background-image: linear-gradient(var(--hero-overlay), var(--hero-overlay)), 
                        url('brothers.png') !important; 
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

    /* Section & Cards */
    .section { padding: 6rem 0; }
    .section-title { text-align: center; margin-bottom: 4rem; }
    .section-title h2 { font-size: 2.25rem; color: var(--primary-blue); margin-bottom: 1rem; }
    
    .grid-3 { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; }
    .grid-2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 3rem; align-items: center; }

    .card {
      background: white; padding: 2.5rem; border-radius: 1rem; border: 1px solid #e5e7eb;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: 0.3s; text-align: center;
    }
    .card:hover { transform: translateY(-10px); }
    .card-workshop { border-top: 6px solid var(--color-workshop); }
    .card-farm { border-top: 6px solid var(--color-farm); }
    .card-food { border-top: 6px solid var(--color-food); }

    .btn-div { display: block; padding: 0.8rem; border-radius: 0.5rem; text-decoration: none; font-weight: 700; margin-top: 1.5rem; transition: 0.2s; }
    .btn-workshop { background: var(--color-workshop); color: #78350f; }
    .btn-farm { background: var(--color-farm); color: white; }
    .btn-food { background: var(--color-food); color: white; }

    /* Announcement */
    .announcement-wrapper { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; }
    .card-announcement { background: white; padding: 1.5rem; border-radius: 0.75rem; border: 1px solid #e5e7eb; display: flex; flex-direction: column; min-height: 200px; }
    .announcement-text { font-size: 0.9rem; color: var(--text-gray); margin-bottom: 1.5rem; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; overflow-wrap: break-word; }

    /* Modal */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(5px); display: none; justify-content: center; align-items: center; z-index: 2000; }
    .modal-content { background: white; padding: 2.5rem; border-radius: 1.5rem; width: 90%; max-width: 650px; max-height: 80vh; display: flex; flex-direction: column; position: relative; }
    .modal-overlay.active { display: flex; }
    #modalBody { line-height: 1.8; color: var(--text-gray); white-space: pre-wrap; overflow-y: auto; overflow-wrap: break-word; padding-right: 10px; }
    .close-modal { position: absolute; top: 1rem; right: 1.5rem; font-size: 2rem; cursor: pointer; color: #94a3b8; line-height: 1; }

    .footer { padding: 4rem 0; text-align: center; background: #0f172a; color: #94a3b8; 

	@font-face {
      font-family: 'logoBrothers';
      src: url('assets/font/copperplategothic_bold.ttf') format('truetype');
      font-weight: bold;
      font-style: normal;
    }
  </style>
</head>
<body data-theme="home">

<nav class="navbar">
  <div class="navbar-container container">
    <div class="navbar-logo">Brothers Co.</div>
    <ul class="navbar-menu">
      <li><a href="index.php" class="active">Home</a></li>
      <li><a href="workshop/">Workshop</a></li>
      <li><a href="farm/">Farm</a></li>
      <li><a href="asianfood/">Restaurant</a></li>
      <li><a href="marketplace/">Marketplace</a></li>
    </ul>
    <div class="auth-buttons">
      <?php if(isLoggedIn()): ?>
        <span style="color: #4b5563; font-size: 0.9rem;">👤 <b><?php echo $_SESSION['username']; ?></b></span>
        <?php if(isAdmin()): ?>
          <a href="admin/" class="navbar-btn" style="background: var(--color-workshop); color: #78350f; padding: 0.4rem 0.8rem;">Admin</a>
        <?php endif; ?>
        <?php if(isEmployee()): ?>
          <a href="Brotherssoftware/" class="navbar-btn" style="background: #8b5cf6; color: white; padding: 0.4rem 0.8rem;">Staff</a>
        <?php endif; ?>
        <a href="auth/logout.php" class="navbar-btn" style="background: #e2e8f0; color: #475569;">Keluar</a>
      <?php else: ?>
        <a href="login.php" class="navbar-btn" style="background:transparent; color:var(--primary-blue); border:1.5px solid var(--primary-blue);">Login</a>
        <a href="register.php" class="navbar-btn">Daftar</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<section class="hero">
  <div class="container">
    <h1>Together, Building Better Tomorrow</h1>
    <p>Holding Company yang mengintegrasikan inovasi, kualitas, dan keberlanjutan di setiap sektor bisnis.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid-2">
      <div style="background: linear-gradient(135deg, #1e40af, #60a5fa); height: 350px; border-radius: 2rem;">
        <h1 style="color: white; text-align: center; height: 100%; display: flex; align-items: center; justify-content: center; font-family: 'logoBrothers'; font-size: 60px;">Brothers<p style="font-size: 20px; padding-top: 26px;">®</p></h1>
      </div>
      <div>
        <h2 style="color: var(--primary-blue); font-size: 2.25rem;">Tentang Brothers Company</h2>
        <p>Brothers Company adalah sebuah holding company yang menaungi berbagai lini bisnis strategis di Indonesia, mulai dari otomotif hingga ketahanan pangan.</p>
        <div style="display: flex; gap: 2rem; margin-top: 2rem;">
          <div><h3 style="margin:0; color: var(--primary-blue);">4+</h3><small>Tahun Pengalaman</small></div>
          <div><h3 style="margin:0; color: var(--primary-blue);">3</h3><small>Divisi Utama</small></div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section" style="background: var(--bg-light);">
  <div class="container">
    <div class="section-title"><h2>Layanan Strategis Kami</h2></div>
    <div class="grid-3">
      <div class="card card-workshop">
        <h3>Autojuice Workshop</h3>
        <p>Pusat perawatan kendaraan dan modifikasi performa standar kualitas tinggi.</p>
        <a href="workshop/" class="btn-div btn-workshop">Buka Unit Workshop →</a>
      </div>
      <div class="card card-farm">
        <h3>GreenLaunder Farm</h3>
        <p>Inovasi pertanian organik dan hidroponik untuk masa depan hijau.</p>
        <a href="farm/" class="btn-div btn-farm">Jelajahi Farm Kami →</a>
      </div>
      <div class="card card-food">
        <h3>Mosuban Kitchen</h3>
        <p>Menghadirkan kelezatan masakan Asia autentik dengan cita rasa premium.</p>
        <a href="asianfood/" class="btn-div btn-food">Lihat Menu Restoran →</a>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-title"><h2>📢 Informasi & Pengumuman</h2></div>
    <div class="announcement-wrapper">
      <?php
      $result = mysqli_query($conn, "SELECT * FROM announcements ORDER BY pinned DESC, created_at DESC LIMIT 6");
      if(mysqli_num_rows($result) > 0):
        while($row = mysqli_fetch_assoc($result)):
      ?>
      <div class="card-announcement">
        <h4 style="margin-top:0; color: var(--primary-blue);"><?php echo htmlspecialchars($row['title']); ?></h4>
        <p class="announcement-text"><?php echo htmlspecialchars($row['content']); ?></p>
        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 1rem; margin-top: auto;">
          <small style="color: #94a3b8;"><?php echo date('d M Y', strtotime($row['created_at'])); ?></small>
          <button class="navbar-btn" onclick="openModal('<?php echo addslashes(htmlspecialchars($row['title'])); ?>', '<?php echo addslashes(htmlspecialchars($row['content'])); ?>', '<?php echo date('d M Y', strtotime($row['created_at'])); ?>')" style="padding: 0.35rem 0.8rem; font-size: 0.75rem;">
            Baca Selengkapnya
          </button>
        </div>
      </div>
      <?php endwhile; else: ?>
        <p style="text-align: center; grid-column: span 3; color: var(--text-gray);">Belum ada pengumuman saat ini.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<div class="modal-overlay" id="announcementModal" onclick="closeModal()">
  <div class="modal-content" onclick="event.stopPropagation()">
    <span class="close-modal" onclick="closeModal()">&times;</span>
    <small id="modalDate" style="color: var(--accent-blue); font-weight: bold;"></small>
    <h2 id="modalTitle" style="color: var(--primary-blue); margin: 0.5rem 0 1rem 0;"></h2>
    <div id="modalBody"></div>
  </div>
</div>

<footer class="footer">
  <div class="container">
    <p>&copy; 2026 Brothers Company. All Rights Reserved.</p>
  </div>
</footer>

<script>
  function openModal(title, content, date) {
    document.getElementById('modalTitle').innerText = title;
    document.getElementById('modalBody').innerText = content;
    document.getElementById('modalDate').innerText = date;
    document.getElementById('announcementModal').classList.add('active');
    document.body.style.overflow = 'hidden'; 
  }
  function closeModal() {
    document.getElementById('announcementModal').classList.remove('active');
    document.body.style.overflow = 'auto';
  }
</script>

</body>
</html>