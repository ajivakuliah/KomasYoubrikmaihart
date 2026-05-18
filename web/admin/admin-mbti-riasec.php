<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'auth.php';
require_once __DIR__ . '/../config/db.php';

/* =====================================
   TAMBAH RELASI
===================================== */

if(isset($_POST['add_relation'])){

    $mbti_code   = $_POST['mbti_code'];
    $riasec_code = $_POST['riasec_code'];

    // cek duplicate
    $check = $conn->prepare("
        SELECT id
        FROM mbti_riasec
        WHERE mbti_code = ?
        AND riasec_code = ?
    ");

    $check->bind_param(
        "ss",
        $mbti_code,
        $riasec_code
    );

    $check->execute();

    $exist = $check->get_result();

    if($exist->num_rows < 1){

        $stmt = $conn->prepare("
            INSERT INTO mbti_riasec
            (mbti_code, riasec_code)
            VALUES (?, ?)
        ");

        $stmt->bind_param(
            "ss",
            $mbti_code,
            $riasec_code
        );

        $stmt->execute();
    }

    header("Location: admin-mbti-riasec.php");
    exit;
}

/* =====================================
   DELETE
===================================== */

if(isset($_GET['delete'])){

    $id = (int) $_GET['delete'];

    $stmt = $conn->prepare("
        DELETE FROM mbti_riasec
        WHERE id = ?
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: admin-mbti-riasec.php");
    exit;
}

/* =====================================
   GET DATA
===================================== */

$mbti = $conn->query("
    SELECT *
    FROM mbti_types
    ORDER BY code ASC
");

$riasec = $conn->query("
    SELECT *
    FROM riasec_types
    ORDER BY code ASC
");

$relations = $conn->query("
    SELECT
        mr.id,
        mt.code AS mbti_code,
        mt.name AS mbti_name,
        rt.code AS riasec_code,
        rt.label AS riasec_label

    FROM mbti_riasec mr

    JOIN mbti_types mt
    ON mt.code = mr.mbti_code

    JOIN riasec_types rt
    ON rt.code = mr.riasec_code

    ORDER BY mt.code ASC
");

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1">

    <title>Relasi MBTI - RIASEC</title>

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
    <li class="nav-item">
        <a class="nav-link" href="admin-mbti.php">
            <i class="fas fa-brain"></i>
            <span>Data MBTI</span>
        </a>
    </li>

    <!-- RELASI MBTI RIASEC -->
    <li class="nav-item active">
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
                    Relasi MBTI - RIASEC
                </h4>

            </nav>

            <!-- CONTAINER -->
            <div class="container-fluid">

                <!-- FORM -->
                <div class="card shadow mb-4">

                    <div class="card-header py-3">

                        <h6 class="m-0 font-weight-bold text-primary">
                            Tambah Relasi
                        </h6>

                    </div>

                    <div class="card-body">

                        <form method="POST">

                            <div class="row">

                                <!-- MBTI -->
                                <div class="col-md-5 mb-3">

                                    <label>MBTI</label>

                                    <select name="mbti_code"
                                        class="form-control"
                                        required>

                                        <option value="">
                                            -- Pilih MBTI --
                                        </option>

                                        <?php
                                        $mbti->data_seek(0);
                                        while($m = $mbti->fetch_assoc()):
                                        ?>

                                        <option value="<?= $m['code'] ?>">

                                            <?= $m['code'] ?>
                                            -
                                            <?= $m['name'] ?>

                                        </option>

                                        <?php endwhile; ?>

                                    </select>

                                </div>

                                <!-- RIASEC -->
                                <div class="col-md-5 mb-3">

                                    <label>RIASEC</label>

                                    <select name="riasec_code"
                                        class="form-control"
                                        required>

                                        <option value="">
                                            -- Pilih RIASEC --
                                        </option>

                                        <?php
                                        $riasec->data_seek(0);
                                        while($r = $riasec->fetch_assoc()):
                                        ?>

                                        <option value="<?= $r['code'] ?>">

                                            <?= $r['code'] ?>
                                            -
                                            <?= $r['label'] ?>

                                        </option>

                                        <?php endwhile; ?>

                                    </select>

                                </div>

                                <!-- BUTTON -->
                                <div class="col-md-2 mb-3 d-flex align-items-end">

                                    <button type="submit"
                                        name="add_relation"
                                        class="btn btn-primary btn-block">

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
                            Data Relasi MBTI - RIASEC
                        </h6>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered">

                                <thead class="thead-dark">

                                    <tr>

                                        <th width="60">No</th>
                                        <th>MBTI</th>
                                        <th>RIASEC</th>
                                        <th width="120">Aksi</th>

                                    </tr>

                                </thead>

                                <tbody>

                                <?php if($relations->num_rows > 0): ?>

                                    <?php $no = 1; ?>

                                    <?php while($row = $relations->fetch_assoc()): ?>

                                    <tr>

                                        <td><?= $no++ ?></td>

                                        <td>

                                            <strong>
                                                <?= $row['mbti_code'] ?>
                                            </strong>

                                            -
                                            <?= $row['mbti_name'] ?>

                                        </td>

                                        <td>

                                            <strong>
                                                <?= $row['riasec_code'] ?>
                                            </strong>

                                            -
                                            <?= $row['riasec_label'] ?>

                                        </td>

                                        <td>

                                            <a href="?delete=<?= $row['id'] ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Hapus relasi ini?')">

                                                <i class="fas fa-trash"></i>

                                            </a>

                                        </td>

                                    </tr>

                                    <?php endwhile; ?>

                                <?php else: ?>

                                    <tr>

                                        <td colspan="4"
                                            class="text-center text-muted">

                                            Belum ada relasi.

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