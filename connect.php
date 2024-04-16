<?php

$host="localhost";
$user="root";
$password="";
$database="airport";

$connect = mysqli_connect($host, $user, $password, $database);

if (!$connect){
    die("Error");
}
