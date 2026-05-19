<?php include 'includes/config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Brothers Company</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    /* Styling Dasar */
    body {
      background-color: #f3f4f6;
      font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      margin: 0;
    }

    /* Container Utama */
    .login-container {
      width: 100%;
      max-width: 400px;
      padding: 2.5rem;
      background: #ffffff;
      border-radius: 1rem;
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
      border: 1px solid rgba(229, 231, 235, 0.5);
    }

    .login-container h1 {
      text-align: center;
      margin-bottom: 2rem;
      font-size: 1.75rem;
      font-weight: 700;
      color: #1f2937;
      letter-spacing: -0.025em;
    }

    /* Form Group */
    .form-group {
      margin-bottom: 1.25rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-size: 0.875rem;
      font-weight: 600;
      color: #4b5563;
    }

    .form-group input {
      width: 100%;
      padding: 0.75rem 1rem;
      box-sizing: border-box;
      border: 1.5px solid #e5e7eb;
      border-radius: 0.5rem;
      font-size: 0.95rem;
      transition: all 0.2s ease;
      background-color: #f9fafb;
    }

    .form-group input:focus {
      outline: none;
      border-color: #8b5cf6;
      background-color: #ffffff;
      box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
    }

    /* Tombol Login */
    .btn-login {
      width: 100%;
      padding: 0.8rem;
      background: #8b5cf6;
      color: white;
      border: none;
      border-radius: 0.5rem;
      cursor: pointer;
      font-size: 1rem;
      font-weight: 600;
      margin-top: 1rem;
      transition: all 0.2s ease;
    }

    .btn-login:hover {
      background: #7c3aed;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(139, 92, 246, 0.2);
    }

    .btn-login:active {
      transform: translateY(0);
    }

    /* Alerts */
    .alert {
      padding: 1rem;
      border-radius: 0.5rem;
      margin-bottom: 1.5rem;
      font-size: 0.875rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .alert-error {
      background: #fef2f2;
      color: #991b1b;
      border: 1px solid #fecaca;
    }

    .alert-success {
      background: #f0fdf4;
      color: #166534;
      border: 1px solid #bbf7d0;
    }

    /* Link Register */
    .register-link {
      text-align: center;
      margin-top: 1.5rem;
      font-size: 0.875rem;
      color: #6b7280;
    }

    .register-link a {
      color: #8b5cf6;
      text-decoration: none;
      font-weight: 600;
    }

    .register-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="login-container">
  <h1>🔐 Login</h1>

  <?php
  // 🔐 RATE LIMITING
  if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] > 5) {
    if (time() - $_SESSION['last_attempt'] < 300) {
      echo '<div class="alert alert-error">
        ⚠️ Terlalu banyak gagal login. Tunggu 5 menit.
      </div>';
      exit();
    } else {
      $_SESSION['login_attempts'] = 0;
    }
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Gunakan prepared statement jika memungkinkan untuk keamanan maksimal
    $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    
    if (mysqli_num_rows($result) > 0) {
      $user = mysqli_fetch_assoc($result);
      
      if (verifyPassword($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['login_attempts'] = 0;
        
        header('Location: index.php');
        exit();
      }
    }
    
    $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
    $_SESSION['last_attempt'] = time();
    
    echo '<div class="alert alert-error">
      ❌ Username atau password salah
    </div>';
  }
  ?>

  <form method="POST">
    <div class="form-group">
      <label>Username</label>
      <input type="text" name="username" placeholder="Masukkan username" required autofocus>
    </div>

    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" placeholder="••••••••" required>
    </div>

    <button type="submit" class="btn-login">Masuk ke Akun</button>

    <div class="register-link">
      Belum punya akun? <a href="register.php">Daftar sekarang</a>
    </div>
  </form>
</div>

</body>
</html>