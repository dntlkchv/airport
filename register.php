<?php
require_once 'connect.php';

$error = "";
if(isset($_POST['submit'])){
    
    if ($_POST['pass'] != $_POST['pass1']){
        $error = "Пароли не совпадают";
    } else {
        $hash = password_hash($_POST['pass'], PASSWORD_BCRYPT);

        $email = $connect->real_escape_string($_POST["email"]);
        $telephone = $connect->real_escape_string(preg_replace("/\D/", '', $_POST["telephone"]));
        $password = $connect->real_escape_string($hash);
    
        $connect->query("INSERT INTO user(email, telephone, password) 
        VALUES ('$email','$telephone','$password')");
        
        setcookie("userID", mysqli_insert_id($connect));

        if(isset($_COOKIE['booking_flight_id'])){
            header("Location: booking.php?flight_id={$_COOKIE['booking_flight_id']}");
        } else {
            header("Location: index.php");
        }
        exit();
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
        
        <label>Адрес электронной почты </label><br>
        <input class="auto_style1"  type="email" name="email" placeholder="email@adress.ru" required><br><br>

        <label>Номер телефона</label><br>
        <input class="auto_style1"  type="tel" name="telephone" placeholder="+79000000000" required><br><br>

        <label>Придумайте пароль</label><br>
        <input class="auto_style1"  type="password" name="pass" placeholder="xxxxx"><br><br>

        <label>Придумайте пароль</label><br>
        <input class="auto_style1" type="password" name="pass1" placeholder="xxxxx"><br><br>
        
        <label>"Я согласен с условиями компании"</label><br>
        <input type="checkbox" class="auto_style" required><br><br>

        <input class="auto_style" type="submit" name="submit" value="Отправить данные"><br><br>
    </form>
    </main>

   <? include('./templates/footer.php') ?>
    
</body>

</html>