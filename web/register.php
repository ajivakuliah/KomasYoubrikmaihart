<?php

session_start();

require_once 'config.php';

/* =========================
   REGISTER USER
========================= */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // AMBIL DATA
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $class    = trim($_POST['class']);
    $password = trim($_POST['password']);

    /* =========================
       VALIDASI
    ========================= */

    if (
        empty($name) ||
        empty($email) ||
        empty($phone) ||
        empty($class) ||
        empty($password)
    ) {

        $_SESSION['register_error'] = "Semua field wajib diisi!";
        header("Location: login.php");
        exit;
    }

    /* =========================
       CEK EMAIL
    ========================= */

    $check = $conn->prepare("
        SELECT id
        FROM users
        WHERE email = ?
    ");

    $check->bind_param("s", $email);
    $check->execute();

    $result = $check->get_result();

    if ($result->num_rows > 0) {

        $_SESSION['register_error'] = "Email sudah digunakan!";
        header("Location: login.php");
        exit;
    }

    /* =========================
       INSERT USER
    ========================= */

    $role = 'user';

    $stmt = $conn->prepare("
        INSERT INTO users
        (
            name,
            email,
            phone,
            class,
            password,
            role
        )
        VALUES
        (
            ?, ?, ?, ?, ?, ?
        )
    ");

    $stmt->bind_param(
        "ssssss",
        $name,
        $email,
        $phone,
        $class,
        $password,
        $role
    );

    if ($stmt->execute()) {

        // AUTO LOGIN
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['name']    = $name;
        $_SESSION['email']   = $email;
        $_SESSION['role']    = $role;

        header("Location: quiz.php");
        exit;

    } else {

        $_SESSION['register_error'] = "Registrasi gagal!";
        header("Location: login.php");
        exit;
    }
}
?>