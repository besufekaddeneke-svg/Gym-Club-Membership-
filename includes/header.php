<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
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
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="services.php">Services</a>
        <a href="contact.php">Contact</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php" style="color: #f87171;">Logout (<?= htmlspecialchars($_SESSION['username']) ?>)</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php" style="color: var(--primary-color); font-weight: 600;">Register</a>
        <?php endif; ?>
    </nav>
</header>
<main>