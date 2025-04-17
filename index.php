<?php
require_once('helpers.php');
require_once('db/DBFunctions.php');
require_once('layout.php');
require("getwinner.php");

if (session_status() != PHP_SESSION_ACTIVE) 
    session_start();

//Получение данных страницы
$mysqlConnection = db_get_connection();
if (!$mysqlConnection){
    http_response_code(500); // переделано, чтобы возвращало ошибку
    exit();
}

//Список категорий{
$dbCategoryList = db_get_category_list($mysqlConnection);
$categoryList = array_column($dbCategoryList,'name','id');
//}Список категорий

//Список товаров{
// Если в строке пришел параметр category - отобрать товары по нужной категории
$queryParam = [];
$paramCategory = filter_input(INPUT_GET,'category',FILTER_UNSAFE_RAW);
if($paramCategory != null){ 
    //Проверить, есть ли такая категория
    if(isset($categoryList[$paramCategory])){
        $queryParam['category'] = $paramCategory;
    }
    else{ 
        http_response_code(404);  
        exit();
    }
    
}

$dbItemList = db_get_item_list($mysqlConnection,$queryParam);
$itemList = $dbItemList;
//}Список товаров

db_close_connection($mysqlConnection);

//Вывод страницы
//подготовка блока main
$mainData = [
    'categoryList' => $categoryList,
    'itemList' => $itemList   
];
$mainPageHTML = include_template('main.php',$mainData);


//подготовка блока layout
$mainPageHTML = get_layout_html('Главная страница',$mainPageHTML);

Print($mainPageHTML);
?>



