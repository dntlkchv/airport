<?php
require_once 'connect.php';
if (isset($_POST['submit']) && $_POST['submit'] == "Сохранить") {
    $stmt = mysqli_stmt_init($connect);
    $stmt->prepare("UPDATE `flight` SET `from_flight`=?,`to_flight`=?,`departure_time`=?,`arrival_time`=?,`tickets_num`=?,`price`=? WHERE id=?");
    $stmt->bind_param('iissiii', $_POST['from_flight'], $_POST['to_flight'], $_POST['departure_time'], $_POST['arrival_time'], $_POST['tickets_num'], $_POST['price'], $_POST['id']);
    $stmt->execute();
}

if (isset($_POST['submit']) && $_POST['submit'] == "Удалить") {
    $stmt = mysqli_stmt_init($connect);
    $stmt->prepare("DELETE FROM `flight` WHERE id=?");
    $stmt->bind_param('i',  $_POST['id']);
    $stmt->execute();
}

if (isset($_POST['submit']) && $_POST['submit'] == "Добавить") {
    $stmt = mysqli_stmt_init($connect);
    $stmt->prepare("INSERT INTO `flight` SET `from_flight`=?,`to_flight`=?,`departure_time`=?,`arrival_time`=?,`tickets_num`=?,`price`=?");
    $stmt->bind_param('iissii', $_POST['from_flight'], $_POST['to_flight'], $_POST['departure_time'], $_POST['arrival_time'], $_POST['tickets_num'], $_POST['price']);
    $stmt->execute();
}

$flights = mysqli_query($connect, "SELECT *, flight.id as id, port.id AS port_from, p1.name_air AS port_toflight, city.city_name AS city_namefrom, city1.city_name AS city_nameto FROM flight left JOIN port ON flight.from_flight=port.id left JOIN port AS p1 ON flight.to_flight=p1.id left JOIN city ON port.id_city=city.id left JOIN city AS city1 ON p1.id_city=city1.id order by flight.id");
$flights = $flights->fetch_all(MYSQLI_ASSOC);

$airports = mysqli_query($connect, "SELECT *, port.id as id FROM port join city on city.id = port.id_city join country on country.id = id_country");
$airports = $airports->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <? include('./templates/head.php') ?>
</head>

<body>
    <? include('./templates/header.php') ?>

    <h1 class="admin_title">Редактировать рейсы</h1>
    <table>
        <thead>
            <tr>
                <th>Откуда</th>
                <th>Куда</th>
                <th>Время вылета</th>
                <th>Время прилета</th>
                <th>Цена</th>
                <th>К-во человек</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>

            <?php foreach ($flights as $row) : ?>
                <tr>
                    <form action="" id="form-<?= $row['id'] ?>" method="post"></form>
                    <input type="hidden" name="id" value="<?= $row['id'] ?>" form="form-<?= $row['id'] ?>">
                    <td>
                        <select name="from_flight" form="form-<?= $row['id'] ?>">
                            <option disabled hidden>Не выбрана</option>
                            <?php foreach ($airports as $row1) : ?>
                                <option value="<?= $row1['id'] ?>" <?= $row['from_flight'] == $row1['id'] ? "selected" : "" ?>>
                                    <?= $row1['country'] . ', ' . $row1['city_name'] . ', ' . $row1['name_air'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <select name="to_flight" form="form-<?= $row['id'] ?>">
                            <option disabled hidden>Не выбрана</option>
                            <?php foreach ($airports as $row1) : ?>
                                <option value="<?= $row1['id'] ?>" <?= $row['to_flight'] == $row1['id'] ? "selected" : "" ?>>
                                    <?= $row1['country'] . ', ' . $row1['city_name'] . ', ' . $row1['name_air'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <input type="datetime-local" form="form-<?= $row['id'] ?>" name="departure_time" value="<?= $row['departure_time'] ?>">
                    </td>
                    <td>
                        <input type="datetime-local" form="form-<?= $row['id'] ?>" name="arrival_time" value="<?= $row['arrival_time'] ?>">
                    </td>
                    <td>
                        <input type="number" name="price" form="form-<?= $row['id'] ?>" value="<?= $row['price'] ?>"> ₽
                    </td>
                    <td>
                        <input type="number" name="tickets_num" form="form-<?= $row['id'] ?>" value="<?= $row['tickets_num'] ?>"> чел
                    </td>
                    <td><input class="save_style" name="submit" form="form-<?= $row['id'] ?>" type="submit" value="Сохранить"></td>
                    <td><input class="delete_style" name="submit" form="form-<?= $row['id'] ?>" type="submit" value="Удалить"></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <form action="" id="form-new" method="post"></form>
                <td>
                    <select name="from_flight" form="form-new">
                        <option disabled hidden selected>Не выбрано</option>
                        <?php foreach ($airports as $row1) : ?>
                            <option value="<?= $row1['id'] ?>">
                                <?= $row1['country'] . ', ' . $row1['city_name'] . ', ' . $row1['name_air'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <select name="to_flight" form="form-new">
                        <option disabled hidden selected>Не выбрано</option>
                        <?php foreach ($airports as $row1) : ?>
                            <option value="<?= $row1['id'] ?>">
                                <?= $row1['country'] . ', ' . $row1['city_name'] . ', ' . $row1['name_air'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <input type="datetime-local" form="form-new" name="departure_time">
                </td>
                <td>
                    <input type="datetime-local" form="form-new" name="arrival_time">
                </td>
                <td>
                    <input type="number" name="price" form="form-new"> ₽
                </td>
                <td>
                    <input type="number" name="tickets_num" form="form-new"> чел
                </td>
                <td colspan="2"><input class="add_style" name="submit" form="form-new" type="submit" value="Добавить"></td>
            </tr>
        </tbody>
    </table>
    
   <? include('./templates/footer.php') ?>

</body>