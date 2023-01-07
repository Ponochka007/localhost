<?php

    session_start();
    $_SESSION["date_is_set"] = true;
    $_SESSION["DOB"] = $_POST['DOB']; //запоминаем дату рождения пользователя
    header("Location: index.php"); 

?>