<?php
include '../include/inc_config.php';
include '../include/session_config.php';
//include 'include/auth_redirect.php';


//TODO: попробовать декодить json из $_POST

//парсим полученный JSON в ассоциативный массив
$data = json_decode(file_get_contents('php://input'), true);
//$data = json_decode('{"docNum":"12312","operationDate":"2019-12-20","partner":"ИП Володянкин","productList":[{"name":"Йогурт Домодедовский КЛАССИЧЕСКИЙ жир. 2,7% (250гр)","count":"32","createDate":"2019-12-22","extFat":"","extSolidity":"","extAcidity":""},{"name":"Биокефир Домодедовский жир. 1% (930гр)","count":"3","createDate":"2019-12-22","extFat":"","extSolidity":"","extAcidity":""}]}', true);
//$data = json_decode('{"docNum":"3","operationDate":"2019-12-21","partner":"ИП Володянкин","productList":[{"name":"Йогурт Домодедовский КЛАССИЧЕСКИЙ жир. 2,7% (250гр)","count":"11","createDate":"2019-12-16","extFat":"","extSolidity":"","extAcidity":""}]}', true);


//разбиваем на переменные для удобства
$operation_type = 'add';
$doc_number = $data['docNum'];
$operation_date = $data['operationDate'];
$partner = $data['partner'];
$product_list = $data['productList'];
//валидации!!
//хотя бы HTML special chars добавить надо


//================================={проверка на наличие документа с таким номером}====================================================
//в рамках проверки у каждого документа уникальный номер, не зависящий от операции (плюс решаем проблему с исчерпанием автоинкремента)
$res = $mysqli->query("SELECT * FROM operation_history WHERE document_number = '$doc_number' LIMIT 1");
if ($res->num_rows > 0) {
   //отправляем ответ клиенту
   header('Content-Type: application/json');
   echo json_encode(array('message' => 'Документ с таким номером уже существует. Откорректируйте его, либо обратитесь к администратору.', 'type' => 'error'));
   exit;
}


//========================={запись в историю операций}=================================
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
   echo json_encode(array('message' => "history_error - last id ($mysqli->error)", 'type' => 'error'));
   exit;
}

//=========================={запись в историю прихода}===================================
$values_str = '';
foreach ($product_list as $key => $value) {
   if (!$value["extFat"]) $value["extFat"] = 'DEFAULT';
   if (!$value["extSolidity"]) $value["extSolidity"] = 'DEFAULT';
   if (!$value["extAcidity"]) $value["extAcidity"] = 'DEFAULT';

   //получаем дату окончания срока годности
   $res = $mysqli->query("SELECT DATE_ADD( '$value[createDate]', INTERVAL (SELECT valid_days FROM product_list WHERE title = '$value[name]') DAY ) AS expire");
   $res = $res->fetch_assoc();
   $expire_date = $res['expire'];

   $values_str .= "($last_id, '$value[name]', $value[count], '$value[createDate]', '$expire_date', $value[extFat], $value[extSolidity], $value[extAcidity])";
   if ($key != count($product_list) - 1) $values_str .= ",";
}
unset($value);

$res = $mysqli->query("INSERT INTO operation_add(operation_id, product_name, count, create_date, expire_date, milk_fat, milk_solidity, milk_acidity) VALUES $values_str");
if ($mysqli->error) {
   //printf("Errormessage: %s\n", $mysqli->error);
   header('Content-Type: application/json');
   echo json_encode(array('message' => "add error - last id ($mysqli->error)", 'type' => 'error'));
   exit;
}

//================================{пишем в реестр}=========================================
foreach ($product_list as $key => $value) {
   //проверка - существует ли такая партия в реестре
   $is_exist = $mysqli->query("SELECT EXISTS(SELECT * FROM product_registry WHERE product_id = (SELECT product_id FROM product_list WHERE title = '$value[name]') AND create_date = '$value[createDate]' LIMIT 1) AS exist;");
   if ($mysqli->error) {
      //printf("Errormessage: %s\n", $mysqli->error);
      header('Content-Type: application/json');
      echo json_encode(array('message' => "exist error - query ($mysqli->error)", 'type' => 'error'));
      exit;
   }
   //'1' or '0' bool (string)
   $is_exist = $is_exist->fetch_assoc();
   $is_exist = $is_exist['exist'];

   if ($is_exist) {
      //если есть такая запись в реестре - обновляем
      $res = $mysqli->query("UPDATE product_registry SET count = count + $value[count] WHERE product_id = (SELECT product_id FROM product_list WHERE title = '$value[name]') AND create_date = '$value[createDate]'");
      if ($mysqli->error) {
         //printf("Errormessage: %s\n", $mysqli->error);
         header('Content-Type: application/json');
         echo json_encode(array('message' => "registry_error - update ($mysqli->error)", 'type' => 'error'));
         exit;
      }
   } else {
      //если нет записи - создаем
      $res = $mysqli->query("INSERT INTO product_registry(product_id, count, create_date, expire_date) 
      VALUES 
		(
         (SELECT product_id FROM product_list WHERE title = '$value[name]'),
		   $value[count],
		   '$value[createDate]',
         (SELECT DATE_ADD('$value[createDate]', INTERVAL (SELECT valid_days FROM product_list WHERE title = '$value[name]') DAY ))
      );");
      if ($mysqli->error) {
         //printf("Errormessage: %s\n", $mysqli->error);
         header('Content-Type: application/json');
         echo json_encode(array('message' => "registry_error - insert ($mysqli->error)", 'type' => 'error'));
         exit;
      }
   }
}

header('Content-Type: application/json');
echo json_encode(array('message' => "Операция прихода успешно создана.", 'type' => 'success'));
