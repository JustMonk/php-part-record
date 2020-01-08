<?php
include './include/inc_config.php';
include './include/session_config.php';
include './include/auth_redirect.php';
?>

<?php
// Переменная хранит число сообщений выводимых на станице
$num = 20;
// Извлекаем из URL текущую страницу
$page = $_GET['page'];
// Определяем общее число сообщений в базе данных
$result = $mysqli->query("SELECT COUNT(*) FROM product_list");
$products = $result->fetch_row()[0];

// Находим общее число страниц
$total = intval(($products - 1) / $num) + 1;
// Определяем начало сообщений для текущей страницы
$page = intval($page);
// Если значение $page меньше единицы или отрицательно
// переходим на первую страницу
// А если слишком большое, то переходим на последнюю
if (empty($page) or $page < 0) $page = 1;
if ($page > $total) $page = $total;
// Вычисляем начиная к какого номера
// следует выводить сообщения
$start = $page * $num - $num;
// Выбираем $num сообщений начиная с номера $start
$result = $mysqli->query("SELECT product_list.product_id, product_list.title, units.unit, product_list.capacity, product_list.gtin, product_types.type, product_list.valid_days, product_list.extended_milk_fields    
FROM product_list, units, product_types
WHERE product_list.unit_code = units.unit_id AND product_list.product_type = product_types.type_id
ORDER BY product_id DESC
LIMIT $start, $num");
// В цикле переносим результаты запроса в массив $product_rows
while ($product_rows[] = mysqli_fetch_array($result));
//echo var_dump($product_rows);
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
   <noscript>
      <div style="position: absolute; height: 100vh; width: 100vw; z-index: 500; background: #fff;">Ваш браузер не поддерживает JavaScript</div>
   </noscript>

   <div id="dashboard">

      <?php include './include/inc_sidebar.php'; ?>

      <div id="main-wrapper">
         <div class="content-wrapper">
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
                           <th></th>
                        </tr>
                     </thead>

                     <tbody>

                        <?php
                        foreach ($product_rows as $row) {
                           if ($row) {
                              echo "<tr>
                              <td>$row[product_id]</td>
                              <td>$row[title]</td>
                              <td>$row[unit]</td>
                              <td>$row[capacity]</td>
                              <td>$row[gtin]</td>
                              <td>$row[type]</td>
                              <td>$row[valid_days]</td>
                              <td>$row[extended_milk_fields]</td>
                              <td><a class=\"product-edit\" data-product-id=\"$row[product_id]\">ред.</a></td>
                              </tr>";
                           }
                        }
                        ?>

                     </tbody>
                  </table>

               </div>

               <ul class="pagination">
                  <?php
                  // Проверяем нужны ли стрелки назад
                  $pervpage = '<li class="disabled"><a href="#!"><i class="material-icons">chevron_left</i></a></li>'; //дефолтное значение (серая кнопка)
                  if ($page != 1) $pervpage = '<li class="waves-effect"><a href= ./product_list.php?page=' . ($page - 1) . '><i class="material-icons">chevron_left</i></a></li>';
                  // <a href= ./product_list.php?page=' . ($page - 1) . '><</a> ';
                  // Проверяем нужны ли стрелки вперед
                  $nextpage = '<li class="disabled"><a href="#!"><i class="material-icons">chevron_right</i></a></li>'; //дефолтное значение (серая кнопка)
                  if ($page != $total) $nextpage = '<li class="waves-effect"><a href= ./product_list.php?page=' . ($page + 1) . '><i class="material-icons">chevron_right</i></a></li>';
                  //<a href= ./product_list.php?page=' . $total . '>>></a>';

                  // Находим две ближайшие станицы с обоих краев, если они есть
                  if ($page - 2 > 0) $page2left = '<li class="waves-effect"> <a href= ./product_list.php?page=' . ($page - 2) . '>' . ($page - 2) . '</a> </li>';
                  if ($page - 1 > 0) $page1left = '<li class="waves-effect"> <a href= ./product_list.php?page=' . ($page - 1) . '>' . ($page - 1) . '</a> </li>';
                  if ($page + 2 <= $total) $page2right = '<li class="waves-effect"> <a href= ./product_list.php?page=' . ($page + 2) . '>' . ($page + 2) . '</a> </li>';
                  if ($page + 1 <= $total) $page1right = '<li class="waves-effect"> <a href= ./product_list.php?page=' . ($page + 1) . '>' . ($page + 1) . '</a> </li>';

                  // Вывод меню
                  echo $pervpage . $page2left . $page1left . '<li class="active"><a href="#!">' . $page . '</a></li>' . $page1right . $page2right . $nextpage;
                  ?>
               </ul>

            </div>

         </div>
      </div>
   </div>




   <div id="add-modal" class="modal modal-fixed-footer modal-form">
      <div class="modal-content">
         <h4 id="modal-title" style="font-weight: bold;">Добавление номенклатуры</h4>
         <p id="modal-desc">Заполните необходимую информацию</p>
         <h5>Основные поля</h5>

         <div id="main-fields">
            <div class="row">
               <div class="col s12 flex-col">
                  <div class="input-field">
                     <input autocomplete="off" placeholder="Введите наименование" id="title-input" type="text" class="">
                     <label for="title-input">Наименование</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Начните вводить название для поиска">help_outline</i>
               </div>
            </div>


            <div class="row">
               <div class="col s6 flex-col">
                  <div class="input-field">
                     <input autocomplete="off" placeholder="Введите штрихкод" id="gtin-input" type="number" class="">
                     <label for="gtin-input">Штрихкод (GTIN, опционально)</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Количество товара">help_outline</i>
               </div>

               <div class="col s6 flex-col">
                  <div class="input-field">
                     <input autocomplete="off" placeholder="Введите объем товара" id="capacity-input" type="number" class="">
                     <label for="capacity-input">Объем</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Введите объем товара">help_outline</i>
               </div>
            </div>

            <div class="row">
               <div class="col s6 flex-col">
                  <div class="input-field">
                     <select id="product-unit">
                        <!--no init-->
                     </select>
                     <label>Единицы измерения</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Количество товара">help_outline</i>
               </div>

               <div class="col s6 flex-col">
                  <div class="input-field">
                     <select id="product-type">
                        <!--no init-->
                     </select>
                     <label>Тип продукции</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Количество товара">help_outline</i>
               </div>
            </div>


            <div class="row">

               <div class="col s6 flex-col">
                  <div class="input-field">
                     <input autocomplete="off" placeholder="Введите срок годности" id="valid-input" type="number" class="">
                     <label for="valid-input">Срок годности (суток)</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Начните вводить название для поиска">help_outline</i>
               </div>

               <div class="col s6 flex-col">
                  <div class="input-field">
                     <p>
                        <label>
                           <input id="extended-fields" type="checkbox" class="filled-in" />
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
      </div>
   </div>




   <script src="./assets/materialize/js/materialize.min.js"></script>
   <script src="./js/script.js"></script>
   <script src="./js/logout.js"></script>

   <script>
      function loadUnits() {
         let fetchUnits = fetch('action/get_units.php', {
            method: 'GET'
         });

         return fetchUnits
            .then(response => response.json())
            .then(units => {
               console.log(units);
               if (units) {
                  let unitSelect = document.getElementById('product-unit');

                  //placeholder
                  unitSelect.innerHTML = `<option value="" disabled selected>Выберите единицу измерения</option>`;

                  units.forEach(val => {
                     let option = document.createElement('option');
                     option.value = val.unit;
                     option.text = val.unit;
                     unitSelect.add(option);
                  });
               }
               return units; // возвращаем данные выше
            })
            .catch(err => {
               console.error(err);
            });
      }

      function loadTypes() {
         let fetchTypes = fetch('action/get_product_types.php', {
            method: 'GET'
         });

         return fetchTypes
            .then(response => response.json())
            .then(types => {
               if (types) {
                  let typeSelect = document.getElementById('product-type');

                  //placeholder
                  typeSelect.innerHTML = `<option value="" disabled selected>Выберите тип продукции</option>`;

                  types.forEach(val => {
                     let option = document.createElement('option');
                     option.text = val.type;
                     typeSelect.add(option);
                  });
               }
               return types;
            })
            .catch(err => {
               console.error(err);
            });
      }

      //default modal init===================
      document.addEventListener('DOMContentLoaded', function() {
         var elems = document.querySelector('#add-modal');
         var instances = M.Modal.init(elems, {});
      });

      document.addEventListener('click', (e) => {
         let modal = M.Modal.getInstance(document.getElementById('add-modal'));
         if (e.target.id == 'add-product-form') {
            clearForm();

            Promise.all([
               loadUnits(),
               loadTypes()
            ]).then(result => {
               console.log(result);
               //select init
               let elems = document.querySelectorAll('select');
               let instances = M.FormSelect.init(elems, {});
            }).then(() => {
               //контролы добавления
               document.getElementById('modal-title').innerText = 'Добавление номенклатуры';
               document.querySelector('.modal-footer').innerHTML = `
               <a href="#!" id="add-product-button" class="waves-effect waves-green btn blue">Добавить</a>
               <a href="#!" class="modal-close waves-effect waves-green btn grey">Отмена</a>
               `;
               modal.open();
            });

         }

         //обработчик для редактирования позиции
         if (e.target.tagName == 'A' && e.target.hasAttribute('data-product-id')) {
            console.log('edit form init');
            clearForm();

            //заголовок и контролы
            document.getElementById('modal-title').innerText = 'Редактирование номенклатуры';
            document.querySelector('.modal-footer').innerHTML = `
            <a href="#!" id="edit-product-button" data-id="${e.target.getAttribute('data-product-id')}" class="waves-effect waves-green btn blue">Сохранить</a>
            <a href="#!" class="modal-close waves-effect waves-green btn grey">Отмена</a>
            `;

            fetch(`action/admin/get-product-props.php?id=${e.target.getAttribute('data-product-id')}`, {
               method: 'GET'
            }).then(result => {
               console.log(result);
               return result.json();
            }).then(json => {
               return Promise.all([
                  loadUnits(),
                  loadTypes()
               ]).then(result => {
                  console.log(result);
                  //select init
                  let elems = document.querySelectorAll('select');
                  let instances = M.FormSelect.init(elems, {});
                  return json;
                  console.log('all end in then');
               });
            }).then(json => {
               console.log('in json');
               console.log(json);

               if (json.type == 'error') {
                  showMessage(json);
                  throw new Error("wrong id");
               }

               //заполнение формы
               modal.el.querySelector('#title-input').value = json.title;
               modal.el.querySelector('#gtin-input').value = json.gtin ? json.gtin : '';
               modal.el.querySelector('#capacity-input').value = json.capacity;
               modal.el.querySelector('#product-unit').value = json.unit;
               modal.el.querySelector('#product-type').value = json.type;
               modal.el.querySelector('#valid-input').value = json.valid_days;
               modal.el.querySelector('#extended-fields').checked = +json.extended_milk_fields ? true : false;

               //select re-init
               M.FormSelect.init(document.querySelectorAll('select'), {});
            }).then(() => {
               modal.open();
            });
         }

         if (e.target.id == 'add-product-button') {
            if (!formValidation()) return;

            let data = {
               title: document.getElementById('title-input').value,
               gtin: document.getElementById('gtin-input').value,
               capacity: document.getElementById('capacity-input').value,
               unit: document.getElementById('product-unit').value,
               type: document.getElementById('product-type').value,
               validDays: document.getElementById('valid-input').value,
               extendedMilkFields: document.getElementById('extended-fields').checked ? 1 : 0
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
               /*showMessage(json);
               modal.close();*/
               window.location.replace(`${window.location.origin + window.location.pathname}?success=${true}&target=${json.title}`);
            });
         }

         if (e.target.id == 'edit-product-button') {
            if (!formValidation()) return;

            let data = {
               id: document.getElementById('edit-product-button').getAttribute('data-id'),
               title: document.getElementById('title-input').value,
               gtin: document.getElementById('gtin-input').value,
               capacity: document.getElementById('capacity-input').value,
               unit: document.getElementById('product-unit').value,
               type: document.getElementById('product-type').value,
               validDays: document.getElementById('valid-input').value,
               extendedMilkFields: document.getElementById('extended-fields').checked ? 1 : 0
            }

            console.log(JSON.stringify(data));

            fetch('action/admin/product-update.php', {
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
               /*showMessage(json);
               modal.close();*/
               window.location.replace(`${window.location.origin + window.location.pathname}?success=${true}&target=${json.title}`);
            });
         }

         if (e.target.classList.contains('close-message') || e.target.parentElement.classList.contains('close-message')) {
            e.target.closest('.message-card').remove();
         }
      });

      function formValidation() {
         let isValid = true;
         let modal = M.Modal.getInstance(document.getElementById('add-modal'));

         //название
         if (modal.el.querySelector('#title-input').value.length < 1) {
            isValid = false;
            modal.el.querySelector('#title-input').className = 'validate invalid';
         }

         //объем
         if (modal.el.querySelector('#capacity-input').value.length < 1) {
            isValid = false;
            modal.el.querySelector('#capacity-input').className = 'validate invalid';
         }

         //единицы измерения
         if (modal.el.querySelector('#product-unit').selectedIndex < 1 && modal.el.querySelector('#product-unit').value.length < 1) {
            isValid = false;
         }

         //тип
         if (modal.el.querySelector('#product-type').selectedIndex < 1 && modal.el.querySelector('#product-type').value.length < 1) {
            isValid = false;
         }

         //срок годности
         if (modal.el.querySelector('#valid-input').value.length < 1) {
            isValid = false;
            modal.el.querySelector('#valid-input').className = 'validate invalid';
         }

         if (!isValid) M.toast({
            html: 'Заполните обязательные поля!'
         });
         return isValid;
      }

      function clearForm() {
         let modalNode = document.getElementById('add-modal');
         let inputs = modalNode.querySelectorAll('input');
         inputs.forEach(val => {
            val.value = '';
         });
         document.getElementById('extended-fields').checked = false;
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

         let messageNode = document.createElement('div');
         messageNode.className = `card-panel ${color} lighten-4 message-card fadeIn`;
         messageNode.innerHTML = `
            ${response.message}
            <a class="close-message"><i class="material-icons">close</i></a>
            `;
         let parentNode = document.querySelector('#main-wrapper .container');
         parentNode.insertAdjacentElement('afterbegin', messageNode);
      }


      function checkMessage() {
         let dataObj = {};
         let params = decodeURIComponent(location.search.substr(1)).split('&');
         for (let i = 0; i < params.length; i++) {
            let param = params[i].split('=');
            dataObj[param[0]] = param[1];
         }

         console.log(dataObj);

         if (dataObj.success == 'true' && dataObj.hasOwnProperty('target')) {
            let reg = '/\+';
            showMessage({
               message: `Объект "${dataObj.target.split('+').join(' ')}" успешно сохранен.`,
               type: 'success'
            });
         }

      }
      checkMessage();
   </script>


</body>

</html>