<?php
require_once 'connect.php';

setcookie("userID", "", -1);
header("Location: index.php");
exit();

?>
