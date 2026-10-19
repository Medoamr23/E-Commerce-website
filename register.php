<?php require_once 'config.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $name = trim($_POST['name'] ?? ''); $email = trim($_POST['email'] ?? ''); $pass = $_POST['password'] ?? '';
    if ($name === '') $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (strlen($pass) < 6) $errors[] = 'Password must be at least 6 characters.';
    if (!$errors) {
        $st = $pdo->prepare('SELECT id FROM users WHERE email=?'); $st->execute([$email]);
        if ($st->fetch()) $errors[] = 'Email already registered.';
    }
    if (!$errors) {
        $pdo->prepare('INSERT INTO users (name,email,password) VALUES (?,?,?)')->execute([$name, $email, password_hash($pass, PASSWORD_DEFAULT)]);
        $_SESSION['user'] = ['id' => $pdo->lastInsertId(), 'name' => $name, 'email' => $email, 'is_admin' => 0];
        session_regenerate_id(true);
        flash('Welcome, ' . $name . '!'); header('Location: index.php'); exit;
    }
}
$pageTitle = 'Register'; require 'header.php'; ?>
<form method="post" class="form">
  <h1>Create account</h1>
  <?php foreach ($errors as $er): ?><div class="error"><?= e($er) ?></div><?php endforeach; ?>
  <?= csrf_field() ?>
  <label>Name <input name="name" required value="<?= e($_POST['name'] ?? '') ?>"></label>
  <label>Email <input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>"></label>
  <label>Password <input type="password" name="password" required minlength="6"></label>
  <button class="btn">Register</button>
  <p>Already have an account? <a href="login.php">Login</a></p>
</form>
<?php require 'footer.php'; ?>
