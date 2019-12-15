<?php
include '../include/inc_config.php';
include '../include/session_config.php';
//include 'include/auth_redirect.php';


//TODO: попробовать декодить json из $_POST

//парсим полученный JSON в ассоциативный массив
$data = json_decode(file_get_contents('php://input'), true);
//$data = json_decode('{"docNum":"12312","operationDate":"2019-12-20","partner":"ИП Володянкин","productList":[{"name":"Йогурт Домодедовский КЛАССИЧЕСКИЙ жир. 2,7% (250гр)","count":"32","createDate":"2019-12-22","extFat":"","extSolidity":"","extAcidity":""},{"name":"Биокефир Домодедовский жир. 1% (930гр)","count":"3","createDate":"2019-12-22","extFat":"","extSolidity":"","extAcidity":""}]}', true);

/*DEBUG*/

/*$build_str = '';
foreach ($data["productList"] as &$value) {
   $build_str .= "$value[name]";
   $build_str .= "\n";
}
var_dump( $data );
echo( $build_str );
echo (count($data["productList"]));
echo ("<hr>");

$product_list = $data['productList'];
$row_id = 11; //mysqli_insert_id($mysqli);
$values_str = '';
foreach ($product_list as $key=>$value) {
    $values_str .= "($row_id, $value[name], $value[count], $value[createDate], $value[createDate], $value[extFat], $value[extSolidity], $value[extAcidity])";
    if ($key != count($product_list)-1) $values_str .= ",";
}
echo ($values_str);*/
/*$last_id = 92;
$product_list = $data['productList'];
$values_str = '';
foreach ($product_list as $key=>$value) {
   if (!$value["extFat"]) $value["extFat"] = 'DEFAULT'; 
   if (!$value["extSolidity"]) $value["extSolidity"] = 'DEFAULT'; 
   if (!$value["extAcidity"]) $value["extAcidity"] = 'DEFAULT'; 
   $values_str .= "($last_id, '$value[name]', $value[count], '$value[createDate]', '$value[createDate]', $value[extFat], $value[extSolidity], $value[extAcidity])";
   if ($key != count($product_list)-1) $values_str .= ",";
}
echo($values_str);

exit;*/

/*$is_exist = $mysqli->query("SELECT EXISTS(SELECT * FROM product_registry WHERE create_date = '2019-12-14' AND milk_fat = 1 AND milk_solidity = 0 AND milk_acidity = 0) AS exist;");
$is_exist->data_seek(0);
$is_exist = $is_exist->fetch_assoc();
$is_exist = $is_exist['exist'];
echo "type of <b>is_exist</b> = ", gettype($is_exist), "\n <br>";
echo "value of <b>is_exist</b> = ", $is_exist, "\n <br>";

if ($is_exist) {
   echo "exist (1) \n";
   echo gettype($is_exist);
} else {
   echo "dont exist (0) \n";
   echo gettype($is_exist);
}

exit;*/

//разбиваем на переменные для удобства
$operation_type = 'add';
$doc_number = $data['docNum'];
$operation_date = $data['operationDate'];
$partner = $data['partner'];
$product_list = $data['productList'];

//валидации!!
//хотя бы HTML special chars добавить надо
//================================================
//в рамках проверки у каждого документа уникальный номер, не зависящий от операции (плюс решаем проблему с исчерпанием автоинкремента)
$res = $mysqli->query("SELECT * FROM operation_history WHERE document_number = '$doc_number' LIMIT 1");
if ($res -> num_rows > 0) {
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

//=================================================================
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

//=================================================================
/*header('Content-Type: application/json');
   echo json_encode(array('success' => $last_id));*/

$values_str = '';
foreach ($product_list as $key => $value) {
   if (!$value["extFat"]) $value["extFat"] = 'DEFAULT';
   if (!$value["extSolidity"]) $value["extSolidity"] = 'DEFAULT';
   if (!$value["extAcidity"]) $value["extAcidity"] = 'DEFAULT';
   $values_str .= "($last_id, '$value[name]', $value[count], '$value[createDate]', '$value[createDate]', $value[extFat], $value[extSolidity], $value[extAcidity])";
   if ($key != count($product_list) - 1) $values_str .= ",";
}
unset($value);

$res = $mysqli->query("INSERT INTO operation_add(operation_id, product_name, count, create_date, expire_date, milk_fat, milk_solidity, milk_acidity) VALUES $values_str");
if ($mysqli->error) {
   //printf("Errormessage: %s\n", $mysqli->error);
   header('Content-Type: application/json');
   echo json_encode(array('add_error' => $last_id));
   exit;
}

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

foreach ($product_list as $key => $value) {
   if (!$value["extFat"]) $value["extFat"] = 0;
   if (!$value["extSolidity"]) $value["extSolidity"] = 0;
   if (!$value["extAcidity"]) $value["extAcidity"] = 0;

   $is_exist = $mysqli->query("SELECT EXISTS(SELECT * FROM product_registry WHERE create_date = '$value[create_date]' AND milk_fat = $value[milk_fat] AND milk_fat = $value[milk_solidity] AND milk_fat = $value[milk_acidity]) AS exist;");
   //после проведения тестов, выяснилось что $is_exist сразу как bool возвращается (либо преобразовывается)
   /*$is_exist->data_seek(0);
   $is_exist = $is_exist->fetch_assoc();
   $is_exist = $is_exist['exist'];*/

   if ($is_exist) {
      //если есть такая запись в реестре - обновляем
      $res = $mysqli->query("UPDATE product_registry SET count = count + $value[count] WHERE product_id = (SELECT product_id FROM product_list WHERE title = $value[name]) AND create_date = '$value[createDate]' AND milk_fat = $value[extFat] AND mild_solidity = $value[extSolidity] AND milk_acidity = $value[extAcidity]");
      if ($mysqli->error) {
         //printf("Errormessage: %s\n", $mysqli->error);
         header('Content-Type: application/json');
         echo json_encode(array('registry_error' => 'update'));
         exit;
      }
   } else {
      //если нет записи - создаем
      $res = $mysqli->query("INSERT INTO product_registry(product_id, count, create_date, expire_date, milk_fat, milk_solidity, milk_acidity) 
      VALUES 
		((SELECT product_id FROM product_list WHERE title = '$value[name]'),
		 $value[count],
		  '$value[createDate]',
		    (SELECT DATE_ADD( '$value[createDate]', INTERVAL (SELECT valid_days FROM product_list WHERE title = '$value[name]') DAY ) ),
			 $value[extFat],
			  $value[extSolidity],
            $value[extAcidity]);");
      if ($mysqli->error) {
         //printf("Errormessage: %s\n", $mysqli->error);
         header('Content-Type: application/json');
         echo json_encode(array('registry_error' => 'insert'));
         exit;
      }
   }
}

header('Content-Type: application/json');
echo json_encode(array('message' => 'Операция прихода успешно создана.', 'type' => 'success'));