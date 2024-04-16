<?php
require_once 'connect.php';
require_once 'auth.php';

$result = "";

if (isset($_POST['submit'])) {
    mail($_POST['email'], "Обратная связь", "Здравствуйте, {$_POST['name']}! Ваше обращение зарегистрировано!\nТекст вопроса: {$_POST['question']}.");
    $result = "Спасибо за вопрос, мы ответим в ближайшее время!";
}

$user = getCurrentUser();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <? include('./templates/head.php') ?>
</head>

<body>
    <? include('./templates/header.php') ?>
    <main>
        <h1>Задать вопрос</h1>
        <form class="h-center v-center" action="" method="post">
            <input class="auto_style1" type="text" name="name" placeholder="Ваше имя"  required>
            <input class="auto_style1" type="email" name="email" placeholder="Ваша почта" value="<?= $user['email'] ?? "" ?>"required>
            <textarea class="auto_style1" name="question" placeholder="Ваш вопрос" required></textarea>
            <input class="auto_style " type="submit" name="submit" value="Отправить">
            <span><?= $result ?></span>
        </form>
    </main>

    <? include('./templates/footer.php') ?>

</body>

</html>