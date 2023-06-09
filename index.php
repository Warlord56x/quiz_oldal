<?php
session_start();

include "database.php";
$error = array();

$quizes = null;
$quizes_ids = null;
$temp = null;
if (isset($_GET["category"]) and $_GET["category"] !== "") {
    $temp = get_quiz_list($_GET["category"]);
} else {
    $temp = get_all_quiz();
    //print_r($temp);
}
$quizes = $temp["QUIZ_NEV"];
$quizes_ids = $temp["QUIZ_ID"];

?>

<!DOCTYPE html>
<html lang="hu" data-bs-theme="dark">
<head>
    <title>Főoldal</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
</head>
<body class="bg-black text-white">
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.1/dist/jquery.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>

<?php
include_once "nav.php";
?>

<?php if (!isset($_GET["src-name"]) || !isset($_GET["src-rate"])) { ?>
    <h2 class="text-center m-4">Üdvözöljük a Quiz oldalon!</h2>

    <div class="container">
        <div class="row justify-content-center">
            <?php for($i = 0; $i < count($quizes); $i++) { ?>
                <div class="col-auto me-1 card m-3">
                    <div class="card-body text-center">
                        <a class="stretched-link" href="<?php echo 'kitolt.php?quiz='.$quizes_ids[$i].'&qname='.$quizes[$i]; ?>"></a>
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
                <div class="col-auto me-1 card m-3">
                    <div class="card-body text-center">
                        <a class="stretched-link" href="<?php echo 'kitolt.php?quiz=' . $quizes_ids[$i]; ?>"></a>
                        <h5 class="card-title h5 "><?php echo $quizes[$i] ?></h5>
                    </div>
                </div>
            <?php }?>
        </div>
    </div>
<?php }?>
</body>
</html>
