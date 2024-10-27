<?php
$title="Главная страница"; // название формы
require __DIR__ . '/header.php'; // подключаем шапку проекта
require "db.php"; // подключаем файл для соединения с БД
?>

<div class="container mt-4">
<div class="row">
<div class="col">
<center>
<h1>Добро пожаловать на сайт!</h1>
<link rel="stylesheet" type="text/css" href="css/style.css">
</center>
</div>
</div>
</div>

<!-- Если авторизован выведет приветствие -->
<?php if(isset($_SESSION['logged_user'])) : ?>
	Привет, <?php echo $_SESSION['logged_user']->name; 



// Обработка формы редактирования
if (isset($_POST['update_user'])) {
  $id = $_POST['id'];
  $login = $_POST['login'];
  $email = $_POST['email'];
  $name = $_POST['name'];
  $family = $_POST['family'];

  // Валидация данных
  $errors = [];
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = "Некорректный email";
  }
  if (strlen($name) <= 3) {
      $errors[] = "Имя должно быть длиннее 3 символов";
  }
  if (strlen($family) <= 3) {
      $errors[] = "Фамилия должна быть длиннее 3 символов";
  }
  // Проверка на дубликат email
  $existingUser = R::findOne('users', 'email = ?', [$email]);
  if ($existingUser && $existingUser->id != $id) {
      $errors[] = "Email уже существует";
  }

  // Обновление записи пользователя в базе данных
  if (empty($errors)) {
      $user = R::load('users', $id);
      if ($user) {
          $user->login = $login;
          $user->email = $email;
          $user->name = $name;
          $user->family = $family;
          R::store($user);
          // После обновления перенаправляем на ту же страницу
          header('Location: ' . $_SERVER['PHP_SELF']);
          exit;
      } else {
          echo "Пользователь с ID $id не найден."; // Выводим сообщение об ошибке, если пользователь не найден
      }
  } else {
      // Вывод ошибок
      echo "<ul>";
      foreach ($errors as $error) {
          echo "<li>$error</li>";
      }
      echo "</ul>";
  }
}

// Обработка удаления пользователя
if (isset($_GET['delete_user']) && isset($_GET['id'])) {
  $id = $_GET['id'];
  R::exec('DELETE FROM users WHERE id = ?', [$id]);
  // Перенаправляем на ту же страницу после удаления
  header('Location: ' . $_SERVER['PHP_SELF']);
  exit;
}

$users = R::findAll('users');

if (count($users) > 0) {
  // Вывод данных в таблицу HTML
  echo "<table border='1' style='border-collapse: collapse;'>";
  echo "<tr>";
  echo "<th style='padding: 10px;'>ID</th>";
  echo "<th style='padding: 10px;'>Login</th>";
  echo "<th style='padding: 10px;'>Email</th>";
  echo "<th style='padding: 10px;'>Name</th>";
  echo "<th style='padding: 10px;'>Family</th>";
  // Не показываем пароль, так как он хранится в хэшированном виде
  // echo "<th style='padding: 10px;'>Password</th>";
  echo "<th style='padding: 10px;'>Password</th>";
  echo "<th style='padding: 10px;'>Edit</th>";
  echo "<th style='padding: 10px;'>Delete</th>";
  echo "</tr>";

  foreach ($users as $user) {
      echo "<tr>";
      echo "<td style='padding: 10px;'>" . $user->id . "</td>";
      echo "<td style='padding: 10px;'>" . $user->login . "</td>";
      echo "<td style='padding: 10px;'>" . $user->email . "</td>";
      echo "<td style='padding: 10px;'>" . $user->name . "</td>";
      echo "<td style='padding: 10px;'>" . $user->family . "</td>";
      // Не показываем пароль, так как он хранится в хэшированном виде
      // echo "<td style='padding: 10px;'>" . $user->password . "</td>";
      echo "<td style='padding: 10px;'>" . str_repeat("*", 5) . "</td>";
      echo "<td style='padding: 10px;'><a href='#' data-id='" . $user->id . "' class='edit-user'>Edit</a></td>";
      echo "<td style='padding: 10px;'><a href='#' data-id='" . $user->id . "' class='delete-user'>Delete</a></td>";
      echo "</tr>";
  }

  echo "</table>";
} else {
  echo "В таблице 'users' нет данных.";
}

