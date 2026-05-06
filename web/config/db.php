<?php
$host = "db";        // nama service di docker-compose
$user = "root";
$pass = "root";
$db   = "karirmatch";

// coba koneksi (dengan retry biar aman di docker)
for ($i = 0; $i < 5; $i++) {
    $conn = @new mysqli($host, $user, $pass, $db);
    if (!$conn->connect_error) break;
    sleep(1);
}

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>