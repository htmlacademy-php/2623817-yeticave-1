<?php
require_once('testData.php');
require_once('helpers.php');
require_once('db/DBFunctions.php');

$mysqlConnection = db_get_connection();
if (!$mysqlConnection){
    echo 'error'; // Сюда бы вывести страницу с ошибкой, но такую не нашел
    return;
}

$dbCategoryList = db_get_category_list($mysqlConnection);
$dbItemList = db_get_item_list($mysqlConnection);
db_close_connection($mysqlConnection);

$categoryList = array_column($dbCategoryList,'name','id');
$itemList = $dbItemList;

//подготовка блока main
$mainData = [
    'categoryList' => $categoryList,
    'itemList' => $itemList   
];
$mainPageHTML = include_template('main.php',$mainData);


//подготовка блока layout
$layoutData = $mainData;
$layoutData = ['pageTitle' => 'Главная страница',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'mainContent' => $mainPageHTML,
    'categoryList' => $categoryList];
$mainPageHTML = include_template('layout.php',$layoutData);

Print($mainPageHTML);
?>



