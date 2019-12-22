<?php
//json общий объект
$json = '{';

//добавляем список СЫРЬЯ ($material)
$material = 'materials: {';
foreach ($mysqli->query('SELECT product_registry.registry_id, product_list.title, product_registry.count, units.unit, product_registry.create_date, product_registry.expire_date
      FROM product_registry, product_list, units
      WHERE product_registry.product_id = product_list.product_id AND product_list.unit_code = units.unit_id AND product_list.product_type = (SELECT type_id FROM product_types WHERE type = "Сырье")') as $row) {
   $material .= "'$row[title] [$row[create_date]]': {
      'registry_id': $row[registry_id],
      'name': '$row[title]',
      'count': $row[count],
      'unit': '$row[unit]',
      'create_date': '$row[create_date]',
      'expire_date': '$row[expire_date]'
   },";
}
$material .= '}';
$json .= $material;

//добавляем список ПОЛУФАБРИКАТОВ ($halfway)
$halfway = ',halfway: {';
foreach ($mysqli->query('SELECT product_registry.registry_id, product_list.title, product_registry.count, units.unit, product_registry.create_date, product_registry.expire_date
     FROM product_registry, product_list, units
     WHERE product_registry.product_id = product_list.product_id AND product_list.unit_code = units.unit_id AND product_list.product_type = (SELECT type_id FROM product_types WHERE type = "Полуфабрикат")') as $row) {
   $halfway .= "'$row[title] [$row[create_date]]': {
         'registry_id': $row[registry_id],
         'name': '$row[title]',
         'count': $row[count],
         'unit': '$row[unit]',
         'create_date': '$row[create_date]',
         'expire_date': '$row[expire_date]'
      },";
}
$halfway .= '}';
$json .= $halfway;

//добавляем список всех полуфабрикатов для производства ($halfway_list)
$halfway_list = ',halfwayList: {';
foreach ($mysqli->query('SELECT product_list.product_id, product_list.title, units.unit, product_list.valid_days
      FROM product_list, units
      WHERE product_list.unit_code = units.unit_id AND product_list.product_type = (SELECT type_id FROM product_types WHERE type = "Полуфабрикат")') as $row) {
   $halfway_list .= "'$row[title]': {
         'id': '$row[product_id]',
         'unit': '$row[unit]',
         'valid_days': '$row[valid_days]'
      },";
}
$halfway_list .= '}';
$json .= $halfway_list;

//добавляем список всей готовой продукции для производства ($finished_list)
$finished_list = ',finishedList: {';
foreach ($mysqli->query('SELECT product_list.product_id, product_list.title, units.unit, product_list.valid_days
           FROM product_list, units
           WHERE product_list.unit_code = units.unit_id AND product_list.product_type = (SELECT type_id FROM product_types WHERE type = "Готовая продукция")') as $row) {
   $finished_list .= "'$row[title]': {
         'id': '$row[product_id]',
         'unit': '$row[unit]',
         'valid_days': '$row[valid_days]'
      },";
}
$finished_list .= '}';
$json .= $finished_list;

$json .= '}';
echo $json;
