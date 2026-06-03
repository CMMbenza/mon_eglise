<?php

require_once '../core/auth.php';
require_once '../config/database.php';

$page_title = 'Dashboard';


// ======================================
// STATISTIQUES
// ======================================

$total_fideles = $pdo->query("
    SELECT COUNT(*)
    FROM fideles
")->fetchColumn();

$total_hommes = $pdo->query("
    SELECT COUNT(*)
    FROM fideles
    WHERE sexe='M'
")->fetchColumn();

$total_femmes = $pdo->query("
    SELECT COUNT(*)
    FROM fideles
    WHERE sexe='F'
")->fetchColumn();

$total_cultes = $pdo->query("
    SELECT COUNT(*)
    FROM cultes
")->fetchColumn();

$total_cultes_usd = $pdo->query("
    SELECT COALESCE(SUM(offrande_dime + sociale + autres),0)
    FROM cultes
    WHERE devise = 'USD'
")->fetchColumn();

$total_cultes_cdf = $pdo->query("
    SELECT COALESCE(SUM(offrande_dime + sociale + autres),0)
    FROM cultes
    WHERE devise = 'CDF'
")->fetchColumn();

$total_sociale_usd = $pdo->query("
    SELECT COALESCE(SUM(sociale),0)
    FROM cultes
    WHERE devise = 'USD'
")->fetchColumn();

$total_sociale_cdf = $pdo->query("
    SELECT COALESCE(SUM(sociale),0)
    FROM cultes
    WHERE devise = 'CDF'
")->fetchColumn();

$total_autres_usd = $pdo->query("
    SELECT COALESCE(SUM(autres),0)
    FROM cultes
    WHERE devise = 'USD'
")->fetchColumn();

$total_autres_cdf = $pdo->query("
    SELECT COALESCE(SUM(autres),0)
    FROM cultes
    WHERE devise = 'CDF'
")->fetchColumn();

$total_depenses_usd = $pdo->query("
    SELECT COALESCE(SUM(montant),0)
    FROM depenses
    WHERE devise = 'USD'
")->fetchColumn();

$total_depenses_cdf = $pdo->query("
    SELECT COALESCE(SUM(montant),0)
    FROM depenses
    WHERE devise = 'CDF'
")->fetchColumn();

$total_fonds_usd = $pdo->query("
    SELECT COALESCE(SUM(CAST(montant AS DECIMAL(10,2))),0)
    FROM fonds
    WHERE devise = 'USD'
")->fetchColumn();

$total_fonds_cdf = $pdo->query("
    SELECT COALESCE(SUM(CAST(montant AS DECIMAL(10,2))),0)
    FROM fonds
    WHERE devise = 'CDF'
")->fetchColumn();

function sumByDevise($pdo, $table, $column, $devise)
{
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM($column),0)
        FROM $table
        WHERE devise = ?
    ");
    $stmt->execute([$devise]);
    return $stmt->fetchColumn();
}

// ======================================
// CALCULS
// ======================================

$total_entrees_usd =
    $total_cultes_usd +
    $total_fonds_usd;

$total_entrees_cdf =
    $total_cultes_cdf +
    $total_fonds_cdf;

$solde_usd = $total_entrees_usd - $total_depenses_usd;
$solde_cdf = $total_entrees_cdf - $total_depenses_cdf;


// ======================================
// DERNIERS ENREGISTREMENTS
// ======================================

$last_cultes = $pdo->query("
    SELECT *
    FROM cultes
    ORDER BY id DESC
    LIMIT 5
");

$last_depenses = $pdo->query("
    SELECT *
    FROM depenses
    ORDER BY id DESC
    LIMIT 5
");

require_once '../layouts/header.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
body {
    background: #f4f6f9;
}

.dashboard-card {
    border: none;
    border-radius: 18px;
    overflow: hidden;
    transition: .3s;
}

.dashboard-card:hover {
    transform: translateY(-3px);
}

.icon-box {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
}

.card-title-small {
    font-size: 14px;
    color: #6c757d;
    margin-bottom: 8px;
}

.card-number {
    font-size: 28px;
    font-weight: bold;
}

.table thead th {
    background: #212529;
    color: white;
    border: none;
}

.table-hover tbody tr:hover {
    background: #f8f9fa;
}

.section-card {
    border: none;
    border-radius: 18px;
    overflow: hidden;
}

.section-header {
    padding: 15px 20px;
    font-weight: 600;
    font-size: 16px;
}

.stat-mini {
    background: #fff;
    border-radius: 15px;
    padding: 18px;
    border: 1px solid #eee;
    transition: .3s;
}

.stat-mini:hover {
    background: #f8f9fa;
}
</style>

<?php require_once '../layouts/navbar_sidebar.php'; ?>


<div class="d-flex">

    <div class="container-fluid p-4">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h2 class="fw-bold mb-1">

                    Tableau de bord

                </h2>

                <p class="text-muted mb-0">

                    Vue générale des activités de l’église

                </p>

            </div>

            <span class="badge bg-dark p-2">

                <i class="bi bi-calendar-event"></i>

                <?= date('d/m/Y') ?>

            </span>

        </div>

        <?php require_once '../layouts/alerts.php'; ?>


        <!-- ====================================== -->
        <!-- CARDS PRINCIPALES -->
        <!-- ====================================== -->

        <div class="row g-4 mb-4">

            <!-- FIDELES -->
            <div class="col-xl-3 col-md-6">

                <div class="card shadow-sm dashboard-card">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <div class="card-title-small">
                                    Fidèles enregistrés
                                </div>

                                <div class="card-number">
                                    <?= $total_fideles ?>
                                </div>

                            </div>

                            <div class="icon-box bg-primary-subtle text-primary">

                                <i class="bi bi-people-fill"></i>

                            </div>

                        </div>

                        <hr>

                        <div class="d-flex justify-content-between small">

                            <span>
                                Hommes :
                                <strong><?= $total_hommes ?></strong>
                            </span>

                            <span>
                                Femmes :
                                <strong><?= $total_femmes ?></strong>
                            </span>

                        </div>

                    </div>

                </div>

            </div>


            <!-- CULTES -->
            <div class="col-xl-3 col-md-6">

                <div class="card shadow-sm dashboard-card">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <div class="card-title-small">
                                    Total cultes
                                </div>

                                <div class="card-number">
                                    <?= $total_cultes ?>
                                </div>

                            </div>

                            <div class="icon-box bg-success-subtle text-success">

                                <i class="bi bi-book-half"></i>

                            </div>

                        </div>

                        <hr>

                        <small class="text-muted">

                            Activités spirituelles enregistrées

                        </small>

                    </div>

                </div>

            </div>


            <!-- ENTREES -->
            <div class="col-xl-3 col-md-6">

                <div class="card shadow-sm dashboard-card">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <div class="card-title-small">
                                    Total entrées
                                </div>

                                <div class="card-number text-success" style="font-size: 18px;">

                                    <?= number_format($total_entrees_usd,2) ?> $ <br>
                                    <?= number_format($total_entrees_cdf,2) ?> CDF

                                </div>

                            </div>

                            <div class="icon-box bg-info-subtle text-info">

                                <i class="bi bi-cash-stack"></i>

                            </div>

                        </div>

                        <hr>

                        <small class="text-muted">

                            Offrandes + Dîmes + Fonds

                        </small>

                    </div>

                </div>

            </div>


            <!-- SOLDE -->
            <div class="col-xl-3 col-md-6">

                <div class="card shadow-sm dashboard-card">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <div class="card-title-small">
                                    Solde global
                                </div>

                                <div class="card-number <?= $solde >= 0 ? 'text-primary' : 'text-danger' ?>"
                                    style="font-size: 18px;">
                                    <?= number_format($solde_usd,2) ?> $ <br>
                                    <?= number_format($solde_cdf,2) ?> CDF
                                </div>

                            </div>

                            <div class="icon-box bg-dark-subtle text-dark">

                                <i class="bi bi-pie-chart-fill"></i>

                            </div>

                        </div>

                        <hr>

                        <small class="text-muted">

                            Entrées - Dépenses

                        </small>

                    </div>

                </div>

            </div>

        </div>



        <!-- ====================================== -->
        <!-- DETAILS FINANCIERS -->
        <!-- ====================================== -->

        <div class="card shadow-sm section-card mb-4">

            <div class="section-header bg-success text-white">

                <i class="bi bi-cash-coin me-2"></i>

                Résumé financier

            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-4 col-lg-2">

                        <div class="stat-mini text-center">

                            <i class="bi bi-wallet2 fs-2 text-success"></i>

                            <h6 class="mt-2">Offrandes + Dîmes</h6>

                            <h5 class="text-success">

                                <?= number_format($total_cultes_usd,2) ?> USD
                                <br>

                                <?= number_format($total_cultes_cdf,2) ?> CDF

                            </h5>

                        </div>

                    </div>

                    <div class="d-none col-md-4 col-lg-2">

                        <div class="stat-mini text-center">

                            <i class="bi bi-cash-stack fs-2 text-primary"></i>

                            <h6 class="mt-2">Dîmes</h6>

                            <h5 class="text-primary">

                                <?= number_format($total_dime,2) ?> USD

                            </h5>

                        </div>

                    </div>

                    <div class="col-md-4 col-lg-2">

                        <div class="stat-mini text-center">

                            <i class="bi bi-heart-fill fs-2 text-warning"></i>

                            <h6 class="mt-2">Sociale</h6>

                            <h5 class="text-warning">

                                <?= number_format($total_sociale_usd,2) ?> USD
                                <br>

                                <?= number_format($total_sociale_cdf,2) ?> CDF

                            </h5>

                        </div>

                    </div>

                    <div class="col-md-4 col-lg-2">

                        <div class="stat-mini text-center">

                            <i class="bi bi-plus-circle-fill fs-2 text-dark"></i>

                            <h6 class="mt-2">Autres</h6>

                            <h5>

                                <?= number_format($total_autres_usd,2) ?> USD
                                <br>

                                <?= number_format($total_autres_cdf,2) ?> CDF

                            </h5>

                        </div>

                    </div>

                    <div class="col-md-4 col-lg-2">

                        <div class="stat-mini text-center">

                            <i class="bi bi-bank fs-2 text-info"></i>

                            <h6 class="mt-2">Fonds</h6>

                            <h5 class="text-info">

                                <?= number_format($total_fonds_usd,2) ?> USD
                                <br>

                                <?= number_format($total_fonds_cdf,2) ?> CDF

                            </h5>

                        </div>

                    </div>

                    <div class="col-md-4 col-lg-4 h-100" style="heigth: 100% auto">

                        <div class="stat-mini text-left">

                            <i class="bi bi-credit-card-2-front-fill fs-2 text-danger"></i>

                            <h6 class="mt-2">Dépenses</h6>

                            <h5 class="text-danger">

                                <?= number_format($total_depenses_usd,2) ?> USD
                                -
                                <?= number_format($total_depenses_cdf,2) ?> CDF

                            </h5>

                        </div>

                    </div>

                </div>

            </div>

        </div>



        <!-- ====================================== -->
        <!-- DERNIERS CULTES -->
        <!-- ====================================== -->

        <div class="card shadow-sm section-card mb-4">

            <div class="section-header bg-primary text-white">

                <i class="bi bi-journal-bookmark-fill me-2"></i>

                5 Derniers cultes

            </div>

            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>

                            <th>Date</th>
                            <th>Thème</th>
                            <th>Orateur</th>
                            <th>Participants</th>
                            <th>Offrande + Dîme</th>
                            <!-- <th>Dîme</th> -->
                            <th>Sociale</th>
                            <th>Autres</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach($last_cultes as $c): ?>

                        <tr>

                            <td><?= $c['date_culte'] ?></td>

                            <td><?= htmlspecialchars($c['theme']) ?></td>

                            <td><?= htmlspecialchars($c['orateur']) ?></td>

                            <td>

                                <span class="badge bg-dark">

                                    <?= $c['hommes'] + $c['femmes'] ?>

                                </span>

                            </td>

                            <td class="text-success">

                                <?= number_format($c['offrande_dime'],2) ?> <?= $c['devise'] ?>

                            </td>

                            <td>

                                <?= number_format($c['sociale'],2) ?> <?= $c['devise'] ?>

                            </td>

                            <td>

                                <?= number_format($c['autres'],2) ?> <?= $c['devise'] ?>

                            </td>

                        </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>



        <!-- ====================================== -->
        <!-- DEPENSES -->
        <!-- ====================================== -->

        <div class="card shadow-sm section-card mb-5">

            <div class="section-header bg-danger text-white">

                <i class="bi bi-credit-card-2-front-fill me-2"></i>

                5 Dernières dépenses

            </div>

            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>

                            <th>Date</th>
                            <th>Motif</th>
                            <th>Bénéficiaire</th>
                            <th>Montant</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach($last_depenses as $d): ?>

                        <tr>

                            <td><?= $d['date_depense'] ?></td>

                            <td><?= htmlspecialchars($d['motif']) ?></td>

                            <td><?= htmlspecialchars($d['beneficiaire']) ?></td>

                            <td class="text-danger fw-bold">

                                <?= number_format($d['montant'],2) ?> <?= $d['devise'] ?>

                            </td>

                        </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<?php require_once '../layouts/footer.php'; ?>