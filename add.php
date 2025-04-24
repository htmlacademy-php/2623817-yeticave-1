<?php

require_once('helpers.php');
require_once('db/DBFunctions.php');
require_once('layout.php');

if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['id'])) {
     http_response_code(403);
    exit();
}
//Инициализация параметров
$formError = false;
$formData = [];
$fieldNames = [
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
        return empty($formData[$fieldName]) || ((string) $formData[$fieldName] === 'Выберите категорию');
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
            return (string) $value === filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
        },
        'message' => 'Некорректная строка'
    ],
    'category' => [
        'function' => function ($value, $params = []) {
            return ((string) $value === filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS)) &&
                array_key_exists($value, $params['categoryList']);
        },
        'message' => 'Некорректная категория'
    ],
    'message' => [
        'function' => function ($value, $params = []) {
            return (string) $value === filter_var($value, FILTER_UNSAFE_RAW);
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
foreach ($fieldNames as $fieldId => $fieldId) {
    $errors[$fieldId] = [
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
$categoryListInput = array_column($dbCategoryList, 'name', 'id');

db_close_connection($mysqlConnection);

//Проверка, что форма отправлена
$itIsPost = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Была отправлена форма
    foreach ($_POST as $key => $value) {
        $formData[$key] = htmlspecialchars($value);
    }
    $itIsPost = true;
}



//Проверка данных формы
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
            if (!$isValid($formData[$fieldId], ['categoryList' => $categoryListInput])) {
                set_error($errors, $fieldId, true, $message);
                $formError = true;
            }
        }
    }

    //Проверка файла
    //lot-img
    //Сохранение выбранного файла не делал
    $fieldName = 'lot-img';
    upload_file($formData, $fieldName, $errors, $formError);
}

// если форма была отправлена и нет оишбок - запись в БД
if ($itIsPost && !$formError) {
    $mysqlConnection = db_get_connection();
    if (!$mysqlConnection) {
        http_response_code(500);
        exit();
    }

    $queryParam = db_get_add_item_params(
        $formData['lot-name'],
        $formData['message'],
        $formData['lot-img'],
        $formData['lot-rate'],
        $formData['lot-date'],
        $formData['lot-step'],
        $_SESSION['id'],
        null,
        $formData['category']
    );
    $queryResult = db_add_item($mysqlConnection, $queryParam);

    if ($queryResult) {
        header("Location: lot.php?id=$mysqlConnection->insert_id");
    } else {
        http_response_code(500);
    }
    db_close_connection($mysqlConnection);
    exit();
}

//Вывод страницы
//подготовка блока main
$addLotPageParam = [
    'categoryList' => $categoryListInput,
    'formData' => $formData,
    'errors' => $errors,
    'formError' => $formError
];
$addLotPageHTML = include_template('add-lot.php', $addLotPageParam);


//подготовка блока layout
$layoutPageHTML = get_layout_html('Добавление лота', $addLotPageHTML);

print ($layoutPageHTML);

/**
 * Summary of upload_file
 * @param mixed $formData
 * @param mixed $fieldName
 * @param mixed $errors
 * @param mixed $formError
 * @return void
 */
function upload_file(&$formData, $fieldName, &$errors, &$formError)
{

    $mimeTypesAllowed = ['image/png', 'image/jpg', 'image/jpeg'];

    if (!$formError && isset($fieldName, $_FILES) && !empty($_FILES[$fieldName]['tmp_name'])) {
        //Проверить содержимое файла
        $fileMimeType = mime_content_type($_FILES[$fieldName]['tmp_name']);
        if (in_array($fileMimeType, $mimeTypesAllowed)) {
            $targetDirectory = "uploads/"; // Путь к папке, куда хотим переместить файл
            $originalName = $_FILES[$fieldName]['name'];
            $fileInfo = pathinfo($originalName);     // Получаем расширение файла
            $temporaryName = basename($_FILES[$fieldName]['tmp_name']);
            $newFilePath = $targetDirectory . $temporaryName . '.' . $fileInfo['extension'];  // Новое имя файла
            $moveResult = move_uploaded_file($_FILES[$fieldName]['tmp_name'], $newFilePath);
            if ($moveResult) {
                $formData[$fieldName] = $newFilePath;
            } else {
                $message = 'Не удалось загрузить. Выберите другой файл. ';
                set_error($errors, $fieldName, true, $message);
                $formError = true;
            }
        } else {
            $message = 'Неверный тип файла. Выберите другой файл. ';
            set_error($errors, $fieldName, true, $message);
            $formError = true;
        }
    } else {
        $message = 'Выберите файл';
        set_error($errors, $fieldName, true, $message);
        $formError = true;
    }
}
