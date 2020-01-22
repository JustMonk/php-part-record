<?php
include '../include/inc_config.php';
include '../include/session_config.php';
//include 'include/auth_redirect.php';


//TODO: попробовать декодить json из $_POST

//парсим полученный JSON в ассоциативный массив
$data = json_decode(file_get_contents('php://input'), true);
//$data = json_decode('{"docNum":"test_prod_1","operationDate":"2020-01-31","materialList":[{"registry_id":"7","product_id":"7","string_key":"Сырое молочко [2020-01-24]","name":"Сырое молочко","count":"11","unit":"л","createDate":"2020-01-24","expireDate":"2020-01-27"}],"productList":[{"product_id":"15","name":"Йогурт Домодедовский ПЕРСИК жир. 33%","count":"11","unit":"л"}]}', true);
//{"docNum":"2prod","operationDate":"2020-01-30","materialList":[{"registry_id":"147","product_id":"20","string_key":"Йогурт Домодедовский ВИШНЯ жир. 2,7% (350гр) [2020-01-31]","name":"Йогурт Домодедовский ВИШНЯ жир. 2,7% (350гр)","count":"12","unit":"шт","createDate":"2020-01-31","expireDate":"2020-02-07"}],"productList":[{"product_id":"37","name":"молоко полной безысходности","count":"500","unit":"л"}]}
//$data = json_decode('{"operation_id":"104","operation_type":"prod","docNum":"prodie","operationDate":"2020-01-30","materialList":[{"product_id":"20","name":"Йогурт Домодедовский ВИШНЯ жир. 2,7% (350гр)","count":"12","unit":"шт","type":"Готовая продукция","createDate":"2020-01-31","expireDate":"2020-02-07"}],"productList":[{"product_id":"37","name":"молоко полной безысходности","count":"500","unit":"л","type":"Готовая продукция"}],"rewrite":true}', true);

//разбиваем на переменные для удобства
//htmlspecialchars - базовая валидация
$operation_type = 'prod';
$doc_number = htmlspecialchars($data['docNum']);
$operation_date = htmlspecialchars($data['operationDate']);
//если передано
$operation_id = intval(htmlspecialchars($data['operation_id']));

$material_list = $data['materialList'];
//normalize object values
/*foreach ($material_list as $key => $value) {
   $value[$key] = htmlspecialchars($value[$key]);
}
unset($value);*/
$product_list = $data['productList'];
//normalize object values
/*foreach ($product_list as $key => $value) {
   $value[$key] = htmlspecialchars($value[$key]);
}
unset($value);*/


//=========================={проверка на количество}===========================
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
}

