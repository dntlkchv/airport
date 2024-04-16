<?php
require_once 'connect.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <? include('./templates/head.php') ?>
</head>
<body>
    <? include('./templates/header.php') ?>
    <main>
        <h1>Вы уже знакомы с Полетом?</h1>

        <form action="login.php" method="post">
            <input  class="auto_style" type="submit" value="Войти">
        </form>

        <form action="register.php" method="post">
            <input class="auto_style" type="submit" value="Зарегистрироваться">
        </form>
    </main>

    
    <? include('./templates/footer.php') ?>

</body>
</html>