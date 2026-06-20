<?php

require_once '../../core/auth.php';
require_once '../../config/database.php';

$id = $_GET['id'] ?? 0;


// ======================================
// DETAILS FONDS
// ======================================

$stmt = $pdo->prepare("
    SELECT *
    FROM fonds
    WHERE id = ?
");
$stmt->execute([$id]);
$f = $stmt->fetch();

if(!$f){
    $_SESSION['error'] = "Contribution introuvable.";
    header("Location: index.php");
    exit;
}

$devise = $f['devise'];


// ======================================
// NOMBRE SOUSCRIPTIONS
// ======================================

$stmtSouscriptions = $pdo->prepare("
    SELECT COUNT(DISTINCT ef.user_id)
    FROM engagements_fonds ef
    WHERE fonds_id = ?
");
$stmtSouscriptions->execute([$id]);
$nbre_souscriptions = $stmtSouscriptions->fetchColumn();


// ======================================
// TOTAL ENGAGE
// ======================================

$stmtMontant = $pdo->prepare("
    SELECT SUM(montant_engage)
    FROM engagements_fonds
    WHERE fonds_id = ?
");
$stmtMontant->execute([$id]);
$total_engage = $stmtMontant->fetchColumn() ?? 0;


// ======================================
// TOTAL VERSE
// ======================================

$stmtVersements = $pdo->prepare("
    SELECT SUM(vf.montant)
    FROM versements_fonds vf
    INNER JOIN engagements_fonds ef
        ON ef.id = vf.engagement_id
    WHERE ef.fonds_id = ?
");
$stmtVersements->execute([$id]);
$total_verse = $stmtVersements->fetchColumn() ?? 0;


// ======================================
// SOLDE
// ======================================

$reste = $total_engage - $total_verse;


// ======================================
// VERSEMENTS MENSUELS
// ======================================

$stmtMensuel = $pdo->prepare("
    SELECT
        DATE_FORMAT(vf.date_versement, '%Y-%m') AS mois,
        SUM(vf.montant) AS total_mois
    FROM versements_fonds vf
    INNER JOIN engagements_fonds ef
        ON ef.id = vf.engagement_id
    WHERE ef.fonds_id = ?
    GROUP BY mois
    ORDER BY mois DESC
");

$stmtMensuel->execute([$id]);
$mensuel = $stmtMensuel->fetchAll();


// ======================================
// ENGAGEMENTS
// ======================================

$engagements = $pdo->prepare("
    SELECT ef.*, u.nom
    FROM engagements_fonds ef
    INNER JOIN users u ON u.id = ef.user_id
    WHERE ef.fonds_id = ?
    GROUP BY ef.user_id
    ORDER BY ef.id DESC
    LIMIT 10
");
$engagements->execute([$id]);


// ======================================
// DERNIERS VERSEMENTS
// ======================================

$versements = $pdo->prepare("
    SELECT vf.*, u.nom, ef.montant_engage
    FROM versements_fonds vf
    INNER JOIN engagements_fonds ef ON ef.id = vf.engagement_id
    INNER JOIN users u ON u.id = ef.user_id
    WHERE ef.fonds_id = ?
    ORDER BY vf.id DESC
    LIMIT 10
");
$versements->execute([$id]);

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';
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
    transition: .3s;
}

.stat-card:hover {
    transform: translateY(-3px);
}

.icon-box {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
}

.table thead th {
    background: #212529;
    color: white;
    border: none;
    white-space: nowrap;
}

.badge-soft {
    background: rgba(13, 110, 253, .1);
    color: #0d6efd;
    padding: 8px 14px;
    border-radius: 30px;
    font-size: 13px;
}
</style>

<div class="container-fluid p-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="fw-bold mb-1">
                <i class="bi bi-bank2 text-primary"></i>
                Détails contribution
            </h3>

            <p class="text-muted mb-0">
                Informations générales de la campagne
            </p>

        </div>

        <a href="index.php" class="btn btn-dark">
            <i class="bi bi-arrow-left"></i>
            Retour
        </a>

    </div>

    <div class="d-flex flex-wrap gap-2 mt-3 mb-3">

        <a href="souscrire_fidele.php?fonds_id=<?= $f['id'] ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus-fill"></i> Souscrire un fidèle
        </a>

        <a href="historique_paiements.php?id=<?= $f['id'] ?>" class="btn btn-success btn-sm">
            <i class="bi bi-clock-history"></i> Historique
        </a>

        <a href="enregistrer_paiement.php?id=<?= $f['id'] ?>" class="btn btn-dark btn-sm">
            <i class="bi bi-cash-coin"></i> Effectuer le Paiement
        </a>

        <a href="create_update_delete.php?action=edit&id=<?= $f['id'] ?>" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil-fill"></i> Modifier
        </a>

        <a href="create_update_delete.php?action=delete&id=<?= $f['id'] ?>" class="btn btn-danger btn-sm"
            onclick="return confirm('Supprimer cette contribution ?')">
            <i class="bi bi-trash-fill"></i> Supprimer
        </a>

    </div>

    <!-- DETAILS -->
    <div class="card shadow-sm page-card mb-4">

        <div class="card-body p-4">

            <div class="row g-4">

                <div class="col-md-6">

                    <small class="text-muted">
                        Campagne
                    </small>

                    <h4 class="fw-bold mt-1">
                        <?= htmlspecialchars($f['campagne']) ?>
                    </h4>

                </div>

                <div class="col-md-3">

                    <small class="text-muted">
                        Montant cible
                    </small>

                    <h4 class="text-success fw-bold mt-1">
                        <?= htmlspecialchars($f['montant']) ?> <?= $f['devise'] ?>
                    </h4>

                </div>

                <div class="col-md-3">

                    <small class="text-muted">
                        Date création
                    </small>

                    <h6 class="mt-2">
                        <?= date('d/m/Y', strtotime($f['created_at'])) ?>
                    </h6>

                </div>

                <div class="col-12">

                    <small class="text-muted">
                        Description / motif
                    </small>

                    <div class="bg-light rounded p-3 mt-2 border">

                        <?= nl2br(htmlspecialchars($f['motif'])) ?>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- STATS -->
    <div class="row g-4 mb-4">

        <!-- SOUSCRIPTIONS -->
        <div class="col-xl-3 col-md-6">

            <div class="card shadow-sm stat-card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <small class="text-muted">
                                Souscriptions
                            </small>

                            <h3 class="fw-bold mt-2">
                                <?= $nbre_souscriptions ?>
                            </h3>

                        </div>

                        <div class="icon-box bg-primary-subtle text-primary">

                            <i class="bi bi-people-fill"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- ENGAGE -->
        <div class="col-xl-3 col-md-6">

            <div class="card shadow-sm stat-card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <small class="text-muted">
                                Total engagé
                            </small>

                            <h3 class="fw-bold text-primary mt-2">
                                <?= number_format($total_engage,2) ?> <?= $f['devise'] ?>
                            </h3>

                        </div>

                        <div class="icon-box bg-info-subtle text-info">

                            <i class="bi bi-wallet2"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- VERSE -->
        <div class="col-xl-3 col-md-6">

            <div class="card shadow-sm stat-card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <small class="text-muted">
                                Total versé
                            </small>

                            <h3 class="fw-bold text-success mt-2">
                                <?= number_format($total_verse,2) ?> <?= $f['devise'] ?>
                            </h3>

                        </div>

                        <div class="icon-box bg-success-subtle text-success">

                            <i class="bi bi-cash-stack"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- RESTE -->
        <div class="col-xl-3 col-md-6">

            <div class="card shadow-sm stat-card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <small class="text-muted">
                                Solde restant
                            </small>

                            <h3 class="fw-bold text-danger mt-2">
                                <?= number_format($reste,2) ?> <?= $f['devise'] ?>
                            </h3>

                        </div>

                        <div class="icon-box bg-danger-subtle text-danger">

                            <i class="bi bi-graph-down-arrow"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ENGAGEMENTS -->
    <div class="card shadow-sm page-card mb-5">

        <div class="card-header bg-primary text-white py-3">

            <i class="bi bi-clipboard-check-fill"></i>
            Derniers engagements

        </div>

        <div class="card-body table-responsive">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>
                        <th>Fidèle</th>
                        <th>Montant engagé</th>
                        <th>Période</th>
                        <th>Statut</th>
                        <th>Date début</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach($engagements as $e): ?>

                    <tr>

                        <td>

                            <?= htmlspecialchars($e['nom']) ?>

                        </td>

                        <td class="fw-bold text-primary">

                            <?= number_format($e['montant_engage'],2) ?> <?= $f['devise'] ?>

                        </td>

                        <td>

                            <span class="badge-soft">

                                <?= htmlspecialchars($e['periode']) ?>

                            </span>

                        </td>

                        <td>

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

                        </td>

                        <td>

                            <?= $e['date_debut'] ?>

                        </td>

                    </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

    <!-- VERSEMENTS -->
    <div class="card shadow-sm page-card mb-5">

        <div class="card-header bg-success text-white py-3">

            <i class="bi bi-cash-coin"></i>
            Historique des versements

        </div>

        <div class="card-body table-responsive">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>
                        <th>Fidèle</th>
                        <th>Engagement</th>
                        <th>Montant payé</th>
                        <th>Commentaire</th>
                        <th>Date</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach($versements as $v): ?>

                    <tr>

                        <td>

                            <?= htmlspecialchars($v['nom']) ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($v['montant_engage']) ?> <?= $f['devise'] ?>

                        </td>

                        <td class="fw-bold text-success">

                            <?= htmlspecialchars($v['montant']) ?> <?= $f['devise'] ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($v['commentaire'] ?? '-') ?>

                        </td>

                        <td>

                            <?= $v['date_versement'] ?>

                        </td>

                    </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

    <!-- TABLE MENSUELLE -->
    <div class="card shadow-sm page-card mb-5">

        <div class="card-header bg-dark text-white py-3">
            <i class="bi bi-bar-chart-line"></i>
            Versements mensuels
        </div>

        <div class="card-body table-responsive">

            <table class="table table-hover align-middle">

                <thead>
                    <tr>
                        <th>Mois</th>
                        <th>Total versé</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach($mensuel as $m): ?>

                    <tr>
                        <td>
                            <?= $m['mois'] ?>
                        </td>

                        <td class="fw-bold text-success">
                            <?= number_format($m['total_mois'],2) ?> <?= $devise ?>
                        </td>
                    </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php require_once '../../layouts/footer.php'; ?>