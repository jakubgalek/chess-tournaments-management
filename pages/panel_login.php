<?php
session_start();

if ((isset($_SESSION["logged"])) && ($_SESSION["logged"]==true))
{
    header('Location: ../pages/account_statistics.php');
    exit();
}
?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Logowanie - TurniejeSzachowe</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/main_mobile.css">
</head>

<body>

<?php include('../include/header.php'); ?>
<div class="main-container">
    
<div class="form-container">
    <h1 class="form-container-title">Logowanie</h1>
    <p class="form-container-info">Aby móc korzystać ze wszystkich funkcjonalności strony, musisz się zalogować.</p>
    
    <form class="form-container-form form" action="../scripts/login.php" method="post">
        <input class="form-text-input" type="text" name="login" id="e-mail" placeholder="Nazwa użytkownika" 
               value="<?php if (isset($_SESSION['login'])) echo $_SESSION['login']; ?>">

        <input class="form-text-input" type="password" name="password" id="password" placeholder="Hasło">

        <?php
        if (isset($_SESSION['error'])) echo $_SESSION['error'];
        unset($_SESSION['error']);
        ?>

        <button class="form-button" type="submit">Zaloguj</button>

        <p class="form-container-info"><a href="panel_register.php" class="form-a">Utwórz nowe konto</a></p>
    </form>
</div>
</div>

<?php include('../include/footer.php'); unset($_SESSION['login']);?>

</body>
</html>

<?php unset($_SESSION['login']);?>