<?php
include '../../include/inc_config.php';
include '../../include/session_config.php';

$id = htmlspecialchars($_GET["id"]);

$res = $mysqli->query("SELECT users.login, account_types.title as access, users.name, users.lastname
FROM users, account_types
   WHERE user_id = '$id' AND users.access_id = account_types.ID
   LIMIT 1");
if ($res->num_rows < 0) {
   //отправляем ответ клиенту
   header('Content-Type: application/json');
   echo json_encode(array('message' => 'ID не найден', 'type' => 'error'));
   exit;
}
$res = $res->fetch_assoc();

header('Content-Type: application/json');
echo json_encode($res);

exit;