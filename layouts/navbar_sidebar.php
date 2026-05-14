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
        <a href="/mon-eglise/views/dashboard.php" class="text-white text-decoration-none fw-bold fs-5 me-4">

            <i class="bi bi-building"></i> Gestion Église

        </a>

        <!-- MENU -->
        <ul class="nav d-none d-lg-flex">

            <li class="nav-item">
                <a href="/mon-eglise/views/dashboard.php" class="nav-link text-white">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a href="/mon-eglise/views/fideles/" class="nav-link text-white">
                    <i class="bi bi-people"></i>
                    Fidèles
                </a>
            </li>

            <li class="nav-item">
                <a href="/mon-eglise/views/cultes/" class="nav-link text-white">
                    <i class="bi bi-book"></i>
                    Cultes
                </a>
            </li>

            <li class="nav-item">
                <a href="/mon-eglise/views/depenses/" class="nav-link text-white">
                    <i class="bi bi-cash-stack"></i>
                    Dépenses
                </a>
            </li>

            <li class="nav-item">
                <a href="/mon-eglise/views/fonds/" class="nav-link text-white">
                    <i class="bi bi-wallet2"></i>
                    Contributions
                </a>
            </li>

            <li class="nav-item">
                <a href="/mon-eglise/views/annonces/" class="nav-link text-white">
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
                <a class="dropdown-item" href="/mon-eglise/views/reports/">
                    <i class="bi bi-bar-chart"></i>
                    Rapports
                </a>
            </li>
            <?php if($_SESSION['user']['role'] == 'admin'): ?>
            <li class="nav-item">
                <a href="/mon-eglise/views/users/" class="dropdown-item">
                    <i class="bi bi-person-gear"></i>
                    Gest. des utilisateurs
                </a>
            </li>
            <?php endif; ?>

            <li>
                <hr class="dropdown-divider">
            </li>

            <li>
                <a class="dropdown-item text-danger" href="/mon-eglise/core/logout.php">

                    <i class="bi bi-box-arrow-right"></i>
                    Déconnexion

                </a>
            </li>

        </ul>

    </div>

</div>