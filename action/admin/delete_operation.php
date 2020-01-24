<?php
include '../../include/inc_config.php';
include '../../include/session_config.php';
//include 'include/auth_redirect.php';


//TODO: попробовать декодить json из $_POST

//парсим полученный JSON в ассоциативный массив
$data = json_decode(file_get_contents('php://input'), true);
//$data = json_decode('{"docNum":"12312","operationDate":"2019-12-20","partner":"ИП Володянкин","productList":[{"name":"Йогурт Домодедовский КЛАССИЧЕСКИЙ жир. 2,7% (250гр)","count":"32","createDate":"2019-12-22","extFat":"","extSolidity":"","extAcidity":""},{"name":"Биокефир Домодедовский жир. 1% (930гр)","count":"3","createDate":"2019-12-22","extFat":"","extSolidity":"","extAcidity":""}]}', true);
//$data = json_decode('{"operation_id":"119","operation_type":"prod","docNum":"пр/2","operationDate":"2020-01-13","materialList":[{"product_id":"67","name":"БЕЗ ДАТЫ. Молоко пастеризованное жир. 2,5% (930мл)","count":"0","unit":"шт","type":"Полуфабрикат","createDate":"2020-01-03","expireDate":"2020-01-13"}],"productList":[{"product_id":"68","name":"БЕЗ ДАТЫ. Молоко пастеризованное жир. 3,2% (2л)","count":"188","unit":"шт","type":"Полуфабрикат"},{"product_id":"66","name":"БЕЗ ДАТЫ. Молоко пастеризованное жир. 1,5% (930мл)","count":"120","unit":"шт","type":"Полуфабрикат"},{"product_id":"67","name":"БЕЗ ДАТЫ. Молоко пастеризованное жир. 2,5% (930мл)","count":"600","unit":"шт","type":"Полуфабрикат"},{"product_id":"69","name":"БЕЗ ДАТЫ. Молоко пастеризованное жир. 3,2% (930мл)","count":"1240","unit":"шт","type":"Полуфабрикат"},{"product_id":"70","name":"БЕЗ ДАТЫ. Молоко пастеризованное жир. 4% (930мл)","count":"240","unit":"шт","type":"Полуфабрикат"},{"product_id":"82","name":"БЕЗ ДАТЫ. Творог жир. 5% (250гр)","count":"257","unit":"шт","type":"Полуфабрикат"},{"product_id":"84","name":"БЕЗ ДАТЫ. Творог жир. 9% (250гр)","count":"264","unit":"шт","type":"Полуфабрикат"},{"product_id":"72","name":"БЕЗ ДАТЫ. Молоко топленое жир. 3,5% (930мл)","count":"279","unit":"шт","type":"Полуфабрикат"},{"product_id":"83","name":"БЕЗ ДАТЫ. Творог жир. 5% (300гр)","count":"87","unit":"шт","type":"Полуфабрикат"},{"product_id":"85","name":"БЕЗ ДАТЫ. Творог жир. 9% (300гр)","count":"100","unit":"шт","type":"Полуфабрикат"},{"product_id":"86","name":"БЕЗ ДАТЫ. Творог жир. 9% (500гр) / контейнер","count":"10","unit":"шт","type":"Полуфабрикат"},{"product_id":"87","name":"БЕЗ ДАТЫ. Творог жир. 9% (500гр) / пакет","count":"171","unit":"шт","type":"Полуфабрикат"},{"product_id":"65","name":"БЕЗ ДАТЫ. Масса творожная с изюмом (300гр)","count":"7","unit":"шт","type":"Полуфабрикат"},{"product_id":"79","name":"БЕЗ ДАТЫ. Сыр БРЫНЗА жир. 40% (300гр)","count":"19","unit":"шт","type":"Полуфабрикат"},{"product_id":"90","name":"БЕЗ ДАТЫ. Творог жир. 9% в ведре (3кг)","count":"1","unit":"шт","type":"Полуфабрикат"}],"rewrite":true}', true);


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

//ситуативные параметры (зависят от операции) //ВОЗМОЖНО не требуются, т.к уже есть объект $data для передачи
/*$product_list = $data['productList'];
$material_list = $data['materialList']; //для производства*/

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
   //удяляем из истории прихода
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
   //удяляем из истории продаж
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