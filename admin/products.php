<?php define('IN_ADMIN', true); require_once '../config.php'; require_admin();
$errors = [];
$cats = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
function save_image(&$errors) {
    if (empty($_FILES['image']['name'])) return null;
    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) { $errors[] = 'Upload error (file too big for server?).'; return null; }
    $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
    $mime = mime_content_type($_FILES['image']['tmp_name']);
    if (!isset($allowed[$mime])) { $errors[] = 'Image must be JPG, PNG, WEBP or GIF.'; return null; }
    if ($_FILES['image']['size'] > 5*1024*1024) { $errors[] = 'Image must be under 5MB.'; return null; }
    $name = bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../uploads/' . $name)) { $errors[] = 'Upload failed (check uploads/ folder permissions).'; return null; }
    return $name;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $st = $pdo->prepare('SELECT image FROM products WHERE id=?'); $st->execute([(int)$_POST['id']]); $img = $st->fetchColumn();
        $pdo->prepare('DELETE FROM products WHERE id=?')->execute([(int)$_POST['id']]);
        if ($img && is_file(__DIR__ . '/../uploads/' . $img)) unlink(__DIR__ . '/../uploads/' . $img);
        flash('Product deleted.'); header('Location: products.php'); exit;
    }
    $title = trim($_POST['title'] ?? ''); $price = (float)($_POST['price'] ?? 0); $stock = (int)($_POST['stock'] ?? 0);
    $cc = $pdo->prepare('SELECT COUNT(*) FROM categories WHERE id=?'); $cc->execute([(int)($_POST['category_id'] ?? 0)]);
    if (!$cc->fetchColumn()) $errors[] = 'Choose a valid category.';
    if ($title === '') $errors[] = 'Title is required.';
    if ($price <= 0) $errors[] = 'Price must be greater than 0.';
    if ($stock < 0) $errors[] = 'Stock cannot be negative.';
    $img = $errors ? null : save_image($errors);
    if (!$errors) {
        $data = [(int)$_POST['category_id'], $title, trim($_POST['description'] ?? ''), $price, $stock];
        if ($action === 'update') {
            $pdo->prepare('UPDATE products SET category_id=?, title=?, description=?, price=?, stock=? WHERE id=?')->execute([...$data, (int)$_POST['id']]);
            if ($img) $pdo->prepare('UPDATE products SET image=? WHERE id=?')->execute([$img, (int)$_POST['id']]);
            flash('Product updated.');
        } else {
            $pdo->prepare('INSERT INTO products (category_id,title,description,price,stock,image) VALUES (?,?,?,?,?,?)')->execute([...$data, $img]);
            flash('Product added.');
        }
        header('Location: products.php'); exit;
    }
}
$edit = null;
if (isset($_GET['edit'])) { $st = $pdo->prepare('SELECT * FROM products WHERE id=?'); $st->execute([(int)$_GET['edit']]); $edit = $st->fetch(); if (!$edit) { flash('Product not found.'); header('Location: products.php'); exit; } }
$f = $edit ?: ($_POST ? $_POST + ['title'=>'','description'=>'','price'=>'','stock'=>1,'category_id'=>''] : ['title'=>'','description'=>'','price'=>'','stock'=>1,'category_id'=>'']);
$products = $pdo->query('SELECT p.*, c.name AS cat FROM products p LEFT JOIN categories c ON c.id=p.category_id ORDER BY p.id DESC')->fetchAll();
$pageTitle = 'Admin · Products'; require '../header.php'; require '_nav.php'; ?>
<form method="post" enctype="multipart/form-data" class="form wide" style="margin-top:0">
  <h2><?= $edit ? 'Edit product' : 'Add new product' ?></h2>
  <?php foreach ($errors as $er): ?><div class="error"><?= e($er) ?></div><?php endforeach; ?>
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="<?= $edit ? 'update' : 'create' ?>">
  <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
  <label>Title <input name="title" required maxlength="200" value="<?= e($f['title']) ?>"></label>
  <label>Category <select name="category_id"><?php foreach ($cats as $c): ?><option value="<?= $c['id'] ?>" <?= $f['category_id'] == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></label>
  <div class="row">
    <label>Price (<?= CURRENCY ?>) <input type="number" name="price" step="0.01" min="0.01" required value="<?= e($f['price']) ?>"></label>
    <label>Stock <input type="number" name="stock" min="0" value="<?= e($f['stock']) ?>"></label>
  </div>
  <label>Description <textarea name="description" rows="4"><?= e($f['description']) ?></textarea></label>
  <label>Photo <?= $edit ? '(leave empty to keep current)' : '' ?> <input type="file" name="image" accept="image/*" id="imgInput"></label>
  <img id="imgPreview" class="preview" alt="" hidden>
  <button class="btn"><?= $edit ? 'Save changes' : 'Add product' ?></button>
  <?php if ($edit): ?><a href="products.php">Cancel</a><?php endif; ?>
</form>
<table class="table">
  <tr><th></th><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th></th></tr>
  <?php foreach ($products as $p): ?>
  <tr>
    <td><?php if ($p['image']): ?><img src="../uploads/<?= e($p['image']) ?>" alt=""><?php endif; ?></td>
    <td><a href="../product.php?id=<?= $p['id'] ?>"><?= e($p['title']) ?></a></td>
    <td><?= e($p['cat']) ?></td><td><?= money($p['price']) ?></td><td><?= $p['stock'] ?></td>
    <td><a href="products.php?edit=<?= $p['id'] ?>">Edit</a>
      <form method="post" class="inline" onsubmit="return confirm('Delete this product?')"><?= csrf_field() ?>
      <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $p['id'] ?>"><button class="btn danger small">Delete</button></form></td>
  </tr>
  <?php endforeach; ?>
</table>
<?php require '../footer.php'; ?>
