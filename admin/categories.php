<?php define('IN_ADMIN', true); require_once '../config.php'; require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (($_POST['action'] ?? '') === 'add' && trim($_POST['name'] ?? '') !== '') { $pdo->prepare('INSERT INTO categories (name) VALUES (?)')->execute([trim($_POST['name'])]); flash('Category added.'); }
    if (($_POST['action'] ?? '') === 'delete') {
        try { $pdo->prepare('DELETE FROM categories WHERE id=?')->execute([(int)$_POST['id']]); flash('Category deleted.'); }
        catch (PDOException $ex) { flash('Cannot delete: category still has products.'); }
    }
    header('Location: categories.php'); exit;
}
$list = $pdo->query('SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id=c.id) AS n FROM categories c ORDER BY name')->fetchAll();
$pageTitle = 'Admin · Categories'; require '../header.php'; require '_nav.php'; ?>
<form method="post" class="addcart" style="margin-bottom:16px"><?= csrf_field() ?><input type="hidden" name="action" value="add">
  <input name="name" placeholder="New category name" required style="width:260px"><button class="btn">Add</button></form>
<table class="table"><?php foreach ($list as $c): ?><tr><td><?= e($c['name']) ?></td><td><?= $c['n'] ?> products</td>
  <td><form method="post" onsubmit="return confirm('Delete category?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $c['id'] ?>"><button class="btn danger small">Delete</button></form></td></tr><?php endforeach; ?></table>
<?php require '../footer.php'; ?>
