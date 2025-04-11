<?php

require_once('DBConfig.php');
require_once('DBQueries.php');


//Общие функции работы с БД
function db_get_connection()
{
    mysqli_report(MYSQLI_REPORT_OFF);
    $connection = mysqli_connect(DB_CONFIG['hostname'], DB_CONFIG['login'], DB_CONFIG['password'], DB_CONFIG['database']);
    if ($connection) {
        return $connection;
    }

    echo "Ошибка подключения к БД: " . mysqli_connect_error();
    return false;
}

function db_close_connection(mysqli $connection)
{
    if ($connection)
        mysqli_close($connection);
}

function db_query_execute_array(mysqli $connection, $query, array $queryParam)
{

    if (!$connection) {
        return [];
    }
    ;

    //Подготовить запрос 
    $stmt = mysqli_prepare($connection, $query);
    if (!$stmt) {
        echo "Ошибка выполнения запроса к БД: " . mysqli_error($connection);
        return [];
    }
    //подставить параметры
    //по не актуально
    if(isset($queryParam['type']) && is_array($queryParam['value'])){
        $paramTypes = $queryParam['type'];
        if(is_string($paramTypes) && strlen($paramTypes) > 0){
            $bindResult = mysqli_stmt_bind_param($stmt, $queryParam['type'], ...$queryParam['value']);
            if (!$bindResult) {
                echo "Ошибка выполнения запроса к БД: " . mysqli_stmt_error($stmt);
                return [];
            }
        }
    }

    //Выполнить запрос
    $executeResult = mysqli_stmt_execute($stmt);
    if (!$executeResult) {
        echo "Ошибка выполнения запроса к БД: " . mysqli_stmt_error($stmt);
        return [];
    }

    //Вернуть результат 
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        echo "Ошибка выполнения запроса к БД: " . mysqli_stmt_error($stmt);
        return [];
    }
    //извлечь результат в массив
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return $rows;

}

function db_query_execute_bool(mysqli $connection, $query, array $queryParam)
{

    if (!$connection) {
        return false;
    }
    ;

    //Подготовить запрос 
    $stmt = mysqli_prepare($connection, $query);
    if (!$stmt) {
        echo "Ошибка выполнения запроса к БД: " . mysqli_error($connection);
        return false;
    }
    //подставить параметры
    //по не актуально
    if(isset($queryParam['type']) && is_array($queryParam['value'])){
        $paramTypes = $queryParam['type'];
        if(is_string($paramTypes) && strlen($paramTypes) > 0){
            $bindResult = mysqli_stmt_bind_param($stmt, $queryParam['type'], ...$queryParam['value']);
            if (!$bindResult) {
                echo "Ошибка выполнения запроса к БД: " . mysqli_stmt_error($stmt);
                return false;
            }
        }
    }

    //Выполнить запрос
    $executeResult = mysqli_stmt_execute($stmt);
    if (!$executeResult) {
        echo "Ошибка выполнения запроса к БД: " . mysqli_stmt_error($stmt);
        return false;
    }

    return true;

}


//Прикладные функции получения данных
function db_get_category_list(mysqli $connection): array
{

    $query = DB_QUERIES['getCategoryList'];

    return db_query_execute_array($connection, $query, []);

}

function db_get_item_list(mysqli $connection, array $param): array
{

    $query = DB_QUERIES['getItemList'];
    $queryParam = [
        'type' => '',
        'value' => []];
    if (isset($param['category'])) {
        $query  = str_replace('&setIdCondition','lots.category_id = ?',$query);
        //Добавить параметр Категория
        $queryParam['type'] .= 's';
        $queryParam['value'][] = $param['category'];
    } else {
        $query  = str_replace('&setIdCondition','TRUE',$query);   
    }
    return db_query_execute_array($connection, $query, $queryParam);

}

function db_get_item_list_fts(mysqli $connection, array $param): array
{

    $query = DB_QUERIES['getItemListFts'];
    $queryParam = [
        'type' => '',
        'value' => []];

    if (isset($param['category'])) {
        $query  = str_replace('&setIdCondition','lots.category_id = ?',$query);
        //Добавить параметр Категория
        $queryParam['type'] .= 's';
        $queryParam['value'][] = $param['category'];
    } else {
        $query  = str_replace('&setIdCondition','TRUE',$query);   
    }
    if (isset($param['search'])) {
        $query  = str_replace('&setFtsCondition','MATCH(lots.name,lots.description) AGAINST(? IN NATURAL LANGUAGE MODE)',$query);
        //Добавить параметр Категория
        $queryParam['type'] .= 's';
        $queryParam['value'][] = $param['search'];
    } else {
        $query  = str_replace('&setFtsCondition','TRUE',$query);   
    }
    return db_query_execute_array($connection, $query, $queryParam);

}

