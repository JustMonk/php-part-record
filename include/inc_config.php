<?php

$mysqli = new mysqli("194.87.147.158", "dev", "developer773full", "reactive_record");

/* проверяем соединение */
if (mysqli_connect_errno()) {
   printf("Connect failed: %s\n", mysqli_connect_error());
   exit();
}

?>