<?php require_once __DIR__ . '/config.php';
$cats = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle ?? 'Souq Market') ?> — Souq Market</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
  <a href="index.php" class="logo">souq<span>.</span></a>
  <form class="search" action="index.php" method="get">
    <select name="cat">
      <option value="">All</option>
      <?php foreach ($cats as $c): ?>
        <option value="<?= $c['id'] ?>" <?= (($_GET['cat'] ?? '') == $c['id']) ? 'selected' : '' ?>><?= e($c['name']) ?></option>
      <?php endforeach; ?>
    </select>
    <input type="text" name="q" placeholder="Search products..." value="<?= e($_GET['q'] ?? '') ?>">
    <button type="submit">Search</button>
  </form>
  <nav>
    <?php if (user()): ?>
      <span class="hello">Hi, <?= e(user()['name']) ?></span>
      <a href="sell.php">Sell</a>
      <a href="my_listings.php">My listings</a>
      <a href="orders.php">Orders</a>
      <a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="login.php">Login</a>
      <a href="register.php">Register</a>
    <?php endif; ?>
    <a href="cart.php" class="cart">Cart <b id="cart-count"><?= cart_count() ?></b></a>
  </nav>
</header>
<div class="catbar">
  <?php foreach ($cats as $c): ?><a href="index.php?cat=<?= $c['id'] ?>"><?= e($c['name']) ?></a><?php endforeach; ?>
</div>
<main class="container">
<?php if ($m = flash()): ?><div class="flash"><?= e($m) ?></div><?php endif; ?>
