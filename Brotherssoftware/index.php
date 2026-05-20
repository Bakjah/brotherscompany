<!DOCTYPE html>
<?php
// Auto-login dari session brotherscompany site
session_start();

$currentRole = $_SESSION['role'] ?? null;
$currentUserId = $_SESSION['user_id'] ?? null;

$autoLogin = false;
$autoUser = null;
$accessDenied = false;

// Allow employee and admin to access
if ($currentUserId && $currentRole) {
    if ($currentRole === 'employee' || $currentRole === 'admin') {
        $autoLogin = true;
        $autoUser = [
            'username' => $_SESSION['username'] ?? $currentRole,
            'name' => $_SESSION['full_name'] ?? $_SESSION['username'] ?? ucfirst($currentRole),
            'role' => $currentRole
        ];
    } else {
        // User role - show access denied
        $accessDenied = true;
    }
}
?>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MechanicApp - Brothers Company</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            overflow: hidden;
            height: 100vh;
            user-select: none;
        }

        /* ===== LOGIN SCREEN ===== */
        .login-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #008080 0%, #1a6b7a 50%, #0d4f5c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .login-screen.hidden {
            display: none;
        }

        .login-box {
            background: #ece9d8;
            border: 2px solid;
            border-color: #fff #808080 #808080 #fff;
            box-shadow: 4px 4px 16px rgba(0,0,0,0.5);
            padding: 0;
            width: 380px;
        }

        .login-header {
            height: 50px;
            background: linear-gradient(180deg, #245edc 0%, #1a4db8 50%, #1038a0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 10px;
        }

        .login-header-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .login-header-icon {
            width: 32px;
            height: 32px;
        }

        .login-title {
            color: #fff;
            font-size: 16px;
            font-weight: 700;
        }

        .login-body {
            padding: 24px;
        }

        .login-user-icon {
            display: flex;
            justify-content: center;
            margin-bottom: 16px;
        }

        .login-user-icon svg {
            width: 64px;
            height: 64px;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .login-field {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .login-field label {
            font-size: 12px;
            font-weight: 700;
            color: #444;
        }

        .login-field input {
            padding: 8px 10px;
            border: 2px solid;
            border-color: #808080 #fff #fff #808080;
            background: #fff;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            font-size: 13px;
        }

        .login-field input:focus {
            outline: none;
            border-color: #245edc;
        }

        .login-field select {
            padding: 8px 10px;
            border: 2px solid;
            border-color: #808080 #fff #fff #808080;
            background: #fff;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            font-size: 13px;
            cursor: pointer;
        }

        .login-buttons {
            display: flex;
            gap: 10px;
            margin-top: 8px;
        }

        .login-buttons button {
            flex: 1;
            padding: 10px;
            border: 2px solid;
            border-color: #fff #707070 #707070 #fff;
            border-radius: 3px;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-login {
            background: linear-gradient(180deg, #3a6ea5 0%, #245edc 50%, #1a4db8 100%);
            color: #fff;
            border-color: #a8c8e8 #1e3a6e #1e3a6e #a8c8e8;
        }

        .btn-login:hover {
            background: linear-gradient(180deg, #4a7eb5 0%, #346eec 50%, #2a5dc8 100%);
        }

        .btn-cancel {
            background: linear-gradient(180deg, #f0f0f0 0%, #d8d8d8 50%, #c0c0c0 100%);
            color: #000;
        }

        .btn-cancel:hover {
            background: linear-gradient(180deg, #fff 0%, #e8e8e8 50%, #d0d0d0 100%);
        }

        .login-error {
            background: #ffebeb;
            border: 2px solid #cc0000;
            padding: 8px 12px;
            font-size: 12px;
            color: #cc0000;
            display: none;
            margin-bottom: 10px;
        }

        .login-error.show {
            display: block;
        }

        .login-footer {
            padding: 12px 24px;
            background: #ddd4c8;
            border-top: 1px solid #bbb;
            text-align: center;
            font-size: 11px;
            color: #666;
        }

        /* Demo users info */
        .demo-users {
            margin-top: 12px;
            padding: 12px;
            background: #fffde7;
            border: 1px dashed #d4a017;
            font-size: 11px;
        }

        .demo-users-title {
            font-weight: 700;
            color: #8b6914;
            margin-bottom: 6px;
        }

        .demo-users-list {
            color: #666;
            line-height: 1.6;
        }

        /* ===== USER INFO IN TASKBAR ===== */
        .user-info {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            background: rgba(0,0,0,0.2);
            border-radius: 4px;
            margin-right: auto;
        }

        .user-info svg {
            width: 20px;
            height: 20px;
        }

        .user-info-text {
            color: #fff;
            font-size: 11px;
        }

        .user-role {
            font-size: 9px;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: 700;
        }

        .role-admin {
            background: #ffd700;
            color: #000;
        }

        .role-employee {
            background: #4db84d;
            color: #fff;
        }

        .role-user {
            background: #245edc;
            color: #fff;
        }

        /* ===== DESKTOP BACKGROUND ===== */
        .desktop {
            width: 100vw;
            height: 100vh;
            background: linear-gradient(135deg, #008080 0%, #1a6b7a 50%, #0d4f5c 100%);
            display: flex;
            flex-direction: column;
            padding: 10px;
            position: relative;
        }

        /* ===== DESKTOP ICONS ===== */
        .desktop-icons {
            flex: 1;
            display: flex;
            flex-direction: column;
            flex-wrap: wrap;
            align-content: flex-start;
            gap: 5px;
            padding: 10px;
        }

        .desktop-icon {
            width: 75px;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 8px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .desktop-icon:hover {
            background: rgba(255, 255, 255, 0.2);
            outline: 1px dotted #fff;
        }

        .desktop-icon:active .icon-img {
            filter: brightness(0.7) sepia(1) hue-rotate(180deg);
        }

        .icon-img {
            width: 48px;
            height: 48px;
            margin-bottom: 4px;
        }

        .icon-label {
            color: #fff;
            font-size: 11px;
            text-align: center;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
            word-wrap: break-word;
            line-height: 1.2;
        }

        /* ===== TASKBAR ===== */
        .taskbar {
            height: 42px;
            background: linear-gradient(180deg, #245edc 0%, #1a4db8 50%, #1038a0 100%);
            border-top: 1px solid #6b8fe0;
            display: flex;
            align-items: center;
            padding: 0 4px;
            gap: 2px;
            z-index: 100;
        }

        .start-button {
            height: 34px;
            padding: 0 10px;
            display: flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(180deg, #e5edfd 0%, #c8d8f5 50%, #aabde5 100%);
            border: 1px solid #fff;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            color: #000;
            margin-right: 4px;
            box-shadow: inset 0 1px 0 #fff, 1px 1px 2px rgba(0,0,0,0.3);
        }

        .start-button:hover {
            background: linear-gradient(180deg, #f0f5ff 0%, #dce8ff 50%, #c0d4f0 100%);
        }

        .start-button:active {
            background: linear-gradient(180deg, #aabde5 0%, #c8d8f5 50%, #e5edfd 100%);
        }

        .start-button img {
            width: 20px;
            height: 20px;
        }

        .taskbar-divider {
            width: 1px;
            height: 28px;
            background: linear-gradient(180deg, transparent, rgba(255,255,255,0.3), transparent);
            margin: 0 4px;
        }

        .tray-clock {
            margin-left: auto;
            padding: 4px 12px;
            background: linear-gradient(180deg, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.4) 100%);
            border: 1px solid rgba(0,0,0,0.3);
            border-radius: 2px;
            color: #fff;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-right: 6px;
        }

        /* ===== START MENU ===== */
        .start-menu {
            display: none;
            position: absolute;
            bottom: 42px;
            left: 4px;
            width: 380px;
            background: linear-gradient(180deg, #ede7de 0%, #ddd4c8 100%);
            border: 2px solid;
            border-color: #fff #808080 #808080 #fff;
            box-shadow: 2px 2px 8px rgba(0,0,0,0.4);
            z-index: 200;
        }

        .start-menu.open {
            display: flex;
        }

        .start-menu-sidebar {
            width: 26px;
            background: linear-gradient(180deg, #3a6ea5 0%, #1e4d7a 100%);
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding-bottom: 8px;
        }

        .start-menu-sidebar span {
            color: #fff;
            font-size: 18px;
            font-weight: 700;
            writing-mode: vertical-rl;
            text-orientation: mixed;
            transform: rotate(180deg);
            letter-spacing: 2px;
        }

        .start-menu-programs {
            flex: 1;
            padding: 6px 0;
        }

        .start-menu-title {
            padding: 6px 14px 4px;
            font-size: 11px;
            font-weight: 700;
            color: #666;
            border-bottom: 1px solid #bbb;
            margin-bottom: 4px;
        }

        .start-menu-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 14px;
            cursor: pointer;
            font-size: 13px;
            color: #000;
        }

        .start-menu-item:hover {
            background: linear-gradient(180deg, #316ac5 0%, #1a5499 100%);
            color: #fff;
        }

        .start-menu-item img {
            width: 28px;
            height: 28px;
        }

        .start-menu-separator {
            height: 1px;
            background: linear-gradient(90deg, transparent, #bbb, transparent);
            margin: 4px 10px;
        }

        /* ===== WINDOW COMPONENT ===== */
        .window {
            position: absolute;
            background: #ece9d8;
            border: 2px solid;
            border-color: #fff #808080 #808080 #fff;
            box-shadow: 2px 2px 8px rgba(0,0,0,0.4);
            min-width: 400px;
            min-height: 250px;
            z-index: 50;
        }

        .window-titlebar {
            height: 26px;
            background: linear-gradient(180deg, #245edc 0%, #1a4db8 50%, #1038a0 100%);
            display: flex;
            align-items: center;
            padding: 0 4px;
            cursor: move;
        }

        .window.inactive .window-titlebar {
            background: linear-gradient(180deg, #a8a8a8 0%, #888 50%, #707070 100%);
        }

        .window-icon {
            width: 16px;
            height: 16px;
            margin-right: 4px;
        }

        .window-title {
            flex: 1;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
        }

        .window-controls {
            display: flex;
            gap: 2px;
        }

        .window-btn {
            width: 21px;
            height: 16px;
            background: linear-gradient(180deg, #f0f0f0 0%, #d8d8d8 50%, #c0c0c0 100%);
            border: 1px solid;
            border-color: #fff #707070 #707070 #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 10px;
            line-height: 1;
        }

        .window-btn:hover {
            background: linear-gradient(180deg, #fff 0%, #e8e8e8 50%, #d0d0d0 100%);
        }

        .window-btn:active {
            border-color: #707070 #fff #fff #707070;
            background: linear-gradient(180deg, #c0c0c0 0%, #d8d8d8 50%, #f0f0f0 100%);
        }

        .window-btn-max {
            font-size: 8px;
        }

        .window-menubar {
            height: 22px;
            background: linear-gradient(180deg, #fff 0%, #ede7de 100%);
            border-bottom: 1px solid #bbb;
            display: flex;
            align-items: center;
            padding: 0 4px;
            gap: 0;
        }

        .menu-item {
            padding: 2px 10px;
            font-size: 12px;
            cursor: pointer;
            height: 100%;
            display: flex;
            align-items: center;
        }

        .menu-item:hover {
            background: linear-gradient(180deg, #316ac5 0%, #1a5499 100%);
            color: #fff;
        }

        .window-content {
            background: #ece9d8;
            padding: 8px;
            overflow: auto;
        }

        .window-statusbar {
            height: 22px;
            background: linear-gradient(180deg, #fff 0%, #ede7de 100%);
            border-top: 1px solid #bbb;
            display: flex;
            align-items: center;
            padding: 0 6px;
            font-size: 11px;
            color: #444;
        }

        .statusbar-section {
            padding: 0 8px;
            border-right: 1px solid #bbb;
            height: 14px;
            display: flex;
            align-items: center;
        }

        /* ===== XP-STYLE BUTTONS ===== */
        .btn-xp {
            padding: 6px 20px;
            background: linear-gradient(180deg, #f0f0f0 0%, #d8d8d8 50%, #c0c0c0 100%);
            border: 2px solid;
            border-color: #fff #707070 #707070 #fff;
            border-radius: 3px;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            font-size: 12px;
            cursor: pointer;
            box-shadow: 1px 1px 3px rgba(0,0,0,0.2);
            outline: none;
        }

        .btn-xp:hover {
            background: linear-gradient(180deg, #fff 0%, #e8e8e8 50%, #d0d0d0 100%);
        }

        .btn-xp:active {
            border-color: #707070 #fff #fff #707070;
            background: linear-gradient(180deg, #c0c0c0 0%, #d8d8d8 50%, #f0f0f0 100%);
            padding: 7px 19px 5px 21px;
        }

        .btn-xp:focus {
            outline: 1px dotted #000;
            outline-offset: -4px;
        }

        .btn-xp-primary {
            padding: 8px 24px;
            background: linear-gradient(180deg, #3a6ea5 0%, #245edc 50%, #1a4db8 100%);
            border-color: #a8c8e8 #1e3a6e #1e3a6e #a8c8e8;
            color: #fff;
            font-weight: 700;
            font-size: 13px;
        }

        .btn-xp-primary:hover {
            background: linear-gradient(180deg, #4a7eb5 0%, #346eec 50%, #2a5dc8 100%);
        }

        .btn-xp-primary:active {
            border-color: #1e3a6e #a8c8e8 #a8c8e8 #1e3a6e;
            background: linear-gradient(180deg, #1a4db8 0%, #245edc 50%, #3a6ea5 100%);
        }

        .btn-xp-green {
            background: linear-gradient(180deg, #90ee90 0%, #6bce6b 50%, #4db84d 100%);
            border-color: #c8f0c8 #2d8a2d #2d8a2d #c8f0c8;
        }

        .btn-xp-red {
            background: linear-gradient(180deg, #ff6b6b 0%, #ee4444 50%, #cc2222 100%);
            border-color: #ffaaaa #992222 #992222 #ffaaaa;
            color: #fff;
        }

        .btn-xp-orange {
            background: linear-gradient(180deg, #ffd080 0%, #f5b830 50%, #d9a020 100%);
            border-color: #ffe8b0 #b07800 #b07800 #ffe8b0;
        }

        /* ===== APP PANEL (inside window content) ===== */
        .app-panel {
            background: #ece9d8;
            border: 2px solid;
            border-color: #808080 #fff #fff #808080;
            padding: 16px;
        }

        .app-section {
            background: #f4f4f4;
            border: 2px solid;
            border-color: #808080 #fff #fff #808080;
            padding: 12px;
            margin-bottom: 14px;
        }

        .app-section-title {
            font-size: 12px;
            font-weight: 700;
            color: #1a4db8;
            margin-bottom: 10px;
            padding-bottom: 4px;
            border-bottom: 1px solid #ccc;
        }

        .btn-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .app-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px 14px;
            min-width: 90px;
            gap: 6px;
            background: linear-gradient(180deg, #f0f0f0 0%, #d8d8d8 50%, #c0c0c0 100%);
            border: 2px solid;
            border-color: #fff #707070 #707070 #fff;
            border-radius: 4px;
            cursor: pointer;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            font-size: 11px;
            font-weight: 700;
            color: #000;
            box-shadow: 1px 1px 3px rgba(0,0,0,0.2);
            text-align: center;
        }

        .app-btn:hover {
            background: linear-gradient(180deg, #e0eaff 0%, #c0d8f5 50%, #a0c0e0 100%);
        }

        .app-btn:active {
            border-color: #707070 #fff #fff #707070;
            background: linear-gradient(180deg, #c0c0c0 0%, #d8d8d8 50%, #f0f0f0 100%);
            box-shadow: none;
        }

        .app-btn img {
            width: 32px;
            height: 32px;
        }

        .app-btn-small {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            min-width: auto;
            font-size: 12px;
        }

        .app-btn-small img {
            width: 18px;
            height: 18px;
        }

        .status-bar-text {
            font-size: 12px;
            color: #333;
            padding: 4px 0;
        }

        /* ===== NOTIFICATION TOAST ===== */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(180deg, #fffadc 0%, #ffe870 100%);
            border: 2px solid;
            border-color: #fff #b8a000 #b8a000 #fff;
            padding: 10px 16px;
            font-size: 12px;
            box-shadow: 2px 2px 8px rgba(0,0,0,0.3);
            z-index: 999;
            display: none;
            max-width: 280px;
        }

        .toast.show {
            display: block;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { transform: translateX(300px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .toast-title {
            font-weight: 700;
            margin-bottom: 4px;
            color: #000;
        }

        /* ===== CLOCK WIDGET ===== */
        .clock-widget {
            display: flex;
            align-items: center;
            gap: 4px;
            color: rgba(255,255,255,0.8);
            font-size: 11px;
        }

        .resizer {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 20px;
            height: 20px;
            cursor: nw-resize;
            z-index: 60;
            background: linear-gradient(135deg, transparent 50%, #c0c0c0 50%);
        }

        .resizer:hover {
            background: linear-gradient(135deg, transparent 50%, #808080 50%);
        }
    </style>
</head>
<body>

<?php if ($accessDenied): ?>
<!-- ACCESS DENIED SCREEN -->
<div class="login-screen">
    <div class="login-box" style="width:400px;text-align:center;">
        <div class="login-header" style="background: linear-gradient(180deg, #cc0000 0%, #991b1b 50%, #7f1d1d 100%);">
            <div class="login-header-content">
                <svg class="login-header-icon" viewBox="0 0 32 32">
                    <circle cx="16" cy="16" r="12" fill="#fff" stroke="#cc0000" stroke-width="2"/>
                    <rect x="14" y="8" width="4" height="10" fill="#cc0000"/>
                    <circle cx="16" cy="22" r="3" fill="#cc0000"/>
                </svg>
                <span class="login-title" style="color:#fff;">Access Denied</span>
            </div>
        </div>
        <div class="login-body" style="text-align:center;">
            <svg viewBox="0 0 80 80" style="width:80px;height:80px;margin-bottom:16px;">
                <circle cx="40" cy="40" r="36" fill="#fee2e2" stroke="#cc0000" stroke-width="2"/>
                <rect x="35" y="20" width="10" height="28" rx="5" fill="#cc0000"/>
                <circle cx="40" cy="56" r="5" fill="#cc0000"/>
            </svg>
            <h2 style="color:#991b1b;margin-bottom:8px;">Akses Ditolak!</h2>
            <p style="color:#666;margin-bottom:16px;line-height:1.6;">
                Anda tidak memiliki izin untuk mengakses Brothers Software.<br>
                Hanya <strong>Employee</strong> dan <strong>Admin</strong> yang dapat mengakses sistem ini.
            </p>
            <div style="padding:12px;background:#fef2f2;border:2px solid #cc0000;border-radius:8px;margin-bottom:16px;">
                <small style="color:#991b1b;">
                    <strong>Role Anda saat ini:</strong> <?php echo htmlspecialchars(ucfirst($currentRole)); ?><br>
                    <strong>Username:</strong> <?php echo htmlspecialchars($_SESSION['username'] ?? 'Unknown'); ?>
                </small>
            </div>
            <a href="../index.php" class="btn-xp btn-xp-primary" style="text-decoration:none;display:inline-block;padding:10px 24px;">
                ← Kembali ke Beranda
            </a>
        </div>
        <div class="login-footer">
            Brothers Company - Brothers Desktop v1.0
        </div>
    </div>
</div>
<?php else: ?>

<div class="desktop" id="desktop">
    <!-- Toast Notification -->
    <div class="toast" id="toast">
        <div class="toast-title" id="toast-title">MechanicApp</div>
        <div class="toast-body" id="toast-body">Pesan notifikasi</div>
    </div>

    <!-- Desktop Icons -->
    <div class="desktop-icons">
        <!-- CargoApp -->
        <div class="desktop-icon" ondblclick="openWindow('cargo')">
            <svg class="icon-img" viewBox="0 0 48 48">
                <rect x="6" y="14" width="36" height="26" rx="2" fill="#8b5cf6" stroke="#5b21b6" stroke-width="2"/>
                <rect x="10" y="18" width="28" height="18" fill="#c4b5fd"/>
                <rect x="14" y="22" width="20" height="10" fill="#7c3aed"/>
                <rect x="18" y="8" width="12" height="6" rx="1" fill="#a78bfa"/>
                <circle cx="24" cy="4" r="3" fill="#ddd"/>
                <rect x="10" y="36" width="8" height="6" fill="#333"/>
                <rect x="30" y="36" width="8" height="6" fill="#333"/>
            </svg>
            <span class="icon-label">CargoApp</span>
        </div>

        <!-- FarmerApp -->
        <div class="desktop-icon" ondblclick="openWindow('farmer')">
            <svg class="icon-img" viewBox="0 0 48 48">
                <rect x="4" y="30" width="40" height="14" rx="2" fill="#8b4513" stroke="#5d3a1a"/>
                <rect x="8" y="32" width="8" height="10" fill="#228b22"/>
                <rect x="20" y="32" width="8" height="10" fill="#32cd32"/>
                <rect x="32" y="32" width="8" height="10" fill="#228b22"/>
                <circle cx="12" cy="26" r="6" fill="#90ee90" stroke="#228b22"/>
                <circle cx="24" cy="22" r="6" fill="#90ee90" stroke="#228b22"/>
                <circle cx="36" cy="26" r="6" fill="#90ee90" stroke="#228b22"/>
                <path d="M24 4 L24 16" stroke="#8b4513" stroke-width="2"/>
                <circle cx="24" cy="4" r="3" fill="#ffd700"/>
            </svg>
            <span class="icon-label">FarmerApp</span>
        </div>

        <!-- MechanicApp -->
        <div class="desktop-icon" ondblclick="openWindow('mechanic')">
            <svg class="icon-img" viewBox="0 0 48 48">
                <rect x="4" y="8" width="40" height="32" rx="2" fill="#3a6ea5" stroke="#1e4d7a" stroke-width="2"/>
                <rect x="8" y="12" width="32" height="20" fill="#c8e0f5"/>
                <circle cx="14" cy="22" r="5" fill="#f0f0f0" stroke="#707070" stroke-width="1"/>
                <circle cx="34" cy="22" r="5" fill="#f0f0f0" stroke="#707070" stroke-width="1"/>
                <rect x="16" y="34" width="16" height="4" rx="2" fill="#245edc"/>
                <circle cx="14" cy="22" r="2" fill="#2d8a2d"/>
                <circle cx="34" cy="22" r="2" fill="#cc2222"/>
            </svg>
            <span class="icon-label">MechanicApp</span>
        </div>

        <!-- RestauranApp -->
        <div class="desktop-icon" ondblclick="openWindow('restaurant')">
            <svg class="icon-img" viewBox="0 0 48 48">
                <ellipse cx="24" cy="38" rx="18" ry="6" fill="#8b4513" stroke="#5d3a1a"/>
                <rect x="8" y="32" width="32" height="8" fill="#f5f5f5"/>
                <ellipse cx="24" cy="32" rx="16" ry="4" fill="#fff"/>
                <path d="M12 32 Q12 20 24 20 Q36 20 36 32" fill="#cc2222" stroke="#991b1b"/>
                <rect x="20" y="12" width="8" height="8" rx="2" fill="#ffd700"/>
                <path d="M6 30 Q6 24 12 24" stroke="#8b4513" stroke-width="3" fill="none"/>
                <path d="M42 30 Q42 24 36 24" stroke="#8b4513" stroke-width="3" fill="none"/>
            </svg>
            <span class="icon-label">RestauranApp</span>
        </div>

        <!-- Admin Apps - Only visible for Admin -->
        <!-- Daftar Pekerja -->
        <div class="desktop-icon admin-only-icon" ondblclick="openWindow('daftarpekerja')" style="display:none;">
            <svg class="icon-img" viewBox="0 0 48 48">
                <rect x="4" y="6" width="40" height="36" rx="2" fill="#3a6ea5" stroke="#1e4d7a" stroke-width="2"/>
                <rect x="8" y="10" width="32" height="6" fill="#1e4d7a"/>
                <circle cx="14" cy="28" r="6" fill="#f5c890"/>
                <ellipse cx="14" cy="38" rx="8" ry="4" fill="#245edc"/>
                <circle cx="28" cy="26" r="5" fill="#f5c890"/>
                <ellipse cx="28" cy="34" rx="6" ry="3" fill="#245edc"/>
                <circle cx="38" cy="28" r="4" fill="#f5c890"/>
                <ellipse cx="38" cy="35" rx="5" ry="3" fill="#245edc"/>
            </svg>
            <span class="icon-label">Daftar<br>Pekerja</span>
        </div>

        <!-- Tambah Pekerja -->
        <div class="desktop-icon admin-only-icon" ondblclick="openWindow('tambahpekerja')" style="display:none;">
            <svg class="icon-img" viewBox="0 0 48 48">
                <rect x="4" y="6" width="40" height="36" rx="2" fill="#238636" stroke="#1a5c1a" stroke-width="2"/>
                <rect x="8" y="10" width="32" height="6" fill="#1a5c1a"/>
                <circle cx="24" cy="28" r="10" fill="#f5c890" stroke="#c8a060" stroke-width="2"/>
                <path d="M24 22 L24 34" stroke="#333" stroke-width="3" stroke-linecap="round"/>
                <path d="M18 28 L30 28" stroke="#333" stroke-width="3" stroke-linecap="round"/>
            </svg>
            <span class="icon-label">Tambah<br>Pekerja</span>
        </div>

        <!-- Laporan Kerja -->
        <div class="desktop-icon admin-only-icon" ondblclick="openWindow('laporan kerja')" style="display:none;">
            <svg class="icon-img" viewBox="0 0 48 48">
                <rect x="6" y="4" width="36" height="40" rx="2" fill="#ffd700" stroke="#b07800" stroke-width="2"/>
                <rect x="6" y="4" width="36" height="10" rx="2" fill="#e8b828"/>
                <rect x="10" y="18" width="28" height="4" fill="#8b6914"/>
                <rect x="10" y="24" width="20" height="4" fill="#8b6914"/>
                <rect x="10" y="30" width="24" height="4" fill="#8b6914"/>
                <rect x="10" y="36" width="16" height="4" fill="#8b6914"/>
                <rect x="10" y="42" width="28" height="4" fill="#cc2222"/>
            </svg>
            <span class="icon-label">Laporan<br>Kerja</span>
        </div>

        <!-- Keuangan Company -->
        <div class="desktop-icon admin-only-icon" ondblclick="openWindow('keuangan')" style="display:none;">
            <svg class="icon-img" viewBox="0 0 48 48">
                <rect x="4" y="8" width="40" height="32" rx="2" fill="#4db84d" stroke="#2d8a2d" stroke-width="2"/>
                <circle cx="24" cy="24" r="14" fill="#fff" stroke="#2d8a2d" stroke-width="2"/>
                <text x="24" y="28" text-anchor="middle" font-size="14" font-weight="bold" fill="#2d8a2d">$</text>
                <rect x="18" y="36" width="12" height="4" fill="#2d8a2d"/>
            </svg>
            <span class="icon-label">Keuangan<br>Company</span>
        </div>
    </div>

    <!-- Windows -->
    <div class="window" id="win-mechanic" style="top:30px;left:60px;width:680px;height:480px;display:none;">
        <div class="window-titlebar" onmousedown="startDrag(event, 'win-mechanic')">
            <svg class="window-icon" viewBox="0 0 16 16">
                <rect x="1" y="3" width="14" height="11" rx="1" fill="#3a6ea5" stroke="#1e4d7a" stroke-width="1"/>
                <rect x="3" y="5" width="10" height="6" fill="#c8e0f5"/>
                <rect x="5" y="11" width="6" height="2" rx="1" fill="#245edc"/>
            </svg>
            <span class="window-title">MechanicApp - Brothers Company</span>
            <div class="window-controls">
                <button class="window-btn window-btn-max" onclick="minimizeWindow('win-mechanic')">_</button>
                <button class="window-btn window-btn-max" onclick="maximizeWindow('win-mechanic')">□</button>
                <button class="window-btn" onclick="closeWindow('win-mechanic')">✕</button>
            </div>
        </div>
        <div class="window-menubar">
            <span class="menu-item">File</span>
            <span class="menu-item">Edit</span>
            <span class="menu-item">View</span>
            <span class="menu-item">Tools</span>
            <span class="menu-item">Help</span>
        </div>
        <div class="window-content">
            <div class="app-panel">
                <!-- MechanicApp Header -->
                <div class="app-section">
                    <div class="app-section-title">⚙️ MECHANIC APP - Brothers Company</div>
                    <div class="btn-grid" style="justify-content:center;">
                        <!-- Calculator -->
                        <button class="app-btn" onclick="openWindow('calculator')" style="min-width:120px;padding:16px;">
                            <svg viewBox="0 0 48 48">
                                <rect x="6" y="4" width="36" height="40" rx="3" fill="#c0c0c0" stroke="#707070" stroke-width="2"/>
                                <rect x="10" y="8" width="28" height="12" rx="2" fill="#98fb98"/>
                                <rect x="12" y="10" width="24" height="8" fill="#fff"/>
                                <rect x="12" y="24" width="6" height="6" rx="1" fill="#ffd700"/>
                                <rect x="21" y="24" width="6" height="6" rx="1" fill="#ffd700"/>
                                <rect x="30" y="24" width="6" height="6" rx="1" fill="#ffd700"/>
                                <rect x="12" y="32" width="6" height="6" rx="1" fill="#ffd700"/>
                                <rect x="21" y="32" width="6" height="6" rx="1" fill="#ffd700"/>
                                <rect x="30" y="32" width="6" height="6" rx="1" fill="#4db84d"/>
                                <rect x="12" y="40" width="15" height="4" rx="1" fill="#707070"/>
                                <rect x="30" y="40" width="6" height="4" rx="1" fill="#cc2222"/>
                            </svg>
                            Calculator
                        </button>
                        <!-- LaporanApp -->
                        <button class="app-btn" onclick="openWindow('laporan')" style="min-width:120px;padding:16px;">
                            <svg viewBox="0 0 48 48">
                                <rect x="6" y="4" width="36" height="40" rx="2" fill="#ffd700" stroke="#b07800" stroke-width="2"/>
                                <rect x="6" y="4" width="36" height="10" rx="2" fill="#e8b828"/>
                                <text x="24" y="12" text-anchor="middle" font-size="8" fill="#8b4513" font-weight="bold">LAP</text>
                                <rect x="12" y="18" width="24" height="3" fill="#c8a000"/>
                                <rect x="12" y="24" width="18" height="3" fill="#c8a000"/>
                                <rect x="12" y="30" width="20" height="3" fill="#c8a000"/>
                                <rect x="12" y="36" width="14" height="3" fill="#c8a000"/>
                                <rect x="12" y="42" width="24" height="4" fill="#cc2222"/>
                            </svg>
                            LaporanApp
                        </button>
                        <!-- Mecharing -->
                        <button class="app-btn" onclick="showToast('Mecharing', 'Membuka Mecharing...')" style="min-width:120px;padding:16px;">
                            <svg viewBox="0 0 48 48">
                                <circle cx="24" cy="24" r="18" fill="#245edc" stroke="#1038a0" stroke-width="2"/>
                                <circle cx="24" cy="24" r="12" fill="#fff"/>
                                <circle cx="24" cy="24" r="8" fill="#c8e0f5"/>
                                <circle cx="24" cy="24" r="3" fill="#245edc"/>
                                <line x1="24" y1="24" x2="24" y2="14" stroke="#333" stroke-width="2" stroke-linecap="round"/>
                                <line x1="24" y1="24" x2="32" y2="28" stroke="#cc2222" stroke-width="2" stroke-linecap="round"/>
                                <circle cx="24" cy="4" r="2" fill="#ffd700"/>
                                <circle cx="24" cy="44" r="2" fill="#ffd700"/>
                                <circle cx="4" cy="24" r="2" fill="#ffd700"/>
                                <circle cx="44" cy="24" r="2" fill="#ffd700"/>
                            </svg>
                            Mecharing
                        </button>
                        <!-- MemberApp -->
                        <button class="app-btn" onclick="openWindow('member')" style="min-width:120px;padding:16px;">
                            <svg viewBox="0 0 48 48">
                                <circle cx="24" cy="14" r="10" fill="#f5c890" stroke="#c8a060" stroke-width="2"/>
                                <ellipse cx="24" cy="40" rx="14" ry="8" fill="#3a6ea5" stroke="#1e4d7a" stroke-width="2"/>
                                <circle cx="24" cy="14" r="6" fill="#f5c890"/>
                                <path d="M18 12 Q24 18 30 12" stroke="#333" stroke-width="1.5" fill="none"/>
                                <circle cx="20" cy="12" r="2" fill="#333"/>
                                <circle cx="28" cy="12" r="2" fill="#333"/>
                                <path d="M10 30 Q24 24 38 30" stroke="#1e4d7a" stroke-width="2" fill="none"/>
                            </svg>
                            MemberApp
                        </button>

                        <!-- AdminPanel - Only for Admin -->
                        <button class="app-btn" onclick="openWindow('adminpanel')" id="btn-adminpanel" style="min-width:120px;padding:16px;display:none;">
                            <svg viewBox="0 0 48 48">
                                <rect x="4" y="8" width="40" height="32" rx="3" fill="#cc2222" stroke="#991b1b" stroke-width="2"/>
                                <rect x="8" y="12" width="32" height="8" fill="#ff6b6b"/>
                                <rect x="8" y="24" width="12" height="12" fill="#fff"/>
                                <rect x="28" y="24" width="12" height="12" fill="#fff"/>
                                <circle cx="14" cy="30" r="4" fill="#cc2222"/>
                                <circle cx="34" cy="30" r="4" fill="#cc2222"/>
                                <rect x="10" y="28" width="8" height="4" fill="#cc2222"/>
                                <rect x="30" y="28" width="8" height="4" fill="#cc2222"/>
                            </svg>
                            AdminPanel
                        </button>
                    </div>
                </div>

                <!-- Info Section -->
                <div class="app-section">
                    <div class="app-section-title">📋 INFORMASI</div>
                    <p class="status-bar-text">
                        MechanicApp menyediakan akses ke Calculator untuk perhitungan, LaporanApp untuk laporan kerja, Mecharing untuk mechanic recording, dan MemberApp untuk manajemen member.
                    </p>
                </div>
            </div>
        </div>
        <div class="window-statusbar">
            <span class="statusbar-section">Ready</span>
            <span class="statusbar-section" id="status-msg">Selamat datang di Brothers Software</span>
        </div>
        <div class="resizer" onmousedown="startResize(event, 'win-mechanic')"></div>
    </div>

    <!-- CALCULATOR APP WINDOW -->
    <div class="window" id="win-calculator" style="top:50px;left:100px;width:900px;height:600px;display:none;">
        <div class="window-titlebar" onmousedown="startDrag(event, 'win-calculator')">
            <svg class="window-icon" viewBox="0 0 16 16">
                <rect x="2" y="2" width="12" height="12" rx="1" fill="#c0c0c0" stroke="#707070"/>
                <rect x="4" y="4" width="8" height="4" rx="1" fill="#98fb98"/>
                <rect x="4" y="10" width="2" height="2" fill="#ffd700"/>
                <rect x="7" y="10" width="2" height="2" fill="#ffd700"/>
                <rect x="10" y="10" width="2" height="2" fill="#4db84d"/>
            </svg>
            <span class="window-title">Calculator - AutoJuice</span>
            <div class="window-controls">
                <button class="window-btn window-btn-max" onclick="minimizeWindow('win-calculator')">_</button>
                <button class="window-btn window-btn-max" onclick="maximizeWindow('win-calculator')">□</button>
                <button class="window-btn" onclick="closeWindow('win-calculator')">✕</button>
            </div>
        </div>
        <div class="window-menubar">
            <span class="menu-item">File</span>
            <span class="menu-item">Edit</span>
            <span class="menu-item">View</span>
            <span class="menu-item">Help</span>
        </div>
        <div class="window-content" id="win-calculator-content" style="padding:0;background:#0d1117;overflow:auto;height:calc(100% - 48px);">
            <!-- Calculator App - Built In -->
            <div id="calc-app" style="width:100%;height:100%;background:#0d1117;padding:10px;box-sizing:border-box;font-family:'Segoe UI',sans-serif;color:#c9d1d9;overflow:auto;">
                <style>
                    .calc-app-wrapper {
                        display: flex;
                        gap: 15px;
                        align-items: flex-start;
                        justify-content: center;
                        width: 100%;
                        min-height: calc(100% - 60px);
                        padding: 5px;
                    }
                    .calc-card-standalone {
                        background: #161b22;
                        padding: 15px;
                        padding-bottom: 70px;
                        border-radius: 12px;
                        width: 280px;
                        border: 1px solid #30363d;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                        flex-shrink: 0;
                        position: relative;
                    }
                    .calc-card-standalone h2 {
                        text-align: center;
                        color: #58a6ff;
                        margin: 0 0 15px 0;
                        font-size: 18px;
                    }
                    .calc-card-standalone label {
                        font-size: 10px;
                        font-weight: bold;
                        color: #8b949e;
                        text-transform: uppercase;
                        display: block;
                        margin-bottom: 5px;
                    }
                    .calc-card-standalone select,
                    .calc-card-standalone input {
                        width: 100%;
                        padding: 8px;
                        margin-bottom: 10px;
                        border-radius: 6px;
                        border: 1px solid #30363d;
                        background: #010409;
                        color: #fff;
                        font-size: 12px;
                        box-sizing: border-box;
                    }
                    .calc-card-standalone .btn {
                        width: 100%;
                        padding: 8px;
                        border-radius: 6px;
                        border: none;
                        font-weight: bold;
                        font-size: 11px;
                        cursor: pointer;
                        margin-bottom: 8px;
                    }
                    .btn-calc-main { background: #58a6ff; color: #fff; }
                    .btn-log-main { background: #238636; color: #fff; }
                    #calc-hasil { font-size: 20px; font-weight: bold; text-align: center; color: #58a6ff; margin-top: 10px; }
                    .calc-receipt-container {
                        flex: 1;
                        display: none;
                        flex-wrap: wrap;
                        gap: 10px;
                        align-items: flex-start;
                        width: auto;
                        max-width: 400px;
                        min-width: 200px;
                        overflow: auto;
                        padding: 5px;
                    }
                    .calc-receipt-container.visible { display: flex; }
                    .calc-receipt {
                        background: #fff;
                        color: #000;
                        padding: 15px;
                        width: 260px;
                        font-family: 'Courier New', monospace;
                        font-size: 10px;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                    }
                    .calc-receipt-header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px; }
                    .calc-receipt-header strong { font-size: 14px; }
                    .calc-item-block { margin-bottom: 12px; border-bottom: 1px dashed #000; padding-bottom: 10px; }
                    .calc-item-main { display: flex; justify-content: space-between; align-items: center; font-weight: bold; }
                    .calc-item-detail { font-size: 9px; color: #000; padding-left: 24px; margin-top: 5px; font-weight: 600; }
                    .calc-divider { border-top: 2px solid #000; margin: 10px 0; }
                    .calc-row { display: flex; justify-content: space-between; margin-bottom: 4px; font-size: 10px; }
                    .calc-row.jasa { color: #1a7f37; font-weight: bold; }
                    .calc-row.vault { color: #005cc5; font-style: italic; }
                    .calc-total { font-size: 16px; font-weight: 900; border-top: 2px solid #000; margin-top: 10px; padding-top: 8px; }
                    .calc-compliment { margin-top: 15px; padding: 8px; border: 2px solid #000; text-align: center; font-weight: bold; background: #f9f9f9; font-size: 9px; }
                    .calc-del-btn { color: #f85149; cursor: pointer; font-size: 16px; border: none; background: none; padding: 0; font-weight: bold; }
                    .calc-floating {
                        position: absolute;
                        bottom: 8px;
                        left: 50%;
                        transform: translateX(-50%);
                        display: flex;
                        flex-wrap: nowrap;
                        gap: 5px;
                        z-index: 100;
                    }
                    .calc-floating .btn-sm {
                        padding: 5px 10px;
                        font-size: 9px;
                        border-radius: 4px;
                        border: none;
                        cursor: pointer;
                        font-weight: bold;
                    }
                    .calc-btn-show { background: #1f6feb; color: white; }
                    .calc-btn-reset { background: #21262d; color: #f85149; border: 1px solid #f85149; }
                    .calc-btn-download { background: #ffd700; color: #000; }
                    /* Price Config */
                    .calc-config-btn {
                        position: absolute;
                        top: 8px;
                        right: 8px;
                        padding: 4px 8px;
                        font-size: 9px;
                        border-radius: 4px;
                        border: none;
                        cursor: pointer;
                        background: #30363d;
                        color: #fff;
                    }
                    .calc-config-panel {
                        display: none;
                        background: #161b22;
                        border: 1px solid #30363d;
                        border-radius: 8px;
                        padding: 15px;
                        margin-top: 10px;
                    }
                    .calc-config-panel.visible { display: block; }
                    .calc-config-panel h3 { color: #58a6ff; margin: 0 0 10px 0; font-size: 14px; }
                    .calc-config-row { display: flex; align-items: center; margin-bottom: 8px; }
                    .calc-config-row label { flex: 1; font-size: 11px; color: #8b949e; }
                    .calc-config-row input { width: 80px; padding: 5px; border-radius: 4px; border: 1px solid #30363d; background: #010409; color: #fff; font-size: 11px; text-align: center; }
                    .calc-config-row span { width: 30px; text-align: center; font-size: 10px; }
                </style>

                <div class="calc-app-wrapper">
                    <div class="calc-card-standalone" style="position:relative;">
                        <button class="calc-config-btn" onclick="toggleCalcConfig()">⚙️ Config</button>
                        <h2>AUTOJUICE</h2>
                        <label>Layanan</label>
                        <select id="calc-service-type" onchange="resetCalcInputs()">
                            <option value="">-- Pilih --</option>
                            <option value="repair">Mechanical Repair</option>
                            <option value="modif">Custom Modification</option>
                            <option value="brother">Brotherhood Disc</option>
                            <option value="ws_stored">WS Stored</option>
                        </select>
                        <div id="calc-input-container"></div>
                        <button class="btn btn-calc-main" onclick="runCalcCalculation()">Hitung Harga</button>
                        <button class="btn btn-log-main" onclick="logCalcToReceipt()">Simpan ke Struk</button>
                        <div id="calc-hasil">$0</div>

                        <!-- Price Config Panel -->
                        <div id="calc-config-panel" class="calc-config-panel">
                            <h3>⚙️ Price Config</h3>
                            <div class="calc-config-row">
                                <label>Repair Multiplier:</label>
                                <input type="number" id="config-repair" value="3.0" step="0.1" onchange="updateCalcConfig()">
                            </div>
                            <div class="calc-config-row">
                                <label>Modif Multiplier:</label>
                                <input type="number" id="config-modif" value="3.0" step="0.1" onchange="updateCalcConfig()">
                            </div>
                            <div class="calc-config-row">
                                <label>Brother Multiplier:</label>
                                <input type="number" id="config-brother" value="2.3" step="0.1" onchange="updateCalcConfig()">
                            </div>
                            <div class="calc-config-row">
                                <label>WS Stored Multiplier:</label>
                                <input type="number" id="config-ws_stored" value="2.0" step="0.1" onchange="updateCalcConfig()">
                            </div>
                            <div class="calc-config-row">
                                <label>Sparepart Price/Unit:</label>
                                <input type="number" id="config-sparepart" value="2.0" step="0.1" onchange="updateCalcConfig()">
                            </div>
                        </div>

                        <!-- Floating Controls -->
                        <div class="calc-floating">
                            <button class="btn-sm calc-btn-show" onclick="toggleCalcReceipt()">Show/Hide</button>
                            <button class="btn-sm calc-btn-reset" onclick="clearCalcData()">Reset</button>
                            <button class="btn-sm calc-btn-download" onclick="downloadCalcReceipt()">Download</button>
                        </div>
                    </div>

                    <div id="calc-receipt-container" class="calc-receipt-container">
                        <div id="calc-receipt-wrap" class="calc-receipt">
                            <div class="calc-receipt-header">
                                <strong style="font-size:14px;">AUTOJUICE WORKSHOP</strong><br>
                                <span style="font-size:9px;" id="calc-r-date"></span>
                            </div>
                            <div id="calc-receipt-items"></div>
                            <div class="calc-divider"></div>
                            <div class="calc-row"><span>Total Akumulasi Compo:</span><span id="calc-final-compo">0</span></div>
                            <div class="calc-row vault"><span>Harga Suku Cadang:</span><span id="calc-final-vault">$0.00</span></div>
                            <div class="calc-row jasa"><span>Jasa Mekanik:</span><span id="calc-final-jasa">$0.00</span></div>
                            <div class="calc-row calc-total"><span>GRAND TOTAL</span><span id="calc-final-bill">$0.00</span></div>
                            <div id="calc-compliment-section" class="calc-compliment" style="display:none;">
                                <div style="font-size:8px;text-decoration:underline;">FREE COMPLIMENT</div>
                                <div id="calc-compliment-text" style="font-size:10px;text-transform:uppercase;">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
            // Calculator Standalone State
            const calcConfig = {
                multipliers: {
                    repair: 3.0,
                    modif: 3.0,
                    brother: 2.3,
                    ws_stored: 2.0
                },
                sparepartPricePerUnit: 2.0,
                complimentRules: [
                    { threshold: 5000, gift: "Makanan dari Restoran" },
                    { threshold: 1000, gift: "Snack + Air Minum" },
                    { threshold: 150, gift: "Snack" },
                    { threshold: 0, gift: "Air Minum" }
                ]
            };

            let calcState = { bill: 0, vault: 0, compo: 0, hasWS: false };
            let calcCurrent = { bill: 0, compo: 0 };

            // Initialize date
            document.getElementById('calc-r-date').innerText = "Tgl: " + new Date().toLocaleDateString('id-ID');

            // Toggle Config Panel
            function toggleCalcConfig() {
                const panel = document.getElementById('calc-config-panel');
                panel.classList.toggle('visible');
            }

            // Update Config from inputs
            function updateCalcConfig() {
                calcConfig.multipliers.repair = parseFloat(document.getElementById('config-repair').value) || 3.0;
                calcConfig.multipliers.modif = parseFloat(document.getElementById('config-modif').value) || 3.0;
                calcConfig.multipliers.brother = parseFloat(document.getElementById('config-brother').value) || 2.3;
                calcConfig.multipliers.ws_stored = parseFloat(document.getElementById('config-ws_stored').value) || 2.0;
                calcConfig.sparepartPricePerUnit = parseFloat(document.getElementById('config-sparepart').value) || 2.0;
            }

            // Reset inputs based on service type
            function resetCalcInputs() {
                const type = document.getElementById('calc-service-type').value;
                const container = document.getElementById('calc-input-container');
                container.innerHTML = "";

                if (type === "repair") {
                    container.innerHTML = '<input type="number" placeholder="Body Compo" class="in-val-r" style="width:100%;padding:8px;margin-bottom:8px;border-radius:6px;border:1px solid #30363d;background:#010409;color:#fff;font-size:12px;"><input type="number" placeholder="Engine Compo" class="in-val-r" style="width:100%;padding:8px;margin-bottom:8px;border-radius:6px;border:1px solid #30363d;background:#010409;color:#fff;font-size:12px;">';
                } else if (type === "modif" || type === "ws_stored") {
                    const cls = type === "modif" ? "in-val-m" : "in-val-w";
                    addCalcInput(cls, container);
                    container.innerHTML += '<button onclick="addCalcInput(\'' + cls + '\', document.getElementById(\'calc-input-container\'))" style="width:100%;padding:6px;background:transparent;border:1px dashed #484f58;color:#8b949e;cursor:pointer;font-size:10px;border-radius:4px;">+ Tambah Item</button>';
                } else if (type === "brother") {
                    container.innerHTML = '<select id="calc-b-sub" onchange="calcBrotherSub()" style="width:100%;padding:8px;margin-bottom:8px;border-radius:6px;border:1px solid #30363d;background:#010409;color:#fff;font-size:12px;"><option value="">-- Tipe --</option><option value="r">Repair</option><option value="m">Modif</option></select><div id="calc-b-area"></div>';
                }
            }

            function calcBrotherSub() {
                const type = document.getElementById('calc-b-sub').value;
                const area = document.getElementById('calc-b-area');
                area.innerHTML = "";
                if (type === "r") {
                    area.innerHTML = '<input type="number" placeholder="Body (B)" class="in-val-b" style="width:100%;padding:8px;margin-bottom:8px;border-radius:6px;border:1px solid #30363d;background:#010409;color:#fff;font-size:12px;"><input type="number" placeholder="Engine (B)" class="in-val-b" style="width:100%;padding:8px;margin-bottom:8px;border-radius:6px;border:1px solid #30363d;background:#010409;color:#fff;font-size:12px;">';
                } else if (type === "m") {
                    addCalcInput("in-val-b", area);
                    area.innerHTML += '<button onclick="addCalcInput(\'in-val-b\', document.getElementById(\'calc-b-area\'))" style="width:100%;padding:6px;background:transparent;border:1px dashed #484f58;color:#8b949e;cursor:pointer;font-size:10px;border-radius:4px;">+ Item</button>';
                }
            }

            function addCalcInput(cls, container) {
                const input = document.createElement("input");
                input.type = "number";
                input.placeholder = "Jumlah Compo";
                input.className = cls;
                input.style = "width:100%;padding:8px;margin-bottom:8px;border-radius:6px;border:1px solid #30363d;background:#010409;color:#fff;font-size:12px;";
                const btn = container.querySelector('button');
                btn ? container.insertBefore(input, btn) : container.appendChild(input);
            }

            // Run Calculation
            function runCalcCalculation() {
                let bill = 0, compo = 0;
                const mult = calcConfig.multipliers;

                document.querySelectorAll('.in-val-r, .in-val-m, .in-val-w, .in-val-b').forEach(i => {
                    let v = parseFloat(i.value) || 0;
                    if (v < 0) v = 0;
                    compo += v;
                    if (i.classList.contains('in-val-r')) bill += v * mult.repair;
                    else if (i.classList.contains('in-val-m')) bill += v * mult.modif;
                    else if (i.classList.contains('in-val-w')) bill += v * mult.ws_stored;
                    else if (i.classList.contains('in-val-b')) bill += v * mult.brother;
                });

                calcCurrent = { bill, compo };
                document.getElementById('calc-hasil').innerText = "$" + bill.toLocaleString('en-US', {minimumFractionDigits: 2});
            }

            // Log to Receipt
            function logCalcToReceipt() {
                if (calcCurrent.bill <= 0) {
                    alert('Hitung dulu harga!');
                    return;
                }

                const typeValue = document.getElementById('calc-service-type').value;
                let typeLabel = typeValue.toUpperCase().replace('_', ' ');
                if (typeValue === 'repair') typeLabel = "MECHANICAL REPAIR (B&E)";

                const list = document.getElementById('calc-receipt-items');
                const bill = calcCurrent.bill;
                const compo = calcCurrent.compo;
                const vault = compo * calcConfig.sparepartPricePerUnit;
                const jasa = bill - vault;

                const id = Date.now();
                const div = document.createElement("div");
                div.className = "calc-item-block";
                div.id = 'calc-item-' + id;
                div.dataset.type = typeValue;
                div.innerHTML = '<div class="calc-item-main"><div style="display:flex;align-items:center;gap:8px;"><span class="calc-del-btn" onclick="removeCalcLog(' + id + ',' + bill + ',' + vault + ',' + compo + ')">×</span><span>' + typeLabel + '</span></div><span style="white-space:nowrap;">$' + bill.toLocaleString('en-US', {minimumFractionDigits: 2}) + '</span></div><div class="calc-item-detail"><div>• Componen : ' + compo + ' Unit</div><div>• Jasa Mekanik : $' + jasa.toLocaleString('en-US', {minimumFractionDigits: 2}) + '</div></div>';
                list.appendChild(div);

                calcState.bill += bill;
                calcState.vault += vault;
                calcState.compo += compo;
                updateCalcUI();
                document.getElementById('calc-receipt-container').classList.add('visible');
            }

            function removeCalcLog(id, b, v, c) {
                const el = document.getElementById('calc-item-' + id);
                if (el) {
                    el.remove();
                    calcState.bill -= b;
                    calcState.vault -= v;
                    calcState.compo -= c;
                    updateCalcUI();
                }
            }

            function updateCalcUI() {
                const finalJasa = calcState.bill - calcState.vault;
                document.getElementById('calc-final-bill').innerText = "$" + calcState.bill.toLocaleString('en-US', {minimumFractionDigits: 2});
                document.getElementById('calc-final-vault').innerText = "$" + calcState.vault.toLocaleString('en-US', {minimumFractionDigits: 2});
                document.getElementById('calc-final-compo').innerText = calcState.compo;
                document.getElementById('calc-final-jasa').innerText = "$" + finalJasa.toLocaleString('en-US', {minimumFractionDigits: 2});

                const items = document.querySelectorAll('.calc-item-block');
                let hasWS = false;
                items.forEach(item => { if(item.dataset.type === 'ws_stored') hasWS = true; });
                calcState.hasWS = hasWS;

                const compBox = document.getElementById('calc-compliment-section');
                const compText = document.getElementById('calc-compliment-text');

                if (calcState.compo > 0 && !calcState.hasWS) {
                    compBox.style.display = 'block';
                    const rule = calcConfig.complimentRules.find(r => calcState.compo > r.threshold);
                    compText.innerText = rule ? rule.gift : calcConfig.complimentRules[calcConfig.complimentRules.length - 1].gift;
                } else {
                    compBox.style.display = 'none';
                }
            }

            function toggleCalcReceipt() {
                const container = document.getElementById('calc-receipt-container');
                container.classList.toggle('visible');
            }

            function clearCalcData() {
                if (confirm("Reset semua isi struk?")) {
                    calcState = { bill: 0, vault: 0, compo: 0, hasWS: false };
                    document.getElementById('calc-receipt-items').innerHTML = "";
                    updateCalcUI();
                    document.getElementById('calc-hasil').innerText = "$0";
                    document.getElementById('calc-service-type').value = "";
                    document.getElementById('calc-input-container').innerHTML = "";
                }
            }

            function downloadCalcReceipt() {
                if (calcState.bill <= 0) {
                    alert('Tidak ada struk untuk didownload!');
                    return;
                }

                const receipt = document.getElementById('calc-receipt-wrap');
                const delBtns = receipt.querySelectorAll('.calc-del-btn');
                delBtns.forEach(b => b.style.visibility = 'hidden');

                html2canvas(receipt, { backgroundColor: "#ffffff", scale: 3, useCORS: true, scrollY: 0, height: receipt.scrollHeight }).then(canvas => {
                    const link = document.createElement('a');
                    link.download = 'Struk-AutoJuice-' + Date.now() + '.png';
                    link.href = canvas.toDataURL("image/png");
                    link.click();
                    delBtns.forEach(b => b.style.visibility = 'visible');
                });
            }
            </script>
        </div>
        <div class="window-statusbar">
            <span class="statusbar-section">Calculator</span>
            <span class="statusbar-section">AutoJuice Workshop Calculator</span>
        </div>
        <div class="resizer" onmousedown="startResize(event, 'win-calculator')"></div>
    </div>

    <!-- LAPORAN APP WINDOW -->
    <div class="window" id="win-laporan" style="top:60px;left:150px;width:380px;height:320px;display:none;">
        <div class="window-titlebar" onmousedown="startDrag(event, 'win-laporan')">
            <svg class="window-icon" viewBox="0 0 16 16">
                <rect x="2" y="2" width="12" height="12" rx="1" fill="#ffd700" stroke="#b07800"/>
                <rect x="5" y="5" width="6" height="2" fill="#b07800"/>
                <rect x="5" y="8" width="4" height="2" fill="#b07800"/>
                <rect x="5" y="11" width="5" height="2" fill="#b07800"/>
            </svg>
            <span class="window-title">LaporanApp - Brothers Company</span>
            <div class="window-controls">
                <button class="window-btn window-btn-max" onclick="minimizeWindow('win-laporan')">_</button>
                <button class="window-btn window-btn-max" onclick="maximizeWindow('win-laporan')">□</button>
                <button class="window-btn" onclick="closeWindow('win-laporan')">✕</button>
            </div>
        </div>
        <div class="window-menubar">
            <span class="menu-item">File</span>
            <span class="menu-item">Edit</span>
            <span class="menu-item">View</span>
            <span class="menu-item">Help</span>
        </div>
        <div class="window-content" style="padding:0;background:#0d1117;overflow:auto;height:calc(100% - 48px);">
            <div style="width:100%;height:100%;background:#0d1117;padding:15px;box-sizing:border-box;font-family:'Segoe UI',sans-serif;color:#c9d1d9;display:flex;flex-direction:column;">
                <style>
                    .laporan-form-container {
                        width: 100%;
                        max-width: 100%;
                    }
                    .laporan-title {
                        color: #ffd700;
                        font-size: 16px;
                        text-align: center;
                        margin: 0 0 15px 0;
                        padding-bottom: 8px;
                        border-bottom: 1px solid #30363d;
                    }
                    .laporan-field {
                        margin-bottom: 10px;
                    }
                    .laporan-field label {
                        display: block;
                        font-size: 10px;
                        font-weight: bold;
                        color: #8b949e;
                        text-transform: uppercase;
                        margin-bottom: 4px;
                    }
                    .laporan-field input,
                    .laporan-field select {
                        width: 100%;
                        padding: 8px;
                        border-radius: 4px;
                        border: 1px solid #30363d;
                        background: #010409;
                        color: #fff;
                        font-size: 12px;
                        box-sizing: border-box;
                    }
                    .laporan-field input:focus,
                    .laporan-field select:focus {
                        outline: none;
                        border-color: #ffd700;
                    }
                    .laporan-btn-row {
                        display: flex;
                        gap: 8px;
                        margin-top: 10px;
                    }
                    .laporan-btn {
                        flex: 1;
                        padding: 8px;
                        border-radius: 4px;
                        border: none;
                        font-weight: bold;
                        font-size: 11px;
                        cursor: pointer;
                        background: #ffd700;
                        color: #000;
                    }
                    .laporan-btn:hover {
                        background: #e8b828;
                    }
                    .laporan-btn-reset {
                        background: #30363d;
                        color: #fff;
                    }
                    .laporan-btn-reset:hover {
                        background: #484f58;
                    }
                    .laporan-success {
                        display: none;
                        background: #238636;
                        color: #fff;
                        padding: 8px;
                        border-radius: 4px;
                        text-align: center;
                        margin-top: 8px;
                        font-size: 11px;
                    }
                </style>
                <div class="laporan-form-container">
                    <h2 class="laporan-title">📝 Form Laporan Mechanic</h2>
                    <form id="laporan-form" onsubmit="submitLaporan(event)">
                        <div class="laporan-field">
                            <label>Nama Mechanic</label>
                            <input type="text" id="laporan-nama" placeholder="Nama mechanic" required>
                        </div>
                        <div class="laporan-field">
                            <label>Compo Used</label>
                            <input type="number" id="laporan-compo" placeholder="Jumlah komponen" min="0" required>
                        </div>
                        <div class="laporan-field">
                            <label>Money Stored</label>
                            <input type="number" id="laporan-money" placeholder="Jumlah uang (Rp)" min="0" required>
                        </div>
                        <div class="laporan-btn-row">
                            <button type="submit" class="laporan-btn">📤 Kirim</button>
                            <button type="button" class="laporan-btn laporan-btn-reset" onclick="resetLaporan()">🔄 Reset</button>
                        </div>
                    </form>
                    <div id="laporan-success" class="laporan-success">
                        ✅ Laporan berhasil dikirim!
                    </div>
                </div>
            </div>
            <script>
                function submitLaporan(e) {
                    e.preventDefault();
                    const nama = document.getElementById('laporan-nama').value;
                    const compo = document.getElementById('laporan-compo').value;
                    const money = document.getElementById('laporan-money').value;

                    const success = document.getElementById('laporan-success');
                    success.style.display = 'block';
                    success.innerHTML = `✅ Laporan berhasil!<br><strong>${nama}</strong><br>Compo: ${compo} | Rp ${parseInt(money).toLocaleString('id-ID')}`;

                    setTimeout(() => {
                        success.style.display = 'none';
                    }, 3000);

                    console.log('Laporan:', { nama, compo, money });
                }

                function resetLaporan() {
                    document.getElementById('laporan-form').reset();
                    document.getElementById('laporan-success').style.display = 'none';
                }
            </script>
        </div>
        <div class="window-statusbar">
            <span class="statusbar-section">LaporanApp</span>
            <span class="statusbar-section">Form laporan kerja</span>
        </div>
        <div class="resizer" onmousedown="startResize(event, 'win-laporan')"></div>
    </div>

    <!-- MEMBER APP WINDOW -->
    <div class="window" id="win-member" style="top:70px;left:160px;width:380px;height:380px;display:none;">
        <div class="window-titlebar" onmousedown="startDrag(event, 'win-member')">
            <svg class="window-icon" viewBox="0 0 16 16">
                <circle cx="8" cy="5" r="3" fill="#f5c890" stroke="#c8a060"/>
                <ellipse cx="8" cy="13" rx="5" ry="3" fill="#3a6ea5"/>
            </svg>
            <span class="window-title">MemberApp - Brothers Company</span>
            <div class="window-controls">
                <button class="window-btn window-btn-max" onclick="minimizeWindow('win-member')">_</button>
                <button class="window-btn window-btn-max" onclick="maximizeWindow('win-member')">□</button>
                <button class="window-btn" onclick="closeWindow('win-member')">✕</button>
            </div>
        </div>
        <div class="window-menubar">
            <span class="menu-item">File</span>
            <span class="menu-item">Edit</span>
            <span class="menu-item">View</span>
            <span class="menu-item">Help</span>
        </div>
        <div class="window-content" style="padding:0;background:#0d1117;height:calc(100% - 48px);overflow:auto;">
            <div style="width:100%;min-height:100%;background:#0d1117;padding:15px;box-sizing:border-box;font-family:'Segoe UI',sans-serif;color:#c9d1d9;">
                <style>
                    .member-container {
                        width: 100%;
                    }
                    .member-title {
                        color: #58a6ff;
                        font-size: 16px;
                        text-align: center;
                        margin: 0 0 15px 0;
                        padding-bottom: 10px;
                        border-bottom: 1px solid #30363d;
                    }
                    .member-tabs {
                        display: flex;
                        gap: 5px;
                        margin-bottom: 15px;
                    }
                    .member-tab {
                        flex: 1;
                        padding: 8px;
                        border: 1px solid #30363d;
                        background: #21262d;
                        color: #8b949e;
                        border-radius: 4px;
                        cursor: pointer;
                        font-size: 11px;
                        font-weight: bold;
                        text-align: center;
                    }
                    .member-tab.active {
                        background: #58a6ff;
                        color: #fff;
                        border-color: #58a6ff;
                    }
                    .member-tab:hover:not(.active) {
                        background: #30363d;
                    }
                    .member-form {
                        display: none;
                    }
                    .member-form.active {
                        display: block;
                    }
                    .member-field {
                        margin-bottom: 10px;
                    }
                    .member-field label {
                        display: block;
                        font-size: 10px;
                        font-weight: bold;
                        color: #8b949e;
                        text-transform: uppercase;
                        margin-bottom: 4px;
                    }
                    .member-field input {
                        width: 100%;
                        padding: 8px;
                        border-radius: 4px;
                        border: 1px solid #30363d;
                        background: #010409;
                        color: #fff;
                        font-size: 12px;
                        box-sizing: border-box;
                    }
                    .member-field input:focus {
                        outline: none;
                        border-color: #58a6ff;
                    }
                    .member-btn {
                        width: 100%;
                        padding: 10px;
                        border-radius: 4px;
                        border: none;
                        font-weight: bold;
                        font-size: 12px;
                        cursor: pointer;
                        background: #238636;
                        color: #fff;
                        margin-top: 5px;
                    }
                    .member-btn:hover {
                        background: #2ea043;
                    }
                    .member-btn-search {
                        background: #58a6ff;
                    }
                    .member-btn-search:hover {
                        background: #79b8ff;
                    }
                    .member-success {
                        display: none;
                        background: #238636;
                        color: #fff;
                        padding: 8px;
                        border-radius: 4px;
                        text-align: center;
                        margin-top: 10px;
                        font-size: 11px;
                    }
                    .member-search-result {
                        display: none;
                        background: #161b22;
                        border: 1px solid #30363d;
                        border-radius: 4px;
                        padding: 10px;
                        margin-top: 10px;
                    }
                    .member-search-result.show {
                        display: block;
                    }
                    .member-result-item {
                        display: flex;
                        justify-content: space-between;
                        padding: 5px 0;
                        border-bottom: 1px solid #30363d;
                        font-size: 11px;
                    }
                    .member-result-item:last-child {
                        border-bottom: none;
                    }
                    .member-result-label {
                        color: #8b949e;
                    }
                    .member-result-value {
                        color: #fff;
                        font-weight: bold;
                    }
                    .member-not-found {
                        text-align: center;
                        color: #f85149;
                        padding: 20px;
                        font-size: 12px;
                    }
                </style>

                <div class="member-container">
                    <h2 class="member-title">👥 MemberApp</h2>

                    <!-- Tabs -->
                    <div class="member-tabs">
                        <button class="member-tab active" onclick="switchMemberTab('add')">+ Member</button>
                        <button class="member-tab" onclick="switchMemberTab('search')">🔍 Cari Member</button>
                    </div>

                    <!-- Add Member Form -->
                    <div id="member-add-form" class="member-form active">
                        <form onsubmit="submitMember(event)">
                            <div class="member-field">
                                <label>Nama Pelanggan</label>
                                <input type="text" id="member-nama" placeholder="Masukkan nama pelanggan" required>
                            </div>
                            <div class="member-field">
                                <label>Nomor Telepon</label>
                                <input type="tel" id="member-telp" placeholder="08xxxxxxxxxx" required>
                            </div>
                            <button type="submit" class="member-btn">✓ Tambah Member</button>
                        </form>
                        <div id="member-add-success" class="member-success">
                            ✅ Member berhasil ditambahkan!
                        </div>
                    </div>

                    <!-- Search Member Form -->
                    <div id="member-search-form" class="member-form">
                        <form onsubmit="searchMember(event)">
                            <div class="member-field">
                                <label>Nama atau Nomor Telepon</label>
                                <input type="text" id="member-search-input" placeholder="Ketik nama atau nomor telepon">
                            </div>
                            <button type="submit" class="member-btn member-btn-search">🔍 Cari Member</button>
                        </form>
                        <div id="member-search-result" class="member-search-result">
                            <!-- Search results will appear here -->
                        </div>
                    </div>
                </div>

                <script>
                    // Member data storage (simulated - will be replaced with API)
                    let members = [];

                    // Load members from API on init
                    async function loadMembersFromAPI() {
                        try {
                            const response = await fetch('api/member_api.php?action=get_all');
                            const result = await response.json();
                            if (result.success) {
                                members = result.data.map(m => ({
                                    id: m.id,
                                    nama: m.nama,
                                    telp: m.telepon,
                                    tgl: m.tanggal_daftar,
                                    compoUsed: m.compo_used || 0
                                }));
                            }
                        } catch (e) {
                            console.log('Using local data:', e);
                        }
                    }
                    loadMembersFromAPI();

                    function switchMemberTab(tab) {
                        document.querySelectorAll('.member-tab').forEach(t => t.classList.remove('active'));
                        document.querySelectorAll('.member-form').forEach(f => f.classList.remove('active'));

                        if (tab === 'add') {
                            document.querySelector('.member-tab:nth-child(1)').classList.add('active');
                            document.getElementById('member-add-form').classList.add('active');
                        } else {
                            document.querySelector('.member-tab:nth-child(2)').classList.add('active');
                            document.getElementById('member-search-form').classList.add('active');
                        }

                        // Clear results
                        document.getElementById('member-search-result').classList.remove('show');
                        document.getElementById('member-add-success').style.display = 'none';
                    }

                    async function submitMember(e) {
                        e.preventDefault();
                        const nama = document.getElementById('member-nama').value;
                        const telp = document.getElementById('member-telp').value;

                        try {
                            const formData = new FormData();
                            formData.append('action', 'add');
                            formData.append('nama', nama);
                            formData.append('telepon', telp);

                            const response = await fetch('api/member_api.php', {
                                method: 'POST',
                                body: formData
                            });
                            const result = await response.json();

                            if (result.success) {
                                // Add to local array
                                members.push({
                                    id: result.data.id,
                                    nama: result.data.nama,
                                    telp: result.data.telepon,
                                    tgl: result.data.tanggal_daftar,
                                    compoUsed: 0
                                });

                                const success = document.getElementById('member-add-success');
                                success.style.display = 'block';
                                success.innerHTML = `✅ Member ditambahkan!<br><strong>${nama}</strong><br>${telp}`;

                                setTimeout(() => {
                                    success.style.display = 'none';
                                }, 3000);

                                document.getElementById('member-nama').value = '';
                                document.getElementById('member-telp').value = '';
                            } else {
                                alert('Error: ' + result.message);
                            }
                        } catch (e) {
                            // Fallback to local storage if API fails
                            const newMember = {
                                id: Date.now(),
                                nama: nama,
                                telp: telp,
                                tgl: new Date().toISOString().split('T')[0],
                                compoUsed: 0
                            };
                            members.push(newMember);

                            const success = document.getElementById('member-add-success');
                            success.style.display = 'block';
                            success.innerHTML = `✅ Member ditambahkan!<br><strong>${nama}</strong><br>${telp}`;

                            setTimeout(() => {
                                success.style.display = 'none';
                            }, 3000);

                            document.getElementById('member-nama').value = '';
                            document.getElementById('member-telp').value = '';
                        }
                    }

                    async function searchMember(e) {
                        e.preventDefault();
                        const query = document.getElementById('member-search-input').value.toLowerCase().trim();
                        const resultDiv = document.getElementById('member-search-result');

                        if (!query) {
                            resultDiv.classList.remove('show');
                            return;
                        }

                        try {
                            // Search from API
                            const response = await fetch('api/member_api.php?action=search&query=' + encodeURIComponent(query));
                            const result = await response.json();

                            if (result.success && result.data.length > 0) {
                                let html = '';
                                result.data.forEach(m => {
                                    const compoCount = m.compo_used || 0;
                                    html += `
                                        <div class="member-result-item">
                                            <span class="member-result-label">Nama:</span>
                                            <span class="member-result-value">${m.nama}</span>
                                        </div>
                                        <div class="member-result-item">
                                            <span class="member-result-label">Telepon:</span>
                                            <span class="member-result-value">${m.telepon}</span>
                                        </div>
                                        <div class="member-result-item">
                                            <span class="member-result-label">Member Sejak:</span>
                                            <span class="member-result-value">${m.tanggal_daftar}</span>
                                        </div>
                                        <div class="member-result-item" style="border-top: 2px solid #58a6ff; margin-top: 5px; padding-top: 8px;">
                                            <span class="member-result-label">Component Used:</span>
                                            <span class="member-result-value" id="member-compo-count-${m.id}" style="color:#ffd700;font-size:14px;">${compoCount}</span>
                                        </div>
                                        <div style="margin-top:8px; display:flex; gap:5px; align-items:center;">
                                            <input type="number" id="member-compo-add-${m.id}" placeholder="Jumlah" min="1" value="1" style="flex:1; padding:6px; border-radius:4px; border:1px solid #30363d; background:#010409; color:#fff; font-size:11px;">
                                            <button onclick="addMemberCompo(${m.id})" style="padding:6px 12px; background:#238636; color:#fff; border:none; border-radius:4px; font-size:11px; font-weight:bold; cursor:pointer;">
                                                ➕ Tambah
                                            </button>
                                        </div>
                                    `;
                                });
                                resultDiv.innerHTML = html;
                            } else {
                                resultDiv.innerHTML = '<div class="member-not-found">❌ Member tidak ditemukan</div>';
                            }
                        } catch (e) {
                            // Fallback to local search
                            const found = members.filter(m =>
                                m.nama.toLowerCase().includes(query) ||
                                m.telp.includes(query)
                            );

                            if (found.length > 0) {
                                let html = '';
                                found.forEach(m => {
                                    const compoCount = m.compoUsed || 0;
                                    html += `
                                        <div class="member-result-item">
                                            <span class="member-result-label">Nama:</span>
                                            <span class="member-result-value">${m.nama}</span>
                                        </div>
                                        <div class="member-result-item">
                                            <span class="member-result-label">Telepon:</span>
                                            <span class="member-result-value">${m.telp}</span>
                                        </div>
                                        <div class="member-result-item">
                                            <span class="member-result-label">Member Sejak:</span>
                                            <span class="member-result-value">${m.tgl}</span>
                                        </div>
                                        <div class="member-result-item" style="border-top: 2px solid #58a6ff; margin-top: 5px; padding-top: 8px;">
                                            <span class="member-result-label">Component Used:</span>
                                            <span class="member-result-value" id="member-compo-count-${m.id}" style="color:#ffd700;font-size:14px;">${compoCount}</span>
                                        </div>
                                        <div style="margin-top:8px; display:flex; gap:5px; align-items:center;">
                                            <input type="number" id="member-compo-add-${m.id}" placeholder="Jumlah" min="1" value="1" style="flex:1; padding:6px; border-radius:4px; border:1px solid #30363d; background:#010409; color:#fff; font-size:11px;">
                                            <button onclick="addMemberCompo(${m.id})" style="padding:6px 12px; background:#238636; color:#fff; border:none; border-radius:4px; font-size:11px; font-weight:bold; cursor:pointer;">
                                                ➕ Tambah
                                            </button>
                                        </div>
                                    `;
                                });
                                resultDiv.innerHTML = html;
                            } else {
                                resultDiv.innerHTML = '<div class="member-not-found">❌ Member tidak ditemukan</div>';
                            }
                        }

                        resultDiv.classList.add('show');
                    }

                    async function addMemberCompo(memberId) {
                        const inputEl = document.getElementById('member-compo-add-' + memberId);
                        const jumlah = parseInt(inputEl.value) || 0;

                        if (jumlah <= 0) {
                            alert('Masukkan jumlah yang valid!');
                            return;
                        }

                        try {
                            const formData = new FormData();
                            formData.append('action', 'update_compo');
                            formData.append('id', memberId);
                            formData.append('jumlah', jumlah);

                            const response = await fetch('api/member_api.php', {
                                method: 'POST',
                                body: formData
                            });
                            const result = await response.json();

                            if (result.success) {
                                const countEl = document.getElementById('member-compo-count-' + memberId);
                                if (countEl) {
                                    countEl.textContent = result.data.compo_used;
                                    countEl.style.color = '#4db84d';
                                    setTimeout(() => {
                                        countEl.style.color = '#ffd700';
                                    }, 200);
                                }
                            }
                        } catch (e) {
                            // Fallback to local update
                            const member = members.find(m => m.id === memberId);
                            if (member) {
                                member.compoUsed = (member.compoUsed || 0) + jumlah;
                                const countEl = document.getElementById('member-compo-count-' + memberId);
                                if (countEl) {
                                    countEl.textContent = member.compoUsed;
                                    countEl.style.color = '#4db84d';
                                    setTimeout(() => {
                                        countEl.style.color = '#ffd700';
                                    }, 200);
                                }
                            }
                        }
                    }
                </script>
            </div>
        </div>
        <div class="window-statusbar">
            <span class="statusbar-section">MemberApp</span>
            <span class="statusbar-section">Kelola member</span>
        </div>
        <div class="resizer" onmousedown="startResize(event, 'win-member')"></div>
    </div>

    <!-- ADMIN PANEL WINDOW - For Admin Only -->
    <div class="window" id="win-adminpanel" style="top:60px;left:150px;width:750px;height:500px;display:none;">
        <div class="window-titlebar" onmousedown="startDrag(event, 'win-adminpanel')">
            <svg class="window-icon" viewBox="0 0 16 16">
                <rect x="2" y="3" width="12" height="10" rx="1" fill="#cc2222" stroke="#991b1b"/>
                <rect x="4" y="5" width="8" height="2" fill="#ff6b6b"/>
                <rect x="4" y="9" width="3" height="2" fill="#fff"/>
                <rect x="9" y="9" width="3" height="2" fill="#fff"/>
            </svg>
            <span class="window-title">AdminPanel - Brothers Company</span>
            <div class="window-controls">
                <button class="window-btn window-btn-max" onclick="minimizeWindow('win-adminpanel')">_</button>
                <button class="window-btn window-btn-max" onclick="maximizeWindow('win-adminpanel')">□</button>
                <button class="window-btn" onclick="closeWindow('win-adminpanel')">✕</button>
            </div>
        </div>
        <div class="window-menubar">
            <span class="menu-item">File</span>
            <span class="menu-item">Edit</span>
            <span class="menu-item">View</span>
            <span class="menu-item">Reports</span>
            <span class="menu-item">Help</span>
        </div>
        <div class="window-content" style="padding:0;background:#0d1117;height:calc(100% - 48px);overflow:auto;">
            <div style="width:100%;min-height:100%;background:#0d1117;padding:15px;box-sizing:border-box;font-family:'Segoe UI',sans-serif;color:#c9d1d9;">
                <style>
                    .admin-container { width: 100%; }
                    .admin-title {
                        color: #cc2222;
                        font-size: 18px;
                        text-align: center;
                        margin: 0 0 15px 0;
                        padding-bottom: 10px;
                        border-bottom: 1px solid #30363d;
                    }
                    .admin-tabs {
                        display: flex;
                        gap: 5px;
                        margin-bottom: 15px;
                        flex-wrap: wrap;
                    }
                    .admin-tab {
                        padding: 8px 16px;
                        border: 1px solid #30363d;
                        background: #21262d;
                        color: #8b949e;
                        border-radius: 4px;
                        cursor: pointer;
                        font-size: 11px;
                        font-weight: bold;
                    }
                    .admin-tab.active {
                        background: #cc2222;
                        color: #fff;
                        border-color: #cc2222;
                    }
                    .admin-tab:hover:not(.active) {
                        background: #30363d;
                    }
                    .admin-panel {
                        display: none;
                        background: #161b22;
                        border: 1px solid #30363d;
                        border-radius: 8px;
                        padding: 15px;
                    }
                    .admin-panel.active { display: block; }
                    .admin-panel-title {
                        color: #58a6ff;
                        font-size: 14px;
                        margin: 0 0 10px 0;
                        padding-bottom: 5px;
                        border-bottom: 1px solid #30363d;
                    }
                    .admin-table {
                        width: 100%;
                        border-collapse: collapse;
                        font-size: 11px;
                        margin-top: 10px;
                    }
                    .admin-table th {
                        background: #21262d;
                        color: #8b949e;
                        padding: 8px;
                        text-align: left;
                        border: 1px solid #30363d;
                    }
                    .admin-table td {
                        padding: 8px;
                        border: 1px solid #30363d;
                        color: #c9d1d9;
                    }
                    .admin-table tr:hover td {
                        background: #21262d;
                    }
                    .admin-field {
                        margin-bottom: 10px;
                    }
                    .admin-field label {
                        display: block;
                        font-size: 10px;
                        font-weight: bold;
                        color: #8b949e;
                        text-transform: uppercase;
                        margin-bottom: 4px;
                    }
                    .admin-field input,
                    .admin-field select {
                        width: 100%;
                        padding: 8px;
                        border-radius: 4px;
                        border: 1px solid #30363d;
                        background: #010409;
                        color: #fff;
                        font-size: 12px;
                        box-sizing: border-box;
                    }
                    .admin-btn {
                        padding: 8px 16px;
                        border-radius: 4px;
                        border: none;
                        font-weight: bold;
                        font-size: 11px;
                        cursor: pointer;
                        background: #238636;
                        color: #fff;
                        margin-right: 5px;
                    }
                    .admin-btn:hover { background: #2ea043; }
                    .admin-btn-edit { background: #58a6ff; }
                    .admin-btn-edit:hover { background: #79b8ff; }
                    .admin-btn-delete { background: #cc2222; }
                    .admin-btn-delete:hover { background: #ff4444; }
                    .admin-btn-add { background: #ffd700; color: #000; }
                    .admin-btn-add:hover { background: #e8b828; }
                    .admin-summary {
                        display: flex;
                        gap: 15px;
                        flex-wrap: wrap;
                        margin-bottom: 15px;
                    }
                    .admin-summary-card {
                        flex: 1;
                        min-width: 150px;
                        background: #21262d;
                        border: 1px solid #30363d;
                        border-radius: 8px;
                        padding: 15px;
                        text-align: center;
                    }
                    .admin-summary-value {
                        font-size: 24px;
                        font-weight: bold;
                        color: #ffd700;
                    }
                    .admin-summary-label {
                        font-size: 10px;
                        color: #8b949e;
                        text-transform: uppercase;
                        margin-top: 5px;
                    }
                    .keuangan-grid {
                        display: grid;
                        grid-template-columns: 1fr 1fr;
                        gap: 15px;
                    }
                    .keuangan-card {
                        background: #21262d;
                        border: 1px solid #30363d;
                        border-radius: 8px;
                        padding: 15px;
                    }
                    .keuangan-card-title {
                        font-size: 12px;
                        color: #8b949e;
                        margin-bottom: 10px;
                    }
                    .keuangan-card-value {
                        font-size: 20px;
                        font-weight: bold;
                        color: #4db84d;
                    }
                    .keuangan-card-value.expense { color: #cc2222; }
                    .keuangan-card-value.balance { color: #ffd700; }
                </style>

                <div class="admin-container">
                    <h2 class="admin-title">⚙️ Admin Panel - Brothers Company</h2>

                    <!-- Tabs -->
                    <div class="admin-tabs">
                        <button class="admin-tab active" onclick="switchAdminTab('pekerja')">👷 Daftar Pekerja</button>
                        <button class="admin-tab" onclick="switchAdminTab('tambah-pekerja')">➕ Tambah Pekerja</button>
                        <button class="admin-tab" onclick="switchAdminTab('laporan')">📊 Laporan Kerja</button>
                        <button class="admin-tab" onclick="switchAdminTab('keuangan')">💰 Keuangan</button>
                    </div>

                    <!-- Tab: Daftar Pekerja -->
                    <div id="admin-pekerja" class="admin-panel active">
                        <h3 class="admin-panel-title">👷 Daftar Pekerja</h3>
                        <div class="admin-summary">
                            <div class="admin-summary-card">
                                <div class="admin-summary-value" id="total-pekerja">0</div>
                                <div class="admin-summary-label">Total Pekerja</div>
                            </div>
                            <div class="admin-summary-card">
                                <div class="admin-summary-value" id="pekerja-active">0</div>
                                <div class="admin-summary-label">Aktif</div>
                            </div>
                            <div class="admin-summary-card">
                                <div class="admin-summary-value" id="pekerja-inactive">0</div>
                                <div class="admin-summary-label">Nonaktif</div>
                            </div>
                        </div>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Jabatan</th>
                                    <th>Telepon</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="pekerja-table-body">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Tab: Tambah Pekerja -->
                    <div id="admin-tambah-pekerja" class="admin-panel">
                        <h3 class="admin-panel-title">➕ Tambah / Edit Pekerja</h3>
                        <form id="form-pekerja" onsubmit="submitPekerja(event)">
                            <input type="hidden" id="pekerja-id">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                                <div class="admin-field">
                                    <label>Nama Lengkap</label>
                                    <input type="text" id="pekerja-nama" placeholder="Masukkan nama lengkap" required>
                                </div>
                                <div class="admin-field">
                                    <label>Jabatan</label>
                                    <select id="pekerja-jabatan" required>
                                        <option value="">-- Pilih Jabatan --</option>
                                        <option value="Mechanic">Mechanic</option>
                                        <option value="Supervisor">Supervisor</option>
                                        <option value="Kasir">Kasir</option>
                                        <option value="Helper">Helper</option>
                                        <option value="Manager">Manager</option>
                                    </select>
                                </div>
                                <div class="admin-field">
                                    <label>Nomor Telepon</label>
                                    <input type="tel" id="pekerja-telepon" placeholder="08xxxxxxxxxx" required>
                                </div>
                                <div class="admin-field">
                                    <label>Status</label>
                                    <select id="pekerja-status">
                                        <option value="active">Aktif</option>
                                        <option value="inactive">Nonaktif</option>
                                    </select>
                                </div>
                            </div>
                            <div style="margin-top:10px;">
                                <button type="submit" class="admin-btn admin-btn-add">💾 Simpan</button>
                                <button type="button" class="admin-btn" onclick="resetFormPekerja()">🔄 Reset</button>
                            </div>
                        </form>
                    </div>

                    <!-- Tab: Laporan Kerja -->
                    <div id="admin-laporan" class="admin-panel">
                        <h3 class="admin-panel-title">📊 Laporan Kerja</h3>
                        <div style="display:flex;gap:10px;margin-bottom:15px;flex-wrap:wrap;">
                            <select id="filter-bulan" onchange="loadLaporan()" style="padding:8px;border-radius:4px;border:1px solid #30363d;background:#010409;color:#fff;">
                                <option value="">Semua Bulan</option>
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04">April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                            <button onclick="loadLaporan()" class="admin-btn">🔍 Filter</button>
                        </div>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nama Mechanic</th>
                                    <th>Compo Used</th>
                                    <th>Money Stored</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="laporan-table-body">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                        <div style="margin-top:15px;padding:10px;background:#21262d;border-radius:4px;text-align:right;">
                            <span style="color:#8b949e;">Total: </span>
                            <span id="laporan-total" style="color:#ffd700;font-size:16px;font-weight:bold;">Rp 0</span>
                        </div>
                    </div>

                    <!-- Tab: Keuangan -->
                    <div id="admin-keuangan" class="admin-panel">
                        <h3 class="admin-panel-title">💰 Keuangan Company</h3>
                        <div class="admin-summary">
                            <div class="admin-summary-card">
                                <div class="admin-summary-value" style="color:#4db84d;" id="keuangan-pemasukan">Rp 0</div>
                                <div class="admin-summary-label">Total Pemasukan</div>
                            </div>
                            <div class="admin-summary-card">
                                <div class="admin-summary-value" style="color:#cc2222;" id="keuangan-pengeluaran">Rp 0</div>
                                <div class="admin-summary-label">Total Pengeluaran</div>
                            </div>
                            <div class="admin-summary-card">
                                <div class="admin-summary-value" id="keuangan-sisa">Rp 0</div>
                                <div class="admin-summary-label">Sisa Saldo</div>
                            </div>
                        </div>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Keterangan</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody id="keuangan-table-body">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <script>
                    // Admin Panel Functions
                    let pekerjaList = [];

                    function switchAdminTab(tab) {
                        document.querySelectorAll('.admin-tab').forEach(t => t.classList.remove('active'));
                        document.querySelectorAll('.admin-panel').forEach(p => p.classList.remove('active'));

                        document.querySelectorAll('.admin-tab').forEach((t, i) => {
                            if (t.textContent.includes(tab === 'pekerja' ? 'Daftar' : tab === 'tambah-pekerja' ? 'Tambah' : tab === 'laporan' ? 'Laporan' : 'Keuangan')) {
                                t.classList.add('active');
                            }
                        });

                        const panel = document.getElementById('admin-' + tab);
                        if (panel) panel.classList.add('active');

                        // Load data for each tab
                        if (tab === 'pekerja') loadPekerja();
                        if (tab === 'laporan') loadLaporan();
                        if (tab === 'keuangan') loadKeuangan();
                    }

                    // Load workers from API
                    async function loadPekerja() {
                        try {
                            const response = await fetch('api/pekerja_api.php?action=get_all');
                            const result = await response.json();
                            if (result.success) {
                                pekerjaList = result.data;
                                renderPekerjaTable();
                            }
                        } catch (e) {
                            // Fallback demo data
                            pekerjaList = [
                                { id: 1, nama: 'Joko Susanto', jabatan: 'Mechanic', telepon: '081234567890', status: 'active' },
                                { id: 2, nama: 'Ahmad Rizki', jabatan: 'Supervisor', telepon: '085678901234', status: 'active' },
                                { id: 3, nama: 'Dewi Lestari', jabatan: 'Kasir', telepon: '087812345678', status: 'active' },
                                { id: 4, nama: 'Budi Santoso', jabatan: 'Helper', telepon: '089123456789', status: 'inactive' }
                            ];
                            renderPekerjaTable();
                        }
                    }

                    function renderPekerjaTable() {
                        const tbody = document.getElementById('pekerja-table-body');
                        let activeCount = 0;
                        let inactiveCount = 0;

                        let html = '';
                        pekerjaList.forEach(p => {
                            if (p.status === 'active') activeCount++;
                            else inactiveCount++;

                            html += `
                                <tr>
                                    <td>${p.id}</td>
                                    <td>${p.nama}</td>
                                    <td>${p.jabatan}</td>
                                    <td>${p.telepon}</td>
                                    <td><span style="color:${p.status === 'active' ? '#4db84d' : '#f85149'};">${p.status === 'active' ? '● Aktif' : '○ Nonaktif'}</span></td>
                                    <td>
                                        <button class="admin-btn admin-btn-edit" onclick="editPekerja(${p.id})">✏️</button>
                                        <button class="admin-btn admin-btn-delete" onclick="deletePekerja(${p.id})">🗑️</button>
                                    </td>
                                </tr>
                            `;
                        });

                        tbody.innerHTML = html || '<tr><td colspan="6" style="text-align:center;color:#8b949e;">Tidak ada data</td></tr>';
                        document.getElementById('total-pekerja').textContent = pekerjaList.length;
                        document.getElementById('pekerja-active').textContent = activeCount;
                        document.getElementById('pekerja-inactive').textContent = inactiveCount;
                    }

                    function editPekerja(id) {
                        const p = pekerjaList.find(x => x.id === id);
                        if (p) {
                            document.getElementById('pekerja-id').value = p.id;
                            document.getElementById('pekerja-nama').value = p.nama;
                            document.getElementById('pekerja-jabatan').value = p.jabatan;
                            document.getElementById('pekerja-telepon').value = p.telepon;
                            document.getElementById('pekerja-status').value = p.status;
                            switchAdminTab('tambah-pekerja');
                        }
                    }

                    async function deletePekerja(id) {
                        if (confirm('Yakin hapus pekerja ini?')) {
                            try {
                                const formData = new FormData();
                                formData.append('action', 'delete');
                                formData.append('id', id);
                                await fetch('api/pekerja_api.php', { method: 'POST', body: formData });
                            } catch (e) {}
                            loadPekerja();
                        }
                    }

                    async function submitPekerja(e) {
                        e.preventDefault();
                        const id = document.getElementById('pekerja-id').value;
                        const data = {
                            nama: document.getElementById('pekerja-nama').value,
                            jabatan: document.getElementById('pekerja-jabatan').value,
                            telepon: document.getElementById('pekerja-telepon').value,
                            status: document.getElementById('pekerja-status').value
                        };

                        try {
                            const formData = new FormData();
                            formData.append('action', id ? 'update' : 'add');
                            if (id) formData.append('id', id);
                            formData.append('nama', data.nama);
                            formData.append('jabatan', data.jabatan);
                            formData.append('telepon', data.telepon);
                            formData.append('status', data.status);
                            await fetch('api/pekerja_api.php', { method: 'POST', body: formData });
                        } catch (e) {}

                            // Local update
                            if (id) {
                                const idx = pekerjaList.findIndex(x => x.id == id);
                                if (idx >= 0) pekerjaList[idx] = { ...pekerjaList[idx], ...data };
                            } else {
                                pekerjaList.push({ id: Date.now(), ...data });
                            }
                            alert('Pekerja berhasil disimpan!');
                            resetFormPekerja();
                            loadPekerja();
                            switchAdminTab('pekerja');
                    }

                    function resetFormPekerja() {
                        document.getElementById('pekerja-id').value = '';
                        document.getElementById('form-pekerja').reset();
                    }

                    // Load Laporan
                    async function loadLaporan() {
                        const tbody = document.getElementById('laporan-table-body');
                        // Demo data
                        const demoLaporan = [
                            { tanggal: '2026-05-20', nama_mechanic: 'Joko Susanto', compo_used: 10, money_stored: 500000, keterangan: 'Service harian' },
                            { tanggal: '2026-05-19', nama_mechanic: 'Ahmad Rizki', compo_used: 8, money_stored: 400000, keterangan: 'Perbaikan mesin' },
                            { tanggal: '2026-05-18', nama_mechanic: 'Dewi Lestari', compo_used: 15, money_stored: 750000, keterangan: 'Routine check' }
                        ];

                        let total = 0;
                        let html = '';
                        demoLaporan.forEach(l => {
                            total += parseFloat(l.money_stored);
                            html += `
                                <tr>
                                    <td>${l.tanggal}</td>
                                    <td>${l.nama_mechanic}</td>
                                    <td>${l.compo_used}</td>
                                    <td>Rp ${parseInt(l.money_stored).toLocaleString('id-ID')}</td>
                                    <td>${l.keterangan}</td>
                                </tr>
                            `;
                        });

                        tbody.innerHTML = html || '<tr><td colspan="5" style="text-align:center;color:#8b949e;">Tidak ada data</td></tr>';
                        document.getElementById('laporan-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
                    }

                    // Load Keuangan
                    async function loadKeuangan() {
                        const tbody = document.getElementById('keuangan-table-body');
                        // Demo data
                        const demoKeuangan = [
                            { tanggal: '2026-05-20', jenis: 'Pemasukan', keterangan: 'Service Motor', jumlah: 500000 },
                            { tanggal: '2026-05-20', jenis: 'Pengeluaran', keterangan: 'Beli Sparepart', jumlah: 150000 },
                            { tanggal: '2026-05-19', jenis: 'Pemasukan', keterangan: 'Modifikasi', jumlah: 800000 },
                            { tanggal: '2026-05-18', jenis: 'Pengeluaran', keterangan: 'Gaji Karyawan', jumlah: 2000000 }
                        ];

                        let pemasukan = 0;
                        let pengeluaran = 0;
                        let html = '';
                        demoKeuangan.forEach(k => {
                            if (k.jenis === 'Pemasukan') pemasukan += k.jumlah;
                            else pengeluaran += k.jumlah;

                            html += `
                                <tr>
                                    <td>${k.tanggal}</td>
                                    <td><span style="color:${k.jenis === 'Pemasukan' ? '#4db84d' : '#f85149'};">${k.jenis}</span></td>
                                    <td>${k.keterangan}</td>
                                    <td style="color:${k.jenis === 'Pemasukan' ? '#4db84d' : '#f85149'};">Rp ${parseInt(k.jumlah).toLocaleString('id-ID')}</td>
                                </tr>
                            `;
                        });

                        tbody.innerHTML = html || '<tr><td colspan="4" style="text-align:center;color:#8b949e;">Tidak ada data</td></tr>';
                        document.getElementById('keuangan-pemasukan').textContent = 'Rp ' + pemasukan.toLocaleString('id-ID');
                        document.getElementById('keuangan-pengeluaran').textContent = 'Rp ' + pengeluaran.toLocaleString('id-ID');
                        document.getElementById('keuangan-sisa').textContent = 'Rp ' + (pemasukan - pengeluaran).toLocaleString('id-ID');
                    }
                </script>
            </div>
        </div>
        <div class="window-statusbar">
            <span class="statusbar-section">AdminPanel</span>
            <span class="statusbar-section">Kelola pekerja & keuangan</span>
        </div>
        <div class="resizer" onmousedown="startResize(event, 'win-adminpanel')"></div>
    </div>

    <!-- CARGO APP WINDOW -->
    <div class="window" id="win-cargo" style="top:60px;left:120px;width:620px;height:450px;display:none;">
        <div class="window-titlebar" onmousedown="startDrag(event, 'win-cargo')">
            <svg class="window-icon" viewBox="0 0 16 16">
                <rect x="2" y="4" width="12" height="10" rx="1" fill="#8b5cf6"/>
                <rect x="4" y="6" width="8" height="6" fill="#c4b5fd"/>
            </svg>
            <span class="window-title">CargoApp - Brothers Company</span>
            <div class="window-controls">
                <button class="window-btn window-btn-max" onclick="minimizeWindow('win-cargo')">_</button>
                <button class="window-btn window-btn-max" onclick="maximizeWindow('win-cargo')">□</button>
                <button class="window-btn" onclick="closeWindow('win-cargo')">✕</button>
            </div>
        </div>
        <div class="window-menubar">
            <span class="menu-item">File</span>
            <span class="menu-item">Edit</span>
            <span class="menu-item">View</span>
            <span class="menu-item">Help</span>
        </div>
        <div class="window-content">
            <div class="app-panel">
                <div class="app-section">
                    <div class="app-section-title">📦 PENGELOLAAN CARGO</div>
                    <div class="btn-grid">
                        <button class="app-btn" onclick="showToast('New Shipment', 'Membuat pengiriman baru...')">
                            <svg viewBox="0 0 32 32">
                                <rect x="2" y="8" width="28" height="20" rx="2" fill="#8b5cf6"/>
                                <rect x="4" y="10" width="24" height="16" fill="#c4b5fd"/>
                                <rect x="8" y="14" width="16" height="8" fill="#7c3aed"/>
                                <rect x="12" y="28" width="8" height="4" fill="#333"/>
                            </svg>
                            New Shipment
                        </button>
                        <button class="app-btn" onclick="showToast('Track Package', 'Melacak paket...')">
                            <svg viewBox="0 0 32 32">
                                <rect x="4" y="4" width="24" height="24" rx="2" fill="#c4b5fd" stroke="#8b5cf6"/>
                                <rect x="8" y="8" width="16" height="4" fill="#8b5cf6"/>
                                <rect x="8" y="14" width="12" height="2" fill="#8b5cf6"/>
                                <rect x="8" y="18" width="14" height="2" fill="#8b5cf6"/>
                                <rect x="8" y="22" width="10" height="2" fill="#8b5cf6"/>
                            </svg>
                            Track Package
                        </button>
                        <button class="app-btn" onclick="showToast('Delivery List', 'Melihat daftar pengiriman...')">
                            <svg viewBox="0 0 32 32">
                                <rect x="4" y="2" width="24" height="28" rx="2" fill="#f5f5f5" stroke="#8b5cf6"/>
                                <rect x="8" y="6" width="16" height="2" fill="#8b5cf6"/>
                                <rect x="8" y="10" width="12" height="2" fill="#ccc"/>
                                <rect x="8" y="14" width="14" height="2" fill="#ccc"/>
                                <rect x="8" y="18" width="10" height="2" fill="#ccc"/>
                                <circle cx="24" cy="24" r="4" fill="#8b5cf6"/>
                                <path d="M22 24 L24 26 L26 22" stroke="#fff" stroke-width="1.5" fill="none"/>
                            </svg>
                            Delivery List
                        </button>
                    </div>
                </div>
                <div class="app-section">
                    <div class="app-section-title">📊 STATISTIK CARGO</div>
                    <div class="btn-grid">
                        <button class="app-btn btn-xp-green" onclick="showToast('Delivered', '15 paket terkirim!')">
                            ✅ Delivered: <strong>15</strong>
                        </button>
                        <button class="app-btn btn-xp-orange" onclick="showToast('In Transit', '8 paket dalam perjalanan')">
                            🔄 In Transit: <strong>8</strong>
                        </button>
                        <button class="app-btn btn-xp-red" onclick="showToast('Pending', '3 paket tertunda')">
                            ⏳ Pending: <strong>3</strong>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="window-statusbar">
            <span class="statusbar-section">CargoApp</span>
            <span class="statusbar-section">Kelola pengiriman cargo</span>
        </div>
        <div class="resizer" onmousedown="startResize(event, 'win-cargo')"></div>
    </div>

    <!-- DAFTAR PEKERJA WINDOW -->
    <div class="window" id="win-daftarpekerja" style="top:80px;left:150px;width:700px;height:450px;display:none;">
        <div class="window-titlebar" onmousedown="startDrag(event, 'win-daftarpekerja')">
            <svg class="window-icon" viewBox="0 0 16 16">
                <rect x="2" y="2" width="12" height="12" rx="1" fill="#3a6ea5"/>
                <circle cx="5" cy="7" r="2" fill="#f5c890"/>
                <circle cx="11" cy="7" r="2" fill="#f5c890"/>
                <ellipse cx="8" cy="12" rx="4" ry="2" fill="#245edc"/>
            </svg>
            <span class="window-title">Daftar Pekerja - Brothers Company</span>
            <div class="window-controls">
                <button class="window-btn window-btn-max" onclick="minimizeWindow('win-daftarpekerja')">_</button>
                <button class="window-btn window-btn-max" onclick="maximizeWindow('win-daftarpekerja')">□</button>
                <button class="window-btn" onclick="closeWindow('win-daftarpekerja')">✕</button>
            </div>
        </div>
        <div class="window-menubar">
            <span class="menu-item">File</span>
            <span class="menu-item">Edit</span>
            <span class="menu-item">View</span>
            <span class="menu-item">Help</span>
        </div>
        <div class="window-content" style="padding:0;background:#0d1117;height:calc(100% - 48px);overflow:auto;">
            <div style="width:100%;min-height:100%;background:#0d1117;padding:15px;box-sizing:border-box;font-family:'Segoe UI',sans-serif;color:#c9d1d9;">
                <style>
                    .dp-header { display:flex;justify-content:space-between;align-items:center;margin-bottom:15px; }
                    .dp-title { color:#58a6ff;font-size:16px;margin:0; }
                    .dp-stats { display:flex;gap:15px; }
                    .dp-stat { background:#161b22;border:1px solid #30363d;padding:10px 20px;border-radius:8px;text-align:center; }
                    .dp-stat-value { font-size:20px;font-weight:bold;color:#ffd700; }
                    .dp-stat-label { font-size:10px;color:#8b949e;text-transform:uppercase; }
                    .dp-table { width:100%;border-collapse:collapse;font-size:12px; }
                    .dp-table th { background:#21262d;color:#8b949e;padding:10px;border:1px solid #30363d;text-align:left; }
                    .dp-table td { padding:10px;border:1px solid #30363d;color:#c9d1d9; }
                    .dp-table tr:hover td { background:#21262d; }
                    .dp-btn { padding:6px 12px;border-radius:4px;border:none;font-size:11px;font-weight:bold;cursor:pointer; }
                    .dp-btn-edit { background:#58a6ff;color:#fff; }
                    .dp-btn-delete { background:#cc2222;color:#fff; }
                    .dp-status-active { color:#4db84d; }
                    .dp-status-inactive { color:#f85149; }
                </style>
                <div class="dp-header">
                    <h2 class="dp-title">👷 Daftar Pekerja</h2>
                    <div class="dp-stats">
                        <div class="dp-stat"><div class="dp-stat-value" id="dp-total">0</div><div class="dp-stat-label">Total</div></div>
                        <div class="dp-stat"><div class="dp-stat-value" id="dp-active" style="color:#4db84d;">0</div><div class="dp-stat-label">Aktif</div></div>
                        <div class="dp-stat"><div class="dp-stat-value" id="dp-inactive" style="color:#f85149;">0</div><div class="dp-stat-label">Nonaktif</div></div>
                    </div>
                </div>
                <table class="dp-table">
                    <thead><tr><th>ID</th><th>Nama</th><th>Jabatan</th><th>Telepon</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody id="dp-table-body"></tbody>
                </table>
            </div>
            <script>loadDaftarPekerja();</script>
        </div>
        <div class="window-statusbar"><span class="statusbar-section">Daftar Pekerja</span><span class="statusbar-section">Kelola pekerja</span></div>
        <div class="resizer" onmousedown="startResize(event, 'win-daftarpekerja')"></div>
    </div>

    <!-- TAMBAH PEKERJA WINDOW -->
    <div class="window" id="win-tambahpekerja" style="top:90px;left:160px;width:500px;height:400px;display:none;">
        <div class="window-titlebar" onmousedown="startDrag(event, 'win-tambahpekerja')">
            <svg class="window-icon" viewBox="0 0 16 16">
                <rect x="2" y="2" width="12" height="12" rx="1" fill="#238636"/>
                <path d="M8 5 L8 11 M5 8 L11 8" stroke="#fff" stroke-width="2"/>
            </svg>
            <span class="window-title">Tambah Pekerja - Brothers Company</span>
            <div class="window-controls">
                <button class="window-btn window-btn-max" onclick="minimizeWindow('win-tambahpekerja')">_</button>
                <button class="window-btn window-btn-max" onclick="maximizeWindow('win-tambahpekerja')">□</button>
                <button class="window-btn" onclick="closeWindow('win-tambahpekerja')">✕</button>
            </div>
        </div>
        <div class="window-menubar">
            <span class="menu-item">File</span>
            <span class="menu-item">Edit</span>
            <span class="menu-item">View</span>
            <span class="menu-item">Help</span>
        </div>
        <div class="window-content" style="padding:0;background:#0d1117;height:calc(100% - 48px);overflow:auto;">
            <div style="width:100%;min-height:100%;background:#0d1117;padding:15px;box-sizing:border-box;font-family:'Segoe UI',sans-serif;color:#c9d1d9;">
                <style>
                    .tp-form { background:#161b22;border:1px solid #30363d;border-radius:8px;padding:20px; }
                    .tp-title { color:#238636;font-size:16px;margin:0 0 15px 0; }
                    .tp-field { margin-bottom:12px; }
                    .tp-field label { display:block;font-size:10px;font-weight:bold;color:#8b949e;text-transform:uppercase;margin-bottom:4px; }
                    .tp-field input,.tp-field select { width:100%;padding:8px;border-radius:4px;border:1px solid #30363d;background:#010409;color:#fff;font-size:12px;box-sizing:border-box; }
                    .tp-btn { padding:10px 20px;border-radius:4px;border:none;font-weight:bold;font-size:12px;cursor:pointer;background:#238636;color:#fff;margin-right:10px; }
                    .tp-btn:hover { background:#2ea043; }
                    .tp-btn-reset { background:#30363d;color:#fff; }
                </style>
                <form class="tp-form" onsubmit="submitNewPekerja(event)">
                    <h3 class="tp-title">➕ Form Tambah Pekerja Baru</h3>
                    <div class="tp-field"><label>Username</label><input type="text" id="tp-username" placeholder="Masukkan username" required></div>
                    <div class="tp-field"><label>Email</label><input type="email" id="tp-email" placeholder="email@example.com" required></div>
                    <div class="tp-field"><label>Password</label><input type="password" id="tp-password" placeholder="Masukkan password" required></div>
                    <div class="tp-field"><label>Nama Lengkap</label><input type="text" id="tp-nama" placeholder="Masukkan nama lengkap"></div>
                    <div class="tp-field"><label>Nomor Telepon</label><input type="tel" id="tp-telepon" placeholder="08xxxxxxxxxx"></div>
                    <div class="tp-field"><label>Jabatan</label><select id="tp-jabatan" required><option value="">-- Pilih Jabatan --</option><option value="Mechanic">Mechanic</option><option value="Supervisor">Supervisor</option><option value="Kasir">Kasir</option><option value="Helper">Helper</option><option value="Manager">Manager</option></select></div>
                    <button type="submit" class="tp-btn">💾 Simpan Pekerja</button>
                    <button type="button" class="tp-btn tp-btn-reset" onclick="document.getElementById('tp-form').reset()">🔄 Reset</button>
                </form>
            </div>
        </div>
        <div class="window-statusbar"><span class="statusbar-section">Tambah Pekerja</span><span class="statusbar-section">Tambah pekerja baru</span></div>
        <div class="resizer" onmousedown="startResize(event, 'win-tambahpekerja')"></div>
    </div>

    <!-- LAPORAN KERJA WINDOW -->
    <div class="window" id="win-laporankerja" style="top:100px;left:170px;width:750px;height:480px;display:none;">
        <div class="window-titlebar" onmousedown="startDrag(event, 'win-laporankerja')">
            <svg class="window-icon" viewBox="0 0 16 16">
                <rect x="2" y="1" width="12" height="14" rx="1" fill="#ffd700"/>
                <rect x="4" y="4" width="8" height="1" fill="#8b6914"/>
                <rect x="4" y="6" width="6" height="1" fill="#8b6914"/>
                <rect x="4" y="8" width="8" height="1" fill="#8b6914"/>
                <rect x="4" y="10" width="5" height="1" fill="#8b6914"/>
            </svg>
            <span class="window-title">Laporan Kerja - Brothers Company</span>
            <div class="window-controls">
                <button class="window-btn window-btn-max" onclick="minimizeWindow('win-laporankerja')">_</button>
                <button class="window-btn window-btn-max" onclick="maximizeWindow('win-laporankerja')">□</button>
                <button class="window-btn" onclick="closeWindow('win-laporankerja')">✕</button>
            </div>
        </div>
        <div class="window-menubar">
            <span class="menu-item">File</span>
            <span class="menu-item">Edit</span>
            <span class="menu-item">View</span>
            <span class="menu-item">Reports</span>
            <span class="menu-item">Help</span>
        </div>
        <div class="window-content" style="padding:0;background:#0d1117;height:calc(100% - 48px);overflow:auto;">
            <div style="width:100%;min-height:100%;background:#0d1117;padding:15px;box-sizing:border-box;font-family:'Segoe UI',sans-serif;color:#c9d1d9;">
                <style>
                    .lk-header { display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;flex-wrap:wrap;gap:10px; }
                    .lk-title { color:#ffd700;font-size:16px;margin:0; }
                    .lk-filter { display:flex;gap:10px; }
                    .lk-filter select { padding:8px;border-radius:4px;border:1px solid #30363d;background:#010409;color:#fff;font-size:12px; }
                    .lk-btn { padding:8px 16px;border-radius:4px;border:none;background:#58a6ff;color:#fff;font-size:12px;font-weight:bold;cursor:pointer; }
                    .lk-table { width:100%;border-collapse:collapse;font-size:12px; }
                    .lk-table th { background:#21262d;color:#8b949e;padding:10px;border:1px solid #30363d; }
                    .lk-table td { padding:10px;border:1px solid #30363d; }
                    .lk-table tr:hover td { background:#21262d; }
                    .lk-total { margin-top:15px;padding:15px;background:#161b22;border:1px solid #30363d;border-radius:8px;display:flex;justify-content:space-between;align-items:center; }
                    .lk-total-label { color:#8b949e;font-size:12px; }
                    .lk-total-value { font-size:20px;font-weight:bold;color:#ffd700; }
                </style>
                <div class="lk-header">
                    <h2 class="lk-title">📊 Laporan Kerja Mechanic</h2>
                    <div class="lk-filter">
                        <select id="lk-bulan"><option value="">Semua Bulan</option><option value="01">Januari</option><option value="02">Februari</option><option value="03">Maret</option><option value="04">April</option><option value="05">Mei</option><option value="06">Juni</option><option value="07">Juli</option><option value="08">Agustus</option><option value="09">September</option><option value="10">Oktober</option><option value="11">November</option><option value="12">Desember</option></select>
                        <button class="lk-btn" onclick="loadLaporanKerja()">🔍 Filter</button>
                    </div>
                </div>
                <table class="lk-table">
                    <thead><tr><th>Tanggal</th><th>Nama Mechanic</th><th>Compo Used</th><th>Money Stored</th><th>Keterangan</th></tr></thead>
                    <tbody id="lk-table-body"></tbody>
                </table>
                <div class="lk-total"><span class="lk-total-label">Total Pendapatan:</span><span class="lk-total-value" id="lk-total-value">Rp 0</span></div>
            </div>
            <script>loadLaporanKerja();</script>
        </div>
        <div class="window-statusbar"><span class="statusbar-section">Laporan Kerja</span><span class="statusbar-section">Laporan kerja mechanic</span></div>
        <div class="resizer" onmousedown="startResize(event, 'win-laporankerja')"></div>
    </div>

    <!-- KEUANGAN COMPANY WINDOW -->
    <div class="window" id="win-keuangan" style="top:110px;left:180px;width:750px;height:500px;display:none;">
        <div class="window-titlebar" onmousedown="startDrag(event, 'win-keuangan')">
            <svg class="window-icon" viewBox="0 0 16 16">
                <circle cx="8" cy="8" r="6" fill="#4db84d"/>
                <text x="8" y="11" text-anchor="middle" font-size="8" font-weight="bold" fill="#fff">$</text>
            </svg>
            <span class="window-title">Keuangan Company - Brothers Company</span>
            <div class="window-controls">
                <button class="window-btn window-btn-max" onclick="minimizeWindow('win-keuangan')">_</button>
                <button class="window-btn window-btn-max" onclick="maximizeWindow('win-keuangan')">□</button>
                <button class="window-btn" onclick="closeWindow('win-keuangan')">✕</button>
            </div>
        </div>
        <div class="window-menubar">
            <span class="menu-item">File</span>
            <span class="menu-item">Edit</span>
            <span class="menu-item">View</span>
            <span class="menu-item">Reports</span>
            <span class="menu-item">Help</span>
        </div>
        <div class="window-content" style="padding:0;background:#0d1117;height:calc(100% - 48px);overflow:auto;">
            <div style="width:100%;min-height:100%;background:#0d1117;padding:15px;box-sizing:border-box;font-family:'Segoe UI',sans-serif;color:#c9d1d9;">
                <style>
                    .keu-header { margin-bottom:15px; }
                    .keu-title { color:#4db84d;font-size:16px;margin:0 0 15px 0; }
                    .keu-cards { display:grid;grid-template-columns:repeat(3,1fr);gap:15px;margin-bottom:15px; }
                    .keu-card { background:#161b22;border:1px solid #30363d;border-radius:8px;padding:20px;text-align:center; }
                    .keu-card-value { font-size:24px;font-weight:bold; }
                    .keu-card-value.income { color:#4db84d; }
                    .keu-card-value.expense { color:#cc2222; }
                    .keu-card-value.balance { color:#ffd700; }
                    .keu-card-label { font-size:10px;color:#8b949e;text-transform:uppercase;margin-top:5px; }
                    .keu-table { width:100%;border-collapse:collapse;font-size:12px; }
                    .keu-table th { background:#21262d;color:#8b949e;padding:10px;border:1px solid #30363d; }
                    .keu-table td { padding:10px;border:1px solid #30363d; }
                    .keu-table tr:hover td { background:#21262d; }
                    .keu-income { color:#4db84d; }
                    .keu-expense { color:#cc2222; }
                </style>
                <div class="keu-header">
                    <h2 class="keu-title">💰 Keuangan Brothers Company</h2>
                </div>
                <div class="keu-cards">
                    <div class="keu-card"><div class="keu-card-value income" id="keu-pemasukan">Rp 0</div><div class="keu-card-label">Total Pemasukan</div></div>
                    <div class="keu-card"><div class="keu-card-value expense" id="keu-pengeluaran">Rp 0</div><div class="keu-card-label">Total Pengeluaran</div></div>
                    <div class="keu-card"><div class="keu-card-value balance" id="keu-sisa">Rp 0</div><div class="keu-card-label">Saldo Tersisa</div></div>
                </div>
                <table class="keu-table">
                    <thead><tr><th>Tanggal</th><th>Jenis</th><th>Keterangan</th><th>Jumlah</th></tr></thead>
                    <tbody id="keu-table-body"></tbody>
                </table>
            </div>
            <script>loadKeuangan();</script>
        </div>
        <div class="window-statusbar"><span class="statusbar-section">Keuangan</span><span class="statusbar-section">Kelola keuangan company</span></div>
        <div class="resizer" onmousedown="startResize(event, 'win-keuangan')"></div>
    </div>

    <script>
    // Admin Desktop Apps Functions

    // Load Daftar Pekerja
    async function loadDaftarPekerja() {
        const tbody = document.getElementById('dp-table-body');
        if (!tbody) return;

        try {
            const response = await fetch('api/pekerja_api.php?action=get_all');
            const result = await response.json();
            if (result.success) {
                renderDpTable(result.data);
            }
        } catch (e) {
            // Demo data fallback
            const demo = [
                { id: 1, username: 'joko', full_name: 'Joko Susanto', phone: '081234567890', role: 'employee' },
                { id: 2, username: 'ahmad', full_name: 'Ahmad Rizki', phone: '085678901234', role: 'employee' },
                { id: 3, username: 'dewi', full_name: 'Dewi Lestari', phone: '087812345678', role: 'employee' }
            ];
            renderDpTable(demo);
        }
    }

    function renderDpTable(data) {
        const tbody = document.getElementById('dp-table-body');
        if (!tbody) return;

        let active = 0, inactive = 0;
        let html = '';

        data.forEach(p => {
            const status = p.created_at && new Date(p.created_at) > new Date(Date.now() - 30*24*60*60*1000) ? 'active' : 'inactive';
            if (status === 'active') active++;
            else inactive++;

            html += `<tr>
                <td>${p.id}</td>
                <td>${p.full_name || p.username}</td>
                <td>Mechanic</td>
                <td>${p.phone || '-'}</td>
                <td class="${status === 'active' ? 'dp-status-active' : 'dp-status-inactive'}">${status === 'active' ? '● Aktif' : '○ Nonaktif'}</td>
                <td>
                    <button class="dp-btn dp-btn-edit" onclick="alert('Fitur edit dalam pengembangan')">✏️</button>
                    <button class="dp-btn dp-btn-delete" onclick="deleteDpPekerja(${p.id})">🗑️</button>
                </td>
            </tr>`;
        });

        tbody.innerHTML = html || '<tr><td colspan="6" style="text-align:center;color:#8b949e;">Tidak ada data pekerja</td></tr>';
        document.getElementById('dp-total').textContent = data.length;
        document.getElementById('dp-active').textContent = active;
        document.getElementById('dp-inactive').textContent = inactive;
    }

    async function deleteDpPekerja(id) {
        if (confirm('Yakin hapus pekerja ini?')) {
            try {
                const fd = new FormData();
                fd.append('action', 'delete');
                fd.append('id', id);
                await fetch('api/pekerja_api.php', { method: 'POST', body: fd });
            } catch (e) {}
            loadDaftarPekerja();
        }
    }

    // Submit New Pekerja
    async function submitNewPekerja(e) {
        e.preventDefault();
        const data = {
            username: document.getElementById('tp-username').value,
            email: document.getElementById('tp-email').value,
            password: document.getElementById('tp-password').value,
            full_name: document.getElementById('tp-nama').value,
            phone: document.getElementById('tp-telepon').value,
            jabatan: document.getElementById('tp-jabatan').value
        };

        try {
            const fd = new FormData();
            fd.append('action', 'add');
            fd.append('username', data.username);
            fd.append('email', data.email);
            fd.append('password', data.password);
            fd.append('full_name', data.full_name);
            fd.append('phone', data.phone);
            fd.append('jabatan', data.jabatan);
            const response = await fetch('api/pekerja_api.php', { method: 'POST', body: fd });
            const result = await response.json();
            if (result.success) {
                alert('Pekerja berhasil ditambahkan!');
                document.getElementById('tp-form').reset();
            } else {
                alert('Error: ' + result.message);
            }
        } catch (e) {
            alert('Pekerja berhasil ditambahkan (mode offline)!');
            document.getElementById('tp-form').reset();
        }
    }

    // Load Laporan Kerja
    async function loadLaporanKerja() {
        const tbody = document.getElementById('lk-table-body');
        if (!tbody) return;

        // Demo data
        const demo = [
            { tanggal: '2026-05-20', nama_mechanic: 'Joko Susanto', compo_used: 10, money_stored: 500000, keterangan: 'Service harian' },
            { tanggal: '2026-05-19', nama_mechanic: 'Ahmad Rizki', compo_used: 8, money_stored: 400000, keterangan: 'Perbaikan mesin' },
            { tanggal: '2026-05-18', nama_mechanic: 'Dewi Lestari', compo_used: 15, money_stored: 750000, keterangan: 'Routine check' },
            { tanggal: '2026-05-17', nama_mechanic: 'Budi Santoso', compo_used: 12, money_stored: 600000, keterangan: 'Modifikasi' },
            { tanggal: '2026-05-16', nama_mechanic: 'Joko Susanto', compo_used: 6, money_stored: 300000, keterangan: 'Tune up' }
        ];

        let total = 0;
        let html = '';
        demo.forEach(l => {
            total += l.money_stored;
            html += `<tr>
                <td>${l.tanggal}</td>
                <td>${l.nama_mechanic}</td>
                <td>${l.compo_used}</td>
                <td>Rp ${l.money_stored.toLocaleString('id-ID')}</td>
                <td>${l.keterangan}</td>
            </tr>`;
        });

        tbody.innerHTML = html || '<tr><td colspan="5" style="text-align:center;color:#8b949e;">Tidak ada data laporan</td></tr>';
        document.getElementById('lk-total-value').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    // Load Keuangan
    async function loadKeuangan() {
        const tbody = document.getElementById('keu-table-body');
        if (!tbody) return;

        // Demo data
        const demo = [
            { tanggal: '2026-05-20', jenis: 'Pemasukan', keterangan: 'Service Motor', jumlah: 500000 },
            { tanggal: '2026-05-20', jenis: 'Pengeluaran', keterangan: 'Beli Sparepart', jumlah: 150000 },
            { tanggal: '2026-05-19', jenis: 'Pemasukan', keterangan: 'Modifikasi', jumlah: 800000 },
            { tanggal: '2026-05-19', jenis: 'Pengeluaran', keterangan: 'Gaji Karyawan', jumlah: 2000000 },
            { tanggal: '2026-05-18', jenis: 'Pemasukan', keterangan: 'Repair', jumlah: 400000 },
            { tanggal: '2026-05-18', jenis: 'Pengeluaran', keterangan: 'Beli Tools', jumlah: 500000 }
        ];

        let pemasukan = 0, pengeluaran = 0;
        let html = '';
        demo.forEach(k => {
            if (k.jenis === 'Pemasukan') pemasukan += k.jumlah;
            else pengeluaran += k.jumlah;

            html += `<tr>
                <td>${k.tanggal}</td>
                <td class="${k.jenis === 'Pemasukan' ? 'keu-income' : 'keu-expense'}">${k.jenis}</td>
                <td>${k.keterangan}</td>
                <td class="${k.jenis === 'Pemasukan' ? 'keu-income' : 'keu-expense'}">Rp ${k.jumlah.toLocaleString('id-ID')}</td>
            </tr>`;
        });

        tbody.innerHTML = html || '<tr><td colspan="4" style="text-align:center;color:#8b949e;">Tidak ada data keuangan</td></tr>';
        document.getElementById('keu-pemasukan').textContent = 'Rp ' + pemasukan.toLocaleString('id-ID');
        document.getElementById('keu-pengeluaran').textContent = 'Rp ' + pengeluaran.toLocaleString('id-ID');
        document.getElementById('keu-sisa').textContent = 'Rp ' + (pemasukan - pengeluaran).toLocaleString('id-ID');
    }
    </script>

    <!-- FARMER APP WINDOW -->
    <div class="window" id="win-farmer" style="top:80px;left:140px;width:620px;height:450px;display:none;">
        <div class="window-titlebar" onmousedown="startDrag(event, 'win-farmer')">
            <svg class="window-icon" viewBox="0 0 16 16">
                <rect x="2" y="10" width="12" height="4" fill="#228b22"/>
                <circle cx="6" cy="8" r="3" fill="#90ee90"/>
                <circle cx="10" cy="8" r="3" fill="#90ee90"/>
            </svg>
            <span class="window-title">FarmerApp - Brothers Company</span>
            <div class="window-controls">
                <button class="window-btn window-btn-max" onclick="minimizeWindow('win-farmer')">_</button>
                <button class="window-btn window-btn-max" onclick="maximizeWindow('win-farmer')">□</button>
                <button class="window-btn" onclick="closeWindow('win-farmer')">✕</button>
            </div>
        </div>
        <div class="window-menubar">
            <span class="menu-item">File</span>
            <span class="menu-item">Edit</span>
            <span class="menu-item">View</span>
            <span class="menu-item">Help</span>
        </div>
        <div class="window-content">
            <div class="app-panel">
                <div class="app-section">
                    <div class="app-section-title">🌱 PENGELOLAAN TANAMAN</div>
                    <div class="btn-grid">
                        <button class="app-btn" onclick="showToast('Add Plant', 'Menambah tanaman baru...')">
                            <svg viewBox="0 0 32 32">
                                <rect x="4" y="24" width="24" height="6" rx="1" fill="#8b4513"/>
                                <rect x="14" y="8" width="4" height="16" fill="#228b22"/>
                                <circle cx="16" cy="8" r="6" fill="#32cd32"/>
                                <rect x="6" y="26" width="4" height="4" fill="#333"/>
                                <rect x="22" y="26" width="4" height="4" fill="#333"/>
                            </svg>
                            Add Plant
                        </button>
                        <button class="app-btn" onclick="showToast('Harvest Schedule', 'Melihat jadwal panen...')">
                            <svg viewBox="0 0 32 32">
                                <rect x="4" y="4" width="24" height="24" rx="2" fill="#f5f5f5" stroke="#228b22"/>
                                <rect x="4" y="4" width="24" height="6" rx="2" fill="#228b22"/>
                                <rect x="8" y="12" width="16" height="2" fill="#90ee90"/>
                                <rect x="8" y="16" width="12" height="2" fill="#90ee90"/>
                                <rect x="8" y="20" width="14" height="2" fill="#90ee90"/>
                                <circle cx="10" cy="12" r="2" fill="#ffd700"/>
                                <circle cx="16" cy="12" r="2" fill="#ffd700"/>
                                <circle cx="22" cy="12" r="2" fill="#ffd700"/>
                            </svg>
                            Harvest Schedule
                        </button>
                        <button class="app-btn" onclick="showToast('Inventory', 'Melihat inventori...')">
                            <svg viewBox="0 0 32 32">
                                <rect x="4" y="10" width="24" height="18" rx="2" fill="#228b22"/>
                                <rect x="6" y="12" width="6" height="6" fill="#90ee90"/>
                                <rect x="14" y="12" width="6" height="6" fill="#90ee90"/>
                                <rect x="22" y="12" width="4" height="6" fill="#90ee90"/>
                                <rect x="6" y="20" width="6" height="6" fill="#90ee90"/>
                                <rect x="14" y="20" width="6" height="6" fill="#90ee90"/>
                                <rect x="22" y="20" width="4" height="6" fill="#90ee90"/>
                            </svg>
                            Inventory
                        </button>
                    </div>
                </div>
                <div class="app-section">
                    <div class="app-section-title">📈 STATISTIK FARM</div>
                    <div class="btn-grid">
                        <button class="app-btn btn-xp-green" onclick="showToast('Harvested', '25 tanaman dipanen!')">
                            🌾 Harvested: <strong>25</strong>
                        </button>
                        <button class="app-btn btn-xp-orange" onclick="showToast('Growing', '12 tanaman tumbuh')">
                            🌱 Growing: <strong>12</strong>
                        </button>
                        <button class="app-btn btn-xp-red" onclick="showToast('Needs Care', '4 butuh perawatan')">
                            ⚠️ Needs Care: <strong>4</strong>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="window-statusbar">
            <span class="statusbar-section">FarmerApp</span>
            <span class="statusbar-section">Kelola pertanian</span>
        </div>
        <div class="resizer" onmousedown="startResize(event, 'win-farmer')"></div>
    </div>

    <!-- RESTAURANT APP WINDOW -->
    <div class="window" id="win-restaurant" style="top:100px;left:160px;width:620px;height:450px;display:none;">
        <div class="window-titlebar" onmousedown="startDrag(event, 'win-restaurant')">
            <svg class="window-icon" viewBox="0 0 16 16">
                <ellipse cx="8" cy="12" rx="6" ry="2" fill="#8b4513"/>
                <path d="M4 10 Q4 6 8 6 Q12 6 12 10" fill="#cc2222"/>
            </svg>
            <span class="window-title">RestauranApp - Brothers Company</span>
            <div class="window-controls">
                <button class="window-btn window-btn-max" onclick="minimizeWindow('win-restaurant')">_</button>
                <button class="window-btn window-btn-max" onclick="maximizeWindow('win-restaurant')">□</button>
                <button class="window-btn" onclick="closeWindow('win-restaurant')">✕</button>
            </div>
        </div>
        <div class="window-menubar">
            <span class="menu-item">File</span>
            <span class="menu-item">Edit</span>
            <span class="menu-item">View</span>
            <span class="menu-item">Help</span>
        </div>
        <div class="window-content">
            <div class="app-panel">
                <div class="app-section">
                    <div class="app-section-title">🍽️ PENGELOLAAN RESTORAN</div>
                    <div class="btn-grid">
                        <button class="app-btn" onclick="showToast('New Order', 'Membuat pesanan baru...')">
                            <svg viewBox="0 0 32 32">
                                <rect x="4" y="2" width="24" height="28" rx="2" fill="#ffd700" stroke="#b07800"/>
                                <rect x="8" y="6" width="16" height="2" fill="#8b4513"/>
                                <rect x="8" y="10" width="12" height="2" fill="#ccc"/>
                                <rect x="8" y="14" width="14" height="2" fill="#ccc"/>
                                <rect x="8" y="18" width="10" height="2" fill="#ccc"/>
                                <rect x="8" y="22" width="16" height="4" fill="#cc2222"/>
                            </svg>
                            New Order
                        </button>
                        <button class="app-btn" onclick="showToast('Menu', 'Melihat menu...')">
                            <svg viewBox="0 0 32 32">
                                <rect x="4" y="4" width="24" height="24" rx="2" fill="#cc2222"/>
                                <rect x="6" y="6" width="20" height="20" fill="#f5f5f5"/>
                                <rect x="8" y="10" width="16" height="2" fill="#cc2222"/>
                                <rect x="8" y="14" width="12" height="2" fill="#ccc"/>
                                <rect x="8" y="18" width="14" height="2" fill="#ccc"/>
                                <rect x="8" y="22" width="10" height="2" fill="#ccc"/>
                            </svg>
                            Menu
                        </button>
                        <button class="app-btn" onclick="showToast('Table Status', 'Melihat status meja...')">
                            <svg viewBox="0 0 32 32">
                                <rect x="4" y="20" width="8" height="8" rx="1" fill="#ffd700"/>
                                <rect x="14" y="20" width="8" height="8" rx="1" fill="#4db84d"/>
                                <rect x="24" y="20" width="4" height="8" rx="1" fill="#cc2222"/>
                                <rect x="8" y="14" width="4" height="6" fill="#aaa"/>
                                <rect x="18" y="14" width="4" height="6" fill="#aaa"/>
                                <rect x="25" y="14" width="4" height="6" fill="#aaa"/>
                            </svg>
                            Table Status
                        </button>
                    </div>
                </div>
                <div class="app-section">
                    <div class="app-section-title">📈 STATISTIK RESTORAN</div>
                    <div class="btn-grid">
                        <button class="app-btn btn-xp-green" onclick="showToast('Orders', '32 pesanan selesai!')">
                            🍽️ Orders: <strong>32</strong>
                        </button>
                        <button class="app-btn btn-xp-orange" onclick="showToast('Occupied', '5 meja terisi')">
                            🪑 Occupied: <strong>5</strong>
                        </button>
                        <button class="app-btn btn-xp-red" onclick="showToast('Waiting', '3 meja menunggu')">
                            ⏳ Waiting: <strong>3</strong>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="window-statusbar">
            <span class="statusbar-section">RestauranApp</span>
            <span class="statusbar-section">Kelola restoran</span>
        </div>
        <div class="resizer" onmousedown="startResize(event, 'win-restaurant')"></div>
    </div>
</div>
    <div class="window" id="win-settings" style="top:50px;left:100px;width:600px;height:420px;display:none;">
        <div class="window-titlebar" onmousedown="startDrag(event, 'win-settings')">
            <svg class="window-icon" viewBox="0 0 16 16">
                <circle cx="8" cy="8" r="6" fill="#c0c0c0" stroke="#707070" stroke-width="1"/>
                <circle cx="8" cy="8" r="2" fill="#888"/>
            </svg>
            <span class="window-title">Settings - Admin Panel</span>
            <div class="window-controls">
                <button class="window-btn window-btn-max" onclick="minimizeWindow('win-settings')">_</button>
                <button class="window-btn window-btn-max" onclick="maximizeWindow('win-settings')">□</button>
                <button class="window-btn" onclick="closeWindow('win-settings')">✕</button>
            </div>
        </div>
        <div class="window-menubar">
            <span class="menu-item">File</span>
            <span class="menu-item">Edit</span>
            <span class="menu-item">View</span>
            <span class="menu-item">Help</span>
        </div>
        <div class="window-content">
            <div class="app-panel">
                <div class="app-section">
                    <div class="app-section-title">⚙️ PENGATURAN SISTEM</div>
                    <div class="btn-grid">
                        <button class="app-btn" onclick="showToast('User Management', 'Mengelola pengguna...')">
                            <svg viewBox="0 0 32 32">
                                <circle cx="16" cy="10" r="6" fill="#f5c890"/>
                                <ellipse cx="16" cy="26" rx="10" ry="6" fill="#3a6ea5"/>
                                <circle cx="26" cy="8" r="4" fill="#ffd700"/>
                                <text x="26" y="11" text-anchor="middle" font-size="6" fill="#000" font-weight="bold">+</text>
                            </svg>
                            User Management
                        </button>
                        <button class="app-btn" onclick="showToast('Role Management', 'Mengelola role...')">
                            <svg viewBox="0 0 32 32">
                                <rect x="4" y="4" width="10" height="10" rx="2" fill="#ffd700"/>
                                <rect x="18" y="4" width="10" height="10" rx="2" fill="#4db84d"/>
                                <rect x="4" y="18" width="10" height="10" rx="2" fill="#245edc"/>
                                <rect x="18" y="18" width="10" height="10" rx="2" fill="#cc2222"/>
                            </svg>
                            Role Management
                        </button>
                        <button class="app-btn" onclick="showToast('System Logs', 'Melihat log sistem...')">
                            <svg viewBox="0 0 32 32">
                                <rect x="4" y="2" width="24" height="28" rx="2" fill="#f5f5f5" stroke="#707070"/>
                                <rect x="8" y="6" width="16" height="2" fill="#245edc"/>
                                <rect x="8" y="10" width="12" height="1" fill="#aaa"/>
                                <rect x="8" y="13" width="14" height="1" fill="#aaa"/>
                                <rect x="8" y="16" width="10" height="1" fill="#aaa"/>
                                <rect x="8" y="19" width="14" height="1" fill="#aaa"/>
                            </svg>
                            System Logs
                        </button>
                        <button class="app-btn" onclick="showToast('Backup', 'Backup database...')">
                            <svg viewBox="0 0 32 32">
                                <rect x="4" y="8" width="24" height="20" rx="2" fill="#888" stroke="#555"/>
                                <rect x="6" y="10" width="20" height="16" fill="#222"/>
                                <circle cx="16" cy="18" r="4" fill="#4db84d"/>
                                <path d="M14 18 L16 20 L20 16" stroke="#fff" stroke-width="2" fill="none"/>
                            </svg>
                            Backup
                        </button>
                    </div>
                </div>
                <div class="app-section">
                    <div class="app-section-title">👥 DAFTAR PENGGUNA</div>
                    <table style="width:100%;font-size:12px;border-collapse:collapse;">
                        <thead>
                            <tr style="background:#ddd;">
                                <th style="padding:6px;border:1px solid #ccc;text-align:left;">Username</th>
                                <th style="padding:6px;border:1px solid #ccc;text-align:left;">Nama</th>
                                <th style="padding:6px;border:1px solid #ccc;text-align:left;">Role</th>
                                <th style="padding:6px;border:1px solid #ccc;text-align:left;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="users-table-body">
                            <tr>
                                <td style="padding:6px;border:1px solid #ccc;">admin</td>
                                <td style="padding:6px;border:1px solid #ccc;">Administrator</td>
                                <td style="padding:6px;border:1px solid #ccc;"><span class="user-role role-admin">Admin</span></td>
                                <td style="padding:6px;border:1px solid #ccc;"><button class="btn-xp" style="padding:2px 8px;font-size:10px;">Edit</button></td>
                            </tr>
                            <tr>
                                <td style="padding:6px;border:1px solid #ccc;">employee1</td>
                                <td style="padding:6px;border:1px solid #ccc;">John Mechanic</td>
                                <td style="padding:6px;border:1px solid #ccc;"><span class="user-role role-employee">Employee</span></td>
                                <td style="padding:6px;border:1px solid #ccc;"><button class="btn-xp" style="padding:2px 8px;font-size:10px;">Edit</button></td>
                            </tr>
                            <tr>
                                <td style="padding:6px;border:1px solid #ccc;">user1</td>
                                <td style="padding:6px;border:1px solid #ccc;">Regular User</td>
                                <td style="padding:6px;border:1px solid #ccc;"><span class="user-role role-user">User</span></td>
                                <td style="padding:6px;border:1px solid #ccc;"><button class="btn-xp" style="padding:2px 8px;font-size:10px;">Edit</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="window-statusbar">
            <span class="statusbar-section">Admin Panel</span>
            <span class="statusbar-section" id="admin-status-msg">Kelola sistem di sini</span>
        </div>
        <div class="resizer" onmousedown="startResize(event, 'win-settings')"></div>
    </div>
</div>

<!-- Taskbar -->
<div class="taskbar">
    <button class="start-button" onclick="toggleStartMenu()">
        <svg viewBox="0 0 20 20">
            <rect x="1" y="1" width="8" height="8" fill="#ff4444"/>
            <rect x="11" y="1" width="8" height="8" fill="#44ff44"/>
            <rect x="1" y="11" width="8" height="8" fill="#4444ff"/>
            <rect x="11" y="11" width="8" height="8" fill="#ffff44"/>
        </svg>
        Start
    </button>
    <div class="taskbar-divider"></div>
    <!-- User Info -->
    <div class="user-info" id="user-info" style="display:none;">
        <svg viewBox="0 0 20 20">
            <circle cx="10" cy="6" r="4" fill="#f5c890"/>
            <ellipse cx="10" cy="16" rx="6" ry="4" fill="#3a6ea5"/>
        </svg>
        <div class="user-info-text">
            <span id="user-name">User</span>
            <span class="user-role" id="user-role-badge">Employee</span>
        </div>
    </div>
    <!-- Quick Launch -->
    <button class="app-btn app-btn-small" onclick="openWindow('cargo')" title="CargoApp" style="height:28px;padding:2px 6px;">
        <svg viewBox="0 0 18 18">
            <rect x="2" y="5" width="14" height="10" rx="1" fill="#8b5cf6"/>
            <rect x="4" y="7" width="10" height="6" fill="#c4b5fd"/>
            <rect x="6" y="15" width="2" height="2" fill="#333"/>
            <rect x="10" y="15" width="2" height="2" fill="#333"/>
        </svg>
    </button>
    <button class="app-btn app-btn-small" onclick="openWindow('farmer')" title="FarmerApp" style="height:28px;padding:2px 6px;">
        <svg viewBox="0 0 18 18">
            <rect x="2" y="12" width="14" height="5" rx="1" fill="#228b22"/>
            <circle cx="6" cy="9" r="3" fill="#90ee90"/>
            <circle cx="12" cy="9" r="3" fill="#90ee90"/>
        </svg>
    </button>
    <button class="app-btn app-btn-small" onclick="openWindow('mechanic')" title="MechanicApp" style="height:28px;padding:2px 6px;">
        <svg viewBox="0 0 18 18">
            <rect x="2" y="4" width="14" height="11" rx="1" fill="#3a6ea5"/>
            <rect x="4" y="6" width="10" height="7" fill="#c8e0f5"/>
            <rect x="6" y="13" width="6" height="2" rx="1" fill="#245edc"/>
        </svg>
    </button>
    <button class="app-btn app-btn-small" onclick="openWindow('restaurant')" title="RestauranApp" style="height:28px;padding:2px 6px;">
        <svg viewBox="0 0 18 18">
            <ellipse cx="9" cy="13" rx="6" ry="2" fill="#8b4513"/>
            <path d="M5 11 Q5 6 9 6 Q13 6 13 11" fill="#cc2222"/>
            <rect x="7" y="4" width="4" height="3" rx="1" fill="#ffd700"/>
        </svg>
    </button>

    <!-- Taskbar Window Buttons -->
    <div id="taskbar-windows" style="display:flex;gap:2px;flex:1;"></div>

    <!-- System Tray -->
    <div class="tray-clock">
        <div class="clock-widget">
            🔊
        </div>
        <div class="clock-widget" id="tray-clock">
            --:----
        </div>
    </div>
</div>

<!-- Start Menu -->
<div class="start-menu" id="start-menu">
    <div class="start-menu-sidebar">
        <span>Start</span>
    </div>
    <div class="start-menu-programs">
        <div class="start-menu-title">Brothers Software</div>
        <div class="start-menu-item" onclick="openWindow('cargo');toggleStartMenu()">
            <svg viewBox="0 0 28 28">
                <rect x="4" y="8" width="20" height="14" rx="2" fill="#8b5cf6"/>
                <rect x="6" y="10" width="16" height="10" fill="#c4b5fd"/>
                <rect x="8" y="22" width="4" height="4" fill="#333"/>
                <rect x="16" y="22" width="4" height="4" fill="#333"/>
            </svg>
            CargoApp
        </div>
        <div class="start-menu-item" onclick="openWindow('farmer');toggleStartMenu()">
            <svg viewBox="0 0 28 28">
                <rect x="2" y="18" width="24" height="8" rx="1" fill="#8b4513"/>
                <circle cx="8" cy="14" r="5" fill="#90ee90"/>
                <circle cx="14" cy="12" r="5" fill="#90ee90"/>
                <circle cx="20" cy="14" r="5" fill="#90ee90"/>
                <rect x="13" y="6" width="2" height="6" fill="#8b4513"/>
            </svg>
            FarmerApp
        </div>
        <div class="start-menu-item" onclick="openWindow('mechanic');toggleStartMenu()">
            <svg viewBox="0 0 28 28">
                <rect x="2" y="6" width="24" height="18" rx="2" fill="#3a6ea5"/>
                <rect x="4" y="8" width="20" height="12" fill="#c8e0f5"/>
                <rect x="9" y="20" width="10" height="3" rx="1" fill="#245edc"/>
            </svg>
            MechanicApp
        </div>
        <div class="start-menu-item" onclick="openWindow('restaurant');toggleStartMenu()">
            <svg viewBox="0 0 28 28">
                <ellipse cx="14" cy="22" rx="10" ry="3" fill="#8b4513"/>
                <path d="M6 18 Q6 10 14 10 Q22 10 22 18" fill="#cc2222"/>
                <rect x="12" y="6" width="4" height="4" rx="1" fill="#ffd700"/>
            </svg>
            RestauranApp
        </div>
        <div class="start-menu-separator"></div>
        <div class="start-menu-item" onclick="openWindow('settings');toggleStartMenu()">
            <svg viewBox="0 0 28 28">
                <circle cx="14" cy="14" r="10" fill="#c0c0c0"/>
                <circle cx="14" cy="14" r="4" fill="#888"/>
                <rect x="12" y="2" width="4" height="5" rx="2" fill="#aaa"/>
                <rect x="12" y="21" width="4" height="5" rx="2" fill="#aaa"/>
                <rect x="2" y="12" width="5" height="4" rx="2" fill="#aaa"/>
                <rect x="21" y="12" width="5" height="4" rx="2" fill="#aaa"/>
            </svg>
            Settings
        </div>
        <div class="start-menu-separator"></div>
        <div class="start-menu-item" onclick="logout();toggleStartMenu()">
            <svg viewBox="0 0 28 28">
                <circle cx="14" cy="10" r="6" fill="#f5c890"/>
                <ellipse cx="14" cy="24" rx="8" ry="4" fill="#3a6ea5"/>
                <path d="M2 18 L10 14 L2 10" stroke="#cc2222" stroke-width="2" fill="none"/>
                <path d="M10 14 L26 14" stroke="#cc2222" stroke-width="2"/>
                <path d="M24 10 L26 14 L24 18" stroke="#cc2222" stroke-width="2" fill="none"/>
            </svg>
            Log Off
        </div>
    </div>
</div>

<script>
    // ===== CLOCK =====
    function updateClock() {
        const now = new Date();
        const h = now.getHours().toString().padStart(2, '0');
        const m = now.getMinutes().toString().padStart(2, '0');
        const s = now.getSeconds().toString().padStart(2, '0');
        const d = now.getDate().toString().padStart(2, '0');
        const mo = (now.getMonth() + 1).toString().padStart(2, '0');
        const y = now.getFullYear();
        document.getElementById('tray-clock').textContent = `${h}:${m}:${s}`;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // ===== START MENU =====
    function toggleStartMenu() {
        const menu = document.getElementById('start-menu');
        menu.classList.toggle('open');
    }

    document.addEventListener('click', (e) => {
        const menu = document.getElementById('start-menu');
        const startBtn = document.querySelector('.start-button');
        if (!menu.contains(e.target) && !startBtn.contains(e.target)) {
            menu.classList.remove('open');
        }
    });

    // ===== TOAST NOTIFICATION =====
    let toastTimer;
    function showToast(title, message) {
        const toast = document.getElementById('toast');
        document.getElementById('toast-title').textContent = title;
        document.getElementById('toast-body').textContent = message;
        toast.classList.add('show');
        document.getElementById('status-msg').textContent = message;

        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    // ===== WINDOW MANAGEMENT =====
    let zIndex = 100;
    const openWindows = {};

    function openWindow(type) {
        const win = document.getElementById(`win-${type}`);
        if (!win) return;

        win.style.display = 'block';
        bringToFront(`win-${type}`);
        updateTaskbar();

        // Calculator is now built-in, no need to load external content
    }

    function loadAppContent(containerId, url) {
        const container = document.getElementById(containerId);
        if (!container || container.dataset.loaded === 'true') return;

        container.dataset.loaded = 'true';
        container.innerHTML = '<div style="color:#58a6ff;padding:20px;font-family:Segoe UI,sans-serif;">Loading...</div>';

        // Get base URL for resolving relative paths
        const baseUrl = window.location.origin + '/brotherscompany/Brotherssoftware/';

        // Create unique scope ID for this content
        const scopeId = 'app-scope-' + Date.now();

        fetch(url)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Get styles from head
                const styles = doc.querySelectorAll('style');

                // Collect all CSS and scope it to our container
                let scopedCss = '';
                styles.forEach(style => {
                    let css = style.textContent;
                    // Scope all selectors to our container
                    css = scopeCssRules(css, '#' + scopeId);
                    scopedCss += css + '\n';
                });

                // Create scoped container
                const contentDiv = document.createElement('div');
                contentDiv.id = scopeId;
                contentDiv.style.cssText = 'width:100%;height:100%;background:#0d1117;box-sizing:border-box;overflow:auto;font-family:Inter,sans-serif;color:#c9d1d9;position:relative;z-index:1;';

                // Apply scoped CSS with reset styles
                const styleTag = document.createElement('style');
                styleTag.textContent = `
                    #${scopeId} {
                        width: 100%;
                        height: 100%;
                        background: #0d1117 !important;
                        box-sizing: border-box;
                        margin: 0 !important;
                        padding: 0 !important;
                        overflow: hidden !important;
                        position: relative !important;
                    }
                    #${scopeId} * {
                        box-sizing: border-box;
                    }
                    #${scopeId} body,
                    #${scopeId} html {
                        margin: 0 !important;
                        padding: 0 !important;
                        display: block !important;
                        justify-content: unset !important;
                        min-height: unset !important;
                        background: #0d1117 !important;
                        color: #c9d1d9 !important;
                        position: relative !important;
                        overflow: hidden !important;
                        height: 100% !important;
                    }
                    #${scopeId} .main-wrapper {
                        display: flex !important;
                        flex-wrap: wrap !important;
                        gap: 15px !important;
                        align-items: flex-start !important;
                        justify-content: flex-start !important;
                        width: 100% !important;
                        max-width: 100% !important;
                        padding: 10px !important;
                        margin: 0 !important;
                        height: calc(100% - 40px) !important;
                        overflow: auto !important;
                    }
                    #${scopeId} .calc-card {
                        background: #161b22 !important;
                        padding: 12px !important;
                        border-radius: 12px !important;
                        width: 280px !important;
                        min-width: 250px !important;
                        max-width: 280px !important;
                        border: 1px solid #30363d !important;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.3) !important;
                        flex-shrink: 0 !important;
                        height: fit-content !important;
                    }
                    #${scopeId} #receipt-container {
                        flex: 1 !important;
                        flex-wrap: wrap !important;
                        gap: 10px !important;
                        align-items: flex-start !important;
                        width: auto !important;
                        max-width: 400px !important;
                        min-width: 200px !important;
                        overflow: auto !important;
                        padding: 5px !important;
                        display: none !important;
                    }
                    #${scopeId} #receipt-container.receipt-visible {
                        display: flex !important;
                    }
                    #${scopeId} .receipt {
                        background: #fff !important;
                        color: #000 !important;
                        padding: 12px !important;
                        width: 260px !important;
                        font-family: 'Courier New', monospace !important;
                        font-size: 10px !important;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.3) !important;
                        flex-shrink: 0 !important;
                    }
                    #${scopeId} .floating-controls {
                        position: absolute !important;
                        bottom: 8px !important;
                        right: 8px !important;
                        display: flex !important;
                        flex-wrap: nowrap !important;
                        gap: 5px !important;
                        z-index: 100 !important;
                        padding: 0 !important;
                    }
                    #${scopeId} .floating-controls .btn {
                        padding: 5px 10px !important;
                        font-size: 9px !important;
                        border-radius: 4px !important;
                    }
                    #${scopeId} .btn-show {
                        background: #1f6feb !important;
                        color: white !important;
                    }
                    #${scopeId} .btn-reset {
                        background: #21262d !important;
                        color: #f85149 !important;
                        border: 1px solid #f85149 !important;
                    }
                    #${scopeId} #overlay {
                        position: fixed !important;
                        z-index: 9999999 !important;
                        top: 0 !important;
                        left: 0 !important;
                        width: 100vw !important;
                        height: 100vh !important;
                    }
                    #${scopeId} h2 {
                        color: #58a6ff !important;
                        font-size: 16px !important;
                        margin: 0 0 12px 0 !important;
                    }
                    #${scopeId} label {
                        font-size: 10px !important;
                        margin-bottom: 4px !important;
                    }
                    #${scopeId} select,
                    #${scopeId} input {
                        background: #010409 !important;
                        color: #fff !important;
                        border: 1px solid #30363d !important;
                        padding: 8px !important;
                        font-size: 12px !important;
                        margin-bottom: 10px !important;
                        width: 100% !important;
                    }
                    #${scopeId} .btn {
                        padding: 8px !important;
                        font-size: 11px !important;
                    }
                    #${scopeId} .btn-calc {
                        background: #58a6ff !important;
                    }
                    #${scopeId} .btn-log {
                        background: #238636 !important;
                    }
                    #${scopeId} .btn-add {
                        background: transparent !important;
                        border: 1px dashed #484f58 !important;
                        color: #8b949e !important;
                        width: 100% !important;
                    }
                    #${scopeId} .fs-side-btn {
                        display: none !important;
                    }
                    #${scopeId} #hasil-display {
                        font-size: 20px !important;
                        margin-top: 8px !important;
                    }
                ` + scopedCss;
                contentDiv.appendChild(styleTag);

                // Get body content
                const body = doc.body;
                const scripts = body.querySelectorAll('script[src]');
                const inlineScripts = body.querySelectorAll('script:not([src])');

                // Clone body children (excluding scripts)
                Array.from(body.children).forEach(child => {
                    if (child.tagName !== 'SCRIPT') {
                        const cloned = child.cloneNode(true);
                        contentDiv.appendChild(cloned);
                    }
                });

                container.innerHTML = '';
                container.appendChild(contentDiv);

                // Execute inline scripts
                inlineScripts.forEach(script => {
                    const newScript = document.createElement('script');
                    Array.from(script.attributes).forEach(attr => {
                        newScript.setAttribute(attr.name, attr.value);
                    });
                    newScript.textContent = script.textContent;
                    container.appendChild(newScript);
                });

                // Load external scripts
                scripts.forEach(script => {
                    const newScript = document.createElement('script');
                    const src = script.getAttribute('src');
                    if (src.startsWith('http')) {
                        newScript.src = src;
                    } else {
                        newScript.src = baseUrl + src;
                    }
                    container.appendChild(newScript);
                });

                // Fix toggleReceipt function to work with scoped container
                setTimeout(() => {
                    const calcContent = document.getElementById(scopeId);
                    if (calcContent) {
                        const floatingDiv = calcContent.querySelector('.floating-controls');
                        const receiptContainer = calcContent.querySelector('#receipt-container');

                        if (floatingDiv) {
                            // Position floating controls inside the container
                            floatingDiv.style.cssText = 'position:absolute !important;bottom:8px !important;right:8px !important;display:flex !important;flex-wrap:nowrap !important;gap:5px !important;z-index:100 !important;';

                            // Style existing buttons
                            floatingDiv.querySelectorAll('.btn').forEach(btn => {
                                btn.style.cssText = 'padding:5px 10px !important;font-size:9px !important;border-radius:4px !important;';
                            });

                            // Style show/hide button
                            const btnShow = floatingDiv.querySelector('.btn-show');
                            if (btnShow) {
                                btnShow.style.cssText = 'padding:5px 10px !important;font-size:9px !important;border-radius:4px !important;background:#1f6feb !important;color:white !important;';
                                // Override original onclick to toggle class
                                btnShow.onclick = function(e) {
                                    e.stopPropagation();
                                    if (receiptContainer) {
                                        receiptContainer.classList.toggle('receipt-visible');
                                    }
                                };
                            }

                            // Style reset button
                            const btnReset = floatingDiv.querySelector('.btn-reset');
                            if (btnReset) {
                                btnReset.style.cssText = 'padding:5px 10px !important;font-size:9px !important;border-radius:4px !important;background:#21262d !important;color:#f85149 !important;border:1px solid #f85149 !important;';
                            }

                            // Add Download Struk button
                            if (!floatingDiv.querySelector('.btn-download-struk')) {
                                const downloadBtn = document.createElement('button');
                                downloadBtn.className = 'btn btn-download-struk';
                                downloadBtn.style.cssText = 'padding:5px 10px !important;font-size:9px !important;border-radius:4px !important;background:#ffd700 !important;color:#000 !important;font-weight:bold !important;';
                                downloadBtn.textContent = 'Download';
                                downloadBtn.onclick = function(e) {
                                    e.stopPropagation();
                                    // Call the downloadReceipt function from the loaded script
                                    if (typeof downloadReceipt === 'function') {
                                        downloadReceipt();
                                    } else {
                                        // Try to find receipt and download manually
                                        const receipt = calcContent.querySelector('#receipt-wrap');
                                        if (receipt && typeof html2canvas !== 'undefined') {
                                            const delBtns = receipt.querySelectorAll('.del-btn');
                                            delBtns.forEach(b => b.style.visibility = 'hidden');
                                            html2canvas(receipt, { backgroundColor: "#ffffff", scale: 3, useCORS: true, scrollY: 0, height: receipt.scrollHeight }).then(canvas => {
                                                const link = document.createElement('a');
                                                link.download = `Struk-AutoJuice-${Date.now()}.png`;
                                                link.href = canvas.toDataURL("image/png");
                                                link.click();
                                                delBtns.forEach(b => b.style.visibility = 'visible');
                                            });
                                        }
                                    }
                                };
                                floatingDiv.appendChild(downloadBtn);
                            }
                        }
                    }
                }, 800);
            })
            .catch(error => {
                console.error('Error loading content:', error);
                container.innerHTML = '<div style="color:#f85149;padding:20px;font-family:Segoe UI,sans-serif;">Error loading content. Please check if the file exists.</div>';
            });
    }

    // Helper function to scope CSS rules to a specific container
    function scopeCssRules(css, scope) {
        let result = '';

        // Process CSS rule by rule
        const ruleRegex = /([^{]+){([^}]+)}/g;
        let match;

        while ((match = ruleRegex.exec(css)) !== null) {
            const selectors = match[1].trim();
            const rules = match[2].trim();

            // Skip @import rules
            if (selectors.toLowerCase().startsWith('@import')) {
                result += match[0] + '\n';
                continue;
            }

            // Skip @keyframes
            if (selectors.toLowerCase().startsWith('@keyframes')) {
                result += match[0] + '\n';
                continue;
            }

            // Skip @media rules (simplified - just skip them to avoid complexity)
            if (selectors.toLowerCase().startsWith('@media')) {
                result += match[0] + '\n';
                continue;
            }

            // Split multiple selectors
            const selectorList = selectors.split(',').map(s => s.trim()).filter(s => s);
            const scopedSelectors = selectorList.map(s => {
                // Don't scope already scoped selectors
                if (s.startsWith(scope) || s.startsWith('.app-scope-')) {
                    return s;
                }
                // Remove html, :root, body, * from front and scope properly
                let cleanSelector = s
                    .replace(/^html\s*/, '')
                    .replace(/^:root\s*/, '')
                    .replace(/^body\s*/, '')
                    .trim();

                // Handle * selector
                if (s === '*' || s === '* ') {
                    return scope + ' *';
                }

                // If it's empty after cleaning, use just the scope
                if (!cleanSelector) {
                    return scope;
                }

                return scope + ' ' + cleanSelector;
            });

            result += scopedSelectors.join(', ') + ' { ' + rules + ' }\n';
        }

        return result;
    }

    function closeWindow(id) {
        const win = document.getElementById(id);
        if (win) {
            win.style.display = 'none';
            delete openWindows[id];

            // Reset loaded state for content containers
            const content = win.querySelector('[data-loaded]');
            if (content) {
                content.dataset.loaded = 'false';
            }

            updateTaskbar();
        }
    }

    function minimizeWindow(id) {
        const win = document.getElementById(id);
        if (win) {
            win.style.display = 'none';
            updateTaskbar();
        }
    }

    function maximizeWindow(id) {
        const win = document.getElementById(id);
        if (win) {
            if (win.dataset.maximized === 'true') {
                win.style.top = win.dataset.originalTop;
                win.style.left = win.dataset.originalLeft;
                win.style.width = win.dataset.originalWidth;
                win.style.height = win.dataset.originalHeight;
                win.dataset.maximized = 'false';
            } else {
                win.dataset.originalTop = win.style.top;
                win.dataset.originalLeft = win.style.left;
                win.dataset.originalWidth = win.style.width;
                win.dataset.originalHeight = win.style.height;
                win.style.top = '0';
                win.style.left = '0';
                win.style.width = '100%';
                win.style.height = 'calc(100vh - 42px)';
                win.dataset.maximized = 'true';
            }
        }
    }

    function bringToFront(id) {
        zIndex++;
        const win = document.getElementById(id);
        if (win) {
            win.style.zIndex = zIndex;
            document.querySelectorAll('.window').forEach(w => w.classList.add('inactive'));
            win.classList.remove('inactive');
        }
    }

    function updateTaskbar() {
        const container = document.getElementById('taskbar-windows');
        container.innerHTML = '';

        document.querySelectorAll('.window').forEach(win => {
            // Skip login screen and settings (only accessible to admin)
            const winId = win.id;
            if (winId === 'login-screen') return;
            if (winId === 'win-settings' && (!currentUser || currentUser.role !== 'admin')) return;

            if (win.style.display !== 'none') {
                const title = win.querySelector('.window-title').textContent;
                const btn = document.createElement('button');
                btn.className = 'app-btn app-btn-small';
                btn.style.maxWidth = '180px';
                btn.style.overflow = 'hidden';
                btn.style.textOverflow = 'ellipsis';
                btn.style.whiteSpace = 'nowrap';
                btn.onclick = () => bringToFront(winId);
                btn.innerHTML = `<span style="font-size:11px;font-weight:700;overflow:hidden;text-overflow:ellipsis;">${title}</span>`;
                container.appendChild(btn);
            }
        });
    }

    // ===== DRAG & RESIZE =====
    let dragTarget = null, dragOffsetX = 0, dragOffsetY = 0;
    let resizeTarget = null;

    function startDrag(e, id) {
        if (e.target.closest('.window-controls') || e.target.closest('.window-menubar')) return;
        dragTarget = id;
        const win = document.getElementById(id);
        dragOffsetX = e.clientX - win.offsetLeft;
        dragOffsetY = e.clientY - win.offsetTop;
        bringToFront(id);
    }

    function startResize(e, id) {
        e.stopPropagation();
        resizeTarget = id;
        bringToFront(id);
    }

    document.addEventListener('mousemove', (e) => {
        if (dragTarget) {
            const win = document.getElementById(dragTarget);
            win.style.left = (e.clientX - dragOffsetX) + 'px';
            win.style.top = (e.clientY - dragOffsetY) + 'px';
        }
        if (resizeTarget) {
            const win = document.getElementById(resizeTarget);
            const rect = win.getBoundingClientRect();
            win.style.width = Math.max(400, e.clientX - rect.left) + 'px';
            win.style.height = Math.max(250, e.clientY - rect.top) + 'px';
        }
    });

    document.addEventListener('mouseup', () => {
        dragTarget = null;
        resizeTarget = null;
    });

    // ===== RBAC - Role-Based Access Control =====
    const rolePermissions = {
        'admin': {
            windows: ['cargo', 'farmer', 'mechanic', 'restaurant', 'laporan', 'member', 'adminpanel', 'settings', 'daftarpekerja', 'tambahpekerja', 'laporankerja', 'keuangan'],
            actions: ['create', 'edit', 'delete', 'view', 'manage_users', 'settings']
        },
        'employee': {
            windows: ['cargo', 'farmer', 'mechanic', 'restaurant', 'laporan', 'member'],
            actions: ['create', 'edit', 'view', 'checkin', 'checkout']
        }
    };

    let currentUser = null;

    function applyRBAC() {
        if (!currentUser) return;

        const permissions = rolePermissions[currentUser.role];
        if (!permissions) return;

        const allowedWindows = permissions.windows;

        // Hide desktop icons that user cannot access
        document.querySelectorAll('.desktop-icon').forEach(icon => {
            const onclick = icon.getAttribute('ondblclick') || '';
            const windowType = onclick.match(/openWindow\('(\w+)'\)/)?.[1];
            if (windowType && !allowedWindows.includes(windowType)) {
                icon.style.display = 'none';
            }
        });

        // Hide start menu items that user cannot access
        document.querySelectorAll('.start-menu-item').forEach(item => {
            const onclick = item.getAttribute('onclick') || '';
            const windowType = onclick.match(/openWindow\('(\w+)'\)/)?.[1];
            if (windowType && !allowedWindows.includes(windowType)) {
                item.style.display = 'none';
            }
        });

        // Hide settings for non-admin users
        if (currentUser.role !== 'admin') {
            const settingsIcon = document.querySelector('.desktop-icon[ondblclick="openWindow(\'settings\')"]');
            if (settingsIcon) settingsIcon.style.display = 'none';
        }

        // Show/hide Admin-only desktop icons based on role
        document.querySelectorAll('.admin-only-icon').forEach(icon => {
            if (currentUser.role === 'admin') {
                icon.style.display = 'flex';
            } else {
                icon.style.display = 'none';
            }
        });
    }

    function logout() {
        // Redirect ke logout site
        window.location.href = '../auth/logout.php';
    }

    // ===== INIT =====
    // Check for auto-login from Brotherscompany site (PHP session)
    const autoLoginData = <?php echo $autoLogin ? json_encode($autoUser) : 'null'; ?>;

    if (autoLoginData) {
        // Auto-login employee/admin dari session brotherscompany site
        currentUser = autoLoginData;
        document.getElementById('desktop').style.display = 'flex';
        document.getElementById('user-info').style.display = 'flex';
        document.getElementById('user-name').textContent = autoLoginData.name;

        const roleBadge = document.getElementById('user-role-badge');
        roleBadge.textContent = autoLoginData.role === 'admin' ? 'Admin' : 'Employee';
        roleBadge.className = 'user-role role-' + autoLoginData.role;

        applyRBAC();
        showToast('Login Otomatis', `Selamat datang, ${autoLoginData.name}! Role: ${autoLoginData.role}`);
    }
    updateTaskbar();
</script>

<?php endif; // end accessDenied ?>

</body>
</html>