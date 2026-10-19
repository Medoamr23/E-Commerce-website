<?php require_once 'config.php'; require_login();
if (empty($_SESSION['cart'])) { header('Location: cart.php'); exit; }
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $address = trim($_POST['address'] ?? ''); $phone = trim($_POST['phone'] ?? '');
    if ($address === '' || $phone === '') $errors[] = 'Address and phone are required.';
    if (!$errors) {
        try {
            $pdo->beginTransaction();
            $total = 0; $lines = [];
            $st = $pdo->prepare('SELECT * FROM products WHERE id=? FOR UPDATE');
            foreach ($_SESSION['cart'] as $id => $qty) {
                $st->execute([$id]); $p = $st->fetch();
                if (!$p) throw new Exception('A product in your cart no longer exists.');
                if ($p['stock'] < $qty) throw new Exception('Not enough stock for: ' . $p['title']);
                $total += $p['price'] * $qty; $lines[] = [$p, $qty];
            }
            $pdo->prepare('INSERT INTO orders (user_id,total,address,phone) VALUES (?,?,?,?)')->execute([user()['id'], $total, $address, $phone]);
            $orderId = $pdo->lastInsertId();
            $ins = $pdo->prepare('INSERT INTO order_items (order_id,product_id,title,price,quantity) VALUES (?,?,?,?,?)');
            $upd = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id=?');
            foreach ($lines as [$p, $qty]) { $ins->execute([$orderId, $p['id'], $p['title'], $p['price'], $qty]); $upd->execute([$qty, $p['id']]); }
            $pdo->commit();
            $_SESSION['cart'] = [];
            flash('Order #' . $orderId . ' placed! Pay cash on delivery.'); header('Location: orders.php'); exit;
        } catch (Exception $ex) { $pdo->rollBack(); $errors[] = $ex->getMessage(); }
    }
}
$pageTitle = 'Checkout'; require 'header.php'; ?>
<form method="post" class="form">
  <h1>Checkout</h1>
  <?php foreach ($errors as $er): ?><div class="error"><?= e($er) ?></div><?php endforeach; ?>
  <?= csrf_field() ?>
  <label>Delivery address <textarea name="address" rows="3" required><?= e($_POST['address'] ?? '') ?></textarea></label>
  <label>Phone <input name="phone" required value="<?= e($_POST['phone'] ?? '') ?>"></label>
  <p class="note">Payment: cash on delivery.</p>
  <button class="btn">Place order</button>
</form>
<?php require 'footer.php'; ?>
