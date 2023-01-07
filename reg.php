<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
   
    <style>
      
      <?php echo file_get_contents("style.css"); ?>
    </style>
</head>

<body>
 
    <?php
        include 'functions_db.php';
        session_start();
        
        if (getCurrentUser()) {
            header("Location: index.php"); 
            exit;
        }

        $_SESSION["index"] = false; //отмечаем, что регистрация не с главной страницы
    ?>

    <!--Форма регистрации-->
    <div class="form-popup-visible">
        <form method = "post" action="process_reg.php">
            <p>Регистрация</p>

            <div class="form-group">
                <input name="login" class="form-control" type="text" placeholder="Логин" required>
            </div>

            <div class="form-group">
                <input name="password" class="form-control" type="password" placeholder="Пароль" required>
            </div>

            <div class="form-group">
                <input name="password_repeat" class="form-control" type="password" placeholder="Повторите пароль" required>
            </div>
           
            <input name="submit" class ="btn btn-primary" type="submit" value="Зарегистрироваться">

    <?php
    
    //Выводим сообщения о возникших проблемах регистрации
        if (!empty($_SESSION["login_is_taken"])) {
    ?>
            <small class="form-text text-danger">Логин занят!</small>
    
    <?php
    }

        if (!empty($_SESSION["not_match"])) {
    ?>
            <small class="form-text text-danger">Пароли не совпадают!</small>
    <?php
    }

        if (!empty($_SESSION["password_too_short"])) {
    ?>
            <small class="form-text text-danger">Пароль содержит менее 5 символов!</small>
    <?php
    }

    ?>
        </form>
    </div>

   
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

</body>
</html>