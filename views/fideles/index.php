<?php

require_once '../../core/auth.php';
require_once '../../config/database.php';

$page_title = 'Gestion des Fidèles';

$fideles = $pdo->query("
    SELECT fi.*, CONCAT(fi.nom, ' ', fi.postnom, ' ', fi.prenom) AS nom_complet
    FROM fideles fi
    ORDER BY fi.nom ASC;
");

require_once '../../layouts/header.php';
?>

<?php require_once '../../layouts/navbar_sidebar.php'; ?>

<div class="d-flex">

    

    <div class="container-fluid p-4">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h2 class="mb-1">
                    <i class="bi bi-people"></i>
                    Gestion des Fidèles
                </h2>

                <p class="text-muted mb-0">
                    Liste complète des fidèles enregistrés
                </p>

            </div>

            <a href="create_update_delete.php?action=create" class="btn btn-primary">

                <i class="bi bi-plus-circle"></i>
                Ajouter un fidèle

            </a>

        </div>

        <!-- ALERTS -->
        <?php require_once '../../layouts/alerts.php'; ?>

        <!-- CARD -->
        <div class="card shadow border-0">

            <div class="card-body">

                <!-- TABLE HEADER -->
                <div class="d-flex justify-content-between align-items-center mb-3">

                    <h5 class="mb-0">
                        Liste des fidèles
                    </h5>

                    <span class="badge bg-dark">
                        <?= $fideles->rowCount(); ?> fidèle(s)
                    </span>

                </div>

                <!-- TABLE -->
                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead class="table-dark">

                            <tr>

                                <th>#</th>

                                <th>Nom complet</th>

                                <th>Sexe</th>

                                <th>Téléphone</th>

                                <th>Date enregistrement</th>

                                <th width="180">
                                    
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php if($fideles->rowCount() > 0): ?>

                            <?php foreach($fideles as $fidele): ?>

                            <tr>

                                <td>
                                    <?= $fidele['id'] ?>
                                </td>

                                <td>

                                    <strong>
                                        <?= htmlspecialchars($fidele['nom_complet']) ?>
                                    </strong>

                                </td>

                                <td>

                                    <?php if($fidele['sexe'] == 'M'): ?>

                                    <span class="badge bg-primary">
                                        Homme
                                    </span>

                                    <?php else: ?>

                                    <span class="badge bg-danger">
                                        Femme
                                    </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars($fidele['telephone']) ?>

                                </td>

                                <td>

                                    <?= date('d/m/Y', strtotime($fidele['created_at'])) ?>

                                </td>

                                <td>

                                    <div class="btn-group">

                                        <a href="view.php?id=<?= $fidele['id'] ?>" class="btn btn-info btn-sm">

                                            <i class="bi bi-eye"></i>

                                        </a>

                                        <a href="create_update_delete.php?action=edit&id=<?= $fidele['id'] ?>"
                                            class="btn btn-warning btn-sm">

                                            <i class="bi bi-pencil-square"></i>

                                        </a>

                                        <?php if($_SESSION['user']['role'] == 'admin'): ?>

                                        <a href="create_update_delete.php?action=delete&id=<?= $fidele['id'] ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Voulez-vous vraiment supprimer ce fidèle ?')">

                                            <i class="bi bi-trash"></i>

                                        </a>

                                        <?php endif; ?>

                                    </div>

                                </td>

                            </tr>

                            <?php endforeach; ?>

                            <?php else: ?>

                            <tr>

                                <td colspan="6" class="text-center py-4">

                                    <div class="text-muted">

                                        <i class="bi bi-inbox fs-1"></i>

                                        <p class="mt-2 mb-0">
                                            Aucun fidèle enregistré
                                        </p>

                                    </div>

                                </td>

                            </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>
        <?php require_once '../../layouts/footer.php'; ?>
    </div>

</div>