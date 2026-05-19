<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../includes/config.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Marketplace | Brothers Company</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    :root {
      --m-purple: #8b5cf6;
      --m-dark: #1e293b;
      --m-slate: #64748b;
    }

    body { 
        background-color: #f8fafc; 
        margin: 0; 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
        overflow-x: hidden;
    }

    /* --- ANIMASI WELCOME & EXIT SPLASH --- */
    #splash-screen {
      position: fixed; inset: 0; background: #ffffff; z-index: 99999;
      display: flex; justify-content: center; align-items: center;
      transition: all 0.8s cubic-bezier(0.65, 0, 0.35, 1);
      transform: translateY(0);
    }
    .splash-content { text-align: center; }
    
    .splash-logo {
      font-size: 5rem; display: block; margin-bottom: 10px;
      opacity: 0; transform: translateY(30px);
      animation: splashFadeIn 0.8s ease forwards;
    }
    
    .splash-text {
      font-size: 1.8rem; font-weight: 800; color: var(--m-purple); margin: 0;
      opacity: 0; transform: translateY(20px);
      animation: splashFadeIn 0.8s ease forwards 0.3s;
      letter-spacing: -1px;
    }
    
    /* GARIS UNGU (LOADER) */
    .splash-loader {
      height: 4px;
      background: var(--m-purple);
      margin: 20px auto 0;
      border-radius: 10px;
      width: 0;
      transition: width 1.2s cubic-bezier(0.4, 0, 0.2, 1); /* Lebih smooth */
    }

    @keyframes splashFadeIn { to { opacity: 1; transform: translateY(0); } }
    
    /* Animasi Tambahan saat Exit (Bounce) */
    .exit-emoji { animation: emojiBounce 0.5s ease infinite alternate; }
    @keyframes emojiBounce { from { transform: scale(1); } to { transform: scale(1.2); } }

    /* State: Saat Masuk (Garis Memanjang) */
    .show-line .splash-loader { width: 180px; }

    /* State: Halaman Siap (Splash Naik) */
    .loaded #splash-screen { transform: translateY(-100%); }

    /* State: Saat Klik Kembali (Splash Turun & Garis Memendek) */
    .exit-mode #splash-screen { transform: translateY(0) !important; opacity: 1 !important; visibility: visible !important; }
    .exit-mode .splash-loader { width: 0 !important; transition: width 1.5s ease-in; }

    /* --- NAVBAR --- */
    .navbar { background: white; border-bottom: 1px solid #e2e8f0; padding: 1rem 0; position: sticky; top: 0; z-index: 100; }
    .navbar-container { max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; }

    /* --- MARKET GRID --- */
    .market-container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
    .market-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; }

    .m-card {
      background: white; border-radius: 12px; overflow: hidden;
      box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); cursor: pointer;
      transition: all 0.3s ease; border: 1px solid #f1f5f9;
    }
    .m-card:hover { transform: translateY(-8px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }

    .m-img-box { width: 100%; aspect-ratio: 1/1; background: #f1f5f9; position: relative; }
    .m-img-box img { width: 100%; height: 100%; object-fit: cover; }
    
    .vip-tag {
      position: absolute; top: 10px; left: 10px; background: var(--m-purple);
      color: white; font-size: 10px; font-weight: 900; padding: 4px 10px; border-radius: 6px; z-index: 5;
    }

    .m-info { padding: 15px; }
    .m-name { font-weight: 700; color: var(--m-dark); margin-bottom: 2px; }
    .m-seller { font-size: 11px; color: var(--m-purple); font-weight: 600; margin-bottom: 6px; }
    .m-cat { font-size: 12px; color: var(--m-slate); }

    /* --- MODAL --- */
    .m-modal {
      display: none; position: fixed; inset: 0; z-index: 10000;
      background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px); padding: 20px;
      align-items: center; justify-content: center;
    }
    .m-modal.active { display: flex; }
    .m-modal-content {
      background: white; max-width: 900px; width: 100%; border-radius: 24px;
      display: flex; flex-direction: row; position: relative; overflow: hidden;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
      animation: modalPop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes modalPop { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .m-modal-left { flex: 1.2; background: #f8fafc; display: flex; align-items: center; justify-content: center; }
    .m-modal-left img { max-width: 100%; max-height: 550px; object-fit: contain; }
    .m-modal-right { flex: 0.8; padding: 40px; display: flex; flex-direction: column; }
    .m-close {
      position: absolute; top: 20px; right: 20px; width: 45px; height: 45px;
      background: #f1f5f9; border: none; border-radius: 50%; font-size: 28px;
      cursor: pointer; z-index: 10001; color: var(--m-slate);
      display: flex; align-items: center; justify-content: center; transition: 0.2s;
    }
    .m-close:hover { background: #e2e8f0; transform: rotate(90deg); }

    @media (max-width: 800px) {
      .m-modal-content { flex-direction: column; max-height: 90vh; overflow-y: auto; }
      .m-modal-left { height: 250px; flex: none; }
    }
  </style>
</head>
<body data-theme="marketplace">

<div id="splash-screen">
  <div class="splash-content">
    <span class="splash-logo" id="splash-emoji">🛒</span>
    <h2 class="splash-text" id="splash-msg">Brothers Marketplace</h2>
    <div class="splash-loader"></div>
  </div>
</div>

<nav class="navbar">
  <div class="navbar-container">
    <div style="font-weight:900; color:var(--m-purple); font-size:1.4rem; letter-spacing:-1px;">🛒 MARKET</div>
    <div>
      <a href="javascript:void(0)" onclick="handleExit('../index.php')" style="text-decoration:none; color:var(--m-slate); font-weight:600; margin-right:20px; font-size:14px;">← Kembali</a>
      
      <?php if(isLoggedIn()): ?>
        <a href="post.php" style="text-decoration:none; background:var(--m-purple); color:white; padding:10px 20px; border-radius:10px; font-weight:700; font-size:14px;">+ Post Barang</a>
        <a href="my-posts.php" style="text-decoration:none; background:var(--m-purple); color:white; padding:10px 20px; border-radius:10px; font-weight:700; font-size:14px;">Post saya</a>
      <?php else: ?>
        <a href="../login.php" style="text-decoration:none; background:var(--m-purple); color:white; padding:10px 20px; border-radius:10px; font-weight:700;">Login</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div class="market-container">
  <h2 style="margin-bottom:30px; color:var(--m-dark); font-size: 2rem; font-weight: 800;">Daftar Barang</h2>
  <div class="market-grid">
    <?php
    $query = "SELECT p.*, u.username as nama_penjual, (p.is_featured = 1 AND p.featured_expiry > NOW()) as is_vip FROM marketplace_posts p LEFT JOIN users u ON p.user_id = u.id ORDER BY is_vip DESC, p.created_at DESC";
    $result = mysqli_query($conn, $query);

    if($result && mysqli_num_rows($result) > 0):
      while($row = mysqli_fetch_assoc($result)):
        $foto = $row['foto_barang'] ?: '';
        $penjual = $row['nama_penjual'] ?: 'Anonim';
    ?>
      <div class="m-card" onclick="openMe('<?= addslashes($row['nama_barang']) ?>', '<?= addslashes($row['jenis_barang']) ?>', '<?= $row['no_telpon'] ?>', '<?= addslashes($row['discord_id']) ?>', '<?= addslashes($row['keterangan']) ?>', '<?= $foto ?>', '<?= date('d M Y', strtotime($row['created_at'])) ?>', '<?= addslashes($penjual) ?>')">
        <div class="m-img-box">
          <?php if($row['is_vip']): ?><div class="vip-tag">⭐ FEATURED</div><?php endif; ?>
          <img src="../assets/uploads/<?= $foto ?: 'default.jpg' ?>">
        </div>
        <div class="m-info">
          <div class="m-name"><?= htmlspecialchars($row['nama_barang']) ?></div>
          <div class="m-seller">Oleh: <?= htmlspecialchars($penjual) ?></div>
          <div class="m-cat"><?= htmlspecialchars($row['jenis_barang']) ?></div>
        </div>
      </div>
    <?php endwhile; endif; ?>
  </div>
</div>

<div id="mViewer" class="m-modal" onclick="closeMe()">
  <div class="m-modal-content" onclick="event.stopPropagation()">
    <button class="m-close" onclick="closeMe()">&times;</button>
    <div class="m-modal-left" id="vImg"></div>
    <div class="m-modal-right">
      <h2 id="vTitle" style="margin:0; color:var(--m-dark); font-size: 28px; font-weight: 800;"></h2>
      <p id="vSellerInfo" style="color:var(--m-purple); font-size:13px; font-weight:700; margin: 5px 0;"></p>
      <p id="vDate" style="color:var(--m-slate); font-size:12px; margin-bottom:20px;"></p>
      <div style="background:#f8fafc; padding:20px; border-radius:16px; font-size:14px; margin-top:auto;">
        <p id="vDesc" style="white-space: pre-wrap; margin-bottom:15px;"></p>
        <strong>📱 No:</strong> <span id="vTelp"></span><br>
        <strong>💬 Discord:</strong> <span id="vDiscord"></span>
      </div>
    </div>
  </div>
</div>

<script>
  // FUNGSI SAAT HALAMAN DIBUKA (WELCOME)
  window.addEventListener('load', () => {
    setTimeout(() => { document.body.classList.add('show-line'); }, 100);
    setTimeout(() => { document.body.classList.add('loaded'); }, 1800);
  });

  // FUNGSI ANIMASI KELUAR (EXIT)
  function handleExit(url) {
    const emoji = document.getElementById('splash-emoji');
    const msg = document.getElementById('splash-msg');
    
    // Set konten perpisahan
    emoji.innerText = "👋";
    emoji.classList.add('exit-emoji');
    msg.innerText = "See you soon!";
    
    // Mulai animasi
    document.body.classList.add('exit-mode');
    document.body.classList.remove('show-line'); // Garis memendek
    
    // Tahan selama 1.8 detik (Sama dengan durasi Welcome)
    setTimeout(() => {
        window.location.href = url;
    }, 1800);
  }

  // MODAL LOGIC
  function openMe(nama, jenis, telp, discord, ket, foto, tgl, penjual) {
    document.getElementById('vTitle').innerText = nama;
    document.getElementById('vSellerInfo').innerText = "👤 Penjual: " + penjual;
    document.getElementById('vDesc').innerText = ket;
    document.getElementById('vDate').innerText = "Terbit pada " + tgl;
    document.getElementById('vTelp').innerText = telp;
    document.getElementById('vDiscord').innerText = discord;
    document.getElementById('vImg').innerHTML = foto ? `<img src="../assets/uploads/${foto}">` : `📸`;
    document.getElementById('mViewer').classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeMe() {
    document.getElementById('mViewer').classList.remove('active');
    document.body.style.overflow = 'auto';
  }

  document.addEventListener('keydown', (e) => { if(e.key === "Escape") closeMe(); });
</script>
</body>
</html>