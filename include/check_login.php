<?php 
include 'inc_config.php'; 
include 'session_config.php'; 
//include 'include/auth_redirect.php';

//TODO: попробовать декодить json из $_POST

//парсим полученный JSON в ассоциативный массив
$data = json_decode(file_get_contents('php://input'), true);

//разбиваем на переменные для удобства
$login = $data['username'];
$password = $data['password'];

//выполняем запрос
$res = $mysqli->query("SELECT * FROM users WHERE login='$login' AND password='$password' LIMIT 1");
if ($res -> num_rows > 0) {
   //есть такой пользователь
   $row = $res->fetch_array(MYSQLI_ASSOC);
   
   $_SESSION['login'] = $row['login'];
   $_SESSION['name'] = $row['name'];
   $_SESSION['lastname'] = $row['lastname'];
   $_SESSION['isAutorized'] = true;

   header('Content-Type: application/json');
   echo json_encode(array('success' => true));
   
} else {
   //нет такого пользователя
   //возвращаем JSON
   header('Content-Type: application/json');
   echo json_encode(array('success' => false));
}

?>