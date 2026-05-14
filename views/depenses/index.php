<?php
require_once '../../core/auth.php';
require_once '../../config/database.php';

$depenses = $pdo->query("SELECT * FROM depenses ORDER BY date_depense DESC");

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';
?>

<div class="d-flex">

    

    <div class="container-fluid p-4">

        <h3>Dépenses</h3>

        <a href="create_update_delete.php?action=create" class="btn btn-primary mb-3">
            Ajouter
        </a>

        <table class="table table-bordered">

            <tr>
                <th>ID</th>
                <th>Motif</th>
                <th>Montant</th>
                <th>Date dépenses</th>
                <th>Actions</th>
            </tr>

            <?php foreach($depenses as $d): ?>

            <tr>
                <td><?= $d['id'] ?></td>
                <td><?= $d['motif'] ?></td>
                <td><?= $d['montant'] ?></td>
                <td><?= $d['date_depense'] ?></td>
                <td>
                    <a href="view.php?id=<?= $d['id'] ?>" class="btn btn-info btn-sm">Voir</a>
                    <a href="create_update_delete.php?action=edit&id=<?= $d['id'] ?>"
                        class="btn btn-warning btn-sm">Edit</a>
                    <a href="create_update_delete.php?action=delete&id=<?= $d['id'] ?>"
                        class="btn btn-danger btn-sm">Del</a>
                </td>
            </tr>

            <?php endforeach; ?>

        </table>

    </div>
</div>

<?php require_once '../../layouts/footer.php'; ?>