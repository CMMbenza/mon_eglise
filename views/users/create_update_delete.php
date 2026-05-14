<?php

require_once '../../core/auth.php';
require_once '../../config/database.php';

if($_SESSION['user']['role'] != 'admin'){
    die('Accès refusé');
}

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;

$nom = '';
$email = '';
$role = 'visiteur';

if($action == 'edit'){

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute([$id]);

    $u = $stmt->fetch();

    $nom = $u['nom'];
    $email = $u['email'];
    $role = $u['role'];
}

if(isset($_POST['save'])){

    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if($action == 'create'){

        $sql = "INSERT INTO users(nom,email,mot_de_passe,role)
                VALUES(?,?,?,?)";

        $pdo->prepare($sql)->execute([
            $_POST['nom'],
            $_POST['email'],
            $password,
            $_POST['role']
        ]);

    }else{

        $sql = "UPDATE users
                SET nom=?, email=?, role=?
                WHERE id=?";

        $pdo->prepare($sql)->execute([
            $_POST['nom'],
            $_POST['email'],
            $_POST['role'],
            $id
        ]);
    }

    header('Location: ../users/');
    exit;
}

if($action == 'delete'){

    $pdo->prepare("DELETE FROM users WHERE id=?")
        ->execute([$id]);

    header('Location: ../users/');
}

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';
?>
<div class="container mt-4">

    <div class="card">
        <div class="card-header bg-dark text-white">

            <h4 class="mb-0">

                <?php if($action == 'edit'): ?>

                Modifier l'administrateur(trice)

                <?php else: ?>

                Ajouter un administrateur(trice)

                <?php endif; ?>

            </h4>

        </div>

        <form method="POST" class="p-4">

            <div class="mb-3"><label for="">Nom de famille</label>
                <input class="form-control mb-2" name="nom" placeholder="Nom" value="<?= $nom ?>">
            </div>

            <div class="mb-3"><label for="">Nom d'utilisateur</label>
                <input class="form-control mb-2" name="text" placeholder="Entrez votre nom d'utilisateur"
                    value="<?= $email ?>">
            </div>
            <div class="mb-3"><label for="">Mot de passe</label>
                <input class="form-control mb-2" type="password" name="password" placeholder="Mot de passe">
            </div>

            <select class="form-control mb-3" name="role" value="admin" disabled>

                <option value="admin">Admin</option>
                <option value="visiteur">Visiteur</option>

            </select>

            <button class="btn btn-primary" name="save">
                Enregistrer
            </button>

        </form>
    </div>
</div>