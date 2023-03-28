<?php

$tns = "localhost/XE";

$conn = oci_connect('USERDANI', 'dummy', $tns,'UTF8');

function get_all_quiz(): array
{
    global $conn;
    $stid = oci_parse($conn, 'SELECT quiz_nev FROM Quiz');
    oci_execute($stid);
    $result = array();
    while (($row = oci_fetch_array($stid, OCI_NUM)) !== false) {
        $result[] = $row[0];
    }
    return $result;
}

function get_quiz_questions($quiz):array {
    global $conn;
    $sql = "SELECT KERDES.KERDES FROM KERDES INNER JOIN QUIZ Q on Q.QUIZ_ID = KERDES.QUIZ_ID WHERE Q.QUIZ_NEV = :quiz";
    $stid = oci_parse($conn,$sql);
    oci_bind_by_name($stid, ':quiz', $quiz);
    oci_execute($stid);
    $result = array();
    while (($row = oci_fetch_array($stid, OCI_NUM)) !== false) {
        $result[] = $row[0];
    }
    return $result;
}

function get_quiz_questions_answers($quiz):array {
    global $conn;
    $sql = "SELECT K.KERDES, (K.JO_VALASZ || ', ' ||
        LISTAGG(R.ROSSZ_VALASZ, ', ') WITHIN GROUP (ORDER BY R.ROSSZ_VALASZ)) AS VALASZOK
        FROM ROSSZVALASZ R
        INNER JOIN KERDES K ON K.KERDES_ID = R.KERDES_ID
        INNER JOIN QUIZ Q ON Q.QUIZ_ID = K.QUIZ_ID
        WHERE Q.QUIZ_NEV = :quiz
        GROUP BY K.KERDES, K.JO_VALASZ";
    $stid = oci_parse($conn,$sql);
    oci_bind_by_name($stid, ':quiz', $quiz);
    oci_execute($stid);
    $result = array();
    while (($row = oci_fetch_array($stid, OCI_NUM)) !== false) {
        $result[] = $row;
    }
    for($i = 0; $i < count($result); $i++) {
        $result[$i][] = explode(', ', $result[$i][1]);
    }
    //print_r($result[0][2]);
    return $result;
}

function getQuizList($category) {
    global $conn;
    // Prepare the function call
    $sql = 'BEGIN :result := QuizKeres(:category); END;';
    $stmt = oci_parse($conn, $sql);

    // Bind the input parameter
    oci_bind_by_name($stmt, ':category', $category);

    // Bind the output parameter
    oci_bind_by_name($stmt, ':result', $result, 4000);
    oci_execute($stmt);
    oci_free_statement($stmt);

    return $result;
}

function login($email, $jelszo):bool {
    global $conn;
    $sql = "SELECT JELSZO FROM FELHASZNALO WHERE EMAIL = :email";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":email", $email);
    oci_execute($stmt);
    $result  = oci_fetch_array($stmt);

    if (!$result || !isset($result["JELSZO"])) {
        // Nincs felhasznalo.
        return false;
    }

    return password_verify($jelszo ,$result["JELSZO"]);
}

function register($email, $jelszo, $vez, $kereszt, $eletkor):bool {
    global $conn;
    $sql = 'BEGIN :result := REGISTER(:email, :jelszo, :vez, :kereszt, :eletkor); END;';
    $stmt = oci_parse($conn, $sql);

    // Bind the input parameter
    $jelszo_hash = password_hash($jelszo, PASSWORD_BCRYPT);
    oci_bind_by_name($stmt, ':email', $email);
    oci_bind_by_name($stmt, ':jelszo', $jelszo_hash);
    oci_bind_by_name($stmt, ':vez', $vez);
    oci_bind_by_name($stmt, ':kereszt', $kereszt);
    oci_bind_by_name($stmt, ':eletkor', $eletkor);

    // Bind the output parameter
    $result = 0;
    oci_bind_by_name($stmt, ':result', $result);
    oci_execute($stmt);
    oci_free_statement($stmt);
    return $result === 1;
}


