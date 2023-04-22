<?php

// Moved this here, should've done this sooner.
include_once "login.php";
include_once "register.php";

$err = isset($_SESSION["error"]) && count($_SESSION["error"]) !== 1;
$errLogin = $err && end($_SESSION["error"]) === "Login error";
$errRegist = $err && end($_SESSION["error"]) === "Regist error";
if (isset($_SESSION["error"]) && count($_SESSION["error"]) > 0) {
    array_pop($_SESSION["error"]);
}
?>
<header>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">
                            <i class="bi-house" style="font-size: 1.2rem;"></i>
                            Főoldal
                        </a>
                    </li>
                    <?php if (isset($_SESSION["felhasznalo"])) {?>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="keszit.php">
                                Quiz készítés
                            </a>
                        </li>
                    <?php }?>
                </ul>
                <?php if (isset($_SESSION["felhasznalo"])) {?>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi-person-circle" style="font-size: 1.2rem;"></i>
                            Saját Fiók
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php">Fiók beállítások</a></li>
                            <li><a class="dropdown-item" href="logout.php">Kijelentkezés</a></li>
                        </ul>
                    </div>
                <?php } else { ?>
                    <ul class="navbar-nav justify-content-end">
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="offcanvas" href="#offcanvasRight" role="button" aria-controls="offcanvasRight">
                                Bejelentkezés/Regisztráció
                            </a>
                        </li>
                    </ul>
                <?php } ?>
                <form class="d-flex" role="search" method="get" action="index.php">
                    <input class="form-control me-2" type="text" placeholder="Kategória" name="category" aria-label="Search" <?php if (isset($_GET["category"]) and $_GET["category"] !== "") {$_a = $_GET["category"];  echo "value='$_a'";} ?> >
                    <button class="btn btn-outline-success" type="submit">Keresés</button>
                </form>
            </div>
        </div>
    </nav>
</header>

<div class="offcanvas <?php if ($errLogin || $errRegist) {echo "show";}?> offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasRightLabel">Bejelentkezés</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="index.php" method="post">
            <div>
                <p>
                    <?php
                    if ($errLogin)
                    {
                        foreach ($_SESSION["error"] as $item) {
                            echo $item;
                        }
                    }
                    ?>
                </p>
            </div>
            <div class="form-floating">
                <input class="form-control <?php if ($errLogin) {echo "is-invalid";}?>" required type="email" id="regemail" name="email" placeholder="E-mail"/>
                <label for="regemail">E-mail</label>
            </div>
            <div class="form-floating">
                <input class="form-control <?php if ($errLogin) {echo "is-invalid";}?>" required type="password" id="regpswd" name="pswd" placeholder="Password"/>
                <label for="regpswd">Password</label>
            </div>
            <div class="text-center m-3">
                <button type="submit" class="btn btn-primary" name="login">Bejelentkezés</button>
            </div>
        </form>
        <form action="index.php" method="post">
            <div>
                <p>
                    <?php
                    if ($errRegist)
                    {
                        foreach ($_SESSION["error"] as $item) {
                            echo $item;
                        }
                    }
                    ?>
                </p>
            </div>
            <div class="form-floating">
                <input class="form-control <?php if ($errRegist) {echo "is-invalid";}?>" type="email" required id="email" name="email" placeholder="E-mail"/>
                <label for="email">E-mail</label>
            </div>
            <div class="form-floating">
                <input class="form-control <?php if ($errRegist) {echo "is-invalid";}?>" type="password" required id="pswd" name="pswd" placeholder="Jelszó"/>
                <label for="pswd">Jelszó</label>
            </div>
            <div class="form-floating">
                <input class="form-control <?php if ($errRegist) {echo "is-invalid";}?>" type="text" required id="vez" name="vez" placeholder="Vezeték név"/>
                <label for="vez">Vezeték név</label>
            </div>
            <div class="form-floating">
                <input class="form-control <?php if ($errRegist) {echo "is-invalid";}?>" type="text" required id="ker" name="ker" placeholder="Kereszt név"/>
                <label for="ker">Kereszt név</label>
            </div>
            <div class="form-floating">
                <input class="form-control <?php if ($errRegist) {echo "is-invalid";}?>" type="number" required id="kor" name="kor" placeholder="Életkor"/>
                <label for="kor">Életkor</label>
            </div>
            <div class="text-center m-3">
                <button type="submit" class="btn btn-primary" name="register">Regisztráció</button>
            </div>
        </form>
    </div>
</div>
