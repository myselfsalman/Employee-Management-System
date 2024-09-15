<?php

$con = new mysqli('localhost', 'root', '', 'employee_management');

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

?>
