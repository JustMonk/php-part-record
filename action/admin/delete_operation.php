<?php
include '../../include/inc_config.php';
include '../../include/session_config.php';

//парсим полученный JSON в ассоциативный массив
$data = json_decode(file_get_contents('php://input'), true);

//разбиваем на переменные для удобства
//htmlspecialchars - базовая валидация
$operation_id = htmlspecialchars($data['operation_id']);
$operation_type = htmlspecialchars($data['operation_type']);
$doc_number = htmlspecialchars($data['docNum']);
$operation_date = htmlspecialchars($data['operationDate']);
$partner = htmlspecialchars($data['partner'], ENT_NOQUOTES);
$rewrite = boolval(htmlspecialchars($data['rewrite']));
//фикс доступа к сессии при обращении к скрипту
$data['user'] = "$_SESSION[login]";

//проверяем существует ли операция
$res = $mysqli->query("SELECT * FROM operation_history WHERE operation_id = '$operation_id' LIMIT 1");
if ($res->num_rows < 1) {
   header('Content-Type: application/json');
   echo json_encode(array('message' => 'Операции с таким номером не существует', 'type' => 'error'));
   exit;
}

//логика удаления операции в зависимости от типа
//=============приход===============
if ($operation_type == 'add') {
   //списываем из реестра (если есть)
   foreach ($mysqli->query("SELECT * FROM operation_add WHERE operation_id = '$operation_id'") as $row) {
      $mysqli->query("UPDATE product_registry SET count = (SELECT count FROM (SELECT * FROM product_registry) AS current_count WHERE product_id = '$row[product_id]' AND create_date = '$row[create_date]' LIMIT 1) - $row[count]
      WHERE product_id = '$row[product_id]' AND create_date = '$row[create_date]' LIMIT 1");
      //если количество 0 - то удяляем запись из реестра
      $count = $mysqli->query("SELECT count FROM product_registry WHERE product_id = '$row[product_id]' AND create_date = '$row[create_date]' LIMIT 1");
      $count = ($count->fetch_assoc())['count'];
      if (intval($count) < 1) {
         $mysqli->query("DELETE FROM product_registry WHERE product_id = '$row[product_id]' AND create_date = '$row[create_date]' LIMIT 1");
      }
   }
   //удяляем из истории прихода
   $res = $mysqli->query("DELETE FROM operation_add WHERE operation_id = '$operation_id'");

   //если изменяем - то запрос на добавление нового списка, иначе просто удаляем
   if ($rewrite) {
      $options = array(
         'http' => array(
            'method'  => 'POST',
            'content' => json_encode($data),
            'header' =>  "Content-Type: application/json\r\n" .
               "Accept: application/json\r\n"
         )
      );

      $context  = stream_context_create($options);
      $result = file_get_contents('http://' . $_SERVER['HTTP_HOST'] . '/action/add_operation_confirm.php', true, $context); //( $url, false, $context );
      $response = json_decode($result);
   } else {
      //удаляем из истории
      $res = $mysqli->query("DELETE FROM operation_history WHERE operation_id = '$operation_id'");
   }
}

//=============продажа===============
if ($operation_type == 'sell') {
   //возвращаем в реестр
   foreach ($mysqli->query("SELECT * FROM operation_sell WHERE operation_id = '$operation_id'") as $row) {
      //проверка - существует ли такая партия в реестре
      $is_exist = $mysqli->query("SELECT EXISTS(SELECT * FROM product_registry WHERE product_id = '$row[product_id]' AND create_date = '$row[create_date]' LIMIT 1) AS exist;");
      //'1' or '0' bool (string)
      $is_exist = $is_exist->fetch_assoc();
      $is_exist = $is_exist['exist'];

      if ($is_exist) {
         //если есть такая запись в реестре - обновляем
         $res = $mysqli->query("UPDATE product_registry SET count = count + $row[count] WHERE product_id = $row[product_id] AND create_date = '$row[create_date]'");
      } else {
         //если нет записи - создаем
         $res = $mysqli->query("INSERT INTO product_registry(product_id, count, create_date, expire_date) 
         VALUES 
		   (
         $row[product_id],
		   $row[count],
		   '$row[create_date]',
         (SELECT DATE_ADD('$row[create_date]', INTERVAL (SELECT valid_days FROM product_list WHERE product_id = '$row[product_id]') DAY ))
         );");
      }
   }
   //удяляем из истории продаж
   $res = $mysqli->query("DELETE FROM operation_sell WHERE operation_id = '$operation_id'");

   //если изменяем - то запрос на добавление нового списка, иначе просто удаляем
   if ($rewrite) {
      $options = array(
         'http' => array(
            'method'  => 'POST',
            'content' => json_encode($data),
            'header' =>  "Content-Type: application/json\r\n" .
               "Accept: application/json\r\n"
         )
      );

      $context  = stream_context_create($options);
      $result = file_get_contents('http://' . $_SERVER['HTTP_HOST'] . '/action/sell_operation_confirm.php', true, $context); //( $url, false, $context );
      $response = json_decode($result);
   } else {
      //удаляем из истории
      $res = $mysqli->query("DELETE FROM operation_history WHERE operation_id = '$operation_id'");
   }
}

//=============производство===============
if ($operation_type == 'prod') {
   //сначала списываем добавленное
   //списываем из реестра (если есть)
   foreach ($mysqli->query("SELECT * FROM operation_prod_add WHERE operation_id = '$operation_id'") as $row) {
      $mysqli->query("UPDATE product_registry SET count = (SELECT count FROM (SELECT * FROM product_registry) AS current_count WHERE product_id = '$row[product_id]' AND create_date = '$row[create_date]' LIMIT 1) - $row[count]
      WHERE product_id = '$row[product_id]' AND create_date = '$row[create_date]' LIMIT 1");
      
      //если количество 0 - то удяляем запись из реестра
      $count = $mysqli->query("SELECT count FROM product_registry WHERE product_id = '$row[product_id]' AND create_date = '$row[create_date]' LIMIT 1");
      $count = ($count->fetch_assoc())['count'];

      if (intval($count) < 1) {
         $mysqli->query("DELETE FROM product_registry WHERE product_id = '$row[product_id]' AND create_date = '$row[create_date]' LIMIT 1");
      }
   }
   //удяляем из истории производимой продукции
   $res = $mysqli->query("DELETE FROM operation_prod_add WHERE operation_id = '$operation_id'");

   //затем возвращаем в реестр израсходованное
   foreach ($mysqli->query("SELECT * FROM operation_prod_consume WHERE operation_id = '$operation_id'") as $row) {
      //проверка - существует ли такая партия в реестре
      $is_exist = $mysqli->query("SELECT EXISTS(SELECT * FROM product_registry WHERE product_id = '$row[product_id]' AND create_date = '$row[create_date]' LIMIT 1) AS exist;");
      //'1' or '0' bool (string)
      $is_exist = $is_exist->fetch_assoc();
      $is_exist = $is_exist['exist'];

      if ($is_exist) {
         //если есть такая запись в реестре - обновляем
         $res = $mysqli->query("UPDATE product_registry SET count = count + $row[count] WHERE product_id = $row[product_id] AND create_date = '$row[create_date]'");
      } else {
         //если нет записи - создаем
         $res = $mysqli->query("INSERT INTO product_registry(product_id, count, create_date, expire_date) 
         VALUES 
		   (
         $row[product_id],
		   $row[count],
		   '$row[create_date]',
         (SELECT DATE_ADD('$row[create_date]', INTERVAL (SELECT valid_days FROM product_list WHERE product_id = '$row[product_id]') DAY ))
         );");
      }
   }
   //удяляем из истории сырья
   $res = $mysqli->query("DELETE FROM operation_prod_consume WHERE operation_id = '$operation_id'");

   //если изменяем - то запрос на добавление нового списка, иначе просто удаляем
   if ($rewrite) {
      $options = array(
         'http' => array(
            'method'  => 'POST',
            'content' => json_encode($data),
            'header' =>  "Content-Type: application/json\r\n" .
               "Accept: application/json\r\n"
         )
      );

      $context  = stream_context_create($options);
      $result = file_get_contents('http://' . $_SERVER['HTTP_HOST'] . '/action/make_operation_confirm.php', true, $context); //( $url, false, $context );
      $response = json_decode($result);
   } else {
      //удаляем из истории
      $res = $mysqli->query("DELETE FROM operation_history WHERE operation_id = '$operation_id'");
   }
}

header('Content-Type: application/json');
echo $result;
exit;