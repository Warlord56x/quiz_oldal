<?php
global $error;

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
    if (count($error) === 0) {
        $regist_result = register($_POST["email"], $_POST["pswd"], $_POST["vez"], $_POST["ker"], $_POST["kor"]);
        if ($regist_result) {
            $felh = login($_POST["email"], $_POST["pswd"]);
            $_SESSION["felhasznalo"] = $felh;
        } else {
            $error[] = "A megadott e-mail címen már létezik egy felhasználó";
        }
    }
    $error[] = "Regist error";
}
$_SESSION["error"] = $error;
