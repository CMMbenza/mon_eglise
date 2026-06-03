<?php
require_once '../../core/auth.php';
require_once '../../config/database.php';

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;

$nom = '';
$description = '';

if($action == 'edit'){
    $stmt = $pdo->prepare("SELECT * FROM comptes WHERE id=?");
    $stmt->execute([$id]);
    $c = $stmt->fetch();

    $nom = $c['nom'];
    $description = $c['description'];
}

if(isset($_POST['save'])){

    if($action == 'create'){

        $stmt = $pdo->prepare("INSERT INTO comptes(nom,description) VALUES(?,?)");
        $stmt->execute([$_POST['nom'],$_POST['description']]);

    }else{

        $stmt = $pdo->prepare("UPDATE comptes SET nom=?,description=? WHERE id=?");
        $stmt->execute([$_POST['nom'],$_POST['description'],$id]);
    }

    header("Location: index.php");
    exit;
}

if($action == 'delete'){
    $pdo->prepare("DELETE FROM comptes WHERE id=?")->execute([$id]);
    header("Location: index.php");
    exit;
}

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';
?>

<div class="container p-4">

    <div class="card p-4">

        <h3>Compte</h3>

        <form method="POST">

            <label>Nom</label>
            <input class="form-control mb-2" name="nom" value="<?= $nom ?>" required>

            <label>Description</label>
            <textarea class="form-control mb-3" name="description"><?= $description ?></textarea>

            <button class="btn btn-primary" name="save">Créer ce compte</button>

        </form>

    </div>

</div>

<?php require_once '../../layouts/footer.php'; ?>