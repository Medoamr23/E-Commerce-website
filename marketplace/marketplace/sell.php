<?php require_once 'config.php'; require_login();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $title = trim($_POST['title'] ?? ''); $desc = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0); $stock = (int)($_POST['stock'] ?? 1); $cat = (int)($_POST['category_id'] ?? 0);
    if ($title === '') $errors[] = 'Title is required.';
    if ($price <= 0) $errors[] = 'Price must be greater than 0.';
    if ($stock < 1) $errors[] = 'Quantity must be at least 1.';
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
        $mime = mime_content_type($_FILES['image']['tmp_name']);
        if (!isset($allowed[$mime])) $errors[] = 'Image must be JPG, PNG, WEBP or GIF.';
        elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) $errors[] = 'Image must be under 5MB.';
        else {
            $image = bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/uploads/' . $image);
        }
    }
    if (!$errors) {
        $pdo->prepare('INSERT INTO products (user_id,category_id,title,description,price,stock,image) VALUES (?,?,?,?,?,?,?)')
            ->execute([user()['id'], $cat, $title, $desc, $price, $stock, $image]);
        flash('Your listing is live!'); header('Location: product.php?id=' . $pdo->lastInsertId()); exit;
    }
}
$pageTitle = 'Sell an item'; require 'header.php'; ?>
<form method="post" enctype="multipart/form-data" class="form wide">
  <h1>Sell an item</h1>
  <?php foreach ($errors as $er): ?><div class="error"><?= e($er) ?></div><?php endforeach; ?>
  <?= csrf_field() ?>
  <label>Title <input name="title" required maxlength="200" value="<?= e($_POST['title'] ?? '') ?>"></label>
  <label>Category
    <select name="category_id"><?php foreach ($cats as $c): ?><option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?></select>
  </label>
  <div class="row">
    <label>Price (<?= CURRENCY ?>) <input type="number" name="price" step="0.01" min="0.01" required value="<?= e($_POST['price'] ?? '') ?>"></label>
    <label>Quantity <input type="number" name="stock" min="1" value="<?= e($_POST['stock'] ?? 1) ?>"></label>
  </div>
  <label>Description <textarea name="description" rows="5"><?= e($_POST['description'] ?? '') ?></textarea></label>
  <label>Photo <input type="file" name="image" accept="image/*" id="imgInput"></label>
  <img id="imgPreview" class="preview" alt="" hidden>
  <button class="btn">Publish listing</button>
</form>
<?php require 'footer.php'; ?>
