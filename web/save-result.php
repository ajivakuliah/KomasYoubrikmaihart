<?php

session_start();

header('Content-Type: application/json');

require_once 'config.php';

/* =====================================
   CEK LOGIN
===================================== */

if (!isset($_SESSION['user_id'])) {

    echo json_encode([
        'success' => false,
        'message' => 'User belum login'
    ]);

    exit;
}

/* =====================================
   AMBIL DATA JSON
===================================== */

$data = json_decode(file_get_contents("php://input"), true);

$mbti  = trim($data['mbti'] ?? '');
$riasec = trim($data['riasec'] ?? '');

$user_id = $_SESSION['user_id'];

/* =====================================
   VALIDASI
===================================== */

if (empty($mbti) || empty($riasec)) {

    echo json_encode([
        'success' => false,
        'message' => 'Data tidak lengkap'
    ]);

    exit;
}

/* =====================================
   INSERT RESULT
===================================== */

$stmt = $conn->prepare("
    INSERT INTO results
    (
        user_id,
        mbti,
        riasec
    )
    VALUES
    (
        ?, ?, ?
    )
");

$stmt->bind_param(
    "iss",
    $user_id,
    $mbti,
    $riasec
);

if ($stmt->execute()) {

    echo json_encode([
        'success' => true,
        'message' => 'Hasil berhasil disimpan'
    ]);

} else {

    echo json_encode([
        'success' => false,
        'message' => 'Gagal menyimpan hasil'
    ]);
}
?>