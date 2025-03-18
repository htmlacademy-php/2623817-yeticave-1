<?php

require_once('testData.php');
require_once('helpers.php');
require_once('db/DBFunctions.php');

$formError = false;
$formData = [];
$FieldNames = [
    'email' => 'E-mail',
    'password' => 'Пароль'
];

$requiredFieldNames = [
    'email' => function ($formData, $fieldName) {
        return empty($formData[$fieldName]);
    },
    'password' => function ($formData, $fieldName) {
        return empty($formData[$fieldName]);
    }
];
$validateFunctions = [
    'email' => [
        'function' => function ($value, $params = []) {
            return filter_var($value, FILTER_VALIDATE_EMAIL);
        },
        'message' => 'Некорректная почта'
    ],
    'password' => [
        'function' => function ($value, $params = []) {
            return (string) $value === filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
        },
        'message' => 'Некорректный пароль'
    ]
];
$errors = [];
foreach ($FieldNames as $FieldId => $fieldName) {
    $errors[$FieldId] = [
        'IsError' => false, // Флаг, что в поле есть ошибка
        'errorDescription' => ''
    ];
}

//Проверка, что форма отправлена
$itIsPost = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Была отправлена форма
    foreach ($_POST as $key => $value) {
        $formData[$key] = htmlspecialchars($value);
    }
    ;
    $itIsPost = true;
}

//Проверка данных формы
if ($itIsPost) {
    //Проверка на пустоту
    foreach ($requiredFieldNames as $fieldId => $is_empty) {
        if ($is_empty($formData, $fieldId)) {
            set_error($errors, $fieldId, true, 'Заполните это поле. ');
            $formError = true;
        }
    }

    //валидация полей
    foreach ($validateFunctions as $fieldId => $validationFunction) {
        $message = $validationFunction['message'];
        $is_valid = $validationFunction['function'];
        if (!$errors[$fieldId]['IsError'])
            if (!$is_valid($formData[$fieldId])) {
                set_error($errors, $fieldId, true, $message);
                $formError = true;
            }
    }


}

// Проверка, что есть пользователь по этому email
if($itIsPost && !$formError){
    $mysqlConnection = db_get_connection();
    if (!$mysqlConnection) {
        http_response_code(500);
        exit();
    }
    $fieldId = 'email';
    $dbUserList = db_get_user_by_email($mysqlConnection, ['email' => $formData[$fieldId]]);
    if(count($dbUserList) === 0){
        $message = 'Некорректная почта или пароль';
        set_error($errors, $fieldId, true, $message);
        $formError = true;   
    }
    db_close_connection($mysqlConnection);
}

// если форма была отправлена и пользователь найден - проверка пароля и создание сессии
if ($itIsPost && !$formError) {

}
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
$loginPageParam = [
    'formData' => $formData,
    'errors' => $errors,
    'formError' => $formError
];
$loginPageHTML = include_template('tmp_login.php', $loginPageParam);


//подготовка блока layout
$layoutData = [
    'pageTitle' => 'Регистрация',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'mainContent' => $loginPageHTML,
    'categoryList' => $categoryList
];
$layoutPageHTML = include_template('layout.php', $layoutData);

print ($layoutPageHTML);

function set_error(&$errors, string $fieldName, bool $isError, string $errorMessage)
{
    $fieldError = &$errors[$fieldName];
    $fieldError['IsError'] = $isError;
    $fieldError['errorDescription'] = ($fieldError['errorDescription'] ?? '') . $errorMessage;
}

?>