<?php

require_once '../../core/auth.php';
require_once '../../config/database.php';

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;

$titre = '';
$contenu = '';

if($action == 'edit'){

    $stmt = $pdo->prepare("SELECT * FROM annonces WHERE id=?");
    $stmt->execute([$id]);

    $a = $stmt->fetch();

    $titre = $a['titre'];
    $contenu = $a['contenu'];
}

if(isset($_POST['save'])){

    if($action == 'create'){

        $sql = "INSERT INTO annonces(titre,contenu)
                VALUES(?,?)";

        $pdo->prepare($sql)->execute([
            $_POST['titre'],
            $_POST['contenu']
        ]);

    }else{

        $sql = "UPDATE annonces
                SET titre=?, contenu=?
                WHERE id=?";

        $pdo->prepare($sql)->execute([
            $_POST['titre'],
            $_POST['contenu'],
            $id
        ]);
    }

    header('Location: ../annonces/');
    exit;
}

if($action == 'delete'){

    $pdo->prepare("DELETE FROM annonces WHERE id=?")
        ->execute([$id]);

    header('Location: ../annonces/');
}

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';
?>

<form method="POST" class="p-4">

    <input class="form-control mb-2" name="titre" placeholder="Titre" value="<?= $titre ?>">

    <textarea class="form-control mb-3" rows="5" name="contenu"><?= $contenu ?></textarea>

    <button class="btn btn-primary" name="save">
        Enregistrer
    </button>

</form>