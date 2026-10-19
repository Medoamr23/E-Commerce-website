<?php if (!defined('IN_ADMIN')) require_once __DIR__ . '/config.php';
if (!user()) { header('Location: ' . url(defined('IN_ADMIN') ? 'admin/login.php' : 'login.php')); exit; }
$id = user()['id']; $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf(); $a = $_POST['action'] ?? '';
    $st = $pdo->prepare('SELECT * FROM users WHERE id=?'); $st->execute([$id]); $me = $st->fetch();
    if ($a === 'image') {
        $f = $_FILES['avatar'] ?? null; $ok = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
        $type = $f && $f['error'] === 0 ? mime_content_type($f['tmp_name']) : '';
        if (!isset($ok[$type]) || $f['size'] > 2*1024*1024) $err = 'Upload a JPG/PNG/WEBP/GIF under 2 MB.';
        else {
            $name = 'avatar_' . $id . '_' . bin2hex(random_bytes(6)) . '.' . $ok[$type];
            move_uploaded_file($f['tmp_name'], __DIR__ . '/uploads/' . $name);
            if ($me['avatar']) @unlink(__DIR__ . '/uploads/' . basename($me['avatar']));
            $pdo->prepare('UPDATE users SET avatar=? WHERE id=?')->execute([$name, $id]); flash('Photo updated.');
        }
    } elseif ($a === 'email') {
        $em = trim($_POST['email'] ?? '');
        if (!password_verify($_POST['current'] ?? '', $me['password'])) $err = 'Current password is wrong.';
        elseif (!filter_var($em, FILTER_VALIDATE_EMAIL)) $err = 'Invalid email.';
        else {
            $c = $pdo->prepare('SELECT id FROM users WHERE email=? AND id<>?'); $c->execute([$em, $id]);
            if ($c->fetch()) $err = 'That email is already used.';
            else { $pdo->prepare('UPDATE users SET email=? WHERE id=?')->execute([$em, $id]); $_SESSION['user']['email'] = $em; flash('Email updated.'); }
        }
    } elseif ($a === 'password') {
        $p1 = $_POST['password'] ?? '';
        if (!password_verify($_POST['current'] ?? '', $me['password'])) $err = 'Current password is wrong.';
        elseif (strlen($p1) < 6) $err = 'New password must be at least 6 characters.';
        elseif ($p1 !== ($_POST['password2'] ?? '')) $err = 'Passwords do not match.';
        else { $pdo->prepare('UPDATE users SET password=? WHERE id=?')->execute([password_hash($p1, PASSWORD_DEFAULT), $id]); flash('Password changed.'); }
    } elseif ($a === 'phone') {
        $ph = trim($_POST['phone'] ?? '');
        if (!preg_match('/^\+?[0-9 \-]{7,20}$/', $ph)) $err = 'Enter a valid phone number.';
        else { $pdo->prepare('UPDATE users SET phone=? WHERE id=?')->execute([$ph, $id]); flash('Phone saved.'); }
    }
    if (!$err) { header('Location: account.php'); exit; }
}
$st = $pdo->prepare('SELECT * FROM users WHERE id=?'); $st->execute([$id]); $me = $st->fetch();
$pageTitle = 'Account settings'; require __DIR__ . '/header.php';
if (defined('IN_ADMIN')) require __DIR__ . '/admin/_nav.php'; ?>
<div class="form">
  <h1>Account settings</h1>
  <?php if ($err): ?><div class="error"><?= e($err) ?></div><?php endif; ?>
  <img src="<?= url('uploads/' . e($me['avatar'] ?: '.gitkeep')) ?>" alt="" style="width:96px;height:96px;border-radius:50%;object-fit:cover;background:#ddd">
  <form method="post" enctype="multipart/form-data"><?= csrf_field() ?><input type="hidden" name="action" value="image">
    <label>Change image <input type="file" name="avatar" accept="image/*" required></label><button class="btn">Upload</button></form><hr>
  <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="email">
    <label>New email <input type="email" name="email" value="<?= e($me['email']) ?>" required></label>
    <label>Current password <input type="password" name="current" required></label><button class="btn">Change email</button></form><hr>
  <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="password">
    <label>Current password <input type="password" name="current" required></label>
    <label>New password <input type="password" name="password" minlength="6" required></label>
    <label>Repeat new password <input type="password" name="password2" minlength="6" required></label><button class="btn">Reset password</button></form><hr>
  <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="phone">
    <label>Phone number <input type="tel" name="phone" value="<?= e($me['phone']) ?>" required></label><button class="btn">Save phone</button></form>
</div>
<?php require __DIR__ . '/footer.php'; ?>
