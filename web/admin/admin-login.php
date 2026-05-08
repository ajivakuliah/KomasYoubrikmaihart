<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once __DIR__ . '/../config/db.php';

$error = "";

/* =========================
   LOGIN
========================= */

if(isset($_POST['login'])){

    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    /* VALIDASI */
    if(empty($email) || empty($password)){

        $error = "Email dan password wajib diisi!";

    } else {

        /* AMBIL USER */
        $stmt = $conn->prepare("
            SELECT *
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();

        /* LOGIN BERHASIL */
        if(
            $result &&
            $password === $result['password'] &&
            $result['role'] === 'admin'
        ){

            $_SESSION['user_id'] = $result['id'];
            $_SESSION['role']    = $result['role'];
            $_SESSION['name']    = $result['name'];

            header("Location: index.php");
            exit;
        }

        /* LOGIN GAGAL */
        else{

            $error = "Email / Password admin salah!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible"
        content="IE=edge">

    <meta name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Login Admin - PrediksiKarir</title>

    <!-- FONT -->
    <link href="vendor/fontawesome-free/css/all.min.css"
        rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900"
        rel="stylesheet">

    <!-- CSS -->
    <link href="css/sb-admin-2.min.css"
        rel="stylesheet">

    <style>

        .bg-login-image{

            background:
            linear-gradient(
                rgba(78,115,223,0.7),
                rgba(78,115,223,0.7)
            ),
            url('img/login-bg.jpg');

            background-position: center;
            background-size: cover;
        }

    </style>

</head>

<body class="bg-gradient-primary">

<div class="container">

    <!-- OUTER ROW -->
    <div class="row justify-content-center">

        <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5">

                <div class="card-body p-0">

                    <!-- NESTED ROW -->
                    <div class="row">

                        <!-- IMAGE -->
                        <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>

                        <!-- FORM -->
                        <div class="col-lg-6">

                            <div class="p-5">

                                <!-- TITLE -->
                                <div class="text-center">

                                    <h1 class="h4 text-gray-900 mb-2">
                                        PrediksiKarir Admin
                                    </h1>

                                    <p class="mb-4 text-muted">
                                        Login untuk mengakses dashboard admin
                                    </p>

                                </div>

                                <!-- ERROR -->
                                <?php if($error): ?>

                                    <div class="alert alert-danger">

                                        <?= htmlspecialchars($error) ?>

                                    </div>

                                <?php endif; ?>

                                <!-- FORM -->
                                <form method="POST" class="user">

                                    <!-- EMAIL -->
                                    <div class="form-group">

                                        <input
                                            type="email"
                                            name="email"
                                            class="form-control form-control-user"
                                            placeholder="Masukkan Email"
                                            required>

                                    </div>

                                    <!-- PASSWORD -->
                                    <div class="form-group">

                                        <input
                                            type="password"
                                            name="password"
                                            class="form-control form-control-user"
                                            placeholder="Password"
                                            required>

                                    </div>

                                    <!-- BUTTON -->
                                    <button
                                        type="submit"
                                        name="login"
                                        class="btn btn-primary btn-user btn-block">

                                        <i class="fas fa-sign-in-alt mr-2"></i>

                                        Login Admin

                                    </button>

                                </form>

                                <hr>

                                <!-- BACK -->
                                <div class="text-center">

                                    <a class="small"
                                        href="../index.php">

                                        ← Kembali ke Website

                                    </a>

                                </div>

                            </div>

                        </div>
                        <!-- END FORM -->

                    </div>
                    <!-- END ROW -->

                </div>

            </div>

        </div>

    </div>

</div>

<!-- JS -->
<script src="vendor/jquery/jquery.min.js"></script>

<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<script src="js/sb-admin-2.min.js"></script>

</body>
</html>