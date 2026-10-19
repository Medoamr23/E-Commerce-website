<?php
// ===== Database settings (Laragon defaults) =====
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'aura_store');
define('DB_USER', 'root');
define('DB_PASS', '');
define('CURRENCY', 'EGP');

session_start();

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Database connection failed. Did you import database.sql? ' . htmlspecialchars($e->getMessage()));
}

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function money($n) { return number_format((float)$n, 2) . ' ' . CURRENCY; }
function user() { return $_SESSION['user'] ?? null; }
function require_login() {
    if (!user()) { header('Location: ' . url('login.php') . '?next=' . urlencode($_SERVER['REQUEST_URI'])); exit; }
}
function csrf_token() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field() { return '<input type="hidden" name="csrf" value="' . csrf_token() . '">'; }
function check_csrf() {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) die('Invalid request (CSRF).');
}
function flash($msg = null) {
    if ($msg !== null) { $_SESSION['flash'] = $msg; return; }
    $m = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $m;
}
function cart_count() { return array_sum($_SESSION['cart'] ?? []); }
define('APP_NAME', 'Aura Store');
define('DEV_MODE', true); // true = show password-reset link on screen (no mail server needed in Laragon)

function is_admin() { return !empty($_SESSION['user']['is_admin']); }
function require_admin() {
    if (!is_admin()) { header('Location: ' . (defined('IN_ADMIN') ? '' : 'admin/') . 'login.php'); exit; }
}
// path prefix so admin/ pages can reuse header/footer
function url($path) { return (defined('IN_ADMIN') ? '../' : '') . $path; }

// ban status: returns message if banned, else null
function ban_message($u) {
    if (!empty($u['perm_banned'])) return 'This account is permanently banned.';
    if (!empty($u['banned_until']) && strtotime($u['banned_until']) > time()) return 'This account is banned until ' . $u['banned_until'] . '.';
    return null;
}
// kick out banned/deleted users on every request
if (user()) {
    $st = $pdo->prepare('SELECT is_admin, perm_banned, banned_until FROM users WHERE id=?'); $st->execute([user()['id']]);
    $row = $st->fetch();
    if (!$row || ban_message($row)) { $msg = $row ? ban_message($row) : 'Account removed.'; $_SESSION = []; session_regenerate_id(true); flash($msg); }
    else $_SESSION['user']['is_admin'] = (int)$row['is_admin']; // demoted admins lose access immediately
}
