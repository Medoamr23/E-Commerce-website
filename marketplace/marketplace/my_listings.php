<?php require_once 'config.php'; require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    check_csrf();
    $st = $pdo->prepare('SELECT image FROM products WHERE id=? AND user_id=?'); $st->execute([(int)$_POST['id'], user()['id']]);
    if ($row = $st->fetch()) {
        $pdo->prepare('DELETE FROM products WHERE id=? AND user_id=?')->execute([(int)$_POST['id'], user()['id']]);
        if ($row['image'] && is_file(__DIR__ . '/uploads/' . $row['image'])) unlink(__DIR__ . '/uploads/' . $row['image']);
        flash('Listing deleted.');
    }
    header('Location: my_listings.php'); exit;
}
$st = $pdo->prepare('SELECT * FROM products WHERE user_id=? ORDER BY created_at DESC'); $st->execute([user()['id']]);
$items = $st->fetchAll();
$sales = $pdo->prepare('SELECT oi.*, o.created_at, o.address, o.phone, u.name AS buyer FROM order_items oi
  JOIN orders o ON o.id=oi.order_id JOIN users u ON u.id=o.user_id
  JOIN products p ON p.id=oi.product_id WHERE p.user_id=? ORDER BY o.created_at DESC');
$sales->execute([user()['id']]); $sales = $sales->fetchAll();
$pageTitle = 'My listings'; require 'header.php'; ?>
<h1>My listings</h1>
<?php if (!$items): ?><p class="empty">You haven't listed anything yet. <a href="sell.php">Sell an item</a></p><?php endif; ?>
<table class="table">
<?php foreach ($items as $p): ?>
  <tr>
    <td><a href="product.php?id=<?= $p['id'] ?>"><?= e($p['title']) ?></a></td>
    <td><?= money($p['price']) ?></td>
    <td><?= $p['stock'] ?> in stock</td>
    <td><form method="post" onsubmit="return confirm('Delete this listing?')"><?= csrf_field() ?>
      <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $p['id'] ?>">
      <button class="btn danger small">Delete</button></form></td>
  </tr>
<?php endforeach; ?>
</table>
<h2>My sales</h2>
<?php if (!$sales): ?><p class="empty">No sales yet.</p><?php endif; ?>
<table class="table">
<?php foreach ($sales as $s): ?>
  <tr><td><?= e($s['title']) ?> × <?= $s['quantity'] ?></td><td><?= money($s['price'] * $s['quantity']) ?></td>
  <td><?= e($s['buyer']) ?> · <?= e($s['phone']) ?><br><small><?= e($s['address']) ?></small></td><td><?= e($s['created_at']) ?></td></tr>
<?php endforeach; ?>
</table>
<?php require 'footer.php'; ?>
