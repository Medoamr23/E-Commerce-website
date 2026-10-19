<?php require_once 'config.php';
$error = '';
$next = $_GET['next'] ?? $_POST['next'] ?? 'index.php';
if (!preg_match('#^/?[a-zA-Z0-9_\-/\.\?=&%]*$#', $next) || str_contains($next, '//')) $next = 'index.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $st = $pdo->prepare('SELECT * FROM users WHERE email=?'); $st->execute([trim($_POST['email'] ?? '')]);
    $u = $st->fetch();
    if ($u && password_verify($_POST['password'] ?? '', $u['password']) && $u['is_admin']) $error = 'Admins must log in at the admin login page.';
    elseif ($u && password_verify($_POST['password'] ?? '', $u['password']) && ($b = ban_message($u))) $error = $b;
    elseif ($u && password_verify($_POST['password'] ?? '', $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => $u['id'], 'name' => $u['name'], 'email' => $u['email'], 'is_admin' => (int)$u['is_admin']];
        header('Location: ' . $next); exit;
    }
    else $error = 'Wrong email or password.';
}
$pageTitle = 'Login'; require 'header.php'; ?>
<form method="post" class="form">
  <h1>Login</h1>
  <?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
  <?= csrf_field() ?>
  <input type="hidden" name="next" value="<?= e($next) ?>">
  <label>Email <input type="email" name="email" required></label>
  <label>Password <input type="password" name="password" required></label>
  <p><a href="forgot_password.php">Forgot your password?</a></p>
  <button class="btn">Login</button>
  <p>New here? <a href="register.php">Create an account</a></p>
</form>
<?php require 'footer.php'; ?>
