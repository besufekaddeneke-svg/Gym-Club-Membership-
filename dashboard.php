<?php
require_once 'config/db.php';
require_once 'includes/lang.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'My bookings | Besufkad Gym';
$user_id = (int) $_SESSION['user_id'];
$plan_prices = [
    '2 Days / Week' => 2000,
    '4 Days / Week' => 3500,
    'Whole Week' => 5000,
];
$exercise_types = ['Cardio', 'Muscle', 'MMA', 'General Fitness'];
$payment_methods = ['Chapa', 'Telebirr', 'CBE Birr'];
$months_options = range(1, 12);
$branches = ['Bole Atlas', 'Haya Hulet', 'Semit', 'Bisrate Gebreal'];
$today = new DateTimeImmutable('today');
$latest_booking = $today->modify('+6 months');
$popup = null;
$message = '';

if (!empty($_SESSION['dashboard_notice'])) {
    $popup = $_SESSION['dashboard_notice'];
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
                $_SESSION['dashboard_notice'] = ['type' => 'message', 'message' => 'Your membership plan has been cancelled.'];
                $stmt->close();
                header('Location: dashboard.php');
                exit;
            }
            $message = 'That booking could not be cancelled. Past classes can no longer be changed.';
            $stmt->close();
        }
    } elseif (($_POST['action'] ?? '') === 'book') {
        $plan_name = $_POST['plan_name'] ?? '';
        $branch = $_POST['branch'] ?? '';
        $exercise_type = $_POST['exercise_type'] ?? '';
        $payment_method = $_POST['payment_method'] ?? '';
        $months_paid = filter_var($_POST['months_paid'] ?? 1, FILTER_VALIDATE_INT);
        $booking_date = $_POST['booking_date'] ?? '';
        $date = is_string($booking_date) ? DateTimeImmutable::createFromFormat('!Y-m-d', $booking_date) : false;
        $date_errors = DateTimeImmutable::getLastErrors();
        $valid_date = $date && ($date_errors === false || ($date_errors['warning_count'] === 0 && $date_errors['error_count'] === 0)) && $date->format('Y-m-d') === $booking_date;

        if (!isset($plan_prices[$plan_name]) || !in_array($exercise_type, $exercise_types, true) || !in_array($payment_method, $payment_methods, true) || !in_array($branch, $branches, true)) {
            $message = 'Choose a valid membership plan, exercise focus, payment method, and branch.';
        } elseif (!$months_paid || $months_paid < 1 || $months_paid > 12) {
            $message = 'Choose how many months you want to pay for, from 1 to 12.';
        } elseif (!$valid_date || $date < $today || $date > $latest_booking) {
            $message = 'Choose a date from today through the next six months.';
        } else {
            $total_price = $plan_prices[$plan_name] * $months_paid;
            $end_date = $date->modify('+' . $months_paid . ' month')->format('Y-m-d');

            $check = $conn->prepare('SELECT id FROM bookings WHERE user_id = ? AND class_name = ? AND booking_date = ? AND exercise_type = ? LIMIT 1');
            $check->bind_param('isss', $user_id, $plan_name, $booking_date, $exercise_type);
            $check->execute();
            $already_booked = $check->get_result()->num_rows > 0;
            $check->close();

            if ($already_booked) {
                $message = 'You already have that plan and focus scheduled for that start date.';
            } else {
                $payment_status = 'pending';
                $payment_reference = 'BG-' . $today->format('ymd') . '-' . strtoupper(bin2hex(random_bytes(5)));
                $stmt = $conn->prepare('INSERT INTO bookings (user_id, class_name, branch, booking_date, end_date, exercise_type, months_paid, total_price, payment_method, payment_status, payment_reference) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->bind_param('isssssidsss', $user_id, $plan_name, $branch, $booking_date, $end_date, $exercise_type, $months_paid, $total_price, $payment_method, $payment_status, $payment_reference);
                if ($stmt->execute()) {
                    $_SESSION['dashboard_notice'] = [
                        'type' => 'payment',
                        'reference' => $payment_reference,
                        'method' => $payment_method,
                        'total' => $total_price,
                    ];
                    $stmt->close();
                    header('Location: dashboard.php');
                    exit;
                }
                $message = 'Your membership plan could not be saved. Please try again.';
                $stmt->close();
            }
        }
    }
}

