<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- <link rel="stylesheet" href="css/style.css"> -->
 
</head>
<body>
<?php 
        include 'functions.php';
    ?>

<?php
        session_start();
    ?>
    <style>      
      <?php echo file_get_contents("css/style.css"); ?>
    </style> 









    <?php     
    //если пользователь авторизован
    if (!empty($_SESSION['auth'])) {
    

        $count = $_SESSION['count'] ?? 0;
        $count++;
        $_SESSION['count'] = $count;

        //назначаем уникальные именa для куки разных пользователей
        $name_login = "login_" . $_SESSION["login"];
        $entry_time = "entry_time_" . $_SESSION["login"];
        $entry_time_formatted = "entry_time_formatted_" . $_SESSION["login"];

        //если время входа задано, проверяем текущий ли пользователь записан в куки.
        //если пользователь правильный, берем из куки время входа
        if (!empty($_COOKIE[$entry_time])) {
                if ($_COOKIE[$name_login] === $_SESSION["login"]) {
                $_SESSION["entry_time"] = $_COOKIE[$entry_time];
                $_SESSION["entry_time_formatted"] = $_COOKIE[$entry_time_formatted];
                $entry_time_set = true;
            }
        }

        //если в куках нет времени входа, при авторизации берем текущее время и записываем в куки для данного пользователя
        if (empty ($entry_time_set)) {
            if ($_SESSION['count'] == 1 ) {
                $_SESSION["entry_time"] = time();
                $_SESSION["entry_time_formatted"] = date("H:i:s"); 
                $entry_time_set = true;
            }
        }

        //количество секунд, прошедших со времени входа   
        $time_difference = time() - $_SESSION["entry_time"];

        //уникальное имя куки, где хранится (или будет храниться) дата дня рождения пользователя
        $birthday_login = "birthday_" . $_SESSION["login"];

        //если др пользователя задан в куках, берем оттуда
        if (!empty($_COOKIE[$birthday_login])) {
            if ($_COOKIE[$name_login] === $_SESSION["login"]) {
                $birthday = $_COOKIE[$birthday_login];
                $_SESSION["date_is_set"] = true;
            }
        }

        //иначе берем из обработанной формы и сохраняем в куках

        if (!empty($_SESSION["DOB"])) {
            $birthday = date('jS F', strtotime($_SESSION["DOB"]));

        }

        //сколько всего секунд осталось до истечения 24 часов со времени входа
        $all_seconds_left = 86400 - $time_difference;

        if ($all_seconds_left > 0) {
            $discount_active = true; //если время не истекло, акция активна
            $seconds_left = $all_seconds_left % 60;
            $all_minutes_left = ($all_seconds_left - $seconds_left) / 60; 
            $minutes_left = $all_minutes_left % 60;
            $hours_left = ($all_minutes_left - $minutes_left) / 60;

        } else {
            $discount_active = false; //если время истекло, акция неактивна
        }
    ?>  

    <div class="nav"> 
        <!--При авторизации отображаем приветствие и кнопку для выхода-->
        <p class="welcome">Здравствуйте, <?=$_SESSION['login']?></p>
        <a href="logout.php"><button class="open-button btn btn-secondary" type="button">Выйти</button></a>         
    </div>


    <?php
        //форма с просьбой сообщить дату рождения выводится при входе и потом через пять последующих сессий до получения ответа
        
        if ((($_SESSION['count'] - 1) % 5 == 0) && empty($_SESSION["date_is_set"])){ ?>

            <div class="form-popup-visible" id="formLog">
                
                <form method = "post" action="process_date.php">
                    <p>Какого числа Вы родились?</p>
                    <div class="form-group">
                        <label for="DOB"><b>Дата рождения</b></label>
                        <input name="DOB" class="form-control" type="date" max="2020-01-01" placeholder="Логин" required>
                    </div>
    
                    <input name="submit" class ="btn btn-primary" type="submit" value="Отправить">
                    <button type="button" class="btn cancel btn-secondary" onclick="close_formLog()">Закрыть</button> 

                </form>
            </div>
    <?php 
        }


        if (isset($birthday)) {
            //преобразуем др в метку времени Unix
            $DOB_Unix=strtotime($birthday);
            //вычисляем сколько дней 
            $days_until_birthday=ceil(($DOB_Unix-time())/60/60/24);
        //если др пользователя будет в следующем году
        if ($days_until_birthday < 0) {
            $days_until_birthday = 365 + $days_until_birthday;
        }
        //если др пользователя сегодня
        if ($days_until_birthday == 0) {
            $user_birthday = true;
        } 

        }
    ?>


    <?php
        } //конец расчетов для авторизованного пользователя
    ?>

    <!--Если пользователь не авторизован-->
    <?php
        if (empty($_SESSION['auth'])) {
    ?>
      

        <div class="nav">  
            <!--Отображаем вверху кнопки авторизации и регистрации-->
            <button class="open-button btn btn-secondary" type="button" onclick="open_formLog()">Войти</button>        
            <button class="registration-button btn btn-outline-secondary" type="button" onclick="open_formReg()">Регистрация</button>
              
        </div>
 
    <?php
        }
    ?>

    <!--Всплывающая форма авторизации без проблем авторизации, начальное состояние - невидима-->
    <div class="form-popup-invisible" id="formLog">
        <form method = "post" action="process.php">
            <p>Залогиньтесь</p>
            
            <div class="form-group">
                <label for="login"><b>Логин</b></label>
                <input name="login" class="form-control" type="text" placeholder="Логин" required>
            </div>
            
            <div class="form-group">
                <label for="password"><b>Пароль</b></label>
                <input name="password" class="form-control" type="password" placeholder="Пароль" required>
            </div>
    
            <input name="submit" class = "btn btn-info" type="submit" value="Войти">
   
            <button type="button" class="btn cancel btn-outline-dark" onclick="close_formLog()">Закрыть</button>
    
            <?php     
                $_SESSION["index"] = true; //отмечаем, что авторизуемся с главной страницы
            ?>
        </form>
    </div>

    <!--Всплывающая форма авторизации с проблемами авторизации (failed), которые возникли на главной странице
    начальное состояние - видима-->
    <?php
    if (!empty($_SESSION["index"]) && (!empty($_SESSION["failed"])) && empty($_SESSION["from_login"])) { 
        $_SESSION["failed"] = false;
        $_SESSION["from_login"] = false; //не со страницы login.php
    ?>

        <div class="form-popup-visible" id="formLog">
            <form method = "post" action="process.php">
                <p>Залогиньтесь</p>

                <div class="form-group">
                    <label for="login"><b>Логин</b></label>
                    <input name="login" class="form-control" type="text" placeholder="Логин" required>
                </div>

                <div class="form-group">
                    <label for="password"><b>Пароль</b></label>
                    <input name="password" class="form-control" type="password" placeholder="Пароль" required>
                </div>

                <input name="submit" class ="btn btn-info" type="submit" value="Войти">

                <button type="button" class="btn cancel btn-outline-dark" onclick="close_formLog()">Закрыть</button>
                
                <!--Выводим сообщения о возникших проблемах авторизации-->
                <?php  
                    if (!empty($_SESSION["isNull"])) {
                ?>
                    <small class="form-text text-danger">Введите логин и пароль!</small>
                <?php
                    } else {
                ?>
                    <small class="form-text text-danger">Неверный логин или пароль!</small>
    
                <?php
                    }
                ?>
            </form>
        </div>

    <?php
    }
    ?>

    <!--Всплывающая форма регистрации без проблем регистрации, начальное состояние - невидима-->

    <div class="form-popup-invisible" id="formReg">
        <form method = "post" action="process_reg.php">
            
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

            <button type="button" class="btn cancel btn-outline-dark" onclick="close_formReg()">Закрыть</button>

            <?php     
                $_SESSION["index"] = true; //отмечаем, что регистрируемся с главной страницы
            ?>
        </form>
    </div>

    <!--Всплывающая форма регистрации с проблемами регистрации (failed_reg), которые возникли на главной странице
    начальное состояние - видима-->

    <?php
        if (!empty($_SESSION["index"]) && (!empty($_SESSION["failed_reg"]))) {
            $_SESSION["failed_reg"] = false; 
    ?>

        <div class="form-popup-visible" id="formReg">
            <form method = "post" action="process_reg.php">
                
                <div class="form-group">
                    <input name="login" class="form-control" type="text" placeholder="Логин" required>
                </div>
    
                <div class="form-group">
                    <input name="password" class="form-control" type="password" placeholder="Пароль" required>
                </div>
    
                <div class="form-group">
                    <input name="password_repeat" class="form-control" type="password" placeholder="Повторите пароль" required>
                </div>

                <input name="submit" class ="btn btn-info" type="submit" value="Зарегистрироваться">

                <button type="button" class="btn cancel btn-outline-dark" onclick="close_formReg()">Закрыть</button>
                
                <!--Выводим сообщения о возникших проблемах регистрации-->
                <?php     
                    $_SESSION["index"] = true;
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

    <?php
        }
    ?>

    <header class="header">
        <div class="container">
            <div class="header-top">

                <nav class="menu">
                    <ul class="menu__list">
                        <li class="menu__list-item">
                            <a class="menu__list-link" href=#>О нас</a>
                        </li>
                        <li class="menu__list-item">
                            <a class="menu__list-link" href=#>Акции</a>
                        </li>
                        <li class="menu__list-item">
                            <a class="menu__list-link" href=#>Цены</a>
                        </li>
                        <li class="menu__list-item">
                            <a class="menu__list-link" href=#>Контакты</a>
                        </li>
                    </ul>
                </nav>
                
            </div>


            <h1 class="header__title">SPA салон «Sunny Brazil»</h1>
            

        </div>

        
    </header>
    <!--Сообщения в шапке для авторизованных пользователей-->
    <?php
        if (!empty($_SESSION["auth"])) {
    ?>
        <div class="notifications-container">
            <p class="user_notifications">
                Время входа: <?=$_SESSION["entry_time_formatted"]?>
                <br>
        <?php
            if($discount_active) {
        ?>
                Персональная скидка 15% истекает через: <?=$hours_left?>:<?=$minutes_left?>:<?=$seconds_left?>
                <br>
        <?php
            }

            if (isset($days_until_birthday)) {
                if ($days_until_birthday == 0) {
        ?>  
                    Поздравляем с Днем Рождения! Дарим скидку 15% на все услуги!
                    <br>
        <?php
                } else {
        ?>
                    Вы родились <?=getRussianDate($birthday)?>. До Вашего дня рождения <?=$days_until_birthday . " " . dayEnding($days_until_birthday)?> 
        <?php
                }
            }
         }
        ?>
            </p>
        </div>

    <section class="news">
        <article>
            <a href="#">
                <h2 class="new">Акция! Бесплатный массаж лица при первом посещении!</h2>
            </a>
            <div class="article-meta">
                Опубликовал <a href="#">Иванова Яна</a>
                26.06.2033
            </div>
            <img src="https://images.unsplash.com/photo-1616394584738-fc6e612e71b9?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80" alt="train">
            <p>Далеко-далеко за словесными горами в стране гласных и согласных, живут рыбные тексты. Лучше однажды вопрос что ведущими...</p>
            <a href="#">Читать далее</ф>
        </article>
    
        <article>
            <a href="#">
                <h2 class="new">Подарки всегда желанны</h2>
            </a>
            <div class="article-meta">
                Опубликовал <a href="#">Иванова Яна</a>
                20.06.2033
            </div>
            <img src="https://images.unsplash.com/photo-1556760544-74068565f05c?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80" alt="train">
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ex voluptatum provident similique tempora nam laboriosam.</p>
            <a href="#">Читать далее</a>
        </article>
    
        <article>
            <a href="#">
                <h2>Лекарства по рецепторам</h2>
            </a>
            <div class="article-meta">
                Опубликовал <a href="#">Иванова Яна</a>
                18.03.2033
            </div>
            <img src="https://images.unsplash.com/photo-1494194069000-cb794f31d82c?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80" alt="train">
            <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Enim ad harum eos placeat ab doloremque!</p>
            <a href="#">Читать далее</a>
        </article>
    
        <article>
            <a href="#">
                <h2>Навес золота</h2>
            </a>
            <div class="article-meta">
                Опубликовал <a href="#">Иванова Яна</a>
                12.02.2033
            </div>
            <img src="https://images.unsplash.com/photo-1596740926849-2d473dee8d60?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80" alt="train">
            <p>Далеко-далеко за словесными горами, в стране гласных и согласных живут рыбные тексты. Запятых рот текст собрал образ, возвращайся дал меня. Рот, осталось.</p>
            <a href="#">Читать далее</a>
        </article>
    
        <article>
            <a href="#">
                <h2>Люди особой профессии</h2>
            </a>
            <div class="article-meta">
                Опубликовал <a href="#">Иванова Яна</a>
                18.12.2032
            </div>
            <img src="https://images.unsplash.com/photo-1630835425197-50feeba99ecd?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80" alt="train">
            <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Voluptas ipsam voluptates temporibus, non ipsum voluptatum? Debitis ipsam, alias natus quidem eligendi eveniet ducimus sunt mollitia commodi, impedit cum praesentium facilis.</p>
            <a href="#">Читать далее</a>
        </article>

        <article>
            <a href="#">
                <h2>Наши новинки в мире масел</h2>
            </a>
            <div class="article-meta">
                Опубликовал <a href="#">Иванова Яна</a>
                11.12.2032
            </div>
            <img src="https://images.unsplash.com/photo-1621554258209-a4a4305e29e5?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1074&q=80" alt="train">
            <p>Далеко-далеко за словесными, горами в стране гласных и согласных живут.</p>
            <a href="#">Читать далее</a>
        </article>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="header-top">
                <nav class="menu">
                    <ul class="menu__list menu__list-footer">
                        <li class="menu__list-item">
                            <a class="menu__list-link" href=#>О нас</a>
                        </li>
                        <li class="menu__list-item">
                            <a class="menu__list-link" href=#>Акции</a>
                        </li>
                        <li class="menu__list-item">
                            <a class="menu__list-link" href=#>Цены</a>
                        </li>
                        <li class="menu__list-item">
                            <a class="menu__list-link" href=#>Контакты</a>
                        </li>
                    </ul>
                </nav>
            </div>
            <div class="copyright">Все права защищены &copy;</div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <script>

        function open_formLog() {
            document.querySelector("#formLog").style.display = "block"; 
        }

        function close_formLog() {
            let forms = document.querySelectorAll("#formLog");
            forms.forEach (elem => elem.style.display = "none");    
        }

        function open_formReg() {
            document.querySelector("#formReg").style.display = "block"; 
        }

        function close_formReg() {
            let forms = document.querySelectorAll("#formReg");
            forms.forEach (elem => elem.style.display = "none");
        }

    </script>
    
</body>
</html>