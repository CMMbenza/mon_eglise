<?php
require_once '../../core/auth.php';
require_once '../../config/database.php';

$depenses = $pdo->query("SELECT * FROM depenses ORDER BY date_depense DESC");

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';
?>

<div class="d-flex">

    <div class="container-fluid p-4">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <h3 class="mb-0">
                <i class="bi bi-cash-stack"></i>
                Dépenses
            </h3>

            <a href="create_update_delete.php?action=create" class="btn btn-primary">

                <i class="bi bi-plus-circle-fill"></i>
                Ajouter

            </a>

        </div>

        <table class="table table-bordered table-hover align-middle">

            <thead class="table-dark">

                <tr>
                    <th>ID</th>
                    <th>Motif</th>
                    <th>Montant</th>
                    <th>Date dépenses</th>
                    <th width="220"></th>
                </tr>

            </thead>

            <tbody>

                <?php foreach($depenses as $d): ?>

                <tr>
                    <td><?= $d['id'] ?></td>
                    <td><?= htmlspecialchars($d['motif']) ?></td>
                    <td><?= $d['montant'] ?></td>
                    <td><?= $d['date_depense'] ?></td>
                    <td>

                        <div class="d-flex gap-1 flex-wrap">

                            <a href="view.php?id=<?= $d['id'] ?>" class="btn btn-primary btn-sm">

                                <i class="bi bi-eye-fill"></i>
                                <!-- Voir -->

                            </a>

                            <a href="create_update_delete.php?action=edit&id=<?= $d['id'] ?>"
                                class="btn btn-warning btn-sm">

                                <i class="bi bi-pencil-square"></i>
                                <!-- Edit -->

                            </a>

                            <a href="create_update_delete.php?action=delete&id=<?= $d['id'] ?>"
                                class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette dépense ?')">

                                <i class="bi bi-trash-fill"></i>
                                <!-- Del -->

                            </a>

                        </div>

                    </td>
                </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>
</div>

<?php require_once '../../layouts/footer.php'; ?>