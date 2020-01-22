<?php
include '../include/inc_config.php';
include '../include/session_config.php';
//include 'include/auth_redirect.php';

//парсим полученный JSON в ассоциативный массив
$data = json_decode(file_get_contents('php://input'), true);

//разбиваем на переменные для удобства
$user_login = $data['login'];
$old_pass = $data['oldPass'];
$new_pass = $data['newPass'];

//================================={проверка на наличие пользователя}====================================================
$res = $mysqli->query("SELECT * FROM users WHERE login = '$user_login'");
if ($res->num_rows < 1) {
   //отправляем ответ клиенту
   header('Content-Type: application/json');
   echo json_encode(array('message' => 'Пользователь с такими данными не существует.', 'type' => 'error'));
   exit;
}

//================================={проверяем совпадает ли пасс}====================================================
$old_pass_hash = crypt($old_pass, 'hardcodesalt');
$res = $mysqli->query("SELECT password FROM users WHERE login = '$user_login' LIMIT 1");
$current_hash = ($res->fetch_assoc())['password'];

if (hash_equals($old_pass_hash, $current_hash)) {
   $new_hash = crypt($new_pass, 'hardcodesalt');
   $res = $mysqli->query("UPDATE users SET password = '$new_hash' WHERE login = '$user_login'");
   header('Content-Type: application/json');
   echo json_encode(array('message' => "Пароль изменен.", 'type' => 'success'));
   exit;
} else {
   header('Content-Type: application/json');
   echo json_encode(array('message' => "Ошибка смены пароля. Проверьте введенные данные.", 'type' => 'error'));
   exit;
}