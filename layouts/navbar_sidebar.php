<?php
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

if (strpos($base_url, '/views') !== false) {
    $base_url = substr($base_url, 0, strpos($base_url, '/views'));
}

if ($base_url === '') {
    $base_url = '';
}
?>

<style>
.offcanvas .nav-link {
    border-radius: 8px;
    transition: .2s;
}

.offcanvas .nav-link:hover {
    background: rgba(255, 255, 255, .1);
    padding-left: 15px;
}

.offcanvas {
    width: 300px;
}
</style>

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
        <a href="<?= $base_url ?>/views/dashboard.php" class="text-white text-decoration-none fw-bold fs-5 me-4">

            <i class="bi bi-building"></i> Gestion Église

        </a>

        <!-- MENU -->
        <ul class="nav d-none d-lg-flex">

            <li class="nav-item">
                <a href="<?= $base_url ?>/views/dashboard.php" class="nav-link text-white">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= $base_url ?>/views/fideles/" class="nav-link text-white">
                    <i class="bi bi-people"></i>
                    Fidèles
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= $base_url ?>/views/cultes/" class="nav-link text-white">
                    <i class="bi bi-book"></i>
                    Cultes
                </a>
            </li>

            <li class="nav-item dropdown">

                <a class="nav-link text-white dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">

                    <i class="bi bi-folder2-open"></i>
                    Finances
                </a>

                <ul class="dropdown-menu dropdown-menu-dark">

                    <li>
                        <a class="dropdown-item" href="<?= $base_url ?>/views/depenses/">
                            <i class="bi bi-cash-stack"></i>
                            Dépenses
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item" href="<?= $base_url ?>/views/fonds/">
                            <i class="bi bi-wallet2"></i>
                            Contributions
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item" href="<?= $base_url ?>/views/comptes/">
                            <i class="bi bi-bank2"></i>
                            Gestion des comptes
                        </a>
                    </li>

                </ul>

            </li>

            <li class="nav-item">
                <a href="<?= $base_url ?>/views/annonces/" class="nav-link text-white">
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
                <a class="dropdown-item" href="<?= $base_url ?>/views/reports/">
                    <i class="bi bi-bar-chart"></i>
                    Rapports
                </a>
            </li>
            <?php if($_SESSION['user']['role'] == 'admin'): ?>
            <li class="nav-item">
                <a href="<?= $base_url ?>/views/users/" class="dropdown-item">
                    <i class="bi bi-person-gear"></i>
                    Gest. des utilisateurs
                </a>
            </li>
            <?php endif; ?>

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

<!-- SIDEBAR MOBILE -->
<div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="sidebar">

    <div class="offcanvas-header border-bottom border-secondary">

        <h5 class="offcanvas-title">
            <i class="bi bi-building"></i>
            Gestion Église
        </h5>

        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas">
        </button>

    </div>

    <div class="offcanvas-body">

        <ul class="nav flex-column">

            <li class="nav-item mb-2">
                <a href="<?= $base_url ?>/views/dashboard.php" class="nav-link text-white">
                    <i class="bi bi-speedometer2 me-2"></i>
                    Dashboard
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="<?= $base_url ?>/views/fideles/" class="nav-link text-white">
                    <i class="bi bi-people me-2"></i>
                    Fidèles
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="<?= $base_url ?>/views/cultes/" class="nav-link text-white">
                    <i class="bi bi-book me-2"></i>
                    Cultes
                </a>
            </li>

            <li>
                <hr class="border-secondary">
            </li>

            <li class="mb-2 text-uppercase small text-secondary">
                Finances
            </li>

            <li class="nav-item mb-2">
                <a href="<?= $base_url ?>/views/depenses/" class="nav-link text-white">
                    <i class="bi bi-cash-stack me-2"></i>
                    Dépenses
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="<?= $base_url ?>/views/fonds/" class="nav-link text-white">
                    <i class="bi bi-wallet2 me-2"></i>
                    Contributions
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="<?= $base_url ?>/views/comptes/" class="nav-link text-white">
                    <i class="bi bi-bank2 me-2"></i>
                    Gestion des comptes
                </a>
            </li>

            <li>
                <hr class="border-secondary">
            </li>

            <li class="nav-item mb-2">
                <a href="<?= $base_url ?>/views/annonces/" class="nav-link text-white">
                    <i class="bi bi-megaphone me-2"></i>
                    Annonces
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="<?= $base_url ?>/views/reports/" class="nav-link text-white">
                    <i class="bi bi-bar-chart me-2"></i>
                    Rapports
                </a>
            </li>

            <?php if($_SESSION['user']['role'] == 'admin'): ?>
            <li class="nav-item mb-2">
                <a href="<?= $base_url ?>/views/users/" class="nav-link text-white">
                    <i class="bi bi-person-gear me-2"></i>
                    Gest. des utilisateurs
                </a>
            </li>
            <?php endif; ?>

            <li>
                <hr class="border-secondary">
            </li>

            <li class="mb-3">
                <div class="text-secondary small">
                    Connecté en tant que
                </div>

                <div class="fw-bold">
                    <i class="bi bi-person-circle"></i>
                    <?= $_SESSION['user']['nom'] ?>
                </div>
            </li>

            <li>
                <a href="<?= $base_url ?>/core/logout.php" class="btn btn-danger w-100">
                    <i class="bi bi-box-arrow-right"></i>
                    Déconnexion
                </a>
            </li>

        </ul>

    </div>

</div>

<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->