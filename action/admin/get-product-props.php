<?php
include '../../include/inc_config.php';
include '../../include/session_config.php';
//include 'include/auth_redirect.php';

$id = htmlspecialchars($_GET["id"]);

//================================={проверка на наличие номенклатуры (уникальность)}====================================================
//ключ: имя-инн-кпп
$res = $mysqli->query("SELECT product_list.product_id, product_list.title, units.unit, product_list.capacity, product_types.type, product_list.gtin, product_list.valid_days, product_list.extended_milk_fields
FROM product_list, units, product_types
   WHERE product_id = '$id' AND product_list.unit_code = units.unit_id AND product_list.product_type = product_types.type_id
   LIMIT 1");
if ($res->num_rows < 0) {
   //отправляем ответ клиенту
   header('Content-Type: application/json');
   echo json_encode(array('message' => 'ID не найден', 'type' => 'error'));
   exit;
}
$res = $res->fetch_assoc();

header('Content-Type: application/json');
echo json_encode($res);

exit;