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
                  <h2 style="margin: 0">Реестр продукции</h2>
                  <hr>
                  <p>Представление таблицы «<b>product_registry</b>». Аналог складского журнала </p>
                  <table>
                     <thead>
                        <tr>
                           <th>ID в реестре</th>
                           <th>Наименование</th>
                           <th>Количество</th>
                           <th>Ед.изм.</th>
                           <th>Дата производства</th>
                           <th>Годен до</th>
                           <th>Жирность (доп)</th>
                           <th>Плотность (доп)</th>
                           <th>Кислотность (доп)</th>
                        </tr>
                     </thead>

                     <tbody>

                        <?php
                        foreach ($mysqli->query('SELECT product_registry.registry_id, product_list.title , product_registry.count, units.unit, product_registry.create_date, product_registry.expire_date, product_registry.milk_fat, product_registry.milk_solidity, product_registry.milk_acidity
                        FROM product_registry, product_list, units 
                        WHERE product_registry.product_id = product_list.product_id AND product_list.unit_code = units.unit_id') as $row) {
                           echo "<tr>
                           <td>$row[registry_id]</td>
                           <td>$row[title]</td>
                           <td>$row[count]</td>
                           <td>$row[unit]</td>
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