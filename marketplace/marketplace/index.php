<?php $pageTitle = 'Home'; require 'header.php';
$where = []; $params = [];
if (!empty($_GET['q']))   { $where[] = '(p.title LIKE ? OR p.description LIKE ?)'; $params[] = '%'.$_GET['q'].'%'; $params[] = '%'.$_GET['q'].'%'; }
if (!empty($_GET['cat'])) { $where[] = 'p.category_id = ?'; $params[] = (int)$_GET['cat']; }
$sort = match ($_GET['sort'] ?? '') { 'low' => 'p.price ASC', 'high' => 'p.price DESC', default => 'p.created_at DESC' };
$sql = 'SELECT p.*, c.name AS cat, u.name AS seller FROM products p
        JOIN categories c ON c.id = p.category_id JOIN users u ON u.id = p.user_id'
     . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . " ORDER BY $sort";
$st = $pdo->prepare($sql); $st->execute($params); $products = $st->fetchAll();
?>
<div class="listhead">
  <h1><?= !empty($_GET['q']) ? 'Results for "' . e($_GET['q']) . '"' : 'Latest listings' ?></h1>
  <form method="get">
    <input type="hidden" name="q" value="<?= e($_GET['q'] ?? '') ?>">
    <input type="hidden" name="cat" value="<?= e($_GET['cat'] ?? '') ?>">
    <select name="sort" onchange="this.form.submit()">
      <option value="">Newest</option>
      <option value="low"  <?= ($_GET['sort'] ?? '') === 'low' ? 'selected' : '' ?>>Price: low to high</option>
      <option value="high" <?= ($_GET['sort'] ?? '') === 'high' ? 'selected' : '' ?>>Price: high to low</option>
    </select>
  </form>
</div>
<?php if (!$products): ?>
  <p class="empty">No products found. <a href="sell.php">Be the first to sell something!</a></p>
<?php endif; ?>
<div class="grid">
<?php foreach ($products as $p): ?>
  <a class="card" href="product.php?id=<?= $p['id'] ?>">
    <div class="thumb"><?php if ($p['image']): ?><img src="uploads/<?= e($p['image']) ?>" alt="<?= e($p['title']) ?>"><?php else: ?><span>No image</span><?php endif; ?></div>
    <div class="info">
      <small><?= e($p['cat']) ?></small>
      <h3><?= e($p['title']) ?></h3>
      <div class="price"><?= money($p['price']) ?></div>
      <small>by <?= e($p['seller']) ?><?= $p['stock'] < 1 ? ' · <b class="out">Sold out</b>' : '' ?></small>
    </div>
  </a>
<?php endforeach; ?>
</div>
<?php require 'footer.php'; ?>
