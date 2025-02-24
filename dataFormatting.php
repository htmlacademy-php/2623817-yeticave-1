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

?>