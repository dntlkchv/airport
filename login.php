<?php
require_once 'connect.php';
$error = '';

if (isset($_POST['submit'])) {
    $email = $connect->real_escape_string($_POST["email"]);
    $password = $connect->real_escape_string($_POST['password']);

    $user = $connect->query("SELECT * FROM `user` WHERE email='{$email}'");

    if (($user = $user->fetch_assoc()) && password_verify($_POST['password'], $user['password'])) {
        setcookie("userID", $user['id']);

        if (isset($_COOKIE['booking_flight_id'])) {
            header("Location: booking.php?flight_id={$_COOKIE['booking_flight_id']}");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "Неправильный логин или пароль";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <? include('./templates/head.php') ?>
</head>

<body>
    <? include('./templates/header.php') ?>

    <main>
        <form method="post">
            <h1>Войдите</h1>

            <label>Введите адрес электронной почты </label><br>
            <input class="auto_style1" type="email" name="email" placeholder="адрес почты" required><br>

            <label>Пароль</label><br>
            <input class="auto_style1" type="password" name="password" placeholder="пароль"><br>

            <span class="error-text"><?= $error ?></span>

            <input class="auto_style" type="submit" name="submit" value="Войти"><br><br>
        </form>
    </main>

   <? include('./templates/footer.php') ?>


</body>

</html>