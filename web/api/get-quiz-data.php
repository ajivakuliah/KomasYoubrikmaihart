<?php

include '../config/db.php';

header('Content-Type: application/json');

// Check database connection
if (!$conn) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed'
    ]);
    exit;
}

$data = [];

/* =========================
   MBTI DATA
========================= */

$mbti = [];

$q = mysqli_query($conn, "
    SELECT *
    FROM mbti_types
    ORDER BY code
");

if (!$q) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Query error: ' . mysqli_error($conn)
    ]);
    exit;
}

while($row = mysqli_fetch_assoc($q)) {

    $code = $row['code'];

    /* =========================
       BIDANG MBTI
    ========================= */

    $fields = [];

    $stmt_f = $conn->prepare("
        SELECT field_name
        FROM mbti_fields
        WHERE mbti_code = ?
    ");
    
    if ($stmt_f) {
        $stmt_f->bind_param("s", $code);
        $stmt_f->execute();
        $f = $stmt_f->get_result();
        
        while($fr = mysqli_fetch_assoc($f)) {
            $fields[] = $fr['field_name'];
        }
        $stmt_f->close();
    }

    /* =========================
       KARIR MBTI
    ========================= */

    $careers = [];

    $stmt_c = $conn->prepare("
        SELECT c.career_name
        FROM mbti_careers mc
        JOIN careers c
        ON c.id = mc.career_id
        WHERE mc.mbti_code = ?
    ");

    if ($stmt_c) {
        $stmt_c->bind_param("s", $code);
        $stmt_c->execute();
        $cq = $stmt_c->get_result();
        
        while($c = mysqli_fetch_assoc($cq)) {
            $careers[] = $c['career_name'];
        }
        $stmt_c->close();
    }

    /* =========================
       RELASI MBTI -> RIASEC
    ========================= */

    $riasec = [];

    $stmt_r = $conn->prepare("
        SELECT riasec_code
        FROM mbti_riasec
        WHERE mbti_code = ?
    ");

    if ($stmt_r) {
        $stmt_r->bind_param("s", $code);
        $stmt_r->execute();
        $rq = $stmt_r->get_result();
        
        while($r = mysqli_fetch_assoc($rq)) {
            $riasec[] = $r['riasec_code'];
        }
        $stmt_r->close();
    }

    /* =========================
       FINAL ARRAY
    ========================= */

    $mbti[$code] = [

        "name" => $row['name'],

        "desc" => $row['description'],

        "fields" => $fields,

        "careers" => $careers,

        "riasec" => $riasec

    ];
}

/* =========================
   RIASEC QUESTIONS
========================= */

$questions = [];

$q2 = mysqli_query($conn, "
    SELECT *
    FROM riasec_questions
    ORDER BY id ASC
");

if ($q2) {
    while($row = mysqli_fetch_assoc($q2)) {
        $questions[] = [
            "q" => $row['question'],
            "t" => $row['riasec_type']
        ];
    }
}

/* =========================
   RIASEC INFO
========================= */

$riasecInfo = [];

$q3 = mysqli_query($conn, "
    SELECT *
    FROM riasec_types
    ORDER BY code ASC
");

if ($q3) {
    while($row = mysqli_fetch_assoc($q3)) {
        $code = $row['code'];
        $careers = [];

        $stmt_rc = $conn->prepare("
            SELECT c.career_name
            FROM riasec_careers rc
            JOIN careers c
            ON c.id = rc.career_id
            WHERE rc.riasec_code = ?
        ");

        if ($stmt_rc) {
            $stmt_rc->bind_param("s", $code);
            $stmt_rc->execute();
            $cq = $stmt_rc->get_result();
            
            while($c = mysqli_fetch_assoc($cq)) {
                $careers[] = $c['career_name'];
            }
            $stmt_rc->close();
        }

        $riasecInfo[$code] = [
            "label" => $row['label'],
            "careers" => $careers
        ];
    }
}

/* =========================
   JURUSAN
========================= */

$majors = [];

$q4 = mysqli_query($conn, "
    SELECT *
    FROM riasec_types
    ORDER BY code ASC
");

if ($q4) {
    while($row = mysqli_fetch_assoc($q4)) {
        $code = $row['code'];
        $list = [];

        $stmt_m = $conn->prepare("
            SELECT m.major_name
            FROM riasec_majors rm
            JOIN majors m
            ON m.id = rm.major_id
            WHERE rm.riasec_code = ?
        ");

        if ($stmt_m) {
            $stmt_m->bind_param("s", $code);
            $stmt_m->execute();
            $mq = $stmt_m->get_result();
            
            while($m = mysqli_fetch_assoc($mq)) {
                $list[] = $m['major_name'];
            }
            $stmt_m->close();
        }

        $majors[$code] = $list;
    }
}

/* =========================
   OUTPUT JSON
========================= */

echo json_encode([

    "MBTI_DATA" => $mbti,

    "RIASEC_Q" => $questions,

    "RIASEC_INFO" => $riasecInfo,

    "JURUSAN" => $majors

], JSON_PRETTY_PRINT);
?>