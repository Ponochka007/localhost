<?php
    //названия месяцев в родительном падеже
    $monthsRus = ["January" => "января", "February" => "февраля", "March" => "марта", 
    "April" => "апреля", "May" => "мая", "June" => "июня", "July" => "июля", "August" => "августа", 
    "September" => "сентября", "October" => "октября", "November" => "ноября", "December" => "декабря"];

    //возвращает дату в привычном русском формате
    function getRussianDate ($engDate) {
        
        global $monthsRus;
        $temp = explode(" ", $engDate);
        $day = $temp[0];
        $month = $temp[1];
        return mb_substr($day, 0, -2) . " " . $monthsRus[$month];
    }

    //возвращается правильную форму слова дни в зависимости от количества
    function dayEnding ($days) {
        
        $tensOnes = $days % 100;
        
        if (($tensOnes >= 11) && ($tensOnes <= 14)) return "дней";
        
        if (($tensOnes < 11) || ($tensOnes > 14)) {
            if ($tensOnes % 10 == 1) {
                return "день";
            } elseif (($tensOnes % 10 == 2) || ($tensOnes % 10 == 3) || ($tensOnes % 10 == 4)) {
                return "дня";
            } else return "дней";

        }
    }

    //проверяет, если ли на услугу скидка
    function hasDiscount ($discount) {
        
        if ($discount > 0) 
            return true;
        else 
            return false;    
    }

    //вычисляет суммарную скидку
    function getDiscount ($oldDiscount) {
    
        global $discount_active;
        global $days_until_birthday;

        $discountSum = $oldDiscount;

        if (isset($days_until_birthday)) {
            if ($days_until_birthday == 0) {
                $discountSum += 5;
            }
        }

        if ($discount_active) {
            $discountSum += 7;
        }

        return $discountSum;
    }

    //вычисляем новую цену в зависимости от скидки
    function getNewPrice ($oldprice, $discount) {
        return $oldprice * (1 - $discount * 0.01);
    }

?>