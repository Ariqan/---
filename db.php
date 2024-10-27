<?php
require '../htdocs/rb-mysql.php';

// Подключаемся к БД
R::setup( 'mysql:host=localhost;dbname=register',
        'root', '' );

// Проверка подключения к БД
if(!R::testConnection()) die('No DB connection!');

session_start(); // Создаем сессию для авторизации
?>