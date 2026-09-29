<?php
$page_title = 'Besufkad Gym | Train for your life';
include 'includes/header.php';
?>

<section class="hero">
    <div class="hero-content">
        <p class="eyebrow"><?= htmlspecialchars($t['home_eyebrow'], ENT_QUOTES, 'UTF-8') ?></p>
        <h1><?= htmlspecialchars($t['home_title'], ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="hero-copy"><?= htmlspecialchars($t['home_intro'], ENT_QUOTES, 'UTF-8') ?></p>
        <div class="hero-actions">
            <a class="button" href="services.php"><?= htmlspecialchars($t['explore_memberships'], ENT_QUOTES, 'UTF-8') ?> <span aria-hidden="true">↗</span></a>
            <a class="button button-outline" href="contact.php"><?= htmlspecialchars($t['plan_first_visit'], ENT_QUOTES, 'UTF-8') ?></a>
        </div>
        <p class="hero-note"><strong><?= htmlspecialchars($t['new_here'], ENT_QUOTES, 'UTF-8') ?></strong> <?= htmlspecialchars($t['first_visit_note'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>
</section>

<div class="stats-strip" aria-label="Gym highlights">
    <div class="stat"><strong>4</strong><span><?= htmlspecialchars($t['local_branches'], ENT_QUOTES, 'UTF-8') ?></span></div>
    <div class="stat"><strong><?= htmlspecialchars($t['classes_title'] === '🔥 Dynamic Classes' ? 'All levels' : 'ሁሉም ደረጃዎች', ENT_QUOTES, 'UTF-8') ?></strong><span><?= htmlspecialchars($t['beginners_welcome'], ENT_QUOTES, 'UTF-8') ?></span></div>
    <div class="stat"><strong><?= htmlspecialchars($current_lang === 'am' ? 'በየቀኑ' : 'Every day', ENT_QUOTES, 'UTF-8') ?></strong><span><?= htmlspecialchars($t['make_time_for_you'], ENT_QUOTES, 'UTF-8') ?></span></div>
</div>

<section class="section">
    <div class="section-inner">
        <div class="section-heading">
            <p class="eyebrow"><?= htmlspecialchars($current_lang === 'am' ? 'ትንሽ ከሁሉም' : 'A little bit of everything', ENT_QUOTES, 'UTF-8') ?></p>
            <h2><?= htmlspecialchars($t['home_section_title'], ENT_QUOTES, 'UTF-8') ?></h2>
            <p><?= htmlspecialchars($t['home_section_intro'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="card-grid">
            <article class="card feature-card"><span class="feature-icon" aria-hidden="true">↗</span><h3><?= htmlspecialchars($t['feature_strength'], ENT_QUOTES, 'UTF-8') ?></h3><p><?= htmlspecialchars($t['feature_strength_desc'], ENT_QUOTES, 'UTF-8') ?></p></article>
            <article class="card feature-card"><span class="feature-icon" aria-hidden="true">◎</span><h3><?= htmlspecialchars($t['feature_classes'], ENT_QUOTES, 'UTF-8') ?></h3><p><?= htmlspecialchars($t['feature_classes_desc'], ENT_QUOTES, 'UTF-8') ?></p></article>
            <article class="card feature-card"><span class="feature-icon" aria-hidden="true">✳</span><h3><?= htmlspecialchars($t['feature_coaching'], ENT_QUOTES, 'UTF-8') ?></h3><p><?= htmlspecialchars($t['feature_coaching_desc'], ENT_QUOTES, 'UTF-8') ?></p></article>
        </div>
    </div>
</section>

<section class="section" style="padding-top: 15px;">
    <div class="section-inner split-panel">
        <div class="split-image" role="img" aria-label="Training floor with gym equipment"></div>
        <div>
            <p class="eyebrow"><?= htmlspecialchars($current_lang === 'am' ? 'ፍጹም የሆነ ልምድ አያስፈልግም' : 'No perfect routine required', ENT_QUOTES, 'UTF-8') ?></p>
            <h2><?= htmlspecialchars($current_lang === 'am' ? 'እንዳሉ ይምጡ። ቀሪውን እኛ እንረዳለን።' : 'Show up as you are. We’ll take it from there.', ENT_QUOTES, 'UTF-8') ?></h2>
            <p><?= htmlspecialchars($current_lang === 'am' ? 'የአካል ብቃት ለሁሉም አንድ አይነት አይደለም። የመጀመሪያ ስልጠናዎ ይሁን መቶኛዎ፣ የሚመች ልምድ እንዲያገኙ ቡድናችን ይረዳዎታል።' : 'Fitness isn’t one-size-fits-all. Our team makes it easier to find a routine that feels good, whether it’s your first workout or your hundredth.' , ENT_QUOTES, 'UTF-8') ?></p>
            <ul class="check-list">
                <li><?= htmlspecialchars($current_lang === 'am' ? 'ለተለያዩ ግቦች የሚስማሙ ተለዋዋጭ አባልነቶች' : 'Flexible memberships, with options for different goals', ENT_QUOTES, 'UTF-8') ?></li>
                <li><?= htmlspecialchars($current_lang === 'am' ? 'አቀባበል ያላቸው የቡድን ክፍሎች እና ተግባራዊ ምክር' : 'Welcoming group classes and practical coaching', ENT_QUOTES, 'UTF-8') ?></li>
                <li><?= htmlspecialchars($current_lang === 'am' ? 'በአዲስ አበባ ያሉ አራት ምቹ ቦታዎች' : 'Four convenient locations around Addis Ababa', ENT_QUOTES, 'UTF-8') ?></li>
            </ul>
            <a class="button button-dark" href="about.php"><?= htmlspecialchars($current_lang === 'am' ? 'ስለ እኛ ይወቁ' : 'Get to know us', ENT_QUOTES, 'UTF-8') ?> <span aria-hidden="true">→</span></a>
        </div>
    </div>
</section>

<section class="section" style="padding-top: 15px;">
    <div class="section-inner cta-panel">
        <div><h2><?= htmlspecialchars($current_lang === 'am' ? 'ቀጣዩ ጉዞዎ በአንድ ጉብኝት ሊጀምር ይችላል።' : 'Your next chapter can start with one visit.', ENT_QUOTES, 'UTF-8') ?></h2><p><?= htmlspecialchars($current_lang === 'am' ? 'የሚፈልጉትን ይንገሩን፤ ለመጀመር ትክክለኛውን ቦታ እንረዳዎታለን።' : 'Tell us what you’re looking for and we’ll help you find the right place to begin.', ENT_QUOTES, 'UTF-8') ?></p></div>
        <a class="button" href="contact.php"><?= htmlspecialchars($current_lang === 'am' ? 'ሊጎበኙን ይምጡ' : 'Come meet us', ENT_QUOTES, 'UTF-8') ?> <span aria-hidden="true">↗</span></a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>