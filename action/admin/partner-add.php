<?php
include '../../include/inc_config.php';
include '../../include/session_config.php';
//include 'include/auth_redirect.php';


//TODO: попробовать декодить json из $_POST

//парсим полученный JSON в ассоциативный массив
$data = json_decode(file_get_contents('php://input'), true);
//$data = json_decode('{"docNum":"12312","operationDate":"2019-12-20","partner":"ИП Володянкин","productList":[{"name":"Йогурт Домодедовский КЛАССИЧЕСКИЙ жир. 2,7% (250гр)","count":"32","createDate":"2019-12-22","extFat":"","extSolidity":"","extAcidity":""},{"name":"Биокефир Домодедовский жир. 1% (930гр)","count":"3","createDate":"2019-12-22","extFat":"","extSolidity":"","extAcidity":""}]}', true);
//$data = json_decode('{"docNum":"3","operationDate":"2019-12-21","partner":"ИП Володянкин","productList":[{"name":"Йогурт Домодедовский КЛАССИЧЕСКИЙ жир. 2,7% (250гр)","count":"11","createDate":"2019-12-16","extFat":"","extSolidity":"","extAcidity":""}]}', true);


//разбиваем на переменные для удобства
//$operation_type = 'add'; ???????????????????????????????????????????
$partner_inn = $data['inn'];
$partner_kpp = $data['kpp'];
$partner_name = $data['name'];
$partner_comment = $data['comment'];
//валидации!!
//хотя бы HTML special chars добавить надо


//================================={проверка на наличие контрагента (уникальность)}====================================================
//ключ: имя-инн-кпп
$res = $mysqli->query("SELECT * FROM partners WHERE name = '$partner_name' OR inn = $partner_inn AND kpp = $partner_kpp LIMIT 1");
if ($res->num_rows > 0) {
   //отправляем ответ клиенту
   header('Content-Type: application/json');
   echo json_encode(array('message' => 'Контрагент с такими данными уже существует.', 'type' => 'error'));
   exit;
}

//======================================={запись}==================================================
$res = $mysqli->query("INSERT INTO partners(name, inn, kpp, comment) 
VALUES(
   '$partner_name',
   '$partner_inn',
   '$partner_kpp',
   '$partner_comment'
)");
if ($mysqli->error) {
   //printf("Errormessage: %s\n", $mysqli->error);
   header('Content-Type: application/json');
   echo json_encode(array('message' => "add error - last id ($mysqli->error)", 'type' => 'error'));
   exit;
}



header('Content-Type: application/json');
echo json_encode(array('message' => "Контрагент успешно добавлен.", 'type' => 'success'));
