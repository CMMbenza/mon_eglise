<?php

require_once '../auth_visiteurs.php';
require_once '../../config/database.php';

$page_title = "Annonces";


// =====================================
// ANNONCES
// =====================================

$annonces = $pdo->query("
    SELECT *
    FROM annonces
    ORDER BY id DESC
");


require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar_fideles.php';
?>

<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
.annonce-card {
    border: none;
    border-radius: 18px;
    overflow: hidden;
    transition: .3s;
}

.annonce-card:hover {
    transform: translateY(-3px);
}
</style>

<div class="container-fluid p-4">

    <!-- HEADER -->
    <div class="mb-4">

        <h2 class="fw-bold mb-1">

            <i class="bi bi-megaphone-fill text-warning"></i>
            Annonces

        </h2>

        <p class="text-muted mb-0">

            Consultez les dernières annonces de l’église.

        </p>

    </div>


    <!-- LISTE -->
    <div class="row g-4">

        <?php foreach($annonces as $a): ?>

        <div class="col-lg-6">

            <div class="card shadow-sm annonce-card h-100">

                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start mb-3">

                        <div>

                            <h5 class="fw-bold">

                                <?= htmlspecialchars($a['titre']) ?>

                            </h5>

                            <small class="text-muted">

                                <i class="bi bi-calendar-event"></i>

                                <?= date('d/m/Y H:i', strtotime($a['created_at'])) ?>

                            </small>

                        </div>

                        <div class="fs-2 text-warning">

                            <i class="bi bi-megaphone-fill"></i>

                        </div>

                    </div>

                    <p class="mb-0 text-muted">

                        <?= nl2br(htmlspecialchars($a['contenu'])) ?>

                    </p>

                </div>

            </div>

        </div>

        <?php endforeach; ?>

    </div>

</div>

<?php require_once '../../layouts/footer.php'; ?>