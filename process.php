<?php

    include 'functions_db.php';

    $users = getUsersList(); //заданные пары логин/пароль: admin/superuser, Mary/guess
    $login = $_POST['login'] ?? null;
    $password = sha1($_POST['password']) ?? null;

    session_start();

    $_SESSION['failed_reg'] = false; //проверяем авторизацию, а не регистрацию, чье состояние записано в failed_reg

    if (null != $login || null != $_POST['password']) {

        if (checkPassword($login, $password)) {
            $_SESSION['auth'] = true;
            $_SESSION['failed'] = false;  
        
            //Пишем в сессию логин и id пользователя
            $_SESSION['id'] = $user_num; 
            $_SESSION['login'] = $login; 
            header("Location: index.php");
            exit;
        }

    } else {
        //перенаправляем на ту страницу, откуда совершена попытка авторизации
        $_SESSION["isNull"] = true;
        if (!empty($_SESSION["index"])) {
            header("Location: index.php");
            exit;  
        } else {
            header("Location: login.php"); 
        }

        exit;
    }

    //если пароль не пуст, но авторизация не удалась
    
    $_SESSION["isNull"] = false;
    $_SESSION["auth"] = false;
    $_SESSION["failed"] = true;

    if (!empty($_SESSION["index"])) {
        header("Location: index.php");
        exit;  
    } else {
        header("Location: login.php"); 
    }

?>