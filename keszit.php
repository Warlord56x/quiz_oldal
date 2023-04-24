<?php
include "database.php";
global $conn;
session_start();

$account_error = null;
$account_update_error = null;

if (!isset($_SESSION["felhasznalo"])) {
    header("Location: index.php");
}

$account = $_SESSION["felhasznalo"];
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
    <form action="profile.php" method="post" >
    <div class="container" id="quiz">
        <div class="mb-3">
            <label for="question_name" class="form-label">Quiz név</label>
            <input type="text" class="form-control" id="question_name" placeholder="Példa quiz">
        </div>
        <div class="container justify-content-center mb-3" id="1kerdes">
            <div class="mb-3">
                <label for="question_name" class="form-label">Kérdés</label>
                <input type="text" class="form-control" id="question_name" placeholder="Példa kérdés">
            </div>
            <ul class="list-group mb-3" id="1kerdes_valasz_list">
                <li class="list-group-item" id="1kerdes_valasz1">
                    <div class="input-group">
                        <span class="input-group-text">1. Válasz</span>
                        <div class="input-group-text gap-2">
                            <input class="form-check-input mt-0" type="radio" name="radio" id="radio">
                            <label class="form-check-label" for="radio">
                                Helyes válasz
                            </label>
                        </div>
                        <input type="text" class="form-control" name="1kerdes_valasz1" aria-label="Text input with radio button">
                    </div>
                </li>
            </ul>

            <div class="btn-toolbar justify-content-end " role="toolbar" aria-label="Toolbar with button groups">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-primary" name="adder" onclick="add_valasz('1')">Sor hozzáadása</button>
                    <button type="button" class="btn btn-primary" name="remover" onclick="remove_valasz('1')">Sor elvétel</button>
                </div>
            </div>
        </div>

    </div>
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-primary" onclick="add_kerdes()">Kérdés hozzáadása</button>
        <button type="button" class="btn btn-primary" onclick="remove_kerdes()">Kerdés elvétel</button>
    </div>
    <div class="btn-group" role="group">
        <button type="submit" class="btn btn-primary" name="quiz_keszit">Készít</button>
    </div>
    </form>
</div>

<script>
    let kerdesek = 1;
    let valaszok = {
        '1kerdes' : 1,
    }

    const quiz = document.getElementById("quiz");
    const main_kerdes = document.getElementById("1kerdes");
    const valasz = document.getElementById("1kerdes_valasz1");


    const add_valasz = (kerdes) => {
        const valasz_list = document.getElementById(kerdes+"kerdes_valasz_list");

        valaszok[kerdes+'kerdes'] += 1;
        const clone = valasz.cloneNode(true);

        const lastInput = clone.querySelector('input[type="text"]');
        lastInput.name = kerdes+'kerdes_valasz' + valaszok;

        const span = clone.querySelector('span.input-group-text');
        span.textContent = valaszok[kerdes+'kerdes'] + '. Válasz';

        valasz_list.append(clone);
    }

    const remove_valasz = (kerdes) => {
        const valasz_list = document.getElementById(kerdes+"kerdes_valasz_list");

        const last_element = valasz_list.lastChild;
        valasz_list.removeChild(last_element);
        if (valaszok[kerdes+'kerdes'] !== 1) {
            valaszok[kerdes+'kerdes'] -= 1;
        }
    }

    const add_kerdes = () => {
        kerdesek += 1;
        const kerdes_clone = main_kerdes.cloneNode(true);
        kerdes_clone.id = kerdesek + "kerdes";

        const valasz_list = kerdes_clone.querySelector('ul.list-group');
        console.log(valasz_list);
        valasz_list.id = kerdesek+"kerdes_valasz_list";

        const adder = kerdes_clone.querySelector('button[name="adder"]');
        const remover = kerdes_clone.querySelector('button[name="remover"]');

        adder.onclick = () => {
            add_valasz(kerdesek+'');
        };
        remover.onclick = () => {
            remove_valasz(kerdesek+'');
        };

        valaszok[kerdesek+'kerdes'] = 1;
        quiz.append(kerdes_clone);
    }

    const remove_kerdes = () => {
        const last_element = quiz.lastChild;
        quiz.removeChild(last_element);
        if (kerdesek !== 1) {
            kerdesek -= 1;
        }
    }

</script>

</body>
</html>
