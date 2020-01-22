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
                  <h2 style="margin: 0" class="operation-title"><i class="fas fa-dollar-sign fa-fw operation-icon"></i> Продажа</h2>
                  <hr>
                  <div id="test1" class="col s12" style="padding: 20px">

                     <div class="row" style="margin-bottom: 0;">
                        <div class="col s6 flex-col">
                           <div class="input-field">
                              <input autocomplete="off" placeholder="Введите номер документа" id="doc-number" type="text" class="">
                              <label for="doc-number">Номер документа</label>
                           </div>
                           <i class="material-icons help-icon" data-tooltip="Введите номер операции или номер документа в 1С">help_outline</i>
                        </div>

                        <div class="col s6 flex-col">
                           <div class="input-field">
                              <input id="operation-date" type="text" class="datepicker">
                              <label for="operation-date" class="">Дата продажи</label>
                           </div>
                        </div>
                     </div>

                     <div class="row" style="margin-bottom: 0;">
                        <div class="col s12 flex-col">
                           <div class="input-field autocomplete-field">
                              <input autocomplete="off" data-autocomplete-object="partners" id="partner-select" type="text" class="">
                              <label for="partner-select">Контрагент</label>
                           </div>
                           <i class="material-icons help-icon" data-tooltip="Выберите контрагента из списка">help_outline</i>
                        </div>
                     </div>

                     <?php include './include/inc_sell_form.php'; ?>

                  </div>
                  <div class="divider" style="margin: 10px 0px;"></div>
                  <a id="create-record" class="waves-effect waves-light btn-large" style="width: 100%">Создать продажу</a>
               </div>
            </div>


         </div>


      </div>
   </div>

   <?php include './include/modals/sell_modal.php'; ?>

   <script src="./assets/materialize/js/materialize.min.js"></script>
   <script src="./js/script.js"></script>
   <script src="./js/logout.js"></script>
   <script src="./js/sell_goods.js"></script>

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

      //рендерим таблицу
      incomeTableRender();
   </script>

</body>

</html>