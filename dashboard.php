<?php
require_once 'config/db.php';
require_once 'includes/lang.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'My bookings | Besufkad Gym';
$user_id = (int) $_SESSION['user_id'];
$classes = [
    'Yoga & Meditation',
    'HIIT Body Blast',
    'Heavy Powerlifting',
    'Spinning Cardio',
];
$branches = ['Bole Atlas', 'Haya Hulet', 'Semit', 'Bisrate Gebreal'];
$today = new DateTimeImmutable('today');
$latest_booking = $today->modify('+6 months');
$message = '';
$message_class = 'alert-danger';

if (!empty($_SESSION['dashboard_notice'])) {
    $message = $_SESSION['dashboard_notice']['message'];
    $message_class = $_SESSION['dashboard_notice']['class'];
    unset($_SESSION['dashboard_notice']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted_token = $_POST['csrf_token'] ?? '';
    if (!is_string($posted_token) || !hash_equals($csrf_token, $posted_token)) {
        $message = 'Your form session expired. Refresh the page and try again.';
    } elseif (($_POST['action'] ?? '') === 'cancel') {
        $booking_id = filter_var($_POST['booking_id'] ?? null, FILTER_VALIDATE_INT);
        if (!$booking_id) {
            $message = 'We could not find that booking.';
        } else {
            $stmt = $conn->prepare('DELETE FROM bookings WHERE id = ? AND user_id = ? AND booking_date >= CURDATE()');
            $stmt->bind_param('ii', $booking_id, $user_id);
            $stmt->execute();
            if ($stmt->affected_rows === 1) {
                $_SESSION['dashboard_notice'] = ['message' => 'Your class reservation has been cancelled.', 'class' => 'alert-success'];
                $stmt->close();
                header('Location: dashboard.php');
                exit;
            }
            $message = 'That booking could not be cancelled. Past classes can no longer be changed.';
            $stmt->close();
        }
    } elseif (($_POST['action'] ?? '') === 'book') {
        $class_name = $_POST['class_name'] ?? '';
        $branch = $_POST['branch'] ?? '';
        $booking_date = $_POST['booking_date'] ?? '';
        $date = is_string($booking_date) ? DateTimeImmutable::createFromFormat('!Y-m-d', $booking_date) : false;
        $date_errors = DateTimeImmutable::getLastErrors();
        $valid_date = $date && ($date_errors === false || ($date_errors['warning_count'] === 0 && $date_errors['error_count'] === 0)) && $date->format('Y-m-d') === $booking_date;
        if (!in_array($class_name, $classes, true) || !in_array($branch, $branches, true)) {
            $message = 'Choose a class and branch from the available options.';
        } elseif (!$valid_date || $date < $today || $date > $latest_booking) {
            $message = 'Choose a date from today through the next six months.';
        } else {
            $check = $conn->prepare('SELECT id FROM bookings WHERE user_id = ? AND class_name = ? AND booking_date = ? LIMIT 1');
            $check->bind_param('iss', $user_id, $class_name, $booking_date);
            $check->execute();
            $already_booked = $check->get_result()->num_rows > 0;
            $check->close();

            if ($already_booked) {
                $message = 'You already have this class booked on that date.';
            } else {
                $stmt = $conn->prepare('INSERT INTO bookings (user_id, class_name, branch, booking_date) VALUES (?, ?, ?, ?)');
                $stmt->bind_param('isss', $user_id, $class_name, $branch, $booking_date);
                if ($stmt->execute()) {
                    $_SESSION['dashboard_notice'] = ['message' => 'You’re booked! We’ll see you at ' . $branch . '.', 'class' => 'alert-success'];
                    $stmt->close();
                    header('Location: dashboard.php');
                    exit;
                }
                $message = 'Your booking could not be saved. Please try again.';
                $stmt->close();
            }
        }
    }
}

$today_value = $today->format('Y-m-d');
$latest_value = $latest_booking->format('Y-m-d');
$stmt = $conn->prepare('SELECT id, class_name, branch, booking_date, created_at FROM bookings WHERE user_id = ? AND booking_date >= CURDATE() ORDER BY booking_date ASC, created_at ASC');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$bookings = $stmt->get_result();
include 'includes/header.php';
?>

<div class="dashboard-heading">
    <div>
        <p class="eyebrow">Member space</p>
        <h2>Good to see you, <?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?>.</h2>
        <p>Your next good workout is just one booking away.</p>
    </div>
    <span class="pill"><?= $bookings->num_rows ?> upcoming <?= $bookings->num_rows === 1 ? 'class' : 'classes' ?></span>
</div>

<?php if ($message): ?>
    <div class="alert <?= htmlspecialchars($message_class, ENT_QUOTES, 'UTF-8') ?>" role="status"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="dashboard-grid">
    <section class="form-card" id="book-class">
        <p class="eyebrow">Make it happen</p>
        <h3>Book a class</h3>
        <p class="form-intro">Pick a class, location and date. You can cancel anytime before the class day.</p>
        <form action="dashboard.php#book-class" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="action" value="book">
            <div class="form-group">
                <label for="class_name">Choose a class</label>
                <select id="class_name" name="class_name" required>
                    <option value="">Select a class</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= htmlspecialchars($class, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($class, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="branch">Choose a branch</label>
                <select id="branch" name="branch" required>
                    <option value="">Select a location</option>
                    <?php foreach ($branches as $branch_option): ?>
                        <option value="<?= htmlspecialchars($branch_option, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($branch_option, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="booking_date">Choose a date</label>
                <input id="booking_date" type="date" name="booking_date" min="<?= $today_value ?>" max="<?= $latest_value ?>" required>
                <p class="form-hint">Bookings are available up to six months ahead.</p>
            </div>
            <button type="submit" class="button button-full">Reserve my spot <span aria-hidden="true">→</span></button>
        </form>
    </section>

    <section>
        <p class="eyebrow">On your calendar</p>
        <h3 style="margin: 0 0 18px;">Upcoming classes</h3>
        <?php if ($bookings->num_rows > 0): ?>
            <div class="booking-list">
                <?php while ($booking = $bookings->fetch_assoc()): ?>
                    <article class="booking-item">
                        <div>
                            <h3><?= htmlspecialchars($booking['class_name'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <p><?= htmlspecialchars(date('D, M j, Y', strtotime($booking['booking_date'])), ENT_QUOTES, 'UTF-8') ?></p>
                            <p><?= htmlspecialchars($booking['branch'], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                        <form action="dashboard.php" method="POST" onsubmit="return confirm('Cancel this class reservation?');">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="action" value="cancel">
                            <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
                            <button class="cancel-button" type="submit">Cancel</button>
                        </form>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="card booking-empty">
                <span class="feature-icon" aria-hidden="true">◎</span>
                <h3 style="margin-top: 40px;">Your calendar is open.</h3>
                <p>Book your first class and it’ll show up right here.</p>
                <a class="text-link" href="#book-class">Find a class <span aria-hidden="true">↓</span></a>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php
$stmt->close();
include 'includes/footer.php';
?>