//===================Минусуем из реестра=====================
//валидация на превышение количества
$limited_error = false;
foreach ($material_list as $key => $value) {
   //$res = $mysqli->query("SELECT count FROM product_registry WHERE registry_id = $value[registry_id]");
   $res = $mysqli->query("SELECT count FROM product_registry WHERE product_id = '$value[product_id]' AND create_date = '$value[createDate]'");
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
foreach ($material_list as $key => $value) {
   $res = $mysqli->query("UPDATE product_registry SET count = count - $value[count] WHERE product_id = '$value[product_id]' AND create_date = '$value[createDate]'");
   if ($mysqli->error) {
      //printf("Errormessage: %s\n", $mysqli->error);
      header('Content-Type: application/json');
      echo json_encode(array('message' => 'update_error: registry decrease', 'type' => 'error'));
      exit;
   }
}
unset($value);


//========================Пишем в operation_prod_add=============================================
if ($data['rewrite'] && $operation_id) $last_id = $operation_id; //если это изменение, то ID заранее известен
$values_str = '';
foreach ($product_list as $key => $value) {
   //получить срок годности
   //$res = $mysqli->query("SELECT expire_date FROM product_registry WHERE registry_id = $value[registry_id]");
   $res = $mysqli->query("SELECT DATE_ADD('$value[createDate]', INTERVAL (SELECT valid_days FROM product_list WHERE product_id = $value[product_id]) DAY) as expire_date;");
   $res = $res->fetch_assoc();
   $expire_date = $res["expire_date"];

   $values_str .= "($last_id, '$value[product_id]', '$value[name]', $value[count], '$operation_date', (SELECT DATE_ADD('$operation_date', INTERVAL (SELECT valid_days FROM product_list WHERE product_id = '$value[product_id]') DAY)) )";
   if ($key != count($product_list) - 1) $values_str .= ",";
}
unset($value);


$res = $mysqli->query("INSERT INTO operation_prod_add(operation_id, product_id, product_name, count, create_date, expire_date) VALUES $values_str");
if ($mysqli->error) {
   //printf("Errormessage: %s\n", $mysqli->error);
   header('Content-Type: application/json');
   echo json_encode(array('message' => "add_error: operation_prod_add insert ($mysqli->error) <br> $values_str", 'type' => 'error'));
   exit;
}

//===============================Добавляем, либо плюсуем в реестр=============================================
//================================{пишем в реестр}=========================================
foreach ($product_list as $key => $value) {
   //проверка - существует ли такая партия в реестре
   $is_exist = $mysqli->query("SELECT EXISTS(SELECT * FROM product_registry WHERE product_id = $value[product_id] AND create_date = '$operation_date' LIMIT 1) AS exist;");
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
      $res = $mysqli->query("UPDATE product_registry SET count = count + $value[count] WHERE product_id = $value[product_id] AND create_date = '$operation_date'");
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
         $value[product_id],
		   $value[count],
		   '$operation_date',
         (SELECT DATE_ADD('$operation_date', INTERVAL (SELECT valid_days FROM product_list WHERE product_id = '$value[product_id]') DAY ))
      );");
      if ($mysqli->error) {
         //printf("Errormessage: %s\n", $mysqli->error);
         header('Content-Type: application/json');
         echo json_encode(array('message' => "registry_error - insert ($mysqli->error)", 'type' => 'error'));
         exit;
      }
   }
}

//========================Пишем в operation_prod_consume=============================================
$values_str = '';
foreach ($material_list as $key => $value) {
   //получить срок годности
   //$res = $mysqli->query("SELECT expire_date FROM product_registry WHERE registry_id = $value[registry_id]");
   $res = $mysqli->query("SELECT expire_date FROM product_registry WHERE product_id = $value[product_id] AND create_date = '$value[createDate]'");
   $res = $res->fetch_assoc();
   $expire_date = $res["expire_date"];

   $values_str .= "($last_id, $value[product_id], '$value[name]', $value[count], '$value[createDate]', '$expire_date')";
   if ($key != count($material_list) - 1) $values_str .= ",";
}
unset($value);

$res = $mysqli->query("INSERT INTO operation_prod_consume(operation_id, product_id, product_name, count, create_date, expire_date) VALUES $values_str");
if ($mysqli->error) {
   //printf("Errormessage: %s\n", $mysqli->error);
   header('Content-Type: application/json');
   echo json_encode(array('message' => "add_error: operation_prod_consume insert ($mysqli->error) <br> $values_str", 'type' => 'error'));
   exit;
}

//еще одна проходка, проверяем исчерпали ли партию (количество 0). если исчерпали - то удаляем запись
foreach ($material_list as $key => $value) {
   //внутри 2 запроса без валидации
   $res = $mysqli->query("SELECT count FROM product_registry WHERE product_id = $value[product_id] AND create_date = '$operation_date'");
   $res = $res->fetch_assoc();

   if ($res["count"] <= 0) {
      $res = $mysqli->query("DELETE FROM product_registry WHERE product_id = $value[product_id] AND create_date = '$operation_date'");
   }
}
unset($value);


header('Content-Type: application/json');
echo json_encode(array('message' => 'Операция производства успешно создана.', 'type' => 'success'));
