<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/helpers.php';
require_login('student');
$page_title='My attendance';
$student=current_user();

$stmt=db()->prepare("SELECT COUNT(*) FROM attendance WHERE student_id=?");$stmt->execute([$student['id']]);$present=(int)$stmt->fetchColumn();
$totalSessions=(int)db()->query("SELECT COUNT(*) FROM attendance_sessions")->fetchColumn();
$percentage=$totalSessions?round(($present/$totalSessions)*100):0;

$stmt=db()->prepare("SELECT s.subject,s.session_date,a.marked_at FROM attendance a JOIN attendance_sessions s ON s.id=a.session_id WHERE a.student_id=? ORDER BY a.marked_at DESC LIMIT 8");
$stmt->execute([$student['id']]);$history=$stmt->fetchAll();
?>
<?php include __DIR__ . '/partials/header.php'; ?>
<div class="container reveal">
<section class="hero">
<div class="hero-card primary"><h2>Your attendance at a glance.</h2><p>Keep track of every class check-in and stay aware of your attendance rate.</p><a class="btn btn-primary" href="scan.php">⌁ Scan class QR</a></div>
<div class="hero-card"><div class="eyebrow">ATTENDANCE RATE</div><div class="kpi" style="margin-top:13px"><strong><?= $percentage ?>%</strong><small><?= $present ?> of <?= $totalSessions ?> sessions</small></div><div class="progress" style="margin-top:18px"><span style="width:<?= min(100,$percentage) ?>%"></span></div></div>
</section>
<section class="grid stats-grid" style="grid-template-columns:repeat(3,1fr)">
<div class="stat-card"><div class="stat-label">Present</div><div class="stat-value"><?= $present ?></div><div class="stat-meta">Recorded check-ins</div></div>
<div class="stat-card"><div class="stat-label">Sessions</div><div class="stat-value"><?= $totalSessions ?></div><div class="stat-meta">Available attendance sessions</div></div>
<div class="stat-card"><div class="stat-label">Rate</div><div class="stat-value"><?= $percentage ?>%</div><div class="stat-meta">Overall attendance</div></div>
</section>
<section class="panel">
<div class="panel-head"><h3>Recent attendance</h3><a class="btn btn-ghost" href="attendance_history.php">View history →</a></div>
<div class="table-wrap">
<?php if($history): ?><table><thead><tr><th>Subject</th><th>Date</th><th>Marked at</th><th>Status</th></tr></thead><tbody>
<?php foreach($history as $row): ?><tr><td><strong><?=e($row['subject'])?></strong></td><td><?=e(date('d M Y',strtotime($row['session_date'])))?></td><td><?=e(date('h:i A',strtotime($row['marked_at'])))?></td><td><span class="badge badge-success">Present</span></td></tr><?php endforeach;?>
</tbody></table><?php else:?><div class="empty"><div class="empty-icon">✓</div><strong>No attendance yet</strong><br>Scan a class QR to record your first check-in.</div><?php endif;?>
</div></section>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
