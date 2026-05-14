<?php

require_once '../../core/auth.php';
require_once '../../config/database.php';

$page_title = "Rapports";

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';

?>

<div class="d-flex">

    <div class="container-fluid p-4">

        <h2 class="mb-4">📊 Rapports généraux & mensuels</h2>

        <?php require_once '../../layouts/alerts.php'; ?>


        <?php
// =======================
// GLOBALS
// =======================

$total_offrande = $pdo->query("SELECT SUM(offrande) FROM cultes")->fetchColumn();
$total_dime = $pdo->query("SELECT SUM(dime) FROM cultes")->fetchColumn();
$total_sociale = $pdo->query("SELECT SUM(sociale) FROM cultes")->fetchColumn();
$total_autres = $pdo->query("SELECT SUM(autres) FROM cultes")->fetchColumn();

$total_depenses = $pdo->query("SELECT SUM(montant) FROM depenses")->fetchColumn();

$total_fonds = $pdo->query("SELECT SUM(montant) FROM fonds WHERE statut='valide'")->fetchColumn();

$entrees = $total_offrande + $total_dime + $total_sociale + $total_autres + $total_fonds;

$solde_global = $entrees - $total_depenses;


// =======================
// RAPPORT MENSUEL
// =======================

$mois = $pdo->query("

SELECT 
    DATE_FORMAT(date_culte, '%Y-%m') AS mois,
    SUM(offrande + dime + sociale + autres) AS entrees_culte
FROM cultes
GROUP BY mois
ORDER BY mois DESC

")->fetchAll();

$depenses_mensuelles = $pdo->query("

SELECT 
    DATE_FORMAT(date_depense, '%Y-%m') AS mois,
    SUM(montant) AS sorties
FROM depenses
GROUP BY mois

")->fetchAll(PDO::FETCH_KEY_PAIR);

?>

        <!-- ===================== -->
        <!-- CARDS -->
        <!-- ===================== -->
        <div class="row g-4">

            <div class="col-md-4">
                <div class="card shadow border-0">
                    <div class="card-header bg-primary text-white">📖 Cultes</div>
                    <div class="card-body">

                        <p>Offrandes : <strong><?= $total_offrande ?> $</strong></p>
                        <p>Dîmes : <strong><?= $total_dime ?> $</strong></p>
                        <p>Sociale : <strong><?= $total_sociale ?> $</strong></p>
                        <p>Autres : <strong><?= $total_autres ?> $</strong></p>

                    </div>
                </div>
            </div>


            <div class="col-md-4">
                <div class="card shadow border-0">
                    <div class="card-header bg-success text-white">💰 Finances</div>
                    <div class="card-body">

                        <p>Entrées : <strong><?= $entrees ?> $</strong></p>
                        <p>Sorties : <strong><?= $total_depenses ?> $</strong></p>

                        <hr>

                        <h4 class="<?= $solde_global >= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= $solde_global ?> $
                        </h4>

                    </div>
                </div>
            </div>


            <div class="col-md-4">
                <div class="card shadow border-0">
                    <div class="card-header bg-dark text-white">📌 Global</div>
                    <div class="card-body">

                        <?php
$total_fideles = $pdo->query("SELECT COUNT(*) FROM fideles")->fetchColumn();
$total_annonces = $pdo->query("SELECT COUNT(*) FROM annonces")->fetchColumn();
?>

                        <p>Fidèles : <strong><?= $total_fideles ?></strong></p>
                        <p>Annonces : <strong><?= $total_annonces ?></strong></p>

                    </div>
                </div>
            </div>

        </div>
        <!-- ===================== -->
        <!-- TABLE MENSUELLE -->
        <!-- ===================== -->

        <div class="mt-5">

            <h3>📅 Rapport mensuel détaillé</h3>

            <table class="table table-bordered table-hover mt-3">

                <thead class="table-dark">

                    <tr>
                        <th>Mois</th>
                        <th>Entrées</th>
                        <th>Sorties</th>
                        <th>Solde</th>
                        <th>Action</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach($mois as $m): ?>

                    <?php
$mois_key = $m['mois'];


// =======================
// CULTE DETAILS
// =======================

$cultesData = $pdo->prepare("
SELECT 
SUM(offrande) as offrande,
SUM(dime) as dime,
SUM(sociale) as sociale,
SUM(autres) as autres,
SUM(hommes) as hommes,
SUM(femmes) as femmes
FROM cultes
WHERE DATE_FORMAT(date_culte,'%Y-%m') = ?
");
$cultesData->execute([$mois_key]);
$culte = $cultesData->fetch();

$total_cultes_entrees =
($culte['offrande'] ?? 0) +
($culte['dime'] ?? 0) +
($culte['sociale'] ?? 0) +
($culte['autres'] ?? 0);


// =======================
// FONDS
// =======================

$fonds = $pdo->prepare("
SELECT SUM(montant)
FROM fonds
WHERE statut='valide'
AND DATE_FORMAT(created_at,'%Y-%m') = ?
");
$fonds->execute([$mois_key]);
$total_fonds = $fonds->fetchColumn() ?? 0;


// =======================
// ENTREES TOTAL
// =======================

$entrees_mois = $total_cultes_entrees + $total_fonds;


// =======================
// DEPENSES
// =======================

$dep = $pdo->prepare("
SELECT 
SUM(montant) as total_dep,
GROUP_CONCAT(CONCAT(date_depense,' | ',motif,' | ',montant,'$') SEPARATOR '<br>') as details
FROM depenses
WHERE DATE_FORMAT(date_depense,'%Y-%m') = ?
");
$dep->execute([$mois_key]);
$depData = $dep->fetch();

$sorties_mois = $depData['total_dep'] ?? 0;


// =======================
// SOLDE
// =======================

$solde_mois = $entrees_mois - $sorties_mois;


// =======================
// FIDELES + ANNONCES
// =======================

$fid = $pdo->prepare("
SELECT COUNT(*)
FROM fideles
WHERE DATE_FORMAT(created_at,'%Y-%m') = ?
");
$fid->execute([$mois_key]);
$total_fideles = $fid->fetchColumn();

$ann = $pdo->prepare("
SELECT COUNT(*)
FROM annonces
WHERE DATE_FORMAT(created_at,'%Y-%m') = ?
");
$ann->execute([$mois_key]);
$total_annonces = $ann->fetchColumn();

?>

                    <tr>

                        <td><?= $mois_key ?></td>

                        <td class="text-success">
                            <?= $entrees_mois ?> $
                        </td>

                        <td class="text-danger">
                            <?= $sorties_mois ?> $
                        </td>

                        <td class="<?= $solde_mois >= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= $solde_mois ?> $
                        </td>

                        <td>

                            <button class="btn btn-sm btn-primary" data-bs-toggle="collapse"
                                data-bs-target="#detail<?= $mois_key ?>">

                                Voir

                            </button>

                        </td>

                    </tr>

                    <!-- ===================== -->
                    <!-- DÉTAIL COMPLET -->
                    <!-- ===================== -->

                    <tr class="collapse" id="detail<?= $mois_key ?>">

                        <td colspan="5">

                            <div class="p-3 bg-light">

                                <h4>📊 Rapport détaillé du mois <?= $mois_key ?></h4>

                                <hr>

                                <!-- ===================== -->
                                <!-- CULTE -->
                                <!-- ===================== -->

                                <h5>📖 Cultes</h5>

                                <p>
                                    <strong>Offrandes :</strong> <?= $culte['offrande'] ?? 0 ?> $ <br>
                                    <strong>Dîmes :</strong> <?= $culte['dime'] ?? 0 ?> $ <br>
                                    <strong>Sociale :</strong> <?= $culte['sociale'] ?? 0 ?> $ <br>
                                    <strong>Autres :</strong> <?= $culte['autres'] ?? 0 ?> $
                                </p>

                                <p class="text-primary">
                                    <strong>Total entrées cultes :</strong>
                                    <?= $total_cultes_entrees ?> $
                                </p>

                                <p>
                                    👨 Hommes : <?= $culte['hommes'] ?? 0 ?> <br>
                                    👩 Femmes : <?= $culte['femmes'] ?? 0 ?> <br>
                                    👥 Total fidèles :
                                    <?= ($culte['hommes'] ?? 0) + ($culte['femmes'] ?? 0) ?>
                                </p>

                                <hr>

                                <!-- ===================== -->
                                <!-- DEPENSES -->
                                <!-- ===================== -->

                                <h5>💰 Dépenses</h5>

                                <p>
                                    <strong>Total :</strong> <?= $sorties_mois ?> $
                                </p>

                                <div class="small">

                                    <?= $depData['details'] ?? 'Aucune dépense' ?>

                                </div>

                                <hr>

                                <!-- ===================== -->
                                <!-- AUTRES -->
                                <!-- ===================== -->

                                <h5>📌 Autres informations</h5>

                                <p>👥 Fidèles enregistrés : <strong><?= $total_fideles ?></strong></p>

                                <p>📢 Annonces : <strong><?= $total_annonces ?></strong></p>

                                <hr>

                                <h4>
                                    📊 Solde du mois :
                                    <span class="<?= $solde_mois >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <?= $solde_mois ?> $
                                    </span>
                                </h4>

                            </div>

                        </td>

                    </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php require_once '../../layouts/footer.php'; ?>