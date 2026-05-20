<?php

require_once '../../core/auth.php';
require_once '../../config/database.php';

$id = $_GET['id'] ?? null;
$edit = false;

$theme = '';
$passage_biblique = '';
$orateur = '';
$interprete = '';
$hommes = 0;
$femmes = 0;

$offrande_dime = 0;
$sociale = 0;
$autres = 0;
$description_autres = '';

$date_culte = date('Y-m-d');
$devise = 'CDF';


// ======================================
// EDIT
// ======================================

if($id){

    $edit = true;

    $stmt = $pdo->prepare("
        SELECT *
        FROM cultes
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    $culte = $stmt->fetch();

    if($culte){

        $theme = $culte['theme'];
        $passage_biblique = $culte['passage_biblique'];
        $orateur = $culte['orateur'];
        $interprete = $culte['interprete'];

        $hommes = $culte['hommes'];
        $femmes = $culte['femmes'];

        $offrande_dime = $culte['offrande_dime'];
        $sociale = $culte['sociale'];
        $autres = $culte['autres'];
        $description_autres = $culte['description_autres'];

        $date_culte = $culte['date_culte'];
        $devise = $culte['devise'];
    }
}


// ======================================
// SAVE
// ======================================

if(isset($_POST['save'])){

    $theme = trim($_POST['theme']);
    $passage_biblique = trim($_POST['passage_biblique']);
    $orateur = trim($_POST['orateur']);
    $interprete = trim($_POST['interprete']);

    $hommes = (int)$_POST['hommes'];
    $femmes = (int)$_POST['femmes'];

    $offrande_dime = (float)$_POST['offrande_dime'];
    $sociale = (float)$_POST['sociale'];
    $autres = (float)$_POST['autres'];

    $description_autres = trim($_POST['description_autres']);

    $date_culte = $_POST['date_culte'];
    $devise = $_POST['devise'];

    try{

        if($edit){

            $stmt = $pdo->prepare("
                UPDATE cultes SET
                    theme = ?,
                    passage_biblique = ?,
                    orateur = ?,
                    interprete = ?,
                    hommes = ?,
                    femmes = ?,
                    offrande_dime = ?,
                    sociale = ?,
                    autres = ?,
                    description_autres = ?,
                    date_culte = ?,
                    devise = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $theme,
                $passage_biblique,
                $orateur,
                $interprete,
                $hommes,
                $femmes,
                $offrande_dime,
                $sociale,
                $autres,
                $description_autres,
                $date_culte,
                $devise,
                $id
            ]);

        }else{

            $stmt = $pdo->prepare("
                INSERT INTO cultes(
                    theme,
                    passage_biblique,
                    orateur,
                    interprete,
                    hommes,
                    femmes,
                    offrande_dime,
                    sociale,
                    autres,
                    description_autres,
                    date_culte,
                    devise
                )
                VALUES(?,?,?,?,?,?,?,?,?,?,?,?)
            ");

            $stmt->execute([
                $theme,
                $passage_biblique,
                $orateur,
                $interprete,
                $hommes,
                $femmes,
                $offrande_dime,
                $sociale,
                $autres,
                $description_autres,
                $date_culte,
                $devise
            ]);
        }

        header("Location: index.php");
        exit;

    }catch(Exception $e){

        $error = $e->getMessage();
    }
}

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';
?>

<div class="container-fluid p-4">

    <div class="row justify-content-center">

        <div class="col-lg-11">

            <div class="card shadow-sm border-0 rounded-4">

                <div class="card-header bg-dark text-white py-3">

                    <h4 class="mb-0">

                        <?= $edit ? 'Modifier le culte' : 'Nouveau culte' ?>

                    </h4>

                </div>

                <div class="card-body p-4">

                    <?php if(!empty($error)): ?>

                    <div class="alert alert-danger">

                        <?= $error ?>

                    </div>

                    <?php endif; ?>


                    <form method="POST">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Thème
                                </label>

                                <input type="text" name="theme" class="form-control"
                                    placeholder="Entrer le thème du culte" value="<?= htmlspecialchars($theme) ?>"
                                    required>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Passage biblique
                                </label>

                                <input type="text" name="passage_biblique" class="form-control"
                                    placeholder="Ex: Jean 3:16" value="<?= htmlspecialchars($passage_biblique) ?>">

                            </div>

                        </div>


                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Orateur
                                </label>

                                <input type="text" name="orateur" class="form-control" placeholder="Nom de l’orateur"
                                    value="<?= htmlspecialchars($orateur) ?>">

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Interprète
                                </label>

                                <input type="text" name="interprete" class="form-control"
                                    placeholder="Nom de l’interprète" value="<?= htmlspecialchars($interprete) ?>">

                            </div>

                        </div>


                        <div class="row">

                            <div class="col-md-3 mb-3">

                                <label class="form-label">
                                    Hommes
                                </label>

                                <input type="number" id="hommes" name="hommes" class="form-control" placeholder="0"
                                    value="<?= $hommes ?>">

                            </div>

                            <div class="col-md-3 mb-3">

                                <label class="form-label">
                                    Femmes
                                </label>

                                <input type="number" id="femmes" name="femmes" class="form-control" placeholder="0"
                                    value="<?= $femmes ?>">

                            </div>

                            <div class="col-md-3 mb-3">
                                <label for=""></label> <br>

                                <label class="form-label">
                                    Total effectif :
                                    <span id="totalEffectif" class="fw-bold text-primary">
                                        <?= (int)$hommes + (int)$femmes ?>
                                    </span>
                                </label>

                            </div>

                        </div>


                        <hr class="my-4">

                        <div class="row">

                            <div class="col-md-3 mb-3">

                                <label class="form-label">
                                    Devise
                                </label>

                                <select name="devise" class="form-select">

                                    <option value="CDF" <?= $devise == 'CDF' ? 'selected' : '' ?>>
                                        CDF
                                    </option>

                                    <option value="USD" <?= $devise == 'USD' ? 'selected' : '' ?>>
                                        USD
                                    </option>

                                </select>

                            </div>

                            <div class="col-md-3 mb-3">

                                <label class="form-label">
                                    Offrande + Dîme
                                </label>

                                <input type="number" step="0.01" name="offrande_dime" class="form-control"
                                    placeholder="0.00" value="<?= $offrande_dime ?>">

                            </div>

                            <div class="col-md-3 mb-3">

                                <label class="form-label">
                                    Sociale
                                </label>

                                <input type="number" step="0.01" name="sociale" class="form-control" placeholder="0.00"
                                    value="<?= $sociale ?>">

                            </div>

                            <div class="col-md-3 mb-3">

                                <label class="form-label">
                                    Autres
                                </label>

                                <input type="number" step="0.01" name="autres" class="form-control" placeholder="0.00"
                                    value="<?= $autres ?>">

                            </div>

                        </div>

                        <div class="mb-4">

                            <label class="form-label">
                                Description autres entrées
                            </label>

                            <textarea name="description_autres" class="form-control"
                                placeholder="Description des autres entrées"
                                rows="4"><?= htmlspecialchars($description_autres) ?></textarea>

                        </div>

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Date du culte
                                </label>

                                <input type="date" name="date_culte" class="form-control"
                                    placeholder="Sélectionner une date" value="<?= $date_culte ?>" required>

                            </div>

                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" name="save" class="btn btn-success">
                                <i class="bi bi-check-circle-fill"></i>
                                Enregistrer
                            </button>

                            <a href="../cultes/" class="btn btn-secondary">

                                Retour

                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<script>
function calculerTotal() {

    let hommes = parseInt(document.getElementById('hommes').value) || 0;
    let femmes = parseInt(document.getElementById('femmes').value) || 0;

    let total = hommes + femmes;

    document.getElementById('totalEffectif').innerText = total;
}

document.getElementById('hommes').addEventListener('input', calculerTotal);
document.getElementById('femmes').addEventListener('input', calculerTotal);
</script>

<?php require_once '../../layouts/footer.php'; ?>