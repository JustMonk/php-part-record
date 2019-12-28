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

//добавляем список продукции из реестра
foreach ($mysqli->query('SELECT product_registry.registry_id, product_list.title, product_registry.count, units.unit, product_registry.create_date, product_registry.expire_date
  FROM product_registry, product_list, units
  WHERE product_registry.product_id = product_list.product_id AND product_list.unit_code = units.unit_id') as $row) {
   $response["goods"]["$row[title] [$row[create_date]]"] = array(
      'registry_id' => $row["registry_id"],
      'count' => $row["count"],
      'unit' => $row["unit"],
      'create_date' => $row["create_date"],
      'expire_date' => $row["expire_date"]
   );
}

header('Content-Type: application/json');
echo json_encode($response);
