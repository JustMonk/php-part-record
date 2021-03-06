<?php
include '../include/inc_config.php';
include '../include/session_config.php';

//парсим полученный JSON в ассоциативный массив
$data = json_decode(file_get_contents('php://input'), true);

//разбиваем на переменные для удобства
$operation_type = 'add';
$doc_number = htmlspecialchars($data['docNum']);
$operation_date = htmlspecialchars($data['operationDate']);
$partner = htmlspecialchars($data['partner'], ENT_NOQUOTES);
$product_list = $data['productList'];
//если передано
$operation_id = intval(htmlspecialchars($data['operation_id']));

//================================={проверка на наличие документа с таким номером}====================================================
//в рамках проверки у каждого документа уникальный номер, не зависящий от операции (плюс решаем проблему с исчерпанием автоинкремента)
//если флаг rewrite не передан - то проверяем номер, если передан - то игнорируем проверку
if (!$data['rewrite']) {
   $res = $mysqli->query("SELECT * FROM operation_history WHERE document_number = '$doc_number' LIMIT 1");
   if ($res->num_rows > 0) {
      //отправляем ответ клиенту
      header('Content-Type: application/json');
      echo json_encode(array('message' => 'Документ с таким номером уже существует. Откорректируйте его, либо обратитесь к администратору.', 'type' => 'error'));
      exit;
   }

   //========================={запись в историю операций}=================================
   $user_login = $_SESSION['login'] ? $_SESSION['login'] : $data['user']; //если передан логин
   $res = $mysqli->query("INSERT INTO operation_history(operation_type, document_number, operation_date, partner_code, timestamp, user_code) 
   VALUES(
   (SELECT operation_type_id FROM operation_types WHERE operation_name = '$operation_type'),
   '$doc_number',
   '$operation_date',
   (SELECT partner_id FROM partners WHERE name = '$partner'),
   DEFAULT,
   (SELECT user_id FROM users WHERE login = '$user_login')
   )");

   $last_id = $mysqli->query("SELECT LAST_INSERT_ID()");
   $last_id->data_seek(0);
   $last_id = $last_id->fetch_assoc();
   $last_id = $last_id['LAST_INSERT_ID()'];

   if ($mysqli->error) {
      header('Content-Type: application/json');
      echo json_encode(array('message' => "history_error - last id ($mysqli->error)", 'type' => 'error'));
      exit;
   }
}

//=========================={запись в историю прихода}===================================
if ($data['rewrite'] && $operation_id) $last_id = $operation_id; //если это изменение, то ID заранее известен
$values_str = '';
foreach ($product_list as $key => $value) {
   if (!$value["extFat"]) $value["extFat"] = 'DEFAULT';
   if (!$value["extSolidity"]) $value["extSolidity"] = 'DEFAULT';
   if (!$value["extAcidity"]) $value["extAcidity"] = 'DEFAULT';

   //получаем дату окончания срока годности
   $res = $mysqli->query("SELECT DATE_ADD( '$value[createDate]', INTERVAL (SELECT valid_days FROM product_list WHERE title = '$value[name]') DAY ) AS expire");
   $res = $res->fetch_assoc();
   $expire_date = $res['expire'];

   $values_str .= "($last_id, $value[product_id], '$value[name]', $value[count], '$value[createDate]', '$expire_date', $value[extFat], $value[extSolidity], $value[extAcidity])";
   if ($key != count($product_list) - 1) $values_str .= ",";
}
unset($value);


$res = $mysqli->query("INSERT INTO operation_add(operation_id, product_id, product_name, count, create_date, expire_date, milk_fat, milk_solidity, milk_acidity) VALUES $values_str");
if ($mysqli->error) {
   header('Content-Type: application/json');
   echo json_encode(array('message' => "add error - last id = $last_id | $user_login ($mysqli->error)", 'type' => 'error'));
   exit;
}

//================================{пишем в реестр}=========================================
foreach ($product_list as $key => $value) {
   //проверка - существует ли такая партия в реестре
   $is_exist = $mysqli->query("SELECT EXISTS(SELECT * FROM product_registry WHERE product_id = (SELECT product_id FROM product_list WHERE title = '$value[name]') AND create_date = '$value[createDate]' LIMIT 1) AS exist;");
   if ($mysqli->error) {
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
         header('Content-Type: application/json');
         echo json_encode(array('message' => "registry_error - insert ($mysqli->error)", 'type' => 'error'));
         exit;
      }
   }
}
unset($value);

header('Content-Type: application/json');
echo json_encode(array('message' => "Операция прихода успешно создана.", 'type' => 'success'));
