<?php

require_once('helpers.php');
require_once('db/DBFunctions.php');
require_once('layout.php');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
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

//Список товаров{
// Если в строке пришел параметр category - отобрать товары по нужной категории
$queryParam = [];
$paramFind = filter_input(INPUT_GET, 'find', FILTER_UNSAFE_RAW);
$paramSearch = filter_input(INPUT_GET, 'search', FILTER_UNSAFE_RAW);
if (empty($paramFind) || empty($paramSearch)) {
    header('Location: index.php');
} else {
    $queryParam['search'] = trim($paramSearch);
}
$paramCategory = filter_input(INPUT_GET, 'category', FILTER_UNSAFE_RAW);
if ($paramCategory !== null) {
    //Проверить, есть ли такая категория
    if (isset($categoryList[$paramCategory])) {
        $queryParam['category'] = $paramCategory;
    } else {
        http_response_code(404);
        exit();
    }
}

$itemList = [];
if (isset($queryParam['search'])) {
    $dbItemList = db_get_item_list_fts($mysqlConnection, $queryParam);
    $itemList = $dbItemList;
}

//Пагинация
$numberOfPages = 1;
$currentPage = 1;
if (count($itemList) > 0) {
    //Посчитать количество страниц
    $numberOfPages = ceil(count($itemList) / DB_SEARCH_NUMBER_OF_ITEMS_ON_PAGE);
    $paramPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    $queryParam['offset'] = is_int($paramPage) ? $paramPage - 1 : 0;
    $currentPage = $queryParam['offset'] + 1;
    if ($currentPage > $numberOfPages || $currentPage < 1) {
        http_response_code(404);
        exit();
    }
    //Получить ограниченное количество товаров
    $dbItemList = db_get_item_list_fts_limit($mysqlConnection, $queryParam);
    $itemList = $dbItemList;
}
//}Список товаров

db_close_connection($mysqlConnection);

//Вывод страницы
//подготовка блока main
$mainData = [
    'categoryList' => $categoryList,
    'itemList' => $itemList,
    'paramFind' => $paramFind,
    'paramSearch' => $paramSearch,
    'paramCategory' => $paramCategory,
    'numberOfPages' => $numberOfPages,
    'currentPage' => $currentPage

];
$mainPageHTML = include_template('search.php', $mainData);


//подготовка блока layout
$mainPageHTML = get_layout_html('Cтраница поиска', $mainPageHTML, $paramSearch);

print($mainPageHTML);
