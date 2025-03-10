<?php

require_once('testData.php');
require_once('helpers.php');
require_once('db/DBFunctions.php');

//Проверка параметров запроса

//Получение данных страницы
$mysqlConnection = db_get_connection();
if (!$mysqlConnection) {
    http_response_code(500);
    exit();
}
//Список категорий{
$dbCategoryList = db_get_category_list($mysqlConnection);
$categoryList = array_column($dbCategoryList, 'name', 'id');

db_close_connection($mysqlConnection);

//Вывод страницы
//подготовка блока main
$addlotPageParam = [
    
];
$addlotPageHTML = include_template('tmp_add-lot.php', $addlotPageParam);


//подготовка блока layout
$layoutData = [
    'pageTitle' => 'Добавление лота',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'mainContent' => $addlotPageHTML,
    'categoryList' => $categoryList
];
$layoutPageHTML = include_template('layout.php', $layoutData);

print ($layoutPageHTML);
?>