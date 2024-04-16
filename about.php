<?php
require_once 'connect.php';

$gallery = mysqli_query($connect, "SELECT * FROM `gallery`")->fetch_all(MYSQLI_ASSOC);


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <? include('./templates/head.php') ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />


</head>

<body style="display: block;">
    <? include('./templates/header.php') ?>

    <main>
        <div>
            <h1 class="marg">О нас</h1>

            <p>С 2011 года помогаем нашим пользователям искать выгодные билеты и путешествовать с комфортом. Наша цель — не просто помочь вам сэкономить, но и стать персональным помощником на всём пути: от выбора направления до возвращения домой.</p>

            <div style="overflow: hidden;">
                <div class="swiper">
                    <!-- Additional required wrapper -->
                    <div class="swiper-wrapper">
                        <!-- Slides -->
                        <? foreach ($gallery as $photo) : ?>
                            <div class="swiper-slide slide">
                                <img src="<?= $photo['image'] ?>" style="object-fit: cover; width: 100%; height: 300px;">
                                <h2><?= $photo['title'] ?></h2>
                            </div>
                        <? endforeach ?>

                    </div>

                    <!-- If we need navigation buttons -->
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>

                </div>
            </div>
        </div>
    </main>

    <? include('./templates/footer.php') ?>
    <script type="module">
        import Swiper from 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.mjs'

        const swiper = new Swiper('.swiper', {
            slidesPerView: 3,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    </script>
</body>

</html>