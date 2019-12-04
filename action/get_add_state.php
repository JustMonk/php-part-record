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
foreach ($mysqli->query('SELECT * FROM product_list INNER JOIN units ON product_list.unit_code = units.unit_id') as $row) {
   $goods .= "'$row[title]': {
      'valid_days': '$row[valid_days]',
      'id': '$row[product_id]',
      'extended_milk_fields': $row[extended_milk_fields]
   },";
}
$goods .= '}';
$json .= $goods;

$json .= '}';
echo $json;
