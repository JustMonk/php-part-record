<?php
include '../include/inc_config.php';
include '../include/session_config.php';
include '../include/auth_redirect.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="ie=edge">
   <title>Part record</title>
   <link href="../assets/materialize/css/materialize.min.css" rel="stylesheet">
   <link href="../style.css" rel="stylesheet">
   <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
   <link href="../assets/font-awesome/css/all.css" rel="stylesheet">
   <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700&display=swap&subset=cyrillic" rel="stylesheet">
   <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
   <noscript>
      <div style="position: absolute; height: 100vh; width: 100vw; z-index: 500; background: #fff;">Ваш браузер не поддерживает JavaScript</div>
   </noscript>

   <div id="dashboard">

      <?php include '../include/inc_sidebar.php'; ?>


      <div id="main-wrapper">
         <div class="content-wrapper">

            <div class="card-panel white">

               <div id="prihod" class="content-block">
                  <h2 style="margin: 0" class="operation-title"><i class="fas fa-user-cog operation-icon"></i> Настройки</h2>
                  <hr>
                  <div id="test1" class="col s12" style="padding: 20px">
                     <h5>Смена пароля</h5>

                     <div class="row" style="margin-bottom: 0;">
                        <div class="col s12 flex-col">
                           <div class="input-field">
                              <input id="old-password" type="password">
                              <label for="old-password">Старый пароль</label>
                           </div>
                        </div>

                        <div class="col s12 flex-col">
                           <div class="input-field">
                              <input id="new-password" type="password">
                              <label for="new-password">Новый пароль</label>
                           </div>
                        </div>

                        <div class="col s12 flex-col">
                           <div class="input-field">
                              <input id="new-password-repeat" type="password">
                              <label for="new-password-repeat">Повторите новый пароль</label>
                           </div>
                        </div>

                        <div class="divider" style="margin: 10px 0px;"></div>
                        <a id="change-password" class="waves-effect waves-light btn blue lighten-1">Изменить пароль</a>
                     </div>

                  </div>
               </div>
            </div>


         </div>


      </div>
   </div>

   <?php include '../include/modals/add_modal.php'; ?>


   <script src="../assets/materialize/js/materialize.min.js"></script>
   <script src="../js/script.js"></script>
   <script src="../js/logout.js"></script>

   <script>
      //modal init===================
      document.addEventListener('click', e => {
         if (e.target.tagName == 'A' && e.target.id == 'change-password') {
            let oldPass = document.getElementById('old-password');
            let newPass = document.getElementById('new-password');
            let repeatPass = document.getElementById('new-password-repeat');
            if (oldPass.value.length > 0 && newPass.value == repeatPass.value) {
               //..fetch
               let data = {
                  login: "<?php echo $_SESSION['login'] ?>",
                  oldPass: oldPass.value,
                  newPass: newPass.value
               }

               console.log(JSON.stringify(data));

               fetch('change_user_password.php', {
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
                  showMessage(json);
               });
            } else {
               M.toast({
                  html: 'Одно из полей не заполнено, либо пароли не совпадают!'
               });
            }
         }
      });

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
   </script>

</body>

</html>