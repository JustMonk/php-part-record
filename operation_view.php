<?php
include './include/inc_config.php';
include './include/session_config.php';
include './include/auth_redirect.php';
?>

<?php
$id = $_GET['id'];
$id = htmlspecialchars($id);

$res = $mysqli->query("SELECT operation_history.operation_id, operation_types.operation_name, operation_history.document_number, operation_history.operation_date, partners.name AS partner, operation_history.timestamp, users.login AS user
FROM operation_history
LEFT JOIN partners ON operation_history.partner_code = partners.partner_id OR partners.partner_id IS NULL
LEFT JOIN operation_types ON operation_history.operation_type = operation_types.operation_type_id
LEFT JOIN users ON operation_history.user_code = users.user_id
WHERE operation_id = $id");

if ($res) $res = $res->fetch_assoc();

//рендер табличной части
$table = getTable($res['operation_name']);
function getTable($operation_type)
{
   switch ($operation_type) {
      case 'add':
         $path = './include/inc_add_form.php';
         break;
      case 'sell':
         $path = './include/inc_sell_form.php';
         break;
      case 'prod':
         $path = './include/inc_make_form.php';
         break;
      case 'inv':
         $path = './include/inc_inv_form.php';
         break;
   }
   return $path;
}


if ($res['operation_name'] == 'add') {
   $incomeTable = array();
   $result = $mysqli->query("SELECT * FROM operation_add WHERE operation_id = $id");
   foreach ($result as $row) {
      if ($row) {
         $current = array(
            "name" => $row['product_name'],
            "count" => $row['count'],
            "createDate" => $row['create_date'],
            "expireDate" => $row['expire_date'],
            "extFat" => $row['milk_fat'],
            "extSolidity" => $row['milk_solidity'],
            "extAcidity" => $row['milk_acidity']
         );
         array_push($incomeTable, $current);
      }
   }
   $json = json_encode($incomeTable);
   $script = '<script>
   function incomeTableRender() {
      let num = 1;
      let tableBody = document.getElementById("income-table").tBodies[0];
      tableBody.innerHTML = "";
      for (let entry of globalState.incomeTable) {
         let tr = document.createElement("tr");
         tr.setAttribute("data-id", entry[0]);
         tr.setAttribute("data-action", "product-edit");
         tr.innerHTML = `<tr>
            <td>${num}</td>
            <td>${entry[1].name}</td>
            <td>${entry[1].count}</td>
            <td>${entry[1].createDate}</td>
            <td>${entry[1].expireDate}</td>
         </tr>`;
         tableBody.append(tr);
         num++;
      }
   }
   let globalState = {};
   globalState.incomeTable = new Map();
   ' . "
   let data = JSON.parse('$json');
   data.forEach((val, i) => {
      globalState.incomeTable.set(i, val);
   });
   incomeTableRender();
   </script>";
}

if ($res['operation_name'] == 'sell') {
   $incomeTable = array();
   $result = $mysqli->query("SELECT * FROM operation_sell WHERE operation_id = $id");
   foreach ($result as $row) {
      if ($row) {
         $current = array(
            "name" => $row['product_name'],
            "count" => $row['count'],
            "createDate" => $row['create_date'],
            "expireDate" => $row['expire_date']
         );
         array_push($incomeTable, $current);
      }
   }
   $json = json_encode($incomeTable);
   $script = '<script>
   function incomeTableRender() {
      let num = 1;
      let tableBody = document.getElementById("income-table").tBodies[0];
      tableBody.innerHTML = "";
      for (let entry of globalState.incomeTable) {
         let tr = document.createElement("tr");
         tr.setAttribute("data-id", entry[0]);
         tr.setAttribute("data-action", "product-edit");
         tr.innerHTML = `<tr>
            <td>${num}</td>
            <td>${entry[1].name}</td>
            <td>${entry[1].count}</td>
            <td>${entry[1].createDate}</td>
            <td>${entry[1].expireDate}</td>
         </tr>`;
         tableBody.append(tr);
         num++;
      }
   }
   let globalState = {};
   globalState.incomeTable = new Map();
   ' . "
   let data = JSON.parse('$json');
   data.forEach((val, i) => {
      globalState.incomeTable.set(i, val);
   });
   incomeTableRender();
   </script>";
}

if ($res['operation_name'] == 'prod') {
   $materialTable = array();
   $result = $mysqli->query("SELECT * FROM operation_prod_consume
   LEFT JOIN product_list ON operation_prod_consume.product_name = product_list.title
   LEFT JOIN units ON product_list.unit_code = units.unit_id
   WHERE operation_id = $id");
   foreach ($result as $row) {
      if ($row) {
         $current = array(
            "name" => $row['product_name'],
            "count" => $row['count'],
            "unit" => $row['unit'],
            "createDate" => $row['create_date'],
            "expireDate" => $row['expire_date']
         );
         array_push($materialTable, $current);
      }
   }

   $makeTable = array();
   $result = $mysqli->query("SELECT * FROM operation_prod_add
   LEFT JOIN product_list ON operation_prod_add.product_name = product_list.title
   LEFT JOIN units ON product_list.unit_code = units.unit_id
   WHERE operation_id = $id");
   foreach ($result as $row) {
      if ($row) {
         $current = array(
            "name" => $row['product_name'],
            "count" => $row['count'],
            "unit" => $row['unit']
         );
         array_push($makeTable, $current);
      }
   }

   $materials_json = json_encode($materialTable);
   $make_json = json_encode($makeTable);

   $script = '<script>
   function doubleTableRender() {
      //таблица сырья
      let num = 1;
      let tableBody = document.getElementById("material-table").tBodies[0];
      tableBody.innerHTML = "";
      for (let entry of globalState.materialTable) {
         let tr = document.createElement("tr");
         tr.setAttribute("data-id", entry[0]);
         tr.setAttribute("data-action", "product-edit");
         tr.setAttribute("data-type", "material");
         tr.innerHTML = `<tr>
            <td>${num}</td>
            <td>${entry[1].name}</td>
            <td>${entry[1].count}</td>
            <td>${entry[1].unit}</td>
            <td>${entry[1].createDate}</td>
            <td>${entry[1].expireDate}</td>
            <td><a class="delete-row-button"><i class="material-icons">delete_forever</i></a></td>
         </tr>`;
         tableBody.append(tr);
         num++;
      }
   
      //таблица продукции
      num = 1;
      tableBody = document.getElementById("make-table").tBodies[0];
      tableBody.innerHTML = "";
      for (let entry of globalState.makeTable) {
         let tr = document.createElement("tr");
         tr.setAttribute("data-id", entry[0]);
         tr.setAttribute("data-action", "product-edit");
         tr.setAttribute("data-type", "product");
         tr.innerHTML = `<tr>
            <td>${num}</td>
            <td>${entry[1].name}</td>
            <td>${entry[1].count}</td>
            <td>${entry[1].unit}</td>
            <td><a class="delete-row-button"><i class="material-icons">delete_forever</i></a></td>
         </tr>`;
         tableBody.append(tr);
         num++;
      }
   }
   let globalState = {};
   globalState.materialTable = new Map();
   globalState.makeTable = new Map();
   ' . "
   let materialsData = JSON.parse('$materials_json');
   let makeData = JSON.parse('$make_json');

   materialsData.forEach((val, i) => {
      globalState.materialTable.set(i, val);
   });

   makeData.forEach((val, i) => {
      globalState.makeTable.set(i, val);
   });

   doubleTableRender();
   </script>";
}

if ($res['operation_name'] == 'inv') {
   $incomeTable = array();
   $result = $mysqli->query("SELECT * FROM operation_inventory
   LEFT JOIN product_list ON operation_inventory.product_id = product_list.product_id
   LEFT JOIN units ON product_list.unit_code = units.unit_id
   WHERE operation_id = $id");
   foreach ($result as $row) {
      if ($row) {
         $current = array(
            "name" => $row['title'],
            "createDate" => $row['create_date'],
            "unit" => $row['unit'],
            "countBefore" => $row['count_before'],
            "countAfter" => $row['count_after'],
            "diff" => $row['count_before'] - $row['count_after']
         );
         array_push($incomeTable, $current);
      }
   }

   $inventory_json = json_encode($incomeTable);

   $script = '<script>
   function showCompareForm(compareList) {
      let table = document.getElementById("compare-table");
      table.tBodies[0].innerHTML = "";
   
      compareList.forEach((val, i) => {
         let tr = document.createElement("tr");
         tr.innerHTML = `
         <td>${i+1}</td>
         <td>${val.name}</td>
         <td>${val.createDate}</td>
         <td>${val.unit}</td>
         <td>${val.countBefore}</td>
         <td>${val.countAfter}</td>
         <td style="color: ${val.diff < 0 ?"red":"green"}; font-weight: bold;">${val.diff}</td>
         `;
         table.tBodies[0].append(tr);
      });
   
   }'."
   let inventoryData = JSON.parse('$inventory_json');
   showCompareForm(inventoryData);
   </script>";
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="ie=edge">
   <title>Reactive record</title>
   <link href="./assets/materialize/css/materialize.min.css" rel="stylesheet">
   <link href="./style.css" rel="stylesheet">
   <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
   <link href="./assets/font-awesome/css/all.css" rel="stylesheet">
   <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700&display=swap&subset=cyrillic" rel="stylesheet">
   <style>
      .input-field a {
         display: none;
      }
      #add-form {
         display: none;
      }
      #compare-form {
         display: block !important;
      }
      #compare-form a {
         display: none;
      }
      .info-message {
         display: none;
      }
   </style>
</head>

<body>
   <noscript>
      <div style="position: absolute; height: 100vh; width: 100vw; z-index: 500; background: #fff;">Ваш браузер не поддерживает JavaScript</div>
   </noscript>

   <div id="dashboard">

      <?php include './include/inc_sidebar.php'; ?>

      <div id="main-wrapper">
         <div class="content-wrapper">
            <div class="card-panel white">



               <div id="prihod" class="content-block">
                  <h2 style="margin: 0">Просмотр операции</h2>
                  <hr>

                  <?php 
                  //обратная ссылка со всеми параметрами, но без ID
                  $query_array = explode('&', $_SERVER['QUERY_STRING']);
                  foreach($query_array as $key => $value) {
                     if(substr($value, 0, 3) == 'id=') {
                        unset($query_array[$key]);
                     }
                  }
                  $return_link = join("&",$query_array);
                  if (strlen($return_link) > 0) $return_link = './operation_history.php?' . $return_link;
                  else $return_link = './operation_history.php';

                  echo "<p><a href='$return_link'>Вернуться к списку</a></p>"
                  ?>
                  

                  <?php
                  $date = date("d.m.yy", strtotime("$res[operation_date]")); //"формат", "исходная дата"
                  echo "
                     <h4>$res[operation_name] № $res[document_number] от $date</h4>

                     <div class='card-panel blue lighten-5'>
                     <p>Дата операции: $date</p>
                     <p>Номер документа: $res[document_number]</p>
                     <p>Контрагент: $res[partner]</p>
                     <p>Создана пользователем: $res[user]</p>
                     <p>Дата записи в базу: $res[timestamp] (Год-Месяц-День)</p>
                     </div>

                     <h5>Табличная часть</h5>
                  ";
                  include $table;

                  ?>

               </div>

            </div>


         </div>
      </div>
   </div>

   <script src="./assets/materialize/js/materialize.min.js"></script>
   <script src="./js/script.js"></script>
   <script src="./js/logout.js"></script>
   <?php echo $script ?>

</body>

</html>