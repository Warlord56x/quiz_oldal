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
    $sql = "SELECT KERDES.KERDES FROM KERDES INNER JOIN QUIZ Q on Q.QUIZ_ID = KERDES.QUIZ_ID WHERE Q.QUIZ_NEV = '".$quiz."'";
    $stid = oci_parse($conn,$sql);
    oci_execute($stid);
    $result = array();
    while (($row = oci_fetch_array($stid, OCI_NUM)) !== false) {
        $result[] = $row[0];
    }
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

function login($email, $jelszo) {
    global $conn;
    $jelszo = md5($jelszo);
// Prepare the function call
    $sql = 'BEGIN :result := QuizKeres(:category); END;';
    $stmt = oci_parse($conn, $sql);

// Bind the input parameter
    oci_bind_by_name($stmt, ':email', $email);
    oci_bind_by_name($stmt, ':jelszo', $jelszo);

// Bind the output parameter
    oci_bind_by_name($stmt, ':result', $result, 4000);
    oci_execute($stmt);
    oci_free_statement($stmt);

    return $result;
}





?>
