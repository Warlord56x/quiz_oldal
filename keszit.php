<?php
include "database.php";
session_start();

if (!isset($_SESSION["felhasznalo"])) {
    header("Location: index.php");
}

$account = $_SESSION["felhasznalo"];

$keszit_error = null;

if (isset($_POST["quiz_keszit"])) {

    foreach ($_POST as $index => $item) {
        //echo (($item === null || $item === ""));
        if (($item === null || $item === "")) {
            if ($index === "quiz_keszit") {
                continue;
            }
            $keszit_error = "Minden mezőt ki kell tölteni!";
        }
    }
    //print_r($_POST);
    //echo $keszit_error."asdasd";

    if ($keszit_error === null) {
        $kerdes_size = $_POST["count_k"];
        $valasz_size = json_decode($_POST["count_v"], true);

        $valaszok = array();
        $jo_valaszok = array();
        $kerdesek = array();
        foreach ($valasz_size as $index => $key) {
            for ($j = 1; $j <= $key; $j++) {
                $valaszok[$_POST["qname" . intval($index)]][] = $_POST[$index . "_valasz" . $j];
            }

        }
        for ($i = 1; $i <= intval($kerdes_size); $i++) {
            $kerdesek[] = $_POST["qname" . $i];
            $jo_valaszok[] = $valaszok[$_POST["qname" . $i]][$_POST[$i . "radio"] - 1];
        }
        /*
        print_r($_POST);
        echo "<br>";
        print_r($jo_valaszok);
        echo "<br>";
        print_r($valaszok);
        echo "<br>";
        print_r($kerdesek);
        echo "<br>";
        */

        create_quiz($account, $_POST["quiz_name"]);

        foreach ($kerdesek as $index => $item) {
            //echo $index; echo "<br>";
            //echo $item; echo "<br>";
            create_question($item, $jo_valaszok[$index]);
        }
        $i = 1;
        foreach ($valaszok as $index => $kerdes_osztas) {
            //print_r($kerdes_osztas);
            //echo "<br>";
            foreach ($kerdes_osztas as $index2 => $item) {
                if (in_array($item, $jo_valaszok)) {
                    continue;
                }
                /*
                echo $index;
                echo "<br>";
                echo $item;
                echo "<br>";
                */
                create_valasz($item, $_POST["qname" . ($i)]);
            }
            $i++;
        }
    }
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
    <?php if ($keszit_error !== null) {?>
        <h3 class="text-center m-4"><?php echo $keszit_error;?></h3>
    <?php }?>
    <form action="keszit.php" method="post">
        <div class="container" id="quiz">
            <div class="mb-3">
                <label for="quiz_name" class="form-label">Quiz név</label>
                <input type="text" class="form-control" id="quiz_name" name="quiz_name" placeholder="Példa quiz">
            </div>

        </div>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-primary" onclick="add_kerdes()">Kérdés hozzáadása</button>
            <button type="button" class="btn btn-primary" onclick="remove_kerdes()">Kerdés elvétel</button>
        </div>
        <div class="m-3" id="counts">
            <button type="submit" class="btn btn-primary" name="quiz_keszit">Készít</button>
        </div>
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



    const add_valasz = (kerdes) => {
        const valasz_list = document.getElementById(kerdes+"kerdes_valasz_list");

        valaszok[kerdes+'kerdes'] += 1;
        const clone = valasz.cloneNode(true);
        clone.innerHTML = `
    <div class="input-group">
        <span class="input-group-text">`+valaszok[kerdes+'kerdes'] + '. Válasz'+`</span>
        <div class="input-group-text gap-2">
            <input class="form-check-input mt-0" type="radio" name="`+kerdes+`radio" value="`+valaszok[kerdes+'kerdes']+`">
            <label class="form-check-label" for="radio">Helyes válasz</label>
        </div>
        <input type="text" class="form-control" name="`+kerdes+'kerdes_valasz' + valaszok[kerdes+'kerdes']+`" aria-label="Text input with radio button">
    </div>
`;


        valasz_list.append(clone);
        update_counts();
        if (valasz_list.childElementCount > 0) {
            const first = valasz_list.children.item(0);
            const radio = first.querySelector("input[type='radio']");
            radio.checked = true;
        }
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

    const add_kerdes = () => {
        kerdesek += 1;
        const main_kerdes = document.createElement("div");
        main_kerdes.className = 'container justify-content-center mb-3';
        main_kerdes.id = kerdesek+'kerdes';
        main_kerdes.innerHTML = `
            <div class="mb-3">
                <label for="question_name" class="form-label">Kérdés</label>
                <input type="text" class="form-control" id="qname`+kerdesek+`" name="qname`+kerdesek+`" placeholder="Példa kérdés">
            </div>
            <ul class="list-group mb-3" id="`+kerdesek+`kerdes_valasz_list">
            </ul>

            <div class="btn-toolbar justify-content-end " role="toolbar" aria-label="Toolbar with button groups">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-primary" name="adder" onclick="add_valasz('`+kerdesek+`')">Sor hozzáadása</button>
                    <button type="button" class="btn btn-primary" name="remover" onclick="remove_valasz('`+kerdesek+`')">Sor elvétel</button>
                </div>
            </div>
        `;
        valaszok[kerdesek+'kerdes'] = 0;
        quiz.append(main_kerdes);
        add_valasz(kerdesek+"");
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

    const add_kategoria = () => {
        // TODO
    }

    const remove_kategoria = () => {
        // TODO
    }

    const update_counts = () => {
        // TODO implement kategoria handles
        const counts = document.getElementById("counts");
        if (counts.childElementCount > 1) {
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

    add_kerdes();

</script>

</body>
</html>
