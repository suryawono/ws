<?php

$mysqli = new mysqli('localhost', 'prt', 'prt!2015', 'prt');
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}
