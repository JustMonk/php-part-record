<?php
include '../../include/inc_config.php';
include '../../include/session_config.php';

//парсим полученный JSON в ассоциативный массив
$data = json_decode(file_get_contents('php://input'), true);

//разбиваем на переменные для удобства
$partner_inn = $data['inn'];
$partner_kpp = $data['kpp'];
$partner_name = $data['name'];
$partner_comment = $data['comment'];

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
   header('Content-Type: application/json');
   echo json_encode(array('message' => "add error - last id ($mysqli->error)", 'type' => 'error'));
   exit;
}

header('Content-Type: application/json');
echo json_encode(array('message' => "Контрагент успешно добавлен.", 'type' => 'success'));