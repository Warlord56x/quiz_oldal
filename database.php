<?php
declare(strict_types=1);

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

function get_quiz_questions(string $quiz):array {
    global $conn;
    $sql = "SELECT KERDES.KERDES FROM KERDES INNER JOIN QUIZ Q on Q.QUIZ_ID = KERDES.QUIZ_ID WHERE Q.QUIZ_NEV = :quiz";
    $stid = oci_parse($conn,$sql);
    oci_bind_by_name($stid, ':quiz', $quiz);
    oci_execute($stid);
    $result = array();
    while (($row = oci_fetch_array($stid, OCI_NUM)) !== false) {
        $result[] = $row[0];
    }
    oci_free_statement($stid);
    return $result;
}

function get_quiz_questions_answers(string $quiz):array {
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
    oci_free_statement($stid);
    return $result;
}

function get_quiz_list(string $category):array {
    global $conn;
    // Prepare the function call
    $sql = 'BEGIN :cursor := QUIZKERES(:category); END;';
    $stmt = oci_parse($conn, $sql);

    // Bind the input parameter
    oci_bind_by_name($stmt, ':category', $category);

    // Bind the output parameter
    $cursor = oci_new_cursor($conn);
    oci_bind_by_name($stmt, ':cursor', $cursor, -1, OCI_B_CURSOR);

    oci_execute($stmt);
    oci_execute($cursor);

    $result = [];
    oci_fetch_all($cursor, $result, 0, -1, OCI_FETCHSTATEMENT_BY_COLUMN);

    oci_free_statement($stmt);
    oci_free_cursor($cursor);

    return $result;
}

function check_answer(string $kerdes, string $valasz):bool {
    global $conn;
    $sql = "SELECT * FROM KERDES WHERE kerdes = :kerdes AND jo_valasz = :valasz";
    $stmt = oci_parse($conn, $sql);

    oci_bind_by_name($stmt, ":kerdes", $kerdes);
    oci_bind_by_name($stmt, ":valasz", $valasz);

    oci_execute($stmt);
    return oci_fetch($stmt);
}

function login(string $email, string $jelszo):bool {
    global $conn;
    $sql = "SELECT JELSZO FROM FELHASZNALO WHERE EMAIL = :email";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":email", $email);
    oci_execute($stmt);
    $result = oci_fetch_array($stmt);

    if (!$result || !isset($result["JELSZO"])) {
        // Nincs felhasznalo.
        return false;
    }

    return password_verify($jelszo ,$result["JELSZO"]);
}

function register(string $email, string $jelszo, string $vez, string $kereszt, string $eletkor):bool {
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


