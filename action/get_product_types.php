<?php
include '../include/inc_config.php';
include '../include/session_config.php';

//response общий объект ответа
$response = array();

foreach ($mysqli->query('SELECT * FROM product_types') as $row) {
   $currentType = array(
        "type_id" => "$row[type_id]",
        "type" => "$row[type]"        
   );
   array_push($response, $currentType);
}

header('Content-Type: application/json');
echo json_encode($response);