<?php require_once 'config.php';
$st = $pdo->prepare('SELECT p.*, c.name AS cat, u.name AS seller, u.email AS seller_email FROM products p
  JOIN categories c ON c.id=p.category_id JOIN users u ON u.id=p.user_id WHERE p.id=?');
$st->execute([(int)($_GET['id'] ?? 0)]); $p = $st->fetch();
if (!$p) { http_response_code(404); $pageTitle='Not found'; require 'header.php'; echo '<p>Product not found.</p>'; require 'footer.php'; exit; }
$pageTitle = $p['title']; require 'header.php'; ?>
<div class="product">
  <div class="gallery"><?php if ($p['image']): ?><img src="uploads/<?= e($p['image']) ?>" alt="<?= e($p['title']) ?>"><?php else: ?><span>No image</span><?php endif; ?></div>
  <div class="details">
    <small><a href="index.php?cat=<?= $p['category_id'] ?>"><?= e($p['cat']) ?></a></small>
    <h1><?= e($p['title']) ?></h1>
    <div class="price big"><?= money($p['price']) ?></div>
    <p class="desc"><?= nl2br(e($p['description'])) ?></p>
    <p>Seller: <b><?= e($p['seller']) ?></b> · <?= e($p['seller_email']) ?></p>
    <p><?= $p['stock'] > 0 ? $p['stock'] . ' in stock' : '<b class="out">Sold out</b>' ?></p>
    <?php if ($p['stock'] > 0 && (!user() || user()['id'] != $p['user_id'])): ?>
    <form method="post" action="cart.php" class="addcart">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="add">
      <input type="hidden" name="id" value="<?= $p['id'] ?>">
      <input type="number" name="qty" value="1" min="1" max="<?= $p['stock'] ?>">
      <button class="btn">Add to cart</button>
    </form>
    <?php elseif (user() && user()['id'] == $p['user_id']): ?>
      <p class="note">This is your listing.</p>
    <?php endif; ?>
  </div>
</div>
<?php require 'footer.php'; ?>
