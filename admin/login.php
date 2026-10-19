<?php define('IN_ADMIN', true); require_once '../config.php';
if (is_admin()) { header('Location: index.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $st = $pdo->prepare('SELECT * FROM users WHERE email=? AND is_admin=1'); $st->execute([trim($_POST['email'] ?? '')]); $u = $st->fetch();
    if ($u && password_verify($_POST['password'] ?? '', $u['password']) && !ban_message($u)) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email'],'is_admin'=>1];
        header('Location: index.php'); exit;
    }
    $error = 'Wrong admin email or password.';
} ?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin login — <?= APP_NAME ?></title><link rel="stylesheet" href="../assets/style.css"></head>
<body style="background:#131921"><form method="post" class="form" style="margin:10vh auto">
  <h1>Admin panel login</h1>
  <?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
  <?= csrf_field() ?>
  <label>Admin email <input type="email" name="email" required></label>
  <label>Password <input type="password" name="password" required></label>
  <button class="btn">Login</button>
</form></body></html>
