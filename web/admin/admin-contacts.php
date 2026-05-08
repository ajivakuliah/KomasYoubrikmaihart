<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/db.php';

/* =====================================
   DELETE MESSAGE
===================================== */

if(isset($_GET['delete'])){

    $id = (int) $_GET['delete'];

    $stmt = $conn->prepare("
        DELETE FROM contact
        WHERE id = ?
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: admin-contacts.php");
    exit;
}

/* =====================================
   SEARCH
===================================== */

$search = $_GET['search'] ?? '';

$query = "
SELECT *
FROM contact
";

if($search){

    $query .= "
    WHERE
        name LIKE ?
        OR email LIKE ?
        OR subject LIKE ?
    ";
}

$query .= "
ORDER BY created_at DESC
";

$stmt = $conn->prepare($query);

if($search){

    $searchParam = "%$search%";

    $stmt->bind_param(
        "sss",
        $searchParam,
        $searchParam,
        $searchParam
    );
}

$stmt->execute();

$contacts = $stmt->get_result();

/* =====================================
   TOTAL MESSAGE
===================================== */

$totalMessages = $conn->query("
    SELECT COUNT(*) as total
    FROM contact
")->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1">

    <title>Pesan Masuk</title>

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
                    Pesan Masuk
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
                        Manajemen Pesan Masuk
                    </h1>

                </div>

                <!-- CARD -->
                <div class="card shadow mb-4">

                    <!-- HEADER -->
                    <div class="card-header py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center">

                        <h6 class="m-0 font-weight-bold text-primary">

                            Total Pesan:
                            <?= $totalMessages ?>

                        </h6>

                        <!-- SEARCH -->
                        <form method="GET"
                            class="mt-3 mt-md-0">

                            <div class="input-group">

                                <input type="text"
                                    name="search"
                                    class="form-control bg-light border-0 small"
                                    placeholder="Cari nama / email / subject"
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

                    <!-- BODY -->
                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered">

                                <thead class="thead-dark">

                                    <tr>

                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Subject</th>
                                        <th>Tanggal</th>
                                        <th width="140">Aksi</th>

                                    </tr>

                                </thead>

                                <tbody>

                                <?php if($contacts->num_rows > 0): ?>

                                    <?php while($row = $contacts->fetch_assoc()): ?>

                                    <tr>

                                        <!-- NAMA -->
                                        <td>
                                            <?= htmlspecialchars($row['name'] ?? '') ?>
                                        </td>

                                        <!-- EMAIL -->
                                        <td>
                                            <?= htmlspecialchars($row['email'] ?? '') ?>
                                        </td>

                                        <!-- SUBJECT -->
                                        <td>

                                            <?php if(!empty($row['subject'])): ?>

                                                <?= htmlspecialchars($row['subject']) ?>

                                            <?php else: ?>

                                                <span class="text-muted">
                                                    Tidak ada subject
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

                                            <!-- DETAIL -->
                                            <button
                                                class="btn btn-info btn-sm"
                                                data-toggle="modal"
                                                data-target="#detail<?= $row['id'] ?>">

                                                <i class="fas fa-eye"></i>

                                            </button>

                                            <!-- DELETE -->
                                            <a href="?delete=<?= $row['id'] ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Hapus pesan ini?')">

                                                <i class="fas fa-trash"></i>

                                            </a>

                                        </td>

                                    </tr>

                                    <!-- MODAL DETAIL -->
                                    <div class="modal fade"
                                        id="detail<?= $row['id'] ?>"
                                        tabindex="-1"
                                        role="dialog">

                                        <div class="modal-dialog modal-lg"
                                            role="document">

                                            <div class="modal-content">

                                                <!-- HEADER -->
                                                <div class="modal-header">

                                                    <h5 class="modal-title">
                                                        Detail Pesan
                                                    </h5>

                                                    <button type="button"
                                                        class="close"
                                                        data-dismiss="modal">

                                                        <span>&times;</span>

                                                    </button>

                                                </div>

                                                <!-- BODY -->
                                                <div class="modal-body">

                                                    <div class="mb-3">

                                                        <label class="font-weight-bold">
                                                            Nama
                                                        </label>

                                                        <div class="border rounded p-3 bg-light">

                                                            <?= htmlspecialchars($row['name'] ?? '') ?>

                                                        </div>

                                                    </div>

                                                    <div class="mb-3">

                                                        <label class="font-weight-bold">
                                                            Email
                                                        </label>

                                                        <div class="border rounded p-3 bg-light">

                                                            <?= htmlspecialchars($row['email'] ?? '') ?>

                                                        </div>

                                                    </div>

                                                    <div class="mb-3">

                                                        <label class="font-weight-bold">
                                                            Subject
                                                        </label>

                                                        <div class="border rounded p-3 bg-light">

                                                            <?= htmlspecialchars($row['subject'] ?? '-') ?>

                                                        </div>

                                                    </div>

                                                    <div class="mb-3">

                                                        <label class="font-weight-bold">
                                                            Pesan
                                                        </label>

                                                        <div class="border rounded p-3 bg-light">

                                                            <?= nl2br(htmlspecialchars($row['message'] ?? '')) ?>

                                                        </div>

                                                    </div>

                                                    <div>

                                                        <label class="font-weight-bold">
                                                            Tanggal
                                                        </label>

                                                        <div class="border rounded p-3 bg-light">

                                                            <?= date(
                                                                'd M Y H:i',
                                                                strtotime($row['created_at'] ?? '')
                                                            ) ?>

                                                        </div>

                                                    </div>

                                                </div>

                                                <!-- FOOTER -->
                                                <div class="modal-footer">

                                                    <button type="button"
                                                        class="btn btn-secondary"
                                                        data-dismiss="modal">

                                                        Tutup

                                                    </button>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                    <?php endwhile; ?>

                                <?php else: ?>

                                    <tr>

                                        <td colspan="5"
                                            class="text-center text-muted">

                                            Belum ada pesan masuk.

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