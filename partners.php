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
$result = $mysqli->query("SELECT COUNT(*) FROM partners");
$partners = $result->fetch_row()[0];

// Находим общее число страниц
$total = intval(($partners - 1) / $num) + 1;
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
$result = $mysqli->query("SELECT * FROM partners
ORDER BY partner_id DESC
LIMIT $start, $num");
// В цикле переносим результаты запроса в массив $product_rows
while ($partners_rows[] = mysqli_fetch_array($result));
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
                  <h2 style="margin: 0">Список контрагентов</h2>
                  <hr>
                  <p>Представление таблицы «<b>partners</b>». Содержит список контрагентов. </p>

                  <div class="admin-edit-bar">
                     <a class="waves-effect waves-light btn blue lighten-2" id="add-partner-form">Добавить контрагента</a>
                  </div>

                  <table>
                     <thead>
                        <tr>
                           <th>ID</th>
                           <th>Название</th>
                           <th>ИНН</th>
                           <th>КПП</th>
                           <th>Комментарий</th>
                        </tr>
                     </thead>

                     <tbody>

                        <?php
                        foreach ($partners_rows as $row) {
                           if ($row) {
                              echo "<tr>
                              <td>$row[partner_id]</td>
                              <td>$row[name]</td>
                              <td>$row[inn]</td>
                              <td>$row[kpp]</td>
                              <td>$row[comment]</td>
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
                  if ($page != 1) $pervpage = '<li class="waves-effect"><a href= ./partners.php?page=' . ($page - 1) . '><i class="material-icons">chevron_left</i></a></li>';
                  // <a href= ./product_list.php?page=' . ($page - 1) . '><</a> ';
                  // Проверяем нужны ли стрелки вперед
                  $nextpage = '<li class="disabled"><a href="#!"><i class="material-icons">chevron_right</i></a></li>'; //дефолтное значение (серая кнопка)
                  if ($page != $total) $nextpage = '<li class="waves-effect"><a href= ./partners.php?page=' . ($page + 1) . '><i class="material-icons">chevron_right</i></a></li>';
                  //<a href= ./product_list.php?page=' . $total . '>>></a>';

                  // Находим две ближайшие станицы с обоих краев, если они есть
                  if ($page - 2 > 0) $page2left = '<li class="waves-effect"> <a href= ./partners.php?page=' . ($page - 2) . '>' . ($page - 2) . '</a> </li>';
                  if ($page - 1 > 0) $page1left = '<li class="waves-effect"> <a href= ./partners.php?page=' . ($page - 1) . '>' . ($page - 1) . '</a> </li>';
                  if ($page + 2 <= $total) $page2right = '<li class="waves-effect"> <a href= ./partners.php?page=' . ($page + 2) . '>' . ($page + 2) . '</a> </li>';
                  if ($page + 1 <= $total) $page1right = '<li class="waves-effect"> <a href= ./partners.php?page=' . ($page + 1) . '>' . ($page + 1) . '</a> </li>';

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
         <h4 id="modal-title" style="font-weight: bold;">Добавление контрагента</h4>
         <p id="modal-desc">Заполните необходимую информацию</p>
         <h5>Основные поля</h5>

         <div id="main-fields">
            <div class="row">
               <div class="col s6 flex-col">
                  <div class="input-field">
                     <input autocomplete="off" placeholder="Введите ИНН" id="inn-input" type="text" class="">
                     <label for="goods-select">ИНН</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Начните вводить название для поиска">help_outline</i>
               </div>

               <div class="col s6 flex-col">
                  <div class="input-field">
                     <input autocomplete="off" placeholder="Введите КПП" id="kpp-input" type="text" class="">
                     <label for="goods-count">КПП (опционально)</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Количество товара">help_outline</i>
               </div>
            </div>


            <div class="row">
               <div class="col s12 flex-col">
                  <div class="input-field">
                     <input autocomplete="off" placeholder="Введите название организации" id="name-input" type="text" class="">
                     <label for="goods-count">Наименование контрагента</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Количество товара">help_outline</i>
               </div>
            </div>

            <div class="row">
               <div class="col s12 flex-col">
                  <div class="input-field">
                     <input autocomplete="off" placeholder="Введите комментарий (не обязательно)" id="comment-input" type="text" class="">
                     <label for="goods-count">Комментарий (опционально)</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Количество товара">help_outline</i>
               </div>
            </div>

         </div>

      </div>

      <div class="modal-footer">
         <!-- js controls -->
         <a href="#!" id="add-partner-button" class="waves-effect waves-green btn blue">Добавить</a>
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
         if (e.target.id == 'add-partner-form') {
            clearForm();
            modal.open();
         }

         if (e.target.id == 'add-partner-button') {
            if (!formValidation()) return;

            let data = {
               inn: document.getElementById('inn-input').value,
               kpp: document.getElementById('kpp-input').value,
               name: document.getElementById('name-input').value,
               comment: document.getElementById('comment-input').value
            }

            console.log(JSON.stringify(data));

            fetch('action/admin/partner-add.php', {
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