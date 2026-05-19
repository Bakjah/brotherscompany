<?php include 'includes/config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - Brothers Company</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    /* Styling Dasar - Konsisten dengan Login */
    body {
      background-color: #f3f4f6;
      font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      margin: 0;
      padding: 20px 0; /* Memberi ruang saat scroll di layar kecil */
    }

    /* Container Utama */
    .register-container {
      width: 100%;
      max-width: 450px;
      padding: 2.5rem;
      background: #ffffff;
      border-radius: 1rem;
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
      border: 1px solid rgba(229, 231, 235, 0.5);
    }

    .register-container h1 {
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

    .form-group small {
      display: block;
      margin-top: 0.4rem;
      font-size: 0.75rem;
      color: #9ca3af;
    }

    /* Tombol Register */
    .btn-register {
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

    .btn-register:hover {
      background: #7c3aed;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(139, 92, 246, 0.2);
    }

    .btn-register:active {
      transform: translateY(0);
    }

    /* Alerts */
    .alert {
      padding: 0.85rem 1rem;
      border-radius: 0.5rem;
      margin-bottom: 1rem;
      font-size: 0.875rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      line-height: 1.4;
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

    /* Link Login */
    .login-link {
      text-align: center;
      margin-top: 1.5rem;
      font-size: 0.875rem;
      color: #6b7280;
    }

    .login-link a {
      color: #8b5cf6;
      text-decoration: none;
      font-weight: 600;
    }

    .login-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="register-container">
  <h1>📝 Daftar Akun</h1>

  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

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

    $checkUsername = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
    if (mysqli_num_rows($checkUsername) > 0) {
      $errors[] = "Username sudah terdaftar";
    }

    $checkEmail = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
    if (mysqli_num_rows($checkEmail) > 0) {
      $errors[] = "Email sudah terdaftar";
    }

    if (empty($errors)) {
      $hashed_password = hashPassword($password);
      
      $result = mysqli_query($conn, "INSERT INTO users 
        (username, email, password, created_at) 
        VALUES ('$username', '$email', '$hashed_password', NOW())");

      if ($result) {
        echo '<div class="alert alert-success">
          ✅ Pendaftaran berhasil! <a href="login.php" style="color:inherit; font-weight:bold; text-decoration:underline;">Login di sini</a>
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
      <input type="text" name="username" placeholder="Username unik Anda" required autofocus>
    </div>

    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" placeholder="contoh@email.com" required>
    </div>

    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" placeholder="Minimal 6 karakter" required>
      <small>Gunakan kombinasi huruf dan angka agar lebih kuat.</small>
    </div>

    <div class="form-group">
      <label>Konfirmasi Password</label>
      <input type="password" name="password_confirm" placeholder="Ulangi password" required>
    </div>

    <button type="submit" class="btn-register">Buat Akun</button>

    <div class="login-link">
      Sudah punya akun? <a href="login.php">Login di sini</a>
    </div>
  </form>
</div>

</body>
</html>