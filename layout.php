<?php
require_once('db/DBFunctions.php');

function get_layout_html(string $pageTitle, string $mainContent)
{
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
    db_close_connection($mysqlConnection);

    if (session_status() != PHP_SESSION_ACTIVE)
        session_start();

    $sessionIsActive = isset($_SESSION['id']);
    $layoutData = [
        'pageTitle' => $pageTitle,
        'is_auth' => $sessionIsActive,
        'user_name' => $sessionIsActive ? $_SESSION['name'] : '',
        'mainContent' => $mainContent,
        'categoryList' => $categoryList
    ];
    return include_template('layout.php', $layoutData);
}
?>