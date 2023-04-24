<?php

// Ma favorite stuff
declare(strict_types=1);

require_once "account.php";

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

    oci_free_statement($stid);
    return $result;
}

function get_quiz_list(string $category = ""):array {
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
    oci_fetch_all($cursor, $result, 0, -1, OCI_FETCHSTATEMENT_BY_COLUMN);

    oci_free_statement($stmt);
    oci_free_cursor($cursor);

    return $result;
}

function check_answer(string $kerdes, string $valasz):bool {
    global $conn;
    $sql = "SELECT * FROM KERDES WHERE kerdes = :kerdes AND jo_valasz = :valasz";
    $stmt = oci_parse($conn, $sql);

    // Bind all parameters
    oci_bind_by_name($stmt, ":kerdes", $kerdes);
    oci_bind_by_name($stmt, ":valasz", $valasz);

    oci_execute($stmt);
    return oci_fetch($stmt);
}

function login(string $email, string $jelszo):Account {
    global $conn;
    $sql = "SELECT * FROM FELHASZNALO WHERE EMAIL = :email";
    $stmt = oci_parse($conn, $sql);

    // Bind the input parameter
    oci_bind_by_name($stmt, ":email", $email);
    oci_execute($stmt);
    $result = oci_fetch_array($stmt);

    if (!$result || !isset($result["JELSZO"])) {
        // Nincs felhasznalo.
        return new Account();
    }

    if (password_verify($jelszo ,$result["JELSZO"])) {
        return new Account(
            $result["EMAIL"],
            $result["KERESZTNEV"],
            $result["VEZETEKNEV"],
            intval($result["ELETKOR"]),
            intval($result["OSSZPONTSZAM"])
        );
    } else {
        return new Account();
    }
}

function register(string $email, string $jelszo, string $vez, string $kereszt, string $eletkor):bool {
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
function change_password(Account $account, string $old_pswd , string $new_pswd):array {
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

function edit_account(Account $account, string $email = "", string $firstname = "", string $lastname = "", int $age = 0):bool {
    $edit = array();

    global $conn;
    $sql = "SELECT FELHASZNALO_ID FROM FELHASZNALO WHERE EMAIL=:email";
    $stid = oci_parse($conn,$sql);
    $email1 = $account->getEmail();
    oci_bind_by_name($stid, ":email", $email1);
    oci_execute($stid);

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
    $edit[] = intval(oci_fetch_array($stid)["FELHASZNALO_ID"]);
    oci_free_statement($stid);

    $sql = "UPDATE FELHASZNALO SET
            EMAIL = :edit0,
            KERESZTNEV = :edit1,
            VEZETEKNEV = :edit2,
            ELETKOR = :edit3
            WHERE FELHASZNALO_ID = :edit4";
    $stid = oci_parse($conn,$sql);
    //var_dump($edit);

    oci_bind_by_name($stid, ":edit0", $edit[0]);
    oci_bind_by_name($stid, ":edit1", $edit[1]);
    oci_bind_by_name($stid, ":edit2", $edit[2]);
    oci_bind_by_name($stid, ":edit3", $edit[3]);
    oci_bind_by_name($stid, ":edit4", $edit[4]);


    oci_execute($stid);
    oci_free_statement($stid);

    return true;

}
