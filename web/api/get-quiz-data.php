<?php

include '../config/db.php';

header('Content-Type: application/json');

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

while($row = mysqli_fetch_assoc($q)) {

    $code = $row['code'];

    /* =========================
       BIDANG MBTI
    ========================= */

    $fields = [];

    $f = mysqli_query($conn, "
        SELECT field_name
        FROM mbti_fields
        WHERE mbti_code = '$code'
    ");

    while($fr = mysqli_fetch_assoc($f)) {

        $fields[] = $fr['field_name'];

    }

    /* =========================
       KARIR MBTI
    ========================= */

    $careers = [];

    $cq = mysqli_query($conn, "
        SELECT c.career_name
        FROM mbti_careers mc
        JOIN careers c
        ON c.id = mc.career_id
        WHERE mc.mbti_code = '$code'
    ");

    while($c = mysqli_fetch_assoc($cq)) {

        $careers[] = $c['career_name'];

    }

    /* =========================
       RELASI MBTI -> RIASEC
    ========================= */

    $riasec = [];

    $rq = mysqli_query($conn, "
        SELECT riasec_code
        FROM mbti_riasec
        WHERE mbti_code = '$code'
    ");

    while($r = mysqli_fetch_assoc($rq)) {

        $riasec[] = $r['riasec_code'];

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

while($row = mysqli_fetch_assoc($q2)) {

    $questions[] = [

        "q" => $row['question'],

        "t" => $row['riasec_type']

    ];
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

while($row = mysqli_fetch_assoc($q3)) {

    $code = $row['code'];

    $careers = [];

    $cq = mysqli_query($conn, "
        SELECT c.career_name
        FROM riasec_careers rc
        JOIN careers c
        ON c.id = rc.career_id
        WHERE rc.riasec_code = '$code'
    ");

    while($c = mysqli_fetch_assoc($cq)) {

        $careers[] = $c['career_name'];

    }

    $riasecInfo[$code] = [

        "label" => $row['label'],

        "careers" => $careers

    ];
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

while($row = mysqli_fetch_assoc($q4)) {

    $code = $row['code'];

    $list = [];

    $mq = mysqli_query($conn, "
        SELECT m.major_name
        FROM riasec_majors rm
        JOIN majors m
        ON m.id = rm.major_id
        WHERE rm.riasec_code = '$code'
    ");

    while($m = mysqli_fetch_assoc($mq)) {

        $list[] = $m['major_name'];

    }

    $majors[$code] = $list;
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