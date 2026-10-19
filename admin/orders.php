<?php define('IN_ADMIN', true); require_once '../config.php'; require_admin();
$statuses = ['pending','processing','shipped','delivered','cancelled'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (in_array($_POST['status'] ?? '', $statuses, true)) {
        $oid = (int)$_POST['id']; $new = $_POST['status'];
        $pdo->beginTransaction();
        $o = $pdo->prepare('SELECT status FROM orders WHERE id=? FOR UPDATE'); $o->execute([$oid]); $old = $o->fetchColumn();
        if ($old !== false && $old !== $new) {
            if ($new === 'cancelled') $pdo->prepare('UPDATE products p JOIN order_items i ON i.product_id=p.id SET p.stock=p.stock+i.quantity WHERE i.order_id=?')->execute([$oid]);
            elseif ($old === 'cancelled') $pdo->prepare('UPDATE products p JOIN order_items i ON i.product_id=p.id SET p.stock=GREATEST(p.stock-i.quantity,0) WHERE i.order_id=?')->execute([$oid]);
            $pdo->prepare('UPDATE orders SET status=? WHERE id=?')->execute([$new, $oid]);
        }
        $pdo->commit();
        flash('Order #' . (int)$_POST['id'] . ' updated.');
    }
    header('Location: orders.php'); exit;
}
$orders = $pdo->query('SELECT o.*, u.name, u.email FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.created_at DESC')->fetchAll();
$it = $pdo->prepare('SELECT * FROM order_items WHERE order_id=?');
$pageTitle = 'Admin · Orders'; require '../header.php'; require '_nav.php'; ?>
<?php if (!$orders): ?><p class="empty">No orders yet.</p><?php endif; ?>
<?php foreach ($orders as $o): $it->execute([$o['id']]); ?>
<div class="order">
  <div class="orderhead"><b>#<?= $o['id'] ?></b> <span><?= e($o['name']) ?> (<?= e($o['email']) ?>)</span> <span><?= e($o['phone']) ?></span> <b><?= money($o['total']) ?></b> <span><?= e($o['created_at']) ?></span>
    <form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $o['id'] ?>">
      <select name="status" class="auto-submit"><?php foreach ($statuses as $s): ?><option <?= $o['status'] === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select></form>
  </div>
  <small><?= e($o['address']) ?></small>
  <ul><?php foreach ($it->fetchAll() as $i): ?><li><?= e($i['title']) ?> × <?= $i['quantity'] ?> — <?= money($i['price'] * $i['quantity']) ?></li><?php endforeach; ?></ul>
</div>
<?php endforeach; ?>
<?php require '../footer.php'; ?>
