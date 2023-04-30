<?php

// Ma favorite stuff
declare(strict_types=1);

require_once "account.php";

$tns = "localhost/XE";

$conn = oci_connect('USERDANI', 'dummy', $tns,'UTF8');

function get_all_quiz(): array
{
    global $conn;
    $stid = oci_parse($conn, 'SELECT quiz_nev, quiz_id FROM Quiz');
    oci_execute($stid);
    $result = array();
    oci_fetch_all($stid, $result);

    return $result;
}

function get_my_quizes(Account $account): array
{
    global $conn;
    $sql = "SELECT quiz_nev, QUIZ.quiz_id FROM Quiz
    inner join KESZIT K on QUIZ.QUIZ_ID = K.QUIZ_ID
    inner join FELHASZNALO F on F.FELHASZNALO_ID = K.FELHASZNALO_ID
    where f.FELHASZNALO_ID = :id";

    $stid = oci_parse($conn, $sql);
    $id = $account->getId();
    oci_bind_by_name($stid, ':id', $id);
    oci_execute($stid);
    print_r(oci_error($stid));
    $result = array();
    oci_fetch_all($stid, $result);
    return $result;
}



function get_quiz_questions_answers(int $quiz_id): array {
    global $conn;
    $sql = "
        SELECT K.KERDES, (K.JO_VALASZ || ', ' ||
            LISTAGG(R.ROSSZ_VALASZ, ', ') WITHIN GROUP (ORDER BY R.ROSSZ_VALASZ)) AS VALASZOK
        FROM ROSSZVALASZ R
            INNER JOIN KERDES K ON K.KERDES_ID = R.KERDES_ID
            INNER JOIN QUIZ Q ON Q.QUIZ_ID = K.QUIZ_ID
        WHERE Q.QUIZ_ID = :quiz
        GROUP BY K.KERDES, K.JO_VALASZ
        ";
    $stid = oci_parse($conn,$sql);
    oci_bind_by_name($stid, ':quiz', $quiz_id);
    oci_execute($stid);
    $result = array();
    while (($row = oci_fetch_array($stid, OCI_NUM)) !== false) {
        $result[] = $row;
    }
    for($i = 0; $i < count($result); $i++) {
        $result[$i][] = explode(', ', $result[$i][1]);
    }
    return $result;
}

function get_quiz_list(string $category = ""): array {
    global $conn;
    // Prepare the function call
    $sql = 'BEGIN :cursor := QUIZKERES(:category); END;';
    $stmt = oci_parse($conn, $sql);

    // Bind the input parameter
    oci_bind_by_name($stmt, ':category', $category);

    // Bind the output parameter into a cursor
    $cursor = oci_new_cursor($conn);
    oci_bind_by_name($stmt, ':cursor', $cursor, -1, OCI_B_CURSOR);

    oci_execute($stmt);

    // Execute and fetch the cursor
    oci_execute($cursor);

    $result = [];
    oci_fetch_all($cursor, $result);

    oci_free_statement($stmt);
    oci_free_cursor($cursor);

    return $result;
}

function check_answer(string $kerdes, string $valasz): bool {
    global $conn;
    $sql = "SELECT * FROM KERDES WHERE kerdes = :kerdes AND jo_valasz = :valasz";
    $stmt = oci_parse($conn, $sql);

    // Bind all parameters
    oci_bind_by_name($stmt, ":kerdes", $kerdes);
    oci_bind_by_name($stmt, ":valasz", $valasz);

    oci_execute($stmt);
    return oci_fetch($stmt);
}

function login(string $email, string $jelszo) {
    global $conn;
    $sql = "SELECT * FROM FELHASZNALO WHERE EMAIL = :email";
    $stmt = oci_parse($conn, $sql);

    // Bind the input parameter
    oci_bind_by_name($stmt, ":email", $email);
    oci_execute($stmt);
    $result = oci_fetch_array($stmt);

    if (!$result || !isset($result["JELSZO"])) {
        // Nincs felhasznalo.
        return null;
    }

    if (password_verify($jelszo ,$result["JELSZO"])) {
        return new Account(
            intval($result["FELHASZNALO_ID"]),
            $result["EMAIL"],
            $result["KERESZTNEV"],
            $result["VEZETEKNEV"],
            intval($result["ELETKOR"]),
            intval($result["OSSZPONTSZAM"])
        );
    } else {
        return null;
    }
}

