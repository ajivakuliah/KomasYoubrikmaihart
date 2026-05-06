<?php
session_start();
include __DIR__ . '/../config/db.php';

// proteksi admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin-login.php");
    exit;
}

// ambil data user
$data = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Manajemen Pengguna</h3>
        <a href="admin-dashboard.php" class="btn btn-secondary">Kembali</a>
    </div>

    <!-- FORM TAMBAH USER -->
    <div class="card p-3 mb-4 shadow-sm">
        <h5>Tambah User</h5>
        <form method="POST" action="admin-users-actions.php">
            <div class="row g-2">

                <div class="col-md-3">
                    <input name="name" class="form-control" placeholder="Nama" required>
                </div>

                <div class="col-md-3">
                    <input name="email" class="form-control" placeholder="Email" required>
                </div>

                <div class="col-md-2">
                    <input name="password" class="form-control" placeholder="Password" required>
                </div>

                <div class="col-md-2">
                    <select name="class" class="form-control">
                        <option value="10">Kelas 10</option>
                        <option value="11">Kelas 11</option>
                        <option value="12">Kelas 12</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button name="add" class="btn btn-success w-100">Tambah</button>
                </div>

            </div>
        </form>
    </div>

    <!-- TABLE USERS -->
    <div class="card p-3 shadow-sm">
        <h5>Daftar Pengguna</h5>

        <table class="table table-bordered table-hover mt-3">
            <thead class="table-dark">
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Kelas</th>
                    <th>Role</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
            <?php while($row = $data->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']); ?></td>
                    <td><?= htmlspecialchars($row['email']); ?></td>
                    <td><?= $row['class']; ?></td>
                    <td>
                        <span class="badge <?= $row['role']=='admin'?'bg-danger':'bg-primary' ?>">
                            <?= $row['role']; ?>
                        </span>
                    </td>
                    <td><?= $row['created_at']; ?></td>
                    <td>
                        <a href="admin-users-actions.php?delete=<?= $row['id']; ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Yakin hapus user ini?')">
                           Hapus
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>

        </table>
    </div>

</div>

</body>
</html>