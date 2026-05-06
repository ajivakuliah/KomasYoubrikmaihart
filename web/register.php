<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "INSERT INTO users (name, email, password) 
              VALUES ('$name', '$email', '$password')";

    if (mysqli_query($conn, $query)) {
        echo "Registrasi berhasil!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>