function db_get_item_list_fts_limit(mysqli $connection, array $param): array
{

    $query = DB_QUERIES['getItemListFtsLimit'];
    $queryParam = [
        'type' => '',
        'value' => []];

    if (isset($param['category'])) {
        $query  = str_replace('&setIdCondition','lots.category_id = ?',$query);
        //Добавить параметр Категория
        $queryParam['type'] .= 's';
        $queryParam['value'][] = $param['category'];
    } else {
        $query  = str_replace('&setIdCondition','TRUE',$query);   
    }
    if (isset($param['search'])) {
        $query  = str_replace('&setFtsCondition','MATCH(lots.name,lots.description) AGAINST(? IN NATURAL LANGUAGE MODE)',$query);
        //Добавить параметр Категория
        $queryParam['type'] .= 's';
        $queryParam['value'][] = $param['search'];
    } else {
        $query  = str_replace('&setFtsCondition','TRUE',$query);   
    }

    //Limit
    $queryParam['type'] .= 'i';
    $queryParam['value'][] = DB_SEARCH_NUMBER_OF_ITEMS_ON_PAGE;

    //offset
    if (isset($param['offset'])) {
        $queryParam['type'] .= 'i';
        $queryParam['value'][] = $param['offset'];
    }else {
        $queryParam['type'] .= 'i';
        $queryParam['value'][] = 0;
    }

    return db_query_execute_array($connection, $query, $queryParam);

}

function db_get_item(mysqli $connection, array $param): array
{

    $query = DB_QUERIES['getItem'];
    $queryParam = [
        'type' => '',
        'value' => []];
    if (isset($param['id'])) {
        //Добавить параметр ID
        $queryParam['type'] .= 'i';
        $queryParam['value'][] = $param['id'];
    }
    else{
        return [];
    }
    return db_query_execute_array($connection, $query, $queryParam);

}

function db_get_user_by_email(mysqli $connection, array $param): array
{

    $query = DB_QUERIES['getUserByEmail'];
    $queryParam = [
        'type' => '',
        'value' => []];
    if (isset($param['email'])) {
        //Добавить параметр ID
        $queryParam['type'] .= 's';
        $queryParam['value'][] = $param['email'];
    }
    else{
        return [];
    }
    return db_query_execute_array($connection, $query, $queryParam);

}

function db_get_bets_by_user(mysqli $connection, array $param): array
{

    $query = DB_QUERIES['getBetsListByUser'];
    $queryParam = [
        'type' => 'i',
        'value' => []
    ];
    if (isset($param['user_id'])) {
        $queryParam['value'][] = $param['user_id'];
    } else {
        return [];
    }
    if (isset($param['category'])) {
        $query = str_replace('&setCategoryCondition', 'lots.category_id = ?', $query);
        //Добавить параметр Категория
        $queryParam['type'] .= 's';
        $queryParam['value'][] = $param['category'];
    } else {
        $query = str_replace('&setCategoryCondition', 'TRUE', $query);
    }
    return db_query_execute_array($connection, $query, $queryParam);

}

function db_get_bets_by_lot(mysqli $connection, array $param): array
{

    $query = DB_QUERIES['getBetsListByLot'];
    $queryParam = [
        'type' => 'i',
        'value' => []
    ];
    if (isset($param['lot_id'])) {
        $queryParam['value'][] = $param['lot_id'];
    } else {
        return [];
    }
    return db_query_execute_array($connection, $query, $queryParam);

}

function db_add_item(mysqli $connection, array $param): bool
{

    $query = DB_QUERIES['addItem'];
    $queryParam = [
        'type' => 'sssisiiis',
        'value' => $param
    ];

    return db_query_execute_bool($connection, $query, $queryParam);

}

function db_add_user(mysqli $connection, array $param): bool
{

    $query = DB_QUERIES['addUser'];
    $queryParam = [
        'type' => 'ssss',
        'value' => $param
    ];

    return db_query_execute_bool($connection, $query, $queryParam);

}

function db_add_bet(mysqli $connection, array $param): bool
{

    $query = DB_QUERIES['addBet'];
    $queryParam = [
        'type' => 'iii',
        'value' => $param
    ];

    return db_query_execute_bool($connection, $query, $queryParam);

}

function db_get_add_item_params($name, $description, $image_path, $start_price, $expiration_date, $price_step, $author_id, $winner_id, $category_id): array
{

    return [$name, $description, $image_path, $start_price, $expiration_date, $price_step, $author_id, $winner_id, $category_id];

}

function db_get_add_user_params($email, $name, $password, $message): array
{

    return [$email, $name, $password, $message];

}
function db_get_add_bet_params(int $lotId, int $price, int $userId): array
{

    return [$price, $userId, $lotId];

}

?>