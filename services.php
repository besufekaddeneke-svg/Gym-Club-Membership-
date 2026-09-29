<?php
require_once 'config/db.php';
$page_title = 'Memberships & classes | Besufkad Gym';
$result = $conn->query('SELECT id, name, price, duration, description FROM plans ORDER BY price ASC');
include 'includes/header.php';
?>

<section class="page-intro">
    <p class="eyebrow">Find your fit</p>
    <h2>Memberships built around you.</h2>
    <p>Choose how often you want to train. Prices are in Ethiopian birr (ETB) and billed monthly; ask us about joining or create a member account to book classes.</p>
</section>

<?php if ($result && $result->num_rows > 0): ?>
    <div class="card-grid">
        <?php $plan_index = 0; ?>
        <?php while ($plan = $result->fetch_assoc()): ?>
            <article class="card plan-card <?= $plan_index === 1 ? 'featured' : '' ?>">
                <div class="plan-top">
                    <h3><?= htmlspecialchars($plan['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <?php if ($plan_index === 1): ?><span class="pill">Popular</span><?php endif; ?>
                </div>
                <div class="plan-price"><span class="currency">ETB</span> <?= number_format((float) $plan['price'], 0) ?><span> / <?= htmlspecialchars($plan['duration'], ENT_QUOTES, 'UTF-8') ?></span></div>
                <p><?= htmlspecialchars($plan['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                <a class="button <?= $plan_index === 1 ? '' : 'button-outline' ?>" href="contact.php?plan=<?= (int) $plan['id'] ?>">
                    Ask about this package <span aria-hidden="true">→</span>
                </a>
            </article>
            <?php $plan_index++; ?>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="schedule-note">Membership plans are temporarily unavailable. Please contact the club and we’ll help you choose an option.</div>
<?php endif; ?>

<section class="section" style="padding: 50px 0 0;">
    <div class="section-inner cta-panel">
        <div>
            <p class="eyebrow">Payment options</p>
            <h2>Pay securely, then send your receipt.</h2>
            <p>We accept Chapa, Telebirr, and CBE Birr. Contact the gym before transferring to get the current verified account name, account number, and shortcode. After you save a plan, it receives its own payment reference; include it in the transfer note if supported and send your receipt to the admin. Your membership stays pending until payment is verified.</p>
            <p><strong>Never transfer to account details from an unverified source.</strong></p>
        </div>
        <a class="button" href="contact.php">Request payment details <span aria-hidden="true">↗</span></a>
    </div>
</section>

<section class="section" style="padding: 70px 0 0;">
    <div class="section-heading">
        <p class="eyebrow">Move together</p>
        <h2>Find your kind of class.</h2>
        <p>Pick a session when you book. You can manage or cancel upcoming reservations from your member dashboard.</p>
    </div>
    <div class="card-grid">
        <article class="card"><span class="pill">Mind &amp; mobility</span><h3 style="margin-top: 16px;">Yoga &amp; Meditation</h3><p>Build balance, mobility and a calmer finish to your day.</p></article>
        <article class="card"><span class="pill">Conditioning</span><h3 style="margin-top: 16px;">HIIT Body Blast</h3><p>Short, coached intervals with plenty of options to scale.</p></article>
        <article class="card"><span class="pill">Strength</span><h3 style="margin-top: 16px;">Heavy Powerlifting</h3><p>Learn the big lifts and build strength with focused coaching.</p></article>
        <article class="card"><span class="pill">Cardio</span><h3 style="margin-top: 16px;">Spinning Cardio</h3><p>Ride to the rhythm in a high-energy, all-levels session.</p></article>
    </div>
</section>

<section class="section" style="padding: 60px 0 0;">
    <div class="section-inner cta-panel"><div><h2>Not sure where to start?</h2><p>Tell us about your goals and we’ll help you find a first step.</p></div><a class="button" href="contact.php">Talk to the team <span aria-hidden="true">↗</span></a></div>
</section>

<?php include 'includes/footer.php'; ?>