<?php
include "database.php";
session_start();

$quizes_kerdes = null;
$quiz_nev = null;
$quiz_id = null;
if (isset($_GET["quiz"]) && $_GET["quiz"] !== "") {
    $quizes_kerdes = get_quiz_questions_answers(intval($_GET["quiz"]));
    $quiz_nev = $_GET["qname"];
    $quiz_id = $_GET["quiz"];
}
if (isset($_POST["quiz"]) && $_POST["quiz"] !== "") {
    $quizes_kerdes = get_quiz_questions_answers(intval($_POST["quiz"]));
    $quiz_nev = $_POST["qname"];
    $quiz_id = $_POST["quiz"];
}

$account = null;
if (isset($_SESSION["felhasznalo"])) {
    $account = $_SESSION["felhasznalo"];
}



$checking = array();
$valasz = null;
$pont = 0;
if (isset($_POST["fill"])) {
    for($i = 0; $i < count($quizes_kerdes); $i++) {
        $kerdes = $quizes_kerdes[$i][0];
        $valasz = $_POST[str_replace(" ", "_",$quizes_kerdes[$i][0])];
        $result = check_answer($kerdes, $valasz);

        $checking[] = [$valasz, $result];
        if ($result) {
            $pont++;
        }
    }
    if ($account !== null) {
        echo $quiz_id;
        kitolt($account, $pont, intval($quiz_id));
    }
}

?>

<!DOCTYPE html>
<html lang="hu" data-bs-theme="dark">
<head>
    <?php if ((isset($_GET["quiz"]) && $_GET['quiz'] !== "") || (isset($_POST["quiz"]) && $_POST["quiz"] !== "")) {?>
        <title><?php echo $quiz_nev; ?></title>
    <?php } else { ?>
        <title>No page</title>
    <?php } ?>
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

<?php if ((isset($_GET["quiz"]) && $_GET['quiz'] !== "") || (isset($_POST["quiz"]) && $_POST["quiz"] !== "")) {?>
    <h2 class="text-center m-4"><?php echo $quiz_nev; ?></h2>
    <h3 class="text-center m-4"><?php echo $pont. " / " .count($quizes_kerdes); ?></h3>
    <form class="container" action="kitolt.php" method="post">
        <?php
        for($i = 0; $i < count($quizes_kerdes); $i++) {

            if (isset($_POST["fill"])) {
                $valasz = $checking[$i][0];
                $check = $checking[$i][1];
            }
        ?>
            <div class="row justify-content-center">
                <div class="col-sm card mb-3 <?php
                if (count($checking) !== 0 && (isset($check) && $check === true)) {
                    echo "border-success";
                } elseif (isset($_POST["fill"])) {
                    echo "border-danger";
                }
                ?>">
                    <div class="card-body <?php
                    if (count($checking) !== 0 && (isset($check) && $check === true)) {
                        echo "text-success";
                    } elseif (isset($_POST["fill"])) {
                        echo "text-danger";
                    }
                    ?>">
                        <h5 class="card-title h5 "><?php echo $quizes_kerdes[$i][0];?></h5>

                        <div class="form-check">
                            <input
                                class="form-check-input"
                                value="default-void"
                                type="radio"
                                <?php
                                if ($valasz === "default-void" || !isset($_POST["fill"])) {
                                    echo "checked";
                                }
                                ?>
                                name="<?php echo $quizes_kerdes[$i][0];?>"
                                id="<?php echo $i;?>-default-void-label"
                            >
                            <label class="form-check-label" for="<?php echo $i;?>-default-void-label">
                                nincs válasz
                            </label>
                        </div>

                        <?php for($j = 0; $j < count($quizes_kerdes[$i][2]); $j++) { ?>
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    value="<?php echo $quizes_kerdes[$i][2][$j];?>"
                                    type="radio"
                                    <?php
                                    if ($valasz !== null && $valasz === $quizes_kerdes[$i][2][$j]) {
                                        echo "checked";
                                    }
                                    ?>
                                    name="<?php echo $quizes_kerdes[$i][0];?>"
                                    id="<?php echo $j . $i;?>-label"
                                >
                                <label class="form-check-label" for="<?php echo $j . $i;?>-label">
                                    <?php echo $quizes_kerdes[$i][2][$j];?>
                                </label>
                            </div>
                        <?php }?>
                    </div>
                </div>
            </div>
            <?php }?>
        <input type="hidden" name="quiz" value="<?php echo $quiz_id; ?>">
        <input type="hidden" name="qname" value="<?php echo $quiz_nev; ?>">
        <button type="submit" class="btn btn-primary mb-3" name="fill">Kitöltés befejezése.</button>
    </form>
<?php } else { ?>
    <h1 class="text-center m-4" >MŰVELET NEM VÉGRE HAJTHATÓ.</h1>
<?php } ?>
</body>
</html>