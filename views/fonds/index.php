<?php

require_once '../../core/auth.php';
require_once '../../config/database.php';


// ======================================
// LISTE DES FONDS + NBRE SOUSCRIPTIONS
// ======================================

$fonds = $pdo->query("
    SELECT
        f.*,
        COUNT(DISTINCT ef.user_id) AS total_souscriptions
    FROM fonds f
    LEFT JOIN engagements_fonds ef
        ON ef.fonds_id = f.id
    GROUP BY f.id
    ORDER BY f.id DESC
");

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
.table-card {
    border: none;
    border-radius: 18px;
    overflow: hidden;
}

.table th {
    white-space: nowrap;
}
</style>

<div class="d-flex">

    <div class="container-fluid p-4">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h3 class="fw-bold mb-1">
                    Contributions / Fonds
                </h3>

                <p class="text-muted mb-0">
                    Gestion des fonds et souscriptions des fidèles
                </p>

            </div>

            <a href="create_update_delete.php?action=create" class="btn btn-primary">

                <i class="bi bi-plus-circle-fill"></i>
                Nouvelle contribution

            </a>

        </div>


        <!-- TABLE -->
        <div class="card shadow-sm table-card">

            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-dark">

                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Description</th>
                            <th>Montant</th>
                            <!-- <th>Devise</th> -->
                            <th>Souscriptions</th>
                            <th></th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach($fonds as $f): ?>

                        <tr>

                            <td>
                                <a href="view.php?id=<?= $f['id'] ?>" class="btn btn-primary btn-sm">
                                    <?= $f['id'] ?>
                                </a>
                            </td>

                            <td>
                                <strong>
                                    <?= htmlspecialchars($f['campagne']) ?>
                                </strong>
                            </td>

                            <td>
                                <?= htmlspecialchars($f['motif']) ?>
                            </td>

                            <!-- MONTANT + DEVise corrigé -->
                            <td class="fw-bold text-primary">
                                <?= htmlspecialchars($f['montant']) ?>
                                <span class="badge bg-dark"> <?= htmlspecialchars($f['devise'] ?? 'CDF') ?></span>
                            </td>

                            <!-- <td>
                                <span class="badge bg-secondary">
                                    <?= htmlspecialchars($f['devise'] ?? 'CDF') ?>
                                </span>
                            </td> -->

                            <td>
                                <span class="badge bg-primary">
                                    <?= $f['total_souscriptions'] ?>
                                    souscription(s)
                                </span>
                            </td>

                            <td class="d-flex gap-2">
                                <a href="view.php?id=<?= $f['id'] ?>" class="btn btn-success btn-sm">
                                    <i class="bi bi-folder2-open"></i> Ouvrir la contribution
                                </a>
                                <!-- <a href="souscrire_fidele.php?fonds_id=<?= $f['id'] ?>"
                                    class="btn btn-info btn-sm text-white" title="Souscrire un fidèle">
                                    <i class="bi bi-person-plus-fill"></i>
                                </a>                                

                                <a href="historique_paiements.php?id=<?= $f['id'] ?>" class="btn btn-success btn-sm">
                                    <i class="bi bi-clock-history"></i>
                                </a>

                                <a href="enregistrer_paiement.php?id=<?= $f['id'] ?>" class="btn btn-dark btn-sm">
                                    <i class="bi bi-cash-coin"></i>
                                </a>

                                <a href="create_update_delete.php?action=edit&id=<?= $f['id'] ?>"
                                    class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>

                                <a href="create_update_delete.php?action=delete&id=<?= $f['id'] ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Supprimer cette contribution ?')">
                                    <i class="bi bi-trash-fill"></i>
                                </a> -->

                            </td>

                        </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<?php require_once '../../layouts/footer.php'; ?>