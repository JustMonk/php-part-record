<?php
include '../include/inc_config.php';
include '../include/session_config.php';

//response общий объект ответа
$response = array();

foreach ($mysqli->query('SELECT * FROM partners') as $row) {
   $response[$row['name']] = 'null';
}

header('Content-Type: application/json');
echo json_encode($response);
