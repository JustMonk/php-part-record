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
                           <th>ID</th>
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

                        <tr>
                           <td>12</td>
                           <td>Йогурт КЛУБНИКА</td>
                           <td>25</td>
                           <td>шт</td>
                           <td>16.11.2019</td>
                           <td>31.11.2019</td>
                           <td></td>
                           <td></td>
                           <td></td>
                        </tr>

                        <tr>
                           <td>48</td>
                           <td>Молоко 3.2%</td>
                           <td>12</td>
                           <td>л</td>
                           <td>16.11.2019</td>
                           <td>25.11.2019</td>
                           <td>3.2</td>
                           <td>2</td>
                           <td>1.1</td>
                        </tr>

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