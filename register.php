<?php
require_once 'config/db.php';
require_once 'includes/lang.php';
$page_title = 'Join the club | Besufkad Gym';
$error = '';
$success = false;
$form_values = ['full_name' => '', 'email' => '', 'username' => ''];

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($form_values as $key => $_) {
        $value = $_POST[$key] ?? '';
        $form_values[$key] = is_string($value) ? trim($value) : '';
    }
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $posted_token = $_POST['csrf_token'] ?? '';

    if (!is_string($posted_token) || !hash_equals($csrf_token, $posted_token)) {
        $error = 'Your form session expired. Please refresh and try again.';
    } elseif (in_array('', $form_values, true) || !is_string($password) || $password === '' || !is_string($confirm_password) || $confirm_password === '') {
        $error = 'Please complete every field.';
    } elseif (!filter_var($form_values['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a valid email address.';
    } elseif (!preg_match('/^[a-zA-Z0-9_.-]{3,30}$/', $form_values['username'])) {
        $error = 'Username must be 3–30 characters and use letters, numbers, dots, dashes or underscores.';
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,128}$/', $password)) {
        $error = 'Password must be 8–128 characters long and include uppercase, lowercase and a number.';
    } elseif ($password !== $confirm_password) {
        $error = 'The passwords do not match.';
    } else {
        $check = $conn->prepare('SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
        $check->bind_param('ss', $form_values['username'], $form_values['email']);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = 'That username or email is already registered. Try signing in instead.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO users (full_name, email, username, password) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('ssss', $form_values['full_name'], $form_values['email'], $form_values['username'], $hashed_password);
            if ($stmt->execute()) {
                $success = true;
            } else {
                $error = 'We could not create your account. Please try again.';
            }
            $stmt->close();
        }
        $check->close();
    }
}
$dialog_message = $success ? 'Your account is ready. Sign in to book your first class.' : $error;
$dialog_title = $success ? ($current_lang === 'am' ? 'እንኳን ወደ ክለቡ በደህና መጡ' : 'Welcome to the club') : ($current_lang === 'am' ? 'የምዝገባ ችግር' : 'Registration issue');
$dialog_eyebrow = $success ? ($current_lang === 'am' ? 'መለያ ተፈጥሯል' : 'Account created') : ($current_lang === 'am' ? 'ያስገቡትን ያረጋግጡ' : 'Please check your details');
$dialog_icon = $success ? '✓' : '!';
if ($success) {
    $dialog_link_href = 'login.php';
    $dialog_link_label = $t['sign_in_button'];
}
include 'includes/header.php';
?>

<div class="page-intro"><p class="eyebrow"><?= htmlspecialchars($t['register_eyebrow'], ENT_QUOTES, 'UTF-8') ?></p><h2><?= htmlspecialchars($t['register_title'], ENT_QUOTES, 'UTF-8') ?></h2><p><?= htmlspecialchars($t['register_intro'], ENT_QUOTES, 'UTF-8') ?></p></div>
<?php include 'includes/message_dialog.php'; ?>
<?php if (!$success): ?>
    <form class="form-card" id="registerForm" action="register.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
        <div class="form-group"><label for="full_name"><?= htmlspecialchars($t['full_name'], ENT_QUOTES, 'UTF-8') ?></label><input id="full_name" type="text" name="full_name" value="<?= htmlspecialchars($form_values['full_name'], ENT_QUOTES, 'UTF-8') ?>" autocomplete="name" maxlength="100" required></div>
        <div class="form-group"><label for="email"><?= htmlspecialchars($t['email_address'], ENT_QUOTES, 'UTF-8') ?></label><input id="email" type="email" name="email" value="<?= htmlspecialchars($form_values['email'], ENT_QUOTES, 'UTF-8') ?>" autocomplete="email" maxlength="100" required></div>
        <div class="form-group"><label for="username"><?= htmlspecialchars($t['choose_username'], ENT_QUOTES, 'UTF-8') ?></label><input id="username" type="text" name="username" value="<?= htmlspecialchars($form_values['username'], ENT_QUOTES, 'UTF-8') ?>" autocomplete="username" minlength="3" maxlength="30" required><p class="form-hint"><?= htmlspecialchars($t['username_hint'], ENT_QUOTES, 'UTF-8') ?></p></div>
        <div class="form-grid">
            <div class="form-group"><label for="password"><?= htmlspecialchars($t['create_password'], ENT_QUOTES, 'UTF-8') ?></label><input id="password" type="password" name="password" autocomplete="new-password" minlength="8" maxlength="128" required></div>
            <div class="form-group"><label for="confirm_password"><?= htmlspecialchars($t['confirm_password'], ENT_QUOTES, 'UTF-8') ?></label><input id="confirm_password" type="password" name="confirm_password" autocomplete="new-password" minlength="8" maxlength="128" required></div>
        </div>
        <button type="submit" class="button button-full"><?= htmlspecialchars($t['create_account_button'], ENT_QUOTES, 'UTF-8') ?> <span aria-hidden="true">→</span></button>
        <p class="form-footer"><?= htmlspecialchars($t['already_member'], ENT_QUOTES, 'UTF-8') ?> <a href="login.php"><?= htmlspecialchars($t['sign_in_button'], ENT_QUOTES, 'UTF-8') ?></a></p>
    </form>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>