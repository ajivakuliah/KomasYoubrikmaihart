<?php

require_once 'config.php';

$success = "";
$error   = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    /* VALIDASI */
    if(
        !empty($name) &&
        !empty($email) &&
        !empty($subject) &&
        !empty($message)
    ){

        $stmt = $conn->prepare("
            INSERT INTO contact
            (
                name,
                email,
                subject,
                message
            )
            VALUES
            (?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "ssss",
            $name,
            $email,
            $subject,
            $message
        );

        if($stmt->execute()){

            $success = "Pesan berhasil dikirim.";

        } else {

            $error = "Gagal mengirim pesan.";

        }

    } else {

        $error = "Semua field wajib diisi.";

    }
}
?>

<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1">

    <meta name="description"
        content="">

    <meta name="author"
        content="">

    <title>Contact Form</title>

    <!-- CSS FILES -->
    <link rel="preconnect"
        href="https://fonts.googleapis.com">

    <link rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&family=Open+Sans&display=swap"
        rel="stylesheet">

    <link href="css/bootstrap.min.css"
        rel="stylesheet">

    <link href="css/bootstrap-icons.css"
        rel="stylesheet">

    <link href="css/templatemo-topic-listing.css"
        rel="stylesheet">

</head>

<body class="topics-listing-page"
    id="top">

<main>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg">

        <div class="container">

            <a class="navbar-brand"
                href="index.php">

                <i class="bi-back"></i>

                <span>CareerPath</span>

            </a>

            <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav">

                <span class="navbar-toggler-icon"></span>

            </button>

            <div class="collapse navbar-collapse"
                id="navbarNav">

                <ul class="navbar-nav ms-lg-5 me-lg-auto">

                    <li class="nav-item">
                        <a class="nav-link"
                            href="index.php">

                            Home

                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link active"
                            href="contact.php">

                            Contact

                        </a>
                    </li>

                </ul>

            </div>

        </div>

    </nav>

    <!-- HEADER -->
    <header class="site-header d-flex flex-column justify-content-center align-items-center">

        <div class="container">

            <div class="row align-items-center">

                <div class="col-lg-5 col-12">

                    <nav aria-label="breadcrumb">

                        <ol class="breadcrumb">

                            <li class="breadcrumb-item">

                                <a href="index.php">
                                    Homepage
                                </a>

                            </li>

                            <li class="breadcrumb-item active">

                                Contact Form

                            </li>

                        </ol>

                    </nav>

                    <h2 class="text-white">

                        Contact Form

                    </h2>

                </div>

            </div>

        </div>

    </header>

    <!-- CONTACT SECTION -->
    <section class="section-padding section-bg">

        <div class="container">

            <div class="row">

                <!-- FORM -->
                <div class="col-lg-6 col-12">

                    <h3 class="mb-4 pb-2">

                        We'd love to hear from you

                    </h3>

                    <!-- ALERT -->
                    <?php if($success): ?>

                        <div class="alert alert-success">

                            <?= htmlspecialchars($success) ?>

                        </div>

                    <?php endif; ?>

                    <?php if($error): ?>

                        <div class="alert alert-danger">

                            <?= htmlspecialchars($error) ?>

                        </div>

                    <?php endif; ?>

                    <!-- FORM -->
                    <form action=""
                        method="POST"
                        class="custom-form contact-form">

                        <div class="row">

                            <!-- NAME -->
                            <div class="col-lg-6 col-md-6 col-12">

                                <div class="form-floating">

                                    <input
                                        type="text"
                                        name="name"
                                        id="name"
                                        class="form-control"
                                        placeholder="Name"
                                        required>

                                    <label for="name">

                                        Name

                                    </label>

                                </div>

                            </div>

                            <!-- EMAIL -->
                            <div class="col-lg-6 col-md-6 col-12">

                                <div class="form-floating">

                                    <input
                                        type="email"
                                        name="email"
                                        id="email"
                                        class="form-control"
                                        placeholder="Email address"
                                        required>

                                    <label for="email">

                                        Email address

                                    </label>

                                </div>

                            </div>

                            <!-- SUBJECT -->
                            <div class="col-lg-12 col-12">

                                <div class="form-floating">

                                    <input
                                        type="text"
                                        name="subject"
                                        id="subject"
                                        class="form-control"
                                        placeholder="Subject"
                                        required>

                                    <label for="subject">

                                        Subject

                                    </label>

                                </div>

                            </div>

                            <!-- MESSAGE -->
                            <div class="col-lg-12 col-12">

                                <div class="form-floating">

                                    <textarea
                                        class="form-control"
                                        id="message"
                                        name="message"
                                        placeholder="Tell me about the project"
                                        style="height: 180px"
                                        required></textarea>

                                    <label for="message">

                                        Tell me about the project

                                    </label>

                                </div>

                            </div>

                            <!-- BUTTON -->
                            <div class="col-lg-4 col-12 ms-auto">

                                <button
                                    type="submit"
                                    class="form-control">

                                    Submit

                                </button>

                            </div>

                        </div>

                    </form>

                </div>

                <!-- MAP -->
                <div class="col-lg-5 col-12 mx-auto mt-5 mt-lg-0">

                    <iframe class="google-map"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2595.065641062665!2d-122.4230416990949!3d37.80335401520422!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80858127459fabad%3A0x808ba520e5e9edb7!2sFrancisco%20Park!5e1!3m2!1sen!2sth!4v1684340239744!5m2!1sen!2sth"
                        width="100%"
                        height="250"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy">

                    </iframe>

                    <h5 class="mt-4 mb-2">

                        CareerPath Center

                    </h5>

                    <p>
                        Indonesia
                    </p>

                </div>

            </div>

        </div>

    </section>

</main>

<!-- FOOTER -->
<footer class="site-footer section-padding">

    <div class="container">

        <div class="row">

            <div class="col-lg-12 col-12 text-center">

                <p class="copyright-text">

                    Copyright © <?= date('Y') ?>
                    CareerPath

                </p>

            </div>

        </div>

    </div>

</footer>

<!-- JS -->
<script src="js/jquery.min.js"></script>

<script src="js/bootstrap.bundle.min.js"></script>

<script src="js/jquery.sticky.js"></script>

<script src="js/custom.js"></script>

</body>
</html>