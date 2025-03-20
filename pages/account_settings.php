<?php
session_start();

if (!isset($_SESSION["logged"])) {
    header('Location: ../index.php');
    exit();
}

require_once "../scripts/connect.php";

// Połączenie z bazą danych
$con = new mysqli($host, $db_user, $db_password, $db_name);
$con->set_charset("utf8");

// Sprawdź, czy połączenie się powiodło
if ($con->connect_error) {
    die('Błąd połączenia z bazą danych: ' . $con->connect_error);
}

// Pobierz dane użytkownika z bazy danych
$userId = $_SESSION['UserID'];
$stmtGetUser = $con->prepare('SELECT * FROM users WHERE UserID = ?');
$stmtGetUser->bind_param('i', $userId);
$stmtGetUser->execute();
$result = $stmtGetUser->get_result();
$user = $result->fetch_assoc();

$error = '';
$successMessage = '';

// Obsługa zmiany danych użytkownika
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Zmiana nazwy użytkownika
    if (isset($_POST['username'])) {
        $username = $_POST['username'];

        if (empty($username)) {
            $error = 'Nazwa użytkownika nie może być pusta.';
        } else {
            $stmtUpdateUser = $con->prepare('UPDATE users SET UserName = ? WHERE UserID = ?');
            if (!$stmtUpdateUser) {
                $error = 'Błąd przygotowania zapytania do aktualizacji danych.';
            } else {
                $stmtUpdateUser->bind_param('si', $username, $userId);
                if ($stmtUpdateUser->execute()) {
                    $successMessage = 'Nazwa użytkownika została zmieniona pomyślnie.';
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit();
                } else {
                    $error = 'Błąd aktualizacji danych: ' . $stmtUpdateUser->error;
                }
            }
        }
    }

    // Zmiana hasła
    if (isset($_POST['new_password'])) {
        $newPassword = $_POST['new_password'];

        $stmtUpdatePassword = $con->prepare('UPDATE users SET password = ? WHERE UserID = ?');
        $stmtUpdatePassword->bind_param('si', $newPassword, $userId);

        if ($stmtUpdatePassword->execute()) {
            $successMessage = 'Hasło zostało zmienione pomyślnie.';
        } else {
            error_log("Błąd aktualizacji hasła: " . $stmtUpdatePassword->error);
            $error = 'Błąd aktualizacji hasła: ' . $stmtUpdatePassword->error;
        }
    }

    // Zmiana zdjęcia profilowego
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $uploadsDir = '../uploads/'; // Katalog na przesłane pliki
        $tmpName = $_FILES['profile_picture']['tmp_name'];
        $name = preg_replace('/\s+/', '_', basename($_FILES['profile_picture']['name'])); // Zastąpione spacje podkreślnikami
        $path = $uploadsDir . $name;

        // Sprawdź, czy plik już istnieje
        if (file_exists($path)) {
            $error = "Plik o tej nazwie już istnieje. Wybierz inny.";
        } else {
            if (move_uploaded_file($tmpName, $path)) {
                $stmtUpdatePicture = $con->prepare('UPDATE users SET ProfilePicture = ? WHERE UserID = ?');

                if (!$stmtUpdatePicture) {
                    die("Błąd przygotowania zapytania: " . $con->error);
                }

                $stmtUpdatePicture->bind_param('si', $path, $userId);
                if ($stmtUpdatePicture->execute()) {
                    $successMessage = 'Zdjęcie profilowe zostało zmienione pomyślnie.';
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit();
                } else {
                    die("Błąd wykonania zapytania: " . $stmtUpdatePicture->error);
                }
            } else {
                $error = "Błąd podczas przesyłania pliku.";
            }
        }
    }
}

// Pobierz dane użytkownika z bazy danych
$userId = $_SESSION['UserID'];
$stmtGetUser = $con->prepare('SELECT * FROM users WHERE UserID = ?');
$stmtGetUser->bind_param('i', $userId);
$stmtGetUser->execute();

$ranking = $user['Ranking'] ?? 'Nie ustalono rankingu';
$profilePicture = $user['ProfilePicture'] ? htmlspecialchars($user['ProfilePicture']) : '../uploads/default_photo.png';
$userName = htmlspecialchars($user['FirstName']);
$userSurName = htmlspecialchars($user['LastName']);
$title = htmlspecialchars($user['Title'] ?? ''); 
$club = htmlspecialchars($user['Club'] ?? '');
$birthday = htmlspecialchars($user['Birthday']);
$userID = htmlspecialchars($user['UserID']);
$email = htmlspecialchars($user['Email'] ?? '');

?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Ustawienia - TurniejeSzachowe</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/account.css">
    <link rel="stylesheet" href="../css/main_mobile.css">
</head>

<body>

<?php include('../include/header.php'); ?>

<div class="main-container">
    
<?php include('../include/account_side_menu.php'); ?>

    <div class="table-container">
        <div class="form-container">
            <h2 class="form-container-title">Ustawienia konta</h2>


            <?php
        echo "<div class='players-container'>";

            echo "<div class='player-card'>";
            echo "<img src='" . $profilePicture . "' alt='Zdjęcie zawodnika' class='player-image'>";
            ?>

                <!-- Formularz zmiany zdjęcia -->
                <form class="form-container-form form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
                <div>
                    <input class="search-input" type="file" id="profile_picture" name="profile_picture" required>
                </div>
                <div>
                    <button class="form-button" type="submit">Zmień zdjęcie</button>
                </div>
            </form>

            <?php
            echo "<div class='player-info'>";
            echo "<p><strong>Imię:</strong> " . $userName . "</p>";
            echo "<p><strong>Nazwisko:</strong> " . $userSurName . "</p>";
            echo "<p><strong>Tytuł:</strong> " . $title . "</p>";
            echo "<p><strong>Ranking:</strong> " . $ranking . "</p>";
            echo "<p><strong>Klub:</strong> " . $club . "</p>";
            echo "<p><strong>Data urodzenia:</strong> " . $birthday . "</p>";
            echo "<p><strong>Identyfikator:</strong> " . $userID . "</p>";
            echo "<p><strong>E-mail:</strong> " . $email . "</p>";
            echo "</div></div>";
            echo "</div>";
            ?>
            
            <?php
            if ($successMessage) {
                echo '<p style="color: green;">' . $successMessage . '</p>';
            }
            if ($error) {
                echo '<p style="color: red;">' . $error . '</p>';
            }
            ?>

            <!-- Formularz zmiany nazwy użytkownika -->
            <form class="form-container-form form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <div>
                <h3>Zmiana nazwy użytkownika</h3>
                    <input class="search-input" type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['UserName']); ?>" required>
                </div>
                <div>
                    <button class="form-button" type="submit">Zmień nazwę</button>
                </div>
            </form>

            <!-- Formularz zmiany hasła -->
            <form class="form-container-form form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <h3>Zmiana hasła</h3>
                <div>
                    <input class="search-input" type="password" id="new_password" name="new_password" placeholder="Podaj nowe hasło" required>
                </div>
                <div>
                    <button class="form-button" type="submit">Zmień hasło</button>
                </div>
            </form>           
        </div>
    </div>
</div>

<style>
.side-menu #account_settings {
    border: 1px solid #8f8f8f;
}
</style>
</body>
</html>

