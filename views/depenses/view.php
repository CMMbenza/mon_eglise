<?php
require_once '../../core/auth.php';
require_once '../../config/database.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM depenses WHERE id=?");
$stmt->execute([$id]);
$d = $stmt->fetch();

if(!$d){
    die("Dépense introuvable");
}

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';
?>

<div class="container-fluid p-4">

    <div class="row justify-content-center">

        <div class="col-lg-10">

            <div class="card shadow-sm border-0 rounded-4">

                <!-- HEADER -->
                <div class="card-header bg-dark text-white py-3">

                    <h4 class="mb-0">
                        <i class="bi bi-receipt"></i>
                        Détail de la dépense
                    </h4>

                </div>

                <!-- BODY -->
                <div class="card-body p-4">

                    <div class="mb-3">
                        <label class="text-muted">Motif</label>
                        <h5 class="fw-bold">
                            <?= htmlspecialchars($d['motif']) ?>
                        </h5>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted">Montant</label>
                        <h4 class="text-primary fw-bold">
                            <?= number_format($d['montant'], 2) ?> $
                        </h4>
                    </div>



                    <div class="mb-3">
                        <label class="text-muted">Bénéficiaire</label>
                        <p class="fw-semibold">
                            <?= htmlspecialchars($d['beneficiaire'] ?? '-') ?>
                        </p>
                    </div>

                    <!-- <div class="mb-3">
                        <label class="text-muted">Devise</label>
                        <span class="badge bg-success">
                            <?= htmlspecialchars($d['devise'] ?? 'CDF') ?>
                        </span>
                    </div> -->
                    <div class="mb-3">
                        <label class="text-muted">Date</label>
                        <p class="fw-semibold">
                            <?= htmlspecialchars($d['date_depense']) ?>
                        </p>
                    </div>
                    <div class="mt-4 d-flex gap-2">

                        <a href="index.php" class="btn btn-secondary w-100">
                            Retour
                        </a>

                        <a href="create_update_delete.php?action=edit&id=<?= $d['id'] ?>" class="btn btn-warning w-100">
                            Modifier
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once '../../layouts/footer.php'; ?>