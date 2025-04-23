<?php

require_once('helpers.php');
require_once('db/DBFunctions.php');
require_once('layout.php');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (isset($_SESSION['id'])) {
    http_response_code(403);
    exit();
}
$formError = false;
$formData = [];
$fieldNames = [
    'email' => 'E-mail',
    'password' => 'Пароль',
    'name' => 'Имя',
    'message' => 'Контактные Данные'
];

$requiredFieldNames = [
    'email' => function (array $formData, string $fieldName) {
        return empty($formData[$fieldName] ?? false);
    },
    'password' => function (array $formData, string $fieldName) {
        return empty($formData[$fieldName] ?? false);
    },
    'name' => function (array $formData, string $fieldName) {
        return empty($formData[$fieldName] ?? false);
    },
    'message' => function (array $formData, string $fieldName) {
        return empty($formData[$fieldName] ?? false);
    }
];
$validateFunctions = [
    'email' => [
        'function' => function (string $value): bool {
            return $value === filter_var($value, FILTER_VALIDATE_EMAIL);
        },
        'message' => 'Некорректная почта'
    ],
    'password' => [
        'function' => function (string $value): bool {
            return $value === filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
        },
        'message' => 'Некорректный пароль'
    ],
    'name' => [
        'function' => function (string $value): bool {
            return $value === filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
        },
        'message' => 'Некорректное имя'
    ],
    'message' => [
        'function' => function (string $value): bool {
            return $value === filter_var($value, FILTER_UNSAFE_RAW);
        },
        'message' => 'Некорректные контактные данные'
    ]
];
$errors = [];
foreach ($fieldNames as $fieldId => $fieldName) {
    $errors[$fieldId] = [
        'IsError' => false, // Флаг, что в поле есть ошибка
        'errorDescription' => ''
    ];
}

//Проверка, что форма отправлена
$itIsPost = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            if (!$isValid($formData[$fieldId])) {
                set_error($errors, $fieldId, true, $message);
                $formError = true;
            }
        }
    }
}

// Проверка, что нет пользователя по этому email
if ($itIsPost && !$formError) {
    $mysqlConnection = db_get_connection();
    if (!$mysqlConnection) {
        http_response_code(500);
        exit();
    }
    $fieldId = 'email';
    $dbUserList = db_get_user_by_email($mysqlConnection, ['email' => $formData[$fieldId]]);
    if (count($dbUserList) > 0) {
        $message = 'Эта почта уже занята';
        set_error($errors, $fieldId, true, $message);
        $formError = true;
    }
    db_close_connection($mysqlConnection);
}

// если форма была отправлена и нет оишбок - запись в БД
if ($itIsPost && !$formError) {
    $mysqlConnection = db_get_connection();
    if (!$mysqlConnection) {
        http_response_code(500);
        exit();
    }

    $queryParam = db_get_add_user_params(
        $formData['email'],
        $formData['name'],
        password_hash($formData['password'], PASSWORD_DEFAULT),
        $formData['message']
    );
    $queryResult = db_add_user($mysqlConnection, $queryParam);
    db_close_connection($mysqlConnection);

    if ($queryResult) {
        header('Location: login.php');
    } else {
        http_response_code(500);
    }
    exit();
}

//Вывод страницы
//подготовка блока main
$signUpPageParam = [
    'formData' => $formData,
    'errors' => $errors,
    'formError' => $formError
];
$signUpPageHTML = include_template('sign-up.php', $signUpPageParam);


//подготовка блока layout
$layoutPageHTML = get_layout_html('Регистрация', $signUpPageHTML);

print ($layoutPageHTML);
