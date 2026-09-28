<?php 
require_once 'config/db.php';
include 'includes/header.php'; 

$msg = '';
$msg_class = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!empty($name) && !empty($email) && !empty($message) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);
        if ($stmt->execute()) {
            $msg = "Thank you! Your message has been stored.";
            $msg_class = "alert-success";
        } else {
            $msg = "Error submitting message.";
            $msg_class = "alert-danger";
        }
        $stmt->close();
    } else {
        $msg = "Please fill in all fields with a valid email.";
        $msg_class = "alert-danger";
    }
}
?>

<h2>Our Branches</h2>
<p>Visit Besufkad Gym at any of our premium locations across the city.</p>

<div class="card-grid" style="margin-bottom: 3rem;">
    <div class="card" style="text-align: center;">
        <h3 style="font-size: 1.2rem;">📍 Bole Atlas</h3>
        <p style="margin: 0; font-size: 0.9rem;">Main Branch, Atlas Road</p>
    </div>
    <div class="card" style="text-align: center;">
        <h3 style="font-size: 1.2rem;">📍 Haya Hulet</h3>
        <p style="margin: 0; font-size: 0.9rem;">Next to Mazoria</p>
    </div>
    <div class="card" style="text-align: center;">
        <h3 style="font-size: 1.2rem;">📍 Semit</h3>
        <p style="margin: 0; font-size: 0.9rem;">Safari Avenue</p>
    </div>
    <div class="card" style="text-align: center;">
        <h3 style="font-size: 1.2rem;">📍 Bisrate Gebreal</h3>
        <p style="margin: 0; font-size: 0.9rem;">Old Airport Road</p>
    </div>
</div>

<h2>Contact Us</h2>
<?php if ($msg): ?>
    <div class="alert <?= $msg_class ?>"><?= $msg ?></div>
<?php endif; ?>

<div class="form-card">
    <form action="contact.php" method="POST">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Message</label>
            <textarea name="message" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn">Send Message</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>