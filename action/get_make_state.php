<?php
//json общий объект
$json = '{';

//добавляем список СЫРЬЯ ($material)
$material = 'materials: {';
foreach ($mysqli->query('SELECT product_registry.registry_id, product_list.title, product_registry.count, units.unit, product_registry.create_date, product_registry.expire_date, product_registry.milk_fat, product_registry.milk_solidity, product_registry.milk_acidity
      FROM product_registry, product_list, units, product_types
      WHERE product_registry.product_id = product_list.product_id AND product_list.unit_code = units.unit_id AND product_list.product_type = (SELECT type_id FROM product_types WHERE type = "Сырье")') as $row) {
   $material .= "'$row[title]': {
      'registry_id': $row[registry_id],
      'count': $row[count],
      'unit': '$row[unit]',
      'create_date': '$row[create_date]',
      'expire_date': '$row[expire_date]',
      'milk_fat': $row[milk_fat],
      'milk_solidity': $row[milk_solidity],
      'milk_acidity': $row[milk_acidity]
   },";
}
$material .= '}';
$json .= $material;

//добавляем список ПОЛУФАБРИКАТОВ ($halfway)
$halfway = ',halfway: {';
foreach ($mysqli->query('SELECT product_registry.registry_id, product_list.title, product_registry.count, units.unit, product_registry.create_date, product_registry.expire_date, product_registry.milk_fat, product_registry.milk_solidity, product_registry.milk_acidity
     FROM product_registry, product_list, units, product_types
     WHERE product_registry.product_id = product_list.product_id AND product_list.unit_code = units.unit_id AND product_list.product_type = (SELECT type_id FROM product_types WHERE type = "Полуфабрикат")') as $row) {
   $halfway .= "'$row[title]': {
         'registry_id': $row[registry_id],
         'count': $row[count],
         'unit': '$row[unit]',
         'create_date': '$row[create_date]',
         'expire_date': '$row[expire_date]',
         'milk_fat': $row[milk_fat],
         'milk_solidity': $row[milk_solidity],
         'milk_acidity': $row[milk_acidity]
      },";
}
$halfway .= '}';
$json .= $halfway;

//добавляем список ГОТОВОЙ ПРОДУКЦИИ ($finished)
$finished = ',goods: {';
foreach ($mysqli->query('SELECT product_registry.registry_id, product_list.title, product_registry.count, units.unit, product_registry.create_date, product_registry.expire_date, product_registry.milk_fat, product_registry.milk_solidity, product_registry.milk_acidity
        FROM product_registry, product_list, units, product_types
        WHERE product_registry.product_id = product_list.product_id AND product_list.unit_code = units.unit_id AND product_list.product_type = (SELECT type_id FROM product_types WHERE type = "Готовая продукция")') as $row) {
   $finished .= "'$row[title]': {
            'registry_id': $row[registry_id],
            'count': $row[count],
            'unit': '$row[unit]',
            'create_date': '$row[create_date]',
            'expire_date': '$row[expire_date]',
            'milk_fat': $row[milk_fat],
            'milk_solidity': $row[milk_solidity],
            'milk_acidity': $row[milk_acidity]
         },";
}
$finished .= '}';
$json .= $finished;

$json .= '}';
echo $json;
