<?php
include '../include/inc_config.php';
include '../include/session_config.php';

//response общий объект ответа
$response = array();

foreach ($mysqli->query('SELECT * FROM units') as $row) {
   $currentUnit = array(
        "unit_id" => "$row[unit_id]",
        "unit" => "$row[unit]",
        "full_title" => "$row[full_title]"        
   );
   array_push($response, $currentUnit);
}

header('Content-Type: application/json');
echo json_encode($response);