<?php
$access_query = $mysqli->query("SELECT title FROM account_types
WHERE ID = (SELECT access_id FROM users WHERE login = '$_SESSION[login]')
LIMIT 1");
$access = ($access_query->fetch_assoc())['title']; //admin or user
if ($access != 'admin') {
   header('Location: index.php');
}
?>