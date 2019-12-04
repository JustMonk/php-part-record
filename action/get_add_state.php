<?php
//json общий объект
$json = '{';

$patners = 'partners: {';   
foreach ($mysqli->query('SELECT * FROM partners') as $row) {
      $patners .= "'$row[name]': null,";
}
$patners .= '}';
$json .= $patners;

$json .= '}';
echo $json;