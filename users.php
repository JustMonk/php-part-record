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
$result = $mysqli->query("SELECT COUNT(*) FROM users");
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
$result = $mysqli->query("SELECT * FROM users
LEFT JOIN account_types ON users.access_id = account_types.ID
ORDER BY user_id DESC
LIMIT $start, $num");
// В цикле переносим результаты запроса в массив $product_rows
while ($users_rows[] = mysqli_fetch_array($result));
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
                  <h2 style="margin: 0">Список пользователей</h2>
                  <hr>
                  <p>Представление таблицы «<b>users</b>». Содержит список пользователей. </p>

                  <div class="admin-edit-bar">
                     <a class="waves-effect waves-light btn blue lighten-2" id="add-user-form">Добавить пользователя</a>
                  </div>

                  <table>
                     <thead>
                        <tr>
                           <th>ID</th>
                           <th>Логин</th>
                           <th>Полномочия</th>
                           <th>Имя</th>
                           <th>Фамилия</th>
                           <th></th>
                        </tr>
                     </thead>

                     <tbody>

                        <?php
                        foreach ($users_rows as $row) {
                           if ($row) {
                              echo "<tr>
                              <td>$row[user_id]</td>
                              <td>$row[login]</td>
                              <td>$row[title]</td>
                              <td>$row[name]</td>
                              <td>$row[lastname]</td>
                              <td><a class=\"product-edit\" data-user-id=\"$row[user_id]\">ред.</a></td>
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
         if (e.target.id == 'add-user-form') {
            setAddModal();
            modal.open();
         }

         //обработчик добавления пользователя
         if (e.target.id == 'add-user-button') {
            if (!formValidation()) return;

            let data = {
               login: document.getElementById('login-input').value,
               name: document.getElementById('name-input').value,
               lastname: document.getElementById('lastname-input').value,
               access: document.getElementById('access-level').value,
               password: document.getElementById('password-input').value
            }

            console.log(JSON.stringify(data));

            fetch('action/admin/user_add.php', {
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
               /*console.log(json);
               showMessage(json)
               modal.close();*/
               window.location.replace(`${window.location.origin + window.location.pathname}?success=${true}&target=${json.title}`);
            });
         }

         //модалка для редактирования
         if (e.target.tagName == 'A' && e.target.hasAttribute('data-user-id')) {
            setEditModal(e.target.getAttribute('data-user-id'));

            //edit-data fetch
            fetch(`action/admin/get_user_info.php?id=${e.target.getAttribute('data-user-id')}`, {
               method: 'GET'
            }).then(result => {
               return result.json();
            }).then(json => {
               console.log(json);
               document.getElementById('login-input').value = json.login;
               document.getElementById('name-input').value = json.name;
               document.getElementById('lastname-input').value = json.lastname;
               document.getElementById('access-level').value = json.access;
               //select init
               M.FormSelect.init(document.querySelectorAll('select'), {});
               //reinit
               M.updateTextFields();
               /*showMessage(json);
               modal.close();*/
            });

            modal.open();
         }

         //обработчик редактирования
         if (e.target.id == 'edit-user-button') {
            if (!editFormValidation()) return;

            let data = {
               id: document.getElementById('edit-user-button').getAttribute('data-id'),
               login: document.getElementById('login-input').value,
               name: document.getElementById('name-input').value,
               lastname: document.getElementById('lastname-input').value,
               access: document.getElementById('access-level').value,
            }

            if (document.getElementById('password-input').value.length != 0 && document.getElementById('password-input').value.length > 4) {
               data.password = document.getElementById('password-input').value;
            }

            console.log(JSON.stringify(data));

            fetch('action/admin/user_update.php', {
               method: 'POST',
               cache: 'no-cache',
               headers: {
                  'Content-Type': 'application/json'
               },
               body: JSON.stringify(data)
            }).then(result => {
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

         function formValidation() {
            let isValid = true;
            let modal = M.Modal.getInstance(document.getElementById('add-modal'));

            if (modal.el.querySelector('#login-input').value.length < 4) {
               isValid = false;
               modal.el.querySelector('#login-input').className = 'validate invalid';
            }

            if (modal.el.querySelector('#name-input').value.length < 1) {
               isValid = false;
               modal.el.querySelector('#name-input').className = 'validate invalid';
            }

            if (modal.el.querySelector('#lastname-input').value.length < 1) {
               isValid = false;
               modal.el.querySelector('#lastname-input').className = 'validate invalid';
            }

            if (modal.el.querySelector('#password-input').value.length < 4) {
               isValid = false;
               modal.el.querySelector('#password-input').className = 'validate invalid';
            }

            return isValid;
         }

         function editFormValidation() {
            let isValid = true;
            let modal = M.Modal.getInstance(document.getElementById('add-modal'));

            if (modal.el.querySelector('#login-input').value.length < 4) {
               isValid = false;
               modal.el.querySelector('#login-input').className = 'validate invalid';
            }

            if (modal.el.querySelector('#name-input').value.length < 1) {
               isValid = false;
               modal.el.querySelector('#name-input').className = 'validate invalid';
            }

            if (modal.el.querySelector('#lastname-input').value.length < 1) {
               isValid = false;
               modal.el.querySelector('#lastname-input').className = 'validate invalid';
            }

            if (modal.el.querySelector('#password-input').value.length != 0 && modal.el.querySelector('#password-input').value.length < 4) {
               isValid = false;
               modal.el.querySelector('#password-input').className = 'validate invalid';
            }

            return isValid;
         }

      });

      function setAddModal() {
         document.querySelector('#add-modal').innerHTML = `<div class="modal-content">
         <h4 id="modal-title" style="font-weight: bold;">Добавление пользователя</h4>
         <p id="modal-desc">Заполните необходимую информацию</p>
         <h5>Основные поля</h5>

         <div id="main-fields">
            <div class="row">
               <div class="col s12 flex-col">
                  <div class="input-field">
                     <input autocomplete="off" placeholder="Введите логин" id="login-input" type="text" class="">
                     <label for="login-input">Логин</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Начните вводить название для поиска">help_outline</i>
               </div>
            </div>


            <div class="row">
               <div class="col s6 flex-col">
                  <div class="input-field">
                     <input autocomplete="off" placeholder="Введите имя" id="name-input" type="text" class="">
                     <label for="name-input">Имя</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Количество товара">help_outline</i>
               </div>

               <div class="col s6 flex-col">
                  <div class="input-field">
                     <input autocomplete="off" placeholder="Введите фамилию" id="lastname-input" type="text" class="">
                     <label for="lastname-input">Фамилия</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Количество товара">help_outline</i>
               </div>
            </div>

            <div class="row">
               <div class="col s12 flex-col">
                  <div class="input-field">
                     <select id="access-level">
                        <option value="user" selected>Пользователь</option>
                        <option value="admin">Администратор</option>
                     </select>
                     <label>Полномочия</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Количество товара">help_outline</i>
               </div>
            </div>

            <div class="row">
               <div class="col s12 flex-col">
                  <div class="input-field">
                     <input autocomplete="off" placeholder="Задайте начальный пароль" id="password-input" type="text" class="">
                     <label for="password-input">Пароль</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Начните вводить название для поиска">help_outline</i>
               </div>
            </div>

         </div>

      </div>

      <div class="modal-footer">
         <!-- js controls -->
         <a href="#!" id="add-user-button" class="waves-effect waves-green btn blue">Добавить</a>
         <a href="#!" class="modal-close waves-effect waves-green btn grey">Отмена</a>
      </div>`;

         //select init
         M.FormSelect.init(document.querySelectorAll('select'), {});
         //reinit
         M.updateTextFields();
      }

      function setEditModal(id) {
         document.querySelector('#add-modal').innerHTML = `<div class="modal-content">
         <h4 id="modal-title" style="font-weight: bold;">Редактирование пользователя</h4>
         <p id="modal-desc">Измените интересующую информацию</p>
         <h5>Основные поля</h5>

         <div id="main-fields">
            <div class="row">
               <div class="col s12 flex-col">
                  <div class="input-field">
                     <input autocomplete="off" placeholder="Введите логин" id="login-input" type="text" class="">
                     <label for="login-input">Логин</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Начните вводить название для поиска">help_outline</i>
               </div>
            </div>


            <div class="row">
               <div class="col s6 flex-col">
                  <div class="input-field">
                     <input autocomplete="off" placeholder="Введите имя" id="name-input" type="text" class="">
                     <label for="name-input">Имя</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Количество товара">help_outline</i>
               </div>

               <div class="col s6 flex-col">
                  <div class="input-field">
                     <input autocomplete="off" placeholder="Введите фамилию" id="lastname-input" type="text" class="">
                     <label for="lastname-input">Фамилия</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Количество товара">help_outline</i>
               </div>
            </div>

            <div class="row">
               <div class="col s12 flex-col">
                  <div class="input-field">
                     <select id="access-level">
                        <option value="user" selected>Пользователь</option>
                        <option value="admin">Администратор</option>
                     </select>
                     <label>Полномочия</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Количество товара">help_outline</i>
               </div>
            </div>

            <div class="row">
            <div class="info-message">Если пользователь забыл пароль, вы можете временно задать ему новый. Если необходимости менять пароль нет, оставьте поле пустым.</div>
               <div class="col s12 flex-col">
                  <div class="input-field">
                     <input autocomplete="off" placeholder="Задайте новый пароль или оставьте пустым" id="password-input" type="text" class="">
                     <label for="password-input">Пароль</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Начните вводить название для поиска">help_outline</i>
               </div>
            </div>


         </div>

      </div>

      <div class="modal-footer">
         <!-- js controls -->
         <a href="#!" id="edit-user-button" data-id="${id}" class="waves-effect waves-green btn blue">Сохранить</a>
         <a href="#!" class="modal-close waves-effect waves-green btn grey">Отмена</a>
      </div>`;

         //select init
         M.FormSelect.init(document.querySelectorAll('select'), {});
         //reinit
         M.updateTextFields();
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
         let parentNode = document.querySelector('#main-wrapper .content-wrapper');
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
               message: `Пользователь "${dataObj.target.split('+').join(' ')}" успешно сохранен.`,
               type: 'success'
            });
         }

      }
      checkMessage();
   </script>

</body>

</html>