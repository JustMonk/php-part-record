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
$result = $mysqli->query("SELECT COUNT(*) FROM operation_sell");
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
$result = $mysqli->query("SELECT operation_history.document_number, operation_sell.product_name, operation_sell.count, operation_sell.create_date, operation_sell.expire_date 
FROM operation_sell, operation_history 
WHERE operation_sell.operation_id = operation_history.operation_id
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
                  <h2 style="margin: 0">История продаж</h2>
                  <hr>
                  <p>Представление таблицы «<b>operation_sell</b>». Содержит список всех проданных позиций, привязанных к операции продажи. </p>
                  <table>
                     <thead>
                        <tr>
                           <th>Номер документа</th>
                           <th>Номенклатура</th>
                           <th>Количество</th>
                           <th>Дата производства</th>
                           <th>Годен до</th>
                        </tr>
                     </thead>

                     <tbody>

                        <?php
                        foreach ($product_rows as $row) {
                           if ($row) {
                              echo "<tr>
                              <td>$row[document_number]</td>
                              <td>$row[product_name]</td>
                              <td>$row[count]</td>
                              <td>$row[create_date]</td>
                              <td>$row[expire_date]</td>
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
                  if ($page != 1) $pervpage = '<li class="waves-effect"><a href= ./sell_history.php?page=' . ($page - 1) . '><i class="material-icons">chevron_left</i></a></li>';
                  // <a href= ./product_list.php?page=' . ($page - 1) . '><</a> ';
                  // Проверяем нужны ли стрелки вперед
                  $nextpage = '<li class="disabled"><a href="#!"><i class="material-icons">chevron_right</i></a></li>'; //дефолтное значение (серая кнопка)
                  if ($page != $total) $nextpage = '<li class="waves-effect"><a href= ./sell_history.php?page=' . ($page + 1) . '><i class="material-icons">chevron_right</i></a></li>';
                  //<a href= ./product_list.php?page=' . $total . '>>></a>';

                  // Находим две ближайшие станицы с обоих краев, если они есть
                  if ($page - 2 > 0) $page2left = '<li class="waves-effect"> <a href= ./sell_history.php?page=' . ($page - 2) . '>' . ($page - 2) . '</a> </li>';
                  if ($page - 1 > 0) $page1left = '<li class="waves-effect"> <a href= ./sell_history.php?page=' . ($page - 1) . '>' . ($page - 1) . '</a> </li>';
                  if ($page + 2 <= $total) $page2right = '<li class="waves-effect"> <a href= ./sell_history.php?page=' . ($page + 2) . '>' . ($page + 2) . '</a> </li>';
                  if ($page + 1 <= $total) $page1right = '<li class="waves-effect"> <a href= ./sell_history.php?page=' . ($page + 1) . '>' . ($page + 1) . '</a> </li>';

                  // Вывод меню
                  echo $pervpage . $page2left . $page1left . '<li class="active"><a href="#!">' . $page . '</a></li>' . $page1right . $page2right . $nextpage;
                  ?>
               </ul>

            </div>


         </div>
      </div>
   </div>

   <script src="./assets/materialize/js/materialize.min.js"></script>
   <script src="./js/script.js"></script>
   <script src="./js/logout.js"></script>
</body>

</html>