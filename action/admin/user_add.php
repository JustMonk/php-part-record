<?php
include '../../include/inc_config.php';
include '../../include/session_config.php';

//парсим полученный JSON в ассоциативный массив
$data = json_decode(file_get_contents('php://input'), true);

//разбиваем на переменные для удобства
$user_login = $data['login'];
$user_name = $data['name'];
$user_lastname = $data['lastname'];
$user_access = $data['access'];
$user_password = crypt($data['password'], 'hardcodesalt');

//================================={проверка на наличие пользователя}====================================================
$res = $mysqli->query("SELECT * FROM users WHERE login = '$user_login'");
if ($res->num_rows > 0) {
   //отправляем ответ клиенту
   header('Content-Type: application/json');
   echo json_encode(array('message' => 'Пользователь с такими данными уже существует.', 'type' => 'error'));
   exit;
}

//======================================={запись}==================================================
$res = $mysqli->query("INSERT INTO users(login, password, access_id, name, lastname) 
VALUES(
   '$user_login',
   '$user_password',
   (SELECT ID from account_types WHERE title = '$user_access'),
   '$user_name',
   '$user_lastname'
)");
if ($mysqli->error) {
   header('Content-Type: application/json');
   echo json_encode(array('message' => "Ошибка при попытке записи - ($mysqli->error)", 'type' => 'error'));
   exit;
}

header('Content-Type: application/json');
//echo json_encode(array('message' => "Пользователь успешно добавлен.", 'type' => 'success'));
echo json_encode(array('title' => urlencode($user_login), 'type' => 'success'));
