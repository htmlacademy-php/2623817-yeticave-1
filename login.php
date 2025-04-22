<?php

require_once('helpers.php');
require_once('layout.php');

if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}

$formError = false;
$formData = [];
$fieldNames = [
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
        'function' => function ($value) {
            return filter_var($value, FILTER_VALIDATE_EMAIL);
        },
        'message' => 'Некорректная почта'
    ],
    'password' => [
        'function' => function ($value) {
            return (string) $value === filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
        },
        'message' => 'Некорректный пароль'
    ]
];
$errors = [];
foreach ($fieldNames as $fieldId) {
    $errors[$fieldId] = [
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

// Проверка, что есть пользователь по этому email
$dbUserList = [];
if ($itIsPost && !$formError) {
    $mysqlConnection = db_get_connection();
    if (!$mysqlConnection) {
        http_response_code(500);
        exit();
    }
    $fieldId = 'email';
    $dbUserList = db_get_user_by_email($mysqlConnection, ['email' => $formData[$fieldId]]);
    if (count($dbUserList) === 0) {
        $message = 'Некорректная почта';
        set_error($errors, $fieldId, true, $message);
        $formError = true;
    }
    db_close_connection($mysqlConnection);
}

// если форма была отправлена и пользователь найден - проверка пароля и создание сессии
if ($itIsPost && !$formError && count($dbUserList) > 0) {
    $dbUser = $dbUserList[0]; // Считаем, что почта уникальная, и в массиве всегда один элемент
    $passwordFormFieldId = 'password';
    $passwordDbFieldId = 'password';
    $passwordIsCorrect = password_verify($formData[$passwordFormFieldId], $dbUser[$passwordDbFieldId]);
    if ($passwordIsCorrect) {
        $a = session_start();
        $_SESSION['id'] = $dbUser['id'];
        $_SESSION['name'] = $dbUser['name'];
        $_SESSION['email'] = $dbUser['email'];
        header('Location: index.php'); // Переход на главную страницу
    } else {
        $fieldId = 'email';
        $message = 'Некорректный пароль';
        set_error($errors, $fieldId, true, $message);
        $formError = true;
    }
}

//Вывод страницы
//подготовка блока main
$loginPageParam = [
    'formData' => $formData,
    'errors' => $errors,
    'formError' => $formError
];
$loginPageHTML = include_template('login.php', $loginPageParam);


//подготовка блока layout
$layoutPageHTML = get_layout_html('Регистрация', $loginPageHTML);

print ($layoutPageHTML);
