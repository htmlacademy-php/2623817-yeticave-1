<?php

define("DB_QUERIES", [

    //Получение списка категорий
    'getCategoryList' => "SELECT categories.id as id, categories.name as name 
        FROM yeticave_1.categories as categories;",

    //Получение списка открытых лотов. 
    //Считаем, что лот открыт, если не настала дата окончания
    //Считаем, что цену = максимальная ставка. если ставок нет цена = первоначальная цена
    'getItemList' => "SELECT 
        lots.id as lot_id,
        lots.name as lot_name,
        lots.start_price as start_price,
        lots.image_path as image_path,
        categories.name as category,
        lots.category_id as category_id,
        lots.expiration_date as expiration_date,
        IFNULL(prices.price,lots.start_price) as price -- можно сделать 'prices.price + lots.price_step', если нужна последняя ставка + шаг
    FROM yeticave_1.lots as lots
        LEFT JOIN yeticave_1.categories as categories ON
            lots.category_id = categories.id
        LEFT JOIN (SELECT
            bets.lot_id as lot_id,
            max(bets.price) as price
            FROM yeticave_1.bets as bets
            GROUP BY bets.lot_id) as prices ON 
            lots.id = prices.lot_id
    WHERE
        lots.expiration_date > NOW() 
        AND &setIdCondition
    order by created_at DESC",

    'getItem' => "SELECT 
        lots.id as lot_id,
        lots.created_at as lot_created_at,
        lots.name as lot_name,
        lots.description as lot_description,
        lots.image_path as lot_image_path,
        lots.start_price as lot_start_price,
        lots.expiration_date as lot_expiration_date,
        lots.price_step as lot_price_step,
        lots.author_id as lot_author_id,
        lots.winner_id as lot_winner_id,
        lots.category_id as category_id,
        categories.name as category,
        IFNULL(prices.price,lots.start_price) as price,
        IFNULL(prices.price + lots.price_step,lots.start_price) as min_bet
    FROM yeticave_1.lots as lots
    LEFT JOIN yeticave_1.categories as categories ON
        lots.category_id = categories.id
    LEFT JOIN (SELECT
            bets.lot_id as lot_id,
            max(bets.price) as price
            FROM yeticave_1.bets as bets
            GROUP BY bets.lot_id) as prices ON 
            lots.id = prices.lot_id
    WHERE
        lots.id = ?"
]);

?>