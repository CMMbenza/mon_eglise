<?php

require_once '../../core/auth.php';
require_once '../../config/database.php';

$fonds_id = (int)($_GET['id'] ?? 0);

$success = '';
$error = '';


// ======================================
// FONDS
// ======================================

$stmt = $pdo->prepare("
    SELECT *
    FROM fonds
    WHERE id = ?
");
$stmt->execute([$fonds_id]);
$fonds = $stmt->fetch();

if(!$fonds){
    die("Fonds introuvable");
}

$devise = $fonds['devise'] ?? 'CDF';


// ======================================
// ENGAGEMENTS ACTIFS UNIQUEMENT
// ======================================

$engagements = $pdo->prepare("
    SELECT ef.*, f.nom, f.postnom, f.prenom
    FROM engagements_fonds ef
    LEFT JOIN fideles f ON f.user_id = ef.user_id
    WHERE ef.fonds_id = ?
    AND ef.cycle_actif = 1
    ORDER BY ef.id DESC
");
$engagements->execute([$fonds_id]);


// ======================================
// SAVE PAIEMENT
// ======================================

if(isset($_POST['save'])){

    $engagement_id = (int)$_POST['engagement_id'];
    $montant = (float)$_POST['montant'];
    $date_versement = $_POST['date_versement'];
    $commentaire = trim($_POST['commentaire']);

    try {

        // 1. engagement
        $stmt = $pdo->prepare("
            SELECT *
            FROM engagements_fonds
            WHERE id = ?
        ");
        $stmt->execute([$engagement_id]);
        $eng = $stmt->fetch();

        if(!$eng){
            throw new Exception("Engagement invalide");
        }

        // 2. total payé
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(montant),0)
            FROM versements_fonds
            WHERE engagement_id = ?
        ");
        $stmt->execute([$engagement_id]);
        $total_paye = $stmt->fetchColumn();

        $reste = $eng['montant_engage'] - $total_paye;

        if($montant > $reste){
            throw new Exception("Montant supérieur au reste ($reste $devise)");
        }

        // 3. INSERT paiement
        $stmt = $pdo->prepare("
            INSERT INTO versements_fonds(
                engagement_id,
                montant,
                commentaire,
                date_versement,
                devise
            )
            VALUES(?,?,?,?,?)
        ");
        $stmt->execute([
            $engagement_id,
            $montant,
            $commentaire,
            $date_versement,
            $devise
        ]);

        $success = "Paiement enregistré.";

        // ======================================
        // RE-CALCUL RESTE APRÈS PAIEMENT
        // ======================================

        $total_paye += $montant;
        $reste_final = $eng['montant_engage'] - $total_paye;

        // ======================================
        // SI CYCLE TERMINÉ → RENOUVELER
        // ======================================

        if($reste_final <= 0){

            // clôture cycle actuel
            $stmt = $pdo->prepare("
                UPDATE engagements_fonds
                SET cycle_actif = 0
                WHERE id = ?
            ");
            $stmt->execute([$engagement_id]);

            // nouveau cycle
            $stmt = $pdo->prepare("
                INSERT INTO engagements_fonds(
                    user_id,
                    fonds_id,
                    montant_engage,
                    periode,
                    description_periode,
                    cycle_actif,
                    cycle_num,
                    date_debut,
                    devise
                )
                VALUES(?,?,?,?,?,?,?,?,?)
            ");

            $stmt->execute([
                $eng['user_id'],
                $eng['fonds_id'],
                $eng['montant_engage'],
                $eng['periode'],
                'Renouvellement automatique',
                1,
                ($eng['cycle_num'] + 1),
                date('Y-m-d'),
                $devise
            ]);
        }

    } catch(Exception $e){
        $error = $e->getMessage();
    }
}

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';
?>

<div class="container-fluid p-4">

    <div class="row justify-content-center">

        <div class="col-lg-7">

            <div class="card shadow-sm border-0 rounded-4">

                <div class="card-header bg-dark text-white py-3">
                    <h4 class="mb-0">Enregistrer un paiement</h4>
                </div>

                <div class="card-body p-4">

                    <?php if($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>

                    <?php if($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Fonds</label>
                        <input type="text" class="form-control"
                            value="<?= htmlspecialchars($fonds['campagne']) ?>" readonly>
                    </div>

                    <form method="POST">

                        <div class="mb-3">

                            <label>Engagement</label>

                            <select name="engagement_id" class="form-select" required>

                                <option value="">-- Sélectionner --</option>

                                <?php foreach($engagements as $e): ?>

                                <?php
                                    $stmt = $pdo->prepare("
                                        SELECT COALESCE(SUM(montant),0)
                                        FROM versements_fonds
                                        WHERE engagement_id = ?
                                    ");
                                    $stmt->execute([$e['id']]);
                                    $paye = $stmt->fetchColumn();
                                    $reste = $e['montant_engage'] - $paye;
                                    ?>

                                <option value="<?= $e['id'] ?>">
                                    <?= htmlspecialchars($e['nom'].' '.$e['prenom']) ?>
                                    - reste: <?= number_format($reste,2) ?> <?= $devise ?>
                                </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div class="mb-3">
                            <label>Montant <?= $devise ?></label>
                            <input type="number" step="0.01" name="montant" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Date</label>
                            <input type="date" name="date_versement" class="form-control" value="<?= date('Y-m-d') ?>"
                                required>
                        </div>

                        <div class="mb-3">
                            <label>Commentaire</label>
                            <textarea name="commentaire" class="form-control"></textarea>
                        </div>

                        <button class="btn btn-success" name="save">
                            Enregistrer
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once '../../layouts/footer.php'; ?>