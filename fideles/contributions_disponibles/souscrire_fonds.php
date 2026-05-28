<?php

require_once '../auth_visiteurs.php';
require_once '../../config/database.php';

$user_id = $_SESSION['user']['id'];

$page_title = "Souscrire à un fonds";

$error = '';
$success = '';


// =======================================
// RECUPERATION FONDS
// =======================================

$fonds = $pdo->query("
    SELECT *
    FROM fonds
    ORDER BY created_at DESC
");


// =======================================
// FONDS SELECTIONNE
// =======================================

$selected_fonds_id = isset($_GET['fonds'])
    ? (int) $_GET['fonds']
    : 0;

$fonds_selected = null;

if ($selected_fonds_id > 0) {

    $stmt = $pdo->prepare("
        SELECT *
        FROM fonds
        WHERE id = ?
    ");

    $stmt->execute([$selected_fonds_id]);

    $fonds_selected = $stmt->fetch();
}


// =======================================
// ENREGISTREMENT
// =======================================

if(isset($_POST['submit'])){

    $fonds_id = (int) $_POST['fonds_id'];
    $montant_engage = trim($_POST['montant_engage']);
    $periode = trim($_POST['periode']);
    $description_periode = trim($_POST['description_periode']);
    $date_debut = trim($_POST['date_debut']);
    $date_fin = trim($_POST['date_fin']);

    if(empty($fonds_id) || empty($montant_engage) || empty($periode)){

        $error = "Veuillez remplir tous les champs obligatoires.";

    }else{

        // =======================================
        // RECUPERATION DEVISE DU FONDS
        // =======================================

        $stmtFonds = $pdo->prepare("
            SELECT devise
            FROM fonds
            WHERE id = ?
        ");

        $stmtFonds->execute([$fonds_id]);

        $fonds_data = $stmtFonds->fetch();

        $devise = $fonds_data['devise'] ?? 'CDF';


        // =======================================
        // INSERTION
        // =======================================

        $sql = "
            INSERT INTO engagements_fonds
            (
                user_id,
                fonds_id,
                montant_engage,
                periode,
                description_periode,
                date_debut,
                date_fin,
                devise
            )
            VALUES(?,?,?,?,?,?,?,?)
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $user_id,
            $fonds_id,
            $montant_engage,
            $periode,
            $description_periode,
            $date_debut,
            $date_fin,
            $devise
        ]);

        $_SESSION['success'] = "Votre engagement a été enregistré avec succès.";

        header('Location: index.php');
        exit;
    }
}

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar_fideles.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="container-fluid p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="fw-bold mb-1">
                Souscrire à un fonds
            </h3>

            <p class="text-muted mb-0">
                Engagez-vous à contribuer progressivement
            </p>

        </div>

        <a href="../dashboard.php" class="btn btn-dark">

            <i class="bi bi-arrow-left"></i>
            Retour

        </a>

    </div>


    <?php if($error): ?>

    <div class="alert alert-danger">

        <?= $error ?>

    </div>

    <?php endif; ?>


    <div class="card shadow-sm border-0">

        <div class="card-header bg-primary text-white py-3">

            <h5 class="mb-0">

                <i class="bi bi-cash-coin"></i>
                Formulaire d'engagement

            </h5>

        </div>

        <div class="card-body">

            <?php if($fonds_selected): ?>

            <div class="alert alert-info">

                <strong>Campagne :</strong>
                <?= htmlspecialchars($fonds_selected['campagne']) ?>

                <br>

                <strong>Objectif :</strong>
                <?= htmlspecialchars($fonds_selected['montant'],2) ?>
                <?= htmlspecialchars($fonds_selected['devise']) ?>

                <br>

                <strong>Description :</strong>
                <?= nl2br(htmlspecialchars($fonds_selected['motif'])) ?>

            </div>

            <?php endif; ?>

            <form method="POST">

                <div class="row">

                    <!-- FONDS -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Fonds / Campagne
                        </label>

                        <select class="form-select" disabled>

                            <option>
                                <?php
                                foreach($fonds as $f){
                                    if($selected_fonds_id == $f['id']){
                                        echo htmlspecialchars($f['campagne']) . ' — ' .
                                            number_format($f['montant'],2) . ' ' .
                                            $f['devise'];
                                    }
                                }
                            ?>
                            </option>

                        </select>

                        <!-- on garde la valeur pour POST -->
                        <input type="hidden" name="fonds_id" value="<?= $selected_fonds_id ?>">

                    </div>

                    <!-- MONTANT -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Montant engagé
                        </label>

                        <div class="input-group">

                            <input type="number" step="0.01" name="montant_engage" class="form-control"
                                placeholder="0.00" required>

                            <span class="input-group-text">

                                <?= htmlspecialchars($fonds_selected['devise'] ?? 'DEV') ?>

                            </span>

                        </div>

                    </div>

                </div>

                <div class="row">

                    <!-- PERIODE -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Période de contribution
                        </label>

                        <select name="periode" class="form-select" required>

                            <option value="">
                                Choisir
                            </option>

                            <option value="Journalier">
                                Journalier
                            </option>

                            <option value="Hebdomadaire">
                                Hebdomadaire
                            </option>

                            <option value="Mensuel">
                                Mensuel
                            </option>

                            <option value="Chaque culte">
                                Chaque culte
                            </option>

                            <option value="Autre">
                                Autre
                            </option>

                        </select>

                    </div>


                    <!-- AUTRE -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Description supplémentaire
                        </label>

                        <input type="text" name="description_periode" class="form-control"
                            placeholder="Ex: Chaque dimanche">

                    </div>

                </div>


                <div class="row">

                    <!-- DATE DEBUT -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Date début
                        </label>

                        <input type="date" name="date_debut" class="form-control">

                    </div>


                    <!-- DATE FIN -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Date fin
                        </label>

                        <input type="date" name="date_fin" class="form-control">

                    </div>

                </div>


                <!-- BUTTON -->
                <div class="mt-4">

                    <button type="submit" name="submit" class="btn btn-primary px-4">

                        <i class="bi bi-check-circle-fill"></i>
                        Valider mon engagement

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<?php require_once '../../layouts/footer.php'; ?>