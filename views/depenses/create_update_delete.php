<?php
require_once '../../core/auth.php';
require_once '../../config/database.php';

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;

$motif = '';
$montant = '';
$date = '';

if($action == 'edit'){
    $stmt = $pdo->prepare("SELECT * FROM depenses WHERE id=?");
    $stmt->execute([$id]);
    $d = $stmt->fetch();

    $motif = $d['motif'];
    $montant = $d['montant'];
    $date = $d['date_depense'];
}

if(isset($_POST['save'])){

    if($action == 'create'){

        $stmt = $pdo->prepare("INSERT INTO depenses(motif,montant,date_depense) VALUES(?,?,?)");
        $stmt->execute([$_POST['motif'],$_POST['montant'],$_POST['date']]);

    } else {

        $stmt = $pdo->prepare("UPDATE depenses SET motif=?,montant=?,date_depense=? WHERE id=?");
        $stmt->execute([$_POST['motif'],$_POST['montant'],$_POST['date'],$id]);
    }

    header("Location: ../depenses/");
    exit;
}

if($action == 'delete'){
    $pdo->prepare("DELETE FROM depenses WHERE id=?")->execute([$id]);
    header("Location: ../depenses/");
}

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';
?>
<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-12">

            <!-- CARD -->
            <div class="card shadow-lg border-0 rounded-4">

                <!-- HEADER -->
                <div class="card-header bg-dark text-white py-3 rounded-top-4">

                    <h4 class="mb-0">
                        <i class="bi bi-cash-stack"></i>
                        Gestion des Dépenses
                    </h4>

                </div>

                <!-- BODY -->
                <div class="card-body p-4">

                    <form method="POST">

                        <!-- MOTIF -->
                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                <i class="bi bi-chat-left-text"></i>
                                Motif de la dépense
                            </label>

                            <input type="text" name="motif" value="<?= $motif ?>" class="form-control form-control-lg"
                                placeholder="Ex: Achat matériel, transport...">

                        </div>

                        <!-- MONTANT -->
                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                <i class="bi bi-currency-dollar"></i>
                                Montant
                            </label>

                            <div class="input-group input-group-lg">

                                <span class="input-group-text">
                                    $
                                </span>

                                <input type="number" name="montant" value="<?= $montant ?>" class="form-control"
                                    placeholder="0.00">

                            </div>

                        </div>

                        <!-- DATE -->
                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                <i class="bi bi-calendar-event"></i>
                                Date de la dépense
                            </label>

                            <input type="date" name="date" value="<?= $date ?>" class="form-control form-control-lg">

                        </div>

                        <!-- BUTTON -->
                        <button type="submit" name="save" class="btn btn-primary btn-lg w-100 shadow-sm">

                            <i class="bi bi-check-circle"></i>
                            Enregistrer

                        </button>

                        <!-- CANCEL / BACK -->
                        <a href="index.php" class="btn btn-outline-secondary w-100 mt-2">

                            Retour

                        </a>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>