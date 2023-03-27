<?php
session_start();

include "database.php";


if (isset($_POST["login"])) {
    if (!isset($_POST["pswd"]) || trim($_POST["pswd"]) === "") {
    } else {
        if (login($_POST["email"], $_POST["pswd"])) {
        }
    }
}
$quizes = null;
if (isset($_GET["category"]) and $_GET["category"] !== "") {
    $quizes = explode(";__S__;", getQuizList($_GET["category"]));
    array_shift($quizes);
} else {
    $quizes = get_all_quiz();
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <title>Főoldal</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
</head>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Főoldal</a>
                    </li>
                    <?php if (isset($_SESSION["admin"])) {?>
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="admin.php">Admin</a>
                        </li>
                    <?php } ?>
                </ul>
                <?php if (isset($_SESSION["admin"])) {?>
                    <ul class="navbar-nav justify-content-end">
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Kijelentkezés</a>
                        </li>
                    </ul>
                <?php } else { ?>
                    <ul class="navbar-nav justify-content-end">
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="offcanvas" href="#offcanvasRight" role="button" aria-controls="offcanvasRight">
                                Bejelentkezés
                            </a>
                        </li>
                    </ul>
                <?php } ?>
                <form class="d-flex" role="search" method="get" action="index.php">
                    <input class="form-control me-2" type="text" placeholder="Kategória" name="category" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Keresés</button>
                </form>
            </div>
        </div>
    </nav>
</header>
<body class="bg-black text-white">
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.1/dist/jquery.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>

<div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasRightLabel">Bejelentkezés</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body text-dark">
        <form action="index.php" method="post">
            <div class="form-floating">
                <input class="form-control" type="email" id="email" name="email" placeholder="E-mail"/>
                <label for="email">E-mail</label>
            </div>
            <div class="form-floating">
                <input class="form-control" type="password" id="pswd" name="pswd" placeholder="Password"/>
                <label for="pswd">Password</label>
            </div>
            <div class="text-center m-3">
                <button type="submit" class="btn btn-primary" name="login">Bejelentkezés</button>
            </div>
        </form>
    </div>
</div>

<?php if (!isset($_GET["src-name"]) || !isset($_GET["src-rate"])) { ?>
    <h2 class="text-center m-4">Üdvözöljük a IMClone -on !</h2>

    <div class="container">
        <div class="row justify-content-center">
            <?php
            for($i = 0; $i < count($quizes); $i++) { ?>
                <div class="col-auto me-1 card bg-dark m-3">
                    <div class="card-body text-center">
                        <a class="stretched-link" href="<?php echo 'kitolt.php?quiz=' . $quizes[$i]; ?>"></a>
                        <h5 class="card-title h5 "><?php echo $quizes[$i] ?></h5>
                    </div>
                </div>
            <?php }?>
        </div>
    </div>

<?php } else {?>
    <h2 class="text-center m-4">Keresési találatok:</h2>
    <h6 class="text-center"> Találatok száma: <?php echo count($quizes) -1; ?></h6>
    <div class="container">
        <div class="row justify-content-center">
            <?php
            for($i = 0; $i < count($quizes); $i++) { ?>
                <div class="col-auto me-1 card bg-dark m-3">
                    <div class="card-body text-center">
                        <a class="stretched-link" href="<?php echo 'kitolt.php?quiz=' . $quizes[$i]; ?>"></a>
                        <h5 class="card-title h5 "><?php echo $quizes[$i] ?></h5>
                    </div>
                </div>
            <?php }?>
        </div>
    </div>
<?php }?>
</body>
</html>
