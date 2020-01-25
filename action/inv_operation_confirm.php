<?php
include '../include/inc_config.php';
include '../include/session_config.php';

//парсим полученный JSON в ассоциативный массив
$data = json_decode(file_get_contents('php://input'), true);

//разбиваем на переменные для удобства
$operation_type = 'inv';
$product_list = $data;

//========================={запись в историю операций}=================================
$res = $mysqli->query("INSERT INTO operation_history(operation_type, document_number, operation_date, partner_code, timestamp, user_code) 
VALUES(
   (SELECT operation_type_id FROM operation_types WHERE operation_name = '$operation_type'),
   DEFAULT,
   CURRENT_DATE(),
   DEFAULT,
   DEFAULT,
   (SELECT user_id FROM users WHERE login = '$_SESSION[login]')
)");

$last_id = $mysqli->query("SELECT LAST_INSERT_ID()");
$last_id->data_seek(0);
$last_id = $last_id->fetch_assoc();
$last_id = $last_id['LAST_INSERT_ID()'];

if ($mysqli->error) {
   header('Content-Type: application/json');
   echo json_encode(array('message' => "history_error - last id ($mysqli->error)", 'type' => 'error'));
   exit;
}

//=========================={запись в историю инвентаризаций, с указанием предыдущего и текущего значения}===================================
$values_str = '';
foreach ($product_list as $key => $value) {
   $values_str .= "($last_id, '$value[product_id]', '$value[create_date]', '$value[count]', '$value[real_count]')";
   if ($key != count($product_list) - 1) $values_str .= ",";
}
unset($value);

$res = $mysqli->query("INSERT INTO operation_inventory(operation_id, product_id, create_date, count_before, count_after) VALUES $values_str");
if ($mysqli->error) {
   header('Content-Type: application/json');
   echo json_encode(array('message' => "add error - last id ($mysqli->error)", 'type' => 'error'));
   exit;
}

//====================================={удаление всего текущего реестра}==============================================================
$res = $mysqli->query("DELETE FROM product_registry");
if ($mysqli->error) {
   header('Content-Type: application/json');
   echo json_encode(array('message' => "delete error - last id ($mysqli->error)", 'type' => 'error'));
   exit;
}

//================================{пишем в реестр}=========================================
$values_str = '';
foreach ($product_list as $key => $value) {
   //аккумулируем, только если количество больше нуля
   if (intval($value["real_count"]) > 0) {
      $res = $mysqli->query("SELECT DATE_ADD('$value[create_date]', INTERVAL (SELECT valid_days FROM product_list WHERE product_id = $value[product_id]) DAY) as expire_date;");
      $res = $res->fetch_assoc();
      $expire_date = $res["expire_date"];
      $values_str .= "('$value[product_id]', '$value[real_count]', '$value[create_date]', '$expire_date'),";
   } 
}
unset($value);
$values_str = substr($values_str, 0, -1);

$res = $mysqli->query("INSERT INTO product_registry(product_id, count, create_date, expire_date) VALUES $values_str");
if ($mysqli->error) {
   header('Content-Type: application/json');
   echo json_encode(array('message' => "registry error - rewrite ($mysqli->error)", 'type' => 'error'));
   exit;
}

header('Content-Type: application/json');
echo json_encode(array('message' => "Операция инвентаризации успешно проведена.", 'type' => 'success'));