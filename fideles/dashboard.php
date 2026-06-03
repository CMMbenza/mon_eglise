<?php

require_once 'auth_visiteurs.php';
require_once '../config/database.php';

$user_id = $_SESSION['user']['id'];

$page_title = "Mon espace fidèle";

// =====================================
// FONCTION SOMME PAR DEVISE
// =====================================

function sommeDevise($pdo, $sql, $params = [])
{
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $data = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[$row['devise']] = $row['total'];
    }

    return $data;
}

// =====================================
// INFOS FIDELE
// =====================================

$stmt = $pdo->prepare("
    SELECT *
    FROM fideles
    WHERE user_id = ?
");
$stmt->execute([$user_id]);

$fidele = $stmt->fetch();


// =====================================
// ANNONCES RECENTES
// =====================================

$annonces = $pdo->query("
    SELECT *
    FROM annonces
    ORDER BY id DESC
    LIMIT 5
");


// =====================================
// CULTES RECENTS
// =====================================

$cultes = $pdo->query("
    SELECT
        theme,
        orateur,
        date_culte,
        hommes,
        femmes
    FROM cultes
    ORDER BY date_culte DESC
    LIMIT 5
");


// =====================================
// FONDS DISPONIBLES
// =====================================

$fonds = $pdo->query("
    SELECT *
    FROM fonds
    ORDER BY id DESC
    LIMIT 5
");


// =====================================
// MES ENGAGEMENTS
// =====================================

$engagements = $pdo->prepare("
    SELECT
        ef.*,
        f.campagne,
        f.motif
    FROM engagements_fonds ef

    INNER JOIN fonds f
        ON f.id = ef.fonds_id

    WHERE ef.user_id = ?

    ORDER BY ef.id DESC
");

$engagements->execute([$user_id]);


// =====================================
// TOTAL ENGAGE
// =====================================

$total_engage = sommeDevise($pdo, "
    SELECT
        devise,
        COALESCE(SUM(montant_engage),0) as total
    FROM engagements_fonds
    WHERE user_id = ?
    GROUP BY devise
", [$user_id]);


// =====================================
// MES PAIEMENTS
// =====================================

$paiements = $pdo->prepare("
    SELECT
        vf.*,
        ef.montant_engage,
        f.campagne
    FROM versements_fonds vf

    INNER JOIN engagements_fonds ef
        ON ef.id = vf.engagement_id

    INNER JOIN fonds f
        ON f.id = ef.fonds_id

    WHERE ef.user_id = ?

    ORDER BY vf.id DESC
");

$paiements->execute([$user_id]);


// =====================================
// TOTAL PAYE
// =====================================

$total_paye = sommeDevise($pdo, "
    SELECT
        vf.devise,
        COALESCE(SUM(vf.montant),0) as total
    FROM versements_fonds vf

    INNER JOIN engagements_fonds ef
        ON ef.id = vf.engagement_id

    WHERE ef.user_id = ?

    GROUP BY vf.devise
", [$user_id]);


// =====================================
// SOLDE RESTANT
// =====================================

$solde_usd =
    ($total_engage['USD'] ?? 0)
    - ($total_paye['USD'] ?? 0);

$solde_cdf =
    ($total_engage['CDF'] ?? 0)
    - ($total_paye['CDF'] ?? 0);

    // =====================================
// GESTION DES COMPTES
// =====================================
$compte_mouvements = $pdo->prepare("
    SELECT
        cm.*,
        c.nom AS compte_nom
    FROM compte_mouvements cm

    LEFT JOIN comptes c
        ON c.id = cm.compte_id

    LEFT JOIN fideles f
        ON f.id = cm.fidele_id

    WHERE f.user_id = ?

    ORDER BY cm.date_mouvement DESC
");

$compte_mouvements->execute([$user_id]);

require_once '../layouts/header.php';
require_once '../layouts/navbar_sidebar_fideles.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
.dashboard-card {
    border: none;
    border-radius: 18px;
    overflow: hidden;
}

.dashboard-card .card-body {
    padding: 25px;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
}

.table th {
    white-space: nowrap;
}
</style>

<div class="container-fluid p-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                👤 Mon espace fidèle
            </h2>

            <p class="text-muted mb-0">
                Bienvenue <?= htmlspecialchars($fidele['prenom'] ?? '') ?>
            </p>

        </div>

    </div>

    <?php require_once '../layouts/alerts.php'; ?>


    <!-- ===================== -->
    <!-- STATS -->
    <!-- ===================== -->

    <div class="row g-4 mb-4">

        <!-- ENGAGEMENT -->
        <div class="col-lg-4 col-md-6">

            <div class="card shadow-sm dashboard-card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <small class="text-muted">
                                Total engagé
                            </small>

                            <h5 class="mt-2 text-primary">
                                <?= number_format($total_engage['USD'] ?? 0,2) ?> USD
                            </h5>

                            <h5 class="text-primary">
                                <?= number_format($total_engage['CDF'] ?? 0,2) ?> CDF
                            </h5>

                        </div>

                        <div class="stat-icon bg-primary-subtle text-primary">

                            <i class="bi bi-wallet2"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- PAYE -->
        <div class="col-lg-4 col-md-6">

            <div class="card shadow-sm dashboard-card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <small class="text-muted">
                                Total payé
                            </small>

                            <h5 class="mt-2 text-success">
                                <?= number_format($total_paye['USD'] ?? 0,2) ?> USD
                            </h5>

                            <h5 class="text-success">
                                <?= number_format($total_paye['CDF'] ?? 0,2) ?> CDF
                            </h5>

                        </div>

                        <div class="stat-icon bg-success-subtle text-success">

                            <i class="bi bi-cash-stack"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- RESTANT -->
        <div class="col-lg-4 col-md-6">

            <div class="card shadow-sm dashboard-card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <small class="text-muted">
                                Solde restant
                            </small>

                            <h5 class="mt-2 text-danger">
                                <?= number_format($solde_usd,2) ?> USD
                            </h5>

                            <h5 class="text-danger">
                                <?= number_format($solde_cdf,2) ?> CDF
                            </h5>

                        </div>

                        <div class="stat-icon bg-danger-subtle text-danger">

                            <i class="bi bi-graph-down-arrow"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ===================== -->
    <!-- INFOS -->
    <!-- ===================== -->

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-dark text-white">

            <i class="bi bi-person-circle"></i>
            Mes informations

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-3">
                    <strong>Nom</strong><br>
                    <?= htmlspecialchars($fidele['nom']) ?>
                </div>

                <div class="col-md-3">
                    <strong>Postnom</strong><br>
                    <?= htmlspecialchars($fidele['postnom']) ?>
                </div>

                <div class="col-md-3">
                    <strong>Prénom</strong><br>
                    <?= htmlspecialchars($fidele['prenom']) ?>
                </div>

                <div class="col-md-3">
                    <strong>Téléphone</strong><br>
                    <?= htmlspecialchars($fidele['telephone']) ?>
                </div>

            </div>

        </div>

    </div>


    <!-- ===================== -->
    <!-- ANNONCES + CULTES -->
    <!-- ===================== -->

    <div class="row g-4 mb-4">

        <!-- ANNONCES -->
        <div class="col-lg-6">

            <div class="card shadow-sm h-100">

                <div class="card-header bg-warning">

                    <i class="bi bi-megaphone-fill"></i>
                    5 annonces récentes

                </div>

                <div class="card-body">

                    <?php foreach($annonces as $a): ?>

                    <div class="mb-3">

                        <h6 class="fw-bold">
                            <?= htmlspecialchars($a['titre']) ?>
                        </h6>

                        <small class="text-muted">
                            <?= $a['created_at'] ?>
                        </small>

                        <p class="mb-0 mt-2">
                            <?= nl2br(htmlspecialchars($a['contenu'])) ?>
                        </p>

                        <hr>

                    </div>

                    <?php endforeach; ?>

                </div>

            </div>

        </div>


        <!-- CULTES -->
        <div class="col-lg-6">

            <div class="card shadow-sm h-100">

                <div class="card-header bg-primary text-white">

                    <i class="bi bi-journal-bookmark-fill"></i>
                    5 cultes récents

                </div>

                <div class="card-body table-responsive">

                    <table class="table table-hover align-middle">

                        <thead>

                            <tr>
                                <th>Date</th>
                                <th>Thème</th>
                                <th>Orateur</th>
                                <th>Participants</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach($cultes as $c): ?>

                            <tr>

                                <td><?= $c['date_culte'] ?></td>

                                <td><?= htmlspecialchars($c['theme']) ?></td>

                                <td><?= htmlspecialchars($c['orateur']) ?></td>

                                <td>

                                    <?= $c['hommes'] + $c['femmes'] ?>

                                </td>

                            </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>


    <!-- ===================== -->
    <!-- FONDS DISPONIBLES -->
    <!-- ===================== -->

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-success text-white">

            <i class="bi bi-bank2"></i>
            Fonds disponibles

        </div>

        <div class="card-body table-responsive">

            <table class="table table-hover">

                <thead>

                    <tr>
                        <th>Campagne</th>
                        <th>Montant</th>
                        <th>Motif</th>
                        <th>Date</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach($fonds as $f): ?>

                    <tr>

                        <td><?= htmlspecialchars($f['campagne']) ?></td>

                        <td>

                            <?= htmlspecialchars($f['montant']) ?>
                            <?= htmlspecialchars($f['devise']) ?>

                        </td>

                        <td><?= htmlspecialchars($f['motif']) ?></td>

                        <td><?= $f['created_at'] ?></td>

                    </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>


    <!-- ===================== -->
    <!-- MES ENGAGEMENTS -->
    <!-- ===================== -->

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-info text-white">

            <i class="bi bi-clipboard-check-fill"></i>
            Mes engagements

        </div>

        <div class="card-body table-responsive">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>
                        <th>Campagne</th>
                        <th>Montant choisi</th>
                        <th>Période</th>
                        <th>Date</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach($engagements as $e): ?>

                    <tr>

                        <td><?= htmlspecialchars($e['campagne']) ?></td>

                        <td>

                            <?= number_format($e['montant_engage'],2) ?>
                            <?= htmlspecialchars($e['devise']) ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($e['periode'] ?? 'Non définie') ?>

                        </td>

                        <td><?= $e['created_at'] ?></td>

                    </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>


    <!-- ===================== -->
    <!-- PAIEMENTS -->
    <!-- ===================== -->

    <div class="card shadow-sm mb-5">

        <div class="card-header bg-dark text-white">

            <i class="bi bi-cash-coin"></i>
            Paiements / versements

        </div>

        <div class="card-body table-responsive">

            <table class="table table-striped align-middle">

                <thead>

                    <tr>
                        <th>Campagne</th>
                        <th>Engagement</th>
                        <th>Montant payé</th>
                        <th>Date paiement</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach($paiements as $p): ?>

                    <tr>

                        <td>

                            <?= htmlspecialchars($p['campagne']) ?>

                        </td>

                        <td>

                            <?= number_format($p['montant_engage'],2) ?>
                            <?= htmlspecialchars($p['devise']) ?>

                        </td>

                        <td class="text-success fw-bold">

                            <?= number_format($p['montant'],2) ?>
                            <?= htmlspecialchars($p['devise']) ?>

                        </td>

                        <td>

                            <?= $p['date_versement'] ?>

                        </td>

                    </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

    <!-- ===================== -->
    <!-- MOUVEMENTS COMPTE -->
    <!-- ===================== -->
    <div class="card shadow-sm mb-4">

        <div class="card-header bg-secondary text-white">

            <i class="bi bi-bank"></i>
            Gestion des comptes

        </div>

        <div class="card-body table-responsive">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>
                        <th>Date</th>
                        <th>Compte</th>
                        <!-- <th>Type</th> -->
                        <th>Montant</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach($compte_mouvements as $cm): ?>

                    <tr>

                        <td>
                            <?= $cm['date_mouvement'] ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($cm['compte_nom']) ?>
                        </td>

                        <!-- <td>

                        <?php if($cm['TYPE'] == 'ENTREE'): ?>

                        <span class="badge bg-success">
                            Entrée
                        </span>

                        <?php else: ?>

                        <span class="badge bg-danger">
                            Sortie
                        </span>

                        <?php endif; ?>

                    </td> -->

                        <td class="fw-bold">

                            <?= number_format($cm['montant'],2) ?>
                            <?= htmlspecialchars($cm['devise']) ?>

                        </td>

                    </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>
</div>

<?php require_once '../layouts/footer.php'; ?>