<?php
require_once __DIR__ . '/lang.php';
$current_page = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
$current_uri = $_SERVER['REQUEST_URI'] ?? 'index.php';
$current_path = parse_url($current_uri, PHP_URL_PATH) ?: 'index.php';
$current_query = [];
parse_str(parse_url($current_uri, PHP_URL_QUERY) ?? '', $current_query);
$current_query['lang'] = $t['switch_lang_code'];
$language_url = $current_path . '?' . http_build_query($current_query);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($current_lang, ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5f6f2">
    <meta name="description" content="Besufkad Gym — expert coaching, motivating group classes and flexible membership in Addis Ababa.">
    <title><?= htmlspecialchars($page_title ?? 'Besufkad Gym | Train for your life', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/main.js" defer></script>
</head>
<body>
<header class="site-header">
    <a class="brand" href="index.php" aria-label="Besufkad Gym home">
        <span class="brand-mark">B</span>
        <span class="brand-name">BESUFKAD <span>GYM</span></span>
    </a>
    <nav class="primary-nav" id="primaryNav" aria-label="Main navigation">
        <a class="<?= $current_page === 'index.php' ? 'active' : '' ?>" href="index.php"><?= htmlspecialchars($t['home'], ENT_QUOTES, 'UTF-8') ?></a>
        <a class="<?= $current_page === 'services.php' ? 'active' : '' ?>" href="services.php"><?= htmlspecialchars($t['nav_membership'], ENT_QUOTES, 'UTF-8') ?></a>
        <a class="<?= $current_page === 'about.php' ? 'active' : '' ?>" href="about.php"><?= htmlspecialchars($t['nav_our_gym'], ENT_QUOTES, 'UTF-8') ?></a>
        <a class="<?= $current_page === 'contact.php' ? 'active' : '' ?>" href="contact.php"><?= htmlspecialchars($t['nav_contact'], ENT_QUOTES, 'UTF-8') ?></a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a class="<?= $current_page === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php"><?= htmlspecialchars($t['nav_my_bookings'], ENT_QUOTES, 'UTF-8') ?></a>
            <a href="logout.php"><?= htmlspecialchars($t['nav_sign_out'], ENT_QUOTES, 'UTF-8') ?></a>
        <?php else: ?>
            <a class="<?= $current_page === 'login.php' ? 'active' : '' ?>" href="login.php"><?= htmlspecialchars($t['nav_sign_in'], ENT_QUOTES, 'UTF-8') ?></a>
        <?php endif; ?>
    </nav>
    <div class="header-actions">
        <a class="language-toggle" href="<?= htmlspecialchars($language_url, ENT_QUOTES, 'UTF-8') ?>" lang="<?= htmlspecialchars($t['switch_lang_code'], ENT_QUOTES, 'UTF-8') ?>" aria-label="Switch language"><?= htmlspecialchars($t['switch_lang'], ENT_QUOTES, 'UTF-8') ?></a>
        <button id="themeToggle" class="icon-button" type="button" aria-label="Toggle dark mode" title="Toggle dark mode">◐</button>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a class="button button-small register-link" href="register.php"><?= htmlspecialchars($t['join_club'], ENT_QUOTES, 'UTF-8') ?> <span aria-hidden="true">↗</span></a>
        <?php else: ?>
            <span class="pill"><?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?></span>
        <?php endif; ?>
        <button id="menuToggle" class="icon-button menu-toggle" type="button" aria-label="Open navigation" aria-expanded="false" aria-controls="primaryNav">☰</button>
    </div>
</header>
<main class="<?= $current_page === 'index.php' ? 'home-main' : '' ?>">