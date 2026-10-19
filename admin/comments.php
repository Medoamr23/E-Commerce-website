<?php define('IN_ADMIN', true); require_once '../config.php'; require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $pdo->prepare('DELETE FROM comments WHERE id=?')->execute([(int)$_POST['id']]);
    flash('Comment deleted.'); header('Location: comments.php'); exit;
}
$comments = $pdo->query('SELECT c.*, u.name, p.title FROM comments c JOIN users u ON u.id=c.user_id JOIN products p ON p.id=c.product_id ORDER BY c.created_at DESC')->fetchAll();
$pageTitle = 'Admin · Comments'; require '../header.php'; require '_nav.php'; ?>
<table class="table">
  <tr><th>User</th><th>Product</th><th>Comment</th><th>Date</th><th></th></tr>
  <?php foreach ($comments as $c): ?>
  <tr><td><?= e($c['name']) ?></td><td><a href="../product.php?id=<?= $c['product_id'] ?>#comments"><?= e($c['title']) ?></a></td>
    <td><?= nl2br(e($c['body'])) ?></td><td><?= e($c['created_at']) ?></td>
    <td><form method="post" onsubmit="return confirm('Delete comment?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $c['id'] ?>"><button class="btn danger small">Delete</button></form></td></tr>
  <?php endforeach; ?>
</table>
<?php require '../footer.php'; ?>
