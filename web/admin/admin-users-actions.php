<?php
session_start();
include __DIR__ . '/../config/db.php';

// proteksi admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin-login.php");
    exit;
}

/* ========================
   TAMBAH USER
======================== */
if (isset($_POST['add'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $class = $_POST['class'];

    // validasi
    if ($name == "" || $email == "" || $password == "") {
        die("Semua field wajib diisi!");
    }

    // cek email sudah ada
    $check = $conn->prepare("SELECT id FROM users WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        die("Email sudah digunakan!");
    }

    // insert user (password masih plain text sesuai sistem kamu sekarang)
    $stmt = $conn->prepare("INSERT INTO users (name,email,password,class,role) VALUES (?,?,?,?, 'user')");
    $stmt->bind_param("ssss", $name, $email, $password, $class);

    if ($stmt->execute()) {
        header("Location: admin-users.php");
        exit;
    } else {
        echo "Gagal menambahkan user";
    }
}

/* ========================
   DELETE USER
======================== */
if (isset($_GET['delete'])) {

    $id = intval($_GET['delete']);

    // cegah hapus diri sendiri (optional tapi penting)
    if ($id == $_SESSION['user_id']) {
        die("Tidak bisa hapus akun sendiri!");
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: admin-users.php");
        exit;
    } else {
        echo "Gagal menghapus user";
    }
}