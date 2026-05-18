<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/db.php';

/* =====================================
   TAMBAH KARIR
===================================== */

if(isset($_POST['add_career'])){

    $career_name = trim($_POST['career_name']);

    if($career_name != ''){

        // cek duplicate
        $check = $conn->prepare("
            SELECT id
            FROM careers
            WHERE career_name = ?
        ");

        $check->bind_param("s", $career_name);
        $check->execute();

        $exist = $check->get_result();

        if($exist->num_rows < 1){

            $stmt = $conn->prepare("
                INSERT INTO careers
                (career_name)
                VALUES (?)
            ");

            $stmt->bind_param(
                "s",
                $career_name
            );

            $stmt->execute();
        }

        header("Location: admin-careers.php");
        exit;
    }
}

/* =====================================
   UPDATE KARIR
===================================== */

if(isset($_POST['update_career'])){

    $id          = (int) $_POST['id'];
    $career_name = trim($_POST['career_name']);

    if($career_name != ''){

        $stmt = $conn->prepare("
            UPDATE careers
            SET career_name = ?
            WHERE id = ?
        ");

        $stmt->bind_param(
            "si",
            $career_name,
            $id
        );

        $stmt->execute();
    }

    header("Location: admin-careers.php");
    exit;
}

/* =====================================
   DELETE KARIR
===================================== */

if(isset($_GET['delete'])){

    $id = (int) $_GET['delete'];

    $stmt = $conn->prepare("
        DELETE FROM careers
        WHERE id = ?
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: admin-careers.php");
    exit;
}

/* =====================================
   SEARCH
===================================== */

$search = $_GET['search'] ?? '';

$query = "
SELECT *
FROM careers
";

if($search){

    $query .= "
    WHERE career_name LIKE ?
    ";
}

$query .= "
ORDER BY career_name ASC
";

$stmt = $conn->prepare($query);

if($search){

    $searchParam = "%$search%";

    $stmt->bind_param(
        "s",
        $searchParam
    );
}

$stmt->execute();

$careers = $stmt->get_result();

/* =====================================
   TOTAL KARIR
===================================== */

$totalCareers = $conn->query("
    SELECT COUNT(*) as total
    FROM careers
")->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1">

    <title>Data Karir</title>

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
    <li class="nav-item active">
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
    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <!-- TOPBAR -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                <button id="sidebarToggleTop"
                    class="btn btn-link d-md-none rounded-circle mr-3">

                    <i class="fa fa-bars"></i>

                </button>

                <h4 class="m-0 text-primary">
                    Data Karir
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
                        Manajemen Data Karir
                    </h1>

                </div>

                <!-- ADD -->
                <div class="card shadow mb-4">

                    <div class="card-header py-3">

                        <h6 class="m-0 font-weight-bold text-primary">
                            Tambah Karir
                        </h6>

                    </div>

                    <div class="card-body">

                        <form method="POST">

                            <div class="row">

                                <div class="col-md-10 mb-3">

                                    <input type="text"
                                        name="career_name"
                                        class="form-control"
                                        placeholder="Nama Karir"
                                        required>

                                </div>

                                <div class="col-md-2 mb-3">

                                    <button type="submit"
                                        name="add_career"
                                        class="btn btn-primary btn-block">

                                        Tambah Karir

                                    </button>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>

                <!-- TABLE -->
                <div class="card shadow mb-4">

                    <div class="card-header py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center">

                        <h6 class="m-0 font-weight-bold text-primary">

                            Total Karir:
                            <?= $totalCareers ?>

                        </h6>

                        <!-- SEARCH -->
                        <form method="GET"
                            class="mt-3 mt-md-0">

                            <div class="input-group">

                                <input type="text"
                                    name="search"
                                    class="form-control bg-light border-0 small"
                                    placeholder="Cari karir..."
                                    value="<?= htmlspecialchars($search) ?>">

                                <div class="input-group-append">

                                    <button class="btn btn-primary"
                                        type="submit">

                                        <i class="fas fa-search fa-sm"></i>

                                    </button>

                                </div>

                            </div>

                        </form>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered">

                                <thead class="thead-dark">

                                    <tr>

                                        <th width="60">No</th>
                                        <th>Nama Karir</th>
                                        <th width="180">Aksi</th>

                                    </tr>

                                </thead>

                                <tbody>

                                <?php if($careers->num_rows > 0): ?>

                                    <?php $no = 1; ?>

                                    <?php while($row = $careers->fetch_assoc()): ?>

                                    <tr>

                                        <td><?= $no++ ?></td>

                                        <td>

                                            <strong>
                                                <?= htmlspecialchars($row['career_name']) ?>
                                            </strong>

                                        </td>

                                        <td>

                                            <!-- EDIT -->
                                            <button
                                                class="btn btn-warning btn-sm"
                                                data-toggle="modal"
                                                data-target="#edit<?= $row['id'] ?>">

                                                <i class="fas fa-edit"></i>

                                            </button>

                                            <!-- DELETE -->
                                            <a href="?delete=<?= $row['id'] ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Hapus karir ini?')">

                                                <i class="fas fa-trash"></i>

                                            </a>

                                        </td>

                                    </tr>

                                    <!-- EDIT MODAL -->
                                    <div class="modal fade"
                                        id="edit<?= $row['id'] ?>"
                                        tabindex="-1">

                                        <div class="modal-dialog">

                                            <div class="modal-content">

                                                <form method="POST">

                                                    <div class="modal-header">

                                                        <h5 class="modal-title">
                                                            Edit Karir
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

                                                            <label>Nama Karir</label>

                                                            <input type="text"
                                                                name="career_name"
                                                                class="form-control"
                                                                value="<?= htmlspecialchars($row['career_name']) ?>"
                                                                required>

                                                        </div>

                                                    </div>

                                                    <div class="modal-footer">

                                                        <button type="button"
                                                            class="btn btn-secondary"
                                                            data-dismiss="modal">

                                                            Batal

                                                        </button>

                                                        <button type="submit"
                                                            name="update_career"
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

                                        <td colspan="3"
                                            class="text-center text-muted">

                                            Belum ada data karir.

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