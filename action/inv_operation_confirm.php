<?php
include '../include/inc_config.php';
include '../include/session_config.php';
//include 'include/auth_redirect.php';


//TODO: попробовать декодить json из $_POST

//парсим полученный JSON в ассоциативный массив
$data = json_decode(file_get_contents('php://input'), true);
//$data = json_decode('[{"title":"Биокефир Домодедовский жир. 1% (930гр)","unit":"шт","product_id":"2","registry_id":null,"count":"0","real_count":"333","create_date":"2019-12-26"},{"registry_id":"34","title":"Биокефир Домодедовский жир. 1% (930гр)","unit":"шт","count":"2","create_date":"2019-12-22","product_id":"2","real_count":"0"},{"registry_id":"41","title":"Биокефир Домодедовский жир. 1% (930гр)","unit":"шт","count":"127","create_date":"2019-12-27","product_id":"2","real_count":"0"},{"registry_id":"33","title":"Йогурт Домодедовский ВИШНЯ жир. 2,7% (250гр)","unit":"шт","count":"8","create_date":"2019-12-30","product_id":"3","real_count":"0"},{"registry_id":"35","title":"Йогурт Домодедовский ВИШНЯ жир. 2,7% (250гр)","unit":"шт","count":"4","create_date":"2019-12-23","product_id":"3","real_count":"0"},{"registry_id":"39","title":"Йогурт Домодедовский ВИШНЯ жир. 2,7% (250гр)","unit":"шт","count":"23","create_date":"2019-12-25","product_id":"3","real_count":"0"},{"registry_id":"36","title":"Йогурт Домодедовский КЛУБНИКА жир. 2,7% (250гр)","unit":"шт","count":"1","create_date":"2019-12-23","product_id":"5","real_count":"0"},{"registry_id":"37","title":"Сырое молочко","unit":"л","count":"30","create_date":"2019-12-24","product_id":"7","real_count":"0"},{"registry_id":"30","title":"Йогурт питьевой с клубникой, мдж 2,7%","unit":"л","count":"111","create_date":"2019-12-23","product_id":"8","real_count":"0"},{"registry_id":"38","title":"Йогурт питьевой с клубникой, мдж 2,7%","unit":"л","count":"3","create_date":"2019-12-25","product_id":"8","real_count":"0"},{"registry_id":"40","title":"Йогурт питьевой с клубникой, мдж 2,7%","unit":"л","count":"5","create_date":"2019-12-28","product_id":"8","real_count":"0"},{"registry_id":"32","title":"Молоко пастеризованное с мдж 4%","unit":"л","count":"2","create_date":"2019-12-23","product_id":"9","real_count":"0"},{"registry_id":"28","title":"Снежок с мдж 2,7%","unit":"кг","count":"23","create_date":"2019-12-20","product_id":"10","real_count":"0"},{"registry_id":"29","title":"Снежок с мдж 2,7%","unit":"кг","count":"33","create_date":"2019-12-22","product_id":"10","real_count":"0"},{"registry_id":"31","title":"Снежок с мдж 2,7%","unit":"кг","count":"777","create_date":"2019-12-23","product_id":"10","real_count":"0"}]', true);

//разбиваем на переменные для удобства
//htmlspecialchars - базовая валидация
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
   //printf("Errormessage: %s\n", $mysqli->error);
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
   //printf("Errormessage: %s\n", $mysqli->error);
   header('Content-Type: application/json');
   echo json_encode(array('message' => "add error - last id ($mysqli->error)", 'type' => 'error'));
   exit;
}

//====================================={удаление всего текущего реестра}==============================================================
$res = $mysqli->query("DELETE FROM product_registry");
if ($mysqli->error) {
   //printf("Errormessage: %s\n", $mysqli->error);
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
      //if ($key != count($product_list) - 1) $values_str .= ",";
   } 
}
unset($value);
$values_str = substr($values_str, 0, -1);

$res = $mysqli->query("INSERT INTO product_registry(product_id, count, create_date, expire_date) VALUES $values_str");
if ($mysqli->error) {
   //printf("Errormessage: %s\n", $mysqli->error);
   header('Content-Type: application/json');
   echo json_encode(array('message' => "registry error - rewrite ($mysqli->error)", 'type' => 'error'));
   exit;
}


header('Content-Type: application/json');
echo json_encode(array('message' => "Операция инвентаризации успешно проведена.", 'type' => 'success'));
