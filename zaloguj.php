<?php

session_start();

if (!isset($_POST['login']) || !isset($_POST['haslo'])) {
    header('Location: index.php');
    exit();
}

require_once "connect.php";

$login = $_POST['login'];
$haslo = $_POST['haslo'];

$login = htmlentities($login, ENT_QUOTES, "UTF-8");
$haslo = htmlentities($haslo, ENT_QUOTES, "UTF-8");

$query = "SELECT * FROM uzytkownicy WHERE user='" . mysqli_real_escape_string($connection, $login) . "'";
$result = $connection->query($query);

if ($result) {
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($haslo, $row['pass'])) {

            $_SESSION['zalogowany'] = true;
            $_SESSION['id'] = $row['id'];
            $_SESSION['user'] = $row['user'];
            $_SESSION['drewno'] = $row['drewno'];
            $_SESSION['kamien'] = $row['kamien'];
            $_SESSION['zboze'] = $row['zboze'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['dnipremium'] = $row['dnipremium'];

            unset($_SESSION['blad']);
            $result->free_result();
            header('Location: gra.php');
            exit();
        }
    }
}

$_SESSION['blad'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
header('Location: index.php');
exit();

$connection->close();
?>
