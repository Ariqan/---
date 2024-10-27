<?php

// Подключение к базе данных
require __DIR__ . '/header.php'; // подключаем шапку проекта
require "db.php"; // подключаем файл для соединения с БД
// Получение идентификатора пользователя из параметра GET
$id = $_GET['id'];

// Поиск пользователя по идентификатору
$user = R::load('users', $id);

// Проверка наличия пользователя
if ($user) {

    // Вывод формы редактирования
    echo "<h2>Редактирование пользователя</h2>";
    echo "<form method='post' action='update.php'>"; // Обработка формы на странице update.php
    echo "<input type='hidden' name='id' value='" . $user->id . "'>"; // Передача id в форму
    echo "<label for='login'>Логин:</label>";
    echo "<input type='text' name='login' id='login' value='" . $user->login . "'><br>";
    // Добавьте поля для остальных атрибутов пользователя
    echo "<input type='submit' value='Сохранить'><a href='index.php?id=" . $user->id . "'>";
    echo "</form>";

} else {
    echo "Пользователь не найден.";
}

?>