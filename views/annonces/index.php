<?php

require_once '../../core/auth.php';
require_once '../../config/database.php';

$annonces = $pdo->query("SELECT * FROM annonces ORDER BY id DESC");

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';
?>

<div class="d-flex">

    

    <div class="container-fluid p-4">

        <div class="d-flex justify-content-between mb-3">

            <h3>Annonces</h3>

            <a href="create_update_delete.php?action=create" class="btn btn-primary">

                Ajouter

            </a>

        </div>

        <table class="table table-bordered">

            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>

            <?php foreach($annonces as $a): ?>

            <tr>

                <td><?= $a['id'] ?></td>

                <td><?= $a['titre'] ?></td>

                <td><?= $a['created_at'] ?></td>

                <td>

                    <a href="view.php?id=<?= $a['id'] ?>" class="btn btn-info btn-sm">

                        Voir

                    </a>

                    <a href="create_update_delete.php?action=edit&id=<?= $a['id'] ?>" class="btn btn-warning btn-sm">

                        Edit

                    </a>

                    <a href="create_update_delete.php?action=delete&id=<?= $a['id'] ?>" class="btn btn-danger btn-sm">

                        Delete

                    </a>

                </td>

            </tr>

            <?php endforeach; ?>

        </table>

    </div>

</div>

<?php require_once '../../layouts/footer.php'; ?>