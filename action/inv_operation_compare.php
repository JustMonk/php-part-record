<?php
include '../include/inc_config.php';
include '../include/session_config.php';
//include 'include/auth_redirect.php';

//парсим полученный JSON в ассоциативный массив
$data = json_decode(file_get_contents('php://input'), true);
//$data = json_decode('{"productList":[{"name":"Снежок с мдж 2,7%","count":"321","createDate":"2019-12-20","extFat":"","extSolidity":"","extAcidity":""},{"name":"Йогурт Домодедовский ПЕРСИК жир. 2,7% (250гр)","count":"3","createDate":"2019-12-27","extFat":"","extSolidity":"","extAcidity":""},{"name":"Йогурт питьевой с клубникой, мдж 2,7%","count":"11","createDate":"2019-12-25","extFat":"","extSolidity":"","extAcidity":""}]}', true);
//$data = json_decode('{"productList":[{"name":"Йогурт Домодедовский КЛАССИЧЕСКИЙ жир. 2,7% (250гр)","count":"1","createDate":"2019-12-27","extFat":"","extSolidity":"","extAcidity":""}]}', true);
//$data = json_decode('{"productList":[{"name":"Биокефир Домодедовский жир. 1% (930гр)","count":"3","createDate":"2020-01-31"}]}', true);
//$data = json_decode('{"productList":[{"name":"Йогурт Домодедовский КЛУБНИКА жир. 2,7% (250гр)","count":"1","createDate":"2020-01-24"}]}', true);

//разбиваем на переменные для удобства
$product_list = $data['productList'];

//объект ответа
$response = array();
//оставшиеся ключи (id)
$used_id = array();

//формируем ответ в 2 прохода, находим переданные партии в реестре
//накладываем переданный с клиента список на текущий реестр
foreach ($product_list as $key => $value) {
   $res = $mysqli->query("SELECT product_registry.registry_id, product_list.title, units.unit, product_registry.count, product_registry.create_date, product_registry.product_id
   FROM product_registry, product_list, units
   WHERE product_registry.product_id = product_list.product_id AND product_list.unit_code = units.unit_id AND product_registry.product_id = (SELECT product_id FROM product_list WHERE title = '$value[name]') AND product_registry.create_date = '$value[createDate]'");
   $res = $res->fetch_assoc();
   if ($res) {
      //если в регистре есть запись
      $res["real_count"] = $value["count"];
      array_push($response, $res);
      array_push($used_id, $res["registry_id"]);
   } else {
      //если такой записи нет
      $res = $mysqli->query("SELECT product_list.title, units.unit, product_list.product_id
      FROM product_list, units
      WHERE product_list.unit_code = units.unit_id AND product_list.title = (SELECT title FROM product_list WHERE title = '$value[name]')");
      $res = $res->fetch_assoc();
      $res["registry_id"] = null;
      $res["count"] = '0';
      $res["real_count"] = $value["count"];
      $res["create_date"] = $value["createDate"];
      array_push($response, $res);
   }
}
unset($value);

//строка для условия NOT (в запросе)
$condition_string = '';
if (count($used_id) > 0) {
   foreach($used_id as $key => $value) {
      if ($key < count($used_id)-1) {
         $condition_string .= "NOT(product_registry.registry_id = $value";
         $condition_string .= " OR ";
      } elseif (count($used_id) == 1) {
         //если позиция всего одна
         $condition_string .= "NOT(product_registry.registry_id = $value) AND";
      } else {
         //закрывающая скобка (для последней позиции)
         $condition_string .= "product_registry.registry_id = $value) AND";
      }
   }
}
/*$condition_string = (if used id count > 0)
[NOT('... OR ...') AND] ...
=============================================
or if used id empty =
'' (empty string)
*/

$res = $mysqli->query("SELECT product_registry.registry_id, product_list.title, units.unit, product_registry.count, product_registry.create_date, product_registry.product_id
FROM product_registry, product_list, units
WHERE $condition_string product_registry.product_id = product_list.product_id AND product_list.unit_code = units.unit_id");
//если "оставшиеся" записи есть, то еще один проход
if ($res->num_rows > 1) {
   foreach ($res as $row) {
      $current_row = $row;
      $current_row["real_count"] = '0';
      //пушим в общий объект ответа
      array_push($response, $current_row);  
   }
}

header('Content-Type: application/json');
echo json_encode(array('compareList' => $response, 'type' => 'compare'));