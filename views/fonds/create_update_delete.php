<?php

require_once '../../core/auth.php';
require_once '../../config/database.php';

$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);

$montant = '';
$motif = '';
$campagne = '';
$devise = 'CDF';


// =====================
// EDIT
// =====================
if($action == 'edit'){

    $stmt = $pdo->prepare("SELECT * FROM fonds WHERE id=?");
    $stmt->execute([$id]);

    $f = $stmt->fetch();

    if($f){
        $montant = $f['montant'];
        $motif = $f['motif'];
        $campagne = $f['campagne'];
        $devise = $f['devise'] ?? 'CDF';
    }
}


// =====================
// SAVE
// =====================
if(isset($_POST['save'])){

    if($action == 'create'){

        $sql = "INSERT INTO fonds(campagne,montant,motif,devise)
                VALUES(?,?,?,?)";

        $pdo->prepare($sql)->execute([
            $_POST['campagne'],
            $_POST['montant'],
            $_POST['motif'],
            $_POST['devise']
        ]);

    }else{

        $sql = "UPDATE fonds
                SET campagne=?, montant=?, motif=?, devise=?
                WHERE id=?";

        $pdo->prepare($sql)->execute([
            $_POST['campagne'],
            $_POST['montant'],
            $_POST['motif'],
            $_POST['devise'],
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
        <label class="form-label mb-3">Campagne / Projet</label>
        <input class="form-control mb-2" name="campagne" placeholder="Ex: Achat parcelle, Construction église..."
            value="<?= htmlspecialchars($campagne) ?>" required>

        <!-- MOTIF -->
        <label class="form-label mb-3">Motif (optionnel)</label>
        <textarea class="form-control mb-2" name="motif"
            placeholder="Détail de la contribution"><?= htmlspecialchars($motif) ?></textarea>

        <div class="row">

            <!-- MONTANT -->
            <div class="col-md-6 mb-3">

                <label class="form-label mb-3">Montant (Séparer les montants par des virgules)</label>

                <input type="text" class="form-control" name="montant" value="<?= htmlspecialchars($montant) ?>"
                    placeholder="Ex: 10, 50, 100..." required>

            </div>

            <!-- DEVISE -->
            <div class="col-md-6 mb-3">

                <label class="form-label mb-3">Devise</label>

                <select name="devise" class="form-select" required>

                    <option value="CDF" <?= $devise=='CDF'?'selected':'' ?>>
                        CDF
                    </option>

                    <option value="USD" <?= $devise=='USD'?'selected':'' ?>>
                        USD
                    </option>

                </select>

            </div>

        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" name="save">
                <i class="bi bi-check-circle-fill"></i> Enregistrer contribution
            </button>
            <a href="../fonds/" class="btn btn-secondary">
                Retour
            </a>
        </div>


    </form>

</div>

<?php require_once '../../layouts/footer.php'; ?>