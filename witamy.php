<?php
session_start();

if(isset($_SESSION['udanarejestracja']))
{
    header('Location: index.php');
    exit();
}
else
{
    unset($_SESSION['udanarejestracja']);
}

//usuwanie zmiennych
if(isset($_SESSION['fr_nick'])) unset($_SESSION['fr_nick']);
if(isset($_SESSION['fr_email'])) unset($_SESSION['fr_email']);
if(isset($_SESSION['fr_haslo1'])) unset($_SESSION['fr_haslo1']);
if(isset($_SESSION['fr_haslo2'])) unset($_SESSION['fr_haslo2']);
if(isset($_SESSION['fr_regulamin'])) unset($_SESSION['fr_regulamin']);

//usuwanie błędów rejestracji 
if(isset($_SESSION['e_nick'])) unset($_SESSION['e_nick']);
if(isset($_SESSION['e_email'])) unset($_SESSION['e_email']);
if(isset($_SESSION['e_haslo1'])) unset($_SESSION['e_haslo1']);
if(isset($_SESSION['e_haslo2'])) unset($_SESSION['e_haslo2']);
if(isset($_SESSION['e_regulamin'])) unset($_SESSION['e_regulamin']);
if(isset($_SESSION['e_bot'])) unset($_SESSION['e_bot']);




?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <br/><br/>Diękujemy za rejestracje możesz się zalogować na swoje konto <br/><br/>
    <a href="index.php">Zaloguj się do gry</a>
    <br/><br/>
</body>
</html>