<?php

require_once '../auth_visiteurs.php';
require_once '../../config/database.php';

$user_id = $_SESSION['user']['id'];

$page_title = "Mon profil";

$success = '';
$error = '';


// =====================================
// RECUPERATION
// =====================================

$stmt = $pdo->prepare("
    SELECT
        u.nom AS nom_user,
        u.email,
        f.*
    FROM users u

    LEFT JOIN fideles f
        ON f.user_id = u.id

    WHERE u.id = ?
");

$stmt->execute([$user_id]);

$user = $stmt->fetch();


// =====================================
// UPDATE
// =====================================

if(isset($_POST['update'])){

    $nom = trim($_POST['nom']);
    $postnom = trim($_POST['postnom']);
    $prenom = trim($_POST['prenom']);
    $telephone = trim($_POST['telephone']);
    $adresse = trim($_POST['adresse']);
    $date_naissance = trim($_POST['date_naissance']);

    try{

        $stmt = $pdo->prepare("
            UPDATE fideles
            SET
                nom = ?,
                postnom = ?,
                prenom = ?,
                telephone = ?,
                adresse = ?,
                date_naissance = ?
            WHERE user_id = ?
        ");

        $stmt->execute([
            $nom,
            $postnom,
            $prenom,
            $telephone,
            $adresse,
            $date_naissance,
            $user_id
        ]);

        $success = "Profil mis à jour avec succès.";

        header("Refresh:1");

    }catch(Exception $e){

        $error = "Erreur lors de la modification.";
    }
}


require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar_fideles.php';
?>

<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
.profile-card {
    border: none;
    border-radius: 18px;
    overflow: hidden;
}

.form-control,
.form-select,
textarea {
    border-radius: 12px;
    padding: 12px;
}
</style>

<div class="container-fluid p-4">

    <!-- HEADER -->
    <div class="mb-4">

        <h2 class="fw-bold mb-1">

            <i class="bi bi-person-circle text-primary"></i>
            Mon profil

        </h2>

        <p class="text-muted mb-0">

            Gérez vos informations personnelles.

        </p>

    </div>


    <?php if($success): ?>

    <div class="alert alert-success">

        <?= $success ?>

    </div>

    <?php endif; ?>


    <?php if($error): ?>

    <div class="alert alert-danger">

        <?= $error ?>

    </div>

    <?php endif; ?>


    <div class="card shadow-sm profile-card">

        <div class="card-body p-4">

            <form method="POST">

                <div class="row g-4">

                    <div class="col-md-4">

                        <label class="form-label">
                            Nom
                        </label>

                        <input type="text"
                            name="nom"
                            class="form-control"
                            value="<?= htmlspecialchars($user['nom'] ?? '') ?>"
                            required>

                    </div>

                    <div class="col-md-4">

                        <label class="form-label">
                            Postnom
                        </label>

                        <input type="text"
                            name="postnom"
                            class="form-control"
                            value="<?= htmlspecialchars($user['postnom'] ?? '') ?>">

                    </div>

                    <div class="col-md-4">

                        <label class="form-label">
                            Prénom
                        </label>

                        <input type="text"
                            name="prenom"
                            class="form-control"
                            value="<?= htmlspecialchars($user['prenom'] ?? '') ?>">

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">
                            Email
                        </label>

                        <input type="email"
                            class="form-control"
                            value="<?= htmlspecialchars($user['email']) ?>"
                            readonly>

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">
                            Téléphone
                        </label>

                        <input type="text"
                            name="telephone"
                            class="form-control"
                            value="<?= htmlspecialchars($user['telephone'] ?? '') ?>">

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">
                            Date de naissance
                        </label>

                        <input type="date"
                            name="date_naissance"
                            class="form-control"
                            value="<?= $user['date_naissance'] ?>">

                    </div>

                    <div class="col-12">

                        <label class="form-label">
                            Adresse
                        </label>

                        <textarea name="adresse"
                            class="form-control"
                            rows="4"><?= htmlspecialchars($user['adresse'] ?? '') ?></textarea>

                    </div>

                    <div class="col-12">

                        <button type="submit"
                            name="update"
                            class="btn btn-primary px-4">

                            <i class="bi bi-save-fill"></i>
                            Enregistrer les modifications

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>

<?php require_once '../../layouts/footer.php'; ?>