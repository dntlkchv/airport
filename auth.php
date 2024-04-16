<?php
require_once 'connect.php';

function getCurrentUser(){
    global $connect;

    if(!isset($_COOKIE['userID'])) return null;

    $user = mysqli_query($connect, "SELECT * from user WHERE user.id = {$_COOKIE['userID']}");
    return $user ? $user->fetch_assoc() : null;
}