<?php

require_once '../../core/auth.php';
require_once '../../config/database.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM cultes WHERE id=?");
$stmt->execute([$id]);

$c = $stmt->fetch();

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';

?>

<div class="d-flex">

    

    <div class="container-fluid p-4">

        <h3>Détails du culte</h3>

        <div class="card p-3">

            <p><strong>Thème :</strong> <?= $c['theme'] ?></p>

            <p><strong>Passage :</strong> <?= $c['passage_biblique'] ?></p>

            <p><strong>Orateur :</strong> <?= $c['orateur'] ?></p>

            <p><strong>Interprète :</strong> <?= $c['interprete'] ?></p>

            <p><strong>Hommes :</strong> <?= $c['hommes'] ?></p>

            <p><strong>Femmes :</strong> <?= $c['femmes'] ?></p>

            <p><strong>Offrande :</strong> <?= $c['offrande'] ?> $</p>

            <p><strong>Dîme :</strong> <?= $c['dime'] ?> $</p>

            <p><strong>Sociale :</strong> <?= $c['sociale'] ?> $</p>

            <p><strong>Autres :</strong> <?= $c['autres'] ?> $</p>

            <p><strong>Date :</strong> <?= $c['date_culte'] ?></p>

        </div>

    </div>

</div>

<?php require_once '../../layouts/footer.php'; ?>