<?php
require_once 'config/db.php';
$page_title = 'Memberships & classes | Besufkad Gym';
$result = $conn->query('SELECT id, name, price, duration, description FROM plans ORDER BY price ASC');
include 'includes/header.php';
?>

<section class="page-intro">
    <p class="eyebrow">Find your fit</p>
    <h2>Memberships built around you.</h2>
    <p>Compare local options and get in touch when you’re ready to join. Create a member account to book a class at the branch that works for you.</p>
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
                <div class="plan-price">$<?= number_format((float) $plan['price'], 2) ?><span> / <?= htmlspecialchars($plan['duration'], ENT_QUOTES, 'UTF-8') ?></span></div>
                <p><?= htmlspecialchars($plan['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                <a class="button <?= $plan_index === 1 ? '' : 'button-outline' ?>" href="<?= isset($_SESSION['user_id']) ? 'dashboard.php#book-class' : 'register.php' ?>">
                    <?= isset($_SESSION['user_id']) ? 'Book a class' : 'Create a member account' ?> <span aria-hidden="true">→</span>
                </a>
            </article>
            <?php $plan_index++; ?>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="schedule-note">Membership plans are temporarily unavailable. Please contact the club and we’ll help you choose an option.</div>
<?php endif; ?>

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