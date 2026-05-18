<?php

$host = "db";
$user = "root";
$password = "root";
$db = "karirmatch";

// koneksi dengan retry
$conn = null;
for ($i = 0; $i < 3; $i++) {
    $conn = @mysqli_connect($host, $user, $password, $db);
    if ($conn) break;
    sleep(1);
}

// cek koneksi
if (!$conn) {
    die("
    <div style='padding: 20px; font-family: Arial; color: #d32f2f;'>
        <h3>❌ Koneksi Database Gagal</h3>
        <p><strong>Error:</strong> " . mysqli_connect_error() . "</p>
        <hr>
        <h4>Solusi:</h4>
        <ol>
            <li>✅ Pastikan <strong>MySQL di XAMPP sedang RUNNING</strong>
                <ul>
                    <li>Buka XAMPP Control Panel</li>
                    <li>Klik tombol <strong>Start</strong> untuk MySQL</li>
                </ul>
            </li>
            <li>✅ Pastikan database <strong>karirmatch</strong> sudah dibuat
                <ul>
                    <li>Buka phpMyAdmin: http://localhost/phpmyadmin</li>
                    <li>Import file <strong>karirmatch.sql</strong></li>
                </ul>
            </li>
            <li>✅ Cek password MySQL XAMPP (default adalah kosong)</li>
        </ol>
    </div>
    ");
}
?>