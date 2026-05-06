<?php

// default untuk Laragon / hosting
$host = "localhost";
$user = "root";
$password = "";
$db = "myapp";

// kalau jalan di Docker
if (getenv("DOCKER_ENV")) {
    $host = "db";
    $user = "user";
    $password = "password";
}

// koneksi
$conn = mysqli_connect("db", "root", "root", "karirmatch");

// cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>