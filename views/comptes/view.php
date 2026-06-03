<?php
require_once '../../core/auth.php';
require_once '../../config/database.php';

$id = $_GET['id'] ?? 0;

$c = $pdo->prepare("SELECT * FROM comptes WHERE id=?");
$c->execute([$id]);
$c = $c->fetch();

if(!$c){
    die("Compte introuvable");
}

/* SOLDE CDF */
$soldeCdf = $pdo->prepare("
    SELECT COALESCE(SUM(montant),0)
    FROM compte_mouvements
    WHERE compte_id = ?
    AND devise = 'CDF'
");
$soldeCdf->execute([$id]);
$soldeCdf = $soldeCdf->fetchColumn();

/* SOLDE USD */
$soldeUsd = $pdo->prepare("
    SELECT COALESCE(SUM(montant),0)
    FROM compte_mouvements
    WHERE compte_id = ?
    AND devise = 'USD'
");
$soldeUsd->execute([$id]);
$soldeUsd = $soldeUsd->fetchColumn();

/* MOUVEMENTS */
$mouvements = $pdo->prepare("
    SELECT cm.*, f.nom
    FROM compte_mouvements cm
    LEFT JOIN fideles f ON f.id = cm.fidele_id
    WHERE cm.compte_id = ?
    ORDER BY cm.id DESC
");
$mouvements->execute([$id]);

/* MENSUEL */
$mensuel = $pdo->prepare("
    SELECT 
        DATE_FORMAT(created_at,'%Y-%m') AS mois,
        SUM(CASE WHEN devise='CDF' THEN montant ELSE 0 END) AS total_cdf,
        SUM(CASE WHEN devise='USD' THEN montant ELSE 0 END) AS total_usd
    FROM compte_mouvements
    WHERE compte_id = ?
    GROUP BY mois
    ORDER BY mois DESC
");
$mensuel->execute([$id]);

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';
?>

<div class="container p-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h3 class="mb-0">
            <i class="bi bi-wallet2"></i>
            <?= htmlspecialchars($c['nom']) ?>
        </h3>

        <a href="depot.php?id=<?= $id ?>" class="btn btn-success btn-sm">
            <i class="bi bi-plus-circle-fill"></i>
            Nouveau mouvement
        </a>

    </div>

    <!-- SOLDES -->
    <div class="mb-3">

        <h5 class="text-success">
            <i class="bi bi-cash-coin"></i>
            Solde CDF: <?= number_format($soldeCdf,2) ?> CDF
        </h5>

        <h5 class="text-primary">
            <i class="bi bi-currency-dollar"></i>
            Solde USD: <?= number_format($soldeUsd,2) ?> USD
        </h5>

    </div>

    <hr>

    <!-- MENSUEL -->
    <h5>
        <i class="bi bi-bar-chart-line-fill"></i>
        Mensuel
    </h5>

    <table class="table table-bordered table-hover">

        <thead class="table-dark">
            <tr>
                <th>Mois</th>
                <th>CDF</th>
                <th>USD</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach($mensuel as $m): ?>
            <tr>
                <td><?= $m['mois'] ?></td>
                <td class="text-success fw-bold">
                    <?= number_format($m['total_cdf'],2) ?> CDF
                </td>
                <td class="text-primary fw-bold">
                    <?= number_format($m['total_usd'],2) ?> USD
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>

    <hr>

    <!-- MOUVEMENTS -->
    <h5>
        <i class="bi bi-list-ul"></i>
        Mouvements
    </h5>

    <table class="table table-bordered table-hover align-middle">

        <thead class="table-dark">
            <tr>
                <th>Fidèle</th>
                <th>Montant</th>
                <th>Devise</th>
                <th>Date</th>
            </tr>
        </thead>

        <tbody>

            <?php foreach($mouvements as $m): ?>
            <tr>

                <td><?= htmlspecialchars($m['nom'] ?? '-') ?></td>

                <td class="fw-bold">
                    <?= number_format($m['montant'],2) ?>
                </td>

                <td>
                    <span class="badge bg-dark">
                        <?= $m['devise'] ?>
                    </span>
                </td>

                <td>
                    <i class="bi bi-calendar-event"></i>
                    <?= $m['created_at'] ?>
                </td>

            </tr>
            <?php endforeach; ?>

        </tbody>

    </table>

</div>

<?php require_once '../../layouts/footer.php'; ?>