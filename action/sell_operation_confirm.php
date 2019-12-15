<?php
include '../include/inc_config.php';
include '../include/session_config.php';
//include 'include/auth_redirect.php';


//TODO: попробовать декодить json из $_POST

//парсим полученный JSON в ассоциативный массив
$data = json_decode(file_get_contents('php://input'), true);
//$data = json_decode('{"docNum":"23","operationDate":"2019-12-19","partner":"ООО \"Молочный поставщик\"","productList":[{"registry_id":1,"name":"Йогурт Домодедовский КЛАССИЧЕСКИЙ жир. 2,7% (250гр)","count":"2623","createDate":"2019-12-21","extFat":"","extSolidity":"","extAcidity":""}]}', true);

/*DEBUG*/
/*$product_list = $data['productList'];

$limited_error = false;
foreach ($product_list as $key => $value) {
   $res = $mysqli->query("SELECT count FROM product_registry WHERE registry_id = $value[registry_id]");
   $res = $res->fetch_assoc();

   if ($value["count"] < $res["count"]) echo "попытка записать значение $value[count], в базе $res[count] (success)";
   else echo "попытка записать значение $value[count], в базе $res[count] (error)";

}
unset($value);
exit;*/
/*$product_list = $data['productList'];
$last_id = 12;
$values_str = '';
foreach ($product_list as $key => $value) {
   //получить срок годности
   $res = $mysqli->query("SELECT expire_date FROM product_registry WHERE registry_id = $value[registry_id]");
   $res = $res->fetch_assoc();
   $expire_date = $res["expire_date"];

   if (!$value["extFat"]) $value["extFat"] = 'DEFAULT';
   if (!$value["extSolidity"]) $value["extSolidity"] = 'DEFAULT';
   if (!$value["extAcidity"]) $value["extAcidity"] = 'DEFAULT';
   $values_str .= "($last_id, '$value[name]', $value[count], '$value[createDate]', '$expire_date', $value[extFat], $value[extSolidity], $value[extAcidity])";
   if ($key != count($product_list) - 1) $values_str .= ",";
}
unset($value);
echo $values_str;
exit;*/
/*$product_list = $data['productList'];
foreach ($product_list as $key => $value) {
   //внутри 2 запроса без валидации
   $res = $mysqli->query("SELECT count FROM product_registry WHERE registry_id = $value[registry_id]");
   $res = $res->fetch_assoc();
   echo $res["count"] ;

   if ($res["count"] <= 0) {
      $res = $mysqli->query("DELETE FROM product_registry WHERE operation_id = $value[registry_id]");
   }
}
unset($value);
exit;*/


//разбиваем на переменные для удобства
$operation_type = 'sell';
$doc_number = $data['docNum'];
$operation_date = $data['operationDate'];
$partner = $data['partner'];
$product_list = $data['productList'];

//валидации!!
//хотя бы HTML special chars добавить надо
//================================================
//в рамках проверки у каждого документа уникальный номер, не зависящий от операции (плюс решаем проблему с исчерпанием автоинкремента)
$res = $mysqli->query("SELECT * FROM operation_history WHERE document_number = '$doc_number' LIMIT 1");
if ($res->num_rows > 0) {
   //отправляем ответ клиенту
   header('Content-Type: application/json');
   echo json_encode(array('message' => 'Документ с таким номером уже существует. Откорректируйте его, либо обратитесь к администратору.', 'type' => 'error'));
   exit;
}

//выполняем запрос
/*
mysql> INSERT INTO joke(joke_text, joke_date, author_id)
    -> VALUES (‘Humpty Dumpty had a great fall.’, ‘1899–03–13’, (SELECT id FROM author WHERE author_name = ‘Famous Anthony’));
 Query OK, 1 row affected (0.03 sec)
 mysql_insert_id()
*/

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
   $res = $mysqli->query("UPDATE product_registry SET count = count - $value[count]");
   if ($mysqli->error) {
      //printf("Errormessage: %s\n", $mysqli->error);
      header('Content-Type: application/json');
      echo json_encode(array('update_error' => 'upderr'));
      exit;
   }
}
unset($value);

//=====================Пишем в operation_history=================================
$res = $mysqli->query("INSERT INTO operation_history(operation_type, document_number, operation_date, partner_code) 
VALUES(
   (SELECT operation_type_id FROM operation_types WHERE operation_name = '$operation_type'),
   '$doc_number',
   '$operation_date',
   (SELECT partner_id FROM partners WHERE name = '$partner')
)");

$last_id = $mysqli->query("SELECT LAST_INSERT_ID()");
$last_id->data_seek(0);
$last_id = $last_id->fetch_assoc();
$last_id = $last_id['LAST_INSERT_ID()'];

if ($mysqli->error) {
   //printf("Errormessage: %s\n", $mysqli->error);
   header('Content-Type: application/json');
   echo json_encode(array('history_error' => $last_id));
   exit;
}

//========================Пишем в operation_sell=============================================
/*header('Content-Type: application/json');
   echo json_encode(array('success' => $last_id));*/
$values_str = '';
foreach ($product_list as $key => $value) {
   //получить срок годности
   $res = $mysqli->query("SELECT expire_date FROM product_registry WHERE registry_id = $value[registry_id]");
   $res = $res->fetch_assoc();
   $expire_date = $res["expire_date"];

   if (!$value["extFat"]) $value["extFat"] = 'DEFAULT';
   if (!$value["extSolidity"]) $value["extSolidity"] = 'DEFAULT';
   if (!$value["extAcidity"]) $value["extAcidity"] = 'DEFAULT';
   $values_str .= "($last_id, '$value[name]', $value[count], '$value[createDate]', '$expire_date', $value[extFat], $value[extSolidity], $value[extAcidity])";
   if ($key != count($product_list) - 1) $values_str .= ",";
}
unset($value);

$res = $mysqli->query("INSERT INTO operation_sell(operation_id, product_name, count, create_date, expire_date, milk_fat, milk_solidity, milk_acidity) VALUES $values_str");
if ($mysqli->error) {
   //printf("Errormessage: %s\n", $mysqli->error);
   header('Content-Type: application/json');
   echo json_encode(array('add_error' => $last_id));
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


//=================================================================
/*SELECT EXISTS(SELECT operation_date
FROM operation_history
WHERE operation_date = '2019-12-12') AS exist

МЕНЯЕТСЯ ТОЛЬКО КОЛИЧЕСТВО
INSERT INTO operation_history(operation_type, document_number, operation_date, partner_code) 
VALUES 
   (1, '43243', '2019-12-20', 2),
   (1, '4232', '2019-12-20', 2)
ON DUPLICATE KEY UPDATE partner_code = VALUES(partner_code)

*/