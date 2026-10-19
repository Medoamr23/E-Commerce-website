<?php require_once 'config.php';
$token = $_GET['token'] ?? $_POST['token'] ?? '';
$st = $pdo->prepare('SELECT * FROM password_resets WHERE token_hash=? AND expires_at > NOW()');
$st->execute([hash('sha256', $token)]); $reset = $st->fetch();
$error = '';
if ($reset && $_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $p1 = $_POST['password'] ?? ''; $p2 = $_POST['password2'] ?? '';
    if (strlen($p1) < 6) $error = 'Password must be at least 6 characters.';
    elseif ($p1 !== $p2) $error = 'Passwords do not match.';
    else {
        $pdo->prepare('UPDATE users SET password=? WHERE id=?')->execute([password_hash($p1, PASSWORD_DEFAULT), $reset['user_id']]);
        $pdo->prepare('DELETE FROM password_resets WHERE user_id=?')->execute([$reset['user_id']]);
        flash('Password changed. You can now log in.'); header('Location: login.php'); exit;
    }
}
$pageTitle = 'Choose new password'; require 'header.php'; ?>
<div class="form">
  <h1>Choose a new password</h1>
  <?php if (!$reset): ?>
    <div class="error">This reset link is invalid or has expired.</div>
    <p><a href="forgot_password.php">Request a new link</a></p>
  <?php else: ?>
    <?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
    <form method="post"><?= csrf_field() ?><input type="hidden" name="token" value="<?= e($token) ?>">
      <label>New password <input type="password" name="password" minlength="6" required></label>
      <label>Repeat password <input type="password" name="password2" minlength="6" required></label>
      <button class="btn">Save password</button></form>
  <?php endif; ?>
</div>
<?php require 'footer.php'; ?>
