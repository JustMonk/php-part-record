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

   <div id="login-screen" style="">

      <div id="login-card" class="card-panel white" style="width: 500px">
         <h5 style="margin-bottom: 30px; margin-top: 0;">Вход в систему</h5>

         <div class="input-field col s12">
            <input id="login" type="text" class="validate">
            <label for="login">Логин</label>
         </div>

         <div class="input-field col s12">
            <input id="password" type="password" class="validate">
            <label for="password">Пароль</label>
            <span class="helper-text" data-error="Неверный логин или пароль"></span>
         </div>

         <a id="login-button" class="waves-effect waves-light btn-large" style="width: 100%">Войти</a>
      </div>

   </div>
    

   <script src="./assets/materialize/js/materialize.min.js"></script>
   <script src="./js/login.js"></script>
</body>

</html>