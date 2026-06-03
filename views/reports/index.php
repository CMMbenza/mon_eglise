<?php

require_once '../../core/auth.php';
require_once '../../config/database.php';

$page_title = "Rapports mensuels";

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';


// ======================================
// FILTRE MOIS
// ======================================

$mois_filtre = $_GET['mois'] ?? date('Y-m');


// ======================================
// FONCTION SOMME PAR DEVISE
// ======================================

function sumByDevise(
    $pdo,
    $table,
    $column,
    $devise,
    $dateColumn = null,
    $mois = null,
    $extraWhere = ''
) {

    $sql = "
        SELECT COALESCE(SUM($column),0)
        FROM $table
        WHERE devise = ?
    ";

    if ($dateColumn && $mois) {
        $sql .= " AND DATE_FORMAT($dateColumn,'%Y-%m') = ?";
    }

    if (!empty($extraWhere)) {
        $sql .= " AND $extraWhere";
    }

    $stmt = $pdo->prepare($sql);

    if ($dateColumn && $mois) {
        $stmt->execute([$devise, $mois]);
    } else {
        $stmt->execute([$devise]);
    }

    return $stmt->fetchColumn() ?? 0;
}


// ======================================
// CULTES
// ======================================

$total_offrande_dime_usd = sumByDevise(
    $pdo,
    'cultes',
    'offrande_dime',
    'USD',
    'date_culte',
    $mois_filtre
);

$total_offrande_dime_cdf = sumByDevise(
    $pdo,
    'cultes',
    'offrande_dime',
    'CDF',
    'date_culte',
    $mois_filtre
);


$total_sociale_usd = sumByDevise(
    $pdo,
    'cultes',
    'sociale',
    'USD',
    'date_culte',
    $mois_filtre
);

$total_sociale_cdf = sumByDevise(
    $pdo,
    'cultes',
    'sociale',
    'CDF',
    'date_culte',
    $mois_filtre
);


$total_autres_usd = sumByDevise(
    $pdo,
    'cultes',
    'autres',
    'USD',
    'date_culte',
    $mois_filtre
);

$total_autres_cdf = sumByDevise(
    $pdo,
    'cultes',
    'autres',
    'CDF',
    'date_culte',
    $mois_filtre
);


// ======================================
// NOMBRE CULTES
// ======================================

