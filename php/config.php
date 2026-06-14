<?php
$host     = "localhost";
$user     = "root";
$password = "";
$database = "cook_point";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

session_start();
?>
