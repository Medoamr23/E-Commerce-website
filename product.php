<?php require_once 'config.php';
$id = (int)($_GET['id'] ?? 0);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_login(); check_csrf();
    if (($_POST['action'] ?? '') === 'comment') {
        $body = trim($_POST['body'] ?? '');
        if ($body === '' || mb_strlen($body) > 1000) flash('Comment must be 1–1000 characters.');
        else {
            // Stored as plain text with prepared statements; always escaped with e() on output => XSS-safe.
            $pdo->prepare('INSERT INTO comments (product_id,user_id,body) VALUES (?,?,?)')->execute([$id, user()['id'], $body]);
            flash('Comment posted.');
        }
    } elseif (($_POST['action'] ?? '') === 'delete_comment') {
        if (is_admin()) $pdo->prepare('DELETE FROM comments WHERE id=?')->execute([(int)$_POST['comment_id']]);
        else $pdo->prepare('DELETE FROM comments WHERE id=? AND user_id=?')->execute([(int)$_POST['comment_id'], user()['id']]);
    }
    header('Location: product.php?id=' . $id . '#comments'); exit;
}
$st = $pdo->prepare('SELECT p.*, c.name AS cat FROM products p JOIN categories c ON c.id=p.category_id WHERE p.id=?');
$st->execute([$id]); $p = $st->fetch();
if (!$p) { http_response_code(404); $pageTitle='Not found'; require 'header.php'; echo '<p>Product not found.</p>'; require 'footer.php'; exit; }
$cs = $pdo->prepare('SELECT cm.*, u.name FROM comments cm JOIN users u ON u.id=cm.user_id WHERE cm.product_id=? ORDER BY cm.created_at DESC');
$cs->execute([$id]); $comments = $cs->fetchAll();
$pageTitle = $p['title']; require 'header.php'; ?>
<div class="product">
  <div class="gallery"><?php if ($p['image']): ?><img src="uploads/<?= e($p['image']) ?>" alt="<?= e($p['title']) ?>"><?php else: ?><span>No image</span><?php endif; ?></div>
  <div class="details">
    <small><a href="index.php?cat=<?= $p['category_id'] ?>"><?= e($p['cat']) ?></a></small>
    <h1><?= e($p['title']) ?></h1>
    <div class="price big"><?= money($p['price']) ?></div>
    <p class="desc"><?= nl2br(e($p['description'])) ?></p>
    <p><?= $p['stock'] > 0 ? $p['stock'] . ' in stock' : '<b class="out">Sold out</b>' ?></p>
    <?php if ($p['stock'] > 0): ?>
    <form method="post" action="cart.php" class="addcart">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="add">
      <input type="hidden" name="id" value="<?= $p['id'] ?>">
      <input type="number" name="qty" value="1" min="1" max="<?= $p['stock'] ?>">
      <button class="btn">Add to cart</button>
    </form>
    <?php endif; ?>
  </div>
</div>

<section class="comments" id="comments">
  <h2>Comments (<?= count($comments) ?>)</h2>
  <?php if (user()): ?>
    <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="comment">
      <textarea name="body" rows="3" maxlength="1000" required placeholder="Share your thoughts about this product..."></textarea>
      <button class="btn">Post comment</button></form>
  <?php else: ?>
    <p><a href="login.php?next=<?= urlencode('product.php?id=' . $id) ?>">Log in</a> to leave a comment.</p>
  <?php endif; ?>
  <?php foreach ($comments as $c): ?>
    <div class="comment">
      <b><?= e($c['name']) ?></b> <small><?= e($c['created_at']) ?></small>
      <p><?= e($c['body']) ?></p>
      <?php if (user() && (is_admin() || user()['id'] == $c['user_id'])): ?>
        <form method="post" class="inline" onsubmit="return confirm('Delete comment?')"><?= csrf_field() ?>
          <input type="hidden" name="action" value="delete_comment"><input type="hidden" name="comment_id" value="<?= $c['id'] ?>">
          <button class="btn danger small">Delete</button></form>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</section>
<?php require 'footer.php'; ?>
