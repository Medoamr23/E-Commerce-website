<?php define('IN_ADMIN', true); require_once '../config.php'; require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf(); $id = (int)($_POST['id'] ?? 0); $a = $_POST['action'] ?? '';
    if ($id === (int)user()['id']) flash('You cannot do that to your own account.');
    elseif ($a === 'temp_ban') { $d = max(1, min(3650, (int)($_POST['days'] ?? 7))); $pdo->prepare('UPDATE users SET banned_until = DATE_ADD(NOW(), INTERVAL ? DAY) WHERE id=?')->execute([$d, $id]); flash("User banned for $d day(s)."); }
    elseif ($a === 'perm_ban') { $pdo->prepare('UPDATE users SET perm_banned=1 WHERE id=?')->execute([$id]); flash('User permanently banned.'); }
    elseif ($a === 'unban') { $pdo->prepare('UPDATE users SET perm_banned=0, banned_until=NULL WHERE id=?')->execute([$id]); flash('User unbanned.'); }
    elseif ($a === 'toggle_admin') { $pdo->prepare('UPDATE users SET is_admin = 1 - is_admin WHERE id=?')->execute([$id]); flash('Role updated.'); }
    elseif ($a === 'delete_comments') { $pdo->prepare('DELETE FROM comments WHERE user_id=?')->execute([$id]); flash('User comments deleted.'); }
    elseif ($a === 'delete') { $pdo->prepare('DELETE FROM users WHERE id=?')->execute([$id]); flash('User deleted.'); }
    header('Location: users.php'); exit;
}
$users = $pdo->query('SELECT u.*, (SELECT COUNT(*) FROM orders o WHERE o.user_id=u.id) AS orders FROM users u ORDER BY u.created_at DESC')->fetchAll();
$pageTitle = 'Admin · Users'; require '../header.php'; require '_nav.php';
function act($id, $a, $label, $cls = '', $extra = '') {
    return '<form method="post" class="inline"' . ($cls === 'danger' ? ' onsubmit="return confirm(\'Are you sure?\')"' : '') . '>' . csrf_field()
        . '<input type="hidden" name="id" value="' . $id . '"><input type="hidden" name="action" value="' . $a . '">' . $extra
        . '<button class="btn small ' . $cls . '">' . $label . '</button></form> ';
} ?>
<table class="table">
  <tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Orders</th><th>Commands</th></tr>
  <?php foreach ($users as $u): $b = ban_message($u); ?>
  <tr><td><?= e($u['name']) ?></td><td><?= e($u['email']) ?></td><td><?= e($u['phone']) ?></td>
    <td><?= $u['is_admin'] ? '<span class="badge">admin</span>' : 'customer' ?></td>
    <td><?= $b ? '<b style="color:#c00">' . ($u['perm_banned'] ? 'Perm banned' : 'Banned until ' . e($u['banned_until'])) . '</b>' : 'Active' ?></td>
    <td><?= $u['orders'] ?></td>
    <td><?php if ($u['id'] != user()['id']):
      echo act($u['id'], 'temp_ban', 'Temp ban', '', '<input type="number" name="days" value="7" min="1" style="width:55px"> days ');
      echo act($u['id'], 'perm_ban', 'Perm ban', 'danger');
      if ($b) echo act($u['id'], 'unban', 'Unban');
      echo act($u['id'], 'toggle_admin', $u['is_admin'] ? 'Remove admin' : 'Make admin');
      echo act($u['id'], 'delete_comments', 'Delete comments', 'danger');
      echo act($u['id'], 'delete', 'Delete user', 'danger');
    else: ?>(you)<?php endif; ?></td></tr>
  <?php endforeach; ?>
</table>
<?php require '../footer.php'; ?>
