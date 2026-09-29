<?php
require_once 'config/db.php';
require_once 'includes/lang.php';
$page_title = 'Visit & contact | Besufkad Gym';
$msg = '';
$msg_class = '';
$name = '';
$email = '';
$selected_plan = null;
$plan_id = filter_var($_GET['plan'] ?? null, FILTER_VALIDATE_INT);
if ($plan_id) {
    $plan_lookup = $conn->prepare('SELECT id, name, price, duration FROM plans WHERE id = ?');
    $plan_lookup->bind_param('i', $plan_id);
    $plan_lookup->execute();
    $selected_plan = $plan_lookup->get_result()->fetch_assoc() ?: null;
    $plan_lookup->close();
}
$message = $selected_plan
    ? 'I am interested in the ' . $selected_plan['name'] . ' package. Please tell me how to get started.'
    : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name_value = $_POST['name'] ?? '';
    $email_value = $_POST['email'] ?? '';
    $message_value = $_POST['message'] ?? '';
    $name = is_string($name_value) ? trim($name_value) : '';
    $email = is_string($email_value) ? trim($email_value) : '';
    $message = is_string($message_value) ? trim($message_value) : '';
    $posted_token = $_POST['csrf_token'] ?? '';

    if (!is_string($posted_token) || !hash_equals($csrf_token, $posted_token)) {
        $msg = 'Your form session expired. Please refresh and try again.';
        $msg_class = 'error';
    } elseif ($name === '' || $email === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = 'Please enter your name, a valid email and a message.';
        $msg_class = 'error';
    } else {
        $stmt = $conn->prepare('INSERT INTO messages (name, email, message) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $name, $email, $message);
        if ($stmt->execute()) {
            $msg = 'Thanks for reaching out. Your enquiry has been saved.';
            $msg_class = 'success';
        } else {
            $msg = 'We could not save your message. Please try again.';
            $msg_class = 'error';
        }
        $stmt->close();
    }
}
$dialog_message = $msg;
$dialog_title = $msg_class === 'success' ? 'Message received' : 'Please check your message';
$dialog_eyebrow = $msg_class === 'success' ? 'Thank you' : 'Message not sent';
$dialog_icon = $msg_class === 'success' ? '✓' : '!';
include 'includes/header.php';
?>

<section class="page-intro">
    <p class="eyebrow"><?= htmlspecialchars($t['contact_eyebrow'], ENT_QUOTES, 'UTF-8') ?></p>
    <h2><?= htmlspecialchars($t['contact_title'], ENT_QUOTES, 'UTF-8') ?></h2>
    <p><?= htmlspecialchars($t['contact_intro'], ENT_QUOTES, 'UTF-8') ?></p>
</section>

<div class="contact-layout">
    <div class="contact-info">
        <?php if ($selected_plan): ?>
            <article class="contact-info-card selected-package"><strong><?= htmlspecialchars($t['selected_package'], ENT_QUOTES, 'UTF-8') ?></strong><p><?= htmlspecialchars($selected_plan['name'], ENT_QUOTES, 'UTF-8') ?> · ETB <?= number_format((float) $selected_plan['price'], 0) ?> / <?= htmlspecialchars($selected_plan['duration'], ENT_QUOTES, 'UTF-8') ?></p></article>
        <?php endif; ?>
        <article class="contact-info-card"><strong>Bole Atlas</strong><p><?= htmlspecialchars($t['main_branch'], ENT_QUOTES, 'UTF-8') ?></p></article>
        <article class="contact-info-card"><strong>Haya Hulet</strong><p><?= htmlspecialchars($t['next_to_mazoria'], ENT_QUOTES, 'UTF-8') ?></p></article>
        <article class="contact-info-card"><strong>Semit</strong><p><?= htmlspecialchars($t['safari_avenue'], ENT_QUOTES, 'UTF-8') ?></p></article>
        <article class="contact-info-card"><strong>Bisrate Gebreal</strong><p><?= htmlspecialchars($t['old_airport_road'], ENT_QUOTES, 'UTF-8') ?></p></article>
        <div class="schedule-note"><?= htmlspecialchars($t['branch_help'], ENT_QUOTES, 'UTF-8') ?></div>
    </div>

    <div>
        <?php include 'includes/message_dialog.php'; ?>
        <form class="form-card" action="contact.php" method="POST">
            <h3><?= htmlspecialchars($t['send_message'], ENT_QUOTES, 'UTF-8') ?></h3>
            <p class="form-intro"><?= htmlspecialchars($t['contact_form_intro'], ENT_QUOTES, 'UTF-8') ?></p>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
            <div class="form-group"><label for="name"><?= htmlspecialchars($t['your_name'], ENT_QUOTES, 'UTF-8') ?></label><input id="name" type="text" name="name" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" autocomplete="name" maxlength="100" required></div>
            <div class="form-group"><label for="email"><?= htmlspecialchars($t['email_address'], ENT_QUOTES, 'UTF-8') ?></label><input id="email" type="email" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" autocomplete="email" maxlength="100" required></div>
            <div class="form-group"><label for="message"><?= htmlspecialchars($t['how_help'], ENT_QUOTES, 'UTF-8') ?></label><textarea id="message" name="message" rows="5" maxlength="2000" required><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></textarea></div>
            <button type="submit" class="button button-full"><?= htmlspecialchars($t['send_message_button'], ENT_QUOTES, 'UTF-8') ?> <span aria-hidden="true">↗</span></button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>