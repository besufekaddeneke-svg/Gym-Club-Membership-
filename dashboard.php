<?php
require_once 'config/db.php';
require_once 'includes/lang.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'My bookings | Besufkad Gym';
$user_id = (int) $_SESSION['user_id'];
$plan_prices = [];
$plan_ids = [];
$plans_result = $conn->query('SELECT id, name, price FROM plans ORDER BY price ASC');
if ($plans_result) {
    while ($plan = $plans_result->fetch_assoc()) {
        $plan_prices[$plan['name']] = (float) $plan['price'];
        $plan_ids[$plan['name']] = (int) $plan['id'];
    }
    $plans_result->free();
}
$exercise_types = ['Cardio', 'Muscle', 'MMA', 'General Fitness'];
$payment_options = [
    'Telebirr' => ['account_label' => $current_lang === 'am' ? 'ቴሌብር ቁጥር' : 'Telebirr number', 'account_number' => '0911223344'],
    'CBE Account' => ['account_label' => $current_lang === 'am' ? 'የንግድ ባንክ መለያ ቁጥር' : 'CBE account number', 'account_number' => '1000123456789'],
    'CBE Birr' => ['account_label' => $current_lang === 'am' ? 'የሲቢኢ ብር ቁጥር' : 'CBE Birr number', 'account_number' => '0911223344'],
    'Awash Bank' => ['account_label' => $current_lang === 'am' ? 'የአዋሽ ባንክ መለያ ቁጥር' : 'Awash account number', 'account_number' => '456541321'],
    'Abyssinia Bank' => ['account_label' => $current_lang === 'am' ? 'የአቢሲኒያ ባንክ መለያ ቁጥር' : 'Abyssinia account number', 'account_number' => '1023488'],
];
$payment_methods = array_keys($payment_options);
$payment_method_labels_am = ['Telebirr' => 'ቴሌብር', 'CBE Account' => 'የንግድ ባንክ መለያ', 'CBE Birr' => 'ሲቢኢ ብር', 'Awash Bank' => 'አዋሽ ባንክ', 'Abyssinia Bank' => 'አቢሲኒያ ባንክ'];
$payment_method_labels = $current_lang === 'am' ? $payment_method_labels_am : array_combine($payment_methods, $payment_methods);
$plan_names_am = ['2 Days / Week' => 'በሳምንት 2 ቀን', '4 Days / Week' => 'በሳምንት 4 ቀን', 'Whole Week' => 'ሙሉ ሳምንት'];
$exercise_labels_am = ['Cardio' => 'ካርዲዮ', 'Muscle' => 'ጡንቻ ግንባታ', 'MMA' => 'ኤምኤምኤ', 'General Fitness' => 'አጠቃላይ የአካል ብቃት'];
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
    } elseif (($_POST['action'] ?? '') === 'submit_payment_code') {
        $booking_id = filter_var($_POST['booking_id'] ?? null, FILTER_VALIDATE_INT);
        $payment_transaction_code_value = $_POST['payment_transaction_code'] ?? '';
        $payment_transaction_code = is_string($payment_transaction_code_value) ? trim($payment_transaction_code_value) : '';

        if (!$booking_id || $payment_transaction_code === '' || strlen($payment_transaction_code) > 100) {
            $message = 'Enter the transaction code exactly as it appears on your payment receipt.';
        } else {
            $check = $conn->prepare('SELECT id FROM bookings WHERE id = ? AND user_id = ? AND booking_date >= CURDATE() AND payment_status = \'pending\' LIMIT 1');
            $check->bind_param('ii', $booking_id, $user_id);
            $check->execute();
            $booking_exists = $check->get_result()->num_rows === 1;
            $check->close();

            if (!$booking_exists) {
                $message = 'We could not find an unpaid upcoming plan for that transaction code.';
            } else {
                $stmt = $conn->prepare('UPDATE bookings SET payment_transaction_code = ? WHERE id = ? AND user_id = ? AND payment_status = \'pending\'');
                $stmt->bind_param('sii', $payment_transaction_code, $booking_id, $user_id);
                if ($stmt->execute()) {
                    $_SESSION['dashboard_notice'] = ['type' => 'message', 'message' => 'Your transaction code was submitted. The admin will compare it with the payment record; your plan remains pending until verified.'];
                    $stmt->close();
                    header('Location: dashboard.php');
                    exit;
                }
                $message = 'We could not save that transaction code. Please try again.';
                $stmt->close();
            }
        }
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
                $plan_id = $plan_ids[$plan_name];
                $payment_status = 'pending';
                $payment_reference = 'BG-' . $today->format('ymd') . '-' . strtoupper(bin2hex(random_bytes(5)));
                $stmt = $conn->prepare('INSERT INTO bookings (user_id, plan_id, class_name, branch, booking_date, end_date, exercise_type, months_paid, total_price, payment_method, payment_status, payment_reference) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->bind_param('iisssssidsss', $user_id, $plan_id, $plan_name, $branch, $booking_date, $end_date, $exercise_type, $months_paid, $total_price, $payment_method, $payment_status, $payment_reference);
                if ($stmt->execute()) {
                    $_SESSION['dashboard_notice'] = [
                        'type' => 'payment',
                        'reference' => $payment_reference,
                        'method' => $payment_method,
                        'account_label' => $payment_options[$payment_method]['account_label'],
                        'account_number' => $payment_options[$payment_method]['account_number'],
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
$stmt = $conn->prepare('SELECT id, class_name, branch, booking_date, end_date, exercise_type, months_paid, total_price, payment_method, payment_status, payment_reference, payment_transaction_code, created_at FROM bookings WHERE user_id = ? AND booking_date >= CURDATE() ORDER BY booking_date ASC, created_at ASC');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$bookings = $stmt->get_result();
include 'includes/header.php';
?>

<div class="dashboard-heading">
    <div>
        <p class="eyebrow"><?= htmlspecialchars($t['member_space'], ENT_QUOTES, 'UTF-8') ?></p>
        <h2><?= htmlspecialchars($t['greeting'], ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?>.</h2>
        <p><?= htmlspecialchars($current_lang === 'am' ? 'ቀጣዩ ጥሩ ስልጠናዎ በአንድ የአባልነት እቅድ ብቻ ይርቃል።' : 'Your next good workout is just one membership plan away.', ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <span class="pill"><?= $bookings->num_rows ?> <?= htmlspecialchars($bookings->num_rows === 1 ? $t['upcoming_plan'] : $t['upcoming_plans'], ENT_QUOTES, 'UTF-8') ?></span>
</div>

<?php $is_payment_popup = is_array($popup) && ($popup['type'] ?? '') === 'payment' && !empty($popup['reference']); ?>
<dialog
    class="notice-dialog"
    id="noticeDialog"
    aria-labelledby="noticeDialogTitle"
    aria-describedby="noticeDialogDescription"
    data-auto-open="<?= is_array($popup) ? 'true' : 'false' ?>"
    data-payment-options="<?= htmlspecialchars(json_encode($payment_options, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8') ?>"
    data-payment-labels="<?= htmlspecialchars(json_encode($payment_method_labels_am, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8') ?>"
    data-translations="<?= htmlspecialchars(json_encode($t, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8') ?>"
>
    <button type="button" class="dialog-close" data-dialog-close aria-label="Close popup">×</button>
        <div class="dialog-icon" id="noticeDialogIcon" aria-hidden="true"><?= $is_payment_popup ? '✓' : '↗' ?></div>
    <p class="eyebrow" id="noticeDialogEyebrow"><?= $is_payment_popup ? ($current_lang === 'am' ? 'ቀጣይ እርምጃ፦ ክፍያ' : 'Next step: payment') : (is_array($popup) ? ($current_lang === 'am' ? 'የአባል ማሳወቂያ' : 'Member update') : $t['payment_destination']) ?></p>
    <h2 id="noticeDialogTitle"><?= $is_payment_popup ? ($current_lang === 'am' ? 'እቅድዎ ተቀምጧል' : 'Your plan is saved') : (is_array($popup) ? ($current_lang === 'am' ? 'የአባልነት ማሳወቂያ' : 'Membership update') : ($current_lang === 'am' ? 'የክፍያ ዘዴ ይምረጡ' : 'Choose a payment method')) ?></h2>
    <p id="noticeDialogDescription" class="dialog-copy"><?php if ($is_payment_popup): ?><?= htmlspecialchars($current_lang === 'am' ? 'ወደ ተመረጠው መለያ ያስተላልፉ። ከተቻለ ይህን ልዩ የክፍያ ኮድ በመልዕክቱ ያካትቱ።' : 'Transfer to the selected account and include this unique payment code in the transfer note if supported.', ENT_QUOTES, 'UTF-8') ?><?php elseif (is_array($popup)): ?><?= htmlspecialchars(tr_message($popup['message'] ?? 'Your request has been processed.'), ENT_QUOTES, 'UTF-8') ?><?php else: ?><?= htmlspecialchars($current_lang === 'am' ? 'የክፍያ ዘዴ ይምረጡ፤ የመለያ መረጃው ይታያል።' : 'Select a payment method to see the account details.', ENT_QUOTES, 'UTF-8') ?><?php endif; ?></p>
    <div class="reference-panel" id="dialogAccountPanel"<?= $is_payment_popup ? '' : ' hidden' ?>>
        <span id="dialogAccountLabel"><?= htmlspecialchars($popup['account_label'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
        <strong id="dialogAccountNumber"><?= htmlspecialchars($popup['account_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
    </div>
    <div class="reference-panel" id="dialogReferencePanel"<?= $is_payment_popup ? '' : ' hidden' ?>>
        <span><?= htmlspecialchars($current_lang === 'am' ? 'ልዩ የክፍያ መለያ ኮድ' : 'Your unique payment code / reference', ENT_QUOTES, 'UTF-8') ?></span>
        <strong id="paymentReferenceValue"><?= htmlspecialchars($popup['reference'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
        <button type="button" class="button button-outline copy-reference" id="copyReferenceButton"><?= htmlspecialchars($current_lang === 'am' ? 'ኮድ ቅዳ' : 'Copy code', ENT_QUOTES, 'UTF-8') ?></button>
        <span id="copyReferenceStatus" class="copy-status" role="status" aria-live="polite"></span>
    </div>
    <p id="dialogPaymentSummary" class="dialog-copy"<?= $is_payment_popup ? '' : ' hidden' ?>>
        <strong id="dialogPaymentMethod"><?= htmlspecialchars($popup['method'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
        · ETB <span id="dialogPaymentTotal"><?= number_format((float) ($popup['total'] ?? 0), 0) ?></span>.
        <?= htmlspecialchars($current_lang === 'am' ? 'ይህ ኮድ እቅድዎን ይለያል። ከክፍያ በኋላ በደረሰኙ ላይ ያለውን የግብይት ኮድ በእቅድ ካርዱ ላይ ያስገቡ እና ደረሰኙን ለአስተዳዳሪው ይላኩ። እስኪረጋገጥ ድረስ እቅዱ በጥበቃ ላይ ይቆያል።' : 'This unique code identifies your plan. After transferring, enter the transaction code from your receipt in the form on your plan card. Send the receipt to the admin; your plan remains pending until verified.', ENT_QUOTES, 'UTF-8') ?>
    </p>
    <button type="button" class="button dialog-done" data-dialog-close><?= htmlspecialchars($current_lang === 'am' ? 'ቀጥል' : 'Continue', ENT_QUOTES, 'UTF-8') ?></button>
</dialog>
<dialog class="notice-dialog" id="cancelDialog" aria-labelledby="cancelDialogTitle" aria-describedby="cancelDialogDescription">
    <div class="dialog-icon" aria-hidden="true">?</div>
    <p class="eyebrow"><?= htmlspecialchars($t['confirm_change'], ENT_QUOTES, 'UTF-8') ?></p>
    <h2 id="cancelDialogTitle"><?= htmlspecialchars($t['cancel_plan_title'], ENT_QUOTES, 'UTF-8') ?></h2>
    <p id="cancelDialogDescription" class="dialog-copy"><?= htmlspecialchars($t['cancel_plan_description'], ENT_QUOTES, 'UTF-8') ?></p>
    <div class="dialog-actions">
        <button type="button" class="button button-outline" data-cancel-dialog-close><?= htmlspecialchars($t['keep_plan'], ENT_QUOTES, 'UTF-8') ?></button>
        <button type="button" class="button" id="confirmCancelButton"><?= htmlspecialchars($current_lang === 'am' ? 'እቅዱን ሰርዝ' : 'Cancel plan', ENT_QUOTES, 'UTF-8') ?></button>
    </div>
</dialog>

<div class="dashboard-grid">
    <section class="form-card" id="book-class">
        <p class="eyebrow"><?= htmlspecialchars($t['make_it_happen'], ENT_QUOTES, 'UTF-8') ?></p>
        <h3><?= htmlspecialchars($t['dashboard_title'], ENT_QUOTES, 'UTF-8') ?></h3>
        <p class="form-intro"><?= htmlspecialchars($t['dashboard_intro'], ENT_QUOTES, 'UTF-8') ?></p>
        <form action="dashboard.php#book-class" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="action" value="book">
            <div class="form-group">
                <label for="plan_name"><?= htmlspecialchars($t['choose_a_plan'], ENT_QUOTES, 'UTF-8') ?></label>
                <select id="plan_name" name="plan_name" required>
                    <option value=""><?= htmlspecialchars($t['select_plan'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php foreach ($plan_prices as $plan_name_option => $plan_price): ?>
                        <option value="<?= htmlspecialchars($plan_name_option, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(($current_lang === 'am' ? ($plan_names_am[$plan_name_option] ?? $plan_name_option) : $plan_name_option) . ' — ETB ' . number_format($plan_price), ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="exercise_type"><?= htmlspecialchars($t['exercise_focus'], ENT_QUOTES, 'UTF-8') ?></label>
                <select id="exercise_type" name="exercise_type" required>
                    <option value=""><?= htmlspecialchars($t['select_focus'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php foreach ($exercise_types as $exercise_option): ?>
                        <option value="<?= htmlspecialchars($exercise_option, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($current_lang === 'am' ? ($exercise_labels_am[$exercise_option] ?? $exercise_option) : $exercise_option, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="payment_method"><?= htmlspecialchars($t['payment_method'], ENT_QUOTES, 'UTF-8') ?></label>
                <select id="payment_method" name="payment_method" required>
                    <option value=""><?= htmlspecialchars($t['select_payment_method'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php foreach ($payment_methods as $payment_option): ?>
                        <option value="<?= htmlspecialchars($payment_option, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($current_lang === 'am' ? ($payment_method_labels_am[$payment_option] ?? $payment_option) : $payment_option, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="form-hint"><?= htmlspecialchars($t['payment_hint'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div class="form-group">
                <label for="months_paid"><?= htmlspecialchars($t['how_many_months'], ENT_QUOTES, 'UTF-8') ?></label>
                <select id="months_paid" name="months_paid" required>
                    <option value=""><?= htmlspecialchars($t['select_duration'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php foreach ($months_options as $month_count): ?>
                        <option value="<?= (int) $month_count ?>"><?= (int) $month_count ?> <?= htmlspecialchars($month_count === 1 ? $t['month_singular'] : $t['months_plural'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="branch"><?= htmlspecialchars($t['choose_branch'], ENT_QUOTES, 'UTF-8') ?></label>
                <select id="branch" name="branch" required>
                    <option value=""><?= htmlspecialchars($t['select_location'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php foreach ($branches as $branch_option): ?>
                        <option value="<?= htmlspecialchars($branch_option, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($branch_option, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="booking_date"><?= htmlspecialchars($t['membership_start_date'], ENT_QUOTES, 'UTF-8') ?></label>
                <input id="booking_date" type="date" name="booking_date" min="<?= $today_value ?>" max="<?= $latest_value ?>" required>
                <p class="form-hint"><?= htmlspecialchars($t['membership_date_hint'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <button type="submit" class="button button-full"><?= htmlspecialchars($t['save_my_plan'], ENT_QUOTES, 'UTF-8') ?> <span aria-hidden="true">→</span></button>
        </form>
    </section>

    <section>
        <p class="eyebrow"><?= htmlspecialchars($t['on_calendar'], ENT_QUOTES, 'UTF-8') ?></p>
        <h3 style="margin: 0 0 18px;"><?= htmlspecialchars($t['upcoming_plans_title'], ENT_QUOTES, 'UTF-8') ?></h3>
        <?php if ($bookings->num_rows > 0): ?>
            <div class="booking-list">
                <?php while ($booking = $bookings->fetch_assoc()): ?>
                    <article class="booking-item">
                        <div>
                            <h3><?= htmlspecialchars($current_lang === 'am' ? ($plan_names_am[$booking['class_name']] ?? $booking['class_name']) : $booking['class_name'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <p><?= htmlspecialchars(date('D, M j, Y', strtotime($booking['booking_date'])), ENT_QUOTES, 'UTF-8') ?> → <?= htmlspecialchars(date('D, M j, Y', strtotime($booking['end_date'] ?? $booking['booking_date'])), ENT_QUOTES, 'UTF-8') ?></p>
                            <p><?= htmlspecialchars($booking['branch'], ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($current_lang === 'am' ? ($exercise_labels_am[$booking['exercise_type'] ?? 'General Fitness'] ?? ($booking['exercise_type'] ?? 'General Fitness')) : ($booking['exercise_type'] ?? 'General Fitness'), ENT_QUOTES, 'UTF-8') ?></p>
                            <p><?= (int) ($booking['months_paid'] ?? 1) ?> <?= htmlspecialchars($t['months_plural'], ENT_QUOTES, 'UTF-8') ?> · ETB <?= number_format((float) ($booking['total_price'] ?? 0), 0) ?></p>
                            <p><?= htmlspecialchars($t['payment_label'], ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($booking['payment_method'] ?? 'Chapa', ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars(strtoupper($booking['payment_status'] ?? 'PENDING'), ENT_QUOTES, 'UTF-8') ?></p>
                            <?php if (!empty($booking['payment_reference'])): ?>
                                <p><strong><?= htmlspecialchars($t['payment_reference_label'], ENT_QUOTES, 'UTF-8') ?></strong> <?= htmlspecialchars($booking['payment_reference'], ENT_QUOTES, 'UTF-8') ?></p>
                            <?php endif; ?>
                            <?php if (($booking['payment_status'] ?? 'pending') === 'pending'): ?>
                                <?php if (!empty($booking['payment_transaction_code'])): ?>
                                    <p><strong><?= htmlspecialchars($t['submitted_receipt_code'], ENT_QUOTES, 'UTF-8') ?></strong> <?= htmlspecialchars($booking['payment_transaction_code'], ENT_QUOTES, 'UTF-8') ?></p>
                                <?php endif; ?>
                                <form class="payment-code-form" action="dashboard.php" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="action" value="submit_payment_code">
                                    <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
                                    <label for="payment_code_<?= (int) $booking['id'] ?>"><?= htmlspecialchars(!empty($booking['payment_transaction_code']) ? $t['update_receipt_code'] : $t['enter_receipt_code'], ENT_QUOTES, 'UTF-8') ?></label>
                                    <input id="payment_code_<?= (int) $booking['id'] ?>" type="text" name="payment_transaction_code" value="<?= htmlspecialchars($booking['payment_transaction_code'] ?? '', ENT_QUOTES, 'UTF-8') ?>" maxlength="100" autocomplete="off" required>
                                    <button class="button button-outline" type="submit"><?= htmlspecialchars(!empty($booking['payment_transaction_code']) ? $t['update_code'] : $t['submit_code_verification'], ENT_QUOTES, 'UTF-8') ?></button>
                                </form>
                            <?php endif; ?>
                        </div>
                        <form action="dashboard.php" method="POST" data-confirm="<?= htmlspecialchars($t['cancel_plan_description'], ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="action" value="cancel">
                            <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
                            <button class="cancel-button" type="submit"><?= htmlspecialchars($t['cancel_plan'], ENT_QUOTES, 'UTF-8') ?></button>
                        </form>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="card booking-empty">
                <span class="feature-icon" aria-hidden="true">◎</span>
                <h3 style="margin-top: 40px;"><?= htmlspecialchars($t['empty_plans_title'], ENT_QUOTES, 'UTF-8') ?></h3>
                <p><?= htmlspecialchars($t['empty_plans_text'], ENT_QUOTES, 'UTF-8') ?></p>
                <a class="text-link" href="#book-class"><?= htmlspecialchars($t['pick_a_plan'], ENT_QUOTES, 'UTF-8') ?> <span aria-hidden="true">↓</span></a>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php
$stmt->close();
include 'includes/footer.php';
?>