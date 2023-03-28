<?php
session_start();

include "database.php";

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

<body class="bg-black text-white">
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.1/dist/jquery.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>

<?php
include_once "login.php";
include_once "register.php";
include_once "nav.php";
?>

<?php if (!isset($_GET["src-name"]) || !isset($_GET["src-rate"])) { ?>
    <h2 class="text-center m-4">Üdvözöljük a Quiz oldalon!</h2>

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
