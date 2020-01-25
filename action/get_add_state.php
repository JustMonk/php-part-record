<?php
include '../include/inc_config.php';
include '../include/session_config.php';

//response общий объект ответа
$response = array(
   "partners" => array(),
   "goods" => array()
);

//добавляем контрагентов
foreach ($mysqli->query('SELECT * FROM partners') as $row) {
   $response["partners"]["$row[name]"] = null;
}

//добавляем список номенклатур
foreach ($mysqli->query('SELECT * FROM product_list INNER JOIN units ON product_list.unit_code = units.unit_id') as $row) {
   $response["goods"]["$row[title]"] = array(
      "valid_days" => "$row[valid_days]",
      "id" => "$row[product_id]",
      "extended_milk_fields" => intval($row['extended_milk_fields'])
   );
}

header('Content-Type: application/json');
echo json_encode($response);