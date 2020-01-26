<?php
include './include/inc_config.php';
include './include/session_config.php';
include './include/auth_redirect.php';
include './include/check_admin_access.php';
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
$operation_exist = $res->num_rows;
$res = $res->fetch_assoc();


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
            "product_id" => $row['product_id'],
            "name" => addslashes($row['product_name']),
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
}

if ($res['operation_name'] == 'sell') {
   $incomeTable = array();
   $result = $mysqli->query("SELECT * FROM operation_sell WHERE operation_id = $id");
   foreach ($result as $row) {
      if ($row) {
         $current = array(
            "product_id" => $row['product_id'],
            "name" => addslashes($row['product_name']),
            "count" => $row['count'],
            "createDate" => $row['create_date'],
            "expireDate" => $row['expire_date']
         );
         array_push($incomeTable, $current);
      }
   }
   $json = json_encode($incomeTable);
}

if ($res['operation_name'] == 'prod') {
   $materialTable = array();
   $result = $mysqli->query("SELECT * FROM operation_prod_consume
   LEFT JOIN product_list ON operation_prod_consume.product_name = product_list.title
   LEFT JOIN units ON product_list.unit_code = units.unit_id
   LEFT JOIN product_types ON product_list.product_type = product_types.type_id
   WHERE operation_id = $id");
   foreach ($result as $row) {
      if ($row) {
         $current = array(
            "product_id" => $row['product_id'],
            "name" => addslashes($row['product_name']),
            "count" => $row['count'],
            "unit" => $row['unit'],
            "type" =>  $row['type'],
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
   LEFT JOIN product_types ON product_list.product_type = product_types.type_id
   WHERE operation_id = $id");
   foreach ($result as $row) {
      if ($row) {
         $current = array(
            "product_id" => $row['product_id'],
            "name" => addslashes($row['product_name']),
            "count" => $row['count'],
            "unit" => $row['unit'],
            "type" =>  $row['type']
         );
         array_push($makeTable, $current);
      }
   }

   $materials_json = json_encode($materialTable);
   $make_json = json_encode($makeTable);
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
            "title" => addslashes($row['title']),
            "unit" => $row['unit'],
            "count" => $row['count_before'],
            "real_count" => $row['count_after'],
            "create_date" => $row['create_date'],
            "product_id" => $row['product_id']
         );
         array_push($incomeTable, $current);
      }
   }

   $inventory_json = json_encode($incomeTable);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="ie=edge">
   <title>Part record</title>
   <link href="./assets/materialize/css/materialize.min.css" rel="stylesheet">
   <link href="./style.css" rel="stylesheet">
   <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
   <link href="./assets/font-awesome/css/all.css" rel="stylesheet">
   <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700&display=swap&subset=cyrillic" rel="stylesheet">
   <style>
      /*.input-field a {
         display: none;
      }*/

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
                  foreach ($query_array as $key => $value) {
                     if (substr($value, 0, 3) == 'id=') {
                        unset($query_array[$key]);
                     }
                  }
                  $return_link = join("&", $query_array);
                  if (strlen($return_link) > 0) $return_link = './operation_history.php?' . $return_link;
                  else $return_link = './operation_history.php';

                  echo "<p><a href='$return_link'>Вернуться к списку</a></p>";
                  //если операции не существует - показываем заглушку
                  if (!$operation_exist) {
                     echo "<h5>Операции не существует</h5>";
                     exit;
                  } 
                  ?>


                  <?php
                  $date = date("d.m.yy", strtotime("$res[operation_date]")); //"формат", "исходная дата"
                  echo "
                     <h5>$res[operation_name] № $res[document_number] от $date</h5>

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

                  <?php
                  //инвентаризации не редактируются
                  if ($res['operation_name'] != 'inv') {
                     echo "<div class=\"divider\" style=\"margin: 10px 0px;\"></div>
                     <a id='operation-edit' class=\"waves-effect waves-light btn blue lighten-1\">Сохранить изменения</a>
                     <a id='operation-delete' class=\"waves-effect waves-light btn grey\">Удалить операцию</a>
                     ";
                  }
                  ?>

               </div>

            </div>


         </div>
      </div>
   </div>

   <?php
   //echo $script;
   if ($res['operation_name'] == 'add') {
      include './include/modals/add_modal.php';
   }
   if ($res['operation_name'] == 'sell') {
      include './include/modals/sell_modal.php';
   }
   if ($res['operation_name'] == 'prod') {
      include './include/modals/make_modal.php';
   }
   if ($res['operation_name'] == 'inv') {
      include './include/modals/inv_modal.php';
   }
   ?>

   <script src="./assets/materialize/js/materialize.min.js"></script>
   <script src="./js/script.js"></script>
   <script src="./js/logout.js"></script>
   <?php

   if ($res['operation_name'] == 'add') {
      echo "<script src='./js/add_goods.js'></script>";
      echo "
      <script>
      //modal init===================
      document.addEventListener('DOMContentLoaded', function() {
         var elems = document.querySelector('#add-modal');
         var instances = M.Modal.init(elems, {
            //onOpenStart: clearAddModal
         });
      });

      //datepicker init===========
      document.addEventListener('DOMContentLoaded', function() {
         var elems = document.querySelector('#goods-create-date');
         var instances = M.Datepicker.init(elems, {});
      });

      //все остальные datepicker'ы
      document.addEventListener('DOMContentLoaded', function() {
         var elems = document.querySelector('#operation-date');
         var instances = M.Datepicker.init(elems, {});
      });

      //вспывающие подсказки
      M.Tooltip.init(document.querySelectorAll('.help-icon'), {
         position: 'top'
      });

      globalState.incomeTable = new Map();
      let data = JSON.parse('$json');
      data.forEach((val, i) => {
         globalState.incomeTable.set(i, val);
      });
      //incomeTableRender();

      document.addEventListener('fetchComplete', e => {
         incomeTableRender();
      });

      function changeRequest(rewriteBool) {
         let request = {
            operation_id: '$res[operation_id]',
            operation_type: '$res[operation_name]',
            docNum: '$res[document_number]',
            operationDate: '$res[operation_date]',
            partner: '$res[partner]',
            productList: [...globalState.incomeTable.values()].map(val => {
               let changedObj = val;
               changedObj.createDate = new Date(changedObj.createDate + ' UTC').toISOString().split('T')[0];
               return changedObj;
            }),
            rewrite: rewriteBool
         };
         console.log(request);
         console.log(JSON.stringify(request));

         fetch('action/admin/delete_operation.php', { method: 'POST', cache: 'no-cache', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(request) }).then(result => {
            console.log(result);
            return result.json();
         }).then(json => {
            console.log(json);
            showMessage(json);
         });
      }

      document.addEventListener('click', e => {
         if (e.target.id == 'operation-edit') {
            changeRequest(true);
         }
         
         if (e.target.id == 'operation-delete') {
            changeRequest(false);
            window.location.replace(window.location.origin + '/operation_history.php');
         }
      });

   </script>
      ";
   }
   if ($res['operation_name'] == 'sell') {
      echo "<script src='./js/sell_goods.js'></script>";
      echo "
      <script>
      //modal init===================
      document.addEventListener('DOMContentLoaded', function() {
         var elems = document.querySelector('#add-modal');
         var instances = M.Modal.init(elems, {
            //onOpenStart: clearAddModal
         });
      });

      //datepicker init===========
      document.addEventListener('DOMContentLoaded', function() {
         var elems = document.querySelector('#goods-create-date');
         var instances = M.Datepicker.init(elems, {});
      });

      //все остальные datepicker'ы
      document.addEventListener('DOMContentLoaded', function() {
         var elems = document.querySelector('#operation-date');
         var instances = M.Datepicker.init(elems, {});
      });

      //вспывающие подсказки
      M.Tooltip.init(document.querySelectorAll('.help-icon'), {
         position: 'top'
      });

      globalState.incomeTable = new Map();
      let data = JSON.parse('$json');
      data.forEach((val, i) => {
      globalState.incomeTable.set(val.name + ' [' + val.createDate + ']', val);
      //добавляем метку партии
      globalState.incomeTable.get(val.name + ' [' + val.createDate + ']').name = val.name + ' [' + val.createDate + ']';
      console.log(val);
      });
      
      document.addEventListener('fetchComplete', e => {
         for(let val of globalState.incomeTable.values()) {
            if (!globalState.goods[val.name]) {
               globalState.goods[val.name] = val;
               globalState.goods[val.name].expire_date = globalState.goods[val.name].expireDate;
               globalState.goods[val.name].create_date = globalState.goods[val.name].createDate;
               console.log(globalState.goods[val.name]);
            } else {
               globalState.goods[val.name].count = +globalState.goods[val.name].count + +val.count;
            } 
         }
         incomeTableRender();
      });

      function changeRequest(rewriteBool) {
         let request = {
            operation_id: '$res[operation_id]',
            operation_type: '$res[operation_name]',
            docNum: '$res[document_number]',
            operationDate: '$res[operation_date]',
            partner: '$res[partner]',
            productList: [...globalState.incomeTable.values()].map(val => {
               let changedObj = val;
               changedObj.createDate = new Date(changedObj.createDate + ' UTC').toISOString().split('T')[0];
               return changedObj;
            }),
            rewrite: rewriteBool
         };
         console.log(request);
         console.log(JSON.stringify(request));

         fetch('action/admin/delete_operation.php', { method: 'POST', cache: 'no-cache', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(request) }).then(result => {
            console.log(result);
            return result.json();
         }).then(json => {
            console.log(json);
            showMessage(json);
         });
      }

      document.addEventListener('click', e => {
         if (e.target.id == 'operation-edit') {
            changeRequest(true);
         }
         
         if (e.target.id == 'operation-delete') {
            changeRequest(false);
            window.location.replace(window.location.origin + '/operation_history.php');
         }
      });

      </script>
      ";
   }
   if ($res['operation_name'] == 'prod') {
      echo "<script src='./js/make_goods.js'></script>";
      echo "
      <script>
      //modal init===================
      document.addEventListener('DOMContentLoaded', function() {
         var elems = document.querySelector('#add-modal');
         var instances = M.Modal.init(elems, {
            //onOpenStart: clearAddModal
         });
      });

      //datepicker init===========
      document.addEventListener('DOMContentLoaded', function() {
         var elems = document.querySelector('#goods-create-date');
         var instances = M.Datepicker.init(elems, {});
      });

      //все остальные datepicker'ы
      document.addEventListener('DOMContentLoaded', function() {
         var elems = document.querySelector('#operation-date');
         var instances = M.Datepicker.init(elems, {});
      });

      //вспывающие подсказки
      M.Tooltip.init(document.querySelectorAll('.help-icon'), {
         position: 'top'
      });

      //в производстве 2 мапа (materialTable и makeTable)
      globalState.materialTable = new Map();
      let materialData = JSON.parse('$materials_json');
      materialData.forEach((val, i) => {
      globalState.materialTable.set(val.name + ' [' + val.createDate + ']', val);
      console.log(val);
      });

      //второй мап
      globalState.makeTable = new Map();
      let makeData = JSON.parse('$make_json');
      makeData.forEach((val, i) => {
      globalState.makeTable.set(i, val);
      console.log(val);
      });

      //для добавления количества (сумма остатка и резерва)
      document.addEventListener('fetchComplete', e => {
         //определение типа производимой продукции
         if (globalState.makeTable.get(0).type == 'Готовая продукция') {
            document.getElementById('production_type').value = 'finished';
         } else {
            document.getElementById('production_type').value = 'halfway';
         }
         M.FormSelect.init(document.querySelectorAll('select'), {});

         for(let entry of globalState.materialTable) {
            console.log(entry[0]);
            if (entry[1].type == 'Готовая продукция') {
               if (!globalState.halfway.hasOwnProperty(entry[0])) globalState.halfway[entry[0]] = entry[1];
               else globalState.halfway[entry[0]].count = +globalState.halfway[entry[0]].count + +entry[1].count;
            }
            if (entry[1].type == 'Полуфабрикат') {
               if (!globalState.halfway.hasOwnProperty(entry[0])) globalState.halfway[entry[0]] = entry[1];
               else globalState.halfway[entry[0]].count = +globalState.halfway[entry[0]].count + +entry[1].count;

               if (!globalState.materials.hasOwnProperty(entry[0])) globalState.materials[entry[0]] = entry[1];
               else globalState.materials[entry[0]].count = +globalState.materials[entry[0]].count + +entry[1].count;
            }
            if (entry[1].type == 'Сырье') {
               if (!globalState.materials.hasOwnProperty(entry[0])) globalState.materials[entry[0]] = entry[1];
               else globalState.materials[entry[0]].count = +globalState.materials[entry[0]].count + +entry[1].count;
            }
         }
         doubleTableRender();
      });

      function changeRequest(rewriteBool) {
         let request = {
            operation_id: '$res[operation_id]',
            operation_type: '$res[operation_name]',
            docNum: '$res[document_number]',
            operationDate: '$res[operation_date]',
            materialList: [...globalState.materialTable.values()],
            productList: [...globalState.makeTable.values()],
            rewrite: rewriteBool
         };

         console.log(request);
         console.log(JSON.stringify(request));

         fetch('action/admin/delete_operation.php', { method: 'POST', cache: 'no-cache', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(request) }).then(result => {
            console.log(result);
            return result.json();
         }).then(json => {
            console.log(json);
            showMessage(json);
         });
      }

      document.addEventListener('click', e => {
         if (e.target.id == 'operation-edit') {
            changeRequest(true);
         }
         
         if (e.target.id == 'operation-delete') {
            changeRequest(false);
            window.location.replace(window.location.origin + '/operation_history.php');
         }
      });
      </script>
      ";
   }
   if ($res['operation_name'] == 'inv') {
      echo "<script src='./js/inventory_goods.js'></script>";
      echo "
      <script>
      //modal init===================
      document.addEventListener('DOMContentLoaded', function() {
         var elems = document.querySelector('#add-modal');
         var instances = M.Modal.init(elems, {
            //onOpenStart: clearAddModal
         });
      });

      //datepicker init===========
      document.addEventListener('DOMContentLoaded', function() {
         var elems = document.querySelector('#goods-create-date');
         var instances = M.Datepicker.init(elems, {});
      });

      //все остальные datepicker'ы
      document.addEventListener('DOMContentLoaded', function() {
         var elems = document.querySelector('#operation-date');
         var instances = M.Datepicker.init(elems, {});
      });

      //вспывающие подсказки
      M.Tooltip.init(document.querySelectorAll('.help-icon'), {
         position: 'top'
      });

      let data = JSON.parse('$inventory_json');
      globalState.compareTable = data;
      showCompareForm(globalState.compareTable);
   </script>
   ";
   }
   ?>

</body>

</html>