<?php
session_start();

if ((!isset($_POST['login'])) || (!isset($_POST['password'])))
{
    header('Location: index.php');
    exit();
}

require_once "../scripts/connect.php";

// Utwórz obiekt mysqli i ustaw kodowanie na utf8
$con = @new mysqli($host, $db_user, $db_password, $db_name);
$con->set_charset("utf8");

// Sprawdź, czy udało się połączyć z bazą danych
if ($con->connect_errno != 0)
{
    echo "Error: ".$con->connect_errno;
}
else
{
    $login = $_POST['login'];
    $password = $_POST['password'];
    
    $login = htmlentities($login, ENT_QUOTES, "UTF-8");
    $password = htmlentities($password, ENT_QUOTES, "UTF-8");

    if ($result = @$con->query(
        sprintf("SELECT * FROM users WHERE BINARY UserName='%s' AND BINARY password='%s'",
        mysqli_real_escape_string($con, $login),
        mysqli_real_escape_string($con, $password))))
    {
        $userCount = $result->num_rows;
        if($userCount > 0)
        {
            $_SESSION['logged'] = true;
            
            $row = $result->fetch_assoc();
            $_SESSION['UserID'] = $row['UserID'];
            $_SESSION['UserName'] = $row['UserName'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['password'] = $row['password'];
            $_SESSION['Role'] = $row['Role'];
            
            unset($_SESSION['error']);
			
            $result->free_result();
            header('Location: ../pages/account_statistics.php');
            
        } else {
            $_SESSION['error'] = '<div class="error">Nieprawidłowa nazwa lub hasło!</div>';
            $_SESSION['login'] = $_POST['login'];
            header('Location: ../pages/panel_login.php');
            
        }
        
    }
    $con->close();
}

?>
