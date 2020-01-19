<?php
include 'inc_config.php';
include 'session_config.php';
//include 'include/auth_redirect.php';

//TODO: попробовать декодить json из $_POST

//парсим полученный JSON в ассоциативный массив
$data = json_decode(file_get_contents('php://input'), true);

//разбиваем на переменные для удобства
$login = $data['username'];
$user_password = $data['password'];

$res = $mysqli->query("SELECT * FROM users WHERE login='$login' LIMIT 1");
if ($res->num_rows > 0) {
   //есть такой пользователь
   //сравниваем хэш
   $res = $res->fetch_assoc();
   if (hash_equals($res['password'], crypt($user_password, 'hardcodesalt'))) {
      //пароль верный
      $_SESSION['login'] = $res['login'];
      $_SESSION['name'] = $res['name'];
      $_SESSION['lastname'] = $res['lastname'];
      $_SESSION['isAutorized'] = true;

      header('Content-Type: application/json');
      echo json_encode(array('success' => true));
      exit;
   }
}

//в иных случаях возвращаем false
//возвращаем JSON
header('Content-Type: application/json');
echo json_encode(array('success' => false));