if (isset($_POST['submit_add'])) {
    $login = $_POST['login'];
    $email = $_POST['email'];
    $name = $_POST['name'];
    $family = $_POST['family'];
    $password = $_POST['password'];

    // Проверка на пустые поля
    if (empty($login) || empty($email) || empty($name) || empty($family) || empty($password)) {
        echo "Заполните все поля!";
    } else {
        // Проверка синтаксиса email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Неверный формат email!";
        } else {
            // Проверка длины имени и фамилии
            if (strlen($name) < 3 || strlen($family) < 3) {
                echo "Имя и фамилия должны быть не менее 3 символов!";
            } else {
                // Проверка на наличие записи с таким логином, email, именем и фамилией
                $existingUser = R::findOne('users', 'login = ? OR email = ? OR name = ? OR family = ?', [$login, $email, $name, $family]);
                if ($existingUser) {
                    echo "Пользователь с такими данными уже существует!";
                } else {
                    // Хэширование пароля
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    // Создание новой записи
                    $user = R::dispense('users');
                    $user->login = $login;
                    $user->email = $email;
                    $user->name = $name;
                    $user->family = $family;
                    $user->password = $hashedPassword; 
                    R::store($user);
                    echo "Пользователь успешно добавлен!";
                }
            }
        }
    }
}

// Вывод таблицы пользователей
$users = R::findAll('users');

if (count($users) > 0) {
    // Вывод данных в таблицу HTML
    // ... (ваш код вывода таблицы пользователей)
} else {
    echo "В таблице 'users' нет данных.";
}

// Вывод формы для вставки новой записи


// Вывод формы для вставки новой записи

echo "<h2 class='gg_h2'>Добавить нового пользователя</h2>";
echo "<form method='POST' action='' class='add-form'>";

echo "<label for='login' class='edit'>Login:</label>";

echo "<input type='text' name='login' id='login' class='form-input'><br>";

echo "<label for='email' class='edit'>Email:</label>";
echo "<input type='text' name='email' id='email' class='form-input'><br>";
echo "<label for='name' class='edit'>Name:</label>";
echo "<input type='text' name='name' id='name' class='form-input'><br>";
echo "<label for='family' class='edit'>Family:</label>";
echo "<input type='text' name='family' id='family' class='form-input'><br>";
echo "<label for='password' class='edit'>Password:</label>";
echo "<input type='password' name='password' id='password' class='form-input'><br>";
echo "<input type='submit' name='submit_add' value='Добавить' class='submit-button'>";
echo "</form>";

?>



<div id="edit-form" style="display: none;">
  <h2>Редактирование пользователя</h2>
  <form method="post" action="">
      <input type="hidden" name="id" id="user_id">
      <label for="login">Login:</label>
      <input type="text" name="login" id="login" value=""><br>
      <label for="email">Email:</label>
      <input type="text" name="email" id="email" value=""><br>
      <label for="name">Name:</label>
      <input type="text" name="name" id="name" value=""><br>
      <label for="family">Family:</label>
      <input type="text" name="family" id="family" value=""><br>
      <input type="submit" name="update_user" value="Сохранить">
      <input type="submit" name="date_user" value="Отмена">
  </form>
</div>

<script>
// Добавляем обработчик событий для кликов на кнопке "Edit"
const editButtons = document.querySelectorAll('.edit-user');
  const editForm = document.getElementById('edit-form');

  editButtons.forEach(button => {
      button.addEventListener('click', (event) => {
          const userId = event.target.dataset.id;
          // Заполняем форму данными пользователя
          fetch(`get_user_data.php?id=${userId}`)
              .then(response => response.json())
              .then(data => {
                  document.getElementById('user_id').value = userId;
                  document.getElementById('login').value = data.login;
                  document.getElementById('email').value = data.email;
                  document.getElementById('name').value = data.name;
                  document.getElementById('family').value = data.family;
              })
              .catch(error => console.error('Ошибка:', error));
          // Показываем форму
          editForm.style.display = 'block';
      });
  });


  

  // Подтверждение удаления
  const deleteButtons = document.querySelectorAll('.delete-user');
  deleteButtons.forEach(button => {
      button.addEventListener('click', (event) => {
          const userId = event.target.dataset.id;
          if (confirm('Вы уверены, что хотите удалить пользователя?')) {
              // Переход к удалению
              location.href = `?delete_user=1&id=${userId}`;
          }
      });
  });




</script>