if ($message !== '') {
    $popup = ['type' => 'message', 'message' => $message];
}

$today_value = $today->format('Y-m-d');
$latest_value = $latest_booking->format('Y-m-d');
$stmt = $conn->prepare('SELECT id, class_name, branch, booking_date, end_date, exercise_type, months_paid, total_price, payment_method, payment_status, payment_reference, created_at FROM bookings WHERE user_id = ? AND booking_date >= CURDATE() ORDER BY booking_date ASC, created_at ASC');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$bookings = $stmt->get_result();
include 'includes/header.php';
?>

<div class="dashboard-heading">
    <div>
        <p class="eyebrow">Member space</p>
        <h2>Good to see you, <?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?>.</h2>
        <p>Your next good workout is just one membership plan away.</p>
    </div>
    <span class="pill"><?= $bookings->num_rows ?> upcoming <?= $bookings->num_rows === 1 ? 'plan' : 'plans' ?></span>
</div>

<?php if (is_array($popup)): ?>
    <dialog class="notice-dialog" id="noticeDialog" aria-labelledby="noticeDialogTitle" aria-describedby="noticeDialogDescription">
        <button type="button" class="dialog-close" data-dialog-close aria-label="Close popup">×</button>
        <div class="dialog-icon" aria-hidden="true"><?= ($popup['type'] ?? '') === 'payment' ? '✓' : 'i' ?></div>
        <p class="eyebrow"><?= ($popup['type'] ?? '') === 'payment' ? 'Next step: payment' : 'Member update' ?></p>
        <h2 id="noticeDialogTitle"><?= ($popup['type'] ?? '') === 'payment' ? 'Your plan is saved' : 'Membership update' ?></h2>
        <?php if (($popup['type'] ?? '') === 'payment' && !empty($popup['reference'])): ?>
            <p id="noticeDialogDescription" class="dialog-copy">Use this unique reference when you pay. Include it in the transfer note if supported.</p>
            <div class="reference-panel">
                <span>Your payment reference</span>
                <strong id="paymentReferenceValue"><?= htmlspecialchars($popup['reference'], ENT_QUOTES, 'UTF-8') ?></strong>
                <button type="button" class="button button-outline copy-reference" id="copyReferenceButton">Copy code</button>
                <span id="copyReferenceStatus" class="copy-status" role="status" aria-live="polite"></span>
            </div>
            <p class="dialog-copy"><strong><?= htmlspecialchars($popup['method'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong> · ETB <?= number_format((float) ($popup['total'] ?? 0), 0) ?>. Ask the gym admin for the verified account details and shortcode before transferring. Send the receipt to the admin; your plan remains pending until payment is confirmed.</p>
        <?php else: ?>
            <p id="noticeDialogDescription" class="dialog-copy"><?= htmlspecialchars($popup['message'] ?? 'Your request has been processed.', ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <button type="button" class="button dialog-done" data-dialog-close>Continue</button>
    </dialog>
<?php endif; ?>

<div class="dashboard-grid">
    <section class="form-card" id="book-class">
        <p class="eyebrow">Make it happen</p>
        <h3>Choose your plan</h3>
        <p class="form-intro">Select your membership, choose your training focus, pick your branch, and set the start date. The total is calculated automatically based on the number of months.</p>
        <form action="dashboard.php#book-class" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="action" value="book">
            <div class="form-group">
                <label for="plan_name">Choose a plan</label>
                <select id="plan_name" name="plan_name" required>
                    <option value="">Select a plan</option>
                    <?php foreach ($plan_prices as $plan_name_option => $plan_price): ?>
                        <option value="<?= htmlspecialchars($plan_name_option, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($plan_name_option . ' — ETB ' . number_format($plan_price), ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="exercise_type">Exercise focus</label>
                <select id="exercise_type" name="exercise_type" required>
                    <option value="">Select a focus</option>
                    <?php foreach ($exercise_types as $exercise_option): ?>
                        <option value="<?= htmlspecialchars($exercise_option, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($exercise_option, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="payment_method">Payment method</label>
                <select id="payment_method" name="payment_method" required>
                    <option value="">Select a payment method</option>
                    <?php foreach ($payment_methods as $payment_option): ?>
                        <option value="<?= htmlspecialchars($payment_option, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($payment_option, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="form-hint">Pay with your chosen method, then send the receipt. The admin will verify it shortly.</p>
                <div class="payment-details" style="margin-top: 14px; padding: 14px 16px; border: 1px solid #d9d9d9; border-radius: 12px; background: #f7f7f7;">
                    <p style="margin: 0 0 10px; font-weight: 700;">How to pay</p>
                    <p style="margin: 0; color: #444;">Ask the gym admin for the current verified account name, account number, and shortcode for your selected payment method before transferring. Each saved plan gets its own payment reference; include it in the transfer note if supported, and send the receipt to the admin. Your membership remains pending until payment is confirmed.</p>
                </div>
            </div>
            <div class="form-group">
                <label for="months_paid">How many months?</label>
                <select id="months_paid" name="months_paid" required>
                    <option value="">Select duration</option>
                    <?php foreach ($months_options as $month_count): ?>
                        <option value="<?= (int) $month_count ?>"><?= (int) $month_count ?> <?= $month_count === 1 ? 'month' : 'months' ?></option>
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
                <label for="booking_date">Membership start date</label>
                <input id="booking_date" type="date" name="booking_date" min="<?= $today_value ?>" max="<?= $latest_value ?>" required>
                <p class="form-hint">The membership runs from this date and ends one month after the selected duration.</p>
            </div>
            <button type="submit" class="button button-full">Save my plan <span aria-hidden="true">→</span></button>
        </form>
    </section>

    <section>
        <p class="eyebrow">On your calendar</p>
        <h3 style="margin: 0 0 18px;">Upcoming plans</h3>
        <?php if ($bookings->num_rows > 0): ?>
            <div class="booking-list">
                <?php while ($booking = $bookings->fetch_assoc()): ?>
                    <article class="booking-item">
                        <div>
                            <h3><?= htmlspecialchars($booking['class_name'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <p><?= htmlspecialchars(date('D, M j, Y', strtotime($booking['booking_date'])), ENT_QUOTES, 'UTF-8') ?> → <?= htmlspecialchars(date('D, M j, Y', strtotime($booking['end_date'] ?? $booking['booking_date'])), ENT_QUOTES, 'UTF-8') ?></p>
                            <p><?= htmlspecialchars($booking['branch'], ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($booking['exercise_type'] ?? 'General Fitness', ENT_QUOTES, 'UTF-8') ?></p>
                            <p><?= (int) ($booking['months_paid'] ?? 1) ?> month(s) · ETB <?= number_format((float) ($booking['total_price'] ?? 0), 0) ?></p>
                            <p>Payment: <?= htmlspecialchars($booking['payment_method'] ?? 'Chapa', ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars(strtoupper($booking['payment_status'] ?? 'PENDING'), ENT_QUOTES, 'UTF-8') ?></p>
                            <?php if (!empty($booking['payment_reference'])): ?>
                                <p><strong>Payment reference:</strong> <?= htmlspecialchars($booking['payment_reference'], ENT_QUOTES, 'UTF-8') ?></p>
                            <?php endif; ?>
                        </div>
                        <form action="dashboard.php" method="POST" onsubmit="return confirm('Cancel this plan?');">
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
                <h3 style="margin-top: 40px;">Your plan list is empty.</h3>
                <p>Choose a membership plan and it’ll show up here.</p>
                <a class="text-link" href="#book-class">Pick a plan <span aria-hidden="true">↓</span></a>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php
$stmt->close();
include 'includes/footer.php';
?>