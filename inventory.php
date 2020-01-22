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
   <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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
                  <h2 style="margin: 0" class="operation-title"><i class="fas fa-dolly-flatbed operation-icon"></i> Инвентаризация</h2>
                  <hr>
                  <h6>Добавьте в таблицу фактические отстаки, чтобы сравнить их с реестром продукции в базе данных.</h6>

                  <?php include './include/inc_inv_form.php'; ?>

               </div>
            </div>


         </div>


      </div>
   </div>

   <?php include './include/modals/inv_modal.php'; ?>

   <script src="./assets/materialize/js/materialize.min.js"></script>
   <script src="./js/script.js"></script>
   <script src="./js/logout.js"></script>
   <script src="./js/inventory_goods.js"></script>

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
   </script>

</body>

</html>