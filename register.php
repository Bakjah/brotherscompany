<?php include 'includes/config.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - Brothers Company</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .register-container {
      max-width: 500px;
      margin: 2rem auto;
      padding: 2rem;
      background: white;
      border: 0.5px solid #e5e7eb;
      border-radius: 0.5rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    .register-container h1 {
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
    .btn-register {
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
    .btn-register:hover {
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
    .login-link {
      text-align: center;
      margin-top: 1rem;
      font-size: 14px;
    }
    .login-link a {
      color: #8b5cf6;
      text-decoration: none;
    }
  </style>
</head>
<body>

<div class="register-container">
  <h1>📝 Daftar Akun Baru</h1>

  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // 🔐 VALIDATION
    $errors = [];

    if (strlen($username) < 3) {
      $errors[] = "Username minimal 3 karakter";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = "Email tidak valid";
    }

    if (strlen($password) < 6) {
      $errors[] = "Password minimal 6 karakter";
    }

    if ($password !== $password_confirm) {
      $errors[] = "Password tidak cocok";
    }

    // Check username sudah ada
    $checkUsername = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
    if (mysqli_num_rows($checkUsername) > 0) {
      $errors[] = "Username sudah terdaftar";
    }

    // Check email sudah ada
    $checkEmail = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
    if (mysqli_num_rows($checkEmail) > 0) {
      $errors[] = "Email sudah terdaftar";
    }

    if (empty($errors)) {
      // 🔐 HASH PASSWORD SEBELUM INSERT
      $hashed_password = hashPassword($password);
      
      $result = mysqli_query($conn, "INSERT INTO users 
        (username, email, password, created_at) 
        VALUES ('$username', '$email', '$hashed_password', NOW())");

      if ($result) {
        echo '<div class="alert alert-success">
          ✅ Pendaftaran berhasil! <a href="login.php">Klik di sini untuk login</a>
        </div>';
      } else {
        echo '<div class="alert alert-error">
          ❌ Terjadi error. Coba lagi nanti.
        </div>';
      }
    } else {
      foreach ($errors as $error) {
        echo '<div class="alert alert-error">❌ ' . $error . '</div>';
      }
    }
  }
  ?>

  <form method="POST">
    <div class="form-group">
      <label>Username</label>
      <input type="text" name="username" required>
    </div>

    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" required>
    </div>

    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" required>
      <small style="color: #666;">Minimal 6 karakter</small>
    </div>

    <div class="form-group">
      <label>Konfirmasi Password</label>
      <input type="password" name="password_confirm" required>
    </div>

    <button type="submit" class="btn-register">Daftar</button>

    <div class="login-link">
      Sudah punya akun? <a href="login.php">Login di sini</a>
    </div>
  </form>
</div>

</body>
</html>
