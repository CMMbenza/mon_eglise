<?php
require_once '../../core/auth.php';
require_once '../../config/database.php';

$comptes = $pdo->query("
    SELECT 
        c.*,

        -- Solde CDF
        COALESCE(SUM(
            CASE 
                WHEN cm.devise = 'CDF' THEN cm.montant 
                ELSE 0 
            END
        ),0) AS solde_cdf,

        -- Solde USD
        COALESCE(SUM(
            CASE 
                WHEN cm.devise = 'USD' THEN cm.montant 
                ELSE 0 
            END
        ),0) AS solde_usd

    FROM comptes c
    LEFT JOIN compte_mouvements cm 
        ON cm.compte_id = c.id

    GROUP BY c.id
    ORDER BY c.id DESC
");

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';
?>

<div class="container-fluid p-4">

    <div class="d-flex justify-content-between mb-3">
        <h3>Comptes</h3>
        <a href="create_update_delete.php?action=create" class="btn btn-primary">
            Nouveau compte
        </a>
    </div>

    <table class="table table-bordered table-hover">

        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Description</th>
                <th>Solde (CDF)</th>
                <th>Solde (USD)</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>

            <?php foreach($comptes as $c): ?>

            <tr>

                <td><?= $c['id'] ?></td>

                <td><?= htmlspecialchars($c['nom']) ?></td>

                <td><?= htmlspecialchars($c['description']) ?></td>

                <td class="fw-bold text-success">
                    <?= number_format($c['solde_cdf'], 2) ?> CDF
                </td>

                <td class="fw-bold text-primary">
                    <?= number_format($c['solde_usd'], 2) ?> USD
                </td>

                <td>
                    <div class="d-flex gap-1 flex-wrap">

                        <a href="view.php?id=<?= $c['id'] ?>" class="btn btn-primary btn-sm">
                            <i class="bi bi-eye-fill"></i>
                        </a>

                        <a href="depot.php?id=<?= $c['id'] ?>" class="btn btn-success btn-sm">
                            <i class="bi bi-arrow-left-right"></i>
                        </a>

                        <a href="create_update_delete.php?action=edit&id=<?= $c['id'] ?>"
                            class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        <a href="create_update_delete.php?action=delete&id=<?= $c['id'] ?>"
                            class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce compte ?')">
                            <i class="bi bi-trash-fill"></i>
                        </a>

                    </div>
                </td>

            </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

</div>

<?php require_once '../../layouts/footer.php'; ?>