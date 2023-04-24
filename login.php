<?php
global $error;

if (isset($_POST["login"])) {
    if (!isset($_POST["pswd"]) || trim($_POST["pswd"]) === "") {
        $error[] = "Jelszó megadása kötelező";
    }
    if (!isset($_POST["email"]) || trim($_POST["email"]) === "") {
        $error[] = "E-mail cím megadása kötelező";
    }

    if (count($error) == 0) {
        $felh = login($_POST["email"], $_POST["pswd"]);
        if ($felh !== new Account()) {
            $_SESSION["felhasznalo"] = $felh;
        } else {
            $error[] = "Hibás jelszó, email páros";
        }
    }
    $error[] = "Login error";
}
$_SESSION["error"] = $error;