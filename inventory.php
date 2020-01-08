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

                  <div id="add-form">
                     <div id="test1" class="col s12" style="padding: 20px">
                        <div class="row" style="margin-bottom: 0;">
                           <div class="input-field col s12">
                              <a class="waves-effect waves-light btn blue-grey lighten-4 z-depth-0" style="width: 100%; margin-top: 5px;" id="add-new-product">добавить позицию</a>
                              <table id="income-table" class="product-table">
                                 <thead>
                                    <tr>
                                       <th>№</th>
                                       <th>Номенклатура</th>
                                       <th>Количество</th>
                                       <th>Дата изготовления</th>
                                       <th>Срок годности</th>
                                       <th></th>
                                    </tr>
                                 </thead>

                                 <tbody>
                                    <tr>

                                    </tr>
                                 </tbody>
                              </table>
                              <p id="empty-message" style="display: none;">Список продукции пуст, нажмите «добавить позицию».</p>

                           </div>
                        </div>

                     </div>
                     <div class="divider" style="margin: 10px 0px;"></div>
                     <a id="create-record" class="waves-effect waves-light btn-large" style="width: 100%">Начать инвентаризацию</a>
                  </div>

                  <div id="compare-form" style="display: none;">
                     <div id="test1" class="col s12" style="padding: 20px">
                        <div class="row" style="margin-bottom: 0;">
                           <div class="info-message">Проверьте данные и подтвердите операцию</div>
                           <div class="col s12">
                              <table id="compare-table" class="product-table">
                                 <thead>
                                    <tr>
                                       <th>№</th>
                                       <th>Номенклатура</th>
                                       <th>Дата изготовления</th>
                                       <th>Ед.изм</th>
                                       <th>Количество в журнале</th>
                                       <th>Количество по факту</th>
                                       <th>Разница</th>
                                       <th></th>
                                    </tr>
                                 </thead>

                                 <tbody>
                                    <tr>

                                    </tr>
                                 </tbody>
                              </table>
                           </div>
                        </div>

                     </div>
                     <div class="divider" style="margin: 10px 0px;"></div>
                     <div class="row" style="margin-bottom: 0;">
                        <div class="col s6">
                           <a id="inventory-confirm" class="waves-effect waves-light btn-large" style="width: 100%">Скорректировать остатки</a>
                        </div>
                        <div class="col s6">
                           <a id="return-to-add" class="waves-effect waves-light btn-large blue-grey lighten-2" style="width: 100%">Вернуться назад</a>
                        </div>
                     </div>
                  </div>

               </div>
            </div>


         </div>


      </div>
   </div>
   </div>

   <!-- Modal Structure (модалка для добавления, триггерится по нажатию кнопки "добавить позицию") -->
   <div id="add-modal" class="modal modal-fixed-footer modal-form">
      <div class="modal-content">
         <h4 id="modal-title" style="font-weight: bold;"></h4>
         <p id="modal-desc"></p>
         <h5>Основные поля</h5>

         <div id="main-fields">
            <div class="row">
               <div class="col s12 flex-col">
                  <div class="input-field">
                     <input autocomplete="off" data-autocomplete-object="goods" placeholder="Нажмите для выбора или поиска номенклатуры" id="goods-select" type="text" class="">
                     <label for="goods-select">Номенклатура</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Начните вводить название для поиска">help_outline</i>
               </div>
            </div>


            <div class="row">
               <div class="col s12 flex-col">
                  <div class="input-field">
                     <input autocomplete="off" placeholder="Введите количество товара" id="goods-count" type="number" min="1" class="">
                     <label for="goods-count">Количество</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Количество товара">help_outline</i>
               </div>
            </div>

            <div class="row">
               <div class="col s6 flex-col">
                  <div class="input-field">
                     <input id="goods-create-date" type="text" class="datepicker">
                     <label for="goods-create-date" class="active">Дата изготовления</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Введите дату изготовления">help_outline</i>
               </div>

               <div class="col s6 flex-col">
                  <div class="input-field">
                     <input disabled id="valid-until" placeholder="Сначала выберите номенклатуру" type="text">
                     <label for="valid-until" class="active">Срок годности</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Срок годности вычисляется автоматически">help_outline</i>
               </div>
            </div>
         </div>

      </div>
      <div class="modal-footer">
         <!-- js controls -->
      </div>
   </div>

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