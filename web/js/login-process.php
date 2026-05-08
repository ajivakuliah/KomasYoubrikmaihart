<?php

session_start();

require_once 'config.php';

/* =========================
   LOGIN USER
========================= */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {

        $_SESSION['login_error'] = "Email dan password wajib diisi!";
        header("Location: login.php");
        exit;
    }

    $stmt = $conn->prepare("
        SELECT *
        FROM users
        WHERE email = ?
    ");

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result()->fetch_assoc();

    if (
        $result &&
        $password === $result['password']
    ) {

        $_SESSION['user_id'] = $result['id'];
        $_SESSION['name']    = $result['name'];
        $_SESSION['email']   = $result['email'];
        $_SESSION['role']    = $result['role'];

        header("Location: quiz.php");
        exit;

    } else {

        $_SESSION['login_error'] = "Email atau password salah!";
        header("Location: login.php");
        exit;
    }
}
?>