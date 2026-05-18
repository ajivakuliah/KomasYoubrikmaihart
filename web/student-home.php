<?php
session_start();
require_once 'config.php';

/* =========================
   CEK LOGIN
========================= */

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

/* =========================
   AMBIL DATA USER
========================= */

$user_id   = $_SESSION['user_id'];
$user_data = null;
$results   = null;
$latest_mbti = null;

if(!$conn) {
    die("Error: Database connection failed");
}

$stmt = $conn->prepare("
    SELECT id, name, email, phone, class, gender 
    FROM users 
    WHERE id = ?
");

if(!$stmt) die("Prepare failed: " . $conn->error);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* =========================
   AMBIL RIWAYAT HASIL QUIZ
========================= */

$stmt_results = $conn->prepare("
    SELECT 
        tr.id,
        tr.created_at,
        tr.mbti_code,
        mt.name        AS mbti_name,
        mt.description AS mbti_desc,
        tr.top_riasec,
        tr.recommended_major,
        tr.recommended_careers,
        tr.riasec_r,
        tr.riasec_i,
        tr.riasec_a,
        tr.riasec_s,
        tr.riasec_e,
        tr.riasec_c
    FROM test_results tr
    LEFT JOIN mbti_types mt ON tr.mbti_code = mt.code
    WHERE tr.user_id = ?
    ORDER BY tr.created_at DESC
");

if($stmt_results) {
    $stmt_results->bind_param("i", $user_id);
    $stmt_results->execute();
    $results = $stmt_results->get_result();
    $stmt_results->close();
}

/* =========================
   MBTI & HASIL TERBARU
========================= */

$stmt_latest = $conn->prepare("
    SELECT 
        tr.mbti_code,
        mt.name,
        mt.description,
        tr.top_riasec,
        tr.recommended_careers,
        tr.recommended_major,
        tr.created_at
    FROM test_results tr
    LEFT JOIN mbti_types mt ON tr.mbti_code = mt.code
    WHERE tr.user_id = ?
    ORDER BY tr.created_at DESC
    LIMIT 1
");

if($stmt_latest) {
    $stmt_latest->bind_param("i", $user_id);
    $stmt_latest->execute();
    $latest_result = $stmt_latest->get_result()->fetch_assoc();
    if($latest_result && $latest_result['mbti_code']) {
        $latest_mbti = $latest_result;
    }
    $stmt_latest->close();
}

/* Hitung jumlah test */
$total_tests = 0;
$stmt_count = $conn->prepare("SELECT COUNT(*) as cnt FROM test_results WHERE user_id = ?");
if($stmt_count) {
    $stmt_count->bind_param("i", $user_id);
    $stmt_count->execute();
    $cnt_row = $stmt_count->get_result()->fetch_assoc();
    $total_tests = $cnt_row['cnt'] ?? 0;
    $stmt_count->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — KarirMatch</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-icons.css">

    <style>
        :root {
            --primary:   #13547a;
            --accent:    #80d0c7;
            --bg:        #f0f4f8;
            --white:     #ffffff;
            --text:      #1a2a3a;
            --muted:     #6b7f90;
            --radius:    12px;
            --shadow:    0 4px 20px rgba(19,84,122,.10);
        }

        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'Open Sans', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            color: var(--text);
        }

        /* ── NAVBAR ── */
        .navbar-home {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            box-shadow: 0 2px 16px rgba(19,84,122,.25);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .navbar-home h3 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: 22px;
            letter-spacing: -.3px;
        }
        .navbar-home .nav-right { display:flex; align-items:center; gap:16px; }
        .navbar-home .user-greeting { font-size:14px; opacity:.9; }
        .navbar-home a.logout-link {
            color: white;
            text-decoration: none;
            font-size: 13px;
            padding: 7px 16px;
            border-radius: 20px;
            background: rgba(255,255,255,.2);
            transition: background .2s;
            font-weight: 600;
        }
        .navbar-home a.logout-link:hover { background: rgba(255,255,255,.32); }

        /* ── LAYOUT ── */
        .container-home {
            max-width: 1100px;
            margin: 32px auto;
            padding: 0 20px 60px;
        }

        /* ── SECTION ── */
        .section {
            background: var(--white);
            border-radius: var(--radius);
            padding: 28px 32px;
            margin-bottom: 28px;
            box-shadow: var(--shadow);
        }
        .section-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 22px;
            padding-bottom: 14px;
            border-bottom: 3px solid var(--accent);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ── STAT CARDS ── */
        .stat-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }
        .stat-card {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            border-radius: var(--radius);
            padding: 22px 20px;
            text-align: center;
        }
        .stat-card .stat-num {
            font-family: 'Montserrat', sans-serif;
            font-size: 36px;
            font-weight: 800;
            line-height: 1;
        }
        .stat-card .stat-label {
            font-size: 12px;
            opacity: .85;
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: .8px;
        }

        /* ── PROFIL GRID ── */
        .profile-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
        }
        .profile-item {
            background: #f5f8fc;
            padding: 18px;
            border-radius: 10px;
            border-left: 4px solid var(--accent);
        }
        .profile-item .label {
            font-size: 11px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .8px;
            margin-bottom: 6px;
        }
        .profile-item .value {
            font-size: 16px;
            font-weight: 600;
            color: var(--primary);
        }

        /* ── MBTI HERO ── */
        .mbti-hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            padding: 32px;
            border-radius: var(--radius);
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 28px;
            align-items: center;
            margin-bottom: 20px;
        }
        .mbti-hero .mbti-code-big {
            font-family: 'Montserrat', sans-serif;
            font-size: 64px;
            font-weight: 800;
            line-height: 1;
            opacity: .95;
        }
        .mbti-hero .mbti-name {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 6px;
            font-family: 'Montserrat', sans-serif;
        }
        .mbti-hero .mbti-desc {
            font-size: 13px;
            opacity: .88;
            line-height: 1.65;
        }
        .mbti-hero .mbti-date {
            font-size: 11px;
            opacity: .7;
            margin-top: 10px;
        }

        /* Badges */
        .tag-row { display:flex; flex-wrap:wrap; gap:8px; }

        .mbti-badge {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }
        .riasec-tag {
            background: #e0f3f0;
            color: #0d6b5e;
            padding: 4px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .major-tag {
            background: #fff3e0;
            color: #c75000;
            padding: 4px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .career-tag {
            background: #ede7f6;
            color: #512da8;
            padding: 4px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        /* ── HISTORY TABLE ── */
        .history-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .history-table thead { background: #f0f4f8; }
        .history-table th {
            padding: 13px 14px;
            text-align: left;
            font-weight: 700;
            color: var(--primary);
            border-bottom: 2px solid var(--accent);
            white-space: nowrap;
            font-family: 'Montserrat', sans-serif;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .history-table td {
            padding: 14px;
            border-bottom: 1px solid #edf2f7;
            vertical-align: top;
        }
        .history-table tr:last-child td { border-bottom: none; }
        .history-table tr:hover td { background: #f8fbff; }

        .date-cell .date-main { font-weight: 600; color: var(--primary); }
        .date-cell .date-time { font-size: 11px; color: var(--muted); margin-top: 2px; }

        /* ── MENU ── */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }
        .menu-btn {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            border: none;
            padding: 24px 20px;
            border-radius: var(--radius);
            cursor: pointer;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            transition: transform .2s, box-shadow .2s;
            font-family: 'Montserrat', sans-serif;
        }
        .menu-btn:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(19,84,122,.28);
            color: white;
        }
        .menu-btn i { font-size: 28px; }

        .no-data {
            text-align: center;
            padding: 48px 20px;
            color: var(--muted);
            font-style: italic;
        }
        .no-data .no-data-icon { font-size: 40px; margin-bottom: 12px; }

        .responsive-table-wrapper { overflow-x: auto; }

        @media (max-width: 768px) {
            .navbar-home { flex-direction:column; gap:10px; padding:14px 16px; }
            .container-home { padding:0 12px 40px; margin-top:20px; }
            .section { padding:20px 16px; }
            .mbti-hero { grid-template-columns:1fr; gap:16px; text-align:center; }
            .mbti-hero .mbti-code-big { font-size:48px; }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar-home">
    <h3>📚 KarirMatch</h3>
    <div class="nav-right">
        <span class="user-greeting">
            Halo, <?= htmlspecialchars($user_data['name'] ?? 'Siswa') ?> 👋
        </span>
        <a href="logout.php" class="logout-link">🚪 Logout</a>
    </div>
</div>

<div class="container-home">

    <!-- STAT CARDS -->
    <div class="stat-row">
        <div class="stat-card">
            <div class="stat-num"><?= $total_tests ?></div>
            <div class="stat-label">Quiz Dikerjakan</div>
        </div>
        <div class="stat-card">
            <div class="stat-num"><?= htmlspecialchars($latest_mbti['mbti_code'] ?? '—') ?></div>
            <div class="stat-label">Tipe MBTI Terbaru</div>
        </div>
        <div class="stat-card">
            <div class="stat-num">
                <?php
                    if($latest_mbti && $latest_mbti['created_at']) {
                        $d = new DateTime($latest_mbti['created_at']);
                        echo $d->format('d M');
                    } else {
                        echo '—';
                    }
                ?>
            </div>
            <div class="stat-label">Tanggal Tes Terakhir</div>
        </div>
    </div>

    <!-- PROFIL SISWA -->
    <div class="section">
        <div class="section-title">👤 Profil Siswa</div>
        <?php if($user_data): ?>
        <div class="profile-grid">
            <div class="profile-item">
                <div class="label">Nama</div>
                <div class="value"><?= htmlspecialchars($user_data['name'] ?? '-') ?></div>
            </div>
            <div class="profile-item">
                <div class="label">Email</div>
                <div class="value"><?= htmlspecialchars($user_data['email'] ?? '-') ?></div>
            </div>
            <div class="profile-item">
                <div class="label">Telepon</div>
                <div class="value"><?= htmlspecialchars($user_data['phone'] ?? '-') ?></div>
            </div>
            <div class="profile-item">
                <div class="label">Kelas</div>
                <div class="value">Kelas <?= htmlspecialchars($user_data['class'] ?? '-') ?></div>
            </div>
            <div class="profile-item">
                <div class="label">Jenis Kelamin</div>
                <div class="value">
                    <?php
                        $g = $user_data['gender'] ?? '';
                        echo $g === 'L' ? 'Laki-laki' : ($g === 'P' ? 'Perempuan' : '-');
                    ?>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="no-data">⚠️ Gagal mengambil data profil.</div>
        <?php endif; ?>
    </div>

    <!-- HASIL TERBARU -->
    <?php if($latest_mbti): ?>
    <div class="section">
        <div class="section-title">🧠 Hasil Tes Terbaru</div>

        <div class="mbti-hero">
            <div class="mbti-code-big"><?= htmlspecialchars($latest_mbti['mbti_code']) ?></div>
            <div>
                <div class="mbti-name"><?= htmlspecialchars($latest_mbti['name'] ?? '') ?></div>
                <div class="mbti-desc"><?= htmlspecialchars($latest_mbti['description'] ?? '') ?></div>
                <?php if($latest_mbti['created_at']): ?>
                <div class="mbti-date">
                    <?php
                        $d = new DateTime($latest_mbti['created_at']);
                        echo '🗓 Diambil pada ' . $d->format('d F Y, H:i') . ' WITA';
                    ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if(!empty($latest_mbti['top_riasec'])): ?>
        <div style="margin-bottom:16px;">
            <div style="font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.7px;margin-bottom:8px;font-weight:600;">
                Profil Minat Utama (RIASEC)
            </div>
            <div class="tag-row">
                <?php foreach(explode(',', $latest_mbti['top_riasec']) as $r): ?>
                    <span class="riasec-tag"><?= htmlspecialchars(trim($r)) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($latest_mbti['recommended_careers'])): ?>
        <div style="margin-bottom:16px;">
            <div style="font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.7px;margin-bottom:8px;font-weight:600;">
                💼 Rekomendasi Karir
            </div>
            <div class="tag-row">
                <?php foreach(explode(',', $latest_mbti['recommended_careers']) as $c): ?>
                    <span class="career-tag"><?= htmlspecialchars(trim($c)) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($latest_mbti['recommended_major'])): ?>
        <div>
            <div style="font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.7px;margin-bottom:8px;font-weight:600;">
                🎓 Rekomendasi Jurusan
            </div>
            <div class="tag-row">
                <?php foreach(explode(',', $latest_mbti['recommended_major']) as $m): ?>
                    <span class="major-tag"><?= htmlspecialchars(trim($m)) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- RIWAYAT QUIZ -->
    <div class="section">
        <div class="section-title">📋 Riwayat Quiz</div>

        <?php if($results && $results->num_rows > 0): ?>
        <div class="responsive-table-wrapper">
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Tanggal &amp; Waktu</th>
                        <th>MBTI</th>
                        <th>Profil RIASEC</th>
                        <th>Rekomendasi Karir</th>
                        <th>Rekomendasi Jurusan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $results->fetch_assoc()): ?>
                    <tr>
                        <!-- Tanggal -->
                        <td class="date-cell">
                            <?php if($row['created_at']): ?>
                                <?php $d = new DateTime($row['created_at']); ?>
                                <div class="date-main"><?= $d->format('d M Y') ?></div>
                                <div class="date-time"><?= $d->format('H:i') ?> WITA</div>
                            <?php else: ?>
                                <span style="color:var(--muted)">—</span>
                            <?php endif; ?>
                        </td>

                        <!-- MBTI -->
                        <td>
                            <?php if($row['mbti_code']): ?>
                                <span class="mbti-badge"><?= htmlspecialchars($row['mbti_code']) ?></span>
                                <?php if($row['mbti_name']): ?>
                                    <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                                        <?= htmlspecialchars($row['mbti_name']) ?>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span style="color:var(--muted)">—</span>
                            <?php endif; ?>
                        </td>

                        <!-- RIASEC -->
                        <td>
                            <?php if($row['top_riasec']): ?>
                                <div class="tag-row">
                                    <?php foreach(explode(',', $row['top_riasec']) as $r): ?>
                                        <span class="riasec-tag"><?= htmlspecialchars(trim($r)) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <span style="color:var(--muted)">—</span>
                            <?php endif; ?>
                        </td>

                        <!-- Karir -->
                        <td>
                            <?php if(!empty($row['recommended_careers'])): ?>
                                <div class="tag-row">
                                    <?php
                                        $careers = array_slice(explode(',', $row['recommended_careers']), 0, 4);
                                        foreach($careers as $c):
                                    ?>
                                        <span class="career-tag"><?= htmlspecialchars(trim($c)) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <span style="color:var(--muted);font-size:12px;font-style:italic;">
                                    Tes lama (belum ada data)
                                </span>
                            <?php endif; ?>
                        </td>

                        <!-- Jurusan -->
                        <td>
                            <?php if($row['recommended_major']): ?>
                                <div class="tag-row">
                                    <?php
                                        $majors = array_slice(explode(',', $row['recommended_major']), 0, 4);
                                        foreach($majors as $m):
                                    ?>
                                        <span class="major-tag"><?= htmlspecialchars(trim($m)) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <span style="color:var(--muted)">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <?php else: ?>
        <div class="no-data">
            <div class="no-data-icon">📭</div>
            Belum ada riwayat quiz.<br>Mulai quiz untuk mendapatkan rekomendasi karir!
        </div>
        <?php endif; ?>
    </div>

    <!-- MENU AKSI -->
    <div class="section">
        <div class="section-title">🚀 Aksi</div>
        <div class="menu-grid">
            <a href="quiz.php" class="menu-btn">
                <i class="bi bi-pencil-square"></i>
                Mulai Quiz
            </a>
        </div>
    </div>

</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>