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
            width: 16px;
            height: 16px;
            cursor: nw-resize;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"><path d="M14 14L14 8L8 14Z" fill="%23c0c0c0"/><path d="M14 8L8 8L14 14Z" fill="%23a0a0a0"/></svg>');
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
                <!-- Dashboard Section -->
                <div class="app-section">
                    <div class="app-section-title">📊 DASHBOARD UTAMA</div>
                    <div class="btn-grid">
                        <button class="app-btn" onclick="showToast('New Job', 'Membuat pekerjaan baru...')">
                            <svg viewBox="0 0 32 32">
                                <rect x="2" y="8" width="28" height="22" rx="2" fill="#2d8a2d" stroke="#1a5c1a" stroke-width="1"/>
                                <rect x="2" y="4" width="28" height="8" rx="2" fill="#3aba3a"/>
                                <rect x="8" y="14" width="16" height="2" fill="#fff"/>
                                <rect x="8" y="18" width="12" height="2" fill="#fff"/>
                                <rect x="8" y="22" width="14" height="2" fill="#fff"/>
                                <rect x="8" y="26" width="8" height="2" fill="#fff"/>
                            </svg>
                            New Job
                        </button>
                        <button class="app-btn" onclick="showToast('Active Jobs', 'Melihat daftar pekerjaan aktif...')">
                            <svg viewBox="0 0 32 32">
                                <rect x="4" y="2" width="24" height="28" rx="2" fill="#245edc" stroke="#1038a0" stroke-width="1"/>
                                <rect x="8" y="6" width="16" height="2" fill="#a8c8e8"/>
                                <rect x="8" y="10" width="12" height="2" fill="#fff"/>
                                <rect x="8" y="14" width="14" height="2" fill="#fff"/>
                                <rect x="8" y="18" width="10" height="2" fill="#fff"/>
                                <rect x="8" y="22" width="14" height="2" fill="#fff"/>
                                <rect x="8" y="26" width="8" height="2" fill="#ffd700"/>
                            </svg>
                            Active Jobs
                        </button>
                        <button class="app-btn" onclick="showToast('Queue', 'Melihat antrian pekerjaan...')">
                            <svg viewBox="0 0 32 32">
                                <rect x="4" y="4" width="24" height="24" rx="2" fill="#e8b828" stroke="#b07800" stroke-width="1"/>
                                <rect x="4" y="4" width="24" height="6" rx="2" fill="#ffd700"/>
                                <circle cx="10" cy="18" r="4" fill="#fff" stroke="#707070" stroke-width="1"/>
                                <circle cx="22" cy="18" r="4" fill="#fff" stroke="#707070" stroke-width="1"/>
                                <circle cx="16" cy="24" r="4" fill="#fff" stroke="#707070" stroke-width="1"/>
                                <path d="M10 18 L22 18 M16 24 L22 18" stroke="#245edc" stroke-width="1"/>
                            </svg>
                            Queue
                        </button>
                        <button class="app-btn" onclick="showToast('Schedule', 'Membuka jadwal kerja...')">
                            <svg viewBox="0 0 32 32">
                                <rect x="4" y="4" width="24" height="24" rx="2" fill="#f5f5f5" stroke="#707070" stroke-width="1"/>
                                <rect x="4" y="4" width="24" height="6" rx="2" fill="#cc2222"/>
                                <rect x="8" y="14" width="16" height="2" fill="#ccc"/>
                                <rect x="8" y="18" width="12" height="2" fill="#ccc"/>
                                <rect x="8" y="22" width="14" height="2" fill="#ccc"/>
                                <rect x="8" y="10" width="3" height="3" fill="#ffd700"/>
                                <rect x="13" y="10" width="3" height="3" fill="#ffd700"/>
                                <rect x="18" y="10" width="3" height="3" fill="#ffd700"/>
                                <rect x="8" y="14" width="3" height="3" fill="#ffd700"/>
                                <rect x="13" y="14" width="3" height="3" fill="#ffd700"/>
                                <rect x="18" y="14" width="3" height="3" fill="#ffd700"/>
                            </svg>
                            Schedule
                        </button>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="app-section">
                    <div class="app-section-title">⚡ QUICK ACTIONS</div>
                    <div class="btn-grid">
                        <button class="app-btn" onclick="showToast('Check In', 'Check-in mekanik dicatat!')">
                            <svg viewBox="0 0 32 32">
                                <circle cx="16" cy="12" r="8" fill="#f5c890" stroke="#c8a060" stroke-width="1"/>
                                <ellipse cx="16" cy="28" rx="10" ry="4" fill="#3a6ea5"/>
                                <path d="M12 11 Q16 15 20 11" stroke="#333" stroke-width="1" fill="none"/>
                                <circle cx="13" cy="10" r="1" fill="#333"/>
                                <circle cx="19" cy="10" r="1" fill="#333"/>
                            </svg>
                            Check In
                        </button>
                        <button class="app-btn" onclick="showToast('Check Out', 'Check-out mekanic dicatat!')">
                            <svg viewBox="0 0 32 32">
                                <circle cx="16" cy="12" r="8" fill="#f5c890" stroke="#c8a060" stroke-width="1"/>
                                <ellipse cx="16" cy="28" rx="10" ry="4" fill="#cc2222"/>
                                <path d="M12 11 Q16 15 20 11" stroke="#333" stroke-width="1" fill="none"/>
                                <circle cx="13" cy="10" r="1" fill="#333"/>
                                <circle cx="19" cy="10" r="1" fill="#333"/>
                            </svg>
                            Check Out
                        </button>
                        <button class="app-btn" onclick="showToast('Parts Request', 'Meminta spare part...')">
                            <svg viewBox="0 0 32 32">
                                <circle cx="16" cy="16" r="12" fill="#d4a017" stroke="#8b6914" stroke-width="1"/>
                                <circle cx="16" cy="16" r="4" fill="#8b6914"/>
                                <rect x="14" y="2" width="4" height="6" rx="2" fill="#aaa"/>
                                <rect x="14" y="24" width="4" height="6" rx="2" fill="#aaa"/>
                                <rect x="2" y="14" width="6" height="4" rx="2" fill="#aaa"/>
                                <rect x="24" y="14" width="6" height="4" rx="2" fill="#aaa"/>
                            </svg>
                            Parts Request
                        </button>
                        <button class="app-btn" onclick="showToast('Print Job', 'Mencetak dokumen pekerjaan...')">
                            <svg viewBox="0 0 32 32">
                                <rect x="4" y="4" width="24" height="20" rx="2" fill="#c0c0c0" stroke="#707070" stroke-width="1"/>
                                <rect x="8" y="8" width="16" height="12" fill="#f5f5f5" stroke="#707070" stroke-width="1"/>
                                <rect x="4" y="24" width="24" height="4" rx="1" fill="#aaa"/>
                                <rect x="6" y="28" width="8" height="4" rx="1" fill="#888"/>
                                <rect x="10" y="11" width="12" height="1" fill="#ccc"/>
                                <rect x="10" y="13" width="10" height="1" fill="#ccc"/>
                                <rect x="10" y="15" width="8" height="1" fill="#ccc"/>
                            </svg>
                            Print Job
                        </button>
                        <button class="app-btn" onclick="showToast('Invoice', 'Membuat invoice...')">
                            <svg viewBox="0 0 32 32">
                                <rect x="4" y="2" width="24" height="28" rx="2" fill="#ffd700" stroke="#b07800" stroke-width="1"/>
                                <rect x="4" y="2" width="24" height="6" rx="2" fill="#e8b828"/>
                                <text x="16" y="7" text-anchor="middle" font-size="4" fill="#8b6914" font-weight="bold">$</text>
                                <rect x="8" y="10" width="16" height="1" fill="#c8a000"/>
                                <rect x="8" y="13" width="12" height="1" fill="#c8a000"/>
                                <rect x="8" y="16" width="14" height="1" fill="#c8a000"/>
                                <rect x="8" y="20" width="10" height="1" fill="#c8a000"/>
                                <rect x="8" y="23" width="16" height="2" fill="#cc2222"/>
                            </svg>
                            Invoice
                        </button>
                        <button class="app-btn" onclick="showToast('History', 'Membuka riwayat...')">
                            <svg viewBox="0 0 32 32">
                                <circle cx="16" cy="16" r="12" fill="#f5f5f5" stroke="#707070" stroke-width="1"/>
                                <circle cx="16" cy="16" r="10" fill="none" stroke="#ccc" stroke-width="1"/>
                                <line x1="16" y1="16" x2="16" y2="8" stroke="#333" stroke-width="2"/>
                                <line x1="16" y1="16" x2="22" y2="16" stroke="#707070" stroke-width="2"/>
                                <circle cx="16" cy="16" r="2" fill="#245edc"/>
                            </svg>
                            History
                        </button>
                    </div>
                </div>

                <!-- Status -->
                <div class="app-section">
                    <div class="app-section-title">📈 STATUS KERJA</div>
                    <div class="btn-grid">
                        <button class="app-btn btn-xp-green" onclick="showToast('Jobs Done', '8 pekerjaan selesai hari ini!')">
                            ✅ Done Today: <strong>8</strong>
                        </button>
                        <button class="app-btn btn-xp-orange" onclick="showToast('In Progress', '5 pekerjaan sedang berjalan')">
                            🔄 In Progress: <strong>5</strong>
                        </button>
                        <button class="app-btn btn-xp-red" onclick="showToast('Pending', '3 pekerjaan tertunda')">
                            ⏳ Pending: <strong>3</strong>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="window-statusbar">
            <span class="statusbar-section">Ready</span>
            <span class="statusbar-section" id="status-msg">Selamat datang di Brothers Software</span>
        </div>
        <div class="resizer" onmousedown="startResize(event, 'win-mechanic')"></div>
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
    }

    function closeWindow(id) {
        const win = document.getElementById(id);
        if (win) {
            win.style.display = 'none';
            delete openWindows[id];
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
            windows: ['cargo', 'farmer', 'mechanic', 'restaurant', 'settings'],
            actions: ['create', 'edit', 'delete', 'view', 'manage_users', 'settings']
        },
        'employee': {
            windows: ['cargo', 'farmer', 'mechanic', 'restaurant'],
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