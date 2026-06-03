<?php

require_once '../../core/auth.php';
require_once '../../config/database.php';

$id = (int)($_GET['id'] ?? 0);


// ======================================
// RECUPERATION
// ======================================

$stmt = $pdo->prepare("
    SELECT *
    FROM cultes
    WHERE id = ?
");

$stmt->execute([$id]);

$c = $stmt->fetch();

if(!$c){
    die("Culte introuvable");
}

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';

?>

<div class="container-fluid p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="fw-bold mb-1">
                Détails du culte
            </h3>

            <p class="text-muted mb-0">
                Informations complètes du culte enregistré
            </p>

        </div>

        <a href="index.php" class="btn btn-secondary">

            <i class="bi bi-arrow-left"></i>
            Retour

        </a>

    </div>


    <div class="row">

        <div class="col-lg-8">

            <div class="card shadow-sm border-0 rounded-4 mb-4">

                <div class="card-header bg-dark text-white py-3">

                    <h5 class="mb-0">

                        <i class="bi bi-journal-bookmark-fill"></i>
                        Informations générales

                    </h5>

                </div>

                <div class="card-body p-4">

                    <div class="row g-4">

                        <div class="col-md-6">

                            <div class="border rounded-3 p-3 h-100">

                                <small class="text-muted d-block mb-1">
                                    Thème
                                </small>

                                <strong>
                                    <?= htmlspecialchars($c['theme']) ?>
                                </strong>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="border rounded-3 p-3 h-100">

                                <small class="text-muted d-block mb-1">
                                    Passage biblique
                                </small>

                                <strong>
                                    <?= htmlspecialchars($c['passage_biblique']) ?>
                                </strong>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="border rounded-3 p-3 h-100">

                                <small class="text-muted d-block mb-1">
                                    Orateur
                                </small>

                                <strong>
                                    <?= htmlspecialchars($c['orateur']) ?>
                                </strong>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="border rounded-3 p-3 h-100">

                                <small class="text-muted d-block mb-1">
                                    Interprète
                                </small>

                                <strong>
                                    <?= htmlspecialchars($c['interprete']) ?>
                                </strong>

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="border rounded-3 p-3 text-center">

                                <small class="text-muted d-block">
                                    Hommes
                                </small>

                                <h4 class="fw-bold text-primary mb-0">
                                    <?= $c['hommes'] ?>
                                </h4>

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="border rounded-3 p-3 text-center">

                                <small class="text-muted d-block">
                                    Femmes
                                </small>

                                <h4 class="fw-bold text-danger mb-0">
                                    <?= $c['femmes'] ?>
                                </h4>

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="border rounded-3 p-3 text-center">

                                <small class="text-muted d-block">
                                    Total participants
                                </small>

                                <h4 class="fw-bold text-success mb-0">
                                    <?= $c['hommes'] + $c['femmes'] ?>
                                </h4>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="border rounded-3 p-3 h-100">

                                <small class="text-muted d-block mb-1">
                                    Date du culte
                                </small>

                                <strong>
                                    <?= $c['date_culte'] ?>
                                </strong>

                            </div>

                        </div>


                        <!-- <div class="col-md-6">

                            <div class="border rounded-3 p-3 h-100">

                                <small class="text-muted d-block mb-1">
                                    Devise
                                </small>

                                <strong>
                                    <?= $c['devise'] ?>
                                </strong>

                            </div>

                        </div> -->

                    </div>

                </div>

            </div>

        </div>



        <!-- FINANCES -->

        <div class="col-lg-4">

            <div class="card shadow-sm border-0 rounded-4 mb-4">

                <div class="card-header bg-success text-white py-3">

                    <h5 class="mb-0">

                        <i class="bi bi-cash-stack"></i>
                        Finances du culte

                    </h5>

                </div>

                <div class="card-body p-4">

                    <div class="mb-4">

                        <small class="text-muted d-block mb-1">
                            Offrande + Dîme
                        </small>

                        <h4 class="fw-bold text-success">

                            <?= number_format($c['offrande_dime'],2) ?>
                            <?= $c['devise'] ?>

                        </h4>

                    </div>


                    <div class="mb-4">

                        <small class="text-muted d-block mb-1">
                            Sociale
                        </small>

                        <h4 class="fw-bold text-primary">

                            <?= number_format($c['sociale'],2) ?>
                            <?= $c['devise'] ?>

                        </h4>

                    </div>


                    <div class="mb-4">

                        <small class="text-muted d-block mb-1">
                            Autres entrées
                        </small>

                        <h4 class="fw-bold text-warning">

                            <?= number_format($c['autres'],2) ?>
                            <?= $c['devise'] ?>

                        </h4>

                    </div>


                    <?php if(!empty($c['description_autres'])): ?>

                    <hr>

                    <div>

                        <small class="text-muted d-block mb-2">
                            Description autres entrées
                        </small>

                        <div class="bg-light rounded-3 p-3">

                            <?= nl2br(htmlspecialchars($c['description_autres'])) ?>

                        </div>

                    </div>

                    <?php endif; ?>


                    <hr>

                    <?php
                    $total =
                        $c['offrande_dime'] +
                        $c['sociale'] +
                        $c['autres'];
                    ?>

                    <div class="text-center">

                        <small class="text-muted">
                            Total général
                        </small>

                        <h2 class="fw-bold text-dark mt-2">

                            <?= number_format($total,2) ?>
                            <?= $c['devise'] ?>

                        </h2>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once '../../layouts/footer.php'; ?>