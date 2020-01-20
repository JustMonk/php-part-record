<?php
include '../include/inc_config.php';
include '../include/session_config.php';
//include 'include/auth_redirect.php';


//TODO: попробовать декодить json из $_POST

//парсим полученный JSON в ассоциативный массив
$data = json_decode(file_get_contents('php://input'), true);
//$data = json_decode('{"docNum":"test_sell_2","operationDate":"2020-01-31","partner":"ОАО \"Кросс-докинг\"","productList":[{"registry_id":"9","product_id":"10","name":"Снежок с мдж 2,1% [2020-01-25]","count":"3","createDate":"2020-01-25"}]}', true);

//разбиваем на переменные для удобства
//htmlspecialchars - базовая валидация
$operation_type = 'sell';
$doc_number = htmlspecialchars($data['docNum']);
$operation_date = htmlspecialchars($data['operationDate']);
$partner = htmlspecialchars($data['partner'], ENT_NOQUOTES);
$product_list = $data['productList'];
//normalize object values
/*foreach ($product_list as $key => $value) {
   $value[$key] = htmlspecialchars($value[$key]);
}
unset($value);*/

//=========================={проверка на количество}===========================
//в рамках проверки у каждого документа уникальный номер, не зависящий от операции (плюс решаем проблему с исчерпанием автоинкремента)
$res = $mysqli->query("SELECT * FROM operation_history WHERE document_number = '$doc_number' LIMIT 1");
if ($res->num_rows > 0) {
   //отправляем ответ клиенту
   header('Content-Type: application/json');
   echo json_encode(array('message' => 'Документ с таким номером уже существует. Откорректируйте его, либо обратитесь к администратору.', 'type' => 'error'));
   exit;
}

//===================Минусуем из реестра=====================
//валидация на превышение количества
$limited_error = false;
foreach ($product_list as $key => $value) {
   $res = $mysqli->query("SELECT count FROM product_registry WHERE registry_id = $value[registry_id]");
   $res = $res->fetch_assoc();

   if ($value["count"] > $res["count"]) $limited_error = true;
}
if ($limited_error) {
   header('Content-Type: application/json');
   echo json_encode(array('message' => 'Попытка записать количество, превышающее остаток.', 'type' => 'error', 'value' => "$value[count]", 'result' => "$res[count]"));
   exit;
}
unset($value);

//если проверку прошли, то проходим циклом и вычитаем количество с реестра
foreach ($product_list as $key => $value) {
   $res = $mysqli->query("UPDATE product_registry SET count = count - $value[count] WHERE registry_id = $value[registry_id]");
   if ($mysqli->error) {
      //printf("Errormessage: %s\n", $mysqli->error);
      header('Content-Type: application/json');
      echo json_encode(array('message' => 'update_error: registry decrease', 'type' => 'error'));
      exit;
   }
}
unset($value);

//=====================Пишем в operation_history=================================
$res = $mysqli->query("INSERT INTO operation_history(operation_type, document_number, operation_date, partner_code, timestamp, user_code) 
VALUES(
   (SELECT operation_type_id FROM operation_types WHERE operation_name = '$operation_type'),
   '$doc_number',
   '$operation_date',
   (SELECT partner_id FROM partners WHERE name = '$partner'),
   DEFAULT,
   (SELECT user_id FROM users WHERE login = '$_SESSION[login]')
)");

$last_id = $mysqli->query("SELECT LAST_INSERT_ID()");
$last_id->data_seek(0);
$last_id = $last_id->fetch_assoc();
$last_id = $last_id['LAST_INSERT_ID()'];

if ($mysqli->error) {
   //printf("Errormessage: %s\n", $mysqli->error);
   header('Content-Type: application/json');
   echo json_encode(array('message' => 'history_error: insert', 'type' => 'error'));
   exit;
}

//========================Пишем в operation_sell=============================================
$values_str = '';
foreach ($product_list as $key => $value) {
   //получить срок годности
   $res = $mysqli->query("SELECT expire_date FROM product_registry WHERE registry_id = $value[registry_id]");
   $res = $res->fetch_assoc();
   $expire_date = $res["expire_date"];

   $values_str .= "($last_id, $value[product_id], (SELECT title FROM product_list WHERE product_id = $value[product_id]), $value[count], '$value[createDate]', '$expire_date')"; //'$value[name]'
   if ($key != count($product_list) - 1) $values_str .= ",";
}
unset($value);

$res = $mysqli->query("INSERT INTO operation_sell(operation_id, product_id, product_name, count, create_date, expire_date) VALUES $values_str");
if ($mysqli->error) {
   //printf("Errormessage: %s\n", $mysqli->error);
   header('Content-Type: application/json');
   echo json_encode(array('message' => "add_error: operation_sell insert ($mysqli->error) <br> $values_str", 'type' => 'error'));
   exit;
}

//еще одна проходка, проверяем исчерпали ли партию (количество 0). если исчерпали - то удаляем запись
foreach ($product_list as $key => $value) {
   //внутри 2 запроса без валидации
   $res = $mysqli->query("SELECT count FROM product_registry WHERE registry_id = $value[registry_id]");
   $res = $res->fetch_assoc();

   if ($res["count"] <= 0) {
      $res = $mysqli->query("DELETE FROM product_registry WHERE registry_id = $value[registry_id]");
   }
}
unset($value);


header('Content-Type: application/json');
echo json_encode(array('message' => 'Операция продажи успешно создана.', 'type' => 'success'));
