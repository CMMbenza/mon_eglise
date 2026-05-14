<?php
require_once '../../core/auth.php';
require_once '../../config/database.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM depenses WHERE id=?");
$stmt->execute([$id]);
$d = $stmt->fetch();

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';
?>

<div class="container p-4">

    <h3>Détail Dépense</h3>

    <p>Motif: <?= $d['motif'] ?></p>
    <p>Montant: <?= $d['montant'] ?></p>
    <p>Date: <?= $d['date_depense'] ?></p>

</div>

<?php require_once '../../layouts/footer.php'; ?>