<?php

    session_start();

    if(isset($_POST['email']))
    {
        //udana walidacja
        $wszystko_OK=true;

        //nickname
        $nick = $_POST['nick'];

        //długość nickname
        if ((strlen($nick)<3) || (strlen($nick)>20))
        {
            $wszystko_OK = false;
            $_SESSION['e_nick'] = 'Nick musi posadać od 3 do 20 znaków';
        }

        if(ctype_alnum($nick)==false)
        {
            $wszystko_OK = false;
            $_SESSION['e_nick'] = "Nick może skaładać sie z liter i cyfr (bez polskich zanków)";
        }


        //sprawdź E-Mail

        $email = $_POST['email'];

        $emailB = filter_var($email, FILTER_SANITIZE_EMAIL);

        if((filter_var($emailB, FILTER_VALIDATE_EMAIL)==false) || ($emailB != $email))
        {
            $wszystko_OK = false;
            $_SESSION['e_email'] = "Podaj poprawy adres E-Mail";

        }

        //Sparawdź Hasło

        $haslo1 = $_POST['haslo1'];
        $haslo2 = $_POST['haslo2'];

        if((strlen($haslo1)<8) || (strlen($haslo1)>20))
        {
            $wszystko_OK = false;
            $_SESSION['e_haslo'] = "Hasło musi posiadać od 8 do 20 znaków";

        }

        if($haslo1!=$haslo2)
        {
            $wszystko_OK = false;
            $_SESSION['e_haslo'] = "Podane hasła nie są takie same";

        }

        $haslo_hash = password_hash($haslo1, PASSWORD_DEFAULT);

        //akceptacja regulaminu
        if(!isset($_POST['regulamin']))
        {
            $wszystko_OK = false;
            $_SESSION['e_regulamin'] = "Zaakceptuj Regulamin";
        }
        
        //reGÓWNO

        $sekert = 'insert private key';

        $sprawdz = file_get_contents('https://google.com/recaptcha/api/siteverify?secret='.$sekert.'&response='.$_POST['g-recaptcha-response']);

        $odpowiedź = json_decode($sprawdz);

        if($odpowiedź->success==false)
        {
            $wszystko_OK = false;
            $_SESSION['e_bot'] = "Potwierdź że nie jesteś botem";
        }

        //zapamiętaj wprowadzone dane 
        $_SESSION['fr_nick'] = $nick;
        $_SESSION['fr_eamil'] = $email;
        $_SESSION['fr_haslo1'] = $haslo1; 
        $_SESSION['fr_haslo2'] = $haslo2;
        if(isset($_POST['regulamin']))$_SESSION['fr_regulamin'] = true;




        require_once "connect.php";
        mysqli_report(MYSQLI_REPORT_STRICT);

        try
        {
            $polaczenie = new mysqli($host, $db_user, $db_password, $db_name );
            if($polaczenie->connect_errno!=0)
            {
                throw new Exception(mysqli_connect_errno());
            }
            else
            {
                //Czy email już istnieje 
                $result = $polaczenie ->query("SELECT id FROM uzytkownicy WHERE email='$email'");

                if(!$result) throw new Exception($polaczenie->error);

                $_iemail = $result->num_rows;
                if($_iemail>0)
                {  
                    $wszystko_OK = false;
                    $_SESSION['e_email'] = "Instaniej już konto przypisane do tego adresu e-mail!"; 
                }



                //czy nick jest już zarezerwowany
                $result = $polaczenie ->query("SELECT id FROM uzytkownicy WHERE user='$nick'");

                if(!$result) throw new Exception($polaczenie->error);

                $_inick = $result->num_rows;
                if($_inick>0)
                {  
                    $wszystko_OK = false;
                    $_SESSION['e_nick'] = "Istnieje już gracz o takim nick'u "; 
                }

                if($wszystko_OK == true)
                {
                    //Wszytsko się udało
                    if ($polaczenie->query("INSERT INTO uzytkownicy VALUES (NULL, '$nick', '$haslo_hash', '$email', 100, 100, 100, now() + INTERVAL 14 DAY)"))
                    {
                        $_SESSION['udanarejstracja']=true;
                        header('Location:witamy.php');
                    }
                    else
                    {
                        throw new Exception($polaczenie->error);
                    }
                }

                $polaczenie->close();
            }
        }
        catch(Exception $e)
        {
            echo '<span style = "color:red;">Błąd serwera! Przepraszam zarejestruj się w innym terminie :(</span>';
            //echo '<br/>Informacja developerska: '.$e;
        }

    }

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Osadnicy-załóż darmowe konto</title>
    <script src="https://www.google.com/recaptcha/api.js"></script>
    <style>
        .error
        {
            color:red;
            margin-top: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <form method="post">

        Nickname: <br/> <input type="text" value="<?php 
        if(isset($_SESSION['fr_nick']))
        {
            echo $_SESSION['fr_nick'];
            unset($_SESSION["fr_nick"]);
        }
        
        ?>" name="nick"> <br/>

        <?php
            if(isset($_SESSION['e_nick']))
            {
                echo '<div class = "error">'. $_SESSION['e_nick']. '</div>';
                unset($_SESSION['e_nick']);
            }
        ?>

        E-mail: <br/> <input type="text" value="<?php 
        if(isset($_SESSION['fr_email']))
        {
            echo $_SESSION['fr_email'];
            unset($_SESSION["fr_email"]);
        }
        
        ?>" name="email"> <br/>

        <?php
            if(isset($_SESSION['e_email']))
            {
                echo '<div class = "error">'. $_SESSION['e_email']. '</div>';
                unset($_SESSION['e_email']);
            }
        ?>

        Twoje Hasło: <br/> <input type="password" value="<?php 
        if(isset($_SESSION['fr_haslo1']))
        {
            echo $_SESSION['fr_haslo1'];
            unset($_SESSION["fr_haslo1"]);
        }
        
        ?>" name="haslo1"> <br/>

        <?php
            if(isset($_SESSION['e_haslo']))
            {
                echo '<div class = "error">'. $_SESSION['e_haslo']. '</div>';
                unset($_SESSION['e_haslo']);
            }
        ?>

        Powtórz Hasło: <br/> <input type="password" value="<?php 
        if(isset($_SESSION['fr_haslo2']))
        {
            echo $_SESSION['fr_haslo2'];
            unset($_SESSION["fr_haslo2"]);
        }
        
        ?>" name="haslo2"> <br/>

        <label>
        <input type="checkbox" name="regulamin"/><?php 
        if(isset($_SESSION['fr_regulamin']))
        {
            echo "checked";
            unset($_SESSION['fr_regulamin']);
        }


        ?> Akceptuje regulamin
        </label>
        <?php
            if(isset($_SESSION['e_regulamin']))
            {
                echo '<div class = "error">'. $_SESSION['e_regulamin']. '</div>';
                unset($_SESSION['e_regulamin']);
            }
        ?>

        <div class="g-recaptcha" data-sitekey="insert public key"></div>
        <?php
            if(isset($_SESSION['e_bot']))
            {
                echo '<div class = "error">'. $_SESSION['e_bot']. '</div>';
                unset($_SESSION['e_bot']);
            }
        ?>

        <input type="submit" value="Zarejestruj się">

    </form>
</body>
</html>
