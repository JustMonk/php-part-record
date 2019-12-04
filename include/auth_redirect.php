<?php
//TODO: перенести в отдельный файл
if ( $_SESSION['isAutorized'] == true) { //enum: 0 - DISABLED, 1 - NONE, 2 - ACTIVE
   if ($_SERVER['REQUEST_URI'] == '/login.php') {
      header('Location: index.php');
   }
} else {
   if ($_SERVER['REQUEST_URI'] != '/login.php') {
      header('Location: login.php');
   }
}

?>