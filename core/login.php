<?php
session_start();

require_once '../config/database.php';

$error = '';

if(isset($_POST['login'])){

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // =========================
    // LOGIN SUCCESS
    // =========================

    if($user && password_verify($password, $user['mot_de_passe'])){

        $_SESSION['user'] = [
            'id' => $user['id'],
            'nom' => $user['nom'],
            'role' => $user['role']
        ];


        // =========================
        // REDIRECTION ROLE
        // =========================

        if($user['role'] == 'admin'){

            // ADMIN
            header('Location: ../views/dashboard.php');
            exit;

        }else{

            // VISITEUR / FIDELE
            header('Location: ../fideles/dashboard.php');
            exit;
        }

    }else{

        $error = "Nom d'utilisateur ou mot de passe incorrect";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Bienvenue à l'Église connecter vous
    </title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
    body {
        background: #f4f6f9;
        min-height: 100vh;
    }

    .register-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
    }

    .register-header {
        background: linear-gradient(45deg, #212529, #343a40);
        color: white;
        padding: 25px;
    }

    .logo {
        font-size: 55px;
        margin-right: 15px;
    }

    .card-body {
        background: white;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #0d6efd;
        margin-bottom: 20px;
    }

    .form-control,
    .form-select {
        border-radius: 10px;
        padding: 12px;
    }

    .form-control:focus,
    .form-select:focus {
        box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .15);
        border-color: #0d6efd;
    }

    .btn-register {
        border-radius: 10px;
        padding: 13px;
        font-size: 17px;
        font-weight: 600;
    }

    label {
        font-weight: 500;
        margin-bottom: 5px;
    }

    .footer-text {
        color: #6c757d;
    }
    </style>

</head>

<body>

    <div class="container p-5">

        <div class="row justify-content-center">

            <div class="col-lg-10">

                <div class="card shadow-lg register-card">

                    <!-- HEADER -->
                    <div class="register-header d-flex align-items-center">

                        <div class="logo">
                            <i class="bi bi-building"></i>
                        </div>

                        <div>

                            <h2 class="fw-bold mb-1">
                                Bienvenue à l'Église
                            </h2>

                            <p class="mb-0">
                                Connectez-vous à votre compte !
                            </p>

                        </div>

                    </div>

                    <!-- BODY -->
                    <div class="card-body p-4 p-lg-5">

                        <?php if($error): ?>
                        <div class="alert alert-danger">
                            <?= $error ?>
                        </div>
                        <?php endif; ?>

                        <form method="POST" autocomplete="off">

                            <!-- EMAIL -->
                            <div class="mb-3">

                                <label class="mb-2 fw-semibold">
                                    Nom d'utilisateur
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-envelope-fill"></i>
                                    </span>

                                    <input type="text" name="email" class="form-control"
                                        placeholder="Entrez votre nom d'utilisateur" required>

                                </div>

                            </div>


                            <!-- PASSWORD -->
                            <div class="mb-2">

                                <label class="mb-2 fw-semibold">
                                    Mot de passe
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>

                                    <input type="password" id="password" name="password" class="form-control"
                                        placeholder="********" required>

                                </div>

                            </div>

                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="showPassword">
                                <label class="form-check-label" for="showPassword">
                                    Afficher le mot de passe
                                </label>
                            </div>

                            <!-- BTN -->
                            <button type="submit" name="login" class="btn btn-primary btn-login w-100 mt-4">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Se connecter
                            </button>
                        </form>

                        <!-- LINKS -->
                        <div class="text-center mt-4">

                            <p class="mb-2 text-muted">
                                Vous n'avez pas encore de compte ?
                            </p>

                            <a href="../enregistrement_des_fideles.php" class="text-decoration-none fw-semibold">
                                <i class="bi bi-person-plus-fill"></i>
                                Créer un compte fidèle
                            </a>

                        </div>

                    </div>
                    <!-- FOOTER -->
                    <div class="text-center mb-5 footer-text">

                        <small>

                            &copy; <?= date('Y') ?>

                            Mon Église - Tous droits réservés

                        </small>

                    </div>
                </div>



            </div>

        </div>

    </div>

    <!-- Bootstrap JS -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('showPassword').addEventListener('change', function() {
        const password = document.getElementById('password');

        if (this.checked) {
            password.type = 'text';
        } else {
            password.type = 'password';
        }
    });
    </script>
</body>

</html>