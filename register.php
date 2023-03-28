<?php
$error = array();

if (isset($_POST["register"])) {
    if (!isset($_POST["pswd"]) || trim($_POST["pswd"]) === "") {
        $error[] = "Jelszó megadása kötelező";
    }
    if (!isset($_POST["email"]) || trim($_POST["email"]) === "") {
        $error[] = "E-mail cím megadása kötelező";
    }
    if (!isset($_POST["vez"]) || trim($_POST["vez"]) === "") {
        $error[] = "Vezetéknév megadása kötelező";
    }
    if (!isset($_POST["ker"]) || trim($_POST["ker"]) === "") {
        $error[] = "Keresztnév megadása kötelező";
    }
    if (!isset($_POST["kor"]) || trim($_POST["kor"]) === "") {
        $error[] = "Életkor megadása kötelező";
    }
    if (count($error) == 0) {
        if (register($_POST["email"], $_POST["pswd"], $_POST["vez"], $_POST["ker"], $_POST["kor"])) {
            $_SESSION["felhasznalo"] = true;
        } else {
            $error[] = "A megadott e-mail címen már létezik egy felhasználó";
        }
    }
}
if (count($error) > 0) {
    $_SESSION["error"] = $error;
}
