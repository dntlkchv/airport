<?php
require_once 'connect.php';
$result = mysqli_query($connect, "SELECT * FROM `city`");
$result1 = mysqli_query($connect, "SELECT * FROM `city`");

$result2 = mysqli_query($connect, "SELECT * FROM `flight`");
$dates = mysqli_query($connect, "SELECT DATE(departure_time) as date FROM `flight` GROUP BY date ORDER BY date");


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <? include('./templates/head.php') ?>
</head>

<body>
    <? include('./templates/header.php') ?>

    <main>
        <h1> <b>Поиск выгодных авиабилетов</b></h1>
        <form action="checkflight.php" method="post">
            <select name="cityfrom">

                <option>Город отправления</option>
                <?php
                while ($row = mysqli_fetch_array($result)) {
                ?>
                    <option value=<?php echo $row['id'] ?>><?php echo $row['city_name'] ?></option>
                <?php } ?>
            </select>

            <select name="cityto">
                <option>Город прибытия</option>
                <?php
                while ($row = mysqli_fetch_array($result1)) {
                ?>
                    <option value=<?php echo $row['id'] ?>><?php echo $row['city_name'] ?></option>
                <?php } ?>
            </select>

            <input class="auto_style1" type="date" name="date">
            <input class="auto_style" type="submit" value="Полетели!"><br><br>

        </form>
    </main>

    <? include('./templates/footer.php') ?>

</body>

</html>