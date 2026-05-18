<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/db.php';

/* =====================================
   DELETE RESULT
===================================== */

if(isset($_GET['delete'])){

    $id = (int) $_GET['delete'];

    $stmt = $conn->prepare("
        DELETE FROM test_results
        WHERE id = ?
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: admin-results.php");
    exit;
}

/* =====================================
   SEARCH
===================================== */

$search = $_GET['search'] ?? '';

$query = "
SELECT
    test_results.id,
    test_results.mbti_code,
    test_results.top_riasec,
    test_results.recommended_major,
    test_results.created_at,

    users.name,
    users.email,
    users.class

FROM test_results

JOIN users
ON users.id = test_results.user_id
";

if($search){

    $query .= "
    WHERE
        users.name LIKE ?
        OR users.email LIKE ?
        OR test_results.mbti_code LIKE ?
        OR test_results.top_riasec LIKE ?
        OR test_results.recommended_major LIKE ?
    ";
}

$query .= "
ORDER BY test_results.created_at DESC
";

$stmt = $conn->prepare($query);

if($search){

    $searchParam = "%$search%";

    $stmt->bind_param(
        "sssss",
        $searchParam,
        $searchParam,
        $searchParam,
        $searchParam,
        $searchParam
    );
}

$stmt->execute();

$results = $stmt->get_result();

/* =====================================
   TOTAL RESULT
===================================== */

$totalResults = $conn->query("
    SELECT COUNT(*) as total
    FROM test_results
")->fetch_assoc()['total'];

/* =====================================
   MODAL STORAGE
===================================== */

$allModals = "";

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible"
        content="IE=edge">

    <meta name="viewport"
        content="width=device-width, initial-scale=1">

    <title>Hasil Tes - PrediksiKarir Admin</title>

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
    <li class="nav-item active">
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

            <h4 class="m-0 text-primary">
                Hasil Tes
            </h4>

        </nav>

        <!-- CONTAINER -->
        <div class="container-fluid">

            <div class="d-sm-flex align-items-center justify-content-between mb-4">

                <h1 class="h3 mb-0 text-gray-800">
                    Data Hasil Tes
                </h1>

            </div>

            <!-- CARD -->
            <div class="card shadow mb-4">

                <!-- HEADER -->
                <div class="card-header py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center">

                    <h6 class="m-0 font-weight-bold text-primary">

                        Total Hasil Tes:
                        <?= $totalResults ?>

                    </h6>

                    <!-- SEARCH -->
                    <form method="GET"
                        class="mt-3 mt-md-0">

                        <div class="input-group">

                            <input type="text"
                                name="search"
                                class="form-control bg-light border-0 small"
                                placeholder="Cari nama / email / MBTI"
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

                        <table class="table table-bordered"
                            width="100%"
                            cellspacing="0">

                            <thead class="thead-light">

                                <tr>

                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Kelas</th>
                                    <th>MBTI</th>
                                    <th>Top RIASEC</th>
                                    <th>Tanggal</th>
                                    <th width="120">Aksi</th>

                                </tr>

                            </thead>

                            <tbody>

                            <?php if($results->num_rows > 0): ?>

                                <?php $no = 1; ?>

                                <?php while($row = $results->fetch_assoc()): ?>

                                <tr>

                                    <td><?= $no++ ?></td>

                                    <td>
                                        <?= htmlspecialchars($row['name']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['email']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['class']) ?>
                                    </td>

                                    <td>

                                        <span class="badge badge-primary p-2">

                                            <?= htmlspecialchars($row['mbti_code']) ?>

                                        </span>

                                    </td>

                                    <td>

                                        <span class="badge badge-success p-2">

                                            <?= htmlspecialchars($row['top_riasec']) ?>

                                        </span>

                                    </td>

                                    <td>

                                        <?= date(
                                            'd M Y',
                                            strtotime($row['created_at'])
                                        ) ?>

                                    </td>

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
                                            onclick="return confirm('Hapus hasil tes ini?')">

                                            <i class="fas fa-trash"></i>

                                        </a>

                                    </td>

                                </tr>

<?php

$allModals .= '

<div class="modal fade"
    id="detail'.$row['id'].'"
    tabindex="-1">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Detail Hasil Tes
                </h5>

                <button type="button"
                    class="close"
                    data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <div class="modal-body">

                <table class="table table-bordered">

                    <tr>
                        <th width="220">Nama</th>
                        <td>'.$row['name'].'</td>
                    </tr>

                    <tr>
                        <th>Email</th>
                        <td>'.$row['email'].'</td>
                    </tr>

                    <tr>
                        <th>Kelas</th>
                        <td>'.$row['class'].'</td>
                    </tr>

                    <tr>
                        <th>MBTI</th>
                        <td>'.$row['mbti_code'].'</td>
                    </tr>

                    <tr>
                        <th>Top RIASEC</th>
                        <td>'.$row['top_riasec'].'</td>
                    </tr>

                    <tr>
                        <th>Jurusan Rekomendasi</th>
                        <td>'.$row['recommended_major'].'</td>
                    </tr>

                    <tr>
                        <th>Tanggal</th>
                        <td>'.date(
                            'd M Y H:i',
                            strtotime($row['created_at'])
                        ).'</td>
                    </tr>

                </table>

            </div>

        </div>

    </div>

</div>

';

?>

                                <?php endwhile; ?>

                            <?php else: ?>

                                <tr>

                                    <td colspan="8"
                                        class="text-center text-muted">

                                        Belum ada hasil tes.

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

</div>

<!-- MODALS -->
<?= $allModals ?>

<!-- JS -->
<script src="vendor/jquery/jquery.min.js"></script>

<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<script src="js/sb-admin-2.min.js"></script>

</body>
</html>