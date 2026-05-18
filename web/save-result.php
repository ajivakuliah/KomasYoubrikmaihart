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

if (!$data) {
    echo json_encode([
        'success' => false,
        'message' => 'Payload tidak valid atau kosong'
    ]);
    exit;
}

$mbti_code          = trim($data['mbti']                ?? '');
$top_riasec         = trim($data['top_riasec']          ?? '');
$riasec_r           = floatval($data['riasec_r']        ?? 0);
$riasec_i           = floatval($data['riasec_i']        ?? 0);
$riasec_a           = floatval($data['riasec_a']        ?? 0);
$riasec_s           = floatval($data['riasec_s']        ?? 0);
$riasec_e           = floatval($data['riasec_e']        ?? 0);
$riasec_c           = floatval($data['riasec_c']        ?? 0);
$recommended_major  = trim($data['recommended_major']   ?? '');
$recommended_careers = trim($data['recommended_careers'] ?? '');

$user_id = $_SESSION['user_id'];

/* =====================================
   VALIDASI
===================================== */

if (empty($mbti_code) || empty($top_riasec)) {
    echo json_encode([
        'success' => false,
        'message' => 'Data tidak lengkap: mbti dan top_riasec wajib diisi'
    ]);
    exit;
}

/* =====================================
   CEK KONEKSI
===================================== */

if (!$conn) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]);
    exit;
}

/* =====================================
   INSERT RESULT
===================================== */

$stmt = $conn->prepare("
    INSERT INTO test_results
    (
        user_id,
        mbti_code,
        riasec_r,
        riasec_i,
        riasec_a,
        riasec_s,
        riasec_e,
        riasec_c,
        top_riasec,
        recommended_major,
        recommended_careers
    )
    VALUES
    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Prepare statement failed: ' . $conn->error
    ]);
    exit;
}

if (!$stmt->bind_param(
    "isddddddsss",
    $user_id,
    $mbti_code,
    $riasec_r,
    $riasec_i,
    $riasec_a,
    $riasec_s,
    $riasec_e,
    $riasec_c,
    $top_riasec,
    $recommended_major,
    $recommended_careers
)) {
    echo json_encode([
        'success' => false,
        'message' => 'Bind param failed: ' . $stmt->error
    ]);
    exit;
}

if (!$stmt->execute()) {
    echo json_encode([
        'success' => false,
        'message' => 'Execute failed: ' . $stmt->error
    ]);
    exit;
}

$result_id = $stmt->insert_id;
$stmt->close();

/* =====================================
   AMBIL TANGGAL YANG BARU TERSIMPAN
===================================== */

$stmt_date = $conn->prepare("
    SELECT created_at FROM test_results WHERE id = ?
");

$saved_date = null;

if ($stmt_date) {
    $stmt_date->bind_param("i", $result_id);
    $stmt_date->execute();
    $row = $stmt_date->get_result()->fetch_assoc();
    $saved_date = $row['created_at'] ?? null;
    $stmt_date->close();
}

echo json_encode([
    'success'    => true,
    'message'    => 'Hasil berhasil disimpan',
    'result_id'  => $result_id,
    'saved_at'   => $saved_date   // dikembalikan ke JS untuk ditampilkan di UI
]);