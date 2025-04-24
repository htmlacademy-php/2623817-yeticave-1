<?php

require_once('db/DBFunctions.php');

/**
 * Summary of get_layout_html
 * @param string $pageTitle
 * @param string $mainContent
 * @param mixed $search
 * @return string
 */
function get_layout_html(string $pageTitle, string $mainContent, $search = ''): string
{
    //Получение данных страницы
    $mysqlConnection = db_get_connection();
    if (!$mysqlConnection) {
        http_response_code(500); // переделано, чтобы возвращало ошибку
        exit();
    }

    //Список категорий{
    $dbCategoryList = db_get_category_list($mysqlConnection);
    $categoryList = array_column($dbCategoryList, 'name', 'label');
    //}Список категорий
    db_close_connection($mysqlConnection);

    if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
    }

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

/**
 * Summary of set_error
 * @param mixed $errors
 * @param string $fieldName
 * @param bool $isError
 * @param string $errorMessage
 * @return void
 */
function set_error(&$errors, string $fieldName, bool $isError, string $errorMessage)
{
    $fieldError = &$errors[$fieldName];
    $fieldError['IsError'] = $isError;
    $fieldError['errorDescription'] = ($fieldError['errorDescription'] ?? '') . $errorMessage;
}
