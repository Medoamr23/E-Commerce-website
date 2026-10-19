<?php require_once 'config.php'; require_login();
$st = $pdo->prepare('SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC'); $st->execute([user()['id']]);
$orders = $st->fetchAll();
$it = $pdo->prepare('SELECT * FROM order_items WHERE order_id=?');
$pageTitle = 'My orders'; require 'header.php'; ?>
<h1>My orders</h1>
<?php if (!$orders): ?><p class="empty">No orders yet.</p><?php endif; ?>
<?php foreach ($orders as $o): $it->execute([$o['id']]); ?>
  <div class="order">
    <div class="orderhead"><b>Order #<?= $o['id'] ?></b> <span><?= e($o['created_at']) ?></span> <span class="badge"><?= e($o['status']) ?></span> <b><?= money($o['total']) ?></b></div>
    <ul><?php foreach ($it->fetchAll() as $i): ?><li><?= e($i['title']) ?> × <?= $i['quantity'] ?> — <?= money($i['price'] * $i['quantity']) ?></li><?php endforeach; ?></ul>
  </div>
<?php endforeach; ?>
<?php require 'footer.php'; ?>
