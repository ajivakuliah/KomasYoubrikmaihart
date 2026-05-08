<?php
include 'auth.php';
include '../config/db.php';

/* =========================
   TAMBAH MBTI
========================= */
if(isset($_POST['add_mbti'])){

    $code = $_POST['code'];
    $name = $_POST['name'];
    $description = $_POST['description'];

    $conn->query("
        INSERT INTO mbti_types(code, name, description)
        VALUES('$code','$name','$description')
    ");

    header("Location: admin-mbti.php");
    exit;
}

/* =========================
   HAPUS MBTI
========================= */
if(isset($_GET['delete'])){

    $id = $_GET['delete'];

    $conn->query("DELETE FROM mbti_types WHERE id='$id'");

    header("Location: admin-mbti.php");
    exit;
}

/* =========================
   DATA MBTI
========================= */
$mbti = $conn->query("
    SELECT *
    FROM mbti_types
    ORDER BY code ASC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <title>Data MBTI</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

<div id="wrapper">

    <!-- SIDEBAR -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion"
        id="accordionSidebar">

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

        <li class="nav-item">
            <a class="nav-link" href="index.php">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="admin-users.php">
                <i class="fas fa-users"></i>
                <span>Data Pengguna</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="admin-results.php">
                <i class="fas fa-poll"></i>
                <span>Data Hasil Tes</span>
            </a>
        </li>

        <hr class="sidebar-divider">

        <div class="sidebar-heading">
            MBTI
        </div>

        <li class="nav-item active">
            <a class="nav-link" href="admin-mbti.php">
                <i class="fas fa-brain"></i>
                <span>Data MBTI</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="admin-mbti-riasec.php">
                <i class="fas fa-link"></i>
                <span>Relasi MBTI-RIASEC</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="admin-mbti-careers.php">
                <i class="fas fa-briefcase"></i>
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
                <i class="fas fa-link"></i>
                <span>Relasi RIASEC-Karir</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="admin-riasec-majors.php">
                <i class="fas fa-link"></i>
                <span>Relasi RIASEC-Jurusan</span>
            </a>
        </li>

        <hr class="sidebar-divider">

        <div class="sidebar-heading">
            Master Data
        </div>

        <li class="nav-item">
            <a class="nav-link" href="admin-careers.php">
                <i class="fas fa-briefcase"></i>
                <span>Data Karir</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="admin-majors.php">
                <i class="fas fa-university"></i>
                <span>Data Jurusan</span>
            </a>
        </li>

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
                <h5 class="m-0 font-weight-bold text-primary">
                    Data MBTI
                </h5>
            </nav>

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
                                    <select name="code" class="form-control" required>

                                    <option value="">-- Pilih MBTI --</option>

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

                                <thead class="bg-primary text-white">

                                    <tr>
                                        <th width="60">No</th>
                                        <th>Kode MBTI</th>
                                        <th>Nama MBTI</th>
                                        <th>Deskripsi</th>
                                        <th width="120">Aksi</th>
                                    </tr>

                                </thead>

                                <tbody>

                                <?php
                                $no = 1;
                                while($row = $mbti->fetch_assoc()):
                                ?>

                                    <tr>

                                        <td><?= $no++; ?></td>

                                        <td>
                                            <strong><?= $row['code']; ?></strong>
                                        </td>

                                        <td><?= $row['name']; ?></td>

                                        <td><?= $row['description']; ?></td>

                                        <td>

                                            <a href="?delete=<?= $row['id']; ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Hapus data?')">

                                                <i class="fas fa-trash"></i>

                                            </a>

                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                                </tbody>

                            </table>

                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

</body>
</html>