<?php

/**
 * Summary of get_value_in_money_type
 * @param float $value
 * @return string
 */
function get_value_in_money_type(float $value): string
{

    //Получаем целую часть числа
    $intValue = ceil($value);
    $result = (string) $intValue;

    //Добавляем группировку цифр, если надо
    if ($intValue >= 1000) {
        $result = number_format($intValue, 0, ".", ' ');
    }

    //Добавляем знак рубля
    $result .= " ₽";

    return $result;
}

/**
 * Summary of get_expire_time
 * @param string $strExpireDate
 * @return array{hours: int, minutes: int}
 */
function get_expire_time(string $strExpireDate): array
{
    if (strlen($strExpireDate) === 0) {
        return [
            'hours' => 0, // Количество часов
            'minutes' => 0//Количество минут
        ];
    }
    $expireDate = new DateTime($strExpireDate);
    $currentDate = new DateTime("now");
    $expireTime = date_diff($currentDate, $expireDate);
    return [
        'hours' => ($expireTime->days * 24 + $expireTime->h) * ($expireTime->invert ? 0 : 1), // Количество часов
        'minutes' => $expireTime->i * ($expireTime->invert ? 0 : 1)//Количество минут
    ];
}

/**
 * Summary of get_new_url
 * @param array $newParam
 * @return string
 */
function get_new_url(array $newParam): string
{

    // Текущий URL
    $current_url = $_SERVER['REQUEST_URI'];
    $parsed_url = parse_url($current_url);
    parse_str($parsed_url['query'] ?? '', $query_params);

    foreach ($newParam as $key => $value) {
        $query_params[$key] = $value;
    }

    $new_query_string = http_build_query($query_params);
    $new_url = ($parsed_url['path'] ?? '') . '?' . $new_query_string;
    if (isset($parsed_url['fragment'])) {
        $new_url .= '#' . $parsed_url['fragment'];
    }

    return $new_url;
}

/**
 * Summary of get_date_diff_string
 * @param string $date1
 * @param string $date2
 * @return string
 */
function get_date_diff_string(string $date1, string $date2): string
{
    $date1timestamp = strtotime($date1);
    $date2timestamp = strtotime($date2);

    $diff = $date2timestamp - $date1timestamp;
    if ($diff < 60) {
        return 'Только что';
    } elseif ($diff < (60 * 60)) {
        $minutes = floor($diff / 60);
        return "$minutes " . get_noun_plural_form($minutes, 'минута', 'минуты', 'минут') . ' назад';
    } elseif ($diff < (60 * 60 * 24)) {
        $hours = floor($diff / (60 * 60));
        return "$hours " . get_noun_plural_form($hours, 'час', 'часа', 'часов') . ' назад';
    } else {
        return date("d.m.y в H:i", $date1timestamp);
    }
}
