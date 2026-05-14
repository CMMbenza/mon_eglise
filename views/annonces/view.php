<?php

require_once '../../core/auth.php';
require_once '../../config/database.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM annonces WHERE id=?");
$stmt->execute([$id]);

$a = $stmt->fetch();

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';
?>

<div class="container p-4">

    <h3><?= $a['titre'] ?></h3>

    <div class="card p-3">

        <?= nl2br($a['contenu']) ?>

    </div>

</div>

<?php require_once '../../layouts/footer.php'; ?>