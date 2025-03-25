<?php
require_once('db/DBFunctions.php');

function get_layout_html(string $pageTitle, string $mainContent, $search = '')
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
        'isAuth' => $sessionIsActive,
        'userName' => $sessionIsActive ? $_SESSION['name'] : '',
        'mainContent' => $mainContent,
        'categoryList' => $categoryList,
        'searchParam' => $search
    ];
    return include_template('layout.php', $layoutData);
}
?>