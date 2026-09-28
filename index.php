<?php include 'includes/header.php'; ?>

<div class="hero">
    <h2><?= $t['welcome'] ?></h2>
    <p><?= $t['subtitle'] ?></p>
</div>

<h2><?= $t['why_choose'] ?></h2>
<p><?= $t['why_desc'] ?></p>

<div class="card-grid">
    <div class="card">
        <h3><?= $t['equip_title'] ?></h3>
        <p style="text-align: left;"><?= $t['equip_desc'] ?></p>
    </div>
    <div class="card">
        <h3><?= $t['trainers_title'] ?></h3>
        <p style="text-align: left;"><?= $t['trainers_desc'] ?></p>
    </div>
    <div class="card">
        <h3><?= $t['classes_title'] ?></h3>
        <p style="text-align: left;"><?= $t['classes_desc'] ?></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>