<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/db.php';

/* =========================
   STATISTIK
========================= */

$totalUsers = $conn->query("
    SELECT COUNT(*) as t 
    FROM users
")->fetch_assoc()['t'];

$totalResults = $conn->query("
    SELECT COUNT(*) as t 
    FROM test_results
")->fetch_assoc()['t'];

$totalContacts = $conn->query("
    SELECT COUNT(*) as t 
    FROM contact
")->fetch_assoc()['t'];

$totalQuestions = $conn->query("
    SELECT COUNT(*) as t 
    FROM riasec_questions
")->fetch_assoc()['t'];

/* =========================
   HASIL TERBARU
========================= */

$latest = $conn->query("
    SELECT 
        u.name,
        tr.mbti_code,
        tr.recommended_major,
        tr.created_at

    FROM test_results tr

    JOIN users u
    ON u.id = tr.user_id

    ORDER BY tr.created_at DESC
    LIMIT 5
");

/* =========================
   DISTRIBUSI MBTI
========================= */

$mbtiData = $conn->query("
    SELECT 
        mbti_code,
        COUNT(*) as total

    FROM test_results

    GROUP BY mbti_code

    ORDER BY total DESC
");

/* =========================
   TOTAL UNTUK PERSENTASE
========================= */

$totalMBTI = $conn->query("
    SELECT COUNT(*) as t 
    FROM test_results
")->fetch_assoc()['t'];

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>PrediksiKarir Admin Dashboard</title>

    <!-- Bootstrap -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900"
        rel="stylesheet">

    <link href="css/sb-admin-2.min.css" rel="stylesheet">

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
    <li class="nav-item active">
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

    <!-- CONTENT WRAPPER -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- MAIN CONTENT -->
        <div id="content">

            <!-- TOPBAR -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                <button id="sidebarToggleTop"
                    class="btn btn-link d-md-none rounded-circle mr-3">

                    <i class="fa fa-bars"></i>

                </button>

                <h4 class="m-0 text-primary">
                    Dashboard Admin
                </h4>

                <ul class="navbar-nav ml-auto">

                    <li class="nav-item dropdown no-arrow">

                        <a class="nav-link dropdown-toggle"
                            href="#"
                            id="userDropdown"
                            role="button"
                            data-toggle="dropdown">

                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                Admin
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
            <!-- END TOPBAR -->

            <!-- CONTAINER -->
            <div class="container-fluid">

                <!-- HEADING -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">

                    <h1 class="h3 mb-0 text-gray-800">
                        Dashboard
                    </h1>

                </div>

                <!-- STATISTIK -->
                <div class="row">

                    <!-- USER -->
                    <div class="col-xl-3 col-md-6 mb-4">

                        <div class="card border-left-primary shadow h-100 py-2">

                            <div class="card-body">

                                <div class="row no-gutters align-items-center">

                                    <div class="col mr-2">

                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Pengguna
                                        </div>

                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $totalUsers ?>
                                        </div>

                                    </div>

                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- TES -->
                    <div class="col-xl-3 col-md-6 mb-4">

                        <div class="card border-left-success shadow h-100 py-2">

                            <div class="card-body">

                                <div class="row no-gutters align-items-center">

                                    <div class="col mr-2">

                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Tes Selesai
                                        </div>

                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $totalResults ?>
                                        </div>

                                    </div>

                                    <div class="col-auto">
                                        <i class="fas fa-poll fa-2x text-gray-300"></i>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- PESAN -->
                    <div class="col-xl-3 col-md-6 mb-4">

                        <div class="card border-left-warning shadow h-100 py-2">

                            <div class="card-body">

                                <div class="row no-gutters align-items-center">

                                    <div class="col mr-2">

                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Pesan Masuk
                                        </div>

                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $totalContacts ?>
                                        </div>

                                    </div>

                                    <div class="col-auto">
                                        <i class="fas fa-envelope fa-2x text-gray-300"></i>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- SOAL -->
                    <div class="col-xl-3 col-md-6 mb-4">

                        <div class="card border-left-info shadow h-100 py-2">

                            <div class="card-body">

                                <div class="row no-gutters align-items-center">

                                    <div class="col mr-2">

                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Total Soal
                                        </div>

                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $totalQuestions ?>
                                        </div>

                                    </div>

                                    <div class="col-auto">
                                        <i class="fas fa-book fa-2x text-gray-300"></i>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- CONTENT -->
                <div class="row">

                    <!-- HASIL TERBARU -->
                    <div class="col-lg-8">

                        <div class="card shadow mb-4">

                            <div class="card-header py-3">

                                <h6 class="m-0 font-weight-bold text-primary">
                                    Hasil Tes Terbaru
                                </h6>

                            </div>

                            <div class="card-body">

                                <div class="table-responsive">

                                    <table class="table table-bordered">

                                        <thead class="thead-light">

                                            <tr>
                                                <th>Nama</th>
                                                <th>MBTI</th>
                                                <th>Jurusan Rekomendasi</th>
                                                <th>Tanggal</th>
                                            </tr>

                                        </thead>

                                        <tbody>

                                            <?php while($row = $latest->fetch_assoc()): ?>

                                            <tr>

                                                <td>
                                                    <?= htmlspecialchars($row['name']) ?>
                                                </td>

                                                <td>
                                                    <span class="badge badge-primary">
                                                        <?= $row['mbti_code'] ?>
                                                    </span>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($row['recommended_major']) ?>
                                                </td>

                                                <td>
                                                    <?= date('d M Y', strtotime($row['created_at'])) ?>
                                                </td>

                                            </tr>

                                            <?php endwhile; ?>

                                        </tbody>

                                    </table>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- DISTRIBUSI -->
                    <div class="col-lg-4">

                        <div class="card shadow mb-4">

                            <div class="card-header py-3">

                                <h6 class="m-0 font-weight-bold text-primary">
                                    Distribusi MBTI
                                </h6>

                            </div>

                            <div class="card-body">

                                <?php while($row = $mbtiData->fetch_assoc()): ?>

                                    <?php
                                        $percent = ($totalMBTI > 0)
                                            ? ($row['total'] / $totalMBTI) * 100
                                            : 0;
                                    ?>

                                    <h4 class="small font-weight-bold">

                                        <?= $row['mbti_code'] ?>

                                        <span class="float-right">
                                            <?= $row['total'] ?>
                                        </span>

                                    </h4>

                                    <div class="progress mb-4">

                                        <div class="progress-bar bg-primary"
                                            role="progressbar"
                                            style="width: <?= $percent ?>%">
                                        </div>

                                    </div>

                                <?php endwhile; ?>

                            </div>

                        </div>

                    </div>

                </div>

            </div>
            <!-- END CONTAINER -->

        </div>
        <!-- END MAIN CONTENT -->

        <!-- FOOTER -->
        <footer class="sticky-footer bg-white">

            <div class="container my-auto">

                <div class="copyright text-center my-auto">

                    <span>
                        Copyright &copy; PrediksiKarir <?= date('Y') ?>
                    </span>

                </div>

            </div>

        </footer>

    </div>
    <!-- END CONTENT WRAPPER -->

</div>
<!-- END WRAPPER -->

<!-- SCROLL -->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- JS -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

</body>
</html>