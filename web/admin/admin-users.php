<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/db.php';

/* =====================================
   TAMBAH USER
===================================== */

if(isset($_POST['add_user'])){

    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone    = trim($_POST['phone']);
    $class    = trim($_POST['class']);

    if(
        !empty($name) &&
        !empty($email) &&
        !empty($password)
    ){

        // cek email
        $check = $conn->prepare("
            SELECT id
            FROM users
            WHERE email = ?
        ");

        $check->bind_param("s", $email);
        $check->execute();

        $exist = $check->get_result();

        if($exist->num_rows < 1){

            $role = "user";

            $stmt = $conn->prepare("
                INSERT INTO users
                (
                    name,
                    email,
                    password,
                    phone,
                    class,
                    role
                )
                VALUES
                (?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "ssssss",
                $name,
                $email,
                $password,
                $phone,
                $class,
                $role
            );

            $stmt->execute();

            header("Location: admin-users.php");
            exit;
        }
    }
}

/* =====================================
   DELETE USER
===================================== */

if(isset($_GET['delete'])){

    $id = (int) $_GET['delete'];

    // jangan hapus akun sendiri
    if($id != ($_SESSION['user_id'] ?? 0)){

        $stmt = $conn->prepare("
            DELETE FROM users
            WHERE id = ?
        ");

        $stmt->bind_param("i", $id);

        $stmt->execute();
    }

    header("Location: admin-users.php");
    exit;
}

/* =====================================
   GET USERS
===================================== */

$users = $conn->query("
    SELECT *
    FROM users
    ORDER BY created_at DESC
");

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1">

    <title>Data Pengguna</title>

    <!-- FONT -->
    <link href="vendor/fontawesome-free/css/all.min.css"
        rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900"
        rel="stylesheet">

    <!-- CSS -->
    <link href="css/sb-admin-2.min.css"
        rel="stylesheet">

</head>

<body id="page-top">

<div id="wrapper">

 <!-- SIDEBAR -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion"
    id="accordionSidebar">

    <!-- BRAND -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center"
        href="index.php">

        <div class="sidebar-brand-icon">
            <i class="fas fa-brain"></i>
        </div>

        <div class="sidebar-brand-text mx-2">
            PrediksiKarir
        </div>

    </a>

    <hr class="sidebar-divider my-0">

    <!-- DASHBOARD -->
    <li class="nav-item">
        <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- DATA PENGGUNA -->
    <li class="nav-item active">
        <a class="nav-link" href="admin-users.php">
            <i class="fas fa-users"></i>
            <span>Data Pengguna</span>
        </a>
    </li>

    <!-- DATA HASIL TES -->
    <li class="nav-item">
        <a class="nav-link" href="admin-results.php">
            <i class="fas fa-poll"></i>
            <span>Data Hasil Tes</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- MBTI -->
    <div class="sidebar-heading">
        MBTI
    </div>

    <!-- DATA MBTI -->
    <li class="nav-item">
        <a class="nav-link" href="admin-mbti.php">
            <i class="fas fa-brain"></i>
            <span>Data MBTI</span>
        </a>
    </li>

    <!-- RELASI MBTI RIASEC -->
    <li class="nav-item">
        <a class="nav-link" href="admin-mbti-riasec.php">
            <i class="fas fa-random"></i>
            <span>Relasi MBTI-RIASEC</span>
        </a>
    </li>

    <!-- RELASI MBTI KARIR -->
    <li class="nav-item">
        <a class="nav-link" href="admin-mbti-careers.php">
            <i class="fas fa-link"></i>
            <span>Relasi MBTI-Karir</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="admin-mbti-fields.php">
            <i class="fas fa-layer-group"></i>
            <span>Relasi MBTI-Bidang</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- RIASEC -->
    <div class="sidebar-heading">
        RIASEC
    </div>

    <!-- SOAL RIASEC -->
    <li class="nav-item">
        <a class="nav-link" href="admin-riasec-questions.php">
            <i class="fas fa-book"></i>
            <span>Soal RIASEC</span>
        </a>
    </li>

    <!-- RELASI RIASEC KARIR -->
    <li class="nav-item">
        <a class="nav-link" href="admin-riasec-careers.php">
            <i class="fas fa-briefcase"></i>
            <span>Relasi RIASEC-Karir</span>
        </a>
    </li>

    <!-- RELASI RIASEC JURUSAN -->
    <li class="nav-item">
        <a class="nav-link" href="admin-riasec-majors.php">
            <i class="fas fa-university"></i>
            <span>Relasi RIASEC-Jurusan</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- DATA MASTER -->
    <div class="sidebar-heading">
        Data Master
    </div>

    <!-- DATA KARIR -->
    <li class="nav-item">
        <a class="nav-link" href="admin-careers.php">
            <i class="fas fa-briefcase"></i>
            <span>Data Karir</span>
        </a>
    </li>

    <!-- DATA JURUSAN -->
    <li class="nav-item">
        <a class="nav-link" href="admin-majors.php">
            <i class="fas fa-graduation-cap"></i>
            <span>Data Jurusan</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- PESAN MASUK -->
    <li class="nav-item">
        <a class="nav-link" href="admin-contacts.php">
            <i class="fas fa-envelope"></i>
            <span>Pesan Masuk</span>
        </a>
    </li>

</ul>

    <!-- CONTENT -->
    <div id="content-wrapper"
        class="d-flex flex-column">

        <div id="content">

            <!-- TOPBAR -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                <button id="sidebarToggleTop"
                    class="btn btn-link d-md-none rounded-circle mr-3">

                    <i class="fa fa-bars"></i>

                </button>

                <h4 class="m-0 text-primary">
                    Data Pengguna
                </h4>

                <ul class="navbar-nav ml-auto">

                    <li class="nav-item dropdown no-arrow">

                        <a class="nav-link dropdown-toggle"
                            href="#"
                            role="button"
                            data-toggle="dropdown">

                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">

                                <?= htmlspecialchars($_SESSION['name'] ?? 'Admin') ?>

                            </span>

                            <img class="img-profile rounded-circle"
                                src="img/undraw_profile.svg">

                        </a>

                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">

                            <a class="dropdown-item"
                                href="admin-logout.php">

                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>

                                Logout

                            </a>

                        </div>

                    </li>

                </ul>

            </nav>

            <!-- CONTAINER -->
            <div class="container-fluid">

                <!-- TITLE -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">

                    <h1 class="h3 mb-0 text-gray-800">
                        Manajemen Pengguna
                    </h1>

                </div>

                <!-- ADD USER -->
                <div class="card shadow mb-4">

                    <div class="card-header py-3">

                        <h6 class="m-0 font-weight-bold text-primary">

                            Tambah User

                        </h6>

                    </div>

                    <div class="card-body">

                        <form method="POST">

                            <div class="row">

                                <!-- NAMA -->
                                <div class="col-md-3 mb-3">

                                    <input
                                        type="text"
                                        name="name"
                                        class="form-control"
                                        placeholder="Nama"
                                        required>

                                </div>

                                <!-- EMAIL -->
                                <div class="col-md-2 mb-3">

                                    <input
                                        type="email"
                                        name="email"
                                        class="form-control"
                                        placeholder="Email"
                                        required>

                                </div>

                                <!-- PHONE -->
                                <div class="col-md-2 mb-3">

                                    <input
                                        type="text"
                                        name="phone"
                                        class="form-control"
                                        placeholder="No. Telepon"
                                        required>

                                </div>

                                <!-- PASSWORD -->
                                <div class="col-md-2 mb-3">

                                    <input
                                        type="text"
                                        name="password"
                                        class="form-control"
                                        placeholder="Password"
                                        required>

                                </div>

                                <!-- CLASS -->
                                <div class="col-md-1 mb-3">

                                    <select
                                        name="class"
                                        class="form-control">

                                        <option value="10">
                                            Kelas 10
                                        </option>

                                        <option value="11">
                                            Kelas 11
                                        </option>

                                        <option value="12">
                                            Kelas 12
                                        </option>

                                    </select>

                                </div>

                                <!-- BUTTON -->
                                <div class="col-md-2 mb-3">

                                    <button
                                        type="submit"
                                        name="add_user"
                                        class="btn btn-success btn-block">

                                        Tambah

                                    </button>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>

                <!-- TABLE -->
                <div class="card shadow mb-4">

                    <div class="card-header py-3">

                        <h6 class="m-0 font-weight-bold text-primary">

                            Daftar Pengguna

                        </h6>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered">

                                <thead class="thead-dark">

                                    <tr>

                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>No. Telepon</th>
                                        <th>Kelas</th>
                                        <th>Role</th>
                                        <th>Tanggal</th>
                                        <th width="120">Aksi</th>

                                    </tr>

                                </thead>

                                <tbody>

                                <?php if($users->num_rows > 0): ?>

                                    <?php while($row = $users->fetch_assoc()): ?>

                                    <tr>

                                        <!-- NAMA -->
                                        <td>

                                            <?= htmlspecialchars($row['name'] ?? '') ?>

                                        </td>

                                        <!-- EMAIL -->
                                        <td>

                                            <?= htmlspecialchars($row['email'] ?? '') ?>

                                        </td>

                                        <!-- PHONE -->
                                        <td>

                                            <?= htmlspecialchars($row['phone'] ?? '-') ?>

                                        </td>

                                        <!-- KELAS -->
                                        <td>

                                            <?php if(($row['role'] ?? '') == 'admin'): ?>

                                                <span class="text-muted">
                                                    Admin
                                                </span>

                                            <?php else: ?>

                                                Kelas <?= htmlspecialchars($row['class'] ?? '-') ?>

                                            <?php endif; ?>

                                        </td>

                                        <!-- ROLE -->
                                        <td>

                                            <?php if(($row['role'] ?? '') == 'admin'): ?>

                                                <span class="badge badge-danger px-3 py-2">
                                                    Admin
                                                </span>

                                            <?php else: ?>

                                                <span class="badge badge-primary px-3 py-2">
                                                    User
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                        <!-- TANGGAL -->
                                        <td>

                                            <?= date(
                                                'd M Y H:i',
                                                strtotime($row['created_at'] ?? '')
                                            ) ?>

                                        </td>

                                        <!-- AKSI -->
                                        <td>

                                            <?php if(($row['id'] ?? 0) != ($_SESSION['user_id'] ?? 0)): ?>

                                            <a href="?delete=<?= $row['id'] ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Hapus user ini?')">

                                                Hapus

                                            </a>

                                            <?php else: ?>

                                                <span class="text-muted small">
                                                    akun aktif
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                    </tr>

                                    <?php endwhile; ?>

                                <?php else: ?>

                                    <tr>

                                        <td colspan="7"
                                            class="text-center text-muted">

                                            Belum ada pengguna.

                                        </td>

                                    </tr>

                                <?php endif; ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>
            <!-- END CONTAINER -->

        </div>

        <!-- FOOTER -->
        <footer class="sticky-footer bg-white">

            <div class="container my-auto">

                <div class="copyright text-center my-auto">

                    <span>
                        Copyright &copy;
                        PrediksiKarir <?= date('Y') ?>
                    </span>

                </div>

            </div>

        </footer>

    </div>

</div>

<!-- JS -->
<script src="vendor/jquery/jquery.min.js"></script>

<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<script src="js/sb-admin-2.min.js"></script>

</body>
</html>