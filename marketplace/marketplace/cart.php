<?php require_once 'config.php';
$_SESSION['cart'] = $_SESSION['cart'] ?? [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $id = (int)($_POST['id'] ?? 0); $qty = max(0, (int)($_POST['qty'] ?? 1));
    $st = $pdo->prepare('SELECT stock FROM products WHERE id=?'); $st->execute([$id]); $stock = (int)$st->fetchColumn();
    switch ($_POST['action'] ?? '') {
        case 'add':    $_SESSION['cart'][$id] = min($stock, ($_SESSION['cart'][$id] ?? 0) + max(1, $qty)); flash('Added to cart.'); break;
        case 'update': if ($qty > 0) $_SESSION['cart'][$id] = min($stock, $qty); else unset($_SESSION['cart'][$id]); break;
        case 'remove': unset($_SESSION['cart'][$id]); break;
    }
    $_SESSION['cart'] = array_filter($_SESSION['cart']);
    header('Location: cart.php'); exit;
}
$items = []; $total = 0;
if ($_SESSION['cart']) {
    $ids = array_keys($_SESSION['cart']);
    $st = $pdo->prepare('SELECT * FROM products WHERE id IN (' . implode(',', array_fill(0, count($ids), '?')) . ')');
    $st->execute($ids);
    foreach ($st->fetchAll() as $p) { $p['qty'] = $_SESSION['cart'][$p['id']]; $p['sub'] = $p['qty'] * $p['price']; $total += $p['sub']; $items[] = $p; }
}
$pageTitle = 'Cart'; require 'header.php'; ?>
<h1>Shopping cart</h1>
<?php if (!$items): ?><p class="empty">Your cart is empty. <a href="index.php">Start shopping</a></p><?php else: ?>
<table class="table">
<?php foreach ($items as $p): ?>
  <tr>
    <td><a href="product.php?id=<?= $p['id'] ?>"><?= e($p['title']) ?></a></td>
    <td><?= money($p['price']) ?></td>
    <td><form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?= $p['id'] ?>">
      <input type="number" name="qty" value="<?= $p['qty'] ?>" min="0" max="<?= $p['stock'] ?>" class="qty auto-submit"></form></td>
    <td><?= money($p['sub']) ?></td>
    <td><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="remove"><input type="hidden" name="id" value="<?= $p['id'] ?>"><button class="btn danger small">Remove</button></form></td>
  </tr>
<?php endforeach; ?>
</table>
<div class="total">Total: <b><?= money($total) ?></b> <a class="btn" href="checkout.php">Proceed to checkout</a></div>
<?php endif; ?>
<?php require 'footer.php'; ?>
