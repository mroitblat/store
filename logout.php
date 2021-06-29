<?php
//Destroys session to log user out
session_start();
session_destroy();
header('Location: login.php');
exit;
?>
