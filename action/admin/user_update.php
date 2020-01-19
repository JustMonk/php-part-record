<?php
include '../../include/inc_config.php';
include '../../include/session_config.php';
//include 'include/auth_redirect.php';


//TODO: попробовать декодить json из $_POST

//парсим полученный JSON в ассоциативный массив
$data = json_decode(file_get_contents('php://input'), true);

//разбиваем на переменные для удобства
$user_id = $data['id'];
$user_login = $data['login'];
$user_name = $data['name'];
$user_lastname = $data['lastname'];
$user_access = $data['access'];

//если передан пароль
if ($data['password']) {
   $password_field = ', password = ';
   $user_password = "'" . crypt($data['password'], 'hardcodesalt') . "'";
} else {
   $password_field = '';
   $user_password = '';
}
//$user_password = crypt($data['password'], 'hardcodesalt');

//================================={проверка на наличие пользователя (уникальность)}====================================================
$res = $mysqli->query("SELECT * FROM users WHERE user_id = '$user_id' LIMIT 1");
if ($res->num_rows < 1) {
   //отправляем ответ клиенту
   header('Content-Type: application/json');
   echo json_encode(array('message' => 'Пользователя с указанным идентификатором не найдено.', 'type' => 'error'));
   exit;
}

//======================================={запись}==================================================
$res = $mysqli->query("UPDATE users 
SET 
   login = '$user_login',
   access_id = (SELECT ID FROM account_types WHERE title = '$user_access'),
   name = '$user_name', 
   lastname = '$user_lastname'
   $password_field $user_password
WHERE user_id = '$user_id'
");
if ($mysqli->error) {
   //printf("Errormessage: %s\n", $mysqli->error);
   header('Content-Type: application/json');
   echo json_encode(array('message' => "update error - last id ($mysqli->error)", 'type' => 'error'));
   exit;
}

header('Content-Type: application/json');
echo json_encode(array('title' => urlencode($user_login), 'type' => 'success'));