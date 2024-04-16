<?php
require_once 'connect.php';
require_once 'auth.php';

$user = getCurrentUser();
$cart = [];

if($user){
	$cart = mysqli_query($connect, "SELECT * from cart WHERE id_user={$user['id']}")->fetch_all(MYSQLI_ASSOC);
}

?>
<header>
    <div class="header-left">
        <h2><b>Аэропорт</b></h2>

        <a class="v-center" href="index.php">Главная</a>
        <a class="v-center" href="about.php">О нас</a>
        <a class="v-center"  href="contacts.php">Контакты</a>

        <? if($user): ?>
        <a class="v-center" href="cabinet.php">Кабинет</a>
        <? endif; ?>
    </div>

    <div>
        <? if($user): ?>
            <span>Здравствуйте, <?= $user['email'] ?>!</span>
            <a href="cart.php">Корзина (<?= count($cart) ?>)</a>
            <a href="logout.php">Выйти</a>
        <? else: ?>
            <a href="switch_auth.php">Авторизация</a>
        <? endif; ?>
    </div>
</header>