function register(string $email, string $jelszo, string $vez, string $kereszt, string $eletkor): bool {
    global $conn;
    // Succes gives 1 otherwise 0
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
    return boolval($result);
}

// Change password, based on account email, if valid
function change_password(Account $account, string $old_pswd , string $new_pswd): array {
    global $conn;
    $error = array();
    $sql = "SELECT JELSZO FROM FELHASZNALO WHERE EMAIL = :email";
    $stmt = oci_parse($conn, $sql);

    // Bind the input parameter
    $email = $account->getEmail();
    oci_bind_by_name($stmt, ":email", $email);
    oci_execute($stmt);
    $result = oci_fetch_array($stmt);
    oci_free_statement($stmt);

    if (!$result || !isset($result["JELSZO"])) {
        $error[] = "Nincs ilyen felhasznaló";
        return $error;
    }


    if ($old_pswd === "") {
        $error[] = "Kérlek add meg a jelszavad";
        return $error;
    }
    if ($new_pswd === "") {
        $error[] = "Nincs új jelszó";
        return $error;
    }
    if (!password_verify($old_pswd ,$result["JELSZO"])) {
        $error[] = "Hibás jelszó";
        return $error;
    }
    if ($old_pswd === $new_pswd) {
        $error[] = "A két jelszó nem egyezhet";
        return $error;
    }

    $sql = "UPDATE FELHASZNALO SET JELSZO = :pswd WHERE :email = EMAIL";
    $stmt = oci_parse($conn, $sql);

    // Bind the input parameter
    $email = $account->getEmail();
    $new_pswd = password_hash($new_pswd, PASSWORD_BCRYPT);
    oci_bind_by_name($stmt, ":pswd", $new_pswd);
    oci_bind_by_name($stmt, ":email", $email);
    oci_execute($stmt);
    oci_free_statement($stmt);

    return $error;
}

function edit_account(Account $account,
                      string $email = "",
                      string $firstname = "",
                      string $lastname = "",
                      int $age = 0): bool {
    $edit = array();

    global $conn;

    if ($email !== "") {
        $account->setEmail($email);
    }
    if ($firstname !== "") {
        $account->setFirstName($firstname);
    }
    if ($lastname !== "") {
        $account->setLastName($lastname);
    }
    if ($age !== 0) {
        $account->setAge($age);
    }

    $edit[] = $account->getEmail();
    $edit[] = $account->getFirstName();
    $edit[] = $account->getLastName();
    $edit[] = $account->getAge();
    $edit[] = $account->getId();

    $sql = "UPDATE FELHASZNALO SET
            EMAIL = :edit0,
            KERESZTNEV = :edit1,
            VEZETEKNEV = :edit2,
            ELETKOR = :edit3
            WHERE FELHASZNALO_ID = :edit4";
    $stid = oci_parse($conn,$sql);

    oci_bind_by_name($stid, ":edit0", $edit[0]);
    oci_bind_by_name($stid, ":edit1", $edit[1]);
    oci_bind_by_name($stid, ":edit2", $edit[2]);
    oci_bind_by_name($stid, ":edit3", $edit[3]);
    oci_bind_by_name($stid, ":edit4", $edit[4]);


    oci_execute($stid);
    oci_free_statement($stid);

    return true;
}


