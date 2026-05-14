<?php

require_once '../../core/auth.php';
require_once '../../config/database.php';

$id = (int)($_GET['id'] ?? 0);

$sql = "SELECT * FROM fideles WHERE id=? LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);

$fidele = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$fidele){

    $_SESSION['error'] = "Fidèle introuvable";

    header('Location: index.php');
    exit;
}

$page_title = 'Détails fidèle';

require_once '../../layouts/header.php';
?>

<?php require_once '../../layouts/navbar_sidebar.php'; ?>

<div class="d-flex">

    

    <div class="container-fluid p-4">

        <div class="card shadow border-0">

            <div class="card-header bg-dark text-white">

                <h4 class="mb-0">

                    Détails du fidèle

                </h4>

            </div>

            <div class="card-body">

                <div class="row g-4">

                    <div class="col-md-6">

                        <strong>Nom :</strong>

                        <p>
                            <?= htmlspecialchars($fidele['nom']) ?>
                        </p>

                    </div>

                    <div class="col-md-6">

                        <strong>Postnom :</strong>

                        <p>
                            <?= htmlspecialchars($fidele['postnom']) ?>
                        </p>

                    </div>

                    <div class="col-md-6">

                        <strong>Prénom :</strong>

                        <p>
                            <?= htmlspecialchars($fidele['prenom']) ?>
                        </p>

                    </div>

                    <div class="col-md-6">

                        <strong>Sexe :</strong>

                        <p>

                            <?= $fidele['sexe'] == 'M'
                                ? 'Homme'
                                : 'Femme' ?>

                        </p>

                    </div>

                    <div class="col-md-6">

                        <strong>Téléphone :</strong>

                        <p>
                            <?= htmlspecialchars($fidele['telephone']) ?>
                        </p>

                    </div>

                    <div class="col-md-6">

                        <strong>Date naissance :</strong>

                        <p>
                            <?= $fidele['date_naissance'] ?>
                        </p>

                    </div>

                    <div class="col-md-12">

                        <strong>Adresse :</strong>

                        <p>
                            <?= nl2br(htmlspecialchars($fidele['adresse'])) ?>
                        </p>

                    </div>

                    <div class="col-md-12">

                        <strong>Date ajout :</strong>

                        <p>
                            <?= date('d/m/Y H:i', strtotime($fidele['created_at'])) ?>
                        </p>

                    </div>

                </div>

                <hr>

                <div class="d-flex justify-content-between">

                    <a href="index.php" class="btn btn-secondary">

                        Retour

                    </a>

                    <a href="create_update_delete.php?action=edit&id=<?= $fidele['id'] ?>" class="btn btn-warning">

                        Modifier

                    </a>

                </div>

            </div>

        </div>
        <?php require_once '../../layouts/footer.php'; ?>
    </div>

</div>