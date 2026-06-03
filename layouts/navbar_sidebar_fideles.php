<?php
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

if (strpos($base_url, '/fideles') !== false) {
    $base_url = substr($base_url, 0, strpos($base_url, '/fideles'));
}

if ($base_url === '') {
    $base_url = '';
}
?>

<!-- MENU / NAVBAR -->
<div class="bg-dark text-white px-3 py-2 d-flex align-items-center justify-content-between flex-wrap">

    <!-- LEFT -->
    <div class="d-flex align-items-center">

        <!-- Bouton mobile -->
        <button class="btn btn-outline-light d-lg-none me-3" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#sidebar">

            <i class="bi bi-list"></i>

        </button>

        <!-- Logo -->
        <a href="<?= $base_url ?>/fideles/dashboard.php" class="text-white text-decoration-none fw-bold fs-5 me-4">

            <i class="bi bi-building"></i> Gestion Église

        </a>

        <!-- MENU -->
        <ul class="nav d-none d-lg-flex">

            <li class="nav-item">
                <a href="<?= $base_url ?>/fideles/dashboard.php" class="nav-link text-white">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= $base_url ?>/fideles/engagement_est_mes_paiements" class="nav-link text-white">
                    <i class="bi bi-people"></i>
                    Mes engagements & paiements
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= $base_url ?>/fideles/cultes/" class="nav-link text-white">
                    <i class="bi bi-book"></i>
                    Cultes
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= $base_url ?>/fideles/contributions_disponibles/" class="nav-link text-white">
                    <i class="bi bi-wallet2"></i>
                    Contributions
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= $base_url ?>/fideles/annonces/" class="nav-link text-white">
                    <i class="bi bi-megaphone"></i>
                    Annonces
                </a>
            </li>
        </ul>

    </div>

    <!-- RIGHT -->
    <div class="dropdown">

        <button class="btn btn-dark dropdown-toggle border-0 text-white" type="button" data-bs-toggle="dropdown">

            <i class="bi bi-person-circle"></i>
            <?= $_SESSION['user']['nom'] ?>

        </button>

        <ul class="dropdown-menu dropdown-menu-end">

            <li>
                <h6 class="dropdown-header">
                    <?= $_SESSION['user']['nom'] ?>
                </h6>
            </li>
            <li class="nav-item">
                <a class="dropdown-item" href="<?= $base_url ?>/fideles/my_account/">
                    <i class="bi bi-bar-chart"></i>
                    Mon compte
                </a>
            </li>
            <li>
                <hr class="dropdown-divider">
            </li>

            <li>
                <a class="dropdown-item text-danger" href="<?= $base_url ?>/core/logout.php">

                    <i class="bi bi-box-arrow-right"></i>
                    Déconnexion

                </a>
            </li>

        </ul>

    </div>

</div>