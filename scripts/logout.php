<?php
    session_start();
    session_unset();
    header('Location: ../pages/panel_login.php');
?>
