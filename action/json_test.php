<?php
include '../include/inc_config.php';
include '../include/session_config.php';
//include 'include/auth_redirect.php';

//TODO: попробовать декодить json из $_POST

//парсим полученный JSON в ассоциативный массив
$data = json_decode('{"docNum":"2332","operationDate":"2019-12-19","partner":"ИП Володянкин","productList":[{"name":"Йогурт Домодедовский ПЕРСИК жир. 2,7% (250гр)","count":"43","createDate":"2019-12-29","extFat":"","extSolidity":"","extAcidity":""},{"name":"Сырое молочко","count":"32","createDate":"2019-12-29","extFat":"13","extSolidity":"2","extAcidity":"3"}]}', true);

/*DEBUG*/

$build_str = '';
foreach ($data["productList"] as &$value) {
   $build_str .= "$value[name]";
   $build_str .= "\n";
}
unset($value);

var_dump( $data );
echo( $build_str );
echo (count($data["productList"]));
echo ("<hr>");

$product_list = $data['productList'];
$row_id = 11; //mysqli_insert_id($mysqli);
$values_str = '';
foreach ($product_list as $key=>$value) {
   if (!$value["extFat"]) $value["extFat"] = 'DEFAULT'; 
   if (!$value["extSolidity"]) $value["extSolidity"] = 'DEFAULT'; 
   if (!$value["extAcidity"]) $value["extAcidity"] = 'DEFAULT'; 
   $values_str .= "($row_id, '$value[name]', $value[count], '$value[createDate]', '$value[createDate]', $value[extFat], $value[extSolidity], $value[extAcidity])";
   if ($key != count($product_list)-1) $values_str .= ",";
}
unset($value);

echo ($values_str);
exit;

//разбиваем на переменные для удобства
$operation_type = 'add';
$doc_number = $data['docNum'];
$operation_date = $data['operationDate'];
$partner = $data['partner'];
$product_list = $data['productList'];

//TODO валидации
//валидации на сервере, которых нет
//хотя бы HTML special chars добавить надо

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

if (!$mysqli->error) {
   //printf("Errormessage: %s\n", $mysqli->error);
   header('Content-Type: application/json');
   echo json_encode(array('success' => $mysqli->error));
   exit;
}

//=================================================================
$row_id = mysqli_insert_id($mysqli);
$values_str = '';
foreach ($product_list as $key => $value) {
   $values_str .= "($row_id, $value[name], $value[count], $value[createDate], $value[createDate], $value[extFat], $value[extSolidity], $value[extAcidity])";
   if ($key != count($product_list) - 1) $values_str .= ",";
}

$res = $mysqli->query("INSERT INTO operation_add(operation_id, product_name, count, create_date, expire_date, milk_fat, milk_solidity, milk_acidity) VALUES $values_str");
if (!$mysqli->error) {
   //printf("Errormessage: %s\n", $mysqli->error);
   header('Content-Type: application/json');
   echo json_encode(array('success' => $mysqli->error));
   exit;
}


/*
if ($res -> num_rows > 0) {
   //есть такой пользователь
   $row = $res->fetch_array(MYSQLI_ASSOC);
   
   $_SESSION['login'] = $row['login'];
   $_SESSION['name'] = $row['name'];
   $_SESSION['lastname'] = $row['lastname'];
   $_SESSION['isAutorized'] = true;

   header('Content-Type: application/json');
   echo json_encode(array('success' => true));
   
} else {
   //нет такого пользователя
   //возвращаем JSON
   header('Content-Type: application/json');
   echo json_encode(array('success' => false));
}
*/
