<?php

require_once '../../core/auth.php';
require_once '../../config/database.php';

$cultes = $pdo->query("SELECT * FROM cultes ORDER BY id DESC");

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';

?>

<div class="d-flex">

    

    <div class="container-fluid p-4">

        <div class="d-flex justify-content-between mb-3">

            <h3>Cultes</h3>

            <a href="create_update_delete.php?action=create" class="btn btn-primary">

                Ajouter culte

            </a>

        </div>

        <table class="table table-bordered table-hover">

            <thead class="table-dark">

                <tr>
                    <th>ID</th>
                    <th>Thème</th>
                    <th>Orateur</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>

            </thead>

            <tbody>

                <?php foreach($cultes as $c): ?>

                <tr>

                    <td><?= $c['id'] ?></td>

                    <td><?= htmlspecialchars($c['theme']) ?></td>

                    <td><?= htmlspecialchars($c['orateur']) ?></td>

                    <td><?= $c['date_culte'] ?></td>

                    <td>

                        <a href="view.php?id=<?= $c['id'] ?>" class="btn btn-info btn-sm">
                            Voir
                        </a>

                        <a href="create_update_delete.php?action=edit&id=<?= $c['id'] ?>"
                            class="btn btn-warning btn-sm">
                            Edit
                        </a>

                        <a href="create_update_delete.php?action=delete&id=<?= $c['id'] ?>"
                            class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce culte ?')">
                            Delete
                        </a>

                    </td>

                </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>

<?php require_once '../../layouts/footer.php'; ?>