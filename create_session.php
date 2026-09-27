<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/helpers.php';
require_login('teacher');
$page_title = 'Create attendance session';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject'] ?? '');
    $minutes = max(1, min(180, (int)($_POST['duration'] ?? 10)));
    if ($subject === '') {
        flash('error', 'Please enter a subject.');
    } else {
        $token = bin2hex(random_bytes(24));
        $expires = date('Y-m-d H:i:s', time() + $minutes * 60);
        $stmt = db()->prepare("INSERT INTO attendance_sessions (teacher_id,subject,session_token,session_date,expires_at) VALUES (?,?,?,?,?)");
        $stmt->execute([current_user()['id'], $subject, $token, date('Y-m-d'), $expires]);
        header('Location: attendance_qr.php?id=' . db()->lastInsertId());
        exit;
    }
}
?>
<?php include __DIR__ . '/partials/header.php'; ?>
<div class="container reveal">
<div class="panel form-card">
<div class="panel-head"><div><h3>New attendance session</h3><span>Generate a temporary QR code for one class.</span></div></div>
<div class="panel-body">
<form method="post" data-validate>
<div class="form-grid">
<div class="field full"><label for="subject">Subject / class</label><input id="subject" name="subject" required placeholder="e.g. Database Management Systems"></div>
<div class="field"><label for="duration">QR validity</label><select id="duration" name="duration"><option value="5">5 minutes</option><option value="10" selected>10 minutes</option><option value="15">15 minutes</option><option value="30">30 minutes</option><option value="60">1 hour</option></select><small>After expiry, new check-ins are rejected.</small></div>
</div>
<div class="form-actions"><a class="btn btn-secondary" href="teacher_dashboard.php">Cancel</a><button class="btn btn-primary" type="submit">Generate QR →</button></div>
</form>
</div></div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
