<?php
$page_title = 'Our gym | Besufkad Gym';
include 'includes/header.php';
?>

<section class="split-panel">
    <div>
        <p class="eyebrow"><?= htmlspecialchars($current_lang === 'am' ? 'ከስልጠና በላይ' : 'More than a workout', ENT_QUOTES, 'UTF-8') ?></p>
        <h2><?= htmlspecialchars($t['our_gym_title'], ENT_QUOTES, 'UTF-8') ?></h2>
        <p><?= htmlspecialchars($t['about_gym_desc'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><?= htmlspecialchars($t['about_progress_desc'], ENT_QUOTES, 'UTF-8') ?></p>
        <a class="button button-dark" href="contact.php"><?= htmlspecialchars($current_lang === 'am' ? 'ሰላም ሊሉን ይምጡ' : 'Come say hello', ENT_QUOTES, 'UTF-8') ?> <span aria-hidden="true">↗</span></a>
    </div>
    <div class="split-image" role="img" aria-label="A bright, welcoming gym training floor"></div>
</section>

<section class="section student-section" aria-labelledby="student-heading">
    <div class="section-heading">
        <p class="eyebrow"><?= htmlspecialchars($t['about_me'], ENT_QUOTES, 'UTF-8') ?></p>
        <h2 id="student-heading"><?= htmlspecialchars($t['about_me'], ENT_QUOTES, 'UTF-8') ?></h2>
        <p><?= htmlspecialchars($t['about_me_intro'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <div class="student-profile">
        <p><strong><?= htmlspecialchars($t['full_name'], ENT_QUOTES, 'UTF-8') ?></strong><span>Besufkad Deneke</span></p>
        <p><strong><?= htmlspecialchars($t['student_id'], ENT_QUOTES, 'UTF-8') ?></strong><span>006/2016</span></p>
        <p><strong><?= htmlspecialchars($t['department'], ENT_QUOTES, 'UTF-8') ?></strong><span><?= htmlspecialchars($current_lang === 'am' ? 'ኮምፒውተር ሳይንስ' : 'Computer Science', ENT_QUOTES, 'UTF-8') ?></span></p>
        <p class="student-bio"><strong><?= htmlspecialchars($t['personal_intro'], ENT_QUOTES, 'UTF-8') ?></strong><span><?= htmlspecialchars($t['student_bio'], ENT_QUOTES, 'UTF-8') ?></span></p>
    </div>
</section>

<div class="about-values">
    <article class="about-value"><strong><?= htmlspecialchars($current_lang === 'am' ? 'ሰዎች ከውጤት በፊት' : 'People before PRs', ENT_QUOTES, 'UTF-8') ?></strong><p><?= htmlspecialchars($current_lang === 'am' ? 'ወዳጃዊ አቀባበል እና ጥሩ መመሪያ እንደ መሳሪያዎቹ አስፈላጊ ናቸው።' : 'A friendly hello and thoughtful guidance matter as much as the equipment.', ENT_QUOTES, 'UTF-8') ?></p></article>
    <article class="about-value"><strong><?= htmlspecialchars($current_lang === 'am' ? 'እድገትዎ በራስዎ መንገድ' : 'Progress, your way', ENT_QUOTES, 'UTF-8') ?></strong><p><?= htmlspecialchars($current_lang === 'am' ? 'ከእርስዎ ደረጃ ይጀምሩ። ክፍሎቻችን ለተለያዩ የልምድ ደረጃዎች ተዘጋጅተዋል።' : 'Start where you are. Our classes and coaching are designed for different experience levels.', ENT_QUOTES, 'UTF-8') ?></p></article>
    <article class="about-value"><strong><?= htmlspecialchars($current_lang === 'am' ? 'የሰፈርዎ አካል' : 'Part of the neighborhood', ENT_QUOTES, 'UTF-8') ?></strong><p><?= htmlspecialchars($current_lang === 'am' ? 'አራት ቅርንጫፎቻችን ስልጠናን ከቀን መርሃ ግብርዎ ጋር ማስማማት ያስችላሉ።' : 'With four branches, making time for movement can fit around your day.', ENT_QUOTES, 'UTF-8') ?></p></article>
</div>

<section class="section" style="padding: 70px 0 0;">
    <div class="section-heading"><p class="eyebrow"><?= htmlspecialchars($current_lang === 'am' ? 'በአቅራቢያዎ ያግኙን' : 'Meet us nearby', ENT_QUOTES, 'UTF-8') ?></p><h2><?= htmlspecialchars($current_lang === 'am' ? 'ለስልጠና አራት ቦታዎች።' : 'Four places to get moving.', ENT_QUOTES, 'UTF-8') ?></h2><p><?= htmlspecialchars($current_lang === 'am' ? 'ከመርሃ ግብርዎ ጋር የሚስማማውን ቅርንጫፍ ይምረጡ።' : 'Choose the branch that fits your routine. For directions or a first visit, send our team a note.', ENT_QUOTES, 'UTF-8') ?></p></div>
    <div class="card-grid">
        <article class="card"><span class="feature-icon" aria-hidden="true">⌖</span><h3 style="margin-top: 34px;">Bole Atlas</h3><p>Main Branch, Atlas Road</p></article>
        <article class="card"><span class="feature-icon" aria-hidden="true">⌖</span><h3 style="margin-top: 34px;">Haya Hulet</h3><p>Next to Mazoria</p></article>
        <article class="card"><span class="feature-icon" aria-hidden="true">⌖</span><h3 style="margin-top: 34px;">Semit</h3><p>Safari Avenue</p></article>
        <article class="card"><span class="feature-icon" aria-hidden="true">⌖</span><h3 style="margin-top: 34px;">Bisrate Gebreal</h3><p>Old Airport Road</p></article>
    </div>
</section>

<?php include 'includes/footer.php'; ?>