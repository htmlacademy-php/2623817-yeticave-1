<?php
function get_value_in_money_type(float $value): string
{

    //Получаем целую часть числа
    $intValue = ceil($value);
    $result = (string) $intValue;

    //Добавляем группировку цифр, если надо
    if ($intValue >= 1000) {
        $result = number_format($intValue, 0, ".", ' ');
    }
    ;

    //Добавляем знак рубля
    $result .= " ₽";

    return $result;
}

function get_expire_time(string $strExpireDate): array
{
    if (strlen($strExpireDate) < 0) {
        return [
            'hours' => 0, // Количество часов
            'minutes' => 0//Количество минут
        ];
    }
    $expireDate = new DateTime($strExpireDate);
    $currentDate = new DateTime("now");
    $expireTime = date_diff($currentDate, $expireDate, false);
    return [
        'hours' => ($expireTime->days * 24 + $expireTime->h) * ($expireTime->invert ? 0 : 1), // Количество часов
        'minutes' => $expireTime->i * ($expireTime->invert ? 0 : 1)//Количество минут
    ];
}

function get_new_url(array $newParam)
{

    // Текущий URL
    $current_url = $_SERVER['REQUEST_URI'];
    $parsed_url = parse_url($current_url);
    parse_str($parsed_url['query'], $query_params);

    foreach ($newParam as $key => $value)
        $query_params[$key] = $value;

    $new_query_string = http_build_query($query_params);
    $new_url = $parsed_url['path'] . '?' . $new_query_string;
    if (isset($parsed_url['fragment'])) {
        $new_url .= '#' . $parsed_url['fragment'];
    }

    return $new_url;

}

?>