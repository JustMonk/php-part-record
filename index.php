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
   <noscript>
      <div style="position: absolute; height: 100vh; width: 100vw; z-index: 500; background: #fff;">Ваш браузер не поддерживает JavaScript</div>
   </noscript>

   <div id="dashboard">

      <?php include './include/inc_sidebar.php'; ?>

      <div id="main-wrapper">
         <div class="container-wide" style="padding: 40px 100px">
            <div class="">


               <div id="prihod" class="content-block">
                  <h2 style="margin: 0">Главная</h2>
                  <h3 style="margin: 0; font-family: 'Source Sans Pro', sans-serif; color:#6a869a; margin-top: 5px;">Быстрый старт</h3>

                  <div class="card-wrapper" style="margin: 40px 0;">

                     <div class="card small" style="width: 350px; display: inline-block; margin-right: 20px; margin-top: 20px;">
                        <div class="card-image">
                           <div class="images-dummy" style="width: 100%;height: 400px;background: #5c5377;"></div>

                           <span class="card-title" style="top: 0; font-size: 15px;">Разработка</span>
                           <span class="card-title" style="top: 25px;">Карточки в разработке</span>
                        </div>
                        <div class="card-content">
                           <p>Используйте меню для навигации.</p>
                        </div>
                        <div class="card-action">
                           <a href="#">Образец</a>
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
</body>

</html>