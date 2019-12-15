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

   <div id="dashboard">

      <?php include './include/inc_sidebar.php'; ?>

      <div id="main-wrapper">
         <div class="container" style="padding-top: 40px">
            <div class="card-panel white">



               <div id="prihod" class="content-block">
                  <h2 style="margin: 0">История продаж</h2>
                  <hr>
                  <p>Представление таблицы «<b>operation_sell</b>». Содержит список всех проданных позиций, привязанных к операции продажи. </p>
                  <table>
                     <thead>
                        <tr>
                           <th>Номер документа</th>
                           <th>Номенклатура</th>
                           <th>Количество</th>
                           <th>Дата производства</th>
                           <th>Годен до</th>
                           <th>Жирность (доп)</th>
                           <th>Плотность (доп)</th>
                           <th>Кислотность (доп)</th>
                        </tr>
                     </thead>

                     <tbody>

                        <?php
                        foreach ($mysqli->query('SELECT operation_history.document_number, operation_sell.product_name, operation_sell.count, operation_sell.create_date, operation_sell.expire_date, operation_sell.milk_fat, operation_sell.milk_solidity, operation_sell.milk_acidity 
                        FROM operation_sell, operation_history 
                        WHERE operation_sell.operation_id = operation_history.operation_id') as $row) {
                           echo "<tr>
                           <td>$row[document_number]</td>
                           <td>$row[product_name]</td>
                           <td>$row[count]</td>
                           <td>$row[create_date]</td>
                           <td>$row[expire_date]</td>
                           <td>$row[milk_fat]</td>
                           <td>$row[milk_solidity]</td>
                           <td>$row[milk_acidity]</td>
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