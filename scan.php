<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/helpers.php';
require_login('student');
$page_title = 'Scan attendance';

$token = trim($_GET['token'] ?? '');
$message = null; $type = null; $session = null;

if ($token !== '') {
    $stmt = db()->prepare("SELECT * FROM attendance_sessions WHERE session_token=? LIMIT 1");
    $stmt->execute([$token]); $session=$stmt->fetch();

    if (!$session) { $message='This attendance QR code is not valid.'; $type='error'; }
    elseif (strtotime($session['expires_at']) <= time()) { $message='This attendance session has expired.'; $type='error'; }
    else {
        $stmt=db()->prepare("SELECT id FROM attendance WHERE session_id=? AND student_id=? LIMIT 1");
        $stmt->execute([$session['id'], current_user()['id']]);
        if ($stmt->fetch()) { $message='Your attendance is already recorded for this session.'; $type='success'; }
        else {
            $stmt=db()->prepare("INSERT INTO attendance (session_id,student_id,marked_at) VALUES (?,?,NOW())");
            try { $stmt->execute([$session['id'],current_user()['id']]); $message='Attendance marked successfully!'; $type='success'; }
            catch (PDOException $e) { $message='Attendance could not be recorded. Please try again.'; $type='error'; }
        }
    }
}
?>
<?php include __DIR__ . '/partials/header.php'; ?>
<div class="container reveal">
<section class="panel scan-hero">
<div class="panel-body" style="padding:38px 25px">
<div class="eyebrow">QUICK CHECK-IN</div>
<h2 style="font:800 27px Manrope;margin:8px 0 6px">Scan your class QR</h2>
<p style="color:var(--muted);font-size:12px;margin:0">Scan the QR code shown by your teacher. If you open a QR link directly, your attendance will be verified automatically.</p>
<?php if ($message): ?>
<div class="alert <?= $type==='success'?'': 'alert-error' ?>" style="margin-top:22px;background:<?= $type==='success'?'var(--success-soft)':'var(--error-soft)' ?>;color:<?= $type==='success'?'var(--success)':'var(--error)' ?>">
<strong><?= $type==='success'?'✓':'!' ?></strong> <?= e($message) ?><?php if ($session): ?><?= ' · '.e($session['subject']) ?><?php endif; ?>
</div>
<?php else: ?>
<div class="scan-camera"><div style="font-size:40px">▦</div><strong>Ready to scan</strong><p style="font-size:11px;color:var(--muted)">Use your phone camera to scan the QR shown by your teacher. The QR opens this page with a session token; Attendly then verifies the token and records your attendance.</p><div class="alert" style="margin-top:15px;text-align:left;background:var(--accent-soft);color:var(--accent)"><strong>Important:</strong> If the QR opens a <b>localhost</b> address, the phone cannot reach your PC. Use your PC LAN IP such as <b>http://192.168.1.10/Attendly_QR_Attendance_System_fixed</b> in <code>config/helpers.php</code> as <code>APP_URL</code>.</div></div>
<?php endif; ?>
<a class="btn btn-primary" href="student_dashboard.php">View my attendance →</a>
</div></section>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
