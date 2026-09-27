<?php 
require_once 'config/db.php';
include 'includes/header.php'; 

$sql = "SELECT * FROM plans";
$result = $conn->query($sql);
?>

<h2>Membership Plans & Services</h2>
<div class="card-grid">
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="card">
                <h3><?= htmlspecialchars($row['name']) ?></h3>
                <p><strong>Price:</strong> $<?= htmlspecialchars($row['price']) ?> / <?= htmlspecialchars($row['duration']) ?></p>
                <p><?= htmlspecialchars($row['description']) ?></p>
                <br>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="btn">Book Class</a>
                <?php else: ?>
                    <a href="login.php" class="btn">Login to Join</a>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>