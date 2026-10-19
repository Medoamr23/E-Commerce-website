<?php require_once __DIR__ . '/config.php';
$cats = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle ?? 'Home') ?> — <?= APP_NAME ?></title>
<link rel="stylesheet" href="<?= url('assets/style.css') ?>">
</head>
<body>
<header class="topbar">
  <a href="<?= url('index.php') ?>" class="logo">Aura<span> Store</span></a>
  <form class="search" action="<?= url('index.php') ?>" method="get">
    <select name="cat">
      <option value="">All</option>
      <?php foreach ($cats as $c): ?>
        <option value="<?= $c['id'] ?>" <?= (($_GET['cat'] ?? '') == $c['id']) ? 'selected' : '' ?>><?= e($c['name']) ?></option>
      <?php endforeach; ?>
    </select>
    <input type="text" name="q" placeholder="Search Aura Store..." value="<?= e($_GET['q'] ?? '') ?>">
    <button type="submit">Search</button>
  </form>
  <nav>
    <?php if (user()): ?>
      <span class="hello">Hi, <?= e(user()['name']) ?></span>
      <?php if (is_admin()): ?><a href="<?= url('admin/index.php') ?>" class="adminlink">Admin</a><?php endif; ?>
      <a href="<?= url(is_admin() ? 'admin/account.php' : 'account.php') ?>">Account</a>
      <a href="<?= url('orders.php') ?>">Orders</a>
      <a href="<?= url('logout.php') ?>">Logout</a>
    <?php else: ?>
      <a href="<?= url('login.php') ?>">Login</a>
      <a href="<?= url('register.php') ?>">Register</a>
    <?php endif; ?>
    <a href="<?= url('cart.php') ?>" class="cart">Cart <b><?= cart_count() ?></b></a>
  </nav>
</header>
<div class="catbar">
  <?php foreach ($cats as $c): ?><a href="<?= url('index.php?cat=' . $c['id']) ?>"><?= e($c['name']) ?></a><?php endforeach; ?>
</div>
<main class="container">
<?php if ($m = flash()): ?><div class="flash"><?= e($m) ?></div><?php endif; ?>
