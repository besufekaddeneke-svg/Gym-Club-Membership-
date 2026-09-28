<?php
require_once 'config/db.php';
require_once 'includes/lang.php';
$page_title = 'Visit & contact | Besufkad Gym';
$msg = '';
$msg_class = '';

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
        $msg_class = 'alert-danger';
    } elseif ($name === '' || $email === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = 'Please enter your name, a valid email and a message.';
        $msg_class = 'alert-danger';
    } else {
        $stmt = $conn->prepare('INSERT INTO messages (name, email, message) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $name, $email, $message);
        if ($stmt->execute()) {
            $msg = 'Thanks for reaching out. Your enquiry has been saved.';
            $msg_class = 'alert-success';
        } else {
            $msg = 'We could not save your message. Please try again.';
            $msg_class = 'alert-danger';
        }
        $stmt->close();
    }
}
include 'includes/header.php';
?>

<section class="page-intro">
    <p class="eyebrow">Drop by or drop us a line</p>
    <h2>We’d love to meet you.</h2>
    <p>Want to tour the gym, ask about a membership or find the right first class? Send us a note and tell us how we can help.</p>
</section>

<div class="contact-layout">
    <div class="contact-info">
        <article class="contact-info-card"><strong>Bole Atlas</strong><p>Main branch · Atlas Road</p></article>
        <article class="contact-info-card"><strong>Haya Hulet</strong><p>Next to Mazoria</p></article>
        <article class="contact-info-card"><strong>Semit</strong><p>Safari Avenue</p></article>
        <article class="contact-info-card"><strong>Bisrate Gebreal</strong><p>Old Airport Road</p></article>
        <div class="schedule-note">Not sure which branch is right for you? Leave a message and mention your neighborhood. We’ll help you find us.</div>
    </div>

    <div>
        <?php if ($msg): ?>
            <div class="alert <?= htmlspecialchars($msg_class, ENT_QUOTES, 'UTF-8') ?>" role="status"><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <form class="form-card" action="contact.php" method="POST">
            <h3>Send us a message</h3>
            <p class="form-intro">Your message will be recorded so the club can review your enquiry.</p>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
            <div class="form-group"><label for="name">Your name</label><input id="name" type="text" name="name" autocomplete="name" maxlength="100" required></div>
            <div class="form-group"><label for="email">Email address</label><input id="email" type="email" name="email" autocomplete="email" maxlength="100" required></div>
            <div class="form-group"><label for="message">How can we help?</label><textarea id="message" name="message" rows="5" maxlength="2000" required></textarea></div>
            <button type="submit" class="button button-full">Send message <span aria-hidden="true">↗</span></button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>