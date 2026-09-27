<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/helpers.php';
require_login('teacher');
$page_title = 'Teacher dashboard';
$teacher = current_user();

$totalStudents = (int)db()->query("SELECT COUNT(*) FROM students")->fetchColumn();
$stmt = db()->prepare("SELECT COUNT(*) FROM attendance_sessions WHERE teacher_id = ?");
$stmt->execute([$teacher['id']]); $totalSessions = (int)$stmt->fetchColumn();

$stmt = db()->prepare("SELECT COUNT(*) FROM attendance a JOIN attendance_sessions s ON s.id=a.session_id WHERE s.teacher_id=?");
$stmt->execute([$teacher['id']]); $totalAttendance = (int)$stmt->fetchColumn();

$stmt = db()->prepare("SELECT COUNT(*) FROM attendance_sessions WHERE teacher_id=? AND session_date=CURDATE()");
$stmt->execute([$teacher['id']]); $todaySessions = (int)$stmt->fetchColumn();

$stmt = db()->prepare("SELECT s.*, (SELECT COUNT(*) FROM attendance a WHERE a.session_id=s.id) AS present_count
                       FROM attendance_sessions s WHERE s.teacher_id=? ORDER BY s.id DESC LIMIT 8");
$stmt->execute([$teacher['id']]); $sessions = $stmt->fetchAll();
?>
<?php include __DIR__ . '/partials/header.php'; ?>
<div class="container reveal">
<section class="hero">
    <div class="hero-card primary">
        <h2>Good morning, <?= e($teacher['name']) ?>.</h2>
        <p>Create a session, put the QR on screen, and let your class check in in seconds.</p>
        <a class="btn btn-primary" href="create_session.php">＋ Create attendance session</a>
    </div>
    <div class="hero-card">
        <div class="eyebrow">TODAY</div>
        <div class="kpi" style="margin-top:13px"><strong><?= $todaySessions ?></strong><small>sessions created</small></div>
        <div class="progress" style="margin-top:18px"><span style="width:<?= min(100, $todaySessions * 20) ?>%"></span></div>
        <p style="font-size:11px;margin-top:10px">Your attendance workspace is ready.</p>
    </div>
</section>

<section class="grid stats-grid">
    <div class="stat-card"><div class="stat-top"><span class="stat-label">Students</span><span class="stat-icon">♙</span></div><div class="stat-value"><?= $totalStudents ?></div><div class="stat-meta">Registered learners</div></div>
    <div class="stat-card"><div class="stat-top"><span class="stat-label">Sessions</span><span class="stat-icon">▦</span></div><div class="stat-value"><?= $totalSessions ?></div><div class="stat-meta">Created by you</div></div>
    <div class="stat-card"><div class="stat-top"><span class="stat-label">Attendance</span><span class="stat-icon">✓</span></div><div class="stat-value"><?= $totalAttendance ?></div><div class="stat-meta">Total check-ins</div></div>
    <div class="stat-card"><div class="stat-top"><span class="stat-label">Avg. rate</span><span class="stat-icon">%</span></div>
    <?php $avg = $totalStudents && $totalSessions ? round(($totalAttendance / ($totalStudents * $totalSessions)) * 100) : 0; ?>
    <div class="stat-value"><?= $avg ?>%</div><div class="stat-meta">Across your sessions</div></div>
</section>

<section class="panel" id="reports">
    <div class="panel-head"><h3>Recent sessions</h3><a class="btn btn-ghost" href="create_session.php">New session →</a></div>
    <div class="table-wrap">
    <?php if ($sessions): ?>
    <table><thead><tr><th>Subject</th><th>Date</th><th>Expires</th><th>Present</th><th>Status</th><th></th></tr></thead>
    <tbody><?php foreach ($sessions as $s): $active = strtotime($s['expires_at']) > time(); ?>
    <tr><td><strong><?= e($s['subject']) ?></strong></td><td><?= e(date('d M Y', strtotime($s['session_date']))) ?></td><td><?= e(date('h:i A', strtotime($s['expires_at']))) ?></td><td><?= (int)$s['present_count'] ?></td><td><span class="badge <?= $active ? 'badge-success':'badge-neutral' ?>"><?= $active?'Active':'Expired' ?></span></td><td><a class="btn btn-secondary" href="attendance_qr.php?id=<?= (int)$s['id'] ?>">View QR</a></td></tr>
    <?php endforeach; ?></tbody></table>
    <?php else: ?><div class="empty"><div class="empty-icon">▦</div><strong>No attendance sessions yet</strong><br>Create your first session to get a QR code.</div><?php endif; ?>
    </div>
</section>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
