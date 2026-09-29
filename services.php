<?php
require_once 'config/db.php';
$page_title = 'Memberships & classes | Besufkad Gym';
$result = $conn->query('SELECT id, name, price, duration, description FROM plans ORDER BY price ASC');
$plan_names_am = ['2 Days / Week' => 'በሳምንት 2 ቀን', '4 Days / Week' => 'በሳምንት 4 ቀን', 'Whole Week' => 'ሙሉ ሳምንት'];
$plan_descriptions_am = [
    '2 Days / Week' => 'በየሳምንቱ ለሁለት ቀናት ጂሙን ይጠቀሙ። ቋሚ የስልጠና ልምድ ለመገንባት ጥሩ አማራጭ ነው።',
    '4 Days / Week' => 'ጥንካሬን፣ ተንቀሳቃሽነትን እና ካርዲዮን በማመጣጠን በየሳምንቱ ለአራት ቀናት ይሰለጥኑ።',
    'Whole Week' => 'ለሙሉ ሳምንት ተለዋዋጭ የስልጠና መርሃ ግብር የሚፈልጉ አባላት የሰባት ቀን መግቢያ ያገኛሉ።',
];
include 'includes/header.php';
?>

<section class="page-intro">
    <p class="eyebrow"><?= htmlspecialchars($t['find_your_fit'], ENT_QUOTES, 'UTF-8') ?></p>
    <h2><?= htmlspecialchars($t['memberships_title'], ENT_QUOTES, 'UTF-8') ?></h2>
    <p><?= htmlspecialchars($t['membership_intro'], ENT_QUOTES, 'UTF-8') ?></p>
</section>

