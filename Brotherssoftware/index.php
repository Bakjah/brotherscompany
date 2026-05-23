<!DOCTYPE html>
<?php
// Brothers Software - Access Control
// Only admin and employee roles can access

session_start();

$currentRole = $_SESSION['role'] ?? null;
$currentUserId = $_SESSION['user_id'] ?? null;

$autoLogin = false;
$autoUser = null;
$accessDenied = false;
$notLoggedIn = false;

// Check if user is logged in
if (!$currentUserId || !$currentRole) {
    // User not logged in
    $notLoggedIn = true;
    $accessDenied = true;
} elseif ($currentRole === 'admin' || $currentRole === 'employee') {
    // Allow admin and employee to access
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
            margin: 0;
            padding: 0;
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
            position: relative;
            overflow: hidden;
        }

        /* ===== DESKTOP ICONS ===== */
        .desktop-icons {
            position: absolute;
            top: 0;
            left: 0;
            width: 90px;
            height: 100%;
            display: flex;
            flex-direction: column;
            flex-wrap: wrap;
            align-content: flex-start;
            gap: 5px;
            padding: 10px;
            overflow-y: auto;
            z-index: 10;
        }

        /* ===== WINDOWS AREA ===== */
        .windows-area {
            position: absolute;
            top: 0;
            left: 100px;
            right: 0;
            bottom: 42px;
            overflow: hidden;
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
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
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
            left: 0;
            width: 400px;
            background: linear-gradient(180deg, #d4d0c8 0%, #c0bfb8 100%);
            border: 1px solid;
            border-color: #fff #808080 #808080 #fff;
            box-shadow: 2px 2px 8px rgba(0,0,0,0.4);
            z-index: 200;
            font-family: 'Segoe UI', Tahoma, sans-serif;
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
            border-right: 1px solid #1e4d7a;
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
            padding: 0;
            background: linear-gradient(180deg, #e8e4dc 0%, #d4d0c8 100%);
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
            font-size: 12px;
            color: #000;
            height: 32px;
            box-sizing: border-box;
        }

        .start-menu-item:hover {
            background: linear-gradient(180deg, #316ac5 0%, #1a5499 100%);
            color: #fff;
        }

        .start-menu-item svg {
            width: 28px;
            height: 28px;
            flex-shrink: 0;
        }

        .start-menu-item span {
            flex: 1;
        }

        .start-menu-item .arrow {
            font-size: 10px;
            opacity: 0.7;
        }

        .start-menu-item:hover .arrow {
            opacity: 1;
        }

        .start-menu-separator {
            height: 1px;
            background: linear-gradient(90deg, transparent, #bbb, transparent);
            margin: 4px 10px;
        }

        .start-menu-right {
            width: 180px;
            background: linear-gradient(180deg, #c8c4bc 0%, #b8b4ac 100%);
            border-left: 1px solid #808080;
            padding: 6px 0;
        }

        .start-menu-right-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            cursor: pointer;
            font-size: 11px;
            color: #000;
            height: 32px;
            box-sizing: border-box;
        }

        .start-menu-right-item:hover {
            background: linear-gradient(180deg, #316ac5 0%, #1a5499 100%);
            color: #fff;
        }

        .start-menu-right-item svg {
            width: 24px;
            height: 24px;
        }

        .start-menu-logout {
            margin-top: auto;
            border-top: 1px solid #bbb;
            padding-top: 4px;
        }

        .start-menu-shutdown {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin: 8px 12px 6px;
            padding: 6px 12px;
            background: linear-gradient(180deg, #c8c4bc 0%, #b8b4ac 100%);
            border: 1px solid;
            border-color: #fff #808080 #808080 #fff;
            cursor: pointer;
            font-size: 11px;
            color: #000;
        }

        .start-menu-shutdown:hover {
            background: linear-gradient(180deg, #316ac5 0%, #1a5499 100%);
            color: #fff;
        }

        .start-menu-shutdown svg {
            width: 20px;
            height: 20px;
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
                <span class="login-title" style="color:#fff;"><?php echo $notLoggedIn ? 'Login Required' : 'Access Denied'; ?></span>
            </div>
        </div>
        <div class="login-body" style="text-align:center;">
            <svg viewBox="0 0 80 80" style="width:80px;height:80px;margin-bottom:16px;">
                <circle cx="40" cy="40" r="36" fill="#fee2e2" stroke="#cc0000" stroke-width="2"/>
                <rect x="35" y="20" width="10" height="28" rx="5" fill="#cc0000"/>
                <circle cx="40" cy="56" r="5" fill="#cc0000"/>
            </svg>
            <h2 style="color:#991b1b;margin-bottom:8px;">
                <?php echo $notLoggedIn ? 'Silakan Login Dulu!' : 'Akses Ditolak!'; ?>
            </h2>
            <p style="color:#666;margin-bottom:16px;line-height:1.6;">
                <?php if ($notLoggedIn): ?>
                Anda harus <strong>login</strong> terlebih dahulu untuk mengakses Brothers Software.<br>
                Hanya <strong>Employee</strong> dan <strong>Admin</strong> yang dapat mengakses sistem ini.
                <?php else: ?>
                Anda tidak memiliki izin untuk mengakses Brothers Software.<br>
                Hanya <strong>Employee</strong> dan <strong>Admin</strong> yang dapat mengakses sistem ini.<br>
                <small style="color:#999;">(Role Anda: <?php echo htmlspecialchars(ucfirst($currentRole)); ?>)</small>
                <?php endif; ?>
            </p>
            <div style="padding:12px;background:#fef2f2;border:2px solid #cc0000;border-radius:8px;margin-bottom:16px;">
                <small style="color:#991b1b;">
                    <?php if ($notLoggedIn): ?>
                    <strong>Status:</strong> Belum login<br>
                    <?php else: ?>
                    <strong>Role Anda:</strong> <?php echo htmlspecialchars(ucfirst($currentRole)); ?><br>
                    <strong>Username:</strong> <?php echo htmlspecialchars($_SESSION['username'] ?? 'Unknown'); ?>
                    <?php endif; ?>
                </small>
            </div>
            <a href="../index.php" class="btn-xp btn-xp-primary" style="text-decoration:none;display:inline-block;padding:10px 24px;">
                ← <?php echo $notLoggedIn ? 'Login di Sini' : 'Kembali ke Beranda'; ?>
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

        <!-- ManagerApp - Admin Only -->
        <div class="desktop-icon admin-only-icon" ondblclick="openWindow('manager')">
            <svg class="icon-img" viewBox="0 0 48 48">
                <rect x="6" y="4" width="36" height="40" rx="3" fill="#b07800" stroke="#8b6914" stroke-width="2"/>
                <rect x="10" y="8" width="28" height="8" fill="#ffd700"/>
                <rect x="10" y="20" width="12" height="12" rx="2" fill="#228b22"/>
                <rect x="26" y="20" width="12" height="12" rx="2" fill="#cc2222"/>
                <rect x="10" y="34" width="12" height="6" rx="2" fill="#245edc"/>
                <rect x="26" y="34" width="12" height="6" rx="2" fill="#8b5cf6"/>
            </svg>
            <span class="icon-label">ManagerApp</span>
        </div>

        <!-- Admin Apps - Only visible for Admin -->
        <!-- Employee -->
        <div class="desktop-icon admin-only-icon" ondblclick="openWindow('employee')" style="display:none;">
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
            <span class="icon-label">Employee</span>
        </div>

        <!-- Laporan Kerja -->
        <div class="desktop-icon admin-only-icon" ondblclick="openWindow('laporankerja')" style="display:none;">
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

        <!-- Price & Salary Config -->
        <div class="desktop-icon admin-only-icon" ondblclick="openWindow('priceconfig')" style="display:none;">
            <svg class="icon-img" viewBox="0 0 48 48">
                <rect x="4" y="6" width="40" height="36" rx="3" fill="#9333ea" stroke="#7c3aed" stroke-width="2"/>
                <rect x="8" y="10" width="32" height="8" fill="#a855f7"/>
                <text x="24" y="18" text-anchor="middle" font-size="8" font-weight="bold" fill="#fff">PRICE</text>
                <rect x="10" y="22" width="28" height="4" fill="#c084fc"/>
                <rect x="10" y="30" width="14" height="4" fill="#c084fc"/>
                <rect x="28" y="30" width="10" height="4" fill="#c084fc"/>
                <rect x="10" y="36" width="28" height="4" fill="#e9d5ff"/>
            </svg>
            <span class="icon-label">Price &<br>Salary</span>
        </div>
    </div>

    <!-- Windows Area -->
    <div class="windows-area" id="windows-area">
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
                        MechanicApp menyediakan akses ke Calculator untuk perhitungan, LaporanApp untuk laporan kerja, dan MemberApp untuk manajemen member.
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
                    .calc-config-row { display: flex; align-items: center; margin-bottom: 8px; gap: 8px; }
                    .calc-config-row label { flex: 1; font-size: 11px; color: #8b949e; }
                    .calc-config-row input { width: 80px; padding: 5px; border-radius: 4px; border: 1px solid #30363d; background: #010409; color: #fff; font-size: 11px; text-align: center; }
                    .calc-show-value { min-width: 50px; padding: 5px 8px; background: #238636; color: #fff; border-radius: 4px; font-size: 11px; font-weight: bold; text-align: center; }
                </style>

                <div class="calc-app-wrapper">
                    <div class="calc-card-standalone" style="position:relative;">
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
                        <div class="calc-floating" style="position:absolute;bottom:8px;left:50%;transform:translateX(-50%);display:flex;gap:5px;">
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
            let calcConfig = {
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

            // Load config from database
            async function loadCalcConfigFromDB() {
                // First check localStorage for immediate effect (set by savePriceConfig)
                const stored = localStorage.getItem('calcConfig');
                if (stored) {
                    try {
                        const parsed = JSON.parse(stored);
                        if (parsed.multipliers) Object.assign(calcConfig.multipliers, parsed.multipliers);
                        if (parsed.sparepartPricePerUnit) calcConfig.sparepartPricePerUnit = parsed.sparepartPricePerUnit;
                    } catch (e) {}
                }
                // Then fetch from DB to ensure latest values
                try {
                    const res = await fetch('api/price_config_api.php?action=get_all');
                    const result = await res.json();
                    if (result.success && result.data && result.data.length > 0) {
                        result.data.forEach(item => {
                            if (calcConfig.multipliers.hasOwnProperty(item.jenis_layanan)) {
                                calcConfig.multipliers[item.jenis_layanan] = parseFloat(item.multiplier) || 3.0;
                            }
                        });
                    }
                    const spareRes = await fetch('api/sparepart_config_api.php?action=get');
                    const spareResult = await spareRes.json();
                    if (spareResult.success && spareResult.data) {
                        calcConfig.sparepartPricePerUnit = parseFloat(spareResult.data.harga_per_unit) || 2.0;
                    }
                } catch (e) {
                    console.error('Failed to load config from DB:', e);
                }
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

            // Auto-load config on page load (wait for DOM)
            window.addEventListener('load', function() {
                setTimeout(loadCalcConfigFromDB, 500);
            });

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

            // Load config when calculator window opens
            document.getElementById('win-calculator').addEventListener('focus', loadCalcConfigFromDB);

            // Auto-load config on page load (wait a bit for DOM)
            window.addEventListener('load', function() {
                setTimeout(loadCalcConfigFromDB, 500);
            });
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
                            <input type="text" id="laporan-nama" placeholder="Ketik nama mechanic..." autocomplete="off" oninput="checkNamaMechanic()">
                            <div id="laporan-nama-info" style="font-size:10px;margin-top:4px;color:#8b949e;"></div>
                        </div>
                        <div class="laporan-field">
                            <label>Compo Used</label>
                            <input type="number" id="laporan-compo" placeholder="Jumlah komponen" min="0" required>
                        </div>
                        <div class="laporan-field">
                            <label>Money Stored (Rp)</label>
                            <input type="number" id="laporan-money" placeholder="Jumlah uang (Rp)" min="0" required>
                        </div>
                        <div class="laporan-field">
                            <label>Keterangan</label>
                            <input type="text" id="laporan-keterangan" placeholder="Keterangan (opsional)">
                        </div>
                        <div class="laporan-btn-row">
                            <button type="submit" class="laporan-btn" id="laporan-btn">📤 Kirim</button>
                            <button type="button" class="laporan-btn laporan-btn-reset" onclick="resetLaporan()">🔄 Reset</button>
                        </div>
                    </form>
                    <div id="laporan-success" class="laporan-success"></div>
                </div>
            </div>
            <script>
                let verifiedMechanicNama = null;

                async function checkNamaMechanic() {
                    const input = document.getElementById('laporan-nama');
                    const info = document.getElementById('laporan-nama-info');
                    const nama = input.value.trim();

                    if (nama.length < 2) {
                        info.innerHTML = '';
                        info.style.color = '#8b949e';
                        verifiedMechanicNama = null;
                        return;
                    }

                    try {
                        const res = await fetch('api/laporan_api.php?action=get_employees');
                        const result = await res.json();
                        if (result.success && result.data) {
                            const match = result.data.find(emp => emp.nama.toLowerCase() === nama.toLowerCase());
                            if (match) {
                                info.innerHTML = '<span style="color:#4db84d;">✅ Ditemukan: ' + match.nama + ' (🔧 Mechanic)</span>';
                                info.style.color = '#4db84d';
                                verifiedMechanicNama = match.nama;
                            } else {
                                info.innerHTML = '<span style="color:#f85149;">❌ Nama tidak ditemukan atau bukan Mechanic</span>';
                                info.style.color = '#f85149';
                                verifiedMechanicNama = null;
                            }
                        }
                    } catch (e) {
                        info.innerHTML = '<span style="color:#f85149;">⚠️ Gagal cek nama</span>';
                    }
                }

                async function submitLaporan(e) {
                    e.preventDefault();
                    const nama = document.getElementById('laporan-nama').value.trim();
                    const compo = parseInt(document.getElementById('laporan-compo').value) || 0;
                    const money = parseFloat(document.getElementById('laporan-money').value) || 0;
                    const keterangan = document.getElementById('laporan-keterangan').value || '';

                    if (!nama) {
                        alert('Nama mechanic harus diisi!');
                        return;
                    }

                    if (!verifiedMechanicNama) {
                        alert('Nama tidak valid atau bukan Mechanic!');
                        return;
                    }

                    try {
                        const fd = new FormData();
                        fd.append('action', 'add');
                        fd.append('nama_karyawan', verifiedMechanicNama);
                        fd.append('compo_used', compo);
                        fd.append('money_stored', money);
                        fd.append('keterangan', keterangan);
                        fd.append('tanggal', new Date().toISOString().split('T')[0]);

                        const res = await fetch('api/laporan_api.php', { method: 'POST', body: fd });
                        const result = await res.json();

                        const success = document.getElementById('laporan-success');
                        if (result.success) {
                            success.style.display = 'block';
                            success.innerHTML = '✅ Laporan berhasil!<br><strong>' + verifiedMechanicNama + '</strong><br>Compo: ' + compo + ' | Rp ' + money.toLocaleString('id-ID');
                            setTimeout(() => {
                                success.style.display = 'none';
                                resetLaporan();
                            }, 3000);
                        } else {
                            alert(result.message || 'Gagal mengirim laporan');
                        }
                    } catch (e) {
                        alert('Gagal mengirim laporan!');
                    }
                }

                function resetLaporan() {
                    document.getElementById('laporan-form').reset();
                    document.getElementById('laporan-nama-info').innerHTML = '';
                    document.getElementById('laporan-success').style.display = 'none';
                    verifiedMechanicNama = null;
                }

                document.getElementById('win-laporan').addEventListener('focus', function() {
                    checkNamaMechanic();
                });
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
                                    <th>Nama</th>
                                    <th>Compo/Bibit Used</th>
                                    <th>Money/Panen (Rp)</th>
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

                    // Load Laporan Kerja from database (mechanic + farmer) - Admin Panel
                    async function loadLaporan() {
                        const tbody = document.getElementById('laporan-table-body');
                        if (!tbody) return;

                        const bulan = document.getElementById('filter-bulan')?.value || '';

                        try {
                            let mechanicData = [];
                            let farmerData = [];

                            // Load mechanic reports
                            let url1 = 'api/laporan_api.php?action=get_all';
                            if (bulan) url1 += '&bulan=2026-' + bulan;
                            const res1 = await fetch(url1);
                            const result1 = await res1.json();
                            if (result1.success && result1.data) {
                                mechanicData = result1.data;
                            }

                            // Load farmer reports
                            let url2 = 'api/laporan_farmer_api.php?action=get_all';
                            if (bulan) url2 += '&bulan=2026-' + bulan;
                            const res2 = await fetch(url2);
                            const result2 = await res2.json();
                            if (result2.success && result2.data) {
                                farmerData = result2.data;
                            }

                            let total = 0;
                            let html = '';

                            // Process mechanic data
                            mechanicData.forEach(l => {
                                total += parseFloat(l.money_stored || 0);
                                html += `
                                    <tr>
                                        <td>${l.tanggal || '-'}</td>
                                        <td>🔧 ${l.nama_karyawan || '-'}</td>
                                        <td>${l.compo_used ?? 0}</td>
                                        <td>Rp ${parseFloat(l.money_stored || 0).toLocaleString('id-ID')}</td>
                                        <td>${l.keterangan || '-'}</td>
                                    </tr>
                                `;
                            });

                            // Process farmer data
                            farmerData.forEach(l => {
                                total += parseFloat(l.panen_hasil || 0);
                                html += `
                                    <tr>
                                        <td>${l.tanggal || '-'}</td>
                                        <td>🌱 ${l.nama_karyawan || '-'}</td>
                                        <td>${l.bibit_used ?? 0}</td>
                                        <td>${parseFloat(l.panen_hasil || 0).toLocaleString('id-ID')} kg</td>
                                        <td>${l.keterangan || '-'}</td>
                                    </tr>
                                `;
                            });

                            if (!html) {
                                html = '<tr><td colspan="5" style="text-align:center;color:#8b949e;">Tidak ada data laporan</td></tr>';
                            }

                            tbody.innerHTML = html;
                            document.getElementById('laporan-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
                        } catch (e) {
                            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:#f85149;">⚠️ Gagal memuat data</td></tr>';
                        }
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
            <span class="menu-item">View</span>
            <span class="menu-item">Help</span>
        </div>
        <div class="window-content">
            <div class="app-panel">
                <div class="app-section">
                    <div class="app-section-title">📦 CARGO APP - Brothers Company</div>
                    <div class="btn-grid" style="justify-content:center;">
                        <!-- History Delivery -->
                        <button class="app-btn" onclick="openWindow('historydelivery')" style="min-width:140px;padding:16px;">
                            <svg viewBox="0 0 48 48">
                                <rect x="6" y="4" width="36" height="40" rx="2" fill="#8b5cf6" stroke="#5b21b6" stroke-width="2"/>
                                <rect x="6" y="4" width="36" height="10" rx="2" fill="#7c3aed"/>
                                <rect x="12" y="18" width="24" height="4" fill="#5b21b6"/>
                                <rect x="12" y="24" width="18" height="4" fill="#5b21b6"/>
                                <rect x="12" y="30" width="20" height="4" fill="#5b21b6"/>
                                <rect x="12" y="36" width="14" height="4" fill="#5b21b6"/>
                                <circle cx="36" cy="36" r="8" fill="#4db84d"/>
                                <path d="M32 36 L35 39 L40 33" stroke="#fff" stroke-width="2" fill="none"/>
                            </svg>
                            History Delivery
                        </button>
                        <!-- Delivery List -->
                        <button class="app-btn" onclick="openWindow('deliverylist')" style="min-width:140px;padding:16px;">
                            <svg viewBox="0 0 48 48">
                                <rect x="4" y="6" width="40" height="36" rx="2" fill="#8b5cf6" stroke="#5b21b6" stroke-width="2"/>
                                <rect x="8" y="10" width="32" height="8" fill="#7c3aed"/>
                                <rect x="10" y="22" width="28" height="4" fill="#c4b5fd"/>
                                <rect x="10" y="28" width="20" height="4" fill="#c4b5fd"/>
                                <rect x="10" y="34" width="24" height="4" fill="#c4b5fd"/>
                            </svg>
                            Delivery List
                        </button>
                    </div>
                </div>
                <div class="app-section">
                    <div class="app-section-title">📋 INFORMASI</div>
                    <p class="status-bar-text">
                        CargoApp menyediakan akses ke History Delivery untuk melihat riwayat pengiriman dan Delivery List untuk melihat daftar pengiriman aktif.
                    </p>
                </div>
            </div>
        </div>
        <div class="window-statusbar">
            <span class="statusbar-section">CargoApp</span>
            <span class="statusbar-section">Kelola pengiriman cargo</span>
        </div>
        <div class="resizer" onmousedown="startResize(event, 'win-cargo')"></div>
    </div>

    <!-- HISTORY DELIVERY WINDOW -->
    <div class="window" id="win-historydelivery" style="top:80px;left:140px;width:700px;height:450px;display:none;">
        <div class="window-titlebar" onmousedown="startDrag(event, 'win-historydelivery')">
            <svg class="window-icon" viewBox="0 0 16 16">
                <rect x="2" y="2" width="12" height="12" rx="1" fill="#8b5cf6"/>
                <rect x="4" y="4" width="8" height="3" fill="#fff"/>
                <rect x="4" y="8" width="5" height="2" fill="#fff"/>
                <rect x="4" y="11" width="6" height="2" fill="#fff"/>
            </svg>
            <span class="window-title">History Delivery - Brothers Company</span>
            <div class="window-controls">
                <button class="window-btn window-btn-max" onclick="minimizeWindow('win-historydelivery')">_</button>
                <button class="window-btn window-btn-max" onclick="maximizeWindow('win-historydelivery')">□</button>
                <button class="window-btn" onclick="closeWindow('win-historydelivery')">✕</button>
            </div>
        </div>
        <div class="window-menubar">
            <span class="menu-item">File</span>
            <span class="menu-item">View</span>
            <span class="menu-item">Reports</span>
            <span class="menu-item">Help</span>
        </div>
        <div class="window-content" style="padding:0;background:#0d1117;height:calc(100% - 48px);overflow:auto;">
            <div style="width:100%;min-height:100%;background:#0d1117;padding:15px;box-sizing:border-box;font-family:'Segoe UI',sans-serif;color:#c9d1d9;">
                <style>
                    .hd-header { display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;flex-wrap:wrap;gap:10px; }
                    .hd-title { color:#8b5cf6;font-size:16px;margin:0; }
                    .hd-filter { display:flex;gap:10px;flex-wrap:wrap; }
                    .hd-filter select,.hd-filter input { padding:8px;border-radius:4px;border:1px solid #30363d;background:#010409;color:#fff;font-size:12px; }
                    .hd-btn { padding:8px 16px;border-radius:4px;border:none;background:#8b5cf6;color:#fff;font-size:12px;font-weight:bold;cursor:pointer; }
                    .hd-btn:hover { background:#7c3aed; }
                    .hd-btn-reset { background:#30363d; }
                    .hd-btn-reset:hover { background:#484f58; }
                    .hd-table { width:100%;border-collapse:collapse;font-size:12px; }
                    .hd-table th { background:#21262d;color:#8b949e;padding:10px;border:1px solid #30363d;text-align:left; }
                    .hd-table td { padding:10px;border:1px solid #30363d;color:#c9d1d9; }
                    .hd-table tr:hover td { background:#21262d; }
                    .hd-status { padding:4px 8px;border-radius:4px;font-size:10px;font-weight:bold; }
                    .hd-selesai { background:#238636;color:#fff; }
                    .hd-diambil { background:#e8b828;color:#000; }
                    .hd-batal { background:#6b7280;color:#fff; }
                    .hd-total { margin-top:15px;padding:15px;background:#161b22;border:1px solid #30363d;border-radius:8px;display:flex;justify-content:space-between;align-items:center; }
                    .hd-total-label { color:#8b949e;font-size:12px; }
                    .hd-total-value { font-size:18px;font-weight:bold;color:#8b5cf6; }
                </style>
                <div class="hd-header">
                    <h2 class="hd-title">📜 History Delivery</h2>
                    <div class="hd-filter">
                        <input type="date" id="hd-filter-tanggal" value="">
                        <select id="hd-filter-jenis" style="padding:8px;border-radius:4px;border:1px solid #30363d;background:#010409;color:#fff;font-size:12px;">
                            <option value="">Semua Jenis</option>
                            <option value="compo">🔧 Compo</option>
                            <option value="farmer">🌱 Farmer</option>
                        </select>
                        <button class="hd-btn" onclick="loadHistoryDelivery()">🔍 Filter</button>
                        <button class="hd-btn hd-btn-reset" onclick="document.getElementById('hd-filter-tanggal').value='';document.getElementById('hd-filter-jenis').value='';loadHistoryDelivery();">🗑️ Reset</button>
                    </div>
                </div>
                <table class="hd-table">
                    <thead><tr><th>No.</th><th>Tanggal Input</th><th>Jenis</th><th>Crate</th><th>Alamat Tujuan</th><th>Penerima</th><th>Driver</th><th>Status</th></tr></thead>
                    <tbody id="hd-table-body"><tr><td colspan="7" style="text-align:center;color:#8b949e;">Memuat...</td></tr></tbody>
                </table>
                <div class="hd-total">
                    <span class="hd-total-label">Total Delivery:</span>
                    <span class="hd-total-value" id="hd-total-count">0 Pengiriman</span>
                </div>
            </div>
        </div>
        <script>
            async function loadHistoryDelivery() {
                const tbody = document.getElementById('hd-table-body');
                if (!tbody) return;

                const tanggal = document.getElementById('hd-filter-tanggal').value;
                const jenis = document.getElementById('hd-filter-jenis').value;

                let url = 'api/delivery_order_api.php?action=get_all';
                if (tanggal) url += '&tanggal=' + tanggal;
                if (jenis) url += '&jenis=' + jenis;

                try {
                    const res = await fetch(url);
                    const result = await res.json();
                    if (result.success && result.data && result.data.length > 0) {
                        document.getElementById('hd-total-count').textContent = result.data.length + ' Pengiriman';

                        let html = '';
                        let no = 1;
                        result.data.forEach(item => {
                            const statusClass = item.status === 'selesai' ? 'hd-selesai' : (item.status === 'diambil' ? 'hd-diambil' : 'hd-batal');
                            const statusText = item.status === 'selesai' ? 'Selesai' : (item.status === 'diambil' ? 'Diambil' : 'Batal');
                            const jenisIcon = item.jenis_delivery === 'compo' ? '🔧' : '🌱';
                            const tanggalInput = item.tanggal_input ? formatDateTime(item.tanggal_input) : '-';
                            const driverInfo = item.driver_nama ? item.driver_nama : '-';

                            html += '<tr>' +
                                '<td>' + (no++) + '</td>' +
                                '<td>' + tanggalInput + '</td>' +
                                '<td>' + jenisIcon + ' ' + item.jenis_delivery + '</td>' +
                                '<td>' + item.jumlah_crate + ' crate</td>' +
                                '<td>' + item.alamat_tujuan + '</td>' +
                                '<td>' + item.nama_penerima + '</td>' +
                                '<td>' + driverInfo + '</td>' +
                                '<td><span class="hd-status ' + statusClass + '">' + statusText + '</span></td>' +
                            '</tr>';
                        });
                        tbody.innerHTML = html;
                    } else {
                        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:#8b949e;">Tidak ada history</td></tr>';
                        document.getElementById('hd-total-count').textContent = '0 Pengiriman';
                    }
                } catch (e) {
                    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:#f85149;">Gagal memuat data</td></tr>';
                }
            }

            // Load on window focus
            document.getElementById('win-historydelivery').addEventListener('focus', loadHistoryDelivery);

            // Auto-load on page load
            window.addEventListener('load', function() {
                // Set default date to today
                document.getElementById('hd-filter-tanggal').value = getTodayJakarta();
                setTimeout(loadHistoryDelivery, 1000);
            });
        </script>
        <div class="window-statusbar">
            <span class="statusbar-section">History Delivery</span>
            <span class="statusbar-section">Riwayat pengiriman cargo</span>
        </div>
        <div class="resizer" onmousedown="startResize(event, 'win-historydelivery')"></div>
    </div>

    <!-- DELIVERY LIST WINDOW -->
    <div class="window" id="win-deliverylist" style="top:90px;left:150px;width:750px;height:500px;display:none;">
        <div class="window-titlebar" onmousedown="startDrag(event, 'win-deliverylist')">
            <svg class="window-icon" viewBox="0 0 16 16">
                <rect x="1" y="1" width="14" height="14" rx="1" fill="#8b5cf6"/>
                <rect x="3" y="3" width="10" height="3" fill="#fff"/>
                <rect x="3" y="7" width="7" height="2" fill="#fff"/>
                <rect x="3" y="10" width="8" height="2" fill="#fff"/>
            </svg>
            <span class="window-title">Delivery List - Brothers Company</span>
            <div class="window-controls">
                <button class="window-btn window-btn-max" onclick="minimizeWindow('win-deliverylist')">_</button>
                <button class="window-btn window-btn-max" onclick="maximizeWindow('win-deliverylist')">□</button>
                <button class="window-btn" onclick="closeWindow('win-deliverylist')">✕</button>
            </div>
        </div>
        <div class="window-menubar">
            <span class="menu-item">File</span>
            <span class="menu-item">View</span>
            <span class="menu-item">Reports</span>
            <span class="menu-item">Help</span>
        </div>
        <div class="window-content" style="padding:0;background:#0d1117;height:calc(100% - 48px);overflow:auto;">
            <div style="width:100%;min-height:100%;background:#0d1117;padding:15px;box-sizing:border-box;font-family:'Segoe UI',sans-serif;color:#c9d1d9;">
                <style>
                    .dl-header { display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;flex-wrap:wrap;gap:10px; }
                    .dl-title { color:#8b5cf6;font-size:16px;margin:0; }
                    .dl-filter { display:flex;gap:10px;flex-wrap:wrap; }
                    .dl-filter select,.dl-filter input { padding:8px;border-radius:4px;border:1px solid #30363d;background:#010409;color:#fff;font-size:12px; }
                    .dl-btn { padding:8px 16px;border-radius:4px;border:none;background:#8b5cf6;color:#fff;font-size:12px;font-weight:bold;cursor:pointer; }
                    .dl-btn:hover { background:#7c3aed; }
                    .dl-table { width:100%;border-collapse:collapse;font-size:12px; }
                    .dl-table th { background:#21262d;color:#8b949e;padding:10px;border:1px solid #30363d;text-align:left; }
                    .dl-table td { padding:10px;border:1px solid #30363d;color:#c9d1d9; }
                    .dl-table tr:hover td { background:#21262d; }
                    .dl-status { padding:4px 8px;border-radius:4px;font-size:10px;font-weight:bold; }
                    .dl-pending { background:#cc2222;color:#fff; }
                    .dl-diambil { background:#e8b828;color:#000; }
                    .dl-selesai { background:#238636;color:#fff; }
                    .dl-batal { background:#6b7280;color:#fff; }
                    .dl-action { display:flex;gap:5px; }
                    .dl-action-btn { padding:4px 8px;border-radius:4px;border:none;font-size:10px;font-weight:bold;cursor:pointer; }
                    .dl-ambil { background:#238636;color:#fff; }
                    .dl-ambil:hover { background:#2ea043; }
                    .dl-selesai-btn { background:#4db84d;color:#fff; }
                    .dl-selesai-btn:hover { background:#5ecc5e; }
                    .dl-delete { background:#cc2222;color:#fff; }
                    .dl-delete:hover { background:#f85149; }
                    .dl-stats { display:flex;gap:10px;margin-bottom:15px;flex-wrap:wrap; }
                    .dl-stat { flex:1;min-width:100px;padding:12px;background:#161b22;border:1px solid #30363d;border-radius:8px;text-align:center; }
                    .dl-stat-value { font-size:24px;font-weight:bold;color:#8b5cf6; }
                    .dl-stat-label { font-size:11px;color:#8b949e;margin-top:4px; }
                    .dl-modal { display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:9999;justify-content:center;align-items:center; }
                    .dl-modal.show { display:flex; }
                    .dl-modal-content { background:#161b22;border:2px solid #8b5cf6;border-radius:12px;padding:20px;width:400px;max-width:90%; }
                    .dl-modal-title { color:#8b5cf6;font-size:16px;margin-bottom:15px;text-align:center; }
                    .dl-modal-row { margin-bottom:12px; }
                    .dl-modal-row label { display:block;font-size:11px;color:#8b949e;margin-bottom:4px; }
                    .dl-modal-row select,.dl-modal-row input { width:100%;padding:10px;border-radius:6px;border:1px solid #30363d;background:#010409;color:#fff;font-size:12px; }
                    .dl-modal-btn { display:flex;gap:10px;margin-top:15px; }
                    .dl-modal-btn button { flex:1;padding:10px;border-radius:6px;border:none;font-size:12px;font-weight:bold;cursor:pointer; }
                    .dl-modal-btn .dl-modal-submit { background:#8b5cf6;color:#fff; }
                    .dl-modal-btn .dl-modal-cancel { background:#30363d;color:#fff; }
                </style>
                <div class="dl-header">
                    <h2 class="dl-title">📋 Delivery List</h2>
                    <div class="dl-filter">
                        <select id="dl-filter-jenis" onchange="loadDeliveryList()">
                            <option value="">Semua Jenis</option>
                            <option value="compo">🔧 Compo</option>
                            <option value="farmer">🌱 Farmer</option>
                        </select>
                        <select id="dl-filter-status" onchange="loadDeliveryList()">
                            <option value="">Semua Status</option>
                            <option value="pending">⏳ Menunggu</option>
                            <option value="diambil">🚚 Diambil</option>
                            <option value="selesai">✅ Selesai</option>
                        </select>
                        <button class="dl-btn" onclick="loadDeliveryList()">🔍 Refresh</button>
                    </div>
                </div>
                <div class="dl-stats">
                    <div class="dl-stat">
                        <div class="dl-stat-value" id="dl-stat-total">0</div>
                        <div class="dl-stat-label">Total</div>
                    </div>
                    <div class="dl-stat">
                        <div class="dl-stat-value" id="dl-stat-pending">0</div>
                        <div class="dl-stat-label">Menunggu</div>
                    </div>
                    <div class="dl-stat">
                        <div class="dl-stat-value" id="dl-stat-diambil">0</div>
                        <div class="dl-stat-label">Diambil</div>
                    </div>
                    <div class="dl-stat">
                        <div class="dl-stat-value" id="dl-stat-selesai">0</div>
                        <div class="dl-stat-label">Selesai</div>
                    </div>
                </div>
                <table class="dl-table">
                    <thead><tr><th>No.</th><th>Waktu</th><th>Jenis</th><th>Crate</th><th>Alamat Tujuan</th><th>Penerima</th><th>Telepon</th><th>Status</th><th>Driver</th><th>Aksi</th></tr></thead>
                    <tbody id="dl-table-body"><tr><td colspan="8" style="text-align:center;color:#8b949e;">Memuat...</td></tr></tbody>
                </table>
            </div>
        </div>

        <!-- Modal Ambil Delivery -->
        <div class="dl-modal" id="dl-ambil-modal">
            <div class="dl-modal-content">
                <div class="dl-modal-title">🚚 Ambil Delivery</div>
                <div class="dl-modal-row">
                    <label>Nama Driver (Ketik nama, harus Cargo Driver)</label>
                    <input type="text" id="dl-driver-nama" placeholder="Ketik nama driver..." autocomplete="off" oninput="checkDriverName()">
                    <div id="dl-driver-info" style="font-size:10px;margin-top:4px;color:#8b949e;"></div>
                </div>
                <div class="dl-modal-row" id="dl-delivery-info">
                    <label>Detail Delivery</label>
                    <div style="padding:10px;background:#010409;border-radius:6px;font-size:12px;color:#c9d1d9;">
                        <div id="dl-info-jenis"></div>
                        <div id="dl-info-alamat"></div>
                        <div id="dl-info-penerima"></div>
                    </div>
                </div>
                <div class="dl-modal-btn">
                    <button class="dl-modal-cancel" onclick="closeAmbilModal()">Batal</button>
                    <button class="dl-modal-submit" onclick="submitAmbilDelivery()">✅ Ambil</button>
                </div>
                <input type="hidden" id="dl-ambil-id">
                <input type="hidden" id="dl-driver-id">
            </div>
        </div>

        <script>
            let currentDeliveryId = null;
            let verifiedDriverId = null;
            let verifiedDriverNama = null;

            async function checkDriverName() {
                const input = document.getElementById('dl-driver-nama');
                const info = document.getElementById('dl-driver-info');
                const nama = input.value.trim();

                if (nama.length < 2) {
                    info.innerHTML = '';
                    verifiedDriverId = null;
                    verifiedDriverNama = null;
                    document.getElementById('dl-driver-id').value = '';
                    return;
                }

                try {
                    const res = await fetch('api/delivery_order_api.php?action=get_employees_cargo');
                    const result = await res.json();
                    if (result.success && result.data) {
                        const match = result.data.find(emp => emp.nama.toLowerCase() === nama.toLowerCase());
                        if (match) {
                            info.innerHTML = '<span style="color:#4db84d;">✅ Driver ditemukan: ' + match.nama + ' (' + match.divisi + ')</span>';
                            verifiedDriverId = match.id;
                            verifiedDriverNama = match.nama;
                            document.getElementById('dl-driver-id').value = match.id;
                        } else {
                            info.innerHTML = '<span style="color:#f85149;">❌ Driver tidak ditemukan atau bukan Cargo Driver</span>';
                            verifiedDriverId = null;
                            verifiedDriverNama = null;
                            document.getElementById('dl-driver-id').value = '';
                        }
                    }
                } catch (e) {
                    info.innerHTML = '<span style="color:#f85149;">❌ Gagal cek driver</span>';
                }
            }

            async function loadDeliveryList() {
                const tbody = document.getElementById('dl-table-body');
                if (!tbody) return;

                const jenis = document.getElementById('dl-filter-jenis').value;
                const status = document.getElementById('dl-filter-status').value;
                let url = 'api/delivery_order_api.php?action=get_all';
                if (jenis) url += '&jenis=' + jenis;
                if (status) url += '&status=' + status;

                try {
                    const res = await fetch(url);
                    const result = await res.json();
                    if (result.success && result.data && result.data.length > 0) {
                        // Update stats
                        document.getElementById('dl-stat-total').textContent = result.data.length;
                        document.getElementById('dl-stat-pending').textContent = result.data.filter(d => d.status === 'pending').length;
                        document.getElementById('dl-stat-diambil').textContent = result.data.filter(d => d.status === 'diambil').length;
                        document.getElementById('dl-stat-selesai').textContent = result.data.filter(d => d.status === 'selesai').length;

                        let html = '';
                        let no = 1;
                        result.data.forEach(item => {
                            const statusClass = item.status === 'selesai' ? 'dl-selesai' : (item.status === 'diambil' ? 'dl-diambil' : 'dl-pending');
                            const statusText = item.status === 'selesai' ? 'Selesai' : (item.status === 'diambil' ? 'Diambil' : 'Menunggu');
                            const jenisIcon = item.jenis_delivery === 'compo' ? '🔧' : '🌱';
                            const driverInfo = item.driver_nama ? item.driver_nama : '-';
                            const waktuInput = item.tanggal_input ? formatDateTime(item.tanggal_input) : '-';
                            let actions = '';
                            if (item.status === 'pending') {
                                actions = '<button class="dl-action-btn dl-ambil" onclick="openAmbilModal(' + item.id + ', \'' + item.jenis_delivery + '\', \'' + item.alamat_tujuan + '\', \'' + item.nama_penerima + '\')">🚚 Ambil</button>';
                            } else if (item.status === 'diambil') {
                                actions = '<button class="dl-action-btn dl-selesai-btn" onclick="selesaikanDelivery(' + item.id + ')">✅ Selesai</button>';
                            } else {
                                actions = '-';
                            }
                            html += '<tr>' +
                                '<td>' + (no++) + '</td>' +
                                '<td>' + waktuInput + '</td>' +
                                '<td>' + jenisIcon + ' ' + item.jenis_delivery + '</td>' +
                                '<td>' + item.jumlah_crate + ' crate</td>' +
                                '<td>' + item.alamat_tujuan + '</td>' +
                                '<td>' + item.nama_penerima + '</td>' +
                                '<td>' + (item.no_telepon || '-') + '</td>' +
                                '<td><span class="dl-status ' + statusClass + '">' + statusText + '</span></td>' +
                                '<td>' + driverInfo + '</td>' +
                                '<td>' + actions + '</td>' +
                            '</tr>';
                        });
                        tbody.innerHTML = html;
                    } else {
                        tbody.innerHTML = '<tr><td colspan="10" style="text-align:center;color:#8b949e;">Tidak ada delivery</td></tr>';
                    }
                } catch (e) {
                    tbody.innerHTML = '<tr><td colspan="10" style="text-align:center;color:#f85149;">Gagal memuat data</td></tr>';
                }
            }

            async function openAmbilModal(id, jenis, alamat, penerima) {
                currentDeliveryId = id;
                document.getElementById('dl-ambil-id').value = id;
                document.getElementById('dl-info-jenis').innerText = 'Jenis: ' + (jenis === 'compo' ? '🔧 Deliver Compo' : '🌱 Deliver Farmer');
                document.getElementById('dl-info-alamat').innerText = 'Alamat: ' + alamat;
                document.getElementById('dl-info-penerima').innerText = 'Penerima: ' + penerima;
                document.getElementById('dl-driver-nama').value = '';
                document.getElementById('dl-driver-info').innerHTML = '';
                verifiedDriverId = null;
                verifiedDriverNama = null;
                document.getElementById('dl-driver-id').value = '';
                document.getElementById('dl-ambil-modal').classList.add('show');
            }

            function closeAmbilModal() {
                document.getElementById('dl-ambil-modal').classList.remove('show');
                currentDeliveryId = null;
                verifiedDriverId = null;
                verifiedDriverNama = null;
            }

            async function submitAmbilDelivery() {
                const driverNama = document.getElementById('dl-driver-nama').value.trim();

                if (!driverNama) {
                    alert('Ketik nama driver!');
                    return;
                }

                if (!verifiedDriverId) {
                    alert('Driver tidak valid atau bukan Cargo Driver!');
                    return;
                }

                try {
                    const fd = new FormData();
                    fd.append('action', 'ambildelivery');
                    fd.append('id', currentDeliveryId);
                    fd.append('driver_id', verifiedDriverId);
                    fd.append('driver_nama', verifiedDriverNama);

                    const res = await fetch('api/delivery_order_api.php', { method: 'POST', body: fd });
                    const result = await res.json();
                    if (result.success) {
                        closeAmbilModal();
                        loadDeliveryList();
                        showToast('Sukses', result.message);
                    } else {
                        alert(result.message || 'Gagal mengambil delivery');
                    }
                } catch (e) {
                    alert('Gagal mengambil delivery!');
                }
            }

            async function selesaikanDelivery(id) {
                if (!confirm('Tandai delivery ini sebagai selesai?')) return;
                try {
                    const fd = new FormData();
                    fd.append('action', 'selesaikan');
                    fd.append('id', id);
                    const res = await fetch('api/delivery_order_api.php', { method: 'POST', body: fd });
                    const result = await res.json();
                    if (result.success) {
                        loadDeliveryList();
                        showToast('Sukses', result.message);
                    } else {
                        alert(result.message || 'Gagal menyelesaikan delivery');
                    }
                } catch (e) {
                    alert('Gagal menyelesaikan delivery!');
                }
            }

            // Load on window open
            document.getElementById('win-deliverylist').addEventListener('focus', loadDeliveryList);

            // Auto-refresh every 15 seconds for realtime updates
            setInterval(() => {
                if (document.getElementById('win-deliverylist').style.display === 'block') {
                    loadDeliveryList();
                }
            }, 15000);

            // Also load on page load
            window.addEventListener('load', function() {
                setTimeout(loadDeliveryList, 1000);
            });
        </script>
        <div class="window-statusbar">
            <span class="statusbar-section">Delivery List</span>
            <span class="statusbar-section">Daftar pengiriman aktif</span>
        </div>
        <div class="resizer" onmousedown="startResize(event, 'win-deliverylist')"></div>
    </div>

    <!-- EMPLOYEE WINDOW -->
    <div class="window" id="win-employee" style="top:80px;left:150px;width:850px;height:480px;display:none;">
        <div class="window-titlebar" onmousedown="startDrag(event, 'win-employee')">
            <svg class="window-icon" viewBox="0 0 16 16">
                <rect x="2" y="2" width="12" height="12" rx="1" fill="#3a6ea5"/>
                <circle cx="5" cy="7" r="2" fill="#f5c890"/>
                <circle cx="11" cy="7" r="2" fill="#f5c890"/>
                <ellipse cx="8" cy="12" rx="4" ry="2" fill="#245edc"/>
            </svg>
            <span class="window-title">Employee - Brothers Company</span>
            <div class="window-controls">
                <button class="window-btn window-btn-max" onclick="minimizeWindow('win-employee')">_</button>
                <button class="window-btn window-btn-max" onclick="maximizeWindow('win-employee')">□</button>
                <button class="window-btn" onclick="closeWindow('win-employee')">✕</button>
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
                    .emp-header { display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;flex-wrap:wrap;gap:10px; }
                    .emp-title { color:#58a6ff;font-size:16px;margin:0; }
                    .emp-stats { display:flex;gap:10px; }
                    .emp-stat { background:#161b22;border:1px solid #30363d;padding:8px 15px;border-radius:6px;text-align:center; }
                    .emp-stat-value { font-size:18px;font-weight:bold;color:#ffd700; }
                    .emp-stat-label { font-size:9px;color:#8b949e;text-transform:uppercase; }
                    .emp-container { display:flex;gap:20px; }
                    .emp-list { flex:2;min-width:0; }
                    .emp-form-box { flex:1;background:#161b22;border:1px solid #30363d;border-radius:8px;padding:15px;min-width:260px; }
                    .emp-form-title { color:#238636;font-size:14px;margin:0 0 12px 0; }
                    .emp-field { margin-bottom:10px; }
                    .emp-field label { display:block;font-size:10px;font-weight:bold;color:#8b949e;text-transform:uppercase;margin-bottom:4px; }
                    .emp-field input,.emp-field select { width:100%;padding:8px;border-radius:4px;border:1px solid #30363d;background:#010409;color:#fff;font-size:12px;box-sizing:border-box; }
                    .emp-btn-add { width:100%;padding:10px;background:#238636;color:#fff;border:none;border-radius:4px;font-size:12px;font-weight:bold;cursor:pointer; }
                    .emp-btn-add:hover { background:#2ea043; }
                    .emp-table { width:100%;border-collapse:collapse;font-size:11px; }
                    .emp-table th { background:#21262d;color:#8b949e;padding:8px;border:1px solid #30363d; }
                    .emp-table td { padding:8px;border:1px solid #30363d;color:#c9d1d9; }
                    .emp-table tr:hover td { background:#21262d; }
                    .emp-btn-del { padding:4px 8px;background:#cc2222;color:#fff;border:none;border-radius:4px;font-size:10px;cursor:pointer; }
                    .emp-div-m { color:#58a6ff;font-weight:bold; }
                    .emp-div-f { color:#4db84d;font-weight:bold; }
                    .emp-div-c { color:#ffd700;font-weight:bold; }
                    .emp-div-manager { color:#cc2222;font-weight:bold; }
                    .emp-success { display:none;background:#238636;color:#fff;padding:8px;border-radius:4px;text-align:center;margin-top:8px;font-size:11px; }
                </style>
                <div class="emp-header">
                    <h2 class="emp-title">👷 Employee - Brothers Company</h2>
                    <div class="emp-stats">
                        <div class="emp-stat"><div class="emp-stat-value" id="emp-total">0</div><div class="emp-stat-label">Total</div></div>
                        <div class="emp-stat"><div class="emp-stat-value" id="emp-mechanic" style="color:#58a6ff;">0</div><div class="emp-stat-label">Mechanic</div></div>
                        <div class="emp-stat"><div class="emp-stat-value" id="emp-farmer" style="color:#4db84d;">0</div><div class="emp-stat-label">Farmer</div></div>
                        <div class="emp-stat"><div class="emp-stat-value" id="emp-cargo" style="color:#ffd700;">0</div><div class="emp-stat-label">Cargo</div></div>
                        <div class="emp-stat"><div class="emp-stat-value" id="emp-manager" style="color:#cc2222;">0</div><div class="emp-stat-label">Manager</div></div>
                    </div>
                </div>
                <div style="margin-bottom:15px;display:flex;gap:10px;">
                    <input type="text" id="emp-search" placeholder="Cari nama atau nomor telepon..." style="flex:1;padding:10px;border-radius:6px;border:1px solid #30363d;background:#010409;color:#fff;font-size:12px;" onkeyup="if(event.key==='Enter')searchEmployee()">
                    <button onclick="searchEmployee()" style="padding:10px 20px;background:#58a6ff;color:#fff;border:none;border-radius:6px;font-weight:bold;cursor:pointer;font-size:12px;">🔍 Cari</button>
                    <button onclick="loadEmployees()" style="padding:10px 20px;background:#30363d;color:#fff;border:none;border-radius:6px;font-weight:bold;cursor:pointer;font-size:12px;">🔄 Reset</button>
                </div>
                <div class="emp-container">
                    <div class="emp-list">
                        <table class="emp-table">
                            <thead><tr><th>Nama</th><th>Telepon</th><th>No.Rekening</th><th>Divisi</th><th>Aksi</th></tr></thead>
                            <tbody id="emp-table-body"><tr><td colspan="6" style="text-align:center;color:#8b949e;">Loading...</td></tr></tbody>
                        </table>
                    </div>
                    <div class="emp-form-box">
                        <h3 class="emp-form-title">➕ Tambah Employee Baru</h3>
                        <form id="emp-form" onsubmit="submitEmployee(event)">
                            <div class="emp-field"><label>Nama Lengkap *</label><input type="text" id="emp-nama" placeholder="Masukkan nama" required></div>
                            <div class="emp-field"><label>Nomor Telepon</label><input type="tel" id="emp-telepon" placeholder="08xxxxxxxxxx"></div>
                            <div class="emp-field"><label>No Rekening</label><input type="text" id="emp-rekening" placeholder="1234567890"></div>
                            <div class="emp-field"><label>Divisi *</label><select id="emp-divisi" required><option value="">-- Pilih --</option><option value="Mechanic">Mechanic</option><option value="Farmer">Farmer</option><option value="Cargo Driver">Cargo Driver</option><option value="Manager">Manager</option></select></div>
                            <button type="submit" class="emp-btn-add">💾 Simpan Employee</button>
                            <div id="emp-success" class="emp-success">✅ Employee berhasil ditambahkan!</div>
                        </form>
                    </div>
                </div>
            </div>
            <script>
            // Call loadEmployees when window opens
            document.getElementById('win-employee').addEventListener('focus', loadEmployees);

            async function loadEmployees() {
                const tbody = document.getElementById('emp-table-body');
                if (!tbody) {
                    // Retry after a short delay
                    setTimeout(loadEmployees, 500);
                    return;
                }
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#8b949e;">Memuat...</td></tr>';
                try {
                    const res = await fetch('api/employee_api.php?action=get_all');
                    const result = await res.json();
                    if (result.success) {
                        renderEmpTable(result.data);
                    } else {
                        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#f85149;">Error: ' + result.message + '</td></tr>';
                    }
                } catch (e) {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#f85149;">Koneksi gagal - klik window untuk retry</td></tr>';
                }
            }

            function renderEmpTable(data) {
                const tbody = document.getElementById('emp-table-body');
                if (!tbody) return;
                let m=0,f=0,c=0,manager=0,html='';
                data.forEach(e => {
                    const divClass = e.divisi === 'Mechanic' ? 'emp-div-m' : e.divisi === 'Farmer' ? 'emp-div-f' : e.divisi === 'Manager' ? 'emp-div-manager' : 'emp-div-c';
                    if(e.divisi==='Mechanic')m++;else if(e.divisi==='Farmer')f++;else if(e.divisi==='Manager')manager++;else c++;
                    html += '<tr><td>'+e.nama+'</td><td>'+(e.telepon||'-')+'</td><td>'+(e.no_rekening||'-')+'</td><td class="'+divClass+'">'+e.divisi+'</td><td><button class="emp-btn-del" onclick="deleteEmployee('+e.id+')">Hapus</button></td></tr>';
                });
                tbody.innerHTML = html || '<tr><td colspan="6" style="text-align:center;color:#8b949e;">Tidak ada data employee</td></tr>';
                const totalEl = document.getElementById('emp-total');
                const mEl = document.getElementById('emp-mechanic');
                const fEl = document.getElementById('emp-farmer');
                const cEl = document.getElementById('emp-cargo');
                const managerEl = document.getElementById('emp-manager');
                if(totalEl) totalEl.textContent = data.length;
                if(mEl) mEl.textContent = m;
                if(fEl) fEl.textContent = f;
                if(cEl) cEl.textContent = c;
                if(managerEl) managerEl.textContent = manager;
            }

            async function submitEmployee(e) {
                e.preventDefault();
                const nama = document.getElementById('emp-nama');
                const telp = document.getElementById('emp-telepon');
                const rek = document.getElementById('emp-rekening');
                const div = document.getElementById('emp-divisi');
                const success = document.getElementById('emp-success');

                if (!nama.value || !div.value) {
                    alert('Nama dan Divisi harus diisi!');
                    return;
                }

                const fd = new FormData();
                fd.append('action', 'add');
                fd.append('nama', nama.value);
                fd.append('telepon', telp.value || '');
                fd.append('no_rekening', rek.value || '');
                fd.append('divisi', div.value);

                try {
                    const res = await fetch('api/employee_api.php', {method: 'POST', body: fd});
                    const result = await res.json();
                    if (result.success) {
                        success.style.display = 'block';
                        success.textContent = 'Berhasil ditambahkan!';
                        nama.value = '';
                        telp.value = '';
                        rek.value = '';
                        div.value = '';
                        setTimeout(() => { success.style.display = 'none'; }, 3000);
                        loadEmployees();
                    } else {
                        alert('Error: ' + result.message);
                    }
                } catch (err) {
                    alert('Gagal koneksi ke server!');
                }
            }

            async function deleteEmployee(id) {
                if (!confirm('Yakin hapus employee ini?')) return;
                const fd = new FormData();
                fd.append('action', 'delete');
                fd.append('id', id);
                try {
                    await fetch('api/employee_api.php', {method: 'POST', body: fd});
                    loadEmployees();
                } catch (err) {
                    alert('Gagal hapus!');
                }
            }

            // Initial load
            loadEmployees();

            async function searchEmployee() {
                const query = document.getElementById('emp-search').value.trim();
                const tbody = document.getElementById('emp-table-body');
                if (!tbody) return;

                if (!query) {
                    loadEmployees();
                    return;
                }

                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:#8b949e;">Mencari...</td></tr>';

                try {
                    const res = await fetch('api/employee_api.php?action=search&q=' + encodeURIComponent(query));
                    const result = await res.json();
                    if (result.success) {
                        renderEmpTable(result.data);
                    } else {
                        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:#f85149;">Error</td></tr>';
                    }
                } catch (e) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:#f85149;">Koneksi gagal</td></tr>';
                }
            }
            </script>
        </div>
        <div class="window-statusbar"><span class="statusbar-section">Employee</span><span class="statusbar-section">Kelola employee</span></div>
        <div class="resizer" onmousedown="startResize(event, 'win-employee')"></div>
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
                    .lk-btn:hover { opacity:0.8; }
                    .lk-btn-accept { background:#238636; }
                    .lk-btn-accept:hover { background:#2ea043; }
                    .lk-btn-delete { background:#cc2222; padding:4px 8px; }
                    .lk-table { width:100%;border-collapse:collapse;font-size:12px; }
                    .lk-table th { background:#21262d;color:#8b949e;padding:10px;border:1px solid #30363d; }
                    .lk-table td { padding:10px;border:1px solid #30363d; }
                    .lk-table tr:hover td { background:#21262d; }
                    .lk-total { margin-top:15px;padding:15px;background:#161b22;border:1px solid #30363d;border-radius:8px;display:flex;justify-content:space-between;align-items:center; }
                    .lk-total-label { color:#8b949e;font-size:12px; }
                    .lk-total-value { font-size:20px;font-weight:bold;color:#ffd700; }
                    .lk-tabs { display:flex;gap:5px;margin-bottom:15px; }
                    .lk-tab { padding:8px 16px;background:#21262d;border:1px solid #30363d;border-radius:6px;color:#8b949e;font-size:12px;cursor:pointer;font-weight:600; }
                    .lk-tab.active { background:#ffd700;color:#000;border-color:#ffd700; }
                    .lk-section { display:none; }
                    .lk-section.active { display:block; }
                    .lk-success { display:none;background:#238636;color:#fff;padding:8px;border-radius:4px;text-align:center;font-size:12px;margin-top:10px; }
                </style>
                <div class="lk-header">
                    <h2 class="lk-title">📊 Laporan Kerja</h2>
                    <div class="lk-filter">
                        <select id="lk-filter-bulan" onchange="loadLaporanKerja()"><option value="">Semua Bulan</option><option value="2026-01">Januari</option><option value="2026-02">Februari</option><option value="2026-03">Maret</option><option value="2026-04">April</option><option value="2026-05">Mei</option><option value="2026-06">Juni</option><option value="2026-07">Juli</option><option value="2026-08">Agustus</option><option value="2026-09">September</option><option value="2026-10">Oktober</option><option value="2026-11">November</option><option value="2026-12">Desember</option></select>
                        <select id="lk-filter-divisi" onchange="loadLaporanKerja()" style="padding:8px;border-radius:4px;border:1px solid #30363d;background:#010409;color:#fff;font-size:12px;">
                            <option value="">Semua Divisi</option>
                            <option value="mechanic">🔧 Mechanic</option>
                            <option value="farmer">🌱 Farmer</option>
                        </select>
                        <button class="lk-btn" onclick="loadLaporanKerja()">🔍</button>
                    </div>
                </div>

                <!-- TABS -->
                <div class="lk-tabs">
                    <button class="lk-tab active" onclick="switchLkTab('pending')">📝 Pending</button>
                    <button class="lk-tab" onclick="switchLkTab('accepted')">✅ Accepted</button>
                </div>

                <!-- PENDING SECTION -->
                <div class="lk-section active" id="lk-pending">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;">
                        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;flex:1;">
                            <div style="padding:12px;background:#161b22;border:1px solid #30363d;border-radius:8px;text-align:center;">
                                <div style="font-size:18px;font-weight:bold;color:#58a6ff;" id="lk-total-compo">0</div>
                                <div style="font-size:10px;color:#8b949e;">Total Compo (Mechanic)</div>
                            </div>
                            <div style="padding:12px;background:#161b22;border:1px solid #30363d;border-radius:8px;text-align:center;">
                                <div style="font-size:18px;font-weight:bold;color:#4db84d;" id="lk-total-bibit">0</div>
                                <div style="font-size:10px;color:#8b949e;">Total Bibit (Farmer)</div>
                            </div>
                            <div style="padding:12px;background:#161b22;border:1px solid #30363d;border-radius:8px;text-align:center;">
                                <div style="font-size:18px;font-weight:bold;color:#f97316;" id="lk-total-buah">0 kg</div>
                                <div style="font-size:10px;color:#8b949e;">Total Buah Dijual</div>
                            </div>
                            <div style="padding:12px;background:#161b22;border:1px solid #30363d;border-radius:8px;text-align:center;">
                                <div style="font-size:18px;font-weight:bold;color:#ffd700;" id="lk-total-money">Rp 0</div>
                                <div style="font-size:10px;color:#8b949e;">Total Money (Mechanic)</div>
                            </div>
                        </div>
                        <button class="lk-btn lk-btn-delete" style="margin-left:15px;padding:10px 16px;font-size:12px;" onclick="deleteAllPending()">🗑️ Hapus Semua Pending</button>
                    </div>
                    <table class="lk-table">
                        <thead><tr><th>Tanggal</th><th>Divisi</th><th>Nama</th><th>Compo/Bibit</th><th>Value</th><th>Keterangan</th><th>Aksi</th></tr></thead>
                        <tbody id="lk-table-body"><tr><td colspan="7" style="text-align:center;color:#8b949e;">Memuat...</td></tr></tbody>
                    </table>
                </div>

                <!-- ACCEPTED SECTION -->
                <div class="lk-section" id="lk-accepted">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;">
                        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;flex:1;">
                            <div style="padding:12px;background:#161b22;border:1px solid #30363d;border-radius:8px;text-align:center;">
                                <div style="font-size:18px;font-weight:bold;color:#58a6ff;" id="lk-accepted-compo">0</div>
                                <div style="font-size:10px;color:#8b949e;">Total Compo (Mechanic)</div>
                            </div>
                            <div style="padding:12px;background:#161b22;border:1px solid #30363d;border-radius:8px;text-align:center;">
                                <div style="font-size:18px;font-weight:bold;color:#4db84d;" id="lk-accepted-bibit">0</div>
                                <div style="font-size:10px;color:#8b949e;">Total Bibit (Farmer)</div>
                            </div>
                            <div style="padding:12px;background:#161b22;border:1px solid #30363d;border-radius:8px;text-align:center;">
                                <div style="font-size:18px;font-weight:bold;color:#f97316;" id="lk-accepted-buah">0 kg</div>
                                <div style="font-size:10px;color:#8b949e;">Total Buah Dijual</div>
                            </div>
                            <div style="padding:12px;background:#161b22;border:1px solid #30363d;border-radius:8px;text-align:center;">
                                <div style="font-size:18px;font-weight:bold;color:#ffd700;" id="lk-accepted-money">Rp 0</div>
                                <div style="font-size:10px;color:#8b949e;">Total Money (Mechanic)</div>
                            </div>
                        </div>
                        <button class="lk-btn lk-btn-delete" style="margin-left:15px;padding:10px 16px;font-size:12px;" onclick="deleteAllAccepted()">🗑️ Hapus Semua Accepted</button>
                    </div>
                    <table class="lk-table">
                        <thead><tr><th>Tanggal Accept</th><th>Divisi</th><th>Nama</th><th>Compo/Bibit</th><th>Value</th><th>Keterangan</th><th>Aksi</th></tr></thead>
                        <tbody id="lk-accepted-body"><tr><td colspan="7" style="text-align:center;color:#8b949e;">Memuat...</td></tr></tbody>
                    </table>
                </div>

                <div id="lk-success" class="lk-success"></div>
            </div>
            <script>
                window.addEventListener('load', function() {
                    loadLaporanKerja();
                });

                function switchLkTab(tab) {
                    document.querySelectorAll('.lk-tab').forEach(t => t.classList.remove('active'));
                    document.querySelectorAll('.lk-section').forEach(s => s.classList.remove('active'));
                    event.target.classList.add('active');
                    document.getElementById('lk-' + tab).classList.add('active');

                    if (tab === 'accepted') {
                        loadAcceptedLaporan();
                    }
                }
            </script>
        </div>
        <div class="window-statusbar"><span class="statusbar-section">Laporan Kerja</span><span class="statusbar-section">Semua laporan mechanic & farmer</span></div>
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

    // Load Laporan Kerja from database (mechanic + farmer)
    async function loadLaporanKerja() {
        const tbody = document.getElementById('lk-table-body');
        const compoEl = document.getElementById('lk-total-compo');
        const bibitEl = document.getElementById('lk-total-bibit');
        const buahEl = document.getElementById('lk-total-buah');
        const moneyEl = document.getElementById('lk-total-money');
        if (!tbody) return;

        const bulan = document.getElementById('lk-filter-bulan')?.value || '';
        const divisi = document.getElementById('lk-filter-divisi')?.value || '';

        try {
            let mechanicData = [];
            let farmerData = [];

            // Load mechanic reports
            if (!divisi || divisi === 'mechanic') {
                let url = 'api/laporan_api.php?action=get_all';
                if (bulan) url += '&bulan=' + bulan;
                const res = await fetch(url);
                const result = await res.json();
                if (result.success && result.data) {
                    mechanicData = result.data;
                }
            }

            // Load farmer reports
            if (!divisi || divisi === 'farmer') {
                let url = 'api/laporan_farmer_api.php?action=get_all';
                if (bulan) url += '&bulan=' + bulan;
                const res = await fetch(url);
                const result = await res.json();
                if (result.success && result.data) {
                    farmerData = result.data;
                }
            }

            let totalCompo = 0;
            let totalBibit = 0;
            let totalBuah = 0;
            let totalMoney = 0;
            let html = '';

            // Process mechanic data
            mechanicData.forEach(l => {
                totalCompo += parseInt(l.compo_used) || 0;
                totalMoney += parseFloat(l.money_stored) || 0;
                html += `<tr>
                    <td>${l.tanggal || '-'}</td>
                    <td>🔧 Mechanic</td>
                    <td>${l.nama_karyawan || '-'}</td>
                    <td>${l.compo_used ?? 0}</td>
                    <td>Rp ${parseFloat(l.money_stored || 0).toLocaleString('id-ID')}</td>
                    <td>${l.keterangan || '-'}</td>
                    <td>
                        <button class="lk-btn lk-btn-accept" style="padding:4px 8px;margin-right:4px;" onclick="acceptLaporan('mechanic', ${l.id}, '${l.nama_karyawan || ''}', '${l.tanggal || ''}', ${l.compo_used ?? 0}, ${l.money_stored ?? 0}, '${(l.keterangan || '').replace(/'/g, "\\'")}')">✅</button>
                        <button class="lk-btn lk-btn-delete" onclick="deleteLaporan('mechanic', ${l.id})">🗑️</button>
                    </td>
                </tr>`;
            });

            // Process farmer data
            farmerData.forEach(l => {
                totalBibit += parseInt(l.bibit_used) || 0;
                totalBuah += parseFloat(l.panen_hasil) || 0;
                html += `<tr>
                    <td>${l.tanggal || '-'}</td>
                    <td>🌱 Farmer</td>
                    <td>${l.nama_karyawan || '-'}</td>
                    <td>${l.bibit_used ?? 0}</td>
                    <td>${parseFloat(l.panen_hasil || 0).toLocaleString('id-ID')} kg</td>
                    <td>${l.keterangan || '-'}</td>
                    <td>
                        <button class="lk-btn lk-btn-accept" style="padding:4px 8px;margin-right:4px;" onclick="acceptLaporan('farmer', ${l.id}, '${l.nama_karyawan || ''}', '${l.tanggal || ''}', ${l.bibit_used ?? 0}, ${l.panen_hasil ?? 0}, '${(l.keterangan || '').replace(/'/g, "\\'")}')">✅</button>
                        <button class="lk-btn lk-btn-delete" onclick="deleteLaporan('farmer', ${l.id})">🗑️</button>
                    </td>
                </tr>`;
            });

            if (!html) {
                html = '<tr><td colspan="7" style="text-align:center;color:#8b949e;">Tidak ada data laporan</td></tr>';
            }

            tbody.innerHTML = html;
            if (compoEl) compoEl.textContent = totalCompo.toLocaleString('id-ID');
            if (bibitEl) bibitEl.textContent = totalBibit.toLocaleString('id-ID');
            if (buahEl) buahEl.textContent = totalBuah.toLocaleString('id-ID') + ' kg';
            if (moneyEl) moneyEl.textContent = 'Rp ' + totalMoney.toLocaleString('id-ID');
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:#f85149;">⚠️ Gagal memuat data</td></tr>';
        }
    }

    // Delete Laporan
    async function deleteLaporan(type, id) {
        if (!confirm('Yakin hapus laporan ini?')) return;
        try {
            const fd = new FormData();
            fd.append('action', 'delete');
            fd.append('id', id);
            const apiUrl = type === 'farmer' ? 'api/laporan_farmer_api.php' : 'api/laporan_api.php';
            const res = await fetch(apiUrl, { method: 'POST', body: fd });
            const result = await res.json();
            if (result.success) {
                loadLaporanKerja();
            } else {
                alert(result.message || 'Gagal hapus');
            }
        } catch (e) {
            alert('Gagal hapus laporan!');
        }
    }

    // Accept Laporan
    async function acceptLaporan(type, sourceId, nama, tanggal, jumlahUsed, jumlahValue, keterangan) {
        if (!confirm('Accept laporan ini?\n\nNama: ' + nama + '\nDivisi: ' + (type === 'mechanic' ? 'Mechanic' : 'Farmer') + '\nJumlah: ' + jumlahUsed + '\nValue: ' + jumlahValue)) return;

        try {
            // First, accept the laporan
            const fd = new FormData();
            fd.append('action', 'accept');
            fd.append('source_type', type);
            fd.append('source_id', sourceId);
            fd.append('nama_karyawan', nama);
            fd.append('divisi', type);
            fd.append('jumlah_used', jumlahUsed);
            fd.append('jumlah_value', jumlahValue);
            fd.append('keterangan', keterangan);
            fd.append('tanggal_laporan', tanggal);
            fd.append('accepted_by', 'Admin');

            const res = await fetch('api/accepted_laporan_api.php', { method: 'POST', body: fd });
            const result = await res.json();

            if (result.success) {
                // Then delete from original table (so it disappears from pending)
                const deleteFd = new FormData();
                deleteFd.append('action', 'delete');
                deleteFd.append('id', sourceId);
                const apiUrl = type === 'farmer' ? 'api/laporan_farmer_api.php' : 'api/laporan_api.php';
                await fetch(apiUrl, { method: 'POST', body: deleteFd });

                const successEl = document.getElementById('lk-success');
                successEl.textContent = '✅ Laporan berhasil di-accept!';
                successEl.style.display = 'block';
                loadLaporanKerja();
                setTimeout(() => { successEl.style.display = 'none'; }, 3000);
            } else {
                alert(result.message || 'Gagal accept laporan');
            }
        } catch (e) {
            alert('Gagal accept laporan!');
        }
    }

    // Load Accepted Laporan
    async function loadAcceptedLaporan() {
        const tbody = document.getElementById('lk-accepted-body');
        const compoEl = document.getElementById('lk-accepted-compo');
        const bibitEl = document.getElementById('lk-accepted-bibit');
        const buahEl = document.getElementById('lk-accepted-buah');
        const moneyEl = document.getElementById('lk-accepted-money');
        if (!tbody) return;

        const bulan = document.getElementById('lk-filter-bulan')?.value || '';
        const divisi = document.getElementById('lk-filter-divisi')?.value || '';

        try {
            let url = 'api/accepted_laporan_api.php?action=get_all';
            if (bulan) url += '&bulan=' + bulan;
            if (divisi) url += '&divisi=' + divisi;

            const res = await fetch(url);
            const result = await res.json();

            let totalCompo = 0;
            let totalBibit = 0;
            let totalBuah = 0;
            let totalMoney = 0;
            let html = '';

            if (result.success && result.data && result.data.length > 0) {
                result.data.forEach(l => {
                    const isMechanic = l.divisi === 'mechanic';

                    if (isMechanic) {
                        totalCompo += parseInt(l.jumlah_used) || 0;
                        totalMoney += parseFloat(l.jumlah_value) || 0;
                    } else {
                        totalBibit += parseInt(l.jumlah_used) || 0;
                        totalBuah += parseFloat(l.jumlah_value) || 0;
                    }

                    html += `<tr>
                        <td>${l.tanggal_accept ? l.tanggal_accept.substring(0, 10) : '-'}</td>
                        <td>${isMechanic ? '🔧 Mechanic' : '🌱 Farmer'}</td>
                        <td>${l.nama_karyawan || '-'}</td>
                        <td>${l.jumlah_used ?? 0}</td>
                        <td>${isMechanic ? 'Rp ' + parseFloat(l.jumlah_value || 0).toLocaleString('id-ID') : parseFloat(l.jumlah_value || 0).toLocaleString('id-ID') + ' kg'}</td>
                        <td>${l.keterangan || '-'}</td>
                        <td><button class="lk-btn lk-btn-delete" onclick="deleteAcceptedLaporan(${l.id})">🗑️</button></td>
                    </tr>`;
                });
            }

            if (!html) {
                html = '<tr><td colspan="7" style="text-align:center;color:#8b949e;">Tidak ada laporan yang di-accept</td></tr>';
            }

            tbody.innerHTML = html;
            if (compoEl) compoEl.textContent = totalCompo.toLocaleString('id-ID');
            if (bibitEl) bibitEl.textContent = totalBibit.toLocaleString('id-ID');
            if (buahEl) buahEl.textContent = totalBuah.toLocaleString('id-ID') + ' kg';
            if (moneyEl) moneyEl.textContent = 'Rp ' + totalMoney.toLocaleString('id-ID');
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:#f85149;">⚠️ Gagal memuat data</td></tr>';
        }
    }

    // Delete Accepted Laporan
    async function deleteAcceptedLaporan(id) {
        if (!confirm('Yakin hapus laporan accepted ini?')) return;
        try {
            const fd = new FormData();
            fd.append('action', 'delete');
            fd.append('id', id);
            const res = await fetch('api/accepted_laporan_api.php', { method: 'POST', body: fd });
            const result = await res.json();
            if (result.success) {
                loadAcceptedLaporan();
            } else {
                alert(result.message || 'Gagal hapus');
            }
        } catch (e) {
            alert('Gagal hapus laporan!');
        }
    }

    // Delete All Pending Laporan
    async function deleteAllPending() {
        if (!confirm('⚠️ YAKIN HAPUS SEMUA DATA PENDING?\n\nSemua laporan mechanic dan farmer yang belum di-accept akan dihapus permanen!')) return;

        try {
            // Delete all mechanic reports
            await fetch('api/laporan_api.php?action=delete_all', { method: 'POST' });
            // Delete all farmer reports
            await fetch('api/laporan_farmer_api.php?action=delete_all', { method: 'POST' });

            const successEl = document.getElementById('lk-success');
            successEl.textContent = '🗑️ Semua data Pending berhasil dihapus!';
            successEl.style.display = 'block';
            loadLaporanKerja();
            setTimeout(() => { successEl.style.display = 'none'; }, 3000);
        } catch (e) {
            alert('Gagal hapus semua data pending!');
        }
    }

    // Delete All Accepted Laporan
    async function deleteAllAccepted() {
        if (!confirm('⚠️ YAKIN HAPUS SEMUA DATA ACCEPTED?\n\nSemua laporan yang sudah di-accept akan dihapus permanen!')) return;

        try {
            const fd = new FormData();
            fd.append('action', 'delete_all');
            const res = await fetch('api/accepted_laporan_api.php', { method: 'POST', body: fd });
            const result = await res.json();

            if (result.success) {
                const successEl = document.getElementById('lk-success');
                successEl.textContent = '🗑️ Semua data Accepted berhasil dihapus!';
                successEl.style.display = 'block';
                loadAcceptedLaporan();
                setTimeout(() => { successEl.style.display = 'none'; }, 3000);
            } else {
                alert(result.message || 'Gagal hapus');
            }
        } catch (e) {
            alert('Gagal hapus semua data accepted!');
        }
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
                    <div class="app-section-title">🌱 FARMER APP - Brothers Company</div>
                    <div class="btn-grid" style="justify-content:center;">
                        <!-- LaporanApp -->
                        <button class="app-btn" onclick="openWindow('laporanharian')" style="min-width:120px;padding:16px;">
                            <svg viewBox="0 0 48 48">
                                <rect x="6" y="4" width="36" height="40" rx="2" fill="#ffd700" stroke="#b07800" stroke-width="2"/>
                                <rect x="6" y="4" width="36" height="10" rx="2" fill="#e8b828"/>
                                <rect x="12" y="18" width="24" height="3" fill="#c8a000"/>
                                <rect x="12" y="24" width="18" height="3" fill="#c8a000"/>
                                <rect x="12" y="30" width="20" height="3" fill="#c8a000"/>
                                <rect x="12" y="36" width="14" height="3" fill="#c8a000"/>
                                <rect x="12" y="42" width="24" height="4" fill="#cc2222"/>
                            </svg>
                            LaporanApp
                        </button>
                        <!-- Tugas Hari Ini -->
                        <button class="app-btn" onclick="openWindow('tugashariini')" style="min-width:120px;padding:16px;">
                            <svg viewBox="0 0 48 48">
                                <rect x="4" y="4" width="40" height="40" rx="4" fill="#228b22" stroke="#1a5c1a" stroke-width="2"/>
                                <rect x="8" y="8" width="32" height="8" rx="2" fill="#32cd32"/>
                                <path d="M16 24 L22 30 L34 18" stroke="#fff" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                <rect x="8" y="32" width="32" height="8" rx="2" fill="#1a5c1a"/>
                            </svg>
                            Tugas Hari Ini
                        </button>
                    </div>
                </div>
                <div class="app-section">
                    <div class="app-section-title">📋 INFORMASI</div>
                    <p class="status-bar-text">
                        FarmerApp menyediakan akses ke LaporanApp untuk laporan farming dan Tugas Hari Ini untuk melihat tugas harian.
                    </p>
                </div>
            </div>
        </div>
        <div class="window-statusbar">
            <span class="statusbar-section">FarmerApp</span>
            <span class="statusbar-section">Kelola pertanian</span>
        </div>
        <div class="resizer" onmousedown="startResize(event, 'win-farmer')"></div>
    </div>

    <!-- LAPORAN HARIAN WINDOW (FARMER) -->
    <div class="window" id="win-laporanharian" style="top:100px;left:180px;width:380px;height:340px;display:none;">
        <div class="window-titlebar" onmousedown="startDrag(event, 'win-laporanharian')">
            <svg class="window-icon" viewBox="0 0 16 16">
                <rect x="2" y="1" width="12" height="14" rx="1" fill="#ffd700"/>
                <rect x="4" y="4" width="8" height="1" fill="#8b6914"/>
                <rect x="4" y="6" width="6" height="1" fill="#8b6914"/>
                <rect x="4" y="8" width="8" height="1" fill="#8b6914"/>
                <rect x="4" y="10" width="5" height="1" fill="#8b6914"/>
            </svg>
            <span class="window-title">LaporanApp Farming - Brothers Company</span>
            <div class="window-controls">
                <button class="window-btn window-btn-max" onclick="minimizeWindow('win-laporanharian')">_</button>
                <button class="window-btn window-btn-max" onclick="maximizeWindow('win-laporanharian')">□</button>
                <button class="window-btn" onclick="closeWindow('win-laporanharian')">✕</button>
            </div>
        </div>
        <div class="window-menubar">
            <span class="menu-item">File</span>
            <span class="menu-item">View</span>
            <span class="menu-item">Reports</span>
            <span class="menu-item">Help</span>
        </div>
        <div class="window-content" style="padding:0;background:#0d1117;height:calc(100% - 48px);overflow:auto;">
            <div style="width:100%;min-height:100%;background:#0d1117;padding:15px;box-sizing:border-box;font-family:'Segoe UI',sans-serif;color:#c9d1d9;">
                <style>
                    .fh-form-container { width:100%;max-width:100%; }
                    .fh-title { color:#ffd700;font-size:16px;text-align:center;margin:0 0 15px 0;padding-bottom:8px;border-bottom:1px solid #30363d; }
                    .fh-field { margin-bottom:10px; }
                    .fh-field label { display:block;font-size:10px;font-weight:bold;color:#8b949e;text-transform:uppercase;margin-bottom:4px; }
                    .fh-field input,.fh-field select { width:100%;padding:8px;border-radius:4px;border:1px solid #30363d;background:#010409;color:#fff;font-size:12px;box-sizing:border-box; }
                    .fh-field input:focus,.fh-field select:focus { outline:none;border-color:#ffd700; }
                    .fh-btn-row { display:flex;gap:8px;margin-top:10px; }
                    .fh-btn { flex:1;padding:8px;border-radius:4px;border:none;font-weight:bold;font-size:11px;cursor:pointer; }
                    .fh-btn-submit { background:#ffd700;color:#000; }
                    .fh-btn-submit:hover { background:#e8b828; }
                    .fh-btn-reset { background:#30363d;color:#fff; }
                    .fh-btn-reset:hover { background:#484f58; }
                    .fh-success { display:none;background:#238636;color:#fff;padding:10px;border-radius:4px;text-align:center;margin-top:8px;font-size:11px; }
                </style>
                <div class="fh-form-container">
                    <h2 class="fh-title">📝 Form Laporan Farmer</h2>
                    <form id="fh-laporan-form" onsubmit="submitFhLaporan(event)">
                        <div class="fh-field">
                            <label>Nama Farmer</label>
                            <input type="text" id="fh-nama" placeholder="Ketik nama farmer..." autocomplete="off" oninput="checkNamaFarmer()" required>
                            <div id="fh-nama-info" style="font-size:10px;margin-top:4px;color:#8b949e;"></div>
                        </div>
                        <div class="fh-field">
                            <label>Bibit Used</label>
                            <input type="number" id="fh-bibit" placeholder="Jumlah bibit digunakan" min="0" required>
                        </div>
                        <div class="fh-field">
                            <label>Panen Hasil (kg)</label>
                            <input type="number" id="fh-panen" placeholder="Jumlah hasil panen (kg)" min="0" required>
                        </div>
                        <div class="fh-field">
                            <label>Keterangan</label>
                            <input type="text" id="fh-keterangan" placeholder="Keterangan (opsional)">
                        </div>
                        <div class="fh-btn-row">
                            <button type="submit" class="fh-btn fh-btn-submit" id="fh-btn-submit">📤 Kirim</button>
                            <button type="button" class="fh-btn fh-btn-reset" onclick="resetFhLaporan()">🔄 Reset</button>
                        </div>
                    </form>
                    <div id="fh-success" class="fh-success">
                        ✅ Laporan berhasil dikirim!
                    </div>
                </div>
            </div>
            <script>
                let verifiedFarmerNama = null;

                async function checkNamaFarmer() {
                    const input = document.getElementById('fh-nama');
                    const info = document.getElementById('fh-nama-info');
                    const nama = input.value.trim();

                    if (nama.length < 2) {
                        info.innerHTML = '';
                        info.style.color = '#8b949e';
                        verifiedFarmerNama = null;
                        return;
                    }

                    try {
                        const res = await fetch('api/laporan_farmer_api.php?action=get_employees');
                        const result = await res.json();
                        if (result.success && result.data) {
                            const match = result.data.find(emp => emp.nama.toLowerCase() === nama.toLowerCase());
                            if (match) {
                                info.innerHTML = '<span style="color:#4db84d;">✅ Ditemukan: ' + match.nama + ' (🌱 Farmer)</span>';
                                info.style.color = '#4db84d';
                                verifiedFarmerNama = match.nama;
                            } else {
                                info.innerHTML = '<span style="color:#f85149;">❌ Nama tidak ditemukan atau bukan Farmer</span>';
                                info.style.color = '#f85149';
                                verifiedFarmerNama = null;
                            }
                        }
                    } catch (e) {
                        info.innerHTML = '<span style="color:#f85149;">⚠️ Gagal cek nama</span>';
                    }
                }

                async function submitFhLaporan(e) {
                    e.preventDefault();
                    const nama = document.getElementById('fh-nama').value.trim();
                    const bibit = parseInt(document.getElementById('fh-bibit').value) || 0;
                    const panen = parseFloat(document.getElementById('fh-panen').value) || 0;
                    const keterangan = document.getElementById('fh-keterangan').value || '';

                    if (!nama) {
                        alert('Nama farmer harus diisi!');
                        return;
                    }

                    if (!verifiedFarmerNama) {
                        alert('Nama tidak valid atau bukan Farmer!');
                        return;
                    }

                    try {
                        const fd = new FormData();
                        fd.append('action', 'add');
                        fd.append('nama_karyawan', verifiedFarmerNama);
                        fd.append('bibit_used', bibit);
                        fd.append('panen_hasil', panen);
                        fd.append('keterangan', keterangan);
                        fd.append('tanggal', new Date().toISOString().split('T')[0]);

                        const res = await fetch('api/laporan_farmer_api.php', { method: 'POST', body: fd });
                        const result = await res.json();

                        const success = document.getElementById('fh-success');
                        if (result.success) {
                            success.style.display = 'block';
                            success.innerHTML = '✅ Laporan berhasil!<br><strong>' + verifiedFarmerNama + '</strong><br>Bibit: ' + Number(bibit).toLocaleString('id-ID') + ' | Panen: ' + Number(panen).toLocaleString('id-ID') + ' kg';
                            setTimeout(() => {
                                success.style.display = 'none';
                                resetFhLaporan();
                            }, 3000);
                        } else {
                            alert(result.message || 'Gagal mengirim laporan');
                        }
                    } catch (e) {
                        alert('Gagal mengirim laporan!');
                    }
                }

                function resetFhLaporan() {
                    document.getElementById('fh-laporan-form').reset();
                    document.getElementById('fh-nama-info').innerHTML = '';
                    document.getElementById('fh-success').style.display = 'none';
                    verifiedFarmerNama = null;
                }

                document.getElementById('win-laporanharian').addEventListener('focus', checkNamaFarmer);
            </script>
        </div>
        <div class="window-statusbar">
            <span class="statusbar-section">Laporan Farmer</span>
            <span class="statusbar-section">Form laporan farmer</span>
        </div>
        <div class="resizer" onmousedown="startResize(event, 'win-laporanharian')"></div>
    </div>

    <!-- TUGAS HARI INI WINDOW -->
    <div class="window" id="win-tugashariini" style="top:110px;left:190px;width:500px;height:400px;display:none;">
        <div class="window-titlebar" onmousedown="startDrag(event, 'win-tugashariini')">
            <svg class="window-icon" viewBox="0 0 16 16">
                <rect x="2" y="1" width="12" height="14" rx="1" fill="#228b22"/>
                <path d="M5 8 L7 10 L11 6" stroke="#fff" stroke-width="2" fill="none"/>
            </svg>
            <span class="window-title">Tugas Hari Ini - Brothers Company</span>
            <div class="window-controls">
                <button class="window-btn window-btn-max" onclick="minimizeWindow('win-tugashariini')">_</button>
                <button class="window-btn window-btn-max" onclick="maximizeWindow('win-tugashariini')">□</button>
                <button class="window-btn" onclick="closeWindow('win-tugashariini')">✕</button>
            </div>
        </div>
        <div class="window-menubar">
            <span class="menu-item">File</span>
            <span class="menu-item">View</span>
            <span class="menu-item">Help</span>
        </div>
        <div class="window-content" style="padding:0;background:#0d1117;height:calc(100% - 48px);overflow:auto;">
            <div style="width:100%;min-height:100%;background:#0d1117;padding:15px;box-sizing:border-box;font-family:'Segoe UI',sans-serif;color:#c9d1d9;">
                <style>
                    .tht-header { margin-bottom:15px; }
                    .tht-title { color:#228b22;font-size:16px;margin:0 0 5px 0; }
                    .tht-date { color:#8b949e;font-size:12px; }
                    .tht-list { }
                    .tht-item { display:flex;align-items:center;justify-content:space-between;padding:12px;background:#161b22;border:1px solid #30363d;border-radius:8px;margin-bottom:10px; }
                    .tht-item-left { display:flex;align-items:center;gap:10px; }
                    .tht-time { font-size:11px;color:#8b949e;min-width:50px; }
                    .tht-text { font-size:13px;color:#c9d1d9; }
                    .tht-status { padding:4px 12px;border-radius:4px;font-size:11px;font-weight:bold; }
                    .tht-done { background:#238636;color:#fff; }
                    .tht-progress { background:#e8b828;color:#000; }
                    .tht-pending { background:#cc2222;color:#fff; }
                    .tht-btn { padding:8px 16px;background:#228b22;color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:bold;cursor:pointer;width:100%;margin-top:10px; }
                    .tht-btn:hover { background:#32cd32; }
                    .tht-empty { text-align:center;padding:30px;color:#8b949e;font-size:13px; }
                </style>
                <div class="tht-header">
                    <h2 class="tht-title">📋 Tugas Hari Ini</h2>
                    <div class="tht-date" id="tht-date"><?php echo date('d/m/Y'); ?></div>
                </div>
                <div class="tht-list" id="tht-list">
                    <div class="tht-empty">⏳ Memuat tugas...</div>
                </div>
            </div>
        </div>
        <script>
            // Indonesian day names + timezone
            const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

            // Get current date/time in Indonesia Jakarta (WIB = UTC+7)
            function getJakartaDate() {
                const now = new Date();
                // Convert to Jakarta time
                const jakartaOffset = 7 * 60; // WIB is UTC+7
                const localOffset = now.getTimezoneOffset();
                const jakartaTime = new Date(now.getTime() + (jakartaOffset + localOffset) * 60000);
                return jakartaTime;
            }

            function formatDate(dateStr) {
                if (!dateStr) return '';
                const d = new Date(dateStr);
                const day = dayNames[d.getDay()];
                const dd = String(d.getDate()).padStart(2, '0');
                const mm = String(d.getMonth() + 1).padStart(2, '0');
                const yyyy = d.getFullYear();
                return day + ', ' + dd + '/' + mm + '/' + yyyy;
            }

            function formatDateTime(dateStr) {
                if (!dateStr) return '';
                const d = new Date(dateStr);
                const day = dayNames[d.getDay()];
                const dd = String(d.getDate()).padStart(2, '0');
                const mm = String(d.getMonth() + 1).padStart(2, '0');
                const yyyy = d.getFullYear();
                const hh = String(d.getHours()).padStart(2, '0');
                const min = String(d.getMinutes()).padStart(2, '0');
                return day.substring(0, 3) + ', ' + dd + '/' + mm + '/' + yyyy + ' ' + hh + ':' + min;
            }

            // Get current date in YYYY-MM-DD format (Jakarta time)
            function getTodayJakarta() {
                const jakarta = getJakartaDate();
                const yyyy = jakarta.getFullYear();
                const mm = String(jakarta.getMonth() + 1).padStart(2, '0');
                const dd = String(jakarta.getDate()).padStart(2, '0');
                return yyyy + '-' + mm + '-' + dd;
            }

            // Get current time in HH:MM:SS format (Jakarta time)
            function getNowJakarta() {
                const jakarta = getJakartaDate();
                const hh = String(jakarta.getHours()).padStart(2, '0');
                const mm = String(jakarta.getMinutes()).padStart(2, '0');
                const ss = String(jakarta.getSeconds()).padStart(2, '0');
                return hh + ':' + mm + ':' + ss;
            }

            async function loadTugasHariIni() {
                const list = document.getElementById('tht-list');
                const dateEl = document.getElementById('tht-date');
                if (!list) return;

                try {
                    const res = await fetch('api/tugas_harian_api.php?action=get_today');
                    const result = await res.json();

                    if (result.success && result.data && result.data.length > 0) {
                        // Update date header from first item
                        const firstDate = result.data[0].tanggal;
                        if (dateEl) dateEl.innerText = formatDate(firstDate);

                        let html = '';
                        result.data.forEach(item => {
                            const statusClass = item.status === 'selesai' ? 'tht-done' : (item.status === 'proses' ? 'tht-progress' : 'tht-pending');
                            const statusText = item.status === 'selesai' ? 'Selesai' : (item.status === 'proses' ? 'Proses' : 'Pending');
                            const jamMulai = item.jam_mulai ? item.jam_mulai.substring(0, 5) : '00:00';
                            const jamSelesai = item.jam_selesai ? item.jam_selesai.substring(0, 5) : '00:00';
                            html += '<div class="tht-item">' +
                                '<div class="tht-item-left"><span class="tht-time">' + jamMulai + ' - ' + jamSelesai + '</span><span class="tht-text">' + item.tugas + '</span></div>' +
                                '<span class="tht-status ' + statusClass + '">' + statusText + '</span>' +
                            '</div>';
                        });
                        list.innerHTML = html;
                    } else {
                        list.innerHTML = '<div class="tht-empty">📭 Tidak ada tugas hari ini</div>';
                    }
                } catch (e) {
                    list.innerHTML = '<div class="tht-empty" style="color:#f85149;">⚠️ Gagal memuat tugas</div>';
                }
            }

            // Auto-refresh every 30 seconds to check status (Jakarta time)
            setInterval(() => { if (document.getElementById('win-tugashariini').style.display === 'block') loadTugasHariIni(); }, 30000);

            // Load on window open
            document.getElementById('win-tugashariini').addEventListener('focus', loadTugasHariIni);

            // Also load on page load
            window.addEventListener('load', function() {
                setTimeout(loadTugasHariIni, 500);
            });
        </script>
        <div class="window-statusbar">
            <span class="statusbar-section">Tugas Hari Ini</span>
            <span class="statusbar-section">Daftar tugas harian farming</span>
        </div>
        <div class="resizer" onmousedown="startResize(event, 'win-tugashariini')"></div>
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

    <!-- MANAGER APP WINDOW -->
    <div class="window" id="win-manager" style="top:90px;left:150px;width:620px;height:450px;display:none;">
        <div class="window-titlebar" onmousedown="startDrag(event, 'win-manager')">
            <svg class="window-icon" viewBox="0 0 16 16">
                <rect x="2" y="2" width="12" height="12" rx="1" fill="#b07800"/>
                <rect x="4" y="4" width="4" height="4" fill="#ffd700"/>
                <rect x="8" y="4" width="4" height="4" fill="#228b22"/>
                <rect x="4" y="8" width="4" height="4" fill="#cc2222"/>
                <rect x="8" y="8" width="4" height="4" fill="#8b5cf6"/>
            </svg>
            <span class="window-title">ManagerApp - Brothers Company</span>
            <div class="window-controls">
                <button class="window-btn window-btn-max" onclick="minimizeWindow('win-manager')">_</button>
                <button class="window-btn window-btn-max" onclick="maximizeWindow('win-manager')">□</button>
                <button class="window-btn" onclick="closeWindow('win-manager')">✕</button>
            </div>
        </div>
        <div class="window-menubar">
            <span class="menu-item">File</span>
            <span class="menu-item">View</span>
            <span class="menu-item">Help</span>
        </div>
        <div class="window-content">
            <div class="app-panel">
                <div class="app-section">
                    <div class="app-section-title">📊 MANAGER APP - Brothers Company</div>
                    <div class="btn-grid" style="justify-content:center;">
                        <!-- Mechanic Manager -->
                        <button class="app-btn" onclick="openWindow('mechanicmanager')" style="min-width:140px;padding:16px;">
                            <svg viewBox="0 0 48 48">
                                <rect x="4" y="8" width="40" height="32" rx="2" fill="#3a6ea5" stroke="#1e4d7a" stroke-width="2"/>
                                <rect x="8" y="12" width="32" height="20" fill="#c8e0f5"/>
                                <circle cx="14" cy="22" r="5" fill="#f0f0f0" stroke="#707070" stroke-width="1"/>
                                <circle cx="34" cy="22" r="5" fill="#f0f0f0" stroke="#707070" stroke-width="1"/>
                                <rect x="16" y="34" width="16" height="4" rx="2" fill="#245edc"/>
                                <circle cx="14" cy="22" r="2" fill="#2d8a2d"/>
                                <circle cx="34" cy="22" r="2" fill="#cc2222"/>
                            </svg>
                            Mechanic Manager
                        </button>
                        <!-- Farmer Manager -->
                        <button class="app-btn" onclick="openWindow('farmermanager')" style="min-width:140px;padding:16px;">
                            <svg viewBox="0 0 48 48">
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
                            Farmer Manager
                        </button>
                        <!-- Cargo Manager -->
                        <button class="app-btn" onclick="openWindow('cargomanager')" style="min-width:140px;padding:16px;">
                            <svg viewBox="0 0 48 48">
                                <rect x="6" y="14" width="36" height="26" rx="2" fill="#8b5cf6" stroke="#5b21b6" stroke-width="2"/>
                                <rect x="10" y="18" width="28" height="18" fill="#c4b5fd"/>
                                <rect x="14" y="22" width="20" height="10" fill="#7c3aed"/>
                                <rect x="18" y="8" width="12" height="6" rx="1" fill="#a78bfa"/>
                                <circle cx="24" cy="4" r="3" fill="#ddd"/>
                                <rect x="10" y="36" width="8" height="6" fill="#333"/>
                                <rect x="30" y="36" width="8" height="6" fill="#333"/>
                            </svg>
                            Cargo Manager
                        </button>
                    </div>
                </div>
                <div class="app-section">
                    <div class="app-section-title">📋 INFORMASI</div>
                    <p class="status-bar-text">
                        ManagerApp menyediakan akses untuk mengelola setiap divisi: Mechanic, Farmer, dan Cargo.
                    </p>
                </div>
            </div>
        </div>
        <div class="window-statusbar">
            <span class="statusbar-section">ManagerApp</span>
            <span class="statusbar-section">Manager dashboard</span>
        </div>
        <div class="resizer" onmousedown="startResize(event, 'win-manager')"></div>
    </div>

    <!-- MECHANIC MANAGER WINDOW -->
    <div class="window" id="win-mechanicmanager" style="top:100px;left:160px;width:550px;height:500px;display:none;">
        <div class="window-titlebar" onmousedown="startDrag(event, 'win-mechanicmanager')">
            <svg class="window-icon" viewBox="0 0 16 16">
                <rect x="2" y="2" width="12" height="12" rx="1" fill="#3a6ea5"/>
                <circle cx="5" cy="7" r="2" fill="#f0f0f0"/>
                <circle cx="11" cy="7" r="2" fill="#f0f0f0"/>
            </svg>
            <span class="window-title">Mechanic Manager - Brothers Company</span>
            <div class="window-controls">
                <button class="window-btn window-btn-max" onclick="minimizeWindow('win-mechanicmanager')">_</button>
                <button class="window-btn window-btn-max" onclick="maximizeWindow('win-mechanicmanager')">□</button>
                <button class="window-btn" onclick="closeWindow('win-mechanicmanager')">✕</button>
            </div>
        </div>
        <div class="window-menubar">
            <span class="menu-item">File</span>
            <span class="menu-item">View</span>
            <span class="menu-item">Help</span>
        </div>
        <div class="window-content" style="padding:0;background:#0d1117;height:calc(100% - 48px);overflow:auto;">
            <div style="width:100%;min-height:100%;background:#0d1117;padding:15px;box-sizing:border-box;font-family:'Segoe UI',sans-serif;color:#c9d1d9;">
                <style>
                    .pm-title { color:#58a6ff;font-size:16px;margin:0 0 15px 0;text-align:center; }
                    .pm-desc { color:#8b949e;font-size:12px;text-align:center;margin-bottom:20px; }
                    .pm-card { background:#161b22;padding:20px;border-radius:10px;border:1px solid #30363d;margin-bottom:15px; }
                    .pm-card h3 { color:#58a6ff;margin:0 0 15px 0;font-size:14px;border-bottom:1px solid #30363d;padding-bottom:10px; }
                    .pm-row { display:flex;align-items:center;margin-bottom:12px;padding:10px;background:#010409;border-radius:6px;border:1px solid #30363d; }
                    .pm-row label { flex:1;font-size:12px;color:#c9d1d9; }
                    .pm-row input { width:80px;padding:8px;border-radius:4px;border:1px solid #30363d;background:#0d1117;color:#fff;font-size:14px;text-align:center;font-weight:bold; }
                    .pm-row .pm-label { width:40px;text-align:center;font-size:12px;color:#8b949e; }
                    .pm-row .pm-show { min-width:60px;padding:6px 10px;background:#238636;color:#fff;border-radius:4px;font-size:12px;font-weight:bold;text-align:center; }
                    .pm-btn { width:100%;padding:12px;background:#58a6ff;color:#fff;border:none;border-radius:6px;font-size:13px;font-weight:bold;cursor:pointer;margin-top:10px; }
                    .pm-btn:hover { background:#79b8ff; }
                    .pm-btn-reset { background:#30363d;color:#fff;margin-top:5px; }
                    .pm-btn-reset:hover { background:#484f58; }
                    .pm-success { display:none;background:#238636;color:#fff;padding:10px;border-radius:6px;text-align:center;margin-top:10px;font-size:12px; }
                    .pm-note { font-size:10px;color:#8b949e;text-align:center;margin-top:10px; }
                </style>
                <h2 class="pm-title">⚙️ Atur Harga Component</h2>
                <p class="pm-desc">Pengaturan multiplier harga untuk setiap jenis layanan</p>

                <div class="pm-card">
                    <h3>💰 Multiplier Config</h3>
                    <div class="pm-row">
                        <label>Mechanical Repair</label>
                        <span class="pm-label">x</span>
                        <input type="number" id="pm-repair" step="0.1" min="0.1">
                    </div>
                    <div class="pm-row">
                        <label>Custom Modification</label>
                        <span class="pm-label">x</span>
                        <input type="number" id="pm-modif" step="0.1" min="0.1">
                    </div>
                    <div class="pm-row">
                        <label>Brotherhood Disc</label>
                        <span class="pm-label">x</span>
                        <input type="number" id="pm-brother" step="0.1" min="0.1">
                    </div>
                    <div class="pm-row">
                        <label>WS Stored</label>
                        <span class="pm-label">x</span>
                        <input type="number" id="pm-ws_stored" step="0.1" min="0.1">
                    </div>
                </div>

                <div class="pm-card">
                    <h3>🔧 Sparepart Price</h3>
                    <div class="pm-row">
                        <label>Harga per Unit ($)</label>
                        <span class="pm-label">$</span>
                        <input type="number" id="pm-sparepart" step="0.1" min="0">
                    </div>
                </div>

                <div id="pm-current-config" class="pm-card" style="background:#0d1117;border-color:#58a6ff;">
                    <h3 style="border-color:#30363d;">📋 Harga Saat Ini (Dari Database)</h3>
                    <div id="pm-config-list" style="font-size:12px;color:#8b949e;">
                        <div style="padding:20px;text-align:center;color:#58a6ff;">⏳ Memuat harga...</div>
                    </div>
                </div>

                <button class="pm-btn" onclick="savePriceConfig()">💾 Simpan Konfigurasi</button>
                <button class="pm-btn" style="background:#30363d;margin-top:5px;" onclick="if(typeof loadPriceConfig==='function')loadPriceConfig();">🔄 Refresh Harga</button>
                <div id="pm-success" class="pm-success">✅ Konfigurasi berhasil disimpan!</div>

                <p class="pm-note">* Hanya Admin yang dapat mengubah konfigurasi ini</p>
            </div>
            <script>
                async function loadPriceConfig() {
                    console.log('Loading price config...');
                    try {
                        const res = await fetch('api/price_config_api.php?action=get_all');
                        console.log('API response status:', res.status);
                        const result = await res.json();
                        console.log('Price config result:', result);
                        let configHtml = '<div style="margin-top:8px;">';

                        // Default values
                        const defaults = {
                            repair: { name: 'Mechanical Repair', value: 3.0 },
                            modif: { name: 'Custom Modification', value: 3.0 },
                            brother: { name: 'Brotherhood Disc', value: 2.3 },
                            ws_stored: { name: 'WS Stored', value: 2.0 }
                        };

                        if (result.success && result.data && result.data.length > 0) {
                            // Use DB values
                            result.data.forEach(item => {
                                configHtml += '<div style="padding:6px 0;border-bottom:1px solid #30363d;"><span style="color:#c9d1d9;">' + (item.nama_layanan || item.jenis_layanan) + ':</span> <span style="color:#ffd700;font-weight:bold;">x ' + item.multiplier + '</span></div>';
                                const el = document.getElementById('pm-' + item.jenis_layanan);
                                if (el) el.value = item.multiplier;
                                if (defaults[item.jenis_layanan]) delete defaults[item.jenis_layanan];
                            });
                            // Show remaining defaults
                            Object.values(defaults).forEach(d => {
                                configHtml += '<div style="padding:6px 0;border-bottom:1px solid #30363d;"><span style="color:#c9d1d9;">' + d.name + ':</span> <span style="color:#ffd700;font-weight:bold;">x ' + d.value + ' (default)</span></div>';
                            });
                        } else {
                            // Show all defaults
                            Object.values(defaults).forEach(d => {
                                configHtml += '<div style="padding:6px 0;border-bottom:1px solid #30363d;"><span style="color:#c9d1d9;">' + d.name + ':</span> <span style="color:#ffd700;font-weight:bold;">x ' + d.value + ' (default)</span></div>';
                            });
                            configHtml += '<div style="margin-top:8px;padding:6px;background:#21262d;border-radius:4px;font-size:11px;color:#f85149;">⚠️ Database kosong - menggunakan nilai default</div>';
                        }
                        configHtml += '</div>';

                        // Load sparepart price
                        let sparepartValue = 2.0;
                        try {
                            const sparepartRes = await fetch('api/sparepart_config_api.php?action=get');
                            const sparepartResult = await sparepartRes.json();
                            if (sparepartResult.success && sparepartResult.data) {
                                sparepartValue = sparepartResult.data.harga_per_unit;
                                const spareEl = document.getElementById('pm-sparepart');
                                if (spareEl) spareEl.value = sparepartValue;
                            }
                        } catch (e) { /* use default */ }

                        configHtml += '<div style="padding:6px 0;border-top:1px solid #30363d;margin-top:8px;"><span style="color:#c9d1d9;">Sparepart:</span> <span style="color:#ffd700;font-weight:bold;">$ ' + sparepartValue + ' /unit</span></div>';

                        const configList = document.getElementById('pm-config-list');
                        console.log('pm-config-list element:', configList);
                        if (configList) {
                            configList.innerHTML = configHtml;
                            console.log('Config list updated!');
                        } else {
                            console.error('pm-config-list NOT FOUND!');
                        }
                    } catch (e) {
                        console.error('loadPriceConfig error:', e);
                        // Fallback - show defaults
                        const defaults = [
                            { name: 'Mechanical Repair', value: '3.0' },
                            { name: 'Custom Modification', value: '3.0' },
                            { name: 'Brotherhood Disc', value: '2.3' },
                            { name: 'WS Stored', value: '2.0' }
                        ];
                        let html = '<div style="margin-top:8px;">';
                        defaults.forEach(d => {
                            html += '<div style="padding:6px 0;border-bottom:1px solid #30363d;"><span style="color:#c9d1d9;">' + d.name + ':</span> <span style="color:#ffd700;font-weight:bold;">x ' + d.value + ' (default)</span></div>';
                        });
                        html += '</div><div style="padding:6px 0;border-top:1px solid #30363d;margin-top:8px;"><span style="color:#c9d1d9;">Sparepart:</span> <span style="color:#ffd700;font-weight:bold;">$ 2.0 /unit</span></div>';
                        html += '<div style="margin-top:8px;padding:6px;background:#21262d;border-radius:4px;font-size:11px;color:#f85149;">⚠️ Gagal load dari database</div>';
                        const configList = document.getElementById('pm-config-list');
                        if (configList) configList.innerHTML = html;
                    }
                }

                async function savePriceConfig() {
                    const data = {
                        repair: parseFloat(document.getElementById('pm-repair').value) || 3.0,
                        modif: parseFloat(document.getElementById('pm-modif').value) || 3.0,
                        brother: parseFloat(document.getElementById('pm-brother').value) || 2.3,
                        ws_stored: parseFloat(document.getElementById('pm-ws_stored').value) || 2.0,
                        sparepart: parseFloat(document.getElementById('pm-sparepart').value) || 2.0
                    };

                    try {
                        // Save price config
                        for (const [jenis, multiplier] of Object.entries(data)) {
                            if (jenis === 'sparepart') {
                                await fetch('api/sparepart_config_api.php', {
                                    method: 'POST',
                                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                                    body: 'action=update&harga_per_unit=' + multiplier
                                });
                            } else {
                                await fetch('api/price_config_api.php', {
                                    method: 'POST',
                                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                                    body: 'action=update&jenis=' + jenis + '&multiplier=' + multiplier
                                });
                            }
                        }

                        // Update localStorage calcConfig for immediate effect
                        const calcConfigUpdate = {
                            multipliers: {
                                repair: data.repair,
                                modif: data.modif,
                                brother: data.brother,
                                ws_stored: data.ws_stored
                            },
                            sparepartPricePerUnit: data.sparepart,
                            timestamp: Date.now()
                        };
                        localStorage.setItem('calcConfig', JSON.stringify(calcConfigUpdate));

                        const success = document.getElementById('pm-success');
                        success.style.display = 'block';
                        setTimeout(() => { success.style.display = 'none'; }, 3000);
                    } catch (e) {
                        alert('Gagal menyimpan konfigurasi!');
                    }
                }

                // Auto-load price config on page load
            window.addEventListener('load', function() {
                setTimeout(loadPriceConfig, 800);
            });

            // Also load when window becomes visible
            const mmWindow = document.getElementById('win-mechanicmanager');
            if (mmWindow) {
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                            if (mmWindow.style.display === 'block') {
                                loadPriceConfig();
                            }
                        }
                    });
                });
                observer.observe(mmWindow, { attributes: true });
            }
            </script>
        </div>
        <div class="window-statusbar">
            <span class="statusbar-section">Mechanic Manager</span>
            <span class="statusbar-section">Atur harga component</span>
        </div>
        <div class="resizer" onmousedown="startResize(event, 'win-mechanicmanager')"></div>
    </div>

    <!-- FARMER MANAGER WINDOW -->
    <div class="window" id="win-farmermanager" style="top:110px;left:170px;width:650px;height:450px;display:none;">
        <div class="window-titlebar" onmousedown="startDrag(event, 'win-farmermanager')">
            <svg class="window-icon" viewBox="0 0 16 16">
                <rect x="2" y="8" width="12" height="6" fill="#228b22"/>
                <circle cx="5" cy="6" r="3" fill="#90ee90"/>
                <circle cx="11" cy="6" r="3" fill="#90ee90"/>
            </svg>
            <span class="window-title">Farmer Manager - Brothers Company</span>
            <div class="window-controls">
                <button class="window-btn window-btn-max" onclick="minimizeWindow('win-farmermanager')">_</button>
                <button class="window-btn window-btn-max" onclick="maximizeWindow('win-farmermanager')">□</button>
                <button class="window-btn" onclick="closeWindow('win-farmermanager')">✕</button>
            </div>
        </div>
        <div class="window-menubar">
            <span class="menu-item">File</span>
            <span class="menu-item">View</span>
            <span class="menu-item">Reports</span>
            <span class="menu-item">Help</span>
        </div>
        <div class="window-content" style="padding:0;background:#0d1117;height:calc(100% - 48px);overflow:auto;">
            <div style="width:100%;min-height:100%;background:#0d1117;padding:15px;box-sizing:border-box;font-family:'Segoe UI',sans-serif;color:#c9d1d9;">
                <style>
                    .fm-title { color:#4db84d;font-size:16px;margin:0 0 15px 0;text-align:center; }
                    .fm-tabs { display:flex;gap:5px;margin-bottom:15px; }
                    .fm-tab { padding:8px 16px;background:#21262d;border:1px solid #30363d;border-radius:6px;color:#8b949e;font-size:12px;cursor:pointer; }
                    .fm-tab.active { background:#4db84d;color:#fff;border-color:#4db84d; }
                    .fm-section { display:none; }
                    .fm-section.active { display:block; }
                    .fm-form { background:#161b22;padding:15px;border-radius:8px;border:1px solid #30363d;margin-bottom:15px; }
                    .fm-form-row { display:flex;gap:10px;margin-bottom:10px; }
                    .fm-form input, .fm-form select { flex:1;padding:8px;border-radius:4px;border:1px solid #30363d;background:#010409;color:#fff;font-size:12px; }
                    .fm-form button { padding:8px 16px;background:#4db84d;color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:bold;cursor:pointer; }
                    .fm-form button:hover { background:#5ecc5e; }
                    .fm-table { width:100%;border-collapse:collapse;font-size:12px; }
                    .fm-table th { background:#21262d;color:#8b949e;padding:10px;border:1px solid #30363d; }
                    .fm-table td { padding:10px;border:1px solid #30363d;color:#c9d1d9; }
                    .fm-table tr:hover td { background:#21262d; }
                    .fm-status { padding:4px 8px;border-radius:4px;font-size:10px;font-weight:bold; }
                    .fm-done { background:#238636;color:#fff; }
                    .fm-progress { background:#e8b828;color:#000; }
                    .fm-pending { background:#cc2222;color:#fff; }
                    .fm-btn-del { background:#cc2222;color:#fff;border:none;padding:4px 8px;border-radius:4px;cursor:pointer;font-size:10px; }
                    .fm-btn-edit { background:#58a6ff;color:#fff;border:none;padding:4px 8px;border-radius:4px;cursor:pointer;font-size:10px;margin-right:4px; }
                    .fm-success { display:none;background:#238636;color:#fff;padding:8px;border-radius:4px;text-align:center;margin-top:10px;font-size:12px; }
                </style>
                <h2 class="fm-title">🌱 Farmer Manager</h2>

                <div class="fm-tabs">
                    <button class="fm-tab active" onclick="switchFmTab('stats')">📊 Statistik</button>
                    <button class="fm-tab" onclick="switchFmTab('tugas')">📋 Kelola Tugas Harian</button>
                </div>

                <!-- Stats Section -->
                <div class="fm-section active" id="fm-stats">
                    <div class="fm-stats">
                        <div class="fm-stat">
                            <div class="fm-stat-value">2</div>
                            <div class="fm-stat-label">Total Farmer</div>
                        </div>
                        <div class="fm-stat">
                            <div class="fm-stat-value">120</div>
                            <div class="fm-stat-label">Bibit Tertanam</div>
                        </div>
                        <div class="fm-stat">
                            <div class="fm-stat-value">85</div>
                            <div class="fm-stat-label">Total Panen (kg)</div>
                        </div>
                    </div>
                </div>

                <!-- Tugas Harian Section -->
                <div class="fm-section" id="fm-tugas">
                    <div class="fm-form">
                        <div style="font-size:11px;color:#8b949e;margin-bottom:10px;">📝 Tambah Tugas Baru - <span id="fm-tanggal-label" style="color:#4db84d;font-weight:bold;"></span></div>
                        <div class="fm-form-row">
                            <input type="date" id="fm-tugas-tanggal" style="flex:1;">
                            <input type="time" id="fm-tugas-mulai" value="08:00" placeholder="Jam mulai" style="flex:1;">
                            <span style="color:#8b949e;padding:0 5px;">-</span>
                            <input type="time" id="fm-tugas-selesai" value="09:00" placeholder="Jam selesai" style="flex:1;">
                            <input type="text" id="fm-tugas-nama" placeholder="Nama tugas..." style="flex:2;">
                            <button onclick="addFmTugas()">➕</button>
                        </div>
                        <div style="margin-bottom:10px;display:flex;gap:10px;align-items:center;">
                            <span style="font-size:12px;color:#8b949e;">📅 Filter:</span>
                            <input type="date" id="fm-filter-tanggal" onchange="loadFmTugas()" style="padding:6px;border-radius:4px;border:1px solid #30363d;background:#010409;color:#fff;font-size:12px;">
                            <button onclick="document.getElementById('fm-filter-tanggal').value=getTodayJakarta();loadFmTugas();" style="padding:6px 12px;background:#30363d;color:#fff;border:none;border-radius:4px;font-size:11px;cursor:pointer;">🗓️ Hari Ini</button>
                        </div>
                    </div>
                    <table class="fm-table" id="fm-tugas-table">
                        <thead><tr><th>Jam Mulai</th><th>Jam Selesai</th><th>Tugas</th><th>Status</th><th>Aksi</th></tr></thead>
                        <tbody id="fm-tugas-body"><tr><td colspan="5" style="text-align:center;color:#8b949e;">Memuat...</td></tr></tbody>
                    </table>
                </div>
            </div>
        </div>
        <script>
            function switchFmTab(tab) {
                document.querySelectorAll('.fm-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.fm-section').forEach(s => s.classList.remove('active'));
                event.target.classList.add('active');
                document.getElementById('fm-' + tab).classList.add('active');
                if (tab === 'tugas') {
                    // Set default date to today (Jakarta time)
                    const today = getTodayJakarta();
                    document.getElementById('fm-tugas-tanggal').value = today;
                    document.getElementById('fm-tanggal-label').innerText = formatDate(today);
                    loadFmTugas();
                }
            }

            async function loadFmTugas() {
                const tbody = document.getElementById('fm-tugas-body');
                const filterDate = document.getElementById('fm-filter-tanggal').value;
                if (!tbody) return;

                let url = 'api/tugas_harian_api.php?action=get_today';
                if (filterDate) {
                    url = 'api/tugas_harian_api.php?action=get_all&tanggal=' + filterDate;
                }

                try {
                    const res = await fetch(url);
                    const result = await res.json();
                    if (result.success && result.data && result.data.length > 0) {
                        let html = '';
                        result.data.forEach(item => {
                            const statusClass = item.status === 'selesai' ? 'fm-done' : (item.status === 'proses' ? 'fm-progress' : 'fm-pending');
                            const statusText = item.status === 'selesai' ? 'Selesai' : (item.status === 'proses' ? 'Proses' : 'Pending');
                            const jamMulai = item.jam_mulai ? item.jam_mulai.substring(0, 5) : '00:00';
                            const jamSelesai = item.jam_selesai ? item.jam_selesai.substring(0, 5) : '00:00';
                            const tanggal = item.tanggal ? formatDate(item.tanggal) : '';
                            html += '<tr>' +
                                '<td>' + jamMulai + '</td>' +
                                '<td>' + jamSelesai + '</td>' +
                                '<td>' + item.tugas + '</td>' +
                                '<td><span class="fm-status ' + statusClass + '">' + statusText + '</span></td>' +
                                '<td><button class="fm-btn-edit" onclick="editFmTugas(' + item.id + ')">✏️</button><button class="fm-btn-del" onclick="deleteFmTugas(' + item.id + ')">🗑️</button></td>' +
                            '</tr>';
                        });
                        tbody.innerHTML = html;
                    } else {
                        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:#8b949e;">Tidak ada tugas</td></tr>';
                    }
                } catch (e) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:#f85149;">Gagal memuat data</td></tr>';
                }
            }

            async function addFmTugas() {
                const tanggal = document.getElementById('fm-tugas-tanggal').value;
                const jamMulai = document.getElementById('fm-tugas-mulai').value;
                const jamSelesai = document.getElementById('fm-tugas-selesai').value;
                const tugas = document.getElementById('fm-tugas-nama').value;

                if (!tugas) { alert('Nama tugas harus diisi!'); return; }
                if (!jamMulai || !jamSelesai) { alert('Jam mulai dan selesai harus diisi!'); return; }

                try {
                    const fd = new FormData();
                    fd.append('action', 'add');
                    fd.append('tanggal', tanggal);
                    fd.append('jam_mulai', jamMulai + ':00');
                    fd.append('jam_selesai', jamSelesai + ':00');
                    fd.append('tugas', tugas);
                    fd.append('status', 'pending');

                    const res = await fetch('api/tugas_harian_api.php', { method: 'POST', body: fd });
                    const result = await res.json();
                    if (result.success) {
                        document.getElementById('fm-tugas-nama').value = '';
                        document.getElementById('fm-tugas-success').style.display = 'block';
                        document.getElementById('fm-tanggal-label').innerText = formatDate(tanggal);
                        setTimeout(() => { document.getElementById('fm-tugas-success').style.display = 'none'; }, 3000);
                        loadFmTugas();
                    }
                } catch (e) {
                    alert('Gagal menambahkan tugas!');
                }
            }

            async function deleteFmTugas(id) {
                if (!confirm('Yakin hapus tugas ini?')) return;
                try {
                    const fd = new FormData();
                    fd.append('action', 'delete');
                    fd.append('id', id);
                    await fetch('api/tugas_harian_api.php', { method: 'POST', body: fd });
                    loadFmTugas();
                } catch (e) {}
            }

            function editFmTugas(id) {
                alert('Fitur edit dalam pengembangan');
            }
        </script>
        <div class="window-statusbar">
            <span class="statusbar-section">Farmer Manager</span>
            <span class="statusbar-section">Kelola farmer</span>
        </div>
        <div class="resizer" onmousedown="startResize(event, 'win-farmermanager')"></div>
    </div>

    <!-- CARGO MANAGER WINDOW -->
    <div class="window" id="win-cargomanager" style="top:120px;left:180px;width:700px;height:450px;display:none;">
        <div class="window-titlebar" onmousedown="startDrag(event, 'win-cargomanager')">
            <svg class="window-icon" viewBox="0 0 16 16">
                <rect x="2" y="4" width="12" height="10" rx="1" fill="#8b5cf6"/>
                <rect x="4" y="6" width="8" height="6" fill="#c4b5fd"/>
            </svg>
            <span class="window-title">Cargo Manager - Brothers Company</span>
            <div class="window-controls">
                <button class="window-btn window-btn-max" onclick="minimizeWindow('win-cargomanager')">_</button>
                <button class="window-btn window-btn-max" onclick="maximizeWindow('win-cargomanager')">□</button>
                <button class="window-btn" onclick="closeWindow('win-cargomanager')">✕</button>
            </div>
        </div>
        <div class="window-menubar">
            <span class="menu-item">File</span>
            <span class="menu-item">View</span>
            <span class="menu-item">Reports</span>
            <span class="menu-item">Help</span>
        </div>
        <div class="window-content" style="padding:0;background:#0d1117;height:calc(100% - 48px);overflow:auto;">
            <div style="width:100%;min-height:100%;background:#0d1117;padding:15px;box-sizing:border-box;font-family:'Segoe UI',sans-serif;color:#c9d1d9;">
                <style>
                    .cm-title { color:#8b5cf6;font-size:16px;margin:0 0 15px 0;text-align:center; }
                    .cm-tabs { display:flex;gap:5px;margin-bottom:15px; }
                    .cm-tab { padding:8px 16px;background:#21262d;border:1px solid #30363d;border-radius:6px;color:#8b949e;font-size:12px;cursor:pointer; }
                    .cm-tab.active { background:#8b5cf6;color:#fff;border-color:#8b5cf6; }
                    .cm-section { display:none; }
                    .cm-section.active { display:block; }
                    .cm-form { background:#161b22;padding:15px;border-radius:8px;border:1px solid #30363d;margin-bottom:15px; }
                    .cm-form-row { display:flex;gap:10px;margin-bottom:10px; }
                    .cm-form input, .cm-form select { flex:1;padding:8px;border-radius:4px;border:1px solid #30363d;background:#010409;color:#fff;font-size:12px; }
                    .cm-form button { padding:8px 16px;background:#8b5cf6;color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:bold;cursor:pointer; }
                    .cm-form button:hover { background:#a78bfa; }
                    .cm-table { width:100%;border-collapse:collapse;font-size:12px; }
                    .cm-table th { background:#21262d;color:#8b949e;padding:10px;border:1px solid #30363d; }
                    .cm-table td { padding:10px;border:1px solid #30363d;color:#c9d1d9; }
                    .cm-table tr:hover td { background:#21262d; }
                    .cm-status { padding:4px 8px;border-radius:4px;font-size:10px;font-weight:bold; }
                    .cm-pending { background:#cc2222;color:#fff; }
                    .cm-diambil { background:#e8b828;color:#000; }
                    .cm-selesai { background:#238636;color:#fff; }
                    .cm-batal { background:#6b7280;color:#fff; }
                    .cm-btn { background:#cc2222;color:#fff;border:none;padding:4px 8px;border-radius:4px;cursor:pointer;font-size:10px; }
                    .cm-success { display:none;background:#238636;color:#fff;padding:8px;border-radius:4px;text-align:center;margin-top:10px;font-size:12px; }
                    .cm-filter { display:flex;gap:10px;margin-bottom:15px;align-items:center; }
                    .cm-filter select { padding:8px;border-radius:4px;border:1px solid #30363d;background:#010409;color:#fff;font-size:12px; }
                </style>
                <h2 class="cm-title">📦 Cargo Manager</h2>

                <div class="cm-tabs">
                    <button class="cm-tab active" onclick="switchCmTab('stats')">📊 Statistik</button>
                    <button class="cm-tab" onclick="switchCmTab('order')">📋 Order Delivery</button>
                </div>

                <!-- Stats Section -->
                <div class="cm-section active" id="cm-stats">
                    <div class="cm-stats">
                        <div class="cm-stat">
                            <div class="cm-stat-value" id="cm-total-driver">0</div>
                            <div class="cm-stat-label">Total Driver</div>
                        </div>
                        <div class="cm-stat">
                            <div class="cm-stat-value" id="cm-pending">0</div>
                            <div class="cm-stat-label">Menunggu</div>
                        </div>
                        <div class="cm-stat">
                            <div class="cm-stat-value" id="cm-diambil">0</div>
                            <div class="cm-stat-label">Diambil</div>
                        </div>
                        <div class="cm-stat">
                            <div class="cm-stat-value" id="cm-selesai">0</div>
                            <div class="cm-stat-label">Selesai</div>
                        </div>
                    </div>
                </div>

                <!-- Order Delivery Section -->
                <div class="cm-section" id="cm-order">
                    <div class="cm-form">
                        <div style="font-size:11px;color:#8b949e;margin-bottom:10px;">📝 Tambah Order Delivery Baru (Max 50)</div>
                        <div class="cm-form-row">
                            <select id="cm-jenis" style="flex:1;" onchange="toggleCmFarmerOptions()">
                                <option value="">-- Pilih Jenis --</option>
                                <option value="compo">🔧 Deliver Compo</option>
                                <option value="farmer_beli">🌱 Deliver Farmer - Beli Bibit</option>
                                <option value="farmer_jual">🍎 Deliver Farmer - Jual Buah</option>
                            </select>
                            <input type="number" id="cm-crate" placeholder="Jumlah Crate (1-50)" min="1" max="50" value="1" style="width:100px;">
                            <input type="text" id="cm-alamat" placeholder="Alamat tujuan..." style="flex:2;">
                        </div>
                        <div class="cm-form-row">
                            <input type="text" id="cm-penerima" placeholder="Nama penerima..." style="flex:1;">
                            <input type="tel" id="cm-telepon" placeholder="No. Telepon..." style="flex:1;">
                            <input type="text" id="cm-catatan" placeholder="Catatan (opsional)..." style="flex:1;">
                            <button onclick="addDeliveryOrder()">➕ Tambah</button>
                        </div>
                        <div id="cm-success" class="cm-success">✅ Order berhasil ditambahkan!</div>
                    </div>

                    <div class="cm-filter">
                        <span style="font-size:12px;color:#8b949e;">Filter:</span>
                        <select id="cm-filter-jenis" onchange="loadDeliveryOrders()">
                            <option value="">Semua Jenis</option>
                            <option value="compo">🔧 Deliver Compo</option>
                            <option value="farmer_beli">🌱 Beli Bibit</option>
                            <option value="farmer_jual">🍎 Jual Buah</option>
                        </select>
                        <select id="cm-filter-status" onchange="loadDeliveryOrders()">
                            <option value="">Semua Status</option>
                            <option value="pending">⏳ Menunggu</option>
                            <option value="diambil">🚚 Diambil</option>
                            <option value="selesai">✅ Selesai</option>
                            <option value="batal">❌ Batal</option>
                        </select>
                    </div>

                    <table class="cm-table">
                        <thead><tr><th>Jenis</th><th>Crate</th><th>Alamat</th><th>Penerima</th><th>Telepon</th><th>Status</th><th>Driver</th><th>Aksi</th></tr></thead>
                        <tbody id="cm-order-body"><tr><td colspan="8" style="text-align:center;color:#8b949e;">Memuat...</td></tr></tbody>
                    </table>
                </div>
            </div>
        </div>
        <script>
            function switchCmTab(tab) {
                document.querySelectorAll('.cm-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.cm-section').forEach(s => s.classList.remove('active'));
                event.target.classList.add('active');
                document.getElementById('cm-' + tab).classList.add('active');
                if (tab === 'order') loadDeliveryOrders();
                if (tab === 'stats') loadCmStats();
            }

            async function loadCmStats() {
                try {
                    const res = await fetch('api/delivery_order_api.php?action=get_all');
                    const result = await res.json();
                    if (result.success && result.data) {
                        const data = result.data;
                        document.getElementById('cm-pending').textContent = data.filter(d => d.status === 'pending').length;
                        document.getElementById('cm-diambil').textContent = data.filter(d => d.status === 'diambil').length;
                        document.getElementById('cm-selesai').textContent = data.filter(d => d.status === 'selesai').length;
                        // Get driver count
                        const driverRes = await fetch('api/delivery_order_api.php?action=get_employees_cargo');
                        const driverResult = await driverRes.json();
                        document.getElementById('cm-total-driver').textContent = driverResult.success ? driverResult.data.length : 0;
                    }
                } catch (e) {}
            }

            async function loadDeliveryOrders() {
                const tbody = document.getElementById('cm-order-body');
                if (!tbody) return;

                const jenis = document.getElementById('cm-filter-jenis').value;
                const status = document.getElementById('cm-filter-status').value;
                let url = 'api/delivery_order_api.php?action=get_all';
                if (jenis) url += '&jenis=' + jenis;
                if (status) url += '&status=' + status;

                try {
                    const res = await fetch(url);
                    const result = await res.json();
                    if (result.success && result.data && result.data.length > 0) {
                        let html = '';
                        result.data.forEach(item => {
                            const statusClass = item.status === 'selesai' ? 'cm-selesai' : (item.status === 'diambil' ? 'cm-diambil' : (item.status === 'batal' ? 'cm-batal' : 'cm-pending'));
                            const statusText = item.status === 'selesai' ? 'Selesai' : (item.status === 'diambil' ? 'Diambil' : (item.status === 'batal' ? 'Batal' : 'Menunggu'));

                            // Determine icon and label based on jenis_delivery
                            let jenisLabel = item.jenis_delivery;
                            let jenisIcon = '🔧';
                            if (item.jenis_delivery === 'farmer_beli') {
                                jenisIcon = '🌱';
                                jenisLabel = 'Beli Bibit';
                            } else if (item.jenis_delivery === 'farmer_jual') {
                                jenisIcon = '🍎';
                                jenisLabel = 'Jual Buah';
                            } else if (item.jenis_delivery === 'compo') {
                                jenisIcon = '🔧';
                                jenisLabel = 'Compo';
                            } else if (item.jenis_delivery === 'farmer') {
                                jenisIcon = '🌱';
                                jenisLabel = 'Farmer';
                            }

                            const driverInfo = item.driver_nama ? item.driver_nama : '-';
                            html += '<tr>' +
                                '<td>' + jenisIcon + ' ' + jenisLabel + '</td>' +
                                '<td>' + item.jumlah_crate + ' crate</td>' +
                                '<td>' + item.alamat_tujuan + '</td>' +
                                '<td>' + item.nama_penerima + '</td>' +
                                '<td>' + (item.no_telepon || '-') + '</td>' +
                                '<td><span class="cm-status ' + statusClass + '">' + statusText + '</span></td>' +
                                '<td>' + driverInfo + '</td>' +
                                '<td><button class="cm-btn" onclick="deleteDeliveryOrder(' + item.id + ')">🗑️</button></td>' +
                            '</tr>';
                        });
                        tbody.innerHTML = html;
                    } else {
                        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:#8b949e;">Tidak ada order</td></tr>';
                    }
                } catch (e) {
                    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:#f85149;">Gagal memuat data</td></tr>';
                }
            }

            async function addDeliveryOrder() {
                const jenis = document.getElementById('cm-jenis').value;
                const crate = document.getElementById('cm-crate').value;
                const alamat = document.getElementById('cm-alamat').value;
                const penerima = document.getElementById('cm-penerima').value;
                const telepon = document.getElementById('cm-telepon').value;
                const catatan = document.getElementById('cm-catatan').value;

                if (!jenis || !alamat || !penerima) {
                    alert('Jenis, Alamat, dan Penerima harus diisi!');
                    return;
                }

                // Validate farmer types
                if (jenis === 'farmer_beli' || jenis === 'farmer_jual') {
                    const label = jenis === 'farmer_beli' ? 'Beli Bibit' : 'Jual Buah';
                    alert('📦 Deliver Farmer - ' + label + '\nMax: 50 crate per delivery');
                }

                const crateNum = parseInt(crate) || 1;
                if (crateNum < 1 || crateNum > 50) {
                    alert('Jumlah crate minimal 1, maksimal 50!');
                    return;
                }

                try {
                    const fd = new FormData();
                    fd.append('action', 'add');
                    fd.append('jenis_delivery', jenis);
                    fd.append('jumlah_crate', crateNum);
                    fd.append('alamat_tujuan', alamat);
                    fd.append('nama_penerima', penerima);
                    fd.append('no_telepon', telepon);
                    fd.append('catatan', catatan);

                    const res = await fetch('api/delivery_order_api.php', { method: 'POST', body: fd });
                    const result = await res.json();
                    if (result.success) {
                        document.getElementById('cm-jenis').value = '';
                        document.getElementById('cm-crate').value = '1';
                        document.getElementById('cm-alamat').value = '';
                        document.getElementById('cm-penerima').value = '';
                        document.getElementById('cm-telepon').value = '';
                        document.getElementById('cm-catatan').value = '';
                        document.getElementById('cm-success').style.display = 'block';
                        setTimeout(() => { document.getElementById('cm-success').style.display = 'none'; }, 3000);
                        loadDeliveryOrders();
                    } else {
                        alert(result.message || 'Gagal menambah order');
                    }
                } catch (e) {
                    alert('Gagal menambah order!');
                }
            }

            async function deleteDeliveryOrder(id) {
                if (!confirm('Yakin hapus order ini?')) return;
                try {
                    const fd = new FormData();
                    fd.append('action', 'delete');
                    fd.append('id', id);
                    await fetch('api/delivery_order_api.php', { method: 'POST', body: fd });
                    loadDeliveryOrders();
                } catch (e) {}
            }

            // Auto-refresh every 15 seconds
            setInterval(() => {
                if (document.getElementById('win-cargomanager').style.display === 'block') {
                    const orderSection = document.getElementById('cm-order');
                    if (orderSection && orderSection.classList.contains('active')) {
                        loadDeliveryOrders();
                    }
                }
            }, 15000);
        </script>
        <div class="window-statusbar">
            <span class="statusbar-section">Cargo Manager</span>
            <span class="statusbar-section">Kelola cargo</span>
        </div>
        <div class="resizer" onmousedown="startResize(event, 'win-cargomanager')"></div>
    </div>

    <!-- PRICE & SALARY CONFIG WINDOW -->
    <div class="window" id="win-priceconfig" style="top:80px;left:120px;width:780px;height:520px;display:none;">
        <div class="window-titlebar" onmousedown="startDrag(event, 'win-priceconfig')">
            <svg class="window-icon" viewBox="0 0 16 16">
                <rect x="1" y="2" width="14" height="12" rx="2" fill="#9333ea"/>
                <rect x="3" y="4" width="10" height="4" fill="#a855f7"/>
                <rect x="4" y="10" width="4" height="2" fill="#c084fc"/>
                <rect x="10" y="10" width="3" height="3" fill="#e9d5ff"/>
            </svg>
            <span class="window-title">Price & Salary Config - Brothers Company</span>
            <div class="window-controls">
                <button class="window-btn window-btn-max" onclick="minimizeWindow('win-priceconfig')">_</button>
                <button class="window-btn window-btn-max" onclick="maximizeWindow('win-priceconfig')">□</button>
                <button class="window-btn" onclick="closeWindow('win-priceconfig')">✕</button>
            </div>
        </div>
        <div class="window-menubar">
            <span class="menu-item">File</span>
            <span class="menu-item">View</span>
            <span class="menu-item">Help</span>
        </div>
        <div class="window-content" style="padding:0;background:#0d1117;height:calc(100% - 48px);overflow:auto;">
            <div style="width:100%;min-height:100%;background:#0d1117;padding:15px;box-sizing:border-box;font-family:'Segoe UI',sans-serif;color:#c9d1d9;">
                <style>
                    .psc-container { max-width:750px; margin:0 auto; }
                    .psc-header { text-align:center; margin-bottom:20px; }
                    .psc-header h2 { color:#a855f7; font-size:18px; margin:0 0 5px 0; }
                    .psc-header p { color:#8b949e; font-size:12px; margin:0; }
                    .psc-tabs { display:flex; gap:5px; margin-bottom:15px; flex-wrap:wrap; }
                    .psc-tab { padding:8px 16px; background:#21262d; border:1px solid #30363d; border-radius:6px; color:#8b949e; font-size:12px; cursor:pointer; font-weight:600; }
                    .psc-tab.active { background:#9333ea; color:#fff; border-color:#9333ea; }
                    .psc-section { display:none; }
                    .psc-section.active { display:block; }
                    .psc-card { background:#161b22; border:1px solid #30363d; border-radius:8px; padding:15px; margin-bottom:15px; }
                    .psc-card-title { font-size:14px; font-weight:bold; margin-bottom:15px; padding-bottom:10px; border-bottom:1px solid #30363d; }
                    .psc-row { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
                    .psc-row label { flex:0 0 220px; font-size:12px; color:#c9d1d9; text-align:right; }
                    .psc-row input { flex:1; padding:8px 12px; border-radius:6px; border:1px solid #30363d; background:#010409; color:#fff; font-size:13px; text-align:right; }
                    .psc-row input:focus { outline:none; border-color:#9333ea; }
                    .psc-row .unit { flex:0 0 80px; font-size:11px; color:#8b949e; text-align:left; }
                    .psc-btn-row { display:flex; gap:10px; justify-content:center; margin-top:15px; }
                    .psc-btn { padding:10px 24px; border:none; border-radius:6px; font-size:13px; font-weight:bold; cursor:pointer; }
                    .psc-btn-save { background:#9333ea; color:#fff; }
                    .psc-btn-save:hover { background:#a855f7; }
                    .psc-btn-reset { background:#30363d; color:#8b949e; }
                    .psc-btn-reset:hover { background:#3d444d; }
                    .psc-success { display:none; background:#238636; color:#fff; padding:10px; border-radius:6px; text-align:center; font-size:12px; margin-top:10px; }
                    .psc-info { font-size:11px; color:#8b949e; text-align:center; margin-top:5px; }
                    .psc-card.cargo { border-left:3px solid #8b5cf6; }
                    .psc-card.farm { border-left:3px solid #4db84d; }
                    .psc-card.mechanic { border-left:3px solid #245edc; }
                </style>

                <div class="psc-container">
                    <div class="psc-header">
                        <h2>⚙️ Price & Salary Configuration</h2>
                        <p>Atur harga dan gaji untuk semua divisi - Changes saved to database</p>
                    </div>

                    <!-- CURRENT PRICES PREVIEW -->
                    <div id="psc-preview" style="background:#161b22;border:1px solid #30363d;border-radius:8px;padding:15px;margin-bottom:20px;">
                        <div style="font-size:14px;font-weight:bold;color:#a855f7;margin-bottom:12px;">📋 Harga Saat Ini</div>
                        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;">
                            <div style="background:#21262d;border-radius:6px;padding:10px;text-align:center;border-left:3px solid #8b5cf6;">
                                <div style="font-size:10px;color:#8b949e;">📦 CARGO</div>
                                <div style="font-size:12px;color:#fff;margin-top:4px;">Gaji/Crate</div>
                                <div style="font-size:16px;color:#a855f7;font-weight:bold;" id="preview-cargo_gaji_per_crate">$63.00</div>
                            </div>
                            <div style="background:#21262d;border-radius:6px;padding:10px;text-align:center;border-left:3px solid #4db84d;">
                                <div style="font-size:10px;color:#8b949e;">🌱 FARM</div>
                                <div style="font-size:12px;color:#fff;margin-top:4px;">Gaji/Bibit</div>
                                <div style="font-size:16px;color:#4db84d;font-weight:bold;" id="preview-farm_gaji_per_bibit">$22.00</div>
                            </div>
                            <div style="background:#21262d;border-radius:6px;padding:10px;text-align:center;border-left:3px solid #4db84d;">
                                <div style="font-size:10px;color:#8b949e;">🌱 FARM</div>
                                <div style="font-size:12px;color:#fff;margin-top:4px;">Jual Buah</div>
                                <div style="font-size:16px;color:#4db84d;font-weight:bold;" id="preview-farm_harga_jual_buah">$20.00</div>
                            </div>
                            <div style="background:#21262d;border-radius:6px;padding:10px;text-align:center;border-left:3px solid #4db84d;">
                                <div style="font-size:10px;color:#8b949e;">🌱 FARM</div>
                                <div style="font-size:12px;color:#fff;margin-top:4px;">Beli Bibit</div>
                                <div style="font-size:16px;color:#4db84d;font-weight:bold;" id="preview-farm_harga_bibit">$20.00</div>
                            </div>
                            <div style="background:#21262d;border-radius:6px;padding:10px;text-align:center;border-left:3px solid #245edc;">
                                <div style="font-size:10px;color:#8b949e;">🔧 MECHANIC</div>
                                <div style="font-size:12px;color:#fff;margin-top:4px;">Gaji Pokok</div>
                                <div style="font-size:16px;color:#58a6ff;font-weight:bold;" id="preview-mechanic_gaji_dasar">$0.00</div>
                            </div>
                            <div style="background:#21262d;border-radius:6px;padding:10px;text-align:center;border-left:3px solid #245edc;">
                                <div style="font-size:10px;color:#8b949e;">🔧 MECHANIC</div>
                                <div style="font-size:12px;color:#fff;margin-top:4px;">Component</div>
                                <div style="font-size:16px;color:#58a6ff;font-weight:bold;" id="preview-mechanic_harga_component">$1000.00</div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="psc-tabs">
                        <button class="psc-tab active" onclick="switchPscTab('cargo')">📦 CARGO</button>
                        <button class="psc-tab" onclick="switchPscTab('farm')">🌱 FARM</button>
                        <button class="psc-tab" onclick="switchPscTab('mechanic')">🔧 MECHANIC</button>
                    </div>

                    <!-- CARGO SECTION -->
                    <div class="psc-section active" id="psc-cargo">
                        <div class="psc-card cargo">
                            <div class="psc-card-title">📦 Cargo Driver - Gaji per Crate</div>
                            <div class="psc-row">
                                <label>Gaji per Crate</label>
                                <input type="number" id="psc-cargo_gaji_per_crate" step="0.01" min="0">
                                <span class="unit">$/crate</span>
                            </div>
                            <div class="psc-info">Gaji yang diterima cargo driver untuk setiap 1 crate yang diantar</div>
                        </div>
                        <div class="psc-btn-row">
                            <button class="psc-btn psc-btn-save" onclick="savePscConfig('cargo')">💾 Simpan Cargo</button>
                        </div>
                    </div>

                    <!-- FARM SECTION -->
                    <div class="psc-section" id="psc-farm">
                        <div class="psc-card farm">
                            <div class="psc-card-title">🌱 Farm Worker - Gaji & Harga</div>
                            <div class="psc-row">
                                <label>Gaji per Bibit Ditanam</label>
                                <input type="number" id="psc-farm_gaji_per_bibit" step="0.01" min="0">
                                <span class="unit">$/bibit</span>
                            </div>
                            <div class="psc-row">
                                <label>Harga Jual Buah</label>
                                <input type="number" id="psc-farm_harga_jual_buah" step="0.01" min="0">
                                <span class="unit">$/kg</span>
                            </div>
                            <div class="psc-row">
                                <label>Harga Beli Bibit</label>
                                <input type="number" id="psc-farm_harga_bibit" step="0.01" min="0">
                                <span class="unit">$/kg</span>
                            </div>
                            <div class="psc-info">Gaji farmer per bibit, harga jual hasil panen, dan harga beli bibit</div>
                        </div>
                        <div class="psc-btn-row">
                            <button class="psc-btn psc-btn-save" onclick="savePscConfig('farm')">💾 Simpan Farm</button>
                        </div>
                    </div>

                    <!-- MECHANIC SECTION -->
                    <div class="psc-section" id="psc-mechanic">
                        <div class="psc-card mechanic">
                            <div class="psc-card-title">🔧 Mechanic - Gaji & Component</div>
                            <div class="psc-row">
                                <label>Gaji Pokok Mechanic</label>
                                <input type="number" id="psc-mechanic_gaji_dasar" step="0.01" min="0">
                                <span class="unit">$</span>
                            </div>
                            <div class="psc-row">
                                <label>Harga Component / Sparepart</label>
                                <input type="number" id="psc-mechanic_harga_component" step="0.01" min="0">
                                <span class="unit">$/crate</span>
                            </div>
                            <div class="psc-info">Gaji pokok mechanic per periode dan harga sparepart/component per crate</div>
                        </div>
                        <div class="psc-btn-row">
                            <button class="psc-btn psc-btn-save" onclick="savePscConfig('mechanic')">💾 Simpan Mechanic</button>
                        </div>
                    </div>

                    <!-- Simpan Semua -->
                    <div style="margin-top:15px; text-align:center;">
                        <div class="psc-btn-row">
                            <button class="psc-btn psc-btn-save" style="background:#238636;" onclick="savePscAll()">💾💾 Simpan Semua</button>
                        </div>
                        <div id="psc-success" class="psc-success">✓ Konfigurasi berhasil disimpan!</div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            // Load all config from DB when window opens
            document.getElementById('win-priceconfig').addEventListener('focus', loadPscConfig);

            async function loadPscConfig() {
                try {
                    const res = await fetch('api/farm_price_config_api.php?action=get_all');
                    const json = await res.json();
                    if (json.success && json.data) {
                        json.data.forEach(item => {
                            // Update input field
                            const el = document.getElementById('psc-' + item.config_key);
                            if (el) el.value = item.config_value;
                            // Update preview box
                            const preview = document.getElementById('preview-' + item.config_key);
                            if (preview) {
                                const formatted = parseFloat(item.config_value).toFixed(2);
                                preview.textContent = '$' + formatted;
                            }
                        });
                    }
                } catch (e) {
                    console.error('Failed to load price config:', e);
                }
            }

            async function savePscConfig(category) {
                const inputs = document.querySelectorAll('#psc-' + category + ' input[type="number"]');
                const configs = [];
                inputs.forEach(input => {
                    const key = input.id.replace('psc-', '');
                    configs.push({ key: key, value: parseFloat(input.value) || 0 });
                });

                try {
                    const res = await fetch('api/farm_price_config_api.php?action=update_batch', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ configs: configs })
                    });
                    const json = await res.json();
                    const success = document.getElementById('psc-success');
                    if (json.success) {
                        success.style.display = 'block';
                        success.textContent = '✓ ' + json.message;
                        // Update preview boxes
                        configs.forEach(cfg => {
                            const preview = document.getElementById('preview-' + cfg.key);
                            if (preview) preview.textContent = '$' + cfg.value.toFixed(2);
                        });
                        setTimeout(() => { success.style.display = 'none'; }, 4000);
                    } else {
                        alert('Gagal menyimpan: ' + json.message);
                    }
                } catch (e) {
                    alert('Gagal menyimpan konfigurasi!');
                }
            }

            async function savePscAll() {
                const allInputs = document.querySelectorAll('#win-priceconfig input[type="number"]');
                const configs = [];
                allInputs.forEach(input => {
                    const key = input.id.replace('psc-', '');
                    configs.push({ key: key, value: parseFloat(input.value) || 0 });
                });

                try {
                    const res = await fetch('api/farm_price_config_api.php?action=update_batch', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ configs: configs })
                    });
                    const json = await res.json();
                    const success = document.getElementById('psc-success');
                    if (json.success) {
                        success.style.display = 'block';
                        success.textContent = '✓ Semua konfigurasi berhasil disimpan!';
                        // Update all preview boxes
                        configs.forEach(cfg => {
                            const preview = document.getElementById('preview-' + cfg.key);
                            if (preview) preview.textContent = '$' + cfg.value.toFixed(2);
                        });
                        setTimeout(() => { success.style.display = 'none'; }, 4000);
                    } else {
                        alert('Gagal menyimpan: ' + json.message);
                    }
                } catch (e) {
                    alert('Gagal menyimpan konfigurasi!');
                }
            }

            function switchPscTab(category) {
                document.querySelectorAll('.psc-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.psc-section').forEach(s => s.classList.remove('active'));
                event.target.classList.add('active');
                document.getElementById('psc-' + category).classList.add('active');
            }
        </script>
        <div class="window-statusbar">
            <span class="statusbar-section">Price & Salary Config</span>
            <span class="statusbar-section">Konfigurasi harga & gaji semua divisi</span>
        </div>
        <div class="resizer" onmousedown="startResize(event, 'win-priceconfig')"></div>
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
        <div class="start-menu-item" onclick="openWindow('mechanic');toggleStartMenu()">
            <svg viewBox="0 0 28 28">
                <rect x="2" y="6" width="24" height="18" rx="2" fill="#3a6ea5"/>
                <rect x="4" y="8" width="20" height="12" fill="#c8e0f5"/>
                <rect x="9" y="20" width="10" height="3" rx="1" fill="#245edc"/>
            </svg>
            <span>MechanicApp</span>
            <span class="arrow">▶</span>
        </div>
        <div class="start-menu-item" onclick="openWindow('farmer');toggleStartMenu()">
            <svg viewBox="0 0 28 28">
                <rect x="2" y="18" width="24" height="8" rx="1" fill="#228b22"/>
                <circle cx="8" cy="14" r="4" fill="#90ee90"/>
                <circle cx="14" cy="12" r="4" fill="#90ee90"/>
                <circle cx="20" cy="14" r="4" fill="#90ee90"/>
                <rect x="13" y="6" width="2" height="6" fill="#8b4513"/>
            </svg>
            <span>FarmerApp</span>
            <span class="arrow">▶</span>
        </div>
        <div class="start-menu-item" onclick="openWindow('cargo');toggleStartMenu()">
            <svg viewBox="0 0 28 28">
                <rect x="4" y="8" width="20" height="14" rx="2" fill="#8b5cf6"/>
                <rect x="6" y="10" width="16" height="10" fill="#c4b5fd"/>
                <rect x="8" y="22" width="4" height="4" fill="#333"/>
                <rect x="16" y="22" width="4" height="4" fill="#333"/>
            </svg>
            <span>CargoApp</span>
            <span class="arrow">▶</span>
        </div>
        <div class="start-menu-item" onclick="openWindow('restaurant');toggleStartMenu()">
            <svg viewBox="0 0 28 28">
                <ellipse cx="14" cy="22" rx="10" ry="3" fill="#8b4513"/>
                <path d="M6 18 Q6 10 14 10 Q22 10 22 18" fill="#cc2222"/>
                <rect x="12" y="6" width="4" height="4" rx="1" fill="#ffd700"/>
            </svg>
            <span>RestauranApp</span>
            <span class="arrow">▶</span>
        </div>
        <div class="start-menu-item" onclick="openWindow('employee');toggleStartMenu()">
            <svg viewBox="0 0 28 28">
                <circle cx="14" cy="10" r="6" fill="#f5c890"/>
                <ellipse cx="14" cy="24" rx="8" ry="4" fill="#3a6ea5"/>
            </svg>
            <span>Employee</span>
            <span class="arrow">▶</span>
        </div>
        <div class="start-menu-separator"></div>
        <div class="start-menu-item" onclick="openWindow('laporankerja');toggleStartMenu()">
            <svg viewBox="0 0 28 28">
                <rect x="4" y="4" width="20" height="22" rx="2" fill="#ffd700" stroke="#b07800"/>
                <rect x="8" y="8" width="12" height="2" fill="#8b6914"/>
                <rect x="8" y="12" width="10" height="2" fill="#8b6914"/>
                <rect x="8" y="16" width="12" height="2" fill="#8b6914"/>
                <rect x="8" y="20" width="8" height="2" fill="#8b6914"/>
            </svg>
            <span>Laporan Kerja</span>
            <span class="arrow">▶</span>
        </div>
        <div class="start-menu-item" onclick="openWindow('adminpanel');toggleStartMenu()">
            <svg viewBox="0 0 28 28">
                <rect x="2" y="6" width="24" height="18" rx="2" fill="#245edc"/>
                <rect x="4" y="8" width="20" height="12" fill="#c8e0f5"/>
                <rect x="6" y="10" width="4" height="4" fill="#ffd700"/>
                <rect x="12" y="10" width="4" height="4" fill="#4db84d"/>
                <rect x="18" y="10" width="4" height="4" fill="#cc2222"/>
            </svg>
            <span>Admin Panel</span>
            <span class="arrow">▶</span>
        </div>
        <div class="start-menu-item" onclick="openWindow('priceconfig');toggleStartMenu()">
            <svg viewBox="0 0 28 28">
                <rect x="2" y="4" width="24" height="20" rx="2" fill="#9333ea"/>
                <rect x="4" y="6" width="20" height="5" fill="#a855f7"/>
                <rect x="6" y="14" width="8" height="2" fill="#c084fc"/>
                <rect x="6" y="18" width="12" height="2" fill="#c084fc"/>
                <rect x="18" y="14" width="6" height="6" fill="#e9d5ff"/>
            </svg>
            <span>Price & Salary</span>
            <span class="arrow">▶</span>
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
            <span>Settings</span>
            <span class="arrow">▶</span>
        </div>
    </div>
    <div class="start-menu-right">
        <div class="start-menu-right-item" id="sm-user-info">
            <svg viewBox="0 0 28 28">
                <circle cx="14" cy="10" r="6" fill="#f5c890"/>
                <ellipse cx="14" cy="24" rx="8" ry="4" fill="#3a6ea5"/>
            </svg>
            <span id="sm-user-name">User</span>
        </div>
        <div class="start-menu-right-item">
            <svg viewBox="0 0 28 28">
                <rect x="2" y="2" width="24" height="24" rx="4" fill="#ddd" stroke="#888"/>
                <path d="M14 8 L14 20 M8 14 L20 14" stroke="#888" stroke-width="2"/>
            </svg>
            <span>Help and Support</span>
        </div>
        <div class="start-menu-separator"></div>
        <div class="start-menu-shutdown" onclick="logout();toggleStartMenu()">
            <svg viewBox="0 0 28 28">
                <circle cx="14" cy="14" r="10" fill="none" stroke="#cc2222" stroke-width="2"/>
                <rect x="12" y="6" width="4" height="8" fill="#cc2222"/>
            </svg>
            <span>Turn Off Computer</span>
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

        // Call loadCalcConfigFromDB when opening calculator
        if (type === 'calculator') {
            setTimeout(loadCalcConfigFromDB, 100);
        }
        // Call loadPriceConfig when opening mechanicmanager
        if (type === 'mechanicmanager') {
            // Use requestAnimationFrame to ensure window is visible first
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    if (typeof loadPriceConfig === 'function') {
                        loadPriceConfig();
                    }
                });
            });
        }
        // Call loadFmTugas when opening farmermanager
        if (type === 'farmermanager') {
            setTimeout(() => { if (typeof loadFmTugas === 'function') loadFmTugas(); }, 100);
        }
        // Call loadDeliveryList when opening deliverylist
        if (type === 'deliverylist') {
            setTimeout(() => { if (typeof loadDeliveryList === 'function') loadDeliveryList(); }, 100);
        }
        // Call loadHistoryDelivery when opening historydelivery
        if (type === 'historydelivery') {
            setTimeout(() => { if (typeof loadHistoryDelivery === 'function') loadHistoryDelivery(); }, 100);
        }
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
            windows: ['cargo', 'farmer', 'mechanic', 'restaurant', 'laporan', 'member', 'employee', 'laporankerja', 'keuangan', 'historydelivery', 'deliverylist', 'manager', 'mechanicmanager', 'farmermanager', 'cargomanager', 'priceconfig'],
            actions: ['create', 'edit', 'delete', 'view', 'manage_users', 'settings']
        },
        'employee': {
            windows: ['cargo', 'farmer', 'mechanic', 'restaurant', 'laporan', 'member', 'historydelivery', 'deliverylist'],
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

        // Show/hide other admin-only elements (like config button)
        document.querySelectorAll('.admin-only').forEach(el => {
            if (currentUser.role === 'admin') {
                el.style.display = '';
            } else {
                el.style.display = 'none';
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
        document.getElementById('sm-user-name').textContent = autoLoginData.name;

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