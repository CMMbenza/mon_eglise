<?php
session_start();

require_once '../config/database.php';

$error = '';


// =====================================
// LOGIN
// =====================================

if(isset($_POST['login'])){

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if(empty($email) || empty($password)){

        $error = "Veuillez remplir tous les champs.";

    }else{

        $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // =====================================
        // VERIFICATION PASSWORD
        // =====================================

        if($user && password_verify($password, $user['mot_de_passe'])){

            $_SESSION['user'] = [
                'id'   => $user['id'],
                'nom'  => $user['nom'],
                'role' => $user['role']
            ];

            // =====================================
            // REDIRECTION SELON ROLE
            // =====================================

            if($user['role'] == 'admin'){

                header('Location: ../views/dashboard.php');
                exit;

            }else{

                header('Location: ../visiteur/dashboard.php');
                exit;
            }

        }else{

            $error = "Nom d'utilisateur ou mot de passe incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Connexion - Mon Église
    </title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
    body {
        background: linear-gradient(135deg, #f4f6f9, #e9ecef);
        min-height: 100vh;
        display: flex;
        align-items: center;
    }

    .login-card {
        border: none;
        border-radius: 25px;
        overflow: hidden;
    }

    .login-left {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        color: white;
        padding: 50px;
        height: 100%;
    }

    .church-icon {
        width: 90px;
        height: 90px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 42px;
        margin-bottom: 25px;
    }

    .login-right {
        padding: 50px;
        background: white;
    }

    .form-control {
        border-radius: 12px;
        padding: 14px;
        border: 1px solid #dee2e6;
    }

    .form-control:focus {
        box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .15);
        border-color: #0d6efd;
    }

    .btn-login {
        border-radius: 12px;
        padding: 14px;
        font-weight: 600;
        font-size: 16px;
    }

    .input-group-text {
        border-radius: 12px 0 0 12px;
        background: #f8f9fa;
    }

    .footer-text {
        color: #6c757d;
    }

    @media(max-width: 991px) {

        .login-left {
            display: none;
        }

        .login-right {
            padding: 35px;
        }
    }
    </style>

</head>

<body>

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-xl-10">

                <div class="card shadow-lg login-card">

                    <div class="row g-0">

                        <!-- LEFT -->
                        <div class="col-lg-5">

                            <div class="login-left d-flex flex-column justify-content-center">

                                <div class="church-icon">
                                    <i class="bi bi-building"></i>
                                </div>

                                <h1 class="fw-bold mb-3">
                                    Mon Église
                                </h1>

                                <p class="mb-4 fs-5">

                                    Plateforme de gestion des fidèles, cultes, finances et activités de l’église.

                                </p>

                                <div class="d-flex flex-column gap-3">

                                    <div>
                                        <i class="bi bi-check-circle-fill"></i>
                                        Gestion des cultes
                                    </div>

                                    <div>
                                        <i class="bi bi-check-circle-fill"></i>
                                        Gestion des finances
                                    </div>

                                    <div>
                                        <i class="bi bi-check-circle-fill"></i>
                                        Suivi des fidèles
                                    </div>

                                    <div>
                                        <i class="bi bi-check-circle-fill"></i>
                                        Contributions & fonds
                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- RIGHT -->
                        <div class="col-lg-7">

                            <div class="login-right">

                                <div class="text-center mb-4">

                                    <h2 class="fw-bold">
                                        Connexion
                                    </h2>

                                    <p class="text-muted">
                                        Connectez-vous à votre compte
                                    </p>

                                </div>

                                <?php if($error): ?>

                                <div class="alert alert-danger alert-dismissible fade show">

                                    <i class="bi bi-exclamation-triangle-fill"></i>

                                    <?= $error ?>

                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

                                </div>

                                <?php endif; ?>


                                <form method="POST">

                                    <!-- EMAIL -->
                                    <div class="mb-3">

                                        <label class="mb-2 fw-semibold">
                                            Adresse email
                                        </label>

                                        <div class="input-group">

                                            <span class="input-group-text">
                                                <i class="bi bi-envelope-fill"></i>
                                            </span>

                                            <input type="email" name="email" class="form-control"
                                                placeholder="exemple@email.com" required>

                                        </div>

                                    </div>


                                    <!-- PASSWORD -->
                                    <div class="mb-4">

                                        <label class="mb-2 fw-semibold">
                                            Mot de passe
                                        </label>

                                        <div class="input-group">

                                            <span class="input-group-text">
                                                <i class="bi bi-lock-fill"></i>
                                            </span>

                                            <input type="password" name="password" class="form-control"
                                                placeholder="********" required>

                                        </div>

                                    </div>


                                    <!-- BTN -->
                                    <button type="submit" name="login" class="btn btn-primary btn-login w-100">

                                        <i class="bi bi-box-arrow-in-right me-2"></i>

                                        Se connecter

                                    </button>

                                </form>


                                


                                

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>