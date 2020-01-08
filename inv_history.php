<?php
include './include/inc_config.php';
include './include/session_config.php';
include './include/auth_redirect.php';
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
                  <h2 style="margin: 0">История инвентаризаций</h2>
                  <hr>
                  <p>Представление таблицы «<b>operation_inventory</b>». Содержит список всех изменений по инвентаризации. </p>
                  <table>
                     <thead>
                        <tr>
                           <th>ID операции</th>
                           <th>Номенклатура</th>
                           <th>Дата производства</th>
                           <th>Количество до</th>
                           <th>Количество после</th>
                        </tr>
                     </thead>

                     <tbody>

                        <?php
                        foreach ($mysqli->query('SELECT operation_inventory.operation_id, product_list.title, operation_inventory.create_date, operation_inventory.count_before, operation_inventory.count_after 
                        FROM operation_inventory, product_list 
                        WHERE operation_inventory.product_id = product_list.product_id') as $row) {
                           echo "<tr>
                           <td>$row[operation_id]</td>
                           <td>$row[title]</td>
                           <td>$row[create_date]</td>
                           <td>$row[count_before]</td>
                           <td>$row[count_after]</td>
                           </tr>";
                        }
                        ?>

                     </tbody>
                  </table>
               </div>



            </div>


         </div>
      </div>
   </div>

   <script src="./assets/materialize/js/materialize.min.js"></script>
   <script src="./js/script.js"></script>
   <script src="./js/logout.js"></script>
</body>

</html>