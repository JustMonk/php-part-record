<?php
include '../../include/inc_config.php';
include '../../include/session_config.php';
//include 'include/auth_redirect.php';


//TODO: попробовать декодить json из $_POST

//парсим полученный JSON в ассоциативный массив
$data = json_decode(file_get_contents('php://input'), true);
//$data = json_decode('{"docNum":"12312","operationDate":"2019-12-20","partner":"ИП Володянкин","productList":[{"name":"Йогурт Домодедовский КЛАССИЧЕСКИЙ жир. 2,7% (250гр)","count":"32","createDate":"2019-12-22","extFat":"","extSolidity":"","extAcidity":""},{"name":"Биокефир Домодедовский жир. 1% (930гр)","count":"3","createDate":"2019-12-22","extFat":"","extSolidity":"","extAcidity":""}]}', true);


//разбиваем на переменные для удобства
$user_login = $data['login'];
$user_name = $data['name'];
$user_lastname = $data['lastname'];
$user_access = $data['access'];
$user_password = crypt($data['password'], 'hardcodesalt');
//if (hash_equals($hashed_password, crypt($user_input, $hashed_password)))
//валидации!!
//хотя бы HTML special chars добавить надо

//================================={проверка на наличие пользователя (уникальность)}====================================================
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
   //printf("Errormessage: %s\n", $mysqli->error);
   header('Content-Type: application/json');
   echo json_encode(array('message' => "Ошибка при попытке записи - ($mysqli->error)", 'type' => 'error'));
   exit;
}

header('Content-Type: application/json');
//echo json_encode(array('message' => "Пользователь успешно добавлен.", 'type' => 'success'));
echo json_encode(array('title' => urlencode($user_login), 'type' => 'success'));
