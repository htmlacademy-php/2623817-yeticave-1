<?php

require_once('testData.php');
require_once('helpers.php');
require_once('db/DBFunctions.php');

//Проверка параметров запроса
$queryParam = [];
$ParamId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($ParamId == null) {
    http_response_code(404);
    exit();
}
$queryParam['id'] = $ParamId;

//Получение данных страницы
$mysqlConnection = db_get_connection();
if (!$mysqlConnection) {
    http_response_code(500);
    exit();
}
//Список категорий{
$dbCategoryList = db_get_category_list($mysqlConnection);
$categoryList = array_column($dbCategoryList, 'name', 'id');

// информация о лоте
$dbItemArray = db_get_item($mysqlConnection, $queryParam);
db_close_connection($mysqlConnection);

if (count($dbItemArray) == 0) {
    http_response_code(404);
    exit();
}
$dbItem = $dbItemArray[0];

//Вывод страницы
//подготовка блока main
$lotPageParam = [
    'item'=> $dbItem,
];
$lotPageHTML = include_template('tmp_lot.php', $lotPageParam);


//подготовка блока layout
$layoutData = [
    'pageTitle' => $dbItem['lot_name'],
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'mainContent' => $lotPageHTML,
    'categoryList' => $categoryList
];
$layoutPageHTML = include_template('layout.php', $layoutData);

print ($layoutPageHTML);
?>