function create_quiz(Account $account, string $quiz_name): void
{
    global $conn;

    $account_id = $account->getId();
    $sql = "INSERT INTO QUIZ VALUES(SEQ_QUIZ.NEXTVAL, :qname, :r, :k)";
    $stid = oci_parse($conn,$sql);

    $r = 0;
    $k = 0;
    oci_bind_by_name($stid, ":qname", $quiz_name);
    oci_bind_by_name($stid, ":r", $r);
    oci_bind_by_name($stid, ":k", $k);
    oci_execute($stid);
    oci_free_statement($stid);

    $sql = "INSERT INTO KESZIT VALUES(:felh, SEQ_QUIZ.CURRVAL, TRUNC(SYSDATE))";
    $stid = oci_parse($conn,$sql);

    oci_bind_by_name($stid, ":felh", $account_id);
    oci_execute($stid);
    oci_free_statement($stid);
}

function create_question(string $question, string $jo_valasz): void {
    global $conn;
    $sql = "INSERT INTO KERDES VALUES(SEQ_KERDES.NEXTVAL, SEQ_QUIZ.CURRVAL, :kerdes, :jo_valasz)";
    $stid = oci_parse($conn,$sql);

    oci_bind_by_name($stid, ":kerdes", $question);
    oci_bind_by_name($stid, ":jo_valasz", $jo_valasz);
    oci_execute($stid);
    oci_free_statement($stid);
}

function create_valasz(string $valasz, string $qstring): void {
    global $conn;
    $sql = "SELECT KERDES_ID FROM KERDES WHERE KERDES = :qstring";
    $stid = oci_parse($conn,$sql);

    oci_bind_by_name($stid, ":qstring", $qstring);
    oci_execute($stid);
    $id = intval(oci_fetch_array($stid)["KERDES_ID"]);
    oci_free_statement($stid);

    $sql = "INSERT INTO ROSSZVALASZ VALUES(:id,:valasz)";
    $stid = oci_parse($conn,$sql);

    oci_bind_by_name($stid, ":id", $id);
    oci_bind_by_name($stid, ":valasz", $valasz);
    oci_execute($stid);
    oci_free_statement($stid);
}

function update_quiz(string $qname, int $qid): void {
    global $conn;

    $sql = "UPDATE QUIZ SET QUIZ_NEV = :qname WHERE QUIZ_ID = :qid";
    $stid = oci_parse($conn,$sql);

    oci_bind_by_name($stid, ":qname", $qname);
    oci_bind_by_name($stid, ":qid", $qid);
    oci_execute($stid);
    oci_free_statement($stid);
}

function update_question(int $qid, string $question, string $jo_valasz, string $question_old): void {
    global $conn;
    $sql = "UPDATE KERDES SET KERDES = :kerdes, JO_VALASZ = :jo_valasz
            WHERE QUIZ_ID = :qid AND KERDES = :kerdes_old";
    $stid = oci_parse($conn,$sql);

    oci_bind_by_name($stid, ":kerdes", $question);
    oci_bind_by_name($stid, ":kerdes_old", $question_old);
    oci_bind_by_name($stid, ":jo_valasz", $jo_valasz);
    oci_bind_by_name($stid, ":qid", $qid);
    oci_execute($stid);
    oci_free_statement($stid);
}

function update_valasz(string $valasz, string $valasz_old, string $qstring): void {
    global $conn;
    $sql = "
    UPDATE ROSSZVALASZ SET
        ROSSZ_VALASZ = :valasz
    WHERE KERDES_ID = (
    SELECT KERDES_ID FROM KERDES WHERE KERDES.KERDES = :qstring
    ) AND ROSSZ_VALASZ = :valasz_old
    ";

    $stid = oci_parse($conn,$sql);

    oci_bind_by_name($stid, ":valasz", $valasz);
    oci_bind_by_name($stid, ":valasz_old", $valasz_old);
    oci_bind_by_name($stid, ":qstring", $qstring);
    oci_execute($stid);
    oci_free_statement($stid);
}

function delete_quiz(int $qid): void {
    global $conn;
    $sql = "DELETE FROM QUIZ WHERE QUIZ_ID = :qid";
    $stid = oci_parse($conn,$sql);

    oci_bind_by_name($stid, ":qid", $qid);
    oci_execute($stid);
    oci_free_statement($stid);
}

