<?php define('IN_ADMIN', true); require_once '../config.php'; require_admin();
$s = fn($q) => $pdo->query($q)->fetchColumn();
$stats = [
  'Revenue' => money($s("SELECT COALESCE(SUM(total),0) FROM orders WHERE status <> 'cancelled'")),
  'Orders' => $s('SELECT COUNT(*) FROM orders'),
  'Pending orders' => $s("SELECT COUNT(*) FROM orders WHERE status='pending'"),
  'Products' => $s('SELECT COUNT(*) FROM products'),
  'Customers' => $s('SELECT COUNT(*) FROM users WHERE is_admin=0'),
  'Comments' => $s('SELECT COUNT(*) FROM comments'),
];
$recent = $pdo->query('SELECT o.*, u.name FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.created_at DESC LIMIT 5')->fetchAll();
$low = $pdo->query('SELECT * FROM products WHERE stock <= 3 ORDER BY stock')->fetchAll();
$pageTitle = 'Admin'; require '../header.php'; require '_nav.php'; ?>
<div class="stats"><?php foreach ($stats as $k => $v): ?><div class="stat"><?= $k ?><b><?= $v ?></b></div><?php endforeach; ?></div>
<h2>Recent orders</h2>
<table class="table"><?php foreach ($recent as $o): ?><tr><td>#<?= $o['id'] ?></td><td><?= e($o['name']) ?></td><td><?= money($o['total']) ?></td><td><span class="badge"><?= e($o['status']) ?></span></td><td><?= e($o['created_at']) ?></td></tr><?php endforeach; ?></table>
<h2>Low stock</h2>
<table class="table"><?php foreach ($low as $p): ?><tr><td><?= e($p['title']) ?></td><td><?= $p['stock'] ?> left</td><td><a href="products.php?edit=<?= $p['id'] ?>">Edit</a></td></tr><?php endforeach; ?></table>
<?php require '../footer.php'; ?>
