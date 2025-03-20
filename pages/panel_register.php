<?php
session_start();

if (isset($_POST["email"])) {
    $valid_check = true;
    $username = $_POST["username"];

    if (strlen($username) < 3 || strlen($username) > 10) {
        $valid_check = false;
        $_SESSION["info_username"] =
            "Nazwa użytkownika musi mieć od 3 do 10 znaków";
    }

    if (ctype_alnum($username) == false) {
        $valid_check = false;
        $_SESSION["info_username"] =
            "Nazwa użytkownika może zawierać cyfry, litery bez polskich znaków";
    }

    $firstName = $_POST["first_name"];
    if (strlen($firstName) < 3 || strlen($firstName) > 15) {
        $valid_check = false;
        $_SESSION["info_first_name"] = "Imię musi mieć od 3 do 15 znaków";
    }

    $lastName = $_POST["last_name"];
    if (strlen($lastName) < 3 || strlen($lastName) > 15) {
        $valid_check = false;
        $_SESSION["info_last_name"] = "Nazwisko musi mieć od 3 do 15 znaków";
    }

    $_SESSION["form_first_name"] = $firstName;
    $_SESSION["form_last_name"] = $lastName;

    $email = $_POST["email"];
    $email2 = filter_var($email, FILTER_SANITIZE_EMAIL);

    if (
        filter_var($email2, FILTER_VALIDATE_EMAIL) == false ||
        $email2 != $email
    ) {
        $valid_check = false;
        $_SESSION["info_email"] = "Wprowadź poprawny adres e-mail";
    }

    $password1 = $_POST["password1"];
    $password2 = $_POST["password2"];

    if (strlen($password1) < 8 || strlen($password1) > 20) {
        $valid_check = false;
        $_SESSION["info_password"] = "Hasło musi mieć od 8 do 20 znaków";
    }

    if ($password1 != $password2) {
        $valid_check = false;
        $_SESSION["info_password"] = "Hasła się nie zgadzają!";
    }

    $birthday = $_POST["birthday"];
    if (empty($birthday)) {
        $valid_check = false;
        $_SESSION["info_birthday"] = "Wprowadź datę urodzenia";
    }

    $club = $_POST["club"];
    if (strlen($club) < 1 || strlen($club) > 50) {
        $valid_check = false;
        $_SESSION["info_club"] = "Nazwa klubu musi mieć od 1 do 50 znaków";
    }

    $_SESSION["form_birthday"] = $birthday;
    $_SESSION["form_club"] = $club;

    if (isset($_POST["role"])) {
        $role = $_POST["role"];
    } else {
        $valid_check = false;
        $_SESSION["info_role"] = "Wybierz rolę!";
    }

    $captcha_secret_key = "6LeUTIcUAAAAADlKvNuia50oKyzFIwLSo4J9xxPv";
    $check = file_get_contents(
        "https://www.google.com/recaptcha/api/siteverify?secret=" .
            $captcha_secret_key .
            "&response=" .
            $_POST["g-recaptcha-response"]
    );
    $response_from_google = json_decode($check);

    if ($response_from_google->success == false) {
        $valid_check = false;
        $_SESSION["info_bot"] = "Potwierdź że nie jesteś robotem";
    }

    $_SESSION["form_username"] = $username;
    $_SESSION["form_email"] = $email;
    $_SESSION["form_password1"] = $password1;
    $_SESSION["form_password2"] = $password2;
    $_SESSION["form_role"] = $role;

    $defaultRanking = 1000;

    require_once "../scripts/connect.php";
    mysqli_report(MYSQLI_REPORT_STRICT);

    $con = new mysqli($host, $db_user, $db_password, $db_name);
    $con->set_charset("utf8");

    if ($con->connect_errno != 0) {
        echo '<span style="color:red;">Błąd połączenia z bazą danych!</span>';
        exit();
    }

    // Sprawdzenie czy jest taki mail w bazie
    $result = $con->query("SELECT UserID FROM users WHERE email='$email'");
    if (!$result) {
        echo '<span style="color:red;">Błąd zapytania: ' .
            $con->error .
            "</span>";
        exit();
    }

    $email_exist = $result->num_rows;
    if ($email_exist > 0) {
        $valid_check = false;
        $_SESSION["info_email"] = "Istnieje już konto z takim adresem e-mail.";
    }

    // Sprawdzenie czy jest taka nazwa użytkownika w bazie
    $result = $con->query(
        "SELECT UserID FROM users WHERE UserName='$username'"
    );
    if (!$result) {
        echo '<span style="color:red;">Błąd zapytania: ' .
            $con->error .
            "</span>";
        exit();
    }

    $username_exist = $result->num_rows;
    if ($username_exist > 0) {
        $valid_check = false;
        $_SESSION["info_username"] = "Ta nazwa użytkownika jest już zajęta.";
    }

    if ($valid_check == true) {
        // Pobierz największe ID użytkownika z tabeli users
        $result = $con->query("SELECT MAX(UserID) AS max_id FROM users");
        $row = $result->fetch_assoc();
        $max_id = $row["max_id"];

        // Jeżeli nie ma żadnych rekordów w tabeli, ustaw ID na 1, w przeciwnym razie dodaj 1 do największego ID
        $new_id = $max_id === null ? 1 : $max_id + 1;

        if (
            $con->query("INSERT INTO users (UserID, UserName, password, FirstName, LastName, Email, Ranking, Role, ProfilePicture, Birthday, Club, Title) 
    VALUES ('$new_id', '$username', '$password1', '$firstName', '$lastName', '$email', '$defaultRanking' , '$role','../img/default_photo.png', '$birthday', '$club','Brak tytułu')")
        ) {
            $_SESSION["logged"] = true;

            $username = htmlentities(
                $_SESSION["form_username"],
                ENT_QUOTES,
                "UTF-8"
            );
            $password = htmlentities(
                $_SESSION["form_password1"],
                ENT_QUOTES,
                "UTF-8"
            );

            if (
                $result = @$con->query(
                    sprintf(
                        "SELECT * FROM users WHERE BINARY UserName='%s' AND BINARY password='%s'",
                        mysqli_real_escape_string($con, $username),
                        mysqli_real_escape_string($con, $password)
                    )
                )
            ) {
                $userCount = $result->num_rows;
                if ($userCount > 0) {
                    $_SESSION["logged"] = true;

                    $row = $result->fetch_assoc();
                    $_SESSION["UserID"] = $row["UserID"];
                    $_SESSION["UserName"] = $row["UserName"];
                    $_SESSION["email"] = $row["email"];

                    unset($_SESSION["error"]);
                    $result->free_result();
                    header("Location: ../pages/account_statistics.php");
                } else {
                    $_SESSION["error"] =
                        '<div class="error">Nieprawidłowa nazwa lub hasło</div>';
                    header("Location: ../index.php");
                }
            }
        } else {
            echo '<span style="color:red;">Błąd zapytania: ' .
                $con->error .
                "</span>";
        }
    }

    $con->close();
}
?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <title>Rejestracja - TurniejeSzachowe</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/main_mobile.css">
    <script src='https://www.google.com/recaptcha/api.js'></script>
</head>

<body>

<?php include "../include/header.php"; ?>

<div class="main-container">
<div class="form-container">
    <h1 class="form-container-title">Rejestracja</h1>
    <p class="form-container-info">Jeśli chcesz brać udział w turniejach, musisz utworzyć konto.</p>

    <form method="post" class="form-container-form form">
	<input type="text" value="<?php if (isset($_SESSION["form_first_name"])) {
     echo $_SESSION["form_first_name"];
 } ?>" name="first_name" placeholder="Imię" class="form-text-input">

    <?php if (isset($_SESSION["info_first_name"])) {
        echo '<div class="error">' . $_SESSION["info_first_name"] . "</div>";
    } ?>

    <input type="text" value="<?php if (isset($_SESSION["form_last_name"])) {
        echo $_SESSION["form_last_name"];
    } ?>" name="last_name" placeholder="Nazwisko" class="form-text-input">

    <?php if (isset($_SESSION["info_last_name"])) {
        echo '<div class="error">' . $_SESSION["info_last_name"] . "</div>";
    } ?>

    <input type="text" value="<?php if (isset($_SESSION["form_username"])) {
        echo $_SESSION["form_username"];
    } ?>" name="username" placeholder="Nazwa użytkownika" class="form-text-input">

    <?php if (isset($_SESSION["info_username"])) {
        echo '<div class="error">' . $_SESSION["info_username"] . "</div>";
    } ?>

    <input type="email" value="<?php if (isset($_SESSION["form_email"])) {
        echo $_SESSION["form_email"];
    } ?>" name="email" placeholder="E-mail" class="form-text-input">

    <?php if (isset($_SESSION["info_email"])) {
        echo '<div class="error">' . $_SESSION["info_email"] . "</div>";
    } ?>

    <input type="password" value="<?php if (
        isset($_SESSION["form_password1"])
    ) {
        echo $_SESSION["form_password1"];
    } ?>" name="password1" placeholder="Hasło" class="form-text-input">

    <?php if (isset($_SESSION["info_password"])) {
        echo '<div class="error">' . $_SESSION["info_password"] . "</div>";
    } ?>

    <input type="password" value="<?php if (
        isset($_SESSION["form_password2"])
    ) {
        echo $_SESSION["form_password2"];
    } ?>" name="password2" placeholder="Powtórz hasło" class="form-text-input">


    <input type="text" value="<?php if (isset($_SESSION["form_club"])) {
        echo $_SESSION["form_club"];
    } ?>" name="club" placeholder="Klub" class="form-text-input">

    <?php if (isset($_SESSION["info_club"])) {
        echo '<div class="error">' . $_SESSION["info_club"] . "</div>";
    } ?>
                
    <label for="birthday">Data urodzenia:</label>
    <input type="date" id="birthday" name="birthday" value="<?php if (
        isset($_SESSION["form_birthday"])
    ) {
        echo $_SESSION["form_birthday"];
    } ?>" class="form-text-input">
    <?php if (isset($_SESSION["info_birthday"])) {
        echo '<div class="error">' . $_SESSION["info_birthday"] . "</div>";
    } ?>

    <label>Wybierz rolę:</label>
    <label>
        <input type="radio" name="role" value="arbiter" checked> Sędzia
    </label>
    <label>
        <input type="radio" name="role" value="player"> Zawodnik
    </label>
    <br>
    <?php if (isset($_SESSION["info_role"])) {
        echo '<div class="error">' . $_SESSION["info_role"] . "</div>";
    } ?>

    <div class="g-recaptcha" data-sitekey="6LeUTIcUAAAAAOWoquG3JjjqjZ4Lq3VVAdk6jlel"></div>

    <?php if (isset($_SESSION["info_bot"])) {
        echo '<div class="error">' . $_SESSION["info_bot"] . "</div>";
    } ?>

    <input type="submit" value="Ukończ rejestrację" class="form-button">
    Masz już konto?
    <p class="form-container-info"><a href="panel_login.php" class="form-a">Zaloguj się</a></p>
    </form>

</div>

</div>

<?php include "../include/footer.php"; ?>

</body>
</html>
