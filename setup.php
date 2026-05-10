<?php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'brothers_company_db';

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Database not found. Create database 'brothers_company_db' first!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_email = htmlspecialchars($_POST['admin_email']);
    $admin_password = password_hash($_POST['admin_password'], PASSWORD_BCRYPT);
    $admin_name = htmlspecialchars($_POST['admin_name']);
    
    // Update admin user
    $query = "UPDATE users SET email='$admin_email', password='$admin_password', full_name='$admin_name' WHERE username='admin'";
    
    if (mysqli_query($conn, $query)) {
        echo '<div style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">';
        echo '✅ Setup successful! Admin account updated.<br><br>';
        echo '<strong>Login credentials:</strong><br>';
        echo 'Email: ' . $admin_email . '<br>';
        echo 'Password: ' . $_POST['admin_password'] . '<br><br>';
        echo '<a href="index.php">Go to Website →</a>';
        echo '</div>';
    } else {
        echo '<p style="color: red;">Error: ' . mysqli_error($conn) . '</p>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup - Brothers Company</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<section class="hero" style="padding: 5rem 2rem;">
    <div class="container" style="max-width: 500px;">
        <h1>🔧 Setup Administrator</h1>
        <p>Configure your admin account</p>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width: 500px;">
        <div class="card">
            <h2>Admin Account Setup</h2>
            
            <form method="POST">
                <div class="form-group">
                    <label>Admin Name</label>
                    <input type="text" name="admin_name" value="Admin User" required>
                </div>

                <div class="form-group">
                    <label>Admin Email</label>
                    <input type="email" name="admin_email" value="admin@brothers.com" required>
                </div>

                <div class="form-group">
                    <label>Admin Password</label>
                    <input type="password" name="admin_password" value="password" required>
                    <small style="color: #666; display: block; margin-top: 0.5rem;">
                        💡 You can change this to whatever you want
                    </small>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">✅ Setup Admin Account</button>
            </form>

            <hr style="margin: 2rem 0; border: none; border-top: 1px solid #e5e7eb;">

            <h3>📋 After Setup:</h3>
            <ol style="margin-left: 1.5rem;">
                <li>Click "Setup Admin Account" above</li>
                <li>It will hash your password securely</li>
                <li>Your new credentials will be shown</li>
                <li>Go to home page and login with new credentials</li>
                <li>Delete this setup.php file when done</li>
            </ol>
        </div>
    </div>
</section>

</body>
</html>
