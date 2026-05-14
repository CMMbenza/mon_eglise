<?php

require_once '../auth_visiteurs.php';
require_once '../../config/database.php';

$page_title = "Cultes";


// =====================================
// CULTES
// =====================================

$cultes = $pdo->query("
    SELECT
        theme,
        passage_biblique,
        orateur,
        interprete,
        hommes,
        femmes,
        date_culte
    FROM cultes
    ORDER BY date_culte DESC
");


require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar_fideles.php';
?>

<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
.culte-card {
    border: none;
    border-radius: 18px;
    overflow: hidden;
    transition: .3s;
}

.culte-card:hover {
    transform: translateY(-3px);
}

.info-box {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 12px;
}
</style>

<div class="container-fluid p-4">

    <!-- HEADER -->
    <div class="mb-4">

        <h2 class="fw-bold mb-1">

            <i class="bi bi-journal-bookmark-fill text-primary"></i>
            Cultes

        </h2>

        <p class="text-muted mb-0">

            Consultez les derniers cultes organisés.

        </p>

    </div>


    <!-- LISTE -->
    <div class="row g-4">

        <?php foreach($cultes as $c): ?>

        <div class="col-lg-6">

            <div class="card shadow-sm culte-card h-100">

                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start mb-3">

                        <div>

                            <h5 class="fw-bold text-primary">

                                <?= htmlspecialchars($c['theme']) ?>

                            </h5>

                            <small class="text-muted">

                                <i class="bi bi-calendar-event"></i>

                                <?= date('d/m/Y', strtotime($c['date_culte'])) ?>

                            </small>

                        </div>

                        <div class="fs-2 text-primary">

                            <i class="bi bi-book-half"></i>

                        </div>

                    </div>

                    <div class="row g-3">

                        <div class="col-md-6">

                            <div class="info-box">

                                <small class="text-muted">
                                    Passage biblique
                                </small>

                                <div class="fw-semibold">

                                    <?= htmlspecialchars($c['passage_biblique'] ?: '-') ?>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="info-box">

                                <small class="text-muted">
                                    Orateur
                                </small>

                                <div class="fw-semibold">

                                    <?= htmlspecialchars($c['orateur'] ?: '-') ?>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="info-box">

                                <small class="text-muted">
                                    Interprète
                                </small>

                                <div class="fw-semibold">

                                    <?= htmlspecialchars($c['interprete'] ?: '-') ?>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="info-box">

                                <small class="text-muted">
                                    Participants
                                </small>

                                <div class="fw-semibold">

                                    <?= $c['hommes'] + $c['femmes'] ?> fidèles

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <?php endforeach; ?>

    </div>

</div>

<?php require_once '../../layouts/footer.php'; ?>