<?php

require_once '../../core/auth.php';
require_once '../../config/database.php';

$page_title = "Souscrire un fidèle";

$error = '';
$Operationsuccess = '';

$fonds_id = (int)($_GET['fonds_id'] ?? 0);

// ======================================
// RECUPERATION FONDS
// ======================================

$stmt = $pdo->prepare("
    SELECT *
    FROM fonds
    WHERE id = ?
");
$stmt->execute([$fonds_id]);

$fonds = $stmt->fetch();

if (!$fonds) {
    die('Contribution introuvable.');
}

// ======================================
// LISTE DES FIDELES
// ======================================

$fideles = $pdo->query("
    SELECT
        u.id,
        CONCAT(
            COALESCE(f.nom,''),
            ' ',
            COALESCE(f.postnom,''),
            ' ',
            COALESCE(f.prenom,'')
        ) AS nom_complet
    FROM users u
    INNER JOIN fideles f
        ON f.user_id = u.id
    WHERE u.role='visiteur'
    ORDER BY f.nom ASC
");

// ======================================
// ENREGISTREMENT
// ======================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id = (int) $_POST['user_id'];
    $montant_engage = trim($_POST['montant_engage']);
    $periode = trim($_POST['periode']);
    $description_periode = 'Ajouté par l’administration';
    $date_debut = date('Y-m-d');
    $date_fin = null;

    // $description_periode = trim($_POST['description_periode']);
    // $date_debut = !empty($_POST['date_debut']) ? $_POST['date_debut'] : null;
    // $date_fin = !empty($_POST['date_fin']) ? $_POST['date_fin'] : null;

    if (
        empty($user_id) ||
        empty($montant_engage) ||
        empty($periode)
    ) {

        $error = "Veuillez remplir tous les champs obligatoires.";

    } else {

        // Vérification doublon

        $check = $pdo->prepare("
            SELECT id
            FROM engagements_fonds
            WHERE user_id = ?
            AND fonds_id = ?
            AND statut = 'En cours'
        ");

        $check->execute([
            $user_id,
            $fonds_id
        ]);

        if ($check->fetch()) {

            $error = "Ce fidèle possède déjà un engagement actif pour cette contribution.";

        } else {

            $stmt = $pdo->prepare("
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
                VALUES
                (?,?,?,?,?,?,?,?)
            ");

            $stmt->execute([
                $user_id,
                $fonds_id,
                $montant_engage,
                $periode,
                $description_periode,
                $date_debut,
                $date_fin,
                $fonds['devise']
            ]);

            $stmtNom = $pdo->prepare("
            SELECT CONCAT(
                COALESCE(f.nom,''),
                ' ',
                COALESCE(f.postnom,''),
                ' ',
                COALESCE(f.prenom,'')
            ) AS nom_complet
            FROM fideles f
            WHERE f.user_id = ?
        ");

        $stmtNom->execute([$user_id]);

        $nomFidele = $stmtNom->fetchColumn();

            $Operationsuccess = "Le fidèle {$nomFidele} a été souscrit avec succès.";
            // $_SESSION['success'] =
            //     "Le fidèle a été souscrit avec succès.";

            // header("Location: view.php?id=".$fonds_id);
            // exit;
        }
    }
}

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';
?>

<div class="container-fluid p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="fw-bold mb-1">
                Souscrire un fidèle
            </h3>

            <p class="text-muted mb-0">
                Ajouter un engagement à cette contribution
            </p>

        </div>

        <a href="index.php" class="btn btn-dark">

            <i class="bi bi-arrow-left"></i>
            Retour

        </a>

    </div>

    <?php if($error): ?>

    <div class="alert alert-danger">
        <?= $error ?>
    </div>

    <?php endif; ?>

    <?php if($Operationsuccess): ?>

    <div class="alert alert-success">
        <?= $Operationsuccess ?>
    </div>

    <?php endif; ?>

    <div class="card shadow-sm border-0">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">

                <i class="bi bi-bank2"></i>

                <?= htmlspecialchars($fonds['campagne']) ?>

            </h5>

        </div>

        <div class="card-body">

            <div class="alert alert-info">

                <strong>Description :</strong>
                <?= nl2br(htmlspecialchars($fonds['motif'])) ?>

                <hr>

                <strong>Montant cible :</strong>
                <?= htmlspecialchars($fonds['montant']) ?>

                <?= htmlspecialchars($fonds['devise']) ?>

            </div>

            <form method="POST" autocomplete="off">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Fidèle
                        </label>

                        <select name="user_id" class="form-select" required>

                            <option value="">
                                Sélectionner un fidèle
                            </option>

                            <?php foreach($fideles as $f): ?>

                            <option value="<?= $f['id'] ?>">

                                <?= htmlspecialchars($f['nom_complet']) ?>

                            </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Montant engagé
                        </label>

                        <div class="input-group">

                            <input type="number" step="0.01" name="montant_engage" class="form-control" required>

                            <span class="input-group-text">

                                <?= htmlspecialchars($fonds['devise']) ?>

                            </span>

                        </div>

                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Période
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

                    <!-- <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Description période
                        </label>

                        <input type="text" name="description_periode" class="form-control"
                            placeholder="Ex: Chaque dimanche">

                    </div> -->

                </div>

                <!-- <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Date début
                        </label>

                        <input type="date" name="date_debut" class="form-control">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Date fin
                        </label>

                        <input type="date" name="date_fin" class="form-control">

                    </div>

                </div> -->

                <button type="submit" class="btn btn-primary">

                    <i class="bi bi-check-circle-fill"></i>
                    Enregistrer l'engagement

                </button>

            </form>

        </div>

    </div>

</div>

<?php require_once '../../layouts/footer.php'; ?>