<?php if ($result && $result->num_rows > 0): ?>
    <div class="card-grid">
        <?php $plan_index = 0; ?>
        <?php while ($plan = $result->fetch_assoc()): ?>
            <article class="card plan-card">
                <div class="plan-top">
                    <h3><?= htmlspecialchars($current_lang === 'am' ? ($plan_names_am[$plan['name']] ?? $plan['name']) : $plan['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <?php if ($plan_index === 1): ?><span class="pill"><?= htmlspecialchars($t['popular'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                </div>
                <div class="plan-price"><span class="currency">ETB</span> <?= number_format((float) $plan['price'], 0) ?><span> / <?= htmlspecialchars($current_lang === 'am' ? $t['monthly'] : $plan['duration'], ENT_QUOTES, 'UTF-8') ?></span></div>
                <p><?= htmlspecialchars($current_lang === 'am' ? ($plan_descriptions_am[$plan['name']] ?? ($plan['description'] ?? '')) : ($plan['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                <a class="button button-outline" href="contact.php?plan=<?= (int) $plan['id'] ?>">
                    <?= htmlspecialchars($t['ask_package'], ENT_QUOTES, 'UTF-8') ?> <span aria-hidden="true">→</span>
                </a>
            </article>
            <?php $plan_index++; ?>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="schedule-note"><?= htmlspecialchars($t['membership_unavailable'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<section class="section" style="padding: 50px 0 0;">
    <div class="section-inner cta-panel">
        <div>
            <p class="eyebrow"><?= htmlspecialchars($t['payment_options'], ENT_QUOTES, 'UTF-8') ?></p>
            <h2><?= htmlspecialchars($t['payment_receipt_title'], ENT_QUOTES, 'UTF-8') ?></h2>
            <p><?= htmlspecialchars($t['payment_public_desc'], ENT_QUOTES, 'UTF-8') ?></p>
            <ul class="payment-account-list">
                <li><strong><?= htmlspecialchars($t['telebirr'], ENT_QUOTES, 'UTF-8') ?>:</strong> 0911223344</li>
                <li><strong><?= htmlspecialchars($t['cbe_account'], ENT_QUOTES, 'UTF-8') ?>:</strong> 1000123456789</li>
                <li><strong><?= htmlspecialchars($t['cbe_birr'], ENT_QUOTES, 'UTF-8') ?>:</strong> 0911223344</li>
                <li><strong><?= htmlspecialchars($t['awash_bank'], ENT_QUOTES, 'UTF-8') ?>:</strong> 456541321</li>
                <li><strong><?= htmlspecialchars($t['abyssinia_bank'], ENT_QUOTES, 'UTF-8') ?>:</strong> 1023488</li>
            </ul>
            <p><?= htmlspecialchars($t['payment_transaction_note'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <a class="button" href="dashboard.php#book-class"><?= htmlspecialchars($t['choose_plan'], ENT_QUOTES, 'UTF-8') ?> <span aria-hidden="true">↗</span></a>
    </div>
</section>

<section class="section" style="padding: 70px 0 0;">
    <div class="section-heading">
        <p class="eyebrow"><?= htmlspecialchars($t['move_together'], ENT_QUOTES, 'UTF-8') ?></p>
        <h2><?= htmlspecialchars($t['find_class'], ENT_QUOTES, 'UTF-8') ?></h2>
        <p><?= htmlspecialchars($t['class_intro'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <div class="card-grid">
        <article class="card"><span class="pill"><?= htmlspecialchars($t['mind_mobility'], ENT_QUOTES, 'UTF-8') ?></span><h3 style="margin-top: 16px;"><?= htmlspecialchars($t['yoga_meditation'], ENT_QUOTES, 'UTF-8') ?></h3><p><?= htmlspecialchars($current_lang === 'am' ? 'ሚዛንን፣ ተንቀሳቃሽነትን እና እረፍትን ያጎልብቱ።' : 'Build balance, mobility and a calmer finish to your day.', ENT_QUOTES, 'UTF-8') ?></p></article>
        <article class="card"><span class="pill"><?= htmlspecialchars($t['conditioning'], ENT_QUOTES, 'UTF-8') ?></span><h3 style="margin-top: 16px;"><?= htmlspecialchars($t['hiit_body_blast'], ENT_QUOTES, 'UTF-8') ?></h3><p><?= htmlspecialchars($current_lang === 'am' ? 'ለሁሉም ደረጃዎች የሚስማሙ አጭር የስልጠና ዙሮች።' : 'Short, coached intervals with plenty of options to scale.', ENT_QUOTES, 'UTF-8') ?></p></article>
        <article class="card"><span class="pill"><?= htmlspecialchars($t['strength'], ENT_QUOTES, 'UTF-8') ?></span><h3 style="margin-top: 16px;"><?= htmlspecialchars($t['heavy_powerlifting'], ENT_QUOTES, 'UTF-8') ?></h3><p><?= htmlspecialchars($current_lang === 'am' ? 'ዋና የክብደት ማንሳት ልምምዶችን ከአሰልጣኝ ጋር ይማሩ።' : 'Learn the big lifts and build strength with focused coaching.', ENT_QUOTES, 'UTF-8') ?></p></article>
        <article class="card"><span class="pill"><?= htmlspecialchars($t['cardio'], ENT_QUOTES, 'UTF-8') ?></span><h3 style="margin-top: 16px;"><?= htmlspecialchars($t['spinning_cardio'], ENT_QUOTES, 'UTF-8') ?></h3><p><?= htmlspecialchars($current_lang === 'am' ? 'ለሁሉም ደረጃዎች የሚሆን ከፍተኛ ኃይል ያለው የብስክሌት ስልጠና።' : 'Ride to the rhythm in a high-energy, all-levels session.', ENT_QUOTES, 'UTF-8') ?></p></article>
    </div>
</section>

<section class="section" style="padding: 60px 0 0;">
    <div class="section-inner cta-panel"><div><h2><?= htmlspecialchars($t['not_sure_start'], ENT_QUOTES, 'UTF-8') ?></h2><p><?= htmlspecialchars($current_lang === 'am' ? 'ግቦችዎን ይንገሩን፤ ለመጀመር እንረዳዎታለን።' : 'Tell us about your goals and we’ll help you find a first step.', ENT_QUOTES, 'UTF-8') ?></p></div><a class="button" href="contact.php"><?= htmlspecialchars($t['talk_team'], ENT_QUOTES, 'UTF-8') ?> <span aria-hidden="true">↗</span></a></div>
</section>

<?php include 'includes/footer.php'; ?>