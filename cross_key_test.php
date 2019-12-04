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
                  <h2 style="margin: 0">Тестирование первичных ключей</h2>
                  <hr>
                  <p>Тестирование первичных ключей в SELECT (перекрестный запрос)</p>
                  <table>
                     <thead>
                        <tr>
                           <th>ID</th>
                           <th>Тип операции</th>
                           <th>Номер документа</th>
                           <th>Дата операции</th>
                        </tr>
                     </thead>

                     <tbody>

                        <?php
                        //foreach ($mysqli->query('SELECT ID, (SELECT operation_name FROM operation_types WHERE ID=operation_history.operation_type) as operation_type, document_number FROM operation_history') as $row) {
                        foreach ($mysqli->query('SELECT * FROM operation_history INNER JOIN operation_types ON operation_history.operation_type = operation_types.id ') as $row) {
                           echo "<tr>
                           <td>$row[operation_id]</td>
                           <td>$row[operation_name]</td>
                           <td>$row[document_number]</td>
                           <td>$row[operation_date]</td>
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

   <script src="./assets/materialize/js/materialize.min.js"></script>
   <script src="./js/script.js"></script>
   <script src="./js/logout.js"></script>
</body>

</html>