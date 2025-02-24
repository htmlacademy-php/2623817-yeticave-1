<?php
function get_value_in_money_type (float $value): string {

    //Получаем целую часть числа
    $intValue = ceil($value);
    $result = (string) $intValue;

    //Добавляем группировку цифр, если надо
    if ($intValue >= 1000) {
        $result = number_format($intValue,0,".",' ');   
    };

    //Добавляем знак рубля
    $result .= " ₽";

    return $result;
}

function get_expire_time(string $strExpireDate): array{
    if(strlen($strExpireDate)<0){
        return[
            'hours' => 0, // Количество часов
            'minutes' => 0//Количество минут
        ];
    }
    $expireDate = new DateTime($strExpireDate);
    $currentDate = new DateTime("now");
    $expireTime = date_diff($currentDate, $expireDate,false);
    return [
       'hours' => ($expireTime->days * 24 + $expireTime->h) * ($expireTime->invert?0:1), // Количество часов
       'minutes' => $expireTime->i  * ($expireTime->invert?0:1)//Количество минут
    ];
}
?>