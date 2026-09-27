<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/helpers.php';
require_login('teacher');
$page_title = 'Live attendance QR';
$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare("SELECT * FROM attendance_sessions WHERE id=? AND teacher_id=?");
$stmt->execute([$id,current_user()['id']]);
$session = $stmt->fetch();
if (!$session) { http_response_code(404); exit('Session not found.'); }

$stmt = db()->prepare("SELECT COUNT(*) FROM attendance WHERE session_id=?"); $stmt->execute([$id]); $present=(int)$stmt->fetchColumn();
$qrUrl = base_url() . '/scan.php?token=' . urlencode($session['session_token']);
$expired = strtotime($session['expires_at']) <= time();
?>
<?php include __DIR__ . '/partials/header.php'; ?>
<div class="container reveal">
<div class="qr-layout">
<section class="panel qr-card">
<div class="panel-head"><div><h3>Scan to mark attendance</h3><span><?= e($session['subject']) ?></span></div></div>
<div class="panel-body">
<div class="qr-box" id="qrcode"></div>
<div class="token"><?= e($session['session_token']) ?></div>
<div style="margin-top:12px"><span class="badge <?= $expired?'badge-neutral':'badge-success' ?>" id="statusBadge"><?= $expired?'Expired':'Live · Expires '.date('h:i A',strtotime($session['expires_at'])) ?></span></div>
<p style="font-size:11px;color:var(--muted);margin:15px 0 0">Students scan this QR with their phone camera. Each student can check in only once.</p>
</div>
</section>
<section class="panel">
<div class="panel-head"><h3>Session overview</h3><span><?= e(date('d M Y',strtotime($session['session_date']))) ?></span></div>
<div class="panel-body">
<div class="stats-grid" style="grid-template-columns:1fr 1fr;margin:0 0 18px">
<div class="stat-card"><div class="stat-label">Present</div><div class="stat-value" id="presentCount"><?= $present ?></div><div class="stat-meta">Students checked in</div></div>
<div class="stat-card"><div class="stat-label">Valid until</div><div class="stat-value" style="font-size:18px"><?= e(date('h:i A',strtotime($session['expires_at']))) ?></div><div class="stat-meta">Temporary session</div></div>
</div>
<div class="alert" style="background:var(--accent-soft);color:var(--accent)">Tip: Keep this page open on the classroom projector while students scan.</div>
<a class="btn btn-secondary" href="teacher_dashboard.php">← Back to dashboard</a>
</div></section>
</div></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
new QRCode(document.getElementById("qrcode"), {text: <?= json_encode($qrUrl) ?>, width: 232, height: 232, correctLevel: QRCode.CorrectLevel.M});
const expiresAt = new Date(<?= json_encode(date('c',strtotime($session['expires_at']))) ?>).getTime();
const badge = document.getElementById('statusBadge');
setInterval(() => { if (Date.now() >= expiresAt) { badge.textContent='Expired'; badge.className='badge badge-neutral'; } }, 1000);
</script>
<?php include __DIR__ . '/partials/footer.php'; ?>
