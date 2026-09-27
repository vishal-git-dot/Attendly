<?php
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/auth.php';
$user = current_user();
$flash = get_flash();
$page_title = $page_title ?? 'Attendly';
?>
<!doctype html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <title><?= e($page_title) ?> · Attendly</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/app.css">
    <script>document.documentElement.dataset.theme = localStorage.getItem('attendly-theme') || 'light';</script>
</head>
<body>
<div class="app-shell">
<?php if ($user): ?>
    <aside class="sidebar" id="sidebar">
        <a class="brand" href="<?= $user['role'] === 'teacher' ? 'teacher_dashboard.php' : 'student_dashboard.php' ?>">
            <span class="brand-mark">A</span>
            <span><strong>Attendly</strong><small>Smart attendance</small></span>
        </a>
        <nav class="nav">
            <?php if ($user['role'] === 'teacher'): ?>
                <a class="<?= basename($_SERVER['PHP_SELF']) === 'teacher_dashboard.php' ? 'active' : '' ?>" href="teacher_dashboard.php"><span>⌂</span> Overview</a>
                <a class="<?= basename($_SERVER['PHP_SELF']) === 'create_session.php' ? 'active' : '' ?>" href="create_session.php"><span>＋</span> New session</a>
                <a class="<?= basename($_SERVER['PHP_SELF']) === 'attendance_qr.php' ? 'active' : '' ?>" href="qr_sessions.php"><span>▦</span> QR sessions</a>
                <a href="reports.php"><span>▤</span> Reports</a>
            <?php else: ?>
                <a class="<?= basename($_SERVER['PHP_SELF']) === 'student_dashboard.php' ? 'active' : '' ?>" href="student_dashboard.php"><span>⌂</span> My attendance</a>
                <a class="<?= basename($_SERVER['PHP_SELF']) === 'scan.php' ? 'active' : '' ?>" href="scan.php"><span>⌁</span> Scan QR</a>
                <a class="<?= basename($_SERVER['PHP_SELF']) === 'attendance_history.php' ? 'active' : '' ?>" href="attendance_history.php"><span>▤</span> History</a>
            <?php endif; ?>
        </nav>
        <div class="sidebar-bottom">
            <div class="theme-row">
                <button class="theme-toggle" id="themeToggle" type="button" aria-label="Switch theme">
                    <span id="themeIcon">☼</span>
                    <span id="themeLabel">Light Mode</span>
                </button>
            </div>
            <a class="profile-mini" href="logout.php">
                <span class="avatar"><?= e(strtoupper(substr($user['name'], 0, 1))) ?></span>
                <span class="profile-copy"><strong><?= e($user['name']) ?></strong><small><?= e(ucfirst($user['role'])) ?></small></span>
                <span class="logout-icon">↪</span>
            </a>
        </div>
    </aside>
    <main class="main-content">
        <header class="topbar">
            <button class="mobile-menu" id="mobileMenu" type="button" aria-label="Open navigation">☰</button>
            <div>
                <div class="eyebrow">ATTENDLY · <?= e(ucfirst($user['role'])) ?></div>
                <h1><?= e($page_title) ?></h1>
            </div>
            <div class="top-actions">
                <span class="status-pill"><i></i> System online</span>
            </div>
        </header>
<?php endif; ?>

<?php if ($flash): ?>
<div class="toast toast-<?= e($flash['type']) ?>" role="status">
    <span><?= $flash['type'] === 'success' ? '✓' : '!' ?></span>
    <?= e($flash['message']) ?>
    <button onclick="this.parentElement.remove()" aria-label="Close">×</button>
</div>
<?php endif; ?>
