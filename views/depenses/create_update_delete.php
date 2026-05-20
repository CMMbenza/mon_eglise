<?php

require_once '../../core/auth.php';
require_once '../../config/database.php';

$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);

$motif = '';
$montant = '';
$date = date('Y-m-d');
$beneficiaire = '';
$devise = 'CDF';

$error = '';


// ======================================
// EDIT
// ======================================

if($action == 'edit'){

    $stmt = $pdo->prepare("
        SELECT *
        FROM depenses
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    $d = $stmt->fetch();

    if($d){

        $motif = $d['motif'];
        $montant = $d['montant'];
        $date = $d['date_depense'];
        $beneficiaire = $d['beneficiaire'];
        $devise = $d['devise'];
    }
}


// ======================================
// SAVE
// ======================================

if(isset($_POST['save'])){

    $motif = trim($_POST['motif']);
    $montant = (float)$_POST['montant'];
    $date = $_POST['date'];
    $beneficiaire = trim($_POST['beneficiaire']);
    $devise = $_POST['devise'];

    try{

        if($action == 'create'){

            $stmt = $pdo->prepare("
                INSERT INTO depenses(
                    motif,
                    montant,
                    beneficiaire,
                    date_depense,
                    devise
                )
                VALUES(?,?,?,?,?)
            ");

            $stmt->execute([
                $motif,
                $montant,
                $beneficiaire,
                $date,
                $devise
            ]);

        }else{

            $stmt = $pdo->prepare("
                UPDATE depenses SET
                    motif = ?,
                    montant = ?,
                    beneficiaire = ?,
                    date_depense = ?,
                    devise = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $motif,
                $montant,
                $beneficiaire,
                $date,
                $devise,
                $id
            ]);
        }

        header("Location: ../depenses/");
        exit;

    }catch(Exception $e){

        $error = $e->getMessage();
    }
}


// ======================================
// DELETE
// ======================================

if($action == 'delete'){

    $stmt = $pdo->prepare("
        DELETE FROM depenses
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    header("Location: ../depenses/");
    exit;
}

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';

?>

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-12">

            <div class="card shadow-lg border-0 rounded-4">

                <!-- HEADER -->
                <div class="card-header bg-dark text-white py-3 rounded-top-4">

                    <h4 class="mb-0">

                        <i class="bi bi-cash-stack"></i>

                        <?= $action == 'edit'
                            ? 'Modifier la dépense'
                            : 'Nouvelle dépense' ?>

                    </h4>

                </div>


                <!-- BODY -->
                <div class="card-body p-4">

                    <?php if($error): ?>

                    <div class="alert alert-danger">

                        <?= $error ?>

                    </div>

                    <?php endif; ?>


                    <form method="POST">

                        <div class="row">

                            <!-- MOTIF -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-semibold">

                                    <i class="bi bi-chat-left-text"></i>
                                    Motif de la dépense

                                </label>

                                <input type="text" name="motif" value="<?= htmlspecialchars($motif) ?>"
                                    class="form-control form-control-lg" placeholder="Ex: Achat matériel, transport..."
                                    required>

                            </div>


                            <!-- BENEFICIAIRE -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-semibold">

                                    <i class="bi bi-person"></i>
                                    Bénéficiaire

                                </label>

                                <input type="text" name="beneficiaire" value="<?= htmlspecialchars($beneficiaire) ?>"
                                    class="form-control form-control-lg" placeholder="Nom du bénéficiaire">

                            </div>

                        </div>


                        <div class="row">

                            <!-- MONTANT -->
                            <div class="col-md-4 mb-3">

                                <label class="form-label fw-semibold">

                                    <!-- <i class="bi bi-currency-dollar"></i> -->
                                    Montant

                                </label>

                                <input type="number" step="0.01" name="montant"
                                    value="<?= htmlspecialchars($montant) ?>" class="form-control form-control-lg"
                                    placeholder="0.00" required>

                            </div>


                            <!-- DEVISE -->
                            <div class="col-md-4 mb-3">

                                <label class="form-label fw-semibold">

                                    <i class="bi bi-cash"></i>
                                    Devise

                                </label>

                                <select name="devise" class="form-select form-select-lg" required>

                                    <option value="CDF" <?= $devise == 'CDF' ? 'selected' : '' ?>>

                                        CDF

                                    </option>

                                    <option value="USD" <?= $devise == 'USD' ? 'selected' : '' ?>>

                                        USD

                                    </option>

                                </select>

                            </div>


                            <!-- DATE -->
                            <div class="col-md-4 mb-4">

                                <label class="form-label fw-semibold">

                                    <i class="bi bi-calendar-event"></i>
                                    Date de la dépense

                                </label>

                                <input type="date" name="date" value="<?= $date ?>" class="form-control form-control-lg"
                                    required>

                            </div>

                        </div>


                        <!-- BUTTONS -->
                        <div class="d-flex gap-2">

                            <button type="submit" name="save" class="btn btn-primary btn-lg flex-fill shadow-sm">

                                <i class="bi bi-check-circle"></i>
                                Enregistrer

                            </button>


                            <a href="index.php" class="btn btn-outline-secondary btn-lg">

                                Retour

                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once '../../layouts/footer.php'; ?>