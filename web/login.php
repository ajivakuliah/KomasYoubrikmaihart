<?php
session_start();
require_once 'config.php';

/* =========================
   LOGIN
========================= */

if(isset($_POST['login'])){

    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("
        SELECT * FROM users
        WHERE email = ?
    ");

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result()->fetch_assoc();

    if($result && $password === $result['password']){

        $_SESSION['user_id'] = $result['id'];
        $_SESSION['name']    = $result['name'];
        $_SESSION['email']   = $result['email'];
        $_SESSION['role']    = $result['role'];

        $redirect = $result['role'] === 'admin' ? 'admin/admin-dashboard.php' : 'student-home.php';

        echo "
        <script>
            alert('Login berhasil!');
            window.location='$redirect';
        </script>
        ";
        exit;

    } else {

        echo "
        <script>
            alert('Email atau password salah!');
        </script>
        ";
    }
}

/* =========================
   REGISTER
========================= */

if(isset($_POST['register'])){

    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $class    = trim($_POST['class']);
    $gender   = trim($_POST['gender'] ?? '');
    $password = trim($_POST['password']);

    // cek email
    $check = $conn->prepare("
        SELECT id FROM users
        WHERE email = ?
    ");

    $check->bind_param("s", $email);
    $check->execute();

    if($check->get_result()->num_rows > 0){

        echo "
        <script>
            alert('Email sudah digunakan!');
        </script>
        ";

    } else {

        $stmt = $conn->prepare("
            INSERT INTO `users`
            (
                `name`,
                `email`,
                `phone`,
                `class`,
                `gender`,
                `password`,
                `role`
            )
            VALUES (?, ?, ?, ?, ?, ?, 'user')
        ");

        if(!$stmt){
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param(
            "ssssss",
            $name,
            $email,
            $phone,
            $class,
            $gender,
            $password
        );

        if($stmt->execute()){

            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['name']    = $name;
            $_SESSION['email']   = $email;
            $_SESSION['role']    = 'user';

            echo "
            <script>
                alert('Registrasi berhasil!');
                window.location='student-home.php';
            </script>
            ";
            exit;

        } else {

            echo "
            <script>
                alert('Registrasi gagal!');
            </script>
            ";
        }
    }
}
?>

<!doctype html>
<html lang="id">

<head>

    <meta charset="utf-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1">

    <meta name="description"
        content="Login dan Registrasi - Web Prediksi Jurusan & Karir berbasis tes MBTI">

    <title>
        Login & Registrasi - PrediksiKarir
    </title>

    <!-- GOOGLE FONT -->
    <link rel="preconnect"
        href="https://fonts.googleapis.com">

    <link rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=DM+Sans:wght@400;500&display=swap"
        rel="stylesheet">

    <!-- ICON -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- CSS -->
    <link rel="stylesheet"
        href="css/login.css">

</head>

<body>

    <div class="bg-orb orb-1"></div>
    <div class="bg-orb orb-2"></div>
    <div class="bg-orb orb-3"></div>

    <div class="auth-wrapper">

        <div class="auth-card">

            <!-- LOGIN -->
            <div id="loginForm"
                class="auth-panel active">

                <div class="auth-brand">
                    <span class="brand-name">
                        PrediksiKarir
                    </span>
                </div>

                <h1 class="auth-title">
                    Selamat Datang
                </h1>

                <p class="auth-subtitle">
                    Lanjutkan perjalanan menemukan karir impianmu
                </p>

                <!-- FORM LOGIN -->
                <form method="POST">

                    <!-- EMAIL -->
                    <div class="field-group">

                        <label class="field-label">

                            <i class="bi bi-envelope"></i>
                            Email

                        </label>

                        <input
                            type="email"
                            name="email"
                            class="field-input"
                            placeholder="contoh@email.com"
                            required>

                    </div>

                    <!-- PASSWORD -->
                    <div class="field-group">

                        <label class="field-label">

                            <i class="bi bi-lock"></i>
                            Password

                        </label>

                        <div class="input-icon-wrap">

                            <input
                                type="password"
                                name="password"
                                id="loginPassword"
                                class="field-input"
                                placeholder="Masukkan password Anda"
                                required>

                            <button type="button"
                                class="toggle-eye"
                                onclick="togglePassword('loginPassword', this)">

                                <i class="bi bi-eye"></i>

                            </button>

                        </div>

                    </div>

                    <!-- BUTTON -->
                    <button
                        type="submit"
                        name="login"
                        class="btn-primary">

                        <span>Masuk</span>

                        <i class="bi bi-arrow-right"></i>

                    </button>

                </form>

                <!-- SWITCH -->
                <div class="toggle-hint">

                    <span>
                        Belum punya akun?
                    </span>

                    <button
                        type="button"
                        class="link-toggle"
                        onclick="switchPanel('register')">

                        Buat akun baru

                    </button>

                </div>

            </div>

            <!-- REGISTER -->
            <div id="registerForm"
                class="auth-panel">

                <div class="auth-brand">
                    <span class="brand-name">
                        PrediksiKarir
                    </span>
                </div>

                <h1 class="auth-title">
                    Buat Akun Baru
                </h1>

                <p class="auth-subtitle">
                    Mulai temukan karir yang tepat untukmu
                </p>

                <!-- FORM REGISTER -->
                <form method="POST">

                    <!-- NAMA -->
                    <div class="field-group">

                        <label class="field-label">

                            <i class="bi bi-person"></i>
                            Nama Lengkap

                        </label>

                        <input
                            type="text"
                            name="name"
                            class="field-input"
                            placeholder="Nama lengkap Anda"
                            required>

                    </div>

                    <!-- EMAIL -->
                    <div class="field-group">

                        <label class="field-label">

                            <i class="bi bi-envelope"></i>
                            Email

                        </label>

                        <input
                            type="email"
                            name="email"
                            class="field-input"
                            placeholder="contoh@email.com"
                            required>

                    </div>

                    <!-- PHONE -->
                    <div class="field-group">

                        <label class="field-label">

                            <i class="bi bi-phone"></i>
                            No. Telepon

                        </label>

                        <input
                            type="text"
                            name="phone"
                            class="field-input"
                            placeholder="08XXXXXXXXXX"
                            required>

                    </div>

                    <!-- KELAS -->
                    <div class="field-group">

                        <label class="field-label">

                            <i class="bi bi-book"></i>
                            Kelas

                        </label>

                        <select
                            name="class"
                            class="field-input"
                            required>

                            <option value="">
                                -- Pilih Kelas --
                            </option>

                            <option value="10">
                                Kelas 10
                            </option>

                            <option value="11">
                                Kelas 11
                            </option>

                            <option value="12">
                                Kelas 12
                            </option>

                        </select>



                    <!-- JENIS KELAMIN -->
                    <div class="field-group">

                        <label class="field-label">

                            <i class="bi bi-person-check"></i>
                            Jenis Kelamin (Opsional)

                        </label>

                        <select
                            name="gender"
                            class="field-input">

                            <option value="">
                                -- Pilih Jenis Kelamin --
                            </option>

                            <option value="L">
                                Laki-laki
                            </option>

                            <option value="P">
                                Perempuan
                            </option>

                        </select>

                    </div>

                    <!-- PASSWORD -->
                    <div class="field-row">

                        <div class="field-group">

                            <label class="field-label">

                                <i class="bi bi-lock"></i>
                                Password

                            </label>

                            <div class="input-icon-wrap">

                                <input
                                    type="password"
                                    name="password"
                                    id="registerPassword"
                                    class="field-input"
                                    placeholder="Min. 6 karakter"
                                    required>

                                <button type="button"
                                    class="toggle-eye"
                                    onclick="togglePassword('registerPassword', this)">

                                    <i class="bi bi-eye"></i>

                                </button>

                            </div>

                        </div>

                    </div>

                    <!-- TERMS -->
                    <label class="check-label terms-label">

                        <input type="checkbox"
                            class="check-input"
                            required>

                        <span class="check-box"></span>

                        <span>
                            Saya setuju dengan
                            Kebijakan Privasi
                            dan
                            Syarat & Ketentuan
                        </span>

                    </label>

                    <!-- BUTTON -->
                    <button
                        type="submit"
                        name="register"
                        class="btn-primary">

                        <span>Buat Akun</span>

                        <i class="bi bi-arrow-right"></i>

                    </button>

                </form>

                <!-- SWITCH -->
                <div class="toggle-hint">

                    <span>
                        Sudah punya akun?
                    </span>

                    <button
                        type="button"
                        class="link-toggle"
                        onclick="switchPanel('login')">

                        Masuk di sini

                    </button>

                </div>

            </div>

        </div>

    </div>

    <!-- JS -->
    <script>

        function switchPanel(target){

            const loginPanel =
                document.getElementById('loginForm');

            const registerPanel =
                document.getElementById('registerForm');

            if(target === 'register'){

                loginPanel.classList.remove('active');

                registerPanel.classList.add('active');

            } else {

                registerPanel.classList.remove('active');

                loginPanel.classList.add('active');
            }
        }

        function togglePassword(fieldId, btn){

            const input =
                document.getElementById(fieldId);

            const icon =
                btn.querySelector('i');

            if(input.type === 'password'){

                input.type = 'text';
                icon.className = 'bi bi-eye-slash';

            } else {

                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }

    </script>

</body>
</html>