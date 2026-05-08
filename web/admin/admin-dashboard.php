<?php 
include 'auth.php'; 
include __DIR__ . '/../config/db.php';

// Statistik
$totalUsers = $conn->query("SELECT COUNT(*) as t FROM users")->fetch_assoc()['t'];
$totalResults = $conn->query("SELECT COUNT(*) as t FROM test_results")->fetch_assoc()['t'];
$totalContacts = $conn->query("SELECT COUNT(*) as t FROM contact")->fetch_assoc()['t'];

$totalQuestions = 
    $conn->query("SELECT COUNT(*) as t FROM riasec_questions")->fetch_assoc()['t'];

// Latest result
$latest = $conn->query("
SELECT 
    u.name,
    tr.mbti_code,
    tr.recommended_major,
    tr.created_at
FROM test_results tr
JOIN users u ON u.id = tr.user_id
ORDER BY tr.created_at DESC
LIMIT 10
");

// Distribusi MBTI
$mbtiData = $conn->query("
SELECT mbti_code, COUNT(*) as total 
FROM test_results 
GROUP BY mbti_code
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #f5f5f5;
    font-family: 'Segoe UI', sans-serif;
}

/* SIDEBAR */
.sidebar {
    width: 250px;
    height: 100vh;
    background: #ffffff;
    padding: 20px;
    border-right: 1px solid #eee;
}

.sidebar h4 {
    font-weight: 700;
}

.menu a {
    display: block;
    padding: 10px;
    border-radius: 10px;
    color: #333;
    text-decoration: none;
    margin-bottom: 8px;
}

.menu a.active,
.menu a:hover {
    background: #eee;
}

/* CONTENT */
.content {
    flex: 1;
    padding: 25px;
}

/* CARD */
.card-modern {
    border-radius: 15px;
    padding: 20px;
    background: #fff;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.stat-number {
    font-size: 28px;
    font-weight: bold;
}

/* BADGE */
.badge-soft {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
}

/* COLORS MBTI */
.INTJ { background: #dcd6f7; }
.ENFP { background: #d4f4ec; }
.ISTJ { background: #dbeafe; }
.ENTP { background: #fbe4c8; }

/* PROGRESS */
.progress {
    height: 10px;
    border-radius: 10px;
}
</style>

</head>
<body>

<div class="d-flex">

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
<div class="content">

    <!-- STAT -->
    <div class="row g-3">

        <div class="col-md-3">
            <div class="card-modern">
                <small>Total Pengguna</small>
                <div class="stat-number"><?= $totalUsers ?></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-modern">
                <small>Tes Selesai</small>
                <div class="stat-number"><?= $totalResults ?></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-modern">
                <small>Pesan Masuk</small>
                <div class="stat-number"><?= $totalContacts ?></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-modern">
                <small>Total Soal</small>
                <div class="stat-number"><?= $totalQuestions ?></div>
            </div>
        </div>

    </div>

    <!-- TABLE + CHART -->
    <div class="row mt-4">

        <!-- TABLE -->
        <div class="col-md-6">
            <div class="card-modern">
                <div class="d-flex justify-content-between">
                    <h5>Hasil Tes Terbaru</h5>
                </div>

                <table class="table mt-3">
                <tr>
                    <th>Nama</th>
                    <th>MBTI</th>
                    <th>Jurusan</th>
                    <th>Tanggal</th>
                </tr>

                <?php while($row = $latest->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['name']; ?></td>

                    <td>
                        <span class="badge-soft <?= $row['mbti_code']; ?>">
                            <?= $row['mbti_code']; ?>
                        </span>
                    </td>

                    <td><?= $row['recommended_major']; ?></td>

                    <td>
                        <?= date('d M Y', strtotime($row['created_at'])); ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
            </div>
        </div>

        <!-- DISTRIBUSI -->
        <div class="col-md-6">
            <div class="card-modern">
                <h5>Distribusi Tipe MBTI</h5>

                <?php while($row = $mbtiData->fetch_assoc()): ?>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between">
                            <span><?= $row['mbti_code']; ?></span>
                            <span><?= $row['total']; ?></span>
                        </div>

                        <div class="progress">
                            <div class="progress-bar" style="width: <?= $row['total']*5 ?>%"></div>
                        </div>
                    </div>
                <?php endwhile; ?>

            </div>
        </div>

    </div>

</div>
</div>

</body>
</html>