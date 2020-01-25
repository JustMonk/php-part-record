<?php 
include 'session_config.php'; 
include 'auth_redirect.php';

//парсим полученный JSON в ассоциативный массив
$data = json_decode(file_get_contents('php://input'), true);

//разбиваем на переменные для удобства
$logout = $data['logout'];

if ($logout == true) {
   unset ($_SESSION['isAuthorized']);
   unset ($_SESSION['login']);
   destroySession();

   header('Content-Type: application/json');
   echo json_encode(array('response' => true));
} else {
   header('Content-Type: application/json');
   echo json_encode(array('response' => false));
}

?>