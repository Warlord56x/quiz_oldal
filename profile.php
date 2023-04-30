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

if (isset($_POST["delete"])) {
    delete_account($account);
}

if (isset($_POST["pswd_update"])) {
    $account_error = change_password($account, $_POST["pswd_current"], $_POST["pswd_new"]);
}

if (isset($_POST["profile_update"])) {
    edit_account($account, $_POST["email"], $_POST["firstname"], $_POST["lastname"], $_POST["age"]);
    $_SESSION["felhasznalo"] = $account;
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

<div class="container justify-content-center mt-4">
    <div class="row row-cols-md-3 mb-3">
        <div class="card col text-center">
            <i class="bi bi-person-circle" style="font-size: 24rem"></i>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><?php echo $account->getEmail(); ?></li>
            </ul>
        </div>

        <form action="profile.php" method="post">
            <div class="container row-cols-auto">
                <ul class="list-group row">
                    <li class="list-group-item">
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="pemail" name="email" placeholder="name@example.com" value="<?php echo $account->getEmail();?>">
                            <label for="pemail">E-mail cím</label>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="vez" name="lastname" placeholder="Legjobb" value="<?php echo $account->getLastName();?>">
                            <label for="vez">Vezeték név</label>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="ker" name="firstname" placeholder="Sanyi" value="<?php echo $account->getFirstName();?>">
                            <label for="ker">Kereszt név</label>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="form-floating mb-3">
                            <input type="number" class="form-control" id="page" name="age" placeholder="0" value="<?php echo $account->getAge();?>">
                            <label for="page">Életkor</label>
                        </div>
                    </li>
                    <li class="list-group-item">
                        Pontszám: <?php echo $account->getOverallScore();?>
                    </li>
                </ul>
                <button type="submit" class="btn btn-primary row mt-4" name="profile_update">Frissítés</button>
            </div>
        </form>

        <form action="profile.php" method="post">
            <div class="container row-cols-auto">
                <?php
                if (isset($_POST["pswd_update"])) {
                ?>

                <p class="text-danger">
                <?php
                    if (count($account_error) > 0) {
                        foreach ($account_error as $item) {
                            echo "$item\n";
                        }
                ?>
                        </p>
                <?php
                    } else {
                        echo "<p class='text-success'> A jelszavad megváltozottt</p>";
                    }
                }
                ?>

                <ul class="list-group row">
                    <li class="list-group-item">
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control
                            <?php
                            if (isset($_POST["pswd_update"])) {
                                if (gettype($account_error) === "array") {
                                    echo "is-invalid";
                                } else {
                                    echo "is-valid";
                                }
                            }
                            ?>" id="pswd_current" name="pswd_current" placeholder="password">
                            <label for="pswd_current">Jelenlegi jelszó</label>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control
                            <?php
                            if (isset($_POST["pswd_update"])) {
                                if (gettype($account_error) === "array") {
                                    echo "is-invalid";
                                } else {
                                    echo "is-valid";
                                }
                            }
                            ?>" id="pswd_new" name="pswd_new" placeholder="Legjobb">
                            <label for="pswd_new">Új jelszó</label>
                        </div>
                    </li>
                </ul>
                <button type="submit" class="btn btn-primary row mt-4" name="pswd_update">Frissítés</button>
            </div>
        </form>
    </div>
    <form action="profile.php" method="post">
        <button type="submit" class="btn btn-danger" name="delete">Fiók Törlése</button>
    </form>
</div>
</body>
</html>
