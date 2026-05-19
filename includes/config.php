<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'brothers_company');

ini_set('display_errors', 1);
error_reporting(E_ALL);

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$conn) die("❌ Connection failed: " . mysqli_connect_error());
mysqli_set_charset($conn, "utf8");

// 🔐 SESSION SECURITY - HARUS SEBELUM session_start()
// Secure session cookie
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Mulai session
if (session_status() === PHP_SESSION_NONE) session_start();

// 🔐 SESSION SECURITY - Session timeout 1 jam
if (isset($_SESSION['LAST_ACTIVITY']) && 
    (time() - $_SESSION['LAST_ACTIVITY'] > 3600)) {
    session_destroy();
    header('Location: /brothers-company/login.php');
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

// 🔐 SECURITY HEADERS
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");

// 🔐 PASSWORD HASHING FUNCTIONS
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// 🔐 CSRF TOKEN FUNCTIONS
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// 🔐 CSRF TOKEN HTML
function getCSRFField() {
    return '<input type="hidden" name="csrf_token" value="' . generateCSRFToken() . '">';
}

// 🔐 AUTHENTICATION FUNCTIONS
function isLoggedIn() { 
    return isset($_SESSION['user_id']); 
}

function isAdmin() { 
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin'; 
}

function requireLogin() { 
    if (!isLoggedIn()) { 
        header('Location: /brothers-company/login.php'); 
        exit(); 
    } 
}

function requireAdmin() { 
    if (!isAdmin()) { 
        header('Location: /brothers-company/'); 
        exit(); 
    } 
}

function sanitize($input) { 
    global $conn; 
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($input))); 
}
?>
