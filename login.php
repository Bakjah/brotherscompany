<?php include 'includes/config.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Brothers Company</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .login-container {
      max-width: 400px;
      margin: 5rem auto;
      padding: 2rem;
      background: white;
      border: 0.5px solid #e5e7eb;
      border-radius: 0.5rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    .login-container h1 {
      text-align: center;
      margin-bottom: 2rem;
      color: #8b5cf6;
    }
    .form-group {
      margin-bottom: 1rem;
    }
    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: #374151;
    }
    .form-group input {
      width: 100%;
      padding: 0.75rem;
      border: 0.5px solid #e5e7eb;
      border-radius: 0.375rem;
    }
    .form-group input:focus {
      outline: none;
      border-color: #8b5cf6;
    }
    .btn-login {
      width: 100%;
      padding: 0.75rem;
      background: #8b5cf6;
      color: white;
      border: none;
      border-radius: 0.375rem;
      cursor: pointer;
      font-weight: 600;
      margin-top: 1rem;
      transition: background 0.3s;
    }
    .btn-login:hover {
      background: #7c3aed;
    }
    .alert {
      padding: 1rem;
      border-radius: 0.375rem;
      margin-bottom: 1rem;
    }
    .alert-error {
      background: #fee2e2;
      color: #991b1b;
      border: 1px solid #fca5a5;
    }
    .alert-success {
      background: #dcfce7;
      color: #166534;
      border: 1px solid #86efac;
    }
    .register-link {
      text-align: center;
      margin-top: 1rem;
      font-size: 14px;
    }
    .register-link a {
      color: #8b5cf6;
      text-decoration: none;
    }
  </style>
</head>
<body>

<div class="login-container">
  <h1>🔐 Login</h1>

  <?php
  // 🔐 RATE LIMITING - Simple brute force protection
  if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] > 5) {
    if (time() - $_SESSION['last_attempt'] < 300) { // 5 menit
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

    $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    
    if (mysqli_num_rows($result) > 0) {
      $user = mysqli_fetch_assoc($result);
      
      // 🔐 SECURE PASSWORD VERIFICATION
      if (verifyPassword($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['login_attempts'] = 0; // Reset attempt counter
        
        header('Location: index.php');
        exit();
      }
    }
    
    // 🔐 LOGIN FAILED - Increment attempt counter
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
      <input type="text" name="username" required autofocus>
    </div>

    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" required>
    </div>

    <button type="submit" class="btn-login">Login</button>

    <div class="register-link">
      Belum punya akun? <a href="register.php">Daftar sekarang</a>
    </div>
  </form>
</div>

</body>
</html>
