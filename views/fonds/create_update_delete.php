<?php

require_once '../../core/auth.php';
require_once '../../config/database.php';

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;

$montant = '';
$motif = '';
$campagne = '';


// =====================
// EDIT
// =====================
if($action == 'edit'){

    $stmt = $pdo->prepare("SELECT * FROM fonds WHERE id=?");
    $stmt->execute([$id]);

    $f = $stmt->fetch();

    $montant = $f['montant'];
    $motif = $f['motif'];
    $campagne = $f['campagne'];
}


// =====================
// SAVE
// =====================
if(isset($_POST['save'])){

    if($action == 'create'){

        $sql = "INSERT INTO fonds(campagne,montant,motif)
                VALUES(?,?,?)";

        $pdo->prepare($sql)->execute([
            $_POST['campagne'],
            $_POST['montant'],
            $_POST['motif']
        ]);

    }else{

        $sql = "UPDATE fonds
                SET campagne=?, montant=?, motif=?
                WHERE id=?";

        $pdo->prepare($sql)->execute([
            $_POST['campagne'],
            $_POST['montant'],
            $_POST['motif'],
            $id
        ]);
    }

    header('Location: ../fonds/');
    exit;
}


// =====================
// DELETE
// =====================
if($action == 'delete'){

    $pdo->prepare("DELETE FROM fonds WHERE id=?")
        ->execute([$id]);

    header('Location: ../fonds/');
    exit;
}

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';
?>

<div class="container p-4">

    <h3>💰 Contribution / Souscription fonds</h3>

    <form method="POST" class="card p-3 shadow-sm">

        <!-- CAMPAGNE -->
        <label>Campagne / Projet</label>

        <input class="form-control mb-2" name="campagne" placeholder="Ex: Achat parcelle, Construction église..."
            value="<?= $campagne ?>" required>

        <!-- MOTIF -->
        <label>Motif (optionnel)</label>

        <textarea class="form-control mb-2" name="motif"
            placeholder="Détail de la contribution"><?= $motif ?></textarea>

        <!-- MONTANT -->
        <label>Montant (Séparer les montant par des virgules)</label>

        <input class="form-control mb-3" name="montant" type="text" placeholder="Ex: 10, 50, 100..."
            value="<?= $montant ?>" required>

        <button class="btn btn-primary" name="save">
            💾 Enregistrer contribution
        </button>

    </form>

</div>