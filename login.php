<?php
$error = array();

if (isset($_POST["login"])) {
    if (!isset($_POST["pswd"]) || trim($_POST["pswd"]) === "") {
        $error[] = "Jelszó megadása kötelező";
    }
    if (!isset($_POST["email"]) || trim($_POST["email"]) === "") {
        $error[] = "E-mail cím megadása kötelező";
    }

    if (count($error) == 0) {
        if (login($_POST["email"], $_POST["pswd"])) {
            $_SESSION["felhasznalo"] = true;
        } else {
            $error[] = "Hibás jelszó, email páros";
        }
    }
}
if (count($error) > 0) {
    $_SESSION["error"] = $error;
}