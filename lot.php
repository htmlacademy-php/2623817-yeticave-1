<?php

require_once('helpers.php');
require_once('db/DBFunctions.php');
require_once('layout.php');

if (session_status() != PHP_SESSION_ACTIVE) 
    session_start();

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
$sessionIsActive = isset($_SESSION['id']);
$lotPageParam = [
    'item'=> $dbItem,
    'is_auth' => $sessionIsActive
];
$lotPageHTML = include_template('tmp_lot.php', $lotPageParam);


//подготовка блока layout
$layoutPageHTML = get_layout_html($dbItem['lot_name'],$lotPageHTML);

print ($layoutPageHTML);
?>