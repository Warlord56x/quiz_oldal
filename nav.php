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
                <?php if (isset($_SESSION["felhasznalo"])) {?>
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

<div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasRightLabel">Bejelentkezés</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body text-dark">
        <form action="index.php" method="post">
            <div class="text-bg-dark">
                <p>
                    <?php
                    if (isset($_SESSION["error"]) && $_SESSION["error"] !== "")
                    {
                        foreach ($_SESSION["error"] as $item) {
                            echo $item;
                        }
                    }
                    ?>
                </p>
            </div>
            <div class="form-floating">
                <input class="form-control" required type="email" id="email" name="email" placeholder="E-mail"/>
                <label for="email">E-mail</label>
            </div>
            <div class="form-floating">
                <input class="form-control" required type="password" id="pswd" name="pswd" placeholder="Password"/>
                <label for="pswd">Password</label>
            </div>
            <div class="text-center m-3">
                <button type="submit" class="btn btn-primary" name="login">Bejelentkezés</button>
            </div>
        </form>
        <form action="index.php" method="post">
            <div class="text-bg-dark">
                <p>
                    <?php
                    if (isset($_SESSION["error"]) && $_SESSION["error"] !== "")
                    {
                        foreach ($_SESSION["error"] as $item) {
                            echo $item;
                        }
                    }
                    ?>
                </p>
            </div>
            <div class="form-floating">
                <input class="form-control" type="email" required id="email" name="email" placeholder="E-mail"/>
                <label for="email">E-mail</label>
            </div>
            <div class="form-floating">
                <input class="form-control" type="password" required id="pswd" name="pswd" placeholder="Jelszó"/>
                <label for="pswd">Jelszó</label>
            </div>
            <div class="form-floating">
                <input class="form-control" type="text" required id="vez" name="vez" placeholder="Vezeték név"/>
                <label for="vez">Vezeték név</label>
            </div>
            <div class="form-floating">
                <input class="form-control" type="text" required id="ker" name="ker" placeholder="Kereszt név"/>
                <label for="ker">Kereszt név</label>
            </div>
            <div class="form-floating">
                <input class="form-control" type="number" required id="kor" name="kor" placeholder="Életkor"/>
                <label for="kor">Életkor</label>
            </div>
            <div class="text-center m-3">
                <button type="submit" class="btn btn-primary" name="register">Regisztráció</button>
            </div>
        </form>
    </div>
</div>
