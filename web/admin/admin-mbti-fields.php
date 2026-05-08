<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'auth.php';
require_once __DIR__ . '/../config/db.php';

/* =====================================
   TAMBAH RELASI
===================================== */

if(isset($_POST['add_field'])){

    $mbti_code = trim($_POST['mbti_code']);
    $field_name = trim($_POST['field_name']);

    if($mbti_code != '' && $field_name != ''){

        $check = $conn->prepare("
            SELECT id
            FROM mbti_fields
            WHERE mbti_code = ?
            AND field_name = ?
        ");

        $check->bind_param(
            "ss",
            $mbti_code,
            $field_name
        );

        $check->execute();

        $exist = $check->get_result();

        if($exist->num_rows < 1){

            $stmt = $conn->prepare("
                INSERT INTO mbti_fields
                (mbti_code, field_name)
                VALUES (?, ?)
            ");

            $stmt->bind_param(
                "ss",
                $mbti_code,
                $field_name
            );

            $stmt->execute();
        }
    }

    header("Location: admin-mbti-fields.php");
    exit;
}

/* =====================================
   UPDATE
===================================== */

if(isset($_POST['update_field'])){

    $id = (int) $_POST['id'];
    $mbti_code = trim($_POST['mbti_code']);
    $field_name = trim($_POST['field_name']);

    $stmt = $conn->prepare("
        UPDATE mbti_fields
        SET
            mbti_code = ?,
            field_name = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "ssi",
        $mbti_code,
        $field_name,
        $id
    );

    $stmt->execute();

    header("Location: admin-mbti-fields.php");
    exit;
}

/* =====================================
   DELETE
===================================== */

if(isset($_GET['delete'])){

    $id = (int) $_GET['delete'];

    $stmt = $conn->prepare("
        DELETE FROM mbti_fields
        WHERE id = ?
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: admin-mbti-fields.php");
    exit;
}

/* =====================================
   GET DATA
===================================== */

$fields = $conn->query("
    SELECT *
    FROM mbti_fields
    ORDER BY mbti_code ASC
");

$mbti = $conn->query("
    SELECT code
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

<title>Relasi MBTI-Bidang</title>

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

    <li class="nav-item">
        <a class="nav-link" href="admin-mbti.php">
            <i class="fas fa-brain"></i>
            <span>Data MBTI</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="admin-mbti-riasec.php">
            <i class="fas fa-random"></i>
            <span>Relasi MBTI-RIASEC</span>
        </a>
    </li>

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

    <li class="nav-item">
        <a class="nav-link" href="admin-riasec-questions.php">
            <i class="fas fa-book"></i>
            <span>Soal RIASEC</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="admin-riasec-careers.php">
            <i class="fas fa-briefcase"></i>
            <span>Relasi RIASEC-Karir</span>
        </a>
    </li>

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

    <li class="nav-item">
        <a class="nav-link" href="admin-careers.php">
            <i class="fas fa-briefcase"></i>
            <span>Data Karir</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="admin-majors.php">
            <i class="fas fa-graduation-cap"></i>
            <span>Data Jurusan</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- PESAN -->
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

<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <button id="sidebarToggleTop"
        class="btn btn-link d-md-none rounded-circle mr-3">

        <i class="fa fa-bars"></i>

    </button>

    <h4 class="m-0 text-primary">
        Relasi MBTI-Bidang
    </h4>

</nav>

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

                <div class="col-md-3 mb-3">

                    <label>MBTI</label>

                    <select name="mbti_code"
                        class="form-control"
                        required>

                        <option value="">
                            Pilih MBTI
                        </option>

                        <?php while($m = $mbti->fetch_assoc()): ?>

                        <option value="<?= $m['code'] ?>">
                            <?= $m['code'] ?>
                        </option>

                        <?php endwhile; ?>

                    </select>

                </div>

                <div class="col-md-7 mb-3">

                    <label>Bidang</label>

                    <input type="text"
                        name="field_name"
                        class="form-control"
                        placeholder="Contoh: Teknologi"
                        required>

                </div>

                <div class="col-md-2 mb-3 d-flex align-items-end">

                    <button type="submit"
                        name="add_field"
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
            Data Relasi MBTI-Bidang
        </h6>

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered">

                <thead class="thead-dark">

                    <tr>

                        <th width="60">No</th>
                        <th width="120">MBTI</th>
                        <th>Bidang</th>
                        <th width="180">Aksi</th>

                    </tr>

                </thead>

                <tbody>

                <?php if($fields->num_rows > 0): ?>

                    <?php $no = 1; ?>

                    <?php while($row = $fields->fetch_assoc()): ?>

                    <tr>

                        <td><?= $no++ ?></td>

                        <td>
                            <strong><?= $row['mbti_code'] ?></strong>
                        </td>

                        <td><?= $row['field_name'] ?></td>

                        <td>

                            <button class="btn btn-warning btn-sm"
                                data-toggle="modal"
                                data-target="#editModal<?= $row['id'] ?>">

                                <i class="fas fa-edit"></i>

                            </button>

                            <a href="?delete=<?= $row['id'] ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Hapus relasi ini?')">

                                <i class="fas fa-trash"></i>

                            </a>

                        </td>

                    </tr>

                    <!-- MODAL -->
                    <div class="modal fade"
                        id="editModal<?= $row['id'] ?>"
                        tabindex="-1">

                        <div class="modal-dialog">

                            <div class="modal-content">

                                <form method="POST">

                                    <div class="modal-header">

                                        <h5 class="modal-title">
                                            Edit Relasi
                                        </h5>

                                        <button type="button"
                                            class="close"
                                            data-dismiss="modal">

                                            <span>&times;</span>

                                        </button>

                                    </div>

                                    <div class="modal-body">

                                        <input type="hidden"
                                            name="id"
                                            value="<?= $row['id'] ?>">

                                        <div class="form-group">

                                            <label>MBTI</label>

                                            <input type="text"
                                                name="mbti_code"
                                                class="form-control"
                                                value="<?= $row['mbti_code'] ?>"
                                                required>

                                        </div>

                                        <div class="form-group">

                                            <label>Bidang</label>

                                            <input type="text"
                                                name="field_name"
                                                class="form-control"
                                                value="<?= $row['field_name'] ?>"
                                                required>

                                        </div>

                                    </div>

                                    <div class="modal-footer">

                                        <button type="submit"
                                            name="update_field"
                                            class="btn btn-primary">

                                            Simpan

                                        </button>

                                    </div>

                                </form>

                            </div>

                        </div>

                    </div>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="4"
                            class="text-center text-muted">

                            Belum ada data relasi.

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

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

</body>
</html>