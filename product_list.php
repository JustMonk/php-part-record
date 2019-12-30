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
                  <h2 style="margin: 0">Список номенклатур</h2>
                  <hr>
                  <p>Представление таблицы «<b>product_list</b>». Содержит список всех используемых номенклатур с общими свойствами, характерными для каждой конкретной номенклатуры. </p>

                  <div class="admin-edit-bar">
                     <a class="waves-effect waves-light btn blue lighten-2" id="add-product-form">Добавить номенклатуру</a>
                  </div>

                  <table>
                     <thead>
                        <tr>
                           <th>ID</th>
                           <th>Наименование</th>
                           <th>Ед.изм</th>
                           <th>Объем</th>
                           <th>GTIN</th>
                           <th>Вид номенклатуры</th>
                           <th>Срок годности (суток)</th>
                           <th>Доп.поля (молоко)</th>
                        </tr>
                     </thead>

                     <tbody>

                        <?php
                        foreach ($mysqli->query('SELECT product_list.product_id, product_list.title, units.unit, product_list.capacity, product_list.gtin, product_types.type, product_list.valid_days, product_list.extended_milk_fields    
                        FROM product_list, units, product_types
                        WHERE product_list.unit_code = units.unit_id AND product_list.product_type = product_types.type_id') as $row) {
                           echo "<tr>
                           <td>$row[product_id]</td>
                           <td>$row[title]</td>
                           <td>$row[unit]</td>
                           <td>$row[capacity]</td>
                           <td>$row[gtin]</td>
                           <td>$row[type]</td>
                           <td>$row[valid_days]</td>
                           <td>$row[extended_milk_fields]</td>
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




   <div id="add-modal" class="modal modal-fixed-footer">
      <div class="modal-content">
         <h4 id="modal-title" style="font-weight: bold;">Добавление номенклатуры</h4>
         <p id="modal-desc">Заполните необходимую информацию</p>
         <h5>Основные поля</h5>

         <div id="main-fields">
            <div class="row">
               <div class="col s12 flex-col">
                  <div class="input-field">
                     <input autocomplete="off" placeholder="Введите наименование" id="inn-input" type="text" class="">
                     <label for="goods-select">Наименование</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Начните вводить название для поиска">help_outline</i>
               </div>
            </div>


            <div class="row">
               <div class="col s6 flex-col">
                  <div class="input-field">
                     <input autocomplete="off" placeholder="Введите штрихкод" id="name-input" type="text" class="">
                     <label for="goods-count">Штрихкод (GTIN)</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Количество товара">help_outline</i>
               </div>

               <div class="col s6 flex-col">
                  <div class="input-field">
                     <input autocomplete="off" placeholder="Введите объем товара" id="name-input" type="text" class="">
                     <label for="goods-count">Объем</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Введите объем товара">help_outline</i>
               </div>
            </div>

            <div class="row">
               <div class="col s6 flex-col">
                  <div class="input-field">
                     <select>
                        <option value="" disabled selected>Единицы измерения</option>
                        <option value="1">Option 1</option>
                        <option value="2">Option 2</option>
                        <option value="3">Option 3</option>
                     </select>
                     <label>Единицы измерения</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Количество товара">help_outline</i>
               </div>

               <div class="col s6 flex-col">
                  <div class="input-field">
                     <select>
                        <option value="" disabled selected>Тип продукции</option>
                        <option value="1">Option 1</option>
                        <option value="2">Option 2</option>
                        <option value="3">Option 3</option>
                     </select>
                     <label>Тип продукции</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Количество товара">help_outline</i>
               </div>
            </div>


            <div class="row">

               <div class="col s6 flex-col">
                  <div class="input-field">
                     <input autocomplete="off" placeholder="Введите наименование" id="inn-input" type="text" class="">
                     <label for="goods-select">Срок годности (суток)</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Начните вводить название для поиска">help_outline</i>
               </div>

               <div class="col s6 flex-col">
                  <div class="input-field">
                     <p>
                        <label>
                           <input type="checkbox" class="filled-in" />
                           <span>Имеет жирность/плотность/кислотность</span>
                        </label>
                     </p>
                  </div>
               </div>

            </div>



         </div>

      </div>

      <div class="modal-footer">
         <!-- js controls -->
         <a href="#!" id="add-product-button" class="waves-effect waves-green btn blue">Добавить</a>
         <a href="#!" class="modal-close waves-effect waves-green btn grey">Отмена</a>
      </div>
   </div>




   <script src="./assets/materialize/js/materialize.min.js"></script>
   <script src="./js/script.js"></script>
   <script src="./js/logout.js"></script>

   <script>
      //modal init===================
      document.addEventListener('DOMContentLoaded', function() {
         var elems = document.querySelector('#add-modal');
         var instances = M.Modal.init(elems, {
            //onOpenStart: clearAddModal
         });
      });

      document.addEventListener('click', (e) => {
         let modal = M.Modal.getInstance(document.getElementById('add-modal'));
         if (e.target.id == 'add-product-form') {
            clearForm();
            modal.open();
         }

         if (e.target.id == 'add-product-button') {
            if (!formValidation()) return;

            let data = {
               inn: document.getElementById('inn-input').value,
               kpp: document.getElementById('kpp-input').value,
               name: document.getElementById('name-input').value,
               comment: document.getElementById('comment-input').value
            }

            console.log(JSON.stringify(data));

            fetch('action/admin/product-add.php', {
               method: 'POST',
               cache: 'no-cache',
               headers: {
                  'Content-Type': 'application/json'
               },
               body: JSON.stringify(data)
            }).then(result => {
               console.log(result);
               return result.json();
            }).then(json => {
               console.log(json);
               showMessage(json);
               modal.close();
            });
         }

         function formValidation() {
            let isValid = true;
            let modal = M.Modal.getInstance(document.getElementById('add-modal'));

            if (modal.el.querySelector('#inn-input').value.length < 1) {
               isValid = false;
               modal.el.querySelector('#inn-input').className = 'validate invalid';
            }

            if (modal.el.querySelector('#name-input').value.length < 1) {
               isValid = false;
               modal.el.querySelector('#name-input').className = 'validate invalid';
            }

            return isValid;
         }

         function clearForm() {
            let modalNode = document.getElementById('add-modal');
            let inputs = modalNode.querySelectorAll('input');
            inputs.forEach(val => {
               val.value = '';
            });
         }

         function showMessage(response) {
            //удалить старое сообщение
            let oldMessage = document.querySelector('.message-card');
            if (oldMessage) oldMessage.remove();

            let color;
            switch (response.type) {
               case 'message':
                  color = 'blue';
                  break;

               case 'error':
                  color = 'red';
                  break;

               case 'success':
                  color = 'green';
                  break;

               default:
                  color = 'blue';
                  break;
            }

            if (e.target.classList.contains('close-message') || e.target.parentElement.classList.contains('close-message')) {
               e.target.closest('.message-card').remove();
            }


            let messageNode = document.createElement('div');
            messageNode.className = `card-panel ${color} lighten-4 message-card fadeIn`;
            messageNode.innerHTML = `
            ${response.message}
            <a class="close-message"><i class="material-icons">close</i></a>
            `;
            let parentNode = document.querySelector('#main-wrapper .container');
            parentNode.insertAdjacentElement('afterbegin', messageNode);
         }

      });
   </script>


</body>

</html>