<?php
include '../include/inc_config.php';
include '../include/session_config.php';

//response общий объект ответа
$response = array(
   "materials" => array(),
   "halfway" => array(),
   "halfwayList" => array(),
   "finishedList" => array()
);

//добавляем список СЫРЬЯ ($material), тут два селекта в WHERE т.к полуфабрикаты тоже используются в производстве полуфабрикатов
foreach ($mysqli->query('SELECT product_registry.registry_id, product_list.title, product_registry.count, units.unit, product_registry.create_date, product_registry.expire_date
FROM product_registry, product_list, units
WHERE product_registry.product_id = product_list.product_id AND product_list.unit_code = units.unit_id AND (product_list.product_type = (SELECT type_id FROM product_types WHERE TYPE = "Сырье") OR (SELECT type_id FROM product_types WHERE TYPE = "Полуфабрикат"))') as $row) {
   $response["materials"]["$row[title] [$row[create_date]]"] = array(
      'registry_id' => $row["registry_id"],
      'name' => $row["title"],
      'count' => $row["count"],
      'unit' => $row["unit"],
      'create_date' => $row["create_date"],
      'expire_date' => $row["expire_date"]
   );
}

//добавляем список ПОЛУФАБРИКАТОВ ($halfway), по аналогии с прошлым запросом селекта 2 т.к готовая идет из полу- и готовой
foreach ($mysqli->query('SELECT product_registry.registry_id, product_list.title, product_registry.count, units.unit, product_registry.create_date, product_registry.expire_date
     FROM product_registry, product_list, units
     WHERE product_registry.product_id = product_list.product_id AND product_list.unit_code = units.unit_id AND (product_list.product_type = (SELECT type_id FROM product_types WHERE type = "Полуфабрикат") OR (SELECT type_id FROM product_types WHERE type = "Готовая продукция"))') as $row) {
   $response["halfway"]["$row[title] [$row[create_date]]"] = array(
      'registry_id' => $row["registry_id"],
      'name' => $row["title"],
      'count' => $row["count"],
      'unit' => $row["unit"],
      'create_date' => $row["create_date"],
      'expire_date' => $row["expire_date"]
   );
}

//добавляем список всех полуфабрикатов для производства ($halfway_list)
foreach ($mysqli->query('SELECT product_list.product_id, product_list.title, units.unit, product_list.valid_days
      FROM product_list, units
      WHERE product_list.unit_code = units.unit_id AND product_list.product_type = (SELECT type_id FROM product_types WHERE type = "Полуфабрикат")') as $row) {
   $response["halfwayList"]["$row[title]"] = array(
      'id' => $row["product_id"],
      'unit' => $row["unit"],
      'valid_days' => $row["valid_days"]
   );
}

//добавляем список всей готовой продукции для производства ($finished_list)
foreach ($mysqli->query('SELECT product_list.product_id, product_list.title, units.unit, product_list.valid_days
      FROM product_list, units
      WHERE product_list.unit_code = units.unit_id AND product_list.product_type = (SELECT type_id FROM product_types WHERE type = "Полуфабрикат")') as $row) {
   $response["finishedList"]["$row[title]"] = array(
      'id' => $row["product_id"],
      'unit' => $row["unit"],
      'valid_days' => $row["valid_days"]
   );
}

header('Content-Type: application/json');
echo json_encode($response);
