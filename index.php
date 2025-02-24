<?php
require_once('testData.php');
require_once('helpers.php');

//подготовка блока main
$mainData = [
    'categoryList' => $categoryList,
    'itemList' => $itemList   
];
$mainPageHTML = include_template('main.php',$mainData);

//подготовка блока layout
$layoutData = $mainData;
$layoutData = ['pageTitle' => 'Главная страница',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'mainContent' => $mainPageHTML,
    'categoryList' => $categoryList];
$mainPageHTML = include_template('layout.php',$layoutData);

Print($mainPageHTML);
?>



