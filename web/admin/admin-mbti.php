<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'auth.php';
require_once __DIR__ . '/../config/db.php';

/* =====================================
   TAMBAH MBTI
===================================== */

if(isset($_POST['add_mbti'])){

    $code        = trim($_POST['code']);
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);

    // cek duplicate
    $check = $conn->prepare("
        SELECT id
        FROM mbti_types
        WHERE code = ?
    ");

    $check->bind_param("s", $code);
    $check->execute();

    $exist = $check->get_result();

    if($exist->num_rows < 1){

        $stmt = $conn->prepare("
            INSERT INTO mbti_types
            (code, name, description)
            VALUES (?, ?, ?)
        ");

        $stmt->bind_param(
            "sss",
            $code,
            $name,
            $description
        );

        $stmt->execute();
    }

    header("Location: admin-mbti.php");
    exit;
}

/* =====================================
   DELETE MBTI
===================================== */

if(isset($_GET['delete'])){

    $id = (int) $_GET['delete'];

    $stmt = $conn->prepare("
        DELETE FROM mbti_types
        WHERE id = ?
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: admin-mbti.php");
    exit;
}

/* =====================================
   DATA MBTI
===================================== */

$mbti = $conn->query("
    SELECT *
    FROM mbti_types
    ORDER BY code ASC
");

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1">

    <title>Data MBTI</title>

    <!-- SB ADMIN -->
    <link href="vendor/fontawesome-free/css/all.min.css"
        rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900"
        rel="stylesheet">

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
    <li class="nav-item">
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
    <li class="nav-item active">
        <a class="nav-link" href="admin-mbti.php">
            <i class="fas fa-brain"></i>
            <span>Data MBTI</span>
        </a>
    </li>

    <!-- RELASI MBTI-RIASEC -->
    <li class="nav-item">
        <a class="nav-link" href="admin-mbti-riasec.php">
            <i class="fas fa-random"></i>
            <span>Relasi MBTI-RIASEC</span>
        </a>
    </li>

    <!-- RELASI MBTI-KARIR -->
    <li class="nav-item">
        <a class="nav-link" href="admin-mbti-careers.php">
            <i class="fas fa-link"></i>
            <span>Relasi MBTI-Karir</span>
        </a>
    </li>

    <!-- RELASI MBTI-BIDANG -->
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

    <!-- RELASI RIASEC-KARIR -->
    <li class="nav-item">
        <a class="nav-link" href="admin-riasec-careers.php">
            <i class="fas fa-briefcase"></i>
            <span>Relasi RIASEC-Karir</span>
        </a>
    </li>

    <!-- RELASI RIASEC-JURUSAN -->
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

    <!-- SIDEBAR TOGGLER -->
    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">

        <button class="rounded-circle border-0"
            id="sidebarToggle"></button>

    </div>

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
                Data MBTI
            </h4>

        </nav>

        <!-- CONTAINER -->
        <div class="container-fluid">

            <!-- FORM -->
            <div class="card shadow mb-4">

                <div class="card-header py-3">

                    <h6 class="m-0 font-weight-bold text-primary">
                        Tambah Data MBTI
                    </h6>

                </div>

                <div class="card-body">

                    <form method="POST">

                        <div class="row">

                            <div class="col-md-2 mb-3">

                                <label>Kode MBTI</label>

                                <select name="code"
                                    class="form-control"
                                    required>

                                    <option value="">
                                        -- Pilih MBTI --
                                    </option>

                                    <option value="INTJ">INTJ</option>
                                    <option value="INTP">INTP</option>
                                    <option value="ENTJ">ENTJ</option>
                                    <option value="ENTP">ENTP</option>

                                    <option value="INFJ">INFJ</option>
                                    <option value="INFP">INFP</option>
                                    <option value="ENFJ">ENFJ</option>
                                    <option value="ENFP">ENFP</option>

                                    <option value="ISTJ">ISTJ</option>
                                    <option value="ISFJ">ISFJ</option>
                                    <option value="ESTJ">ESTJ</option>
                                    <option value="ESFJ">ESFJ</option>

                                    <option value="ISTP">ISTP</option>
                                    <option value="ISFP">ISFP</option>
                                    <option value="ESTP">ESTP</option>
                                    <option value="ESFP">ESFP</option>

                                </select>

                            </div>

                            <div class="col-md-3 mb-3">

                                <label>Nama MBTI</label>

                                <input type="text"
                                    name="name"
                                    class="form-control"
                                    placeholder="Arsitek"
                                    required>

                            </div>

                            <div class="col-md-5 mb-3">

                                <label>Deskripsi</label>

                                <input type="text"
                                    name="description"
                                    class="form-control"
                                    placeholder="Strategis, mandiri, dan logis"
                                    required>

                            </div>

                            <div class="col-md-2 mb-3 d-flex align-items-end">

                                <button type="submit"
                                    name="add_mbti"
                                    class="btn btn-primary btn-block">

                                    <i class="fas fa-plus"></i>
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
                        List Data MBTI
                    </h6>

                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered">

                            <thead class="thead-dark">

                                <tr>

                                    <th width="60">No</th>
                                    <th>Kode MBTI</th>
                                    <th>Nama MBTI</th>
                                    <th>Deskripsi</th>
                                    <th width="120">Aksi</th>

                                </tr>

                            </thead>

                            <tbody>

                            <?php if($mbti->num_rows > 0): ?>

                                <?php $no = 1; ?>

                                <?php while($row = $mbti->fetch_assoc()): ?>

                                <tr>

                                    <td><?= $no++ ?></td>

                                    <td>
                                        <strong><?= $row['code'] ?></strong>
                                    </td>

                                    <td><?= $row['name'] ?></td>

                                    <td><?= $row['description'] ?></td>

                                    <td>

                                        <a href="?delete=<?= $row['id'] ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Hapus data MBTI ini?')">

                                            <i class="fas fa-trash"></i>

                                        </a>

                                    </td>

                                </tr>

                                <?php endwhile; ?>

                            <?php else: ?>

                                <tr>

                                    <td colspan="5"
                                        class="text-center text-muted">

                                        Belum ada data MBTI.

                                    </td>

                                </tr>

                            <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

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