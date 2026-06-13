<?php

require_once '../../core/auth.php';
require_once '../../config/database.php';

$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);

$page_title = 'Gestion Fidèle';

$nom = '';
$postnom = '';
$prenom = '';
$sexe = '';
$telephone = '';
$adresse = '';
$date_naissance = '';


// =========================
// RECUPERATION POUR EDIT
// =========================

if($action == 'edit' && $id > 0){

    $sql = "SELECT * FROM fideles WHERE id = ? LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    $fidele = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$fidele){

        $_SESSION['error'] = "Fidèle introuvable";

        header('Location: ../fideles/');
        exit;
    }

    $nom = $fidele['nom'];
    $postnom = $fidele['postnom'];
    $prenom = $fidele['prenom'];
    $sexe = $fidele['sexe'];
    $telephone = $fidele['telephone'];
    $adresse = $fidele['adresse'];
    $date_naissance = $fidele['date_naissance'];
}


// =========================
// AJOUT
// =========================

if(isset($_POST['create'])){

    $nom = trim($_POST['nom']);
    $postnom = trim($_POST['postnom']);
    $prenom = trim($_POST['prenom']);
    $sexe = trim($_POST['sexe']);
    $telephone = trim($_POST['telephone']);
    $adresse = trim($_POST['adresse']);
    $date_naissance = trim($_POST['date_naissance']);

    // USER
    $email = trim($_POST['email']);
    $mot_de_passe = trim($_POST['mot_de_passe']);
    $role = 'visiteur';

    $user_id = null;

    // =========================
    // CREATION USER SI EMAIL
    // =========================

    if(!empty($email) && !empty($mot_de_passe)){

        // verifier email
        $check = $pdo->prepare("SELECT id FROM users WHERE email=?");
        $check->execute([$email]);

        // if($check->fetch()){

        //     $_SESSION['error'] = "Email déjà utilisé";

        //     header('Location: create_update_delete.php?action=create');
        //     exit;
        // }

        // hash
        $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        // insert user
        $sqlUser = "INSERT INTO users(nom,email,mot_de_passe,role)
                    VALUES(?,?,?,?)";

        $stmtUser = $pdo->prepare($sqlUser);

        $stmtUser->execute([
            $nom . ' ' . $postnom,
            $email,
            $hash,
            $role
        ]);

        $user_id = $pdo->lastInsertId();
    }

    // =========================
    // INSERT FIDELE
    // =========================

    $sql = "INSERT INTO fideles
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
            VALUES(?,?,?,?,?,?,?,?)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $user_id,
        $nom,
        $postnom,
        $prenom,
        $sexe,
        $telephone,
        $adresse,
        $date_naissance
    ]);

    $_SESSION['success'] = "Fidèle ajouté avec succès";

    header('Location: ../fideles/');
    exit;
}

// =========================
// MODIFICATION
// =========================

if(isset($_POST['update'])){

    $nom = trim($_POST['nom']);
    $postnom = trim($_POST['postnom']);
    $prenom = trim($_POST['prenom']);
    $sexe = trim($_POST['sexe']);
    $telephone = trim($_POST['telephone']);
    $adresse = trim($_POST['adresse']);
    $date_naissance = trim($_POST['date_naissance']);

    $sql = "UPDATE fideles SET
                nom=?,
                postnom=?,
                prenom=?,
                sexe=?,
                telephone=?,
                adresse=?,
                date_naissance=?
            WHERE id=?";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $nom,
        $postnom,
        $prenom,
        $sexe,
        $telephone,
        $adresse,
        $date_naissance,
        $id
    ]);

    $_SESSION['success'] = "Fidèle modifié avec succès";

    header('Location: ../fideles/');
    exit;
}



// =========================
// SUPPRESSION
// =========================

if($action == 'delete' && $id > 0){

    if($_SESSION['user']['role'] != 'admin'){

        $_SESSION['error'] = "Accès refusé";

        header('Location: ../fideles/');
        exit;
    }

    $sql = "DELETE FROM fideles WHERE id=?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    $_SESSION['success'] = "Fidèle supprimé avec succès";

    header('Location: ../fideles/');
    exit;
}

require_once '../../layouts/header.php';
?>

<?php require_once '../../layouts/navbar_sidebar.php'; ?>

<div class="d-flex">



    <div class="container-fluid p-4">

        <div class="card shadow border-0">

            <div class="card-header bg-dark text-white">

                <h4 class="mb-0">

                    <?php if($action == 'edit'): ?>

                    Modifier un fidèle

                    <?php else: ?>

                    Ajouter un fidèle

                    <?php endif; ?>

                </h4>

            </div>

            <div class="card-body">

                <form method="POST">

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <label>Nom</label>

                            <input type="text" name="nom" class="form-control" required
                                value="<?= htmlspecialchars($nom) ?>">

                        </div>

                        <div class="col-md-4 mb-3">

                            <label>Postnom</label>

                            <input type="text" name="postnom" class="form-control"
                                value="<?= htmlspecialchars($postnom) ?>">

                        </div>

                        <div class="col-md-4 mb-3">

                            <label>Prénom</label>

                            <input type="text" name="prenom" class="form-control"
                                value="<?= htmlspecialchars($prenom) ?>">

                        </div>

                    </div>


                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <label>Sexe</label>

                            <select name="sexe" class="form-select" required>

                                <option value="">
                                    Choisir
                                </option>

                                <option value="M" <?= $sexe == 'M' ? 'selected' : '' ?>>
                                    Homme
                                </option>

                                <option value="F" <?= $sexe == 'F' ? 'selected' : '' ?>>
                                    Femme
                                </option>

                            </select>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label>Téléphone</label>

                            <input type="text" name="telephone" class="form-control"
                                value="<?= htmlspecialchars($telephone) ?>">

                        </div>

                        <div class="col-md-4 mb-3">

                            <label>Date naissance</label>

                            <input type="date" name="date_naissance" class="form-control"
                                value="<?= $date_naissance ?>">

                        </div>

                    </div>


                    <div class="mb-3">

                        <label>Adresse</label>

                        <textarea name="adresse" class="form-control"
                            rows="3"><?= htmlspecialchars($adresse) ?></textarea>

                    </div>

                    <hr>

                    <h5 class="mb-3">🔐 Compte utilisateur</h5>

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <label>Nom d'utilisateur</label>

                            <input type="texte" name="email" class="form-control"
                                placeholder="Entrez le nom d'utilisateur">

                        </div>

                        <div class="col-md-4 mb-3">

                            <label>Mot de passe</label>

                            <input type="password" id="password" name="mot_de_passe" class="form-control"
                                placeholder="********">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="showPassword">
                                <label class="form-check-label" for="showPassword">
                                    Afficher le mot de passe
                                </label>
                            </div>
                        </div>

                        <!-- <div class="col-md-4 mb-3">

                            <label>Rôle</label>

                            <select name="role" class="form-select">

                                <option value="visiteur">
                                    Visiteur
                                </option>

                                <option value="admin">
                                    Admin
                                </option>

                            </select>

                        </div> -->

                    </div>

                    <div class="d-flex justify-content-between">

                        <a href="index.php" class="btn btn-secondary">

                            Retour

                        </a>

                        <?php if($action == 'edit'): ?>

                        <button type="submit" name="update" class="btn btn-warning">

                            Modifier

                        </button>

                        <?php else: ?>

                        <button type="submit" name="create" class="btn btn-primary">

                            Ajouter

                        </button>

                        <?php endif; ?>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

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

<?php require_once '../../layouts/footer.php'; ?>