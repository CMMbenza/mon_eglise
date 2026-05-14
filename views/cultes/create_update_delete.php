<?php

require_once '../../core/auth.php';
require_once '../../config/database.php';

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;

$theme = '';
$passage = '';
$orateur = '';
$interprete = '';
$hommes = 0;
$femmes = 0;
$offrande = 0;
$dime = 0;
$sociale = 0;
$autres = 0;
$date_culte = '';


// =====================
// EDIT
// =====================
if($action == 'edit'){

    $stmt = $pdo->prepare("SELECT * FROM cultes WHERE id=?");
    $stmt->execute([$id]);

    $c = $stmt->fetch();

    $theme = $c['theme'];
    $passage = $c['passage_biblique'];
    $orateur = $c['orateur'];
    $interprete = $c['interprete'];
    $hommes = $c['hommes'];
    $femmes = $c['femmes'];
    $offrande = $c['offrande'];
    $dime = $c['dime'];
    $sociale = $c['sociale'];
    $autres = $c['autres'];
    $date_culte = $c['date_culte'];
}


// =====================
// SAVE
// =====================
if(isset($_POST['save'])){

    if($action == 'create'){

        $sql = "INSERT INTO cultes
        (theme,passage_biblique,orateur,interprete,hommes,femmes,offrande,dime,sociale,autres,date_culte)
        VALUES (?,?,?,?,?,?,?,?,?,?,?)";

        $pdo->prepare($sql)->execute([
            $_POST['theme'],
            $_POST['passage'],
            $_POST['orateur'],
            $_POST['interprete'],
            $_POST['hommes'],
            $_POST['femmes'],
            $_POST['offrande'],
            $_POST['dime'],
            $_POST['sociale'],
            $_POST['autres'],
            $_POST['date_culte']
        ]);

    } else {

        $sql = "UPDATE cultes SET
        theme=?,passage_biblique=?,orateur=?,interprete=?,hommes=?,femmes=?,offrande=?,dime=?,sociale=?,autres=?,date_culte=?
        WHERE id=?";

        $pdo->prepare($sql)->execute([
            $_POST['theme'],
            $_POST['passage'],
            $_POST['orateur'],
            $_POST['interprete'],
            $_POST['hommes'],
            $_POST['femmes'],
            $_POST['offrande'],
            $_POST['dime'],
            $_POST['sociale'],
            $_POST['autres'],
            $_POST['date_culte'],
            $id
        ]);
    }

    header("Location: ../cultes/");
    exit;
}


// =====================
// DELETE
// =====================
if($action == 'delete'){

    $pdo->prepare("DELETE FROM cultes WHERE id=?")->execute([$id]);

    header("Location: ../cultes/");
    exit;
}

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';

?>

<div class="container p-4">

    <h3>
        <?= $action == 'edit' ? 'Modifier culte' : 'Ajouter culte' ?>
    </h3>

    <form method="POST">
        <label>Thème</label>
        <input class="form-control mb-2" name="theme" placeholder="Thème" value="<?= $theme ?>">
        <label>Passage biblique</label>
        <input class="form-control mb-2" name="passage" placeholder="Passage biblique" value="<?= $passage ?>">
        <label>Orateur</label>
        <input class="form-control mb-2" name="orateur" placeholder="Orateur" value="<?= $orateur ?>">
        <label>Interprète</label>
        <input class="form-control mb-2" name="interprete" placeholder="Interprète" value="<?= $interprete ?>">

        <div class="row">

            <div class="col"><label>Homme</label><input class="form-control mb-2" name="hommes" placeholder="Hommes"
                    value="<?= $hommes ?>">
            </div>
            <div class="col"><label>Femme</label><input class="form-control mb-2" name="femmes" placeholder="Femmes"
                    value="<?= $femmes ?>">
            </div>

        </div>

        <div class="row">

            <div class="col"><label>Offrande</label><input class="form-control mb-2" name="offrande"
                    placeholder="Offrande" value="<?= $offrande ?>"></div>
            <div class="col"><label>Dime</label><input class="form-control mb-2" name="dime" placeholder="Dîme"
                    value="<?= $dime ?>"></div>

        </div>

        <div class="row">

            <div class="col"><label>Sociale</label><input class="form-control mb-2" name="sociale" placeholder="Sociale"
                    value="<?= $sociale ?>"></div>
            <div class="col"><label>Autres entré</label><input class="form-control mb-2" name="autres"
                    placeholder="Autres" value="<?= $autres ?>">
            </div>

        </div>

        <input type="date" class="form-control mb-3" name="date_culte" value="<?= $date_culte ?>">

        <button class="btn btn-primary" name="save">
            Enregistrer
        </button>

    </form>

</div>

<?php require_once '../../layouts/footer.php'; ?>