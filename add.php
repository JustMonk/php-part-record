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
         let globalState = <?php include './action/get_add_state.php' ?>;
         console.log(globalState.partners);
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
                           <input placeholder="Введите номер документа" id="first_name" type="text" class="validate">
                           <label for="first_name">Номер документа</label>
                        </div>

                        <div class="col s6">
                           <div class="input-field">
                              <div class="modal datepicker-modal" id="modal-761e151b-6430-2323-9c0f-38df333fe599" tabindex="0">
                                 <div class="modal-content datepicker-container">
                                    <div class="datepicker-date-display"><span class="year-text"></span><span class="date-text"></span></div>
                                    <div class="datepicker-calendar-container">
                                       <div class="datepicker-calendar"></div>
                                       <div class="datepicker-footer"><button class="btn-flat datepicker-clear waves-effect" style="visibility: hidden;" type="button"></button>
                                          <div class="confirmation-btns"><button class="btn-flat datepicker-cancel waves-effect" type="button">Cancel</button><button class="btn-flat datepicker-done waves-effect" type="button">Ok</button>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div><input id="birthdate" type="text" class="datepicker">
                              <label for="birthdate" class="">Дата</label>
                           </div>
                        </div>
                        <div class="col s6">
                           <div class="input-field col s12">
                              <input id="partner-select" type="text" class="validate">
                              <label for="partner-select">Контрагент</label>
                           </div>
                        </div>

                        <div class="col s12" style="margin: 40px 0px;">
                           <table>
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
                           <a class="waves-effect waves-light btn blue-grey lighten-4 z-depth-0" style="width: 100%; margin-top: 5px;">добавить позицию</a>
                        </div>

                        <a class="waves-effect waves-light btn-large" style="width: 100%">Создать приход</a>
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

   <script>
      //autocomplete init

      document.addEventListener('DOMContentLoaded', function() {
         var partnerSelect = document.querySelector('#partner-select');
         var instances = M.Autocomplete.init(partnerSelect, {
            data: globalState.partners,
            minLength: 0,
         });
      });
   </script>

</body>

</html>