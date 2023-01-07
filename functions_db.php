<?php

    function getUsersList () {
        
        $users = []; //заданные пары логин/пароль: admin/superuser, Mary/guess

        $lines = file('data.txt');

        foreach ($lines as $line_num => $line) {
    
            if (trim($line)) {
            $temp = explode(" ", $line);
            $users[$line_num] = ['login' => $temp[0], 'password' => trim($temp[1])];  
            }
        }

        return $users;
    }

    
    function existsUser ($login) {
        
        $users = getUsersList();
        $logins = [];


        foreach ($users as $id => $user) {    
    
            $logins[$id] = $users[$id]['login'];    
        }

    
        if (in_array($login, $logins)) {
            return true;   
        } else {
            return false;  
        }

    }

    function checkPassword($login, $password) {
        
        $users = getUsersList();
        $checked = false;
        
        foreach ($users as $user_num => $user) {
            // Если пароль из базы совпадает с паролем из формы
            if  (($login === $users[$user_num]['login']) && ($password === $users[$user_num]['password'])) {
                $checked = true;
            }
        }
    
        return $checked;
    }

    function getCurrentUser() {
        if (!empty($_SESSION['login'])) 
            return $_SESSION['login'];
        else 
            return null;
    }
?>