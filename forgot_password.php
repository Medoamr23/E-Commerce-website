<?php require_once 'config.php';
$sent = false; $devLink = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $st = $pdo->prepare('SELECT id, email FROM users WHERE email=?'); $st->execute([trim($_POST['email'] ?? '')]);
    if ($u = $st->fetch()) {
        $token = bin2hex(random_bytes(32));
        $pdo->prepare('DELETE FROM password_resets WHERE user_id=?')->execute([$u['id']]);
        $pdo->prepare('INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?,?, DATE_ADD(NOW(), INTERVAL 1 HOUR))')
            ->execute([$u['id'], hash('sha256', $token)]);
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $link = $scheme . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/reset_password.php?token=' . $token;
        @mail($u['email'], APP_NAME . ' password reset', "Reset your password (valid 1 hour):\n$link");
        if (DEV_MODE) $devLink = $link;
    }
    $sent = true; // same message whether or not the email exists
}
$pageTitle = 'Forgot password'; require 'header.php'; ?>
<form method="post" class="form">
  <h1>Reset password</h1>
  <?php if ($sent): ?>
    <div class="flash">If that email is registered, a reset link has been sent. It expires in 1 hour.</div>
    <?php if ($devLink): ?><p><b>Dev mode link:</b></p><p class="resetlink"><a href="<?= e($devLink) ?>"><?= e($devLink) ?></a></p><?php endif; ?>
  <?php endif; ?>
  <?= csrf_field() ?>
  <label>Your email <input type="email" name="email" required></label>
  <button class="btn">Send reset link</button>
  <p><a href="login.php">Back to login</a></p>
</form>
<?php require 'footer.php'; ?>