function delete_account(Account $account): void {
    global $conn;
    $sql = "DELETE FROM FELHASZNALO WHERE FELHASZNALO_ID = :id";
    $stid = oci_parse($conn,$sql);

    $id = $account->getId();
    oci_bind_by_name($stid, ":id", $id);
    oci_execute($stid);
    oci_free_statement($stid);
    //header("Location: logout.php");
}

function kitolt(Account $account, int $pont, int $qid): void {
    global $conn;
    $sql = "INSERT INTO KITOLT VALUES(:fid, :qid, TRUNC(SYSDATE), :pont)";

    $stid = oci_parse($conn, $sql);

    $id = $account->getId();
    oci_bind_by_name($stid, ":fid", $id);
    oci_bind_by_name($stid, ":qid", $qid);
    oci_bind_by_name($stid, ":pont", $pont);
    oci_execute($stid);
    oci_free_statement($stid);

    $sql = "
    UPDATE FELHASZNALO SET
        OSSZPONTSZAM = OSSZPONTSZAM + :pont
    WHERE :fid = FELHASZNALO_ID
    ";

    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ":fid", $id);
    oci_bind_by_name($stid, ":pont", $pont);
    oci_execute($stid);
    oci_free_statement($stid);

    $sql = "
    UPDATE QUIZ SET KITOLTES_SZAM = KITOLTES_SZAM + 1 WHERE QUIZ_ID = :qid
    ";

    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ":qid", $qid);
    oci_execute($stid);
    oci_free_statement($stid);
}

function get_leaderboard(): array {
    global $conn;
    $sql = "SELECT VEZETEKNEV, KERESZTNEV, OSSZPONTSZAM, COUNT(K.KITOLTES_PONTSZAM) AS KITOLTESEK FROM FELHASZNALO
    INNER JOIN KITOLT K on FELHASZNALO.FELHASZNALO_ID = K.FELHASZNALO_ID
    GROUP BY OSSZPONTSZAM, KERESZTNEV, VEZETEKNEV
    ORDER BY OSSZPONTSZAM DESC";

    $stid = oci_parse($conn, $sql);
    oci_execute($stid);

    $result = array();
    oci_fetch_all($stid, $result, 0, -1, OCI_FETCHSTATEMENT_BY_ROW);
    oci_free_statement($stid);

    return $result;
}

function get_leaderboard_wavg(): array {
    global $conn;
    $sql = "SELECT
        VEZETEKNEV,
        KERESZTNEV,
        ROUND(SUM(K.KITOLTES_PONTSZAM) / COUNT(*), 2) AS SULYOZOTT_ATLAG,
        COUNT(*) AS KITOLTESEK
    FROM
        FELHASZNALO
            INNER JOIN KITOLT K ON FELHASZNALO.FELHASZNALO_ID = K.FELHASZNALO_ID
    GROUP BY
        VEZETEKNEV,
        KERESZTNEV
    ORDER BY
        SULYOZOTT_ATLAG DESC";

    $stid = oci_parse($conn, $sql);
    oci_execute($stid);

    $result = array();
    oci_fetch_all($stid, $result, 0, -1, OCI_FETCHSTATEMENT_BY_ROW);
    oci_free_statement($stid);

    return $result;
}

function get_quiz_leaderboard(int $qid): array {
    global $conn;
    $sql = "SELECT VEZETEKNEV, KERESZTNEV, MAX(K.KITOLTES_PONTSZAM) AS PONTSZAM FROM FELHASZNALO
    INNER JOIN KITOLT K ON FELHASZNALO.FELHASZNALO_ID = K.FELHASZNALO_ID
    WHERE K.QUIZ_ID = :qid
    group by VEZETEKNEV, KERESZTNEV
    ORDER BY PONTSZAM DESC";

    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ":qid", $qid);
    oci_execute($stid);

    $result = array();
    oci_fetch_all($stid, $result, 0, -1, OCI_FETCHSTATEMENT_BY_ROW);
    oci_free_statement($stid);
    return $result;
}

