<?php
session_start();

include "database.php";

$ranks = get_leaderboard();

$ranks_avg = get_leaderboard_wavg();

$temp = get_all_quiz();
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

    <div class="container text-center mt-4 mb-3">
        <form action="rangsor.php" method="get">
            <h4>Quizek</h4>
            <select class="form-select mb-3" name="qid" aria-label="quiz-select">
                <option selected>Quiz szelekció</option>
                <?php foreach ($quizes as $index => $quiz) { ?>
                <option
                    value="<?php echo $quizes_ids[$index];?>"
                    <?php if (isset($_GET["rang"]) && $_GET["qid"] === $quizes_ids[$index]) { echo "selected";}?>
                >
                    <?php echo $quiz;?>
                </option>
                <?php } ?>
            </select>
            <button class="btn btn-primary" type="submit" name="rang">Rangsor kérése</button>
        </form>
    </div>

    <div class="container">
    <?php
    if (isset($_GET["rang"])) {
        $qranks = get_quiz_leaderboard(intval($_GET["qid"]));
    ?>
        <div class="list-group container">
            <?php foreach ($qranks as $index => $rank) { ?>
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h4 class="mb-1"><?php echo $rank["VEZETEKNEV"]." ".$rank["KERESZTNEV"];?></h4>
                        <h5>Rang: <?php echo $index+1;?></h5>
                    </div>
                    <p class="mb-1">Pontszám: <?php echo $rank["PONTSZAM"];?></p>
                </div>
            <?php } ?>
        </div>

    <?php } ?>
    </div>

    <h3 class="text-center m-4">Rang sorrend összpontszám szerint</h3>
    <div class="list-group container">
        <?php foreach ($ranks as $index => $rank) { ?>
        <div class="list-group-item">
            <div class="d-flex w-100 justify-content-between">
                <h4 class="mb-1"><?php echo $rank["VEZETEKNEV"]." ".$rank["KERESZTNEV"];?></h4>
                <h5>Rang: <?php echo $index+1;?></h5>
            </div>
            <p class="mb-1">Kitöltések száma: <?php echo $rank["KITOLTESEK"];?></p>
            <p class="mb-1">Összpontszám: <?php echo $rank["OSSZPONTSZAM"];?></p>
        </div>
        <?php } ?>
    </div>


    <div class="text-center m-4">
        <h3>Rang sorrend pontszám szerint </h3>
        <small>(összpontszám / kitöltésszám)</small>
    </div>
    <div class="list-group container">
        <?php foreach ($ranks_avg as $index => $rank) { ?>
            <div class="list-group-item">
                <div class="d-flex w-100 justify-content-between">
                    <h4 class="mb-1"><?php echo $rank["VEZETEKNEV"]." ".$rank["KERESZTNEV"];?></h4>
                    <h5>Rang: <?php echo $index+1;?></h5>
                </div>
                <p class="mb-1">Kitöltések száma: <?php echo $rank["KITOLTESEK"];?></p>
                <p class="mb-1">Pontszám: <?php echo $rank["SULYOZOTT_ATLAG"];?></p>
            </div>
        <?php } ?>
    </div>

</body>
</html>
