<?php
$is_auth = rand(0, 1);

$user_name = 'Mikhail Maiseyenka';

$categoryList = [
    'boards'        =>'Доски и лыжи',
    'attachment'    => 'Крепления',
    'boots'         => 'Ботинки', 
    'clothing'      => 'Одежда', 
    'tools'         => 'Инструменты', 
    'other'         => 'Разное'
];

$itemList =[
    //По хорошему, цену числом бы переделать, чтобы убрать неявное преобразование
    //при форматировании, но пока оставлим так
    [
        'name' => '2014 Rossignol District Snowboard',
        'category' => 'boards',
        'price' => '1099',
        'imgPath' =>'img/lot-1.jpg'
    ],
    [
        'name' => 'DC Ply Mens 2016/2017 Snowboard',
        'category' => 'boards',
        'price' => '159999',
        'imgPath' =>'img/lot-2.jpg'
    ],
    [
        'name' => 'Крепления Union Contact Pro 2015 года размер L/XL',
        'category' => 'attachment',
        'price' => '8000',
        'imgPath' =>'img/lot-3.jpg'
    ],
    [
        'name' => 'Ботинки для сноуборда DC Mutiny Charocal',
        'category' => 'boots',
        'price' => '10999',
        'imgPath' =>'img/lot-4.jpg'
    ],
    [
        'name' => 'Куртка для сноуборда DC Mutiny Charocal',
        'category' => 'clothing',
        'price' => '7500',
        'imgPath' =>'img/lot-5.jpg'
    ],
    [
        'name' => 'Маска Oakley Canopy',
        'category' => 'other',
        'price' => '5400',
        'imgPath' =>'img/lot-6.jpg'
    ]    
];
?>