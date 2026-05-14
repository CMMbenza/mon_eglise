<?php

require_once '../../core/auth.php';
require_once '../../config/database.php';

$fonds_id = (int)($_GET['id'] ?? 0);


// ======================================
// INFOS FONDS
// ======================================

$stmt = $pdo->prepare("
    SELECT *
    FROM fonds
    WHERE id = ?
");

$stmt->execute([$fonds_id]);

$fonds = $stmt->fetch();


// ======================================
// HISTORIQUE PAIEMENTS
// ======================================

$paiements = $pdo->prepare("
    SELECT
        vf.*,
        u.nom,
        f.nom AS nom_fidele,
        f.postnom,
        f.prenom,
        ef.montant_engage

    FROM versements_fonds vf

    INNER JOIN engagements_fonds ef
        ON ef.id = vf.engagement_id

    INNER JOIN users u
        ON u.id = ef.user_id

    LEFT JOIN fideles f
        ON f.user_id = u.id

    WHERE ef.fonds_id = ?

    ORDER BY vf.id DESC
");

$paiements->execute([$fonds_id]);


require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';
?>

<div class="container-fluid p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="fw-bold mb-1">

                Historique des paiements

            </h3>

            <p class="text-muted mb-0">

                <?= htmlspecialchars($fonds['campagne']) ?>

            </p>

        </div>

        <a href="index.php" class="btn btn-secondary">

            Retour

        </a>

    </div>


    <div class="card shadow-sm border-0 rounded-4">

        <div class="card-body table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-dark">

                    <tr>

                        <th>Fidèle</th>
                        <th>Engagement</th>
                        <th>Montant payé</th>
                        <th>Date</th>
                        <th>Commentaire</th>

                    </tr>

                </thead>

                <tbody>

                    <?php foreach($paiements as $p): ?>

                    <tr>

                        <td>

                            <?= htmlspecialchars(
                                trim(
                                    ($p['nom_fidele'] ?? '') . ' ' .
                                    ($p['postnom'] ?? '') . ' ' .
                                    ($p['prenom'] ?? '')
                                )
                            ) ?>

                        </td>

                        <td>

                            <?= number_format($p['montant_engage'],2) ?> $

                        </td>

                        <td class="fw-bold text-success">

                            <?= number_format($p['montant'],2) ?> $

                        </td>

                        <td>

                            <?= $p['date_versement'] ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($p['commentaire'] ?? '-') ?>

                        </td>

                    </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php require_once '../../layouts/footer.php'; ?>