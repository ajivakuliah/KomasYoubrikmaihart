<?php

session_start();

/* BELUM LOGIN */
if (!isset($_SESSION['user_id'])) {

    header("Location: admin-login.php");
    exit;
}

/* ROLE TIDAK ADA */
if (!isset($_SESSION['role'])) {

    session_destroy();

    header("Location: admin-login.php");
    exit;
}

/* BUKAN ADMIN */
if ($_SESSION['role'] != 'admin') {

    header("Location: ../index.php");
    exit;
}
?>