<style>
 

 body {
      margin: 0; /* Убираем отступы по умолчанию у body */
      padding: 0; /* Убираем отступы по умолчанию у body */
      font-family: sans-serif; /* Устанавливаем шрифт для всего документа */
    }
    
    #edit-form {
      /* ... (стили для формы редактирования) */
      position: fixed; /* Фиксируем форму на экране */
      top: 410px; /* Отступ сверху */
      left: 500px; /* Отступ слева */
    }

    #add-form {
      /* ... (стили для формы редактирования) */
      position: fixed; /* Фиксируем форму на экране */
      top: 400px; /* Отступ сверху */
      left: 30px; /* Отступ слева */
    }

    /*  Дополнительные стили  для  таблицы  */
    table {
      width: 80%; /*  Таблица  занимает  80%  ширины  экрана  */
      margin: 20px auto; /*  Отступ  сверху  и  по  центру  */
    }
 #add-form h2,
#edit-form h2 {
  margin-top: 0; /* Убираем верхний отступ заголовка */
  font-size: 1.2em; /* Размер шрифта заголовка */
  color: #333; /* Цвет шрифта заголовка */
  top: 400px; /* Отступ сверху */
  left: 30px; /* Отступ слева */
}
#add-form label,
#edit-form label {
  display: block; /*  Располагаем  label  на отдельной строке */
  margin-bottom: 5px; /* Отступ снизу  label */
  font-weight: bold; /*  Жирный  шрифт  для  label */
}
#add-form input[type="text"], 
#add-form input[type="password"],
#edit-form input[type="text"],
#edit-form input[type="password"] {
  width: 80%; /*  Поле  занимает  всю  ширину  формы */
  padding: 8px; /*  Отступы  внутри  полей  ввода */
  margin-bottom: 10px; /*  Отступ  снизу  полей  ввода */
  border: 1px solid #ccc; /*  Рамка  полей  ввода */
  border-radius: 3px; /*  Скругленные  углы  полей  ввода */
}
#add-form input[type="submit"],
#edit-form input[type="submit"],
#edit-form input[type="submit"] { /* Исправлен селектор для кнопки "Отмена" */
  padding: 10px 20px; /*  Отступы  внутри  кнопки */
  background-color: #4CAF50; /*  Цвет  фона  кнопки */
  color: white; /*  Цвет  шрифта  кнопки */
  border: none; /*  Убираем  рамку  кнопки */
  border-radius: 3px; /*  Скругленные  углы  кнопки */
  cursor: pointer; /*  Устанавливаем  курсор  указателя  при  наведении */
}
#add-form input[type="submit"]:hover ,
#edit-form input[type="submit"]:hover {
  background-color: #45a049; /*  Изменяем  цвет  при  наведении */
}
#add-form input[type="submit"]:nth-child(2),
#edit-form input[type="submit"]:nth-child(2) { /*  Стиль  для  второй  кнопки  */
  background-color: #f44336; /*  Красный  цвет  для  "Отмены" */
}
#add-form input[type="submit"]:nth-child(2):hover,
#edit-form input[type="submit"]:nth-child(2):hover {
  background-color: #d32f2f; /*  Темнее  красный  при  наведении */
}



.form-container {
    background-color: #f9f9f9;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.user-form {
    display: flex;
    flex-direction: column;
}

.form-input {
  width: 80%; /*  Поле  занимает  всю  ширину  формы */
  padding: 8px; /*  Отступы  внутри  полей  ввода */
  margin-bottom: 10px; /*  Отступ  снизу  полей  ввода */
  border: 1px solid #ccc; /*  Рамка  полей  ввода */
  border-radius: 3px; /*  Скругленные  углы  полей  ввода */
}

.submit-button {
padding: 10px 20px; /*  Отступы  внутри  кнопки */
  background-color: #4CAF50; /*  Цвет  фона  кнопки */
  color: white; /*  Цвет  шрифта  кнопки */
  border: none; /*  Убираем  рамку  кнопки */
  border-radius: 3px; /*  Скругленные  углы  кнопки */
  cursor: pointer; /*  Устанавливаем  курсор  указателя  при  наведении */
}
}

.submit-button:hover {
    background-color: #45a049;
}

.gg_h2{
    margin-top: 0; /* Убираем верхний отступ заголовка */
  font-size: 1.2em; /* Размер шрифта заголовка */
  color: #333; /* Цвет шрифта заголовка */
}

.edit {
  display: block; /*  Располагаем  label  на отдельной строке */
  margin-bottom: 5px; /* Отступ снизу  label */
  font-weight: bold; /*  Жирный  шрифт  для  label */}
</style>






































































<!-- Пользователь может нажать выйти для выхода из системы -->
<a href="logout.php">Выйти</a> <!-- файл logout.php создадим ниже -->
<?php else : ?>

<!-- Если пользователь не авторизован выведет ссылки на авторизацию и регистрацию -->
<a href="login.php">Авторизоваться</a><br>
<a href="signup.php">Регистрация</a>

<?php endif; ?>
 
