<?php
include '../../include/inc_config.php';
include '../../include/session_config.php';

//парсим полученный JSON в ассоциативный массив
$data = json_decode(file_get_contents('php://input'), true);

//разбиваем на переменные для удобства
$product_title = $data['title'];
$product_gtin = $data['gtin'];
$product_capacity = $data['capacity'];
$product_unit = $data['unit'];
$product_type = $data['type'];
$product_valid_days = $data['validDays'];
$product_extended_bool = $data['extendedMilkFields'];

//================================={проверка на наличие номенклатуры}====================================================
$res = $mysqli->query("SELECT * FROM product_list WHERE title = '$product_title' LIMIT 1");
if ($res->num_rows > 0) {
   //отправляем ответ клиенту
   header('Content-Type: application/json');
   echo json_encode(array('message' => 'Номенклатура с такими данными уже существует.', 'type' => 'error'));
   exit;
}

//======================================={запись}==================================================
$res = $mysqli->query("INSERT INTO product_list(title, unit_code, capacity, gtin, product_type, valid_days, extended_milk_fields) 
VALUES(
   '$product_title',
   (SELECT unit_id FROM units WHERE unit = '$product_unit'),
   '$product_capacity',
   '$product_gtin',
   (SELECT type_id FROM product_types WHERE type = '$product_type'),
   '$product_valid_days',
   $product_extended_bool
)");
if ($mysqli->error) {
   header('Content-Type: application/json');
   echo json_encode(array('message' => "add error - last id ($mysqli->error)", 'type' => 'error'));
   exit;
}

header('Content-Type: application/json');
echo json_encode(array('title' => urlencode($product_title), 'type' => 'success'));