<?php
//json общий объект
$json = '{';

//добавляем контрагентов
$patners = 'partners: {';
foreach ($mysqli->query('SELECT * FROM partners') as $row) {
   $patners .= "'$row[name]': null,";
}
$patners .= '}';
$json .= $patners;

//добавляем список номенклатур
$goods = ',goods: {';
foreach ($mysqli->query('SELECT product_registry.registry_id, product_list.title, product_registry.count, units.unit, product_registry.create_date, product_registry.expire_date, product_registry.milk_fat, product_registry.milk_solidity, product_registry.milk_acidity
  FROM product_registry, product_list, units
  WHERE product_registry.product_id = product_list.product_id AND product_list.unit_code = units.unit_id') as $row) {
   $goods .= "'$row[title]': {
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
$goods .= '}';
$json .= $goods;

$json .= '}';
echo $json;
