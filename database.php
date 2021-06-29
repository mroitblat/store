<?php
$mysqli = new mysqli('localhost', 'store', 'store', 'store', 8889);

if($mysqli->connect_errno) {
    printf("Connection Failed: %s\n", $mysqli->connect_error);
    exit;
}
?>