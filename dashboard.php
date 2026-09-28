<?php 
require_once 'config/db.php';
include 'includes/header.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = '';
$msg_class = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_name = trim($_POST['class_name']);
    $branch = trim($_POST['branch']);
    $booking_date = $_POST['booking_date'];

    if (!empty($class_name) && !empty($branch) && !empty($booking_date)) {
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, class_name, branch, booking_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $class_name, $branch, $booking_date);
        
        if ($stmt->execute()) {
            $msg = "Class booked successfully at our " . htmlspecialchars($branch) . " branch!";
            $msg_class = "alert-success";
        } else {
            $msg = "Failed to book class.";
            $msg_class = "alert-danger";
        }
        $stmt->close();
    }
}

$stmt = $conn->prepare("SELECT id, class_name, branch, booking_date, created_at FROM bookings WHERE user_id = ? ORDER BY booking_date ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookings = $stmt->get_result();
?>

<h2>Member Dashboard</h2>
<p>Welcome back, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>!</p>
<br>

<?php if ($msg): ?><div class="alert <?= $msg_class ?>"><?= $msg ?></div><?php endif; ?>

<div class="form-card">
    <h3 style="text-align: center; margin-bottom: 20px;">Book a Fitness Class</h3>
    <form action="dashboard.php" method="POST">
        <div class="form-group">
            <label>Select Class</label>
            <select name="class_name" required>
                <option value="Yoga & Meditation">Yoga & Meditation</option>
                <option value="HIIT Body Blast">HIIT Body Blast</option>
                <option value="Heavy Powerlifting">Heavy Powerlifting</option>
                <option value="Spinning Cardio">Spinning Cardio</option>
            </select>
        </div>
        <div class="form-group">
            <label>Select Branch</label>
            <select name="branch" required>
                <option value="Bole Atlas">Bole Atlas</option>
                <option value="Haya Hulet">Haya Hulet</option>
                <option value="Semit">Semit</option>
                <option value="Bisrate Gebreal">Bisrate Gebreal</option>
            </select>
        </div>
        <div class="form-group">
            <label>Booking Date</label>
            <input type="date" name="booking_date" min="<?= date('Y-m-d') ?>" required>
        </div>
        <button type="submit" class="btn">Confirm Booking</button>
    </form>
</div>

<br><br>

<h2>Your Upcoming Classes</h2>
<div class="card-grid">
    <?php if ($bookings->num_rows > 0): ?>
        <?php while ($row = $bookings->fetch_assoc()): ?>
            <div class="card">
                <h3><?= htmlspecialchars($row['class_name']) ?></h3>
                <p style="text-align: left; margin-bottom: 5px;"><strong>📍 Branch:</strong> <?= htmlspecialchars($row['branch']) ?></p>
                <p style="text-align: left; margin-bottom: 15px;"><strong>📅 Date:</strong> <?= htmlspecialchars($row['booking_date']) ?></p>
                <small style="color: var(--text-muted);">Booked on: <?= htmlspecialchars($row['created_at']) ?></small>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align: center; width: 100%;">You have not booked any fitness classes yet.</p>
    <?php endif; ?>
</div>

<?php 
$stmt->close();
include 'includes/footer.php'; 
?>