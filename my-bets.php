<?php

require_once('helpers.php');
require_once('db/DBFunctions.php');
require_once('layout.php');

if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION['id'])) {
        http_response_code(403);
       exit();
}
//Получение данных страницы
$mysqlConnection = db_get_connection();
if (!$mysqlConnection) {
    http_response_code(500); // переделано, чтобы возвращало ошибку
    exit();
}

//Список категорий{
$dbCategoryList = db_get_category_list($mysqlConnection);
$categoryList = array_column($dbCategoryList, 'name', 'id');
//}Список категорий

$queryParam = ['user_id' => $_SESSION['id']];
$paramCategory = filter_input(INPUT_GET, 'category', FILTER_UNSAFE_RAW);
if ($paramCategory != null) {
    //Проверить, есть ли такая категория
    if (isset($categoryList[$paramCategory])) {
        $queryParam['category'] = $paramCategory;
    } else {
        http_response_code(404);
        exit();
    }
}

//Список ставок{
$dbMyBetsList = db_get_bets_by_user($mysqlConnection, $queryParam);
//}Список ставок

db_close_connection($mysqlConnection);

//Вывод страницы
//подготовка блока main
$myBetsData = [
    'categoryList' => $categoryList,
    'myBetsList' => $dbMyBetsList
];
$myBetPageHTML = include_template('my-bets.php', $myBetsData);


//подготовка блока layout
$mainPageHTML = get_layout_html('Мои ставки', $myBetPageHTML);

print($mainPageHTML);