$total_cultes = $pdo->prepare("
    SELECT COUNT(*)
    FROM cultes
    WHERE DATE_FORMAT(date_culte,'%Y-%m') = ?
");

$total_cultes->execute([$mois_filtre]);

$total_cultes = $total_cultes->fetchColumn();


// ======================================
// PARTICIPANTS
// ======================================

$cultesData = $pdo->prepare("
    SELECT 
        COALESCE(SUM(hommes),0) as hommes,
        COALESCE(SUM(femmes),0) as femmes
    FROM cultes
    WHERE DATE_FORMAT(date_culte,'%Y-%m') = ?
");

$cultesData->execute([$mois_filtre]);

$culte = $cultesData->fetch();


// ======================================
// VERSEMENTS FONDS
// ======================================

$total_versements_usd = sumByDevise(
    $pdo,
    'versements_fonds',
    'montant',
    'USD',
    'date_versement',
    $mois_filtre
);

$total_versements_cdf = sumByDevise(
    $pdo,
    'versements_fonds',
    'montant',
    'CDF',
    'date_versement',
    $mois_filtre
);

// ======================================
// GESTION DES COMPTES
// ======================================

$total_gestion_des_comptes_usd = sumByDevise(
    $pdo,
    'compte_mouvements',
    'montant',
    'USD',
    'date_mouvement',
    $mois_filtre
);

$total_gestion_des_comptes_cdf = sumByDevise(
    $pdo,
    'compte_mouvements',
    'montant',
    'CDF',
    'date_mouvement',
    $mois_filtre
);

// ======================================
// NOMBRE SOUSCRIPTEURS
// ======================================

$souscripteurs = $pdo->prepare("
    SELECT COUNT(DISTINCT user_id)
    FROM engagements_fonds
    WHERE DATE_FORMAT(created_at,'%Y-%m') = ?
");

$souscripteurs->execute([$mois_filtre]);

$total_souscripteurs = $souscripteurs->fetchColumn();


// ======================================
// COMPTE MOUVEMENTS
// ======================================

$total_compte_entree_usd = sumByDevise(
    $pdo,
    'compte_mouvements',
    'montant',
    'USD',
    'date_mouvement',
    $mois_filtre,
    "TYPE='ENTREE'"
);

$total_compte_entree_cdf = sumByDevise(
    $pdo,
    'compte_mouvements',
    'montant',
    'CDF',
    'date_mouvement',
    $mois_filtre,
    "TYPE='ENTREE'"
);


$total_compte_sortie_usd = sumByDevise(
    $pdo,
    'compte_mouvements',
    'montant',
    'USD',
    'date_mouvement',
    $mois_filtre,
    "TYPE='SORTIE'"
);

$total_compte_sortie_cdf = sumByDevise(
    $pdo,
    'compte_mouvements',
    'montant',
    'CDF',
    'date_mouvement',
    $mois_filtre,
    "TYPE='SORTIE'"
);


// ======================================
// DEPENSES
// ======================================

$total_depenses_usd = sumByDevise(
    $pdo,
    'depenses',
    'montant',
    'USD',
    'date_depense',
    $mois_filtre
);

$total_depenses_cdf = sumByDevise(
    $pdo,
    'depenses',
    'montant',
    'CDF',
    'date_depense',
    $mois_filtre
);


// ======================================
// TOTAL ENTREES
// ======================================

$entrees_usd =
    $total_offrande_dime_usd +
    $total_sociale_usd +
    $total_autres_usd +
    $total_versements_usd +
    $total_compte_entree_usd;

$entrees_cdf =
    $total_offrande_dime_cdf +
    $total_sociale_cdf +
    $total_autres_cdf +
    $total_versements_cdf +
    $total_compte_entree_cdf;


// ======================================
// TOTAL SORTIES
// ======================================

$sorties_usd =
    $total_depenses_usd +
    $total_compte_sortie_usd;

$sorties_cdf =
    $total_depenses_cdf +
    $total_compte_sortie_cdf;


// ======================================
// SOLDES
// ======================================

$solde_usd = $entrees_usd - $sorties_usd;
$solde_cdf = $entrees_cdf - $sorties_cdf;


// ======================================
// FIDELES + ANNONCES
// ======================================

$total_fideles = $pdo->prepare("
    SELECT COUNT(*)
    FROM fideles
    WHERE DATE_FORMAT(created_at,'%Y-%m') = ?
");

$total_fideles->execute([$mois_filtre]);

$total_fideles = $total_fideles->fetchColumn();


$total_annonces = $pdo->prepare("
    SELECT COUNT(*)
    FROM annonces
    WHERE DATE_FORMAT(created_at,'%Y-%m') = ?
");

$total_annonces->execute([$mois_filtre]);

$total_annonces = $total_annonces->fetchColumn();


// ======================================
// DETAILS ENTREES CULTES
// ======================================

$entrees = $pdo->prepare("
    SELECT
        date_culte,
        theme,
        offrande_dime,
        sociale,
        autres,
        devise
    FROM cultes
    WHERE DATE_FORMAT(date_culte,'%Y-%m') = ?
    ORDER BY date_culte DESC
");

$entrees->execute([$mois_filtre]);


// ======================================
// DETAILS VERSEMENTS
// ======================================

$versements = $pdo->prepare("
    SELECT 
        vf.*,
        u.nom,
        fd.campagne AS engagement
    FROM versements_fonds vf
    LEFT JOIN engagements_fonds ef 
        ON ef.id = vf.engagement_id
    LEFT JOIN users u 
        ON u.id = ef.user_id
    LEFT JOIN fonds fd
        ON fd.id = ef.fonds_id
    WHERE DATE_FORMAT(vf.date_versement,'%Y-%m') = ?
    ORDER BY vf.date_versement DESC
");

$versements->execute([$mois_filtre]);


// ======================================
// DETAILS COMPTE MOUVEMENTS
// ======================================

$compte_mouvements = $pdo->prepare("
    SELECT 
        cm.*,
        c.nom AS compte_nom,
        f.nom,
        f.postnom,
        f.prenom
    FROM compte_mouvements cm
    LEFT JOIN comptes c 
        ON c.id = cm.compte_id
    LEFT JOIN fideles f 
        ON f.id = cm.fidele_id
    WHERE DATE_FORMAT(cm.date_mouvement,'%Y-%m') = ?
    ORDER BY cm.date_mouvement DESC
");

$compte_mouvements->execute([$mois_filtre]);


// ======================================
// DETAILS DEPENSES
// ======================================

$depenses = $pdo->prepare("
    SELECT *
    FROM depenses
    WHERE DATE_FORMAT(date_depense,'%Y-%m') = ?
    ORDER BY date_depense DESC
");

$depenses->execute([$mois_filtre]);

?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
body {
    background: #f4f6f9;
}

.report-card {
    border: none;
    border-radius: 18px;
    overflow: hidden;
    transition: .3s;
}

.report-card:hover {
    transform: translateY(-3px);
}

.card-header-custom {
    padding: 15px 20px;
    font-weight: 600;
    font-size: 16px;
}

.stat-box {
    background: #fff;
    border-radius: 15px;
    padding: 20px;
    border: 1px solid #eee;
    height: 100%;
}

.amount-usd {
    color: #198754;
    font-weight: bold;
}

.amount-cdf {
    color: #0d6efd;
    font-weight: bold;
}

.table thead th {
    background: #212529;
    color: #fff;
    border: none;
}

.badge-custom {
    font-size: 13px;
    padding: 8px 12px;
}
</style>

<div class="d-flex">

    <div class="container-fluid p-4">

        <!-- HEADER -->

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h2 class="fw-bold mb-1">
                    📊 Rapports mensuels
                </h2>

                <p class="text-muted mb-0">
                    Rapport détaillé des activités financières et spirituelles
                </p>

            </div>

            <span class="badge bg-dark badge-custom">

                <i class="bi bi-calendar-event"></i>

                <?= date('d/m/Y') ?>

            </span>

        </div>

        <?php require_once '../../layouts/alerts.php'; ?>


        <!-- FILTRE -->

        <div class="card shadow-sm border-0 mb-4">

            <div class="card-body">

                <form method="GET" class="row g-3 align-items-end">

                    <div class="col-md-4">

                        <label class="form-label fw-bold">
                            Filtrer par mois
                        </label>

                        <input type="month" name="mois" class="form-control"
                            value="<?= htmlspecialchars($mois_filtre) ?>">

                    </div>

                    <div class="col-md-2">

                        <button class="btn btn-primary w-100">

                            <i class="bi bi-funnel-fill"></i>

                            Filtrer

                        </button>

                    </div>

                </form>

            </div>

        </div>


        <!-- CARDS -->

        <div class="row g-4 mb-4">

            <!-- CULTES -->

            <div class="col-lg-4">

                <div class="card shadow-sm report-card h-100">

                    <div class="card-header bg-primary text-white card-header-custom">

                        📖 Cultes

                    </div>

                    <div class="card-body">

                        <div class="stat-box">

                            <p>
                                📅 Nombre de cultes :
                                <strong><?= $total_cultes ?></strong>
                            </p>

                            <p>
                                👥 Participants :
                                H: <?= $culte['hommes'] ?> F: <?= $culte['femmes'] ?>
                                <strong>
                                    (<?= $culte['hommes'] + $culte['femmes'] ?>)
                                </strong>
                            </p>

                            <hr>

                            <h6>Offrandes + Dîmes</h6>

                            <div class="d-flex justify-content-between">

                                <p class="amount-usd mb-1">
                                    <?= number_format($total_offrande_dime_usd,2) ?> USD
                                </p>

                                <p class="amount-cdf mb-0">
                                    <?= number_format($total_offrande_dime_cdf,2) ?> CDF
                                </p>

                            </div>

                            <hr>

                            <h6>Sociale</h6>
                            <div class="d-flex justify-content-between">
                                <p class="amount-usd">
                                    <?= number_format($total_sociale_usd,2) ?> USD
                                </p>

                                <p class="amount-cdf">
                                    <?= number_format($total_sociale_cdf,2) ?> CDF
                                </p>
                            </div>
                            <hr>

                            <h6>Autres</h6>
                            <div class="d-flex justify-content-between">
                                <p class="amount-usd">
                                    <?= number_format($total_autres_usd,2) ?> USD
                                </p>

                                <p class="amount-cdf">
                                    <?= number_format($total_autres_cdf,2) ?> CDF
                                </p>
                            </div>
                        </div>

                    </div>

                </div>

            </div>


            <!-- FINANCES -->

            <div class="col-lg-4">

                <div class="card shadow-sm report-card h-100">

                    <div class="card-header bg-success text-white card-header-custom">

                        💰 Finances globales

                    </div>

                    <div class="card-body">

                        <div class="stat-box">

                            <h6>Total revenus</h6>
                            <div class="d-flex justify-content-between">
                                <p class="amount-usd">
                                    <?= number_format($entrees_usd,2) ?> USD
                                </p>

                                <p class="amount-cdf">
                                    <?= number_format($entrees_cdf,2) ?> CDF
                                </p>
                            </div>
                            <hr>

                            <h6>Total dépenses</h6>
                            <div class="d-flex justify-content-between">
                                <p class="text-danger fw-bold">
                                    <?= number_format($sorties_usd,2) ?> USD
                                </p>

                                <p class="text-danger fw-bold">
                                    <?= number_format($sorties_cdf,2) ?> CDF
                                </p>
                            </div>
                            <hr>

                            <h6>Reste / Solde</h6>
                            <div class="d-flex justify-content-between">
                                <p class="<?= $solde_usd >= 0 ? 'text-success fw-bold' : 'text-danger fw-bold' ?>">
                                    <?= number_format($solde_usd,2) ?> USD
                                </p>

                                <p class="<?= $solde_cdf >= 0 ? 'text-primary fw-bold' : 'text-danger fw-bold' ?>">
                                    <?= number_format($solde_cdf,2) ?> CDF
                                </p>
                            </div>
                        </div>

                    </div>

                </div>

            </div>


            <!-- GLOBAL -->

            <div class="col-lg-4">

                <div class="card shadow-sm report-card h-100">

                    <div class="card-header bg-dark text-white card-header-custom">

                        📌 Informations globales

                    </div>

                    <div class="card-body">

                        <div class="stat-box">

                            <p>
                                👥 Fidèles enregistrés :
                                <strong><?= $total_fideles ?></strong>
                            </p>

                            <p>
                                📢 Annonces :
                                <strong><?= $total_annonces ?></strong>
                            </p>

                            <p>
                                🤝 Souscripteurs fonds :
                                <strong><?= $total_souscripteurs ?></strong>
                            </p>

                            <hr>

                            <h6>Versements fonds</h6>
                            <div class="d-flex justify-content-between">
                                <p class="amount-usd">
                                    <?= number_format($total_versements_usd,2) ?> USD
                                </p>

                                <p class="amount-cdf">
                                    <?= number_format($total_versements_cdf,2) ?> CDF
                                </p>
                            </div>

                            <hr>

                            <h6>Gestion des compte</h6>
                            <div class="d-flex justify-content-between">
                                <p class="amount-usd">
                                    <?= number_format($total_gestion_des_comptes_usd, 2) ?> USD
                                </p>

                                <p class="amount-cdf">
                                    <?= number_format($total_gestion_des_comptes_cdf, 2) ?> CDF
                                </p>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- ENTREES CULTES -->

        <div class="card shadow-sm border-0 mb-4">

            <div class="card-header bg-primary text-white">

                💵 Entrées des cultes

            </div>

            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>
                            <th>Date</th>
                            <th>Thème</th>
                            <th>Offrandes + Dîmes</th>
                            <th>Sociale</th>
                            <th>Autres</th>
                            <!-- <th>Devise</th> -->
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach($entrees as $e): ?>

                        <tr>

                            <td><?= $e['date_culte'] ?></td>

                            <td><?= htmlspecialchars($e['theme']) ?></td>

                            <td class="text-success fw-bold">
                                <?= number_format($e['offrande_dime'],2) ?> <?= $e['devise'] ?>
                            </td>

                            <td class="text-warning fw-bold">
                                <?= number_format($e['sociale'],2) ?> <?= $e['devise'] ?>
                            </td>

                            <td class="fw-bold">
                                <?= number_format($e['autres'],2) ?> <?= $e['devise'] ?>
                            </td>

                            <!-- <td>
                                <span class="badge bg-dark">
                                    <?= $e['devise'] ?>
                                </span>
                            </td> -->

                        </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>


        <!-- VERSEMENTS -->

        <div class="card shadow-sm border-0 mb-4">

            <div class="card-header bg-success text-white">

                🤝 Versements fonds

            </div>

            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>
                            <th>Date</th>
                            <th>Type contribution</th>
                            <th>Souscripteur</th>
                            <th>Montant</th>
                            <th>Commentaire</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach($versements as $v): ?>

                        <tr>

                            <td><?= $v['date_versement'] ?></td>
                            <td>
                                <?= htmlspecialchars($v['engagement']) ?>
                            </td>
                            <td><?= htmlspecialchars($v['nom']) ?></td>

                            <td class="text-success fw-bold">
                                <?= number_format($v['montant'],2) ?> <?= $v['devise'] ?>
                            </td>



                            <td>
                                <?= htmlspecialchars($v['commentaire']) ?>
                            </td>

                        </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>


        <!-- COMPTE MOUVEMENTS -->

        <div class="card shadow-sm border-0 mb-4">

            <div class="card-header bg-info text-white">

                🏦 Mouvements des comptes

            </div>

            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>
                            <th>Date</th>
                            <th>Compte</th>
                            <th>Fidèle</th>
                            <th>Type</th>
                            <th>Montant</th>
                            <!-- <th>Devise</th> -->
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach($compte_mouvements as $cm): ?>

                        <tr>

                            <td><?= $cm['date_mouvement'] ?></td>

                            <td><?= htmlspecialchars($cm['compte_nom']) ?></td>

                            <td>
                                <?= htmlspecialchars(
                                    trim(
                                        $cm['nom'] . ' ' .
                                        $cm['postnom'] . ' ' .
                                        $cm['prenom']
                                    )
                                ) ?>
                            </td>

                            <!-- <td>

                                <?php if($cm['TYPE'] == 'ENTREE'): ?>

                                <span class="badge bg-success">
                                    ENTREE
                                </span>

                                <?php else: ?>

                                <span class="badge bg-danger">
                                    SORTIE
                                </span>

                                <?php endif; ?>

                            </td> -->

                            <td class="fw-bold">
                                <?= number_format($cm['montant'],2) ?> <?= $cm['devise'] ?>
                            </td>

                            <!-- <td>
                                <span class="badge bg-dark">
                                    <?= $cm['devise'] ?>
                                </span>
                            </td> -->

                        </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>


        <!-- DEPENSES -->

        <div class="card shadow-sm border-0 mb-5">

            <div class="card-header bg-danger text-white">

                💸 Dépenses du mois

            </div>

            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>
                            <th>Date</th>
                            <th>Motif</th>
                            <th>Bénéficiaire</th>
                            <th>Montant</th>
                            <!-- <th>Devise</th> -->
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach($depenses as $d): ?>

                        <tr>

                            <td><?= $d['date_depense'] ?></td>

                            <td><?= htmlspecialchars($d['motif']) ?></td>

                            <td><?= htmlspecialchars($d['beneficiaire']) ?></td>

                            <td class="text-danger fw-bold">
                                <?= number_format($d['montant'],2) ?> <?= $d['devise'] ?>
                            </td>

                            <!-- <td>
                                <span class="badge bg-dark">
                                    <?= $d['devise'] ?>
                                </span>
                            </td> -->

                        </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<?php require_once '../../layouts/footer.php'; ?>