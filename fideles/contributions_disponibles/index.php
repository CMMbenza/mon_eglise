<?php

require_once '../auth_visiteurs.php';
require_once '../../config/database.php';

$page_title = "Fonds disponibles";


// =====================================
// FONDS
// =====================================

$fonds = $pdo->query("
    SELECT *
    FROM fonds
    ORDER BY id DESC
");


require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar_fideles.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
.fonds-card {
    border: none;
    border-radius: 18px;
    overflow: hidden;
    transition: .3s;
}

.fonds-card:hover {
    transform: translateY(-4px);
}

.icon-box {
    width: 65px;
    height: 65px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
}
</style>

<div class="container-fluid p-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">

                Fonds disponibles

            </h2>

            <p class="text-muted mb-0">

                Découvrez les campagnes disponibles et participez.

            </p>
        </div>
<a href="souscrire_fonds.php" class="btn btn-primary">

                <i class="bi bi-plus-circle-fill"></i>
                Nouvel engagement

            </a>
    </div>


    <!-- LISTE FONDS -->
    <div class="row g-4">

        <?php foreach($fonds as $f): ?>

        <div class="col-lg-4 col-md-6">

            <div class="card shadow-sm fonds-card h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start mb-3">

                        <div>

                            <h5 class="fw-bold mb-1">

                                <?= htmlspecialchars($f['campagne']) ?>

                            </h5>

                            <small class="text-muted">

                                <?= $f['created_at'] ?>

                            </small>

                        </div>

                        <div class="icon-box bg-success-subtle text-success">

                            <i class="bi bi-bank2"></i>

                        </div>

                    </div>

                    <div class="mb-3">

                        <span class="badge bg-primary">

                            Objectif :
                            <?= htmlspecialchars($f['montant'],2) ?> $

                        </span>

                    </div>

                    <p class="text-muted mb-4">

                        <?= nl2br(htmlspecialchars($f['motif'])) ?>

                    </p>

                    <a href="souscrire_fonds.php?fonds=<?= $f['id'] ?>" class="btn btn-success w-100">

                        <i class="bi bi-plus-circle-fill"></i>
                        Souscrire à ce fonds

                    </a>

                </div>

            </div>

        </div>

        <?php endforeach; ?>

    </div>

</div>

<?php require_once '../../layouts/footer.php'; ?>