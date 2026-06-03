<?php

require_once '../../core/auth.php';
require_once '../../config/database.php';

$cultes = $pdo->query("SELECT * FROM cultes ORDER BY id DESC");

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';

?>

<div class="d-flex">

    <div class="container-fluid p-4">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <h3 class="mb-0">
                <i class="bi bi-calendar-event-fill"></i>
                Cultes
            </h3>

            <a href="create_update_delete.php?action=create" class="btn btn-primary">

                <i class="bi bi-plus-circle-fill"></i>
                Ajouter culte

            </a>

        </div>

        <table class="table table-bordered table-hover align-middle">

            <thead class="table-dark">

                <tr>
                    <th>ID</th>
                    <th>Thème</th>
                    <th>Orateur</th>
                    <th>Date</th>
                    <th width="220"></th>
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

                        <div class="d-flex gap-1 flex-wrap">

                            <a href="view.php?id=<?= $c['id'] ?>" class="btn btn-primary btn-sm">

                                <i class="bi bi-eye-fill"></i>
                                <!-- Voir -->

                            </a>

                            <a href="create_update_delete.php?action=edit&id=<?= $c['id'] ?>"
                                class="btn btn-warning btn-sm">

                                <i class="bi bi-pencil-square"></i>
                                <!-- Modifier -->

                            </a>

                            <a href="create_update_delete.php?action=delete&id=<?= $c['id'] ?>"
                                class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce culte ?')">

                                <i class="bi bi-trash-fill"></i>
                                <!-- Supprimer -->

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