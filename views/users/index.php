<?php

require_once '../../core/auth.php';
require_once '../../config/database.php';

if($_SESSION['user']['role'] != 'admin'){
    die('Accès refusé');
}

$users = $pdo->query("SELECT * FROM users ORDER BY id DESC");

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';
?>

<div class="container p-4">

    <div class="d-flex justify-content-between mb-3">

        <h3>Gest. des utilisateurs</h3>

        <a href="create_update_delete.php?action=create" class="btn btn-primary">

            Ajouter un administrateur(trice)

        </a>

    </div>

    <table class="table table-bordered">

        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Actions</th>
        </tr>

        <?php foreach($users as $u): ?>

        <tr>

            <td><?= $u['id'] ?></td>
            <td><?= $u['nom'] ?></td>
            <td><?= $u['email'] ?></td>
            <td><?= $u['role'] ?></td>

            <td>

                <a href="view.php?id=<?= $u['id'] ?>" class="btn btn-info btn-sm">

                    Voir

                </a>

                <a href="create_update_delete.php?action=edit&id=<?= $u['id'] ?>" class="btn btn-warning btn-sm">

                    Edit

                </a>

                <a href="create_update_delete.php?action=delete&id=<?= $u['id'] ?>" class="btn btn-danger btn-sm">

                    Delete

                </a>

            </td>

        </tr>

        <?php endforeach; ?>

    </table>

</div>