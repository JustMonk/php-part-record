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
   <title>Part record</title>
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
         <div class="container-wide" style="padding: 40px 100px">
            <div class="">


               <div class="content-block">
                  <h2 style="margin: 0">Главная</h2>
                  <h3 style="margin: 0; font-family: 'Source Sans Pro', sans-serif; color:#6a869a; margin-top: 5px;">Сегодня</h3>

                  <div class="card-wrapper" style="margin: 40px 0;">

                     <div class="card today-card">
                        <div class="operation-title" style="color: grey;"><i class="fas fa-plus fa-fw operation-icon" style="font-size: 15px;"></i>Приходов</div>
                        <div class="count-title">
                           <?php
                           $date = date('Y-m-d');
                           $result = $mysqli->query("SELECT COUNT(*) FROM operation_history WHERE operation_type = (SELECT operation_type_id FROM operation_types WHERE operation_name = 'add') AND operation_date = '$date'");
                           echo $result->fetch_row()[0];
                           ?>
                        </div>
                     </div>

                     <div class="card today-card">
                        <div class="operation-title" style="color: grey;"><i class="fas fa-dollar-sign fa-fw operation-icon" style="font-size: 15px;"></i>Продаж</div>
                        <div class="count-title">
                           <?php
                           $date = date('Y-m-d');
                           $result = $mysqli->query("SELECT COUNT(*) FROM operation_history WHERE operation_type = (SELECT operation_type_id FROM operation_types WHERE operation_name = 'sell') AND operation_date = '$date'");
                           echo $result->fetch_row()[0];
                           ?>
                        </div>
                     </div>

                     <div class="card today-card">
                        <div class="operation-title" style="color: grey;"><i class="fas fa-industry fa-fw operation-icon" style="font-size: 15px;"></i>Производств</div>
                        <div class="count-title">
                           <?php
                           $date = date('Y-m-d');
                           $result = $mysqli->query("SELECT COUNT(*) FROM operation_history WHERE operation_type = (SELECT operation_type_id FROM operation_types WHERE operation_name = 'prod') AND operation_date = '$date'");
                           echo $result->fetch_row()[0];
                           ?>
                        </div>
                     </div>

                     <div class="card today-card">
                        <div class="operation-title" style="color: grey;"><i class="fas fa-dolly-flatbed operation-icon" style="font-size: 15px;"></i>Инвентаризаций</div>
                        <div class="count-title">
                           <?php
                           $date = date('Y-m-d');
                           $result = $mysqli->query("SELECT COUNT(*) FROM operation_history WHERE operation_type = (SELECT operation_type_id FROM operation_types WHERE operation_name = 'inv') AND operation_date = '$date'");
                           echo $result->fetch_row()[0];
                           ?>
                        </div>
                     </div>

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