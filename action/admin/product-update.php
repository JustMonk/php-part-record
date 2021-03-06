<?php
include '../../include/inc_config.php';
include '../../include/session_config.php';

//парсим полученный JSON в ассоциативный массив
$data = json_decode(file_get_contents('php://input'), true);

//разбиваем на переменные для удобства
$product_id = $data['id'];
$product_title = $data['title'];
$product_gtin = $data['gtin'];
$product_capacity = $data['capacity'];
$product_unit = $data['unit'];
$product_type = $data['type'];
$product_valid_days = $data['validDays'];
$product_extended_bool = $data['extendedMilkFields'];

//================================={проверка на наличие номенклатуры}====================================================
$res = $mysqli->query("SELECT * FROM product_list WHERE product_id = '$product_id' LIMIT 1");
if ($res->num_rows < 1) {
   //отправляем ответ клиенту
   header('Content-Type: application/json');
   echo json_encode(array('message' => 'Номенклатуры с указанным идентификатором не найдено.', 'type' => 'error'));
   exit;
}

//======================================={запись}==================================================
$res = $mysqli->query("UPDATE product_list 
SET 
   title = '$product_title',
   unit_code = (SELECT unit_id FROM units WHERE unit = '$product_unit'),
   capacity = '$product_capacity', 
   gtin = '$product_gtin',
   product_type = (SELECT type_id FROM product_types WHERE type = '$product_type'),
   valid_days = '$product_valid_days',
   extended_milk_fields = $product_extended_bool
WHERE product_id = '$product_id'
");
if ($mysqli->error) {
   header('Content-Type: application/json');
   echo json_encode(array('message' => "update error - last id ($mysqli->error)", 'type' => 'error'));
   exit;
}

header('Content-Type: application/json');
echo json_encode(array('title' => urlencode($product_title), 'type' => 'success'));