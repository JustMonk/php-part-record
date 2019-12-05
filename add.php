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

      <script>
         //подтягиваем состояние БД с сервера
         let globalState = <?php include './action/get_add_state.php' ?>
         //создаем состояние операции, которое будем накапливать
         globalState.incomeTable = new Map();
         //.set(key, val)  .delete(key) .has(key)
         //.keys() .values() .entries()
      </script>

      <div id="main-wrapper">
         <div class="container" style="padding-top: 40px">
            <div class="card-panel white">

               <div id="prihod" class="content-block">
                  <h2 style="margin: 0">Приход</h2>
                  <hr>
                  <div id="test1" class="col s12" style="padding: 20px">
                     <div class="row" style="margin-bottom: 0;">

                        <div class="input-field col s12">
                           <input autocomplete="off" placeholder="Введите номер документа" id="first_name" type="text" class="">
                           <label for="first_name">Номер документа</label>
                        </div>

                        <div class="col s6">
                           <div class="input-field">
                              <input id="operation-date" type="text" class="datepicker">
                              <label for="operation-date" class="">Дата</label>
                           </div>
                        </div>

                        <div class="col s6">
                           <div class="input-field col s12 autocomplete-field">
                              <input autocomplete="off" data-autocomplete-object="partners" id="partner-select" type="text" class="">
                              <label for="partner-select">Контрагент</label>
                           </div>
                        </div>

                        <div class="col s12" style="margin: 40px 0px;">
                           <table id="income-table" class="highlight">
                              <thead>
                                 <tr>
                                    <th>№</th>
                                    <th>Номенклатура</th>
                                    <th>Количество</th>
                                    <th>Дата изготовления</th>
                                    <th>Срок годности</th>
                                    <th>rm</th>
                                 </tr>
                              </thead>

                              <tbody>
                                 <tr>

                                 </tr>
                              </tbody>
                           </table>
                           <a class="waves-effect waves-light btn blue-grey lighten-4 z-depth-0 modal-trigger" style="width: 100%; margin-top: 5px;" href="#add-modal">добавить позицию</a>
                        </div>

                        <a id="create-record" class="waves-effect waves-light btn-large" style="width: 100%">Создать приход</a>
                     </div>

                  </div>
               </div>


            </div>


         </div>
      </div>
   </div>

   <!-- Modal Structure (модалка для добавления, триггерится по нажатию кнопки "добавить позицию") -->
   <div id="add-modal" class="modal modal-fixed-footer">
      <div class="modal-content">
         <h4>Добавление товара</h4>
         <p>Выберите номенклатуру из списка ниже и заполните информацию о товаре</p>

         <div class="row">
            <div class="col s12">
            <h5>Основные поля</h5>
               <div class="input-field col s12">
                  <input autocomplete="off" data-autocomplete-object="goods" placeholder="Нажмите для выбора или поиска номенклатуры" id="goods-select" type="text" class="">
                  <label for="goods-select">Номенклатура</label>
               </div>

               <div class="input-field col s12">
                  <input autocomplete="off" placeholder="Введите количество товара" id="goods-count" type="text" class="">
                  <label for="goods-count">Количество</label>
               </div>

               <div class="input-field col s6">
                  <input id="goods-create-date" type="text" class="datepicker">
                  <label for="goods-create-date" class="active">Дата изготовления</label>
               </div>

               <div class="input-field col s6">
                  <input disabled id="valid-until" placeholder="Сначала введите дату изготовления" type="text">
                  <label for="valid-until" class="active">Годен до</label>
               </div>

               <div id="extended-fields" style="display: none;">
                  <h5>Дополнительные поля</h5>
                  <div class="input-field col s4">
                     <input id="ext-fat" placeholder="Введите процент жирности" type="text">
                     <label for="ext-fat" class="active">Жирность</label>
                  </div>

                  <div class="input-field col s4">
                     <input id="ext-solidity" placeholder="Введите плотность" type="text">
                     <label for="ext-solidity" class="active">Плотность</label>
                  </div>

                  <div class="input-field col s4">
                     <input id="ext-acidity" placeholder="Введите кислотность" type="text">
                     <label for="ext-acidity" class="active">Кислотность</label>
                  </div>
               </div>

            </div>
         </div>
      </div>
      <div class="modal-footer">
         <a href="#!" id="add-product-button" class="modal-close waves-effect waves-green btn blue">Добавить</a>
         <a href="#!" class="modal-close waves-effect waves-green btn grey">Отмена</a>
      </div>
   </div>

   <script src="./assets/materialize/js/materialize.min.js"></script>
   <script src="./js/script.js"></script>
   <script src="./js/logout.js"></script>
   <script src="./js/add_goods.js"></script>

   <script>
      //autocomplete init=============
      //partner autocomplete
      document.addEventListener('DOMContentLoaded', function() {
         var partnerSelect = document.querySelector('#partner-select');
         var instances = M.Autocomplete.init(partnerSelect, {
            data: globalState.partners,
            minLength: 0,
            onAutocomplete: function() {
               console.log('gg');
            }
         });
      });
      //goods autocomplete
      document.addEventListener('DOMContentLoaded', function() {
         var goodsSelect = document.querySelector('#goods-select');
         var instances = M.Autocomplete.init(goodsSelect, {
            data: globalState.goods,
            minLength: 0,
            onAutocomplete: function(elem) {
               if (globalState.goods[elem].extended_milk_fields) document.getElementById('extended-fields').style.display = 'block';
               else document.getElementById('extended-fields').style.display = 'none';
            }
         });
      });

      //modal init===================
      document.addEventListener('DOMContentLoaded', function() {
         var elems = document.querySelector('#add-modal');
         var instances = M.Modal.init(elems, {
            onOpenStart: clearAddModal
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
   </script>

</body>

</html>