<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/helpers.php';
require_login('student');
$page_title='Attendance history';
$stmt=db()->prepare("SELECT s.subject,s.session_date,a.marked_at FROM attendance a JOIN attendance_sessions s ON s.id=a.session_id WHERE a.student_id=? ORDER BY s.session_date DESC,a.marked_at DESC");
$stmt->execute([current_user()['id']]);$rows=$stmt->fetchAll();
?>
<?php include __DIR__ . '/partials/header.php'; ?>
<div class="container reveal"><section class="panel"><div class="panel-head"><div><h3>All attendance records</h3><span>Your date-wise check-in history</span></div></div><div class="table-wrap">
<?php if($rows): ?><table><thead><tr><th>Subject</th><th>Date</th><th>Marked at</th><th>Status</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><strong><?=e($r['subject'])?></strong></td><td><?=e(date('d M Y',strtotime($r['session_date'])))?></td><td><?=e(date('h:i A',strtotime($r['marked_at'])))?></td><td><span class="badge badge-success">Present</span></td></tr><?php endforeach;?></tbody></table>
<?php else:?><div class="empty"><div class="empty-icon">▤</div><strong>No records found</strong></div><?php endif;?>
</div></section></div>
<?php include __DIR__ . '/partials/footer.php'; ?>
