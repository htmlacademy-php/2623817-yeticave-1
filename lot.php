<?php

require_once('helpers.php');
require_once('db/DBFunctions.php');
require_once('layout.php');

$formData = [];
$fieldNames = [
    'cost' => 'Ставка'
];
$requiredFieldNames = [
    'cost' => function ($formData, $fieldName) {
        return empty($formData[$fieldName]);
    }
];
$validateFunctions = [
    'cost' => [
        'function' => function ($value, $params = []) {
            $minBet = $params['minBet'] ?? 0;
            return filter_var($value, FILTER_VALIDATE_INT) && $value >= $minBet;
        },
        'message' => 'Некорректная цена'
    ]
];
$errors = [];
foreach ($fieldNames as $fieldId) {
    $errors[$fieldId] = [
        'IsError' => false, // Флаг, что в поле есть ошибка
        'errorDescription' => ''
    ];
}
$formError = false;

if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}

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
$queryParam = ['lot_id' => $ParamId];
$dbBetsArray = db_get_bets_by_lot($mysqlConnection, $queryParam);
db_close_connection($mysqlConnection);

if (count($dbItemArray) == 0) {
    http_response_code(404);
    exit();
}
$dbItem = $dbItemArray[0];

//Проверяем, надо ли сделать ставку
$itIsPost = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Была отправлена форма
    foreach ($_POST as $key => $value) {
        $formData[$key] = htmlspecialchars($value);
    }
    $itIsPost = true;
}

if ($itIsPost) {
    //Проверка на пустоту
    foreach ($requiredFieldNames as $fieldId => $isEmpty) {
        if ($isEmpty($formData, $fieldId)) {
            set_error($errors, $fieldId, true, 'Заполните это поле. ');
            $formError = true;
        }
    }

    //валидация полей
    foreach ($validateFunctions as $fieldId => $validationFunction) {
        $message = $validationFunction['message'];
        $isValid = $validationFunction['function'];
        if (!$errors[$fieldId]['IsError']) {
            if (!$isValid($formData[$fieldId], ['minBet' => $dbItem['min_bet']])) {
                set_error($errors, $fieldId, true, $message);
                $formError = true;
            }
        }
    }

    //Добавление ставки
    if (!$formError) {
        $mysqlConnection = db_get_connection();
        if (!$mysqlConnection) {
            http_response_code(500);
            exit();
        }

        $queryParam = db_get_add_bet_params(
            $dbItem['lot_id'],
            (int) $formData['cost'],
            $_SESSION['id'],
        );
        $queryResult = db_add_bet($mysqlConnection, $queryParam);

        //Получаем актуальные данные по лоту
        $queryParam = [
            'id' => $ParamId
        ];

        $dbItemArray = db_get_item($mysqlConnection, $queryParam);

        if (count($dbItemArray) == 0) {
            http_response_code(404);
            exit();
        }
        $dbItem = $dbItemArray[0];
        $formData['cost'] = "";
        $queryParam = ['lot_id' => $ParamId];
        $dbBetsArray = db_get_bets_by_lot($mysqlConnection, $queryParam);
        db_close_connection($mysqlConnection);
    }
}

//Получить ставки по лоту


//Вывод страницы
//подготовка блока main
$sessionIsActive = isset($_SESSION['id']);
$lotPageParam = [
    'item' => $dbItem,
    'isAuth' => $sessionIsActive,
    'formData' => $formData,
    'errors' => $errors,
    'formError' => $formError,
    'requestUri' => $_SERVER['REQUEST_URI'],
    'betsArray' => $dbBetsArray
];
$lotPageHTML = include_template('lot.php', $lotPageParam);


//подготовка блока layout
$layoutPageHTML = get_layout_html($dbItem['lot_name'], $lotPageHTML);

print ($layoutPageHTML);
