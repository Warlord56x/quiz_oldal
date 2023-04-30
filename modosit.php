<?php
include "database.php";
session_start();

if (!isset($_SESSION["felhasznalo"])) {
    header("Location: index.php");
}

$account = $_SESSION["felhasznalo"];

if (isset($_GET["delete"])) {
    delete_quiz($_GET["del"]);
}

$temp = get_my_quizes($account);
$quizes = $temp["QUIZ_NEV"];
$quizes_ids = $temp["QUIZ_ID"];

if (isset($_GET["update"])) {

    $kerdes_size = $_GET["count_k"];
    $valasz_size = json_decode($_GET["count_v"], true);

    $valaszok = array();
    $jo_valaszok = array();
    $kerdesek = array();
    foreach ($valasz_size as $index => $key) {
        for ($j = 1; $j <= $key; $j++) {
            $valaszok[$_GET["qname".intval($index)]][] = [$_GET[$index . "_valasz" . $j], $_GET[$index . "_valasz" . $j ."regi"]];
        }
    }

    for ($i = 1; $i <= intval($kerdes_size); $i++) {
        $kerdesek[] = [$_GET["qname".$i], $_GET["qname".$i."regi"]];
        $jo_valaszok[] = $_GET["qname".$i."jo"];
    }

    /*
    print_r($_GET); echo "<br>";
    print_r($jo_valaszok); echo "<br>";
    print_r($valaszok); echo "<br>";
    print_r($kerdesek); echo "<br>";
    */

    update_quiz($_GET["quiz_name"], intval($_GET["qid"]));

    foreach ($kerdesek as $index => $item) {
        //echo $index; echo "<br>";
        //echo $item; echo "<br>";
        update_question(intval($_GET["qid"]), $item[0], $jo_valaszok[$index], $item[1]);
    }

    $i = 1;
    foreach ($valaszok as $kerdes_osztas) {
        //print_r($kerdes_osztas); echo "<br>";
        foreach ($kerdes_osztas as $index => $item) {
            if (in_array($item, $jo_valaszok)) {
                continue;
            }
            update_valasz($item[0], $item[1], $_GET["qname".($i)]);
            //echo $index; echo "<br>";
            //print_r($item); echo "<br>";
        }
        $i++;
    }
}


if (isset($_GET["quiz"]) && $_GET["quiz"] !== "") {
    $quizes_kerdes = get_quiz_questions_answers($_GET["quiz"]);
}
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
<div class="container mt-4" id="page">
    <h2 class="text-center m-4">Módosítás</h2>

    <div class="container">
        <div class="row justify-content-center">
            <?php for($i = 0; $i < count($quizes); $i++) { ?>
                <div class="col-auto me-1 card m-3">
                    <div class="card-body text-center">
                        <a class="stretched-link" href="<?php echo 'modosit.php?quiz='.$quizes_ids[$i].'&qname='.$quizes[$i]; ?>"></a>
                        <h5 class="card-title h5 "><?php echo $quizes[$i] ?></h5>
                    </div>
                </div>
            <?php }?>
        </div>
    </div>
</div>

<?php if (isset($_GET["qname"]) && $_GET["qname"] !== "") {?>
<div class="container mt-4" id="page">
    <form action="modosit.php" method="get">
        <div class="container" id="quiz">
            <div class="mb-3">
                <label for="quiz_name" class="form-label">Quiz név</label>
                <input type="text"
                       class="form-control"
                       id="quiz_name"
                       name="quiz_name"
                       placeholder="Példa quiz"
                       value="<?php echo $_GET["qname"];?>"
                >
            </div>

        </div>
        <div id="counts" class="mb-3">

            <input type="hidden" value="<?php echo $_GET["quiz"];?>" name="qid">
            <button type="submit" class="btn btn-primary" name="update">Frissít</button>
        </div>
    </form>
    <form action="modosit.php" method="get">
        <input type="hidden" value="<?php echo $_GET["quiz"];?>" name="del">
        <button type="submit" class="btn btn-danger" name="delete">Törlés</button>
    </form>
</div>

<script>
    let kerdesek = 0;
    let valaszok = {
        '1kerdes' : 0,
    }

    const quiz = document.getElementById("quiz");

    const valasz = document.createElement('li');
    valasz.className = 'list-group-item';
    valasz.id = '1kerdes_valasz1';



    const add_valasz = (kerdes, kvalasz) => {
        const valasz_list = document.getElementById(kerdes+"kerdes_valasz_list");

        valaszok[kerdes+'kerdes'] += 1;
        const clone = valasz.cloneNode(true);
        clone.innerHTML = `
    <div class="input-group">
        <span class="input-group-text">`+valaszok[kerdes+'kerdes'] + '. Válasz'+`</span>
        <input type="text" class="form-control" name="`+kerdes+'kerdes_valasz' + valaszok[kerdes+'kerdes']+`" value="`+kvalasz+`">
        <input type="hidden" class="form-control" name="`+kerdes+'kerdes_valasz' + valaszok[kerdes+'kerdes']+'regi'+`" value="`+kvalasz+`">
    </div>
`;

        //const radio = clone.querySelector('input[name="'+ kerdes +'radio"][value="'+ valaszok[kerdes+'kerdes'] +'"]');
        //radio.checked = helyes;
        valasz_list.append(clone);
        update_counts();
    }

    const remove_valasz = (kerdes) => {
        if (valaszok[kerdes+'kerdes'] !== 1) {
            valaszok[kerdes+'kerdes'] -= 1;
        } else {
            return
        }
        const valasz_list = document.getElementById(kerdes+"kerdes_valasz_list");

        const last_element = valasz_list.lastChild;
        valasz_list.removeChild(last_element);
        update_counts();
    }

    const add_kerdes = (name, jo_valasz) => {
        kerdesek += 1;
        const main_kerdes = document.createElement("div");
        main_kerdes.className = 'container justify-content-center mb-3';
        main_kerdes.id = kerdesek+'kerdes';
        main_kerdes.innerHTML = `
            <div class="mb-3">
                <div class="input-group mb-3">
                    <span class="input-group-text">Kérdés</span>
                    <input type="text" class="form-control" name="qname`+kerdesek+`" placeholder="Példa kérdés" value="`+name+`">
                    <input type="hidden" class="form-control" name="qname`+kerdesek+`regi" placeholder="Példa kérdés" value="`+name+`">
                </div>
                <div class="input-group">
                    <span class="input-group-text">Jó válasz</span>
                    <input type="text" class="form-control" id="qname`+kerdesek+`" name="qname`+kerdesek+`jo" placeholder="Példa kérdés" value="`+jo_valasz+`">
                </div>
            </div>
            <ul class="list-group mb-2" id="`+kerdesek+`kerdes_valasz_list">
            </ul>
        `;
        valaszok[kerdesek+'kerdes'] = 0;
        quiz.append(main_kerdes);
        update_counts();
    }

    const remove_kerdes = () => {
        if (kerdesek !== 1) {
            kerdesek -= 1;
        } else {
            return
        }
        const last_element = quiz.lastChild;
        quiz.removeChild(last_element);
        update_counts();
    }

    const update_counts = () => {
        const counts = document.getElementById("counts");
        if (counts.childElementCount > 2) {
            let last_element = counts.lastChild;
            counts.removeChild(last_element);

            last_element = counts.lastChild;
            counts.removeChild(last_element);
        }
        const count_v = document.createElement("input");
        count_v.type = "hidden";
        count_v.name = "count_v";
        count_v.value = JSON.stringify(valaszok);

        const count_k = document.createElement("input");
        count_k.type = "hidden";
        count_k.name = "count_k";
        count_k.value = kerdesek;
        counts.append(count_v);
        counts.append(count_k);
    }

    <?php
    foreach ($quizes_kerdes as $index => $item) {
        $j = $item[2];
        echo "add_kerdes('$item[0]', '$j[0]');";
        foreach ($item[2] as $index2 => $item2) {
            $i = $index +1;
            if ($index2 === 0) {
                continue;
            } else {
                echo "add_valasz('$i', '$item2');";
            }
        }
    }
    ?>

</script>
<?php } ?>


</body>
</html>
