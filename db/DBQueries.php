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

    'getItemListLimit' => "SELECT
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
    order by created_at DESC
    LIMIT ? OFFSET ?",

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
        lots.id = ?",

    'addItem' => 'INSERT INTO yeticave_1.lots(
    created_at,
    name,
    description,
    image_path,
    start_price,
    expiration_date,
    price_step,
    author_id,
    winner_id,
    category_id)
    VALUES (now(),?,?,?,?,?,?,?,?,?)',

    'getUserByEmail' => 'SELECT
        users.id as id,
        users.password as password,
        users.name as name,
        users.email as email
    FROM users
    WHERE
        users.email = ? LIMIT 1',

    'getUserById' => 'SELECT
        users.id as id,
        users.password as password,
        users.name as name,
        users.email as email
    FROM yeticave_1.users as users
    WHERE
        users.id = ?',

    'addUser' => 'INSERT INTO yeticave_1.users(
    email,
    name,
    password,
    contact_info,
    created_at)
    VALUES (?,?,?,?,now())',

    'getItemListFts' => "SELECT
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
        AND &setFtsCondition
    order by created_at DESC",

    'getItemListFtsLimit' => "SELECT
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
        AND &setFtsCondition
    order by created_at DESC
    LIMIT ? OFFSET ?",

    'addBet' => 'INSERT INTO yeticave_1.bets(
        date,
        price,
        user_id,
        lot_id)
    VALUES (now(),?,?,?)',

    'getBetsListByUser' => 'SELECT
	    bets.date,
 	    bets.price,
        bets.lot_id as lot_id,
        lots.name as lot_name,
        lots.image_path as lot_image_path,
        lots.expiration_date as lot_expiration_date,
        lots.winner_id as lot_winner_id,
        lots.category_id as lot_category_id,
        categories.name as lot_category_name,
        author.contact_info as author_contact_info,
        lots.winner_id as winner_id
    FROM yeticave_1.bets as bets
    INNER JOIN lots as lots
        LEFT JOIN yeticave_1.categories as categories
    	    ON lots.category_id = categories.id
        LEFT JOIN yeticave_1.users as author
        	ON lots.author_id = author.id
        ON bets.lot_id = lots.id

    WHERE bets.user_id = ? and
        &setCategoryCondition
    ORDER BY bets.date DESC',

    'getBetsListByLot' => 'SELECT
	    bets.date,
 	    bets.price,
        users.name as user_name,
        bets.user_id as user_id
    FROM yeticave_1.bets as bets
   	    INNER JOIN users as users
     	    ON bets.user_id = users.id
    WHERE bets.lot_id = ?
    ORDER BY bets.date DESC',

    'getLotsToSetWinner' => 'SELECT
	    lot_id as lot_id,
	    tmp_bets.user_id as winner_id,
        lots.name as lot_name
    FROM lots
    INNER JOIN (
        SELECT bets.*
        FROM bets
        INNER JOIN (
           SELECT
            	bets.lot_id as lot_id,
    	        MAX(bets.price) as price
            FROM BETS
            GROUP BY bets.lot_id)as max_bets
	        ON bets.lot_id = max_bets.lot_id
		       AND bets.price = max_bets.price
	        ORDER BY bets.date) as tmp_bets
    ON lots.id = tmp_bets.lot_id
    WHERE lots.winner_id IS NULL
    AND lots.expiration_date < now()',

    'setLotWinner' => 'UPDATE yeticave_1.lots
    SET yeticave_1.lots.winner_id = ?
    WHERE yeticave_1.lots.id = ?',
]);
