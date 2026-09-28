<?php
require_once 'config/db.php';
require_once 'includes/lang.php';
$page_title = 'Sign in | Besufkad Gym';
$error = '';
$username_value = '';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_post = $_POST['username'] ?? '';
    $username_value = is_string($username_post) ? trim($username_post) : '';
    $password = $_POST['password'] ?? '';
    $posted_token = $_POST['csrf_token'] ?? '';

    if (!is_string($posted_token) || !hash_equals($csrf_token, $posted_token)) {
        $error = 'Your form session expired. Please refresh and try again.';
    } elseif ($username_value === '' || !is_string($password) || $password === '') {
        $error = 'Enter your username and password.';
    } else {
        $stmt = $conn->prepare('SELECT id, username, password FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username_value);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: dashboard.php');
            exit;
        }
        $error = 'We couldn’t sign you in with those details.';
        $stmt->close();
    }
}
include 'includes/header.php';
?>

<div class="page-intro"><p class="eyebrow">Your next workout awaits</p><h2>Welcome back.</h2><p>Sign in to book a class and keep your training plans in one place.</p></div>
<?php if ($error): ?><div class="alert alert-danger" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<form class="form-card" action="login.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
    <div class="form-group"><label for="username">Username</label><input id="username" type="text" name="username" value="<?= htmlspecialchars($username_value, ENT_QUOTES, 'UTF-8') ?>" autocomplete="username" required></div>
    <div class="form-group"><label for="password">Password</label><input id="password" type="password" name="password" autocomplete="current-password" required></div>
    <button type="submit" class="button button-full">Sign in <span aria-hidden="true">→</span></button>
    <p class="form-footer">New to the club? <a href="register.php">Create an account</a></p>
</form>

<?php include 'includes/footer.php'; ?>