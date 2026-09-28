<?php
require_once 'lang.php';
?>
<!DOCTYPE html>
<html lang="<?= $current_lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Besufkad Gym</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/main.js" defer></script>
</head>
<body>
<header>
    <h1>Besufkad <span>Gym</span></h1>
    <nav>
        <a href="index.php"><?= $t['home'] ?></a>
        <a href="about.php"><?= $t['about'] ?></a>
        <a href="services.php"><?= $t['services'] ?></a>
        <a href="contact.php"><?= $t['contact'] ?></a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php"><?= $t['dashboard'] ?></a>
            <a href="logout.php" style="color: #f87171;"><?= $t['logout'] ?> (<?= htmlspecialchars($_SESSION['username']) ?>)</a>
        <?php else: ?>
            <a href="login.php"><?= $t['login'] ?></a>
            <a href="register.php" style="color: var(--primary-color); font-weight: 600;"><?= $t['register'] ?></a>
        <?php endif; ?>
    </nav>
    <div class="controls">
        <button id="themeToggle" class="toggle-btn" title="Toggle Dark/Light Mode">🌓</button>
        <a href="?lang=<?= $t['switch_lang_code'] ?>" class="toggle-btn lang-btn"><?= $t['switch_lang'] ?></a>
    </div>
</header>
<main>