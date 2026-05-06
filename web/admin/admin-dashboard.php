<?php 
include 'auth.php'; 
include __DIR__ . '/../config/db.php';

// Statistik
$totalUsers = $conn->query("SELECT COUNT(*) as t FROM users")->fetch_assoc()['t'];
$totalResults = $conn->query("SELECT COUNT(*) as t FROM results")->fetch_assoc()['t'];
$totalContacts = $conn->query("SELECT COUNT(*) as t FROM contacts")->fetch_assoc()['t'];

$totalQuestions = 
    $conn->query("SELECT COUNT(*) as t FROM mbti_questions")->fetch_assoc()['t'] +
    $conn->query("SELECT COUNT(*) as t FROM riasec_questions")->fetch_assoc()['t'];

// Latest result
$latest = $conn->query("
SELECT u.name, r.mbti, r.riasec
FROM results r
JOIN users u ON u.id = r.user_id
ORDER BY r.created_at DESC
LIMIT 5
");

// Distribusi MBTI
$mbtiData = $conn->query("
SELECT mbti, COUNT(*) as total 
FROM results 
GROUP BY mbti
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
<div class="sidebar">
    <h4>CareerPath Admin</h4>
    <small>Panel Manajemen</small>

    <hr>

    <div class="menu">

        <!-- UTAMA -->
        <small class="text-muted">UTAMA</small>
        <a href="admin-dashboard.php" class="active">Dashboard</a>
        <a href="admin-results.php">Hasil Tes</a>
        <a href="admin-users.php">Data Pengguna</a>
        <a href="admin-contacts.php">Pesan Masuk</a>

        <!-- KONTEN TES -->
        <small class="text-muted mt-3">KONTEN TES</small>
        <a href="admin-mbti-questions.php">Soal MBTI</a>
        <a href="admin-riasec-questions.php">Soal Bakat & Minat</a>
        <a href="admin-methods.php">Metode Tes</a>
        <a href="admin-careers.php">Data Karir</a>
        <a href="admin-mbti-careers.php">Relasi MBTI-Karir</a>

        <!-- WEBSITE -->
        <small class="text-muted mt-3">WEBSITE</small>
        <a href="admin-homepage.php">Edit Homepage</a>
        <a href="admin-features.php">Fitur Unggulan</a>
        <a href="admin-faq.php">Kelola FAQ</a>
        <a href="admin-footer.php">Edit Footer</a>

        <hr>
        <a href="admin-logout.php" class="text-danger">Logout</a>
    </div>
</div>

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
                        <th>RIASEC</th>
                    </tr>

                    <?php while($row = $latest->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['name']; ?></td>
                        <td>
                            <span class="badge-soft <?= $row['mbti']; ?>">
                                <?= $row['mbti']; ?>
                            </span>
                        </td>
                        <td><?= $row['riasec']; ?></td>
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
                            <span><?= $row['mbti']; ?></span>
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