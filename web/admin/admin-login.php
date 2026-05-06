<?php
session_start();

include __DIR__ . '/../config/db.php';

$error = "";

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    // ambil user
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    // CEK LOGIN (TANPA HASH)
    if($result && $password === $result['password'] && $result['role'] === 'admin'){

        $_SESSION['user_id'] = $result['id'];
        $_SESSION['role'] = $result['role'];

        header("Location: admin-dashboard.php");
        exit;

    } else {
        $error = "Email / password salah!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center" style="height:100vh;">
    <div class="card p-4 shadow" style="width:350px;">
        <h4 class="mb-3 text-center">Admin Login</h4>

        <?php if($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <input class="form-control mb-2" name="email" placeholder="Email">
            <input class="form-control mb-3" type="password" name="password" placeholder="Password">
            <button class="btn btn-dark w-100" name="login">Login</button>
        </form>
    </div>
</div>

</body>
</html>