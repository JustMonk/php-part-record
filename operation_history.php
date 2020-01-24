<?php
include './include/inc_config.php';
include './include/session_config.php';
include './include/auth_redirect.php';
include './include/check_admin_access.php';
?>

<?php
// Переменная хранит число сообщений выводимых на станице
$num = 20;

//поиск
$search_condition = array();
$search_tags = array(); //форма с человекочитаемым текущим запросом и кнопкой отмены поиска
if ($_GET['type']) {
   $type = $_GET['type'];
   if ($_GET['type'] == 'any') {
      $type = '';
   }
   array_push($search_tags, 'Операция: ' . (htmlspecialchars($type) ? htmlspecialchars($type) : 'любая'));
   array_push($search_condition, 'operation_name LIKE "%' . htmlspecialchars($type) . '%"');
}
if ($_GET['document']) {
   array_push($search_tags, 'Документ: ' . htmlspecialchars($_GET['document']));
   array_push($search_condition, 'document_number LIKE "%' . htmlspecialchars($_GET['document']) . '%"');
}
if ($_GET['date']) {
   array_push($search_tags, 'Дата операции: ' . htmlspecialchars($_GET['date']));
   array_push($search_condition, 'operation_date LIKE "%' . htmlspecialchars($_GET['date']) . '%"');
}
if ($_GET['partner']) {
   array_push($search_tags, 'Контрагент: ' . htmlspecialchars($_GET['partner']));
   array_push($search_condition, 'partners.name LIKE "%' . htmlspecialchars($_GET['partner']) . '%"');
}
//если условий больше 0, то создаем WHERE
if (count($search_condition) > 0) {
   $search_condition = 'WHERE ' . join(" AND ", $search_condition);
   $cancel_search = '<a href="./operation_history.php">Отменить поиск</a>';
} else {
   $search_condition = '';
   $search_tags = array();
   $cancel_search = '';
}


// Извлекаем из URL текущую страницу
$page = $_GET['page'];
// Определяем общее число сообщений в базе данных
$result = $mysqli->query("SELECT COUNT(*) 
FROM operation_history
LEFT JOIN partners ON operation_history.partner_code = partners.partner_id OR partners.partner_id IS NULL
LEFT JOIN operation_types ON operation_history.operation_type = operation_types.operation_type_id
LEFT JOIN users ON operation_history.user_code = users.user_id
$search_condition");
$operations = $result->fetch_row()[0];

// Находим общее число страниц
$total = intval(($operations - 1) / $num) + 1;
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
$result = $mysqli->query("SELECT operation_history.operation_id, operation_types.operation_name, operation_history.document_number, operation_history.operation_date, partners.name AS partner, operation_history.timestamp, users.login AS user
FROM operation_history
LEFT JOIN partners ON operation_history.partner_code = partners.partner_id OR partners.partner_id IS NULL
LEFT JOIN operation_types ON operation_history.operation_type = operation_types.operation_type_id
LEFT JOIN users ON operation_history.user_code = users.user_id
$search_condition
ORDER BY operation_id DESC
LIMIT $start, $num");
// В цикле переносим результаты запроса в массив $product_rows
while ($operation_rows[] = mysqli_fetch_array($result));
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
         <div class="content-wrapper">
            <div class="card-panel white">



               <div id="prihod" class="content-block">
                  <h2 style="margin: 0">История операций</h2>
                  <hr>
                  <p>Представление таблицы «<b>operation_history</b>». Содержит список всех операций (приход/продажа/производство). </p>

                  <div class="admin-edit-bar">
                     <a class="waves-effect waves-light btn blue lighten-2" id="search-operation-form">Поиск</a>
                  </div>
                  <div class="info-message">
                     <?php
                     $tag_list = '';
                     foreach ($search_tags as $value) {
                        $tag_list .= "<div class='search-tag'>$value</div>";
                     }

                     echo "Результатов: $operations <br>
                     <div class='search-form'>
                     $tag_list
                     </div>
                     $cancel_search
                     ";
                     //echo '/operation_view.php?' . $_SERVER['QUERY_STRING'];
                     ?>
                  </div>

                  <div class="table-wrapper">
                     <table class="monitoring-table">
                        <thead>
                           <tr>
                              <th>ID операции</th>
                              <th>Тип операции</th>
                              <th>Номер документа</th>
                              <th>Дата операции</th>
                              <th>Контрагент</th>
                              <th>Дата записи в базу</th>
                              <th>Пользователь</th>
                           </tr>
                        </thead>

                        <tbody>

                           <?php
                           if (stristr($_SERVER['REQUEST_URI'], '?')) $join_symbol = '&';
                           else $join_symbol = '';

                           foreach ($operation_rows as $row) {
                              if ($row) {
                                 $link = './operation_view.php?' . $_SERVER['QUERY_STRING'] . $join_symbol . "id=$row[operation_id]";
                                 echo "<tr>
                              <td> <a href='$link'>$row[operation_id]</a> </td>
                              <td> <a href='$link'>$row[operation_name]</a> </td>
                              <td> <a href='$link'>$row[document_number]</a> </td>
                              <td> <a href='$link'>$row[operation_date]</a> </td>
                              <td> <a href='$link'>$row[partner]</a> </td>
                              <td> <a href='$link'>$row[timestamp]</a> </td>
                              <td> <a href='$link'>$row[user]</a> </td>
                              </tr>";
                              }
                           }
                           ?>

                        </tbody>
                     </table>
                  </div>
               </div>

               <ul class="pagination">
                  <?php
                  //ссылка со всеми параметрами, но без page
                  $query_array = explode('&', $_SERVER['QUERY_STRING']);
                  foreach ($query_array as $key => $value) {
                     if (substr($value, 0, 5) == 'page=') {
                        unset($query_array[$key]);
                     }
                  }
                  $return_link = join("&", $query_array);
                  if (strlen($return_link) > 0) $return_link = './operation_history.php?' . $return_link;
                  else {
                     $return_link = './operation_history.php?';
                     $join_symbol = '';
                  }
                  $page_link = $return_link . $join_symbol . "page=";

                  // Проверяем нужны ли стрелки назад
                  $pervpage = '<li class="disabled"><a href="#!"><i class="material-icons">chevron_left</i></a></li>'; //дефолтное значение (серая кнопка)

                  if ($page != 1) $pervpage = "<li class='waves-effect'> <a href=$page_link" . ($page - 1) . '><i class="material-icons">chevron_left</i></a></li>';
                  // <a href= ./product_list.php?page=' . ($page - 1) . '><</a> ';
                  // Проверяем нужны ли стрелки вперед
                  $nextpage = '<li class="disabled"><a href="#!"><i class="material-icons">chevron_right</i></a></li>'; //дефолтное значение (серая кнопка)
                  if ($page != $total) $nextpage = "<li class='waves-effect'> <a href=$page_link" . ($page + 1) . '><i class="material-icons">chevron_right</i></a></li>';
                  //<a href= ./product_list.php?page=' . $total . '>>></a>';

                  // Находим две ближайшие станицы с обоих краев, если они есть
                  if ($page - 2 > 0) $page2left = "<li class='waves-effect'> <a href=$page_link" . ($page - 2) . '>' . ($page - 2) . '</a> </li>';
                  if ($page - 1 > 0) $page1left = "<li class='waves-effect'> <a href=$page_link" . ($page - 1) . '>' . ($page - 1) . '</a> </li>';
                  if ($page + 2 <= $total) $page2right = "<li class='waves-effect'> <a href=$page_link" . ($page + 2) . '>' . ($page + 2) . '</a> </li>';
                  if ($page + 1 <= $total) $page1right = "<li class='waves-effect'> <a href=$page_link" . ($page + 1) . '>' . ($page + 1) . '</a> </li>';

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
         <h4 id="modal-title" style="font-weight: bold;"><i class="fas fa-search"></i> Поиск операции</h4>
         <p id="modal-desc">Выберите интересующие условия и нажмите кнопку "Поиск"</p>
         <h5>Основные поля</h5>

         <div id="main-fields">
            <div class="row">
               <div class="col s12 flex-col">
                  <div class="input-field">
                     <select id="operation-type">
                        <option value="any" selected>Любая</option>
                        <option value="add">Приход</option>
                        <option value="sell">Продажа</option>
                        <option value="prod">Производство</option>
                        <option value="inv">Инвентаризация</option>
                     </select>
                     <label>Тип операции</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Начните вводить название для поиска">help_outline</i>
               </div>
            </div>


            <div class="row">
               <div class="col s12 flex-col">
                  <div class="input-field">
                     <input autocomplete="off" placeholder="Введите номер документа" id="doc-number" type="text" class="">
                     <label for="doc-number">Номер документа</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Количество товара">help_outline</i>
               </div>
            </div>

            <div class="row">
               <div class="col s12 flex-col">
                  <div class="input-field">
                     <input id="operation-date" type="text" class="datepicker">
                     <label for="operation-date">Дата операции</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Количество товара">help_outline</i>
               </div>
            </div>


            <div class="row">
               <div class="col s12 flex-col">
                  <div class="input-field autocomplete-field">
                     <input autocomplete="off" data-autocomplete-object="partners" id="partner-select" type="text" class="">
                     <label for="partner-select">Контрагент</label>
                  </div>
                  <i class="material-icons help-icon" data-tooltip="Выберите контрагента из списка">help_outline</i>
               </div>
            </div>


         </div>

      </div>

      <div class="modal-footer">
         <!-- js controls -->
         <a href="#!" id="start-search" class="waves-effect waves-green btn blue">Поиск</a>
         <a href="#!" class="modal-close waves-effect waves-green btn grey">Отмена</a>
      </div>
   </div>


   <script src="./assets/materialize/js/materialize.min.js"></script>
   <script src="./js/script.js"></script>
   <script src="./js/logout.js"></script>

   <script>
      let globalState = {};

      //default modal init===================
      document.addEventListener('DOMContentLoaded', function() {
         var elems = document.querySelector('#add-modal');
         var instances = M.Modal.init(elems, {});
      });
      //modal open handler
      document.addEventListener('click', (e) => {
         let modal = M.Modal.getInstance(document.getElementById('add-modal'));
         if (e.target.id == 'search-operation-form') {
            fetch('action/get_partners.php').then(result => {
               return result.json();
            }).then(json => {
               globalState.partners = json;
               //reinit
               M.Autocomplete.init(document.querySelector('#partner-select'), {
                  data: globalState.partners,
                  minLength: 0
               });
               modal.open();
            });
         }

         //search handler
         if (e.target.id == 'start-search') {
            let url = './operation_history.php';

            let type = document.getElementById('operation-type').value;
            let documentNum = document.getElementById('doc-number').value;
            let operationDate = document.getElementById('operation-date').value; //new Date(document.getElementById('operation-date').value + ' UTC').toISOString().split('T')[0];
            let partner = document.getElementById('partner-select').value;

            let query = `type=${type}` +
               (documentNum ? '&document=' + encodeURI(documentNum) : '') +
               (operationDate ? '&date=' + new Date(document.getElementById('operation-date').value + ' UTC').toISOString().split('T')[0] : '') +
               (partner ? '&partner=' + encodeURI(partner) : '');

            console.log(query);

            document.location.replace(`./operation_history.php?${query}`);
         }
      });

      //select init
      document.addEventListener('DOMContentLoaded', function() {
         var elems = document.querySelectorAll('select');
         var instances = M.FormSelect.init(elems, {});
      });

      //datepickers
      document.addEventListener('DOMContentLoaded', function() {
         var elems = document.querySelectorAll('.datepicker');
         var instances = M.Datepicker.init(elems, {});
      });

      //callback, чтобы убирать значение если введенного нет в стэйте, либо возвращать предыдущее
      document.addEventListener('focusout', (e) => {
         if (e.target.tagName != 'INPUT' && !e.target.hasAttribute('data-autocomplete-object')) {
            return;
         }
         if (!globalState[e.target.getAttribute('data-autocomplete-object')].hasOwnProperty([e.target.value])) {
            e.target.value = '';
            M.updateTextFields();
         }
      });
   </script>

</body>

</html>