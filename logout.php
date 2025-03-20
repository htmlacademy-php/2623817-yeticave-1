<?php

if ($sessionIsActive = session_status() != PHP_SESSION_ACTIVE) 
    session_start();

if (isset($_SESSION['id'])){
    session_destroy();
}

header('Location: index.php'); // Переход на главную страницу

?>