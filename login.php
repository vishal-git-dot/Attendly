<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/helpers.php';

if (!empty($_SESSION['user'])) {
    header('Location: ' . ($_SESSION['user']['role'] === 'teacher' ? 'teacher_dashboard.php' : 'student_dashboard.php'));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($email && $password) {
        $stmt = db()->prepare('SELECT id,name,email,password,role FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            login_user($user);
            header('Location: ' . ($user['role'] === 'teacher' ? 'teacher_dashboard.php' : 'student_dashboard.php'));
            exit;
        }
    }
    $error = 'Invalid email or password. Please try again.';
}
?>
<!doctype html>
<html lang="en" data-theme="light">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Sign in · Attendly</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/app.css"><script>document.documentElement.dataset.theme=localStorage.getItem('attendly-theme')||'light';</script>
</head>
<body>
<div class="auth-page">
<div class="auth-wrap reveal">
<section class="auth-brand">
    <span class="brand-mark">A</span>
    <h1>Attendance, without the paperwork.</h1>
    <p>Attendly turns classroom attendance into a fast QR-powered workflow for teachers and students.</p>
    <ul class="feature-list">
        <li>✓ Temporary QR sessions</li>
        <li>✓ Duplicate-safe attendance</li>
        <li>✓ Live percentage tracking</li>
        <li>✓ Date-wise reporting</li>
    </ul>
</section>
<section class="auth-form">
    <div style="display:flex;justify-content:flex-end;margin-bottom:22px">
        <button class="theme-toggle" id="themeToggle" type="button" style="width:auto"><span id="themeIcon">☼</span><span id="themeLabel">Light Mode</span></button>
    </div>
    <h2>Welcome back</h2><p>Sign in to continue to your attendance workspace.</p>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <form method="post" data-validate>
        <div class="field" style="margin-bottom:14px"><label for="email">Email</label><input id="email" name="email" type="email" required placeholder="you@example.com"></div>
        <div class="field"><label for="password">Password</label><input id="password" name="password" type="password" required placeholder="••••••••"></div>
        <button class="btn btn-primary" type="submit" style="width:100%;margin-top:20px">Sign in <span>→</span></button>
    </form>
    <div class="demo-box"><strong>Demo accounts</strong><br>Teacher: <b>teacher@attendly.test</b> / password<br>Student: <b>student@attendly.test</b> / password</div>
</section>
</div>
</div>
<script src="assets/js/app.js"></script>
</body></html>
