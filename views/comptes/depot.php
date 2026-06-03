<?php
require_once '../../core/auth.php';
require_once '../../config/database.php';

$compte_id = $_GET['id'];

$compte = $pdo->prepare("SELECT * FROM comptes WHERE id=? ORDER BY nom DESC");
$compte->execute([$compte_id]);
$c = $compte->fetch();

if(!$c){
    die("Compte introuvable");
}

if(isset($_POST['save'])){

    $type = 'ENTREE';
    $montant = $_POST['montant'];
    $devise = $_POST['devise'];
    $fidele_id = $_POST['fidele_id'] ?: null;

    $pdo->prepare("
        INSERT INTO compte_mouvements(compte_id,fidele_id,type,montant,devise)
        VALUES(?,?,?,?,?)
    ")->execute([$compte_id,$fidele_id,$type,$montant,$devise]);

    header("Location: view.php?id=".$compte_id);
    exit;
}

$fideles = $pdo->query("SELECT * FROM fideles");

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';
?>

<div class="container p-4">

    <div class="card p-4">

        <h3>Mouvement - <?= $c['nom'] ?></h3>

        <form method="POST">

            <!-- <label>Type</label>
            <select name="type" class="form-control mb-2">
                <option value="ENTREE">Entrée</option>
                <option value="SORTIE">Sortie</option>
            </select> -->

            <label>Fidèle</label>
            <select name="fidele_id" class="form-control mb-2">
                <option value="">-- optionnel --</option>
                <?php foreach($fideles as $f): ?>
                <option value="<?= $f['id'] ?>">
                    <?= $f['nom'] ?> <?= $f['postnom'] ?> <?= $f['prenom'] ?>
                </option>
                <?php endforeach; ?>
            </select>

            <label>Montant</label>
            <input type="number" step="0.01" name="montant" class="form-control mb-2" required>

            <label>Devise</label>
            <select name="devise" class="form-control mb-3">
                <option value="CDF">CDF</option>
                <option value="USD">USD</option>
            </select>

            <button class="btn btn-success" name="save">Enregistrer</button>

        </form>

    </div>

</div>

<?php require_once '../../layouts/footer.php'; ?>