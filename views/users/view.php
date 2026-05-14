<?php

require_once '../../core/auth.php';
require_once '../../config/database.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$id]);

$u = $stmt->fetch();

require_once '../../layouts/header.php';
require_once '../../layouts/navbar_sidebar.php';
?>

<div class="container p-4">

    <h3>Détails utilisateur</h3>

    <p><strong>Nom :</strong> <?= $u['nom'] ?></p>
    <p><strong>Email :</strong> <?= $u['email'] ?></p>
    <p><strong>Role :</strong> <?= $u['role'] ?></p>

</div>