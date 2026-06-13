<?php

require_once 'config/database.php';

$success = '';
$error = '';


// =========================
// TRAITEMENT
// =========================

if(isset($_POST['submit'])){

    // =========================
    // RECUPERATION
    // =========================

    $nom = trim($_POST['nom']);
    $postnom = trim($_POST['postnom']);
    $prenom = trim($_POST['prenom']);

    $sexe = trim($_POST['sexe']);
    $telephone = trim($_POST['telephone']);

    $adresse = trim($_POST['adresse']);
    $date_naissance = trim($_POST['date_naissance']);

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);



    // =========================
    // VALIDATIONS
    // =========================

    if(
        empty($nom) ||
        empty($sexe) ||
        empty($email) ||
        empty($password)
    ){

        $error = "Veuillez remplir tous les champs obligatoires.";

    }elseif(strlen($password) < 4){

        $error = "Le mot de passe doit contenir au moins 4 caractères.";

    }else{

        // =========================
        // EMAIL EXISTE ?
        // =========================

        $check = $pdo->prepare("
            SELECT id
            FROM users
            WHERE email=?
            LIMIT 1
        ");

        $check->execute([$email]);

        if($check->fetch()){

            $error = "Cet email existe déjà.";

        }else{

            try{

                $pdo->beginTransaction();

                // =========================
                // CREATION USER
                // =========================

                $hashed_password = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $sqlUser = "
                    INSERT INTO users
                    (
                        nom,
                        email,
                        mot_de_passe,
                        role
                    )
                    VALUES(?,?,?,?)
                ";

                $stmtUser = $pdo->prepare($sqlUser);

                $stmtUser->execute([
                    trim($nom . ' ' . $postnom . ' ' . $prenom),
                    $email,
                    $hashed_password,
                    'visiteur'
                ]);

                $user_id = $pdo->lastInsertId();



                // =========================
                // CREATION FIDELE
                // =========================

                $sqlFidele = "
                    INSERT INTO fideles
                    (
                        user_id,
                        nom,
                        postnom,
                        prenom,
                        sexe,
                        telephone,
                        adresse,
                        date_naissance
                    )
                    VALUES(?,?,?,?,?,?,?,?)
                ";

                $stmtFidele = $pdo->prepare($sqlFidele);

                $stmtFidele->execute([
                    $user_id,
                    $nom,
                    $postnom,
                    $prenom,
                    $sexe,
                    $telephone,
                    $adresse,
                    $date_naissance ?: null
                ]);


                $pdo->commit();

                $success = "
                    Votre inscription a été effectuée avec succès.
                    Vous pouvez maintenant vous connecter.
                ";

                // RESET FORM
                $_POST = [];

            }catch(Exception $e){

                $pdo->rollBack();

                $error = "Erreur : " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Enregistrement des Fidèles
    </title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
    body {
        background: #f4f6f9;
        min-height: 100vh;
    }

    .register-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
    }

    .register-header {
        background: linear-gradient(45deg, #212529, #343a40);
        color: white;
        padding: 25px;
    }

    .logo {
        font-size: 55px;
        margin-right: 15px;
    }

    .card-body {
        background: white;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #0d6efd;
        margin-bottom: 20px;
    }

    .form-control,
    .form-select {
        border-radius: 10px;
        padding: 12px;
    }

    .form-control:focus,
    .form-select:focus {
        box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .15);
        border-color: #0d6efd;
    }

    .btn-register {
        border-radius: 10px;
        padding: 13px;
        font-size: 17px;
        font-weight: 600;
    }

    label {
        font-weight: 500;
        margin-bottom: 5px;
    }

    .footer-text {
        color: #6c757d;
    }
    </style>

</head>

<body>

    <div class="container py-5">

        <div class="row justify-content-center">

            <div class="col-lg-10">

                <div class="card shadow-lg register-card">

                    <!-- HEADER -->
                    <div class="register-header d-flex align-items-center">

                        <div class="logo">
                            <i class="bi bi-building"></i>
                        </div>

                        <div>

                            <h2 class="fw-bold mb-1">
                                Bienvenue à l'Église
                            </h2>

                            <p class="mb-0">
                                Formulaire d’enregistrement des fidèles
                            </p>

                        </div>

                    </div>


                    <!-- BODY -->
                    <div class="card-body p-4 p-lg-5">

                        <!-- ALERT SUCCESS -->
                        <?php if($success): ?>

                        <div class="alert alert-success alert-dismissible fade show">

                            <i class="bi bi-check-circle-fill"></i>

                            <?= $success ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert">
                            </button>

                        </div>

                        <?php endif; ?>


                        <!-- ALERT ERROR -->
                        <?php if($error): ?>

                        <div class="alert alert-danger alert-dismissible fade show">

                            <i class="bi bi-exclamation-triangle-fill"></i>

                            <?= $error ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert">
                            </button>

                        </div>

                        <?php endif; ?>


                        <form method="POST">

                            <!-- ========================= -->
                            <!-- INFOS PERSONNELLES -->
                            <!-- ========================= -->

                            <div class="section-title">

                                <i class="bi bi-person-vcard"></i>

                                Informations personnelles

                            </div>


                            <div class="row">

                                <div class="col-md-4 mb-3">

                                    <label>Nom *</label>

                                    <input type="text" name="nom" class="form-control" placeholder="Votre nom" required
                                        value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">

                                </div>


                                <div class="col-md-4 mb-3">

                                    <label>Postnom</label>

                                    <input type="text" name="postnom" class="form-control" placeholder="Votre postnom"
                                        value="<?= htmlspecialchars($_POST['postnom'] ?? '') ?>">

                                </div>


                                <div class="col-md-4 mb-3">

                                    <label>Prénom</label>

                                    <input type="text" name="prenom" class="form-control" placeholder="Votre prénom"
                                        value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">

                                </div>

                            </div>



                            <div class="row">

                                <div class="col-md-4 mb-3">

                                    <label>Sexe *</label>

                                    <select name="sexe" class="form-select" required>

                                        <option value="">
                                            Choisir le sexe
                                        </option>

                                        <option value="M" <?= ($_POST['sexe'] ?? '') == 'M' ? 'selected' : '' ?>>
                                            Homme
                                        </option>

                                        <option value="F" <?= ($_POST['sexe'] ?? '') == 'F' ? 'selected' : '' ?>>
                                            Femme
                                        </option>

                                    </select>

                                </div>


                                <div class="col-md-4 mb-3">

                                    <label>Téléphone</label>

                                    <input type="text" name="telephone" class="form-control" placeholder="+243 ..."
                                        value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">

                                </div>


                                <div class="col-md-4 mb-3">

                                    <label>Date de naissance</label>

                                    <input type="date" name="date_naissance" class="form-control"
                                        value="<?= htmlspecialchars($_POST['date_naissance'] ?? '') ?>">

                                </div>

                            </div>



                            <div class="mb-4">

                                <label>Adresse</label>

                                <textarea name="adresse" class="form-control" rows="3"
                                    placeholder="Votre adresse complète"><?= htmlspecialchars($_POST['adresse'] ?? '') ?></textarea>

                            </div>

                            <!-- ========================= -->
                            <!-- CONNEXION -->
                            <!-- ========================= -->

                            <div class="section-title">

                                <i class="bi bi-shield-lock"></i>

                                Informations de connexion

                            </div>


                            <div class="row">

                                <div class="col-md-6 mb-3">

                                    <label>Nom d'utilisateur *</label>

                                    <input type="text" name="email" class="form-control"
                                        placeholder="Entrez votre nom d'utilisateur" required
                                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

                                </div>


                                <div class="col-md-6 mb-4">

                                    <label>Mot de passe *</label>

                                    <input type="password" id="password" name="password" class="form-control"
                                        placeholder="Minimum 4 caractères" required>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="showPassword">
                                        <label class="form-check-label" for="showPassword">
                                            Afficher le mot de passe
                                        </label>
                                    </div>
                                </div>

                            </div>
                            <!-- BUTTON -->

                            <button type="submit" name="submit" class="btn btn-primary btn-register w-100">
                                <i class="bi bi-person-plus-fill"></i>
                                S’enregistrer maintenant
                            </button>

                        </form>

                    </div>
                    <a href="core/login.php" class="text-center mb-3">Vous avez
                        déjà un compte ?
                        Connectez-vous</a>
                </div>


                <!-- FOOTER -->

                <div class="text-center footer-text mt-4">

                    <small>

                        &copy; <?= date('Y') ?>

                        Mon Église - Tous droits réservés.

                    </small>

                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap JS -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('showPassword').addEventListener('change', function() {
        const password = document.getElementById('password');

        if (this.checked) {
            password.type = 'text';
        } else {
            password.type = 'password';
        }
    });
    </script>
</body>

</html>