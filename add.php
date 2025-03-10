<?php

require_once('testData.php');
require_once('helpers.php');
require_once('db/DBFunctions.php');

//Инициализация параметров
$formError = false;
$formData = [];
$FieldNames = [
    'lot-name' => 'Наименование',
    'category' => 'Категория',
    'message' => 'Описание',
    'lot-rate' => 'Начальная цена',
    'lot-step' => 'Шаг ставки',
    'lot-date' => 'Дата окончания торгов'
];
$requiredFieldNames = [
    'lot-name' => function ($formData, $fieldName) {
        return empty($formData[$fieldName]);
    },
    'category' => function ($formData, $fieldName) {
        return empty($formData[$fieldName]) || $formData[$fieldName] === 'Выберите категорию';
    },
    'message' => function ($formData, $fieldName) {
        return empty($formData[$fieldName]);
    },
    'lot-rate' => function ($formData, $fieldName) {
        return empty($formData[$fieldName]);
    },
    'lot-step' => function ($formData, $fieldName) {
        return empty($formData[$fieldName]);
    },
    'lot-date' => function ($formData, $fieldName) {
        return empty($formData[$fieldName]);
    }
];
$validateFunctions = [
    'lot-name' => [
        'function' => function ($value, $params = []) {
            return filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
        },
        'message' => 'Некорректная строка'
    ],
    'category' => [
        'function' => function ($value, $params = []) {
            return filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS) && array_key_exists($value,$params['categoryList']);
        },
        'message' => 'Некорректная категория'
    ],
    'message' => [
        'function' => function ($value, $params = []) {
            return filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
        },
        'message' => 'Некорректная строка'
    ],
    'lot-rate' => [
        'function' => function ($value, $params = []) {
            return filter_var($value, FILTER_VALIDATE_INT) && $value > 0;
        },
        'message' => 'Введите целое число'
    ],
    'lot-step' => [
        'function' => function ($value, $params = []) {
            return filter_var($value, FILTER_VALIDATE_INT) && $value > 0;
        },
        'message' => 'Введите целое число'
    ],
    'lot-date' => [
        'function' => function ($value, $params = []) {
            return is_date_valid($value) && strToTime($value) - strtotime('now') > 24 * 60 * 60;
        },
        'message' => 'ввдедите дату в формате 2019-01-01. Дата должна быть не раньша чем через 24 часа'
    ]
];
$errors = [];
foreach ($FieldNames as $FieldId => $fieldName) {
    $errors[$FieldId] = [
        'IsError' => false, // Флаг, что в поле есть ошибка
        'errorDescription' => ''
    ];
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

//Проверка, что форма отправлена
$itIsPost = false;
if ($_SERVER['REQUEST_METHOD'] = 'POST') {
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
            $fieldError = &$errors[$fieldId];
            $fieldError['IsError'] = true;
            $fieldError['errorDescription'] = $fieldError['errorDescription'] . 'Заполните это поле. ';
            $formError = true;
        }
    }

    //валидация полей
    foreach ($validateFunctions as $fieldId => $validationFunction) {
        $message = $validationFunction['message'];
        $is_valid = $validationFunction['function'];
        if (!$errors[$fieldId]['IsError'])
            if (!$is_valid($formData[$fieldId],['categoryList' => $categoryList])) {
                $fieldError = &$errors[$fieldId];
                $fieldError['IsError'] = true;
                $fieldError['errorDescription'] = $fieldError['errorDescription'] . $message;
                $formError = true;
            }
    }

}



//Вывод страницы
//подготовка блока main
$addlotPageParam = [
    'categoryList' => $categoryList,
    'formData' => $formData,
    'errors' => $errors,
    'formError' => $formError
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