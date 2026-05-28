<?php

require_once '../auth_visiteurs.php';
require_once '../../config/database.php';

$user_id = $_SESSION['user']['id'];

$page_title = "Mes engagements & paiements";


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
// MES PAIEMENTS
// =====================================

$paiements = $pdo->prepare("
    SELECT
        vf.*,
        ef.montant_engage,
        ef.periode,
        ef.devise AS devise_engagement,
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
// TOTAL ENGAGE
// =====================================

$total_engage = sommeDevise($pdo, "
    SELECT
        devise,
        COALESCE(SUM(montant_engage),0) AS total
    FROM engagements_fonds
    WHERE user_id = ?
    GROUP BY devise
", [$user_id]);


// =====================================
// TOTAL PAYE
// =====================================

$total_paye = sommeDevise($pdo, "
    SELECT
        vf.devise,
        COALESCE(SUM(vf.montant),0) AS total
    FROM versements_fonds vf

    INNER JOIN engagements_fonds ef
        ON ef.id = vf.engagement_id

    WHERE ef.user_id = ?

    GROUP BY vf.devise
", [$user_id]);


// =====================================
// SOLDES
// =====================================

$reste_usd =
    ($total_engage['USD'] ?? 0)
    - ($total_paye['USD'] ?? 0);

$reste_cdf =
    ($total_engage['CDF'] ?? 0)
    - ($total_paye['CDF'] ?? 0);


// =====================================
// MOUVEMENTS COMPTE
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

    ORDER BY cm.id DESC
");

$compte_mouvements->execute([$user_id]);


// =====================================
// TOTAL ENTREES
// =====================================

$total_entrees = sommeDevise($pdo, "
    SELECT
        devise,
        COALESCE(SUM(montant),0) AS total
    FROM compte_mouvements cm

    LEFT JOIN fideles f
        ON f.id = cm.fidele_id

    WHERE f.user_id = ?
    AND TYPE = 'ENTREE'

    GROUP BY devise
", [$user_id]);


// =====================================
// TOTAL SORTIES
// =====================================

$total_sorties = sommeDevise($pdo, "
    SELECT
        devise,
        COALESCE(SUM(montant),0) AS total
    FROM compte_mouvements cm

    LEFT JOIN fideles f
        ON f.id = cm.fidele_id

    WHERE f.user_id = ?
    AND TYPE = 'SORTIE'

    GROUP BY devise
", [$user_id]);


require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar_fideles.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
.page-card {
    border: none;
    border-radius: 18px;
    overflow: hidden;
}

.stat-card {
    border: none;
    border-radius: 18px;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 25px;
}

.table th {
    white-space: nowrap;
}

.badge-soft {
    background: rgba(13, 110, 253, .1);
    color: #0d6efd;
    padding: 8px 14px;
    border-radius: 30px;
}
</style>

<div class="container-fluid p-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                Mes engagements & paiements
            </h2>

            <p class="text-muted mb-0">
                Consultez vos engagements financiers et vos versements
            </p>

        </div>

        <a href="../contributions_disponibles" class="btn btn-primary">

            <i class="bi bi-plus-circle-fill"></i>
            Voir d'autre contributions disponibles

        </a>

    </div>


    <!-- ===================== -->
    <!-- STATS -->
    <!-- ===================== -->

    <div class="row g-4 mb-4">

        <!-- TOTAL ENGAGE -->
        <div class="col-lg-4 col-md-6">

            <div class="card shadow-sm stat-card">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <small class="text-muted">
                                Total engagé
                            </small>

                            <h5 class="mt-2 text-primary fw-bold">
                                <?= number_format($total_engage['USD'] ?? 0, 2) ?> USD
                            </h5>

                            <h5 class="text-primary fw-bold">
                                <?= number_format($total_engage['CDF'] ?? 0, 2) ?> CDF
                            </h5>

                        </div>

                        <div class="stat-icon bg-primary-subtle text-primary">

                            <i class="bi bi-wallet2"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- TOTAL PAYE -->
        <div class="col-lg-4 col-md-6">

            <div class="card shadow-sm stat-card">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <small class="text-muted">
                                Total payé
                            </small>

                            <h5 class="mt-2 text-success fw-bold">
                                <?= number_format($total_paye['USD'] ?? 0, 2) ?> USD
                            </h5>

                            <h5 class="text-success fw-bold">
                                <?= number_format($total_paye['CDF'] ?? 0, 2) ?> CDF
                            </h5>

                        </div>

                        <div class="stat-icon bg-success-subtle text-success">

                            <i class="bi bi-cash-stack"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- RESTE -->
        <div class="col-lg-4 col-md-6">

            <div class="card shadow-sm stat-card">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <small class="text-muted">
                                Solde restant
                            </small>

                            <h5 class="mt-2 text-danger fw-bold">
                                <?= number_format($reste_usd, 2) ?> USD
                            </h5>

                            <h5 class="text-danger fw-bold">
                                <?= number_format($reste_cdf, 2) ?> CDF
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
    <!-- MES ENGAGEMENTS -->
    <!-- ===================== -->

    <div class="card shadow-sm page-card mb-4">

        <div class="card-header bg-info text-white py-3">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <i class="bi bi-clipboard-check-fill"></i>
                    Mes engagements

                </div>

                <span class="badge bg-light text-dark">

                    <?= $engagements->rowCount() ?> engagement(s)

                </span>

            </div>

        </div>

        <div class="card-body table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">

                    <tr>

                        <th>Campagne</th>
                        <th>Montant choisi</th>
                        <th>Période</th>
                        <th>Description</th>
                        <th>Début</th>
                        <th>Fin</th>
                        <!-- <th>Statut</th> -->

                    </tr>

                </thead>

                <tbody>

                    <?php if($engagements->rowCount() > 0): ?>

                    <?php foreach($engagements as $e): ?>

                    <tr>

                        <td>
                            <strong>
                                <?= htmlspecialchars($e['campagne']) ?>
                            </strong>
                        </td>

                        <td class="text-primary fw-bold">

                            <?= number_format($e['montant_engage'], 2) ?>
                            <?= htmlspecialchars($e['devise']) ?>

                        </td>

                        <td>

                            <span class="badge-soft">
                                <?= htmlspecialchars($e['periode']) ?>
                            </span>

                        </td>

                        <td>
                            <?= htmlspecialchars($e['description_periode'] ?? '-') ?>
                        </td>

                        <td>
                            <?= $e['date_debut'] ?: '-' ?>
                        </td>

                        <td>
                            <?= $e['date_fin'] ?: '-' ?>
                        </td>

                        <!-- <td>

                            <?php if($e['statut'] == 'En cours'): ?>

                            <span class="badge bg-success">
                                En cours
                            </span>

                            <?php elseif($e['statut'] == 'Suspendu'): ?>

                            <span class="badge bg-warning text-dark">
                                Suspendu
                            </span>

                            <?php else: ?>

                            <span class="badge bg-primary">
                                Terminé
                            </span>

                            <?php endif; ?>

                        </td> -->

                    </tr>

                    <?php endforeach; ?>

                    <?php else: ?>

                    <tr>

                        <td colspan="7" class="text-center text-muted py-4">
                            Aucun engagement trouvé.
                        </td>

                    </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>


    <!-- ===================== -->
    <!-- PAIEMENTS -->
    <!-- ===================== -->

    <div class="card shadow-sm page-card mb-4">

        <div class="card-header bg-dark text-white py-3">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <i class="bi bi-cash-coin"></i>
                    Paiements / versements

                </div>

                <span class="badge bg-light text-dark">

                    <?= $paiements->rowCount() ?> paiement(s)

                </span>

            </div>

        </div>

        <div class="card-body table-responsive">

            <table class="table table-striped align-middle">

                <thead>

                    <tr>

                        <th>Campagne</th>
                        <th>Engagement</th>
                        <th>Période</th>
                        <th>Montant payé</th>
                        <th>Date paiement</th>
                        <th>Commentaire</th>

                    </tr>

                </thead>

                <tbody>

                    <?php if($paiements->rowCount() > 0): ?>

                    <?php foreach($paiements as $p): ?>

                    <tr>

                        <td>
                            <strong>
                                <?= htmlspecialchars($p['campagne']) ?>
                            </strong>
                        </td>

                        <td>

                            <?= number_format($p['montant_engage'], 2) ?>
                            <?= htmlspecialchars($p['devise_engagement']) ?>

                        </td>

                        <td>
                            <?= htmlspecialchars($p['periode']) ?>
                        </td>

                        <td class="text-success fw-bold">

                            <?= number_format($p['montant'], 2) ?>
                            <?= htmlspecialchars($p['devise']) ?>

                        </td>

                        <td>
                            <?= $p['date_versement'] ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($p['commentaire'] ?? '-') ?>
                        </td>

                    </tr>

                    <?php endforeach; ?>

                    <?php else: ?>

                    <tr>

                        <td colspan="6" class="text-center text-muted py-4">
                            Aucun paiement enregistré.
                        </td>

                    </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>


    <!-- ===================== -->
    <!-- MOUVEMENTS DE COMPTE -->
    <!-- ===================== -->

    <div class="card shadow-sm page-card mb-5">

        <div class="card-header bg-primary text-white py-3">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <i class="bi bi-bank2"></i>
                    Mes mouvements de compte

                </div>

                <span class="badge bg-light text-dark">

                    <?= $compte_mouvements->rowCount() ?> mouvement(s)

                </span>

            </div>

        </div>

        <div class="card-body table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">

                    <tr>

                        <th>Date</th>
                        <th>Compte</th>
                        <!-- <th>Type</th> -->
                        <th>Montant</th>
                        <th>Motif</th>

                    </tr>

                </thead>

                <tbody>

                    <?php if($compte_mouvements->rowCount() > 0): ?>

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

                            <?= number_format($cm['montant'], 2) ?>
                            <?= htmlspecialchars($cm['devise']) ?>

                        </td>

                        <td>
                            <?= htmlspecialchars($cm['motif'] ?? '-') ?>
                        </td>

                    </tr>

                    <?php endforeach; ?>

                    <?php else: ?>

                    <tr>

                        <td colspan="5" class="text-center text-muted py-4">
                            Aucun mouvement trouvé.
                        </td>

                    </tr>

                    <?php endif; ?>

                </tbody>

                <!-- <tfoot>

                    <tr class="table-success">

                        <td colspan="3" class="fw-bold text-end">
                            Total entrées :
                        </td>

                        <td colspan="2" class="fw-bold">

                            <?= number_format($total_entrees['USD'] ?? 0, 2) ?> USD
                            <br>

                            <?= number_format($total_entrees['CDF'] ?? 0, 2) ?> CDF

                        </td>

                    </tr>

                    <tr class="table-danger">

                        <td colspan="3" class="fw-bold text-end">
                            Total sorties :
                        </td>

                        <td colspan="2" class="fw-bold">

                            <?= number_format($total_sorties['USD'] ?? 0, 2) ?> USD
                            <br>

                            <?= number_format($total_sorties['CDF'] ?? 0, 2) ?> CDF

                        </td>

                    </tr>

                </tfoot> -->

            </table>

        </div>

    </div>

</div>

<?php require_once '../../layouts/footer.php'; ?>