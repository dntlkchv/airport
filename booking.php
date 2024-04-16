<?php
require_once 'connect.php';
require_once 'auth.php';

$user = getCurrentUser();

if (!$user) {
    setcookie('booking_flight_id', $_REQUEST['flight_id']);
    header("Location: switch_auth.php");
    exit();
}

if (isset($_POST['submit']) && $_POST['submit'] == "Забронировать") {
    $new_clients = [];
    foreach ($_POST['new_clients'] ?? [] as $client_data) {
        $connect->query("INSERT INTO client(name, surname, patronymic, sex, birthday, passport, id_user) VALUES ('{$client_data['name']}', '{$client_data['surname']}', '{$client_data['patronymic']}', {$client_data['sex']}, '{$client_data['birthday']}','{$client_data['passport']}', {$user['id']})");
    
        $new_clients[mysqli_insert_id($connect)] = $client_data;
    }

    
    foreach ([...$_GET['selected_clients'], ...array_keys($new_clients)] as $client_id) {
        $connect->query("INSERT INTO `ticket`(`ticket_date`,  `id_flight`) VALUES (NOW(),{$_POST['flight_id']})");
        $connect->query("INSERT INTO `reserve`(`id_client`, `id_ticket`) VALUES ({$client_id},".mysqli_insert_id($connect).")");
    }

    mysqli_query($connect, "DELETE FROM cart WHERE id_user={$user['id']} AND id_flight={$_POST['flight_id']}");

    header("Location: cabinet.php");
    exit();
}

$flight = $connect->query("SELECT *, flight.id AS flight_id, port.name_air AS port_from, p1.name_air AS port_toflight, city.city_name AS city_namefrom, city1.city_name AS city_nameto FROM flight RIGHT JOIN port ON flight.from_flight=port.id RIGHT JOIN port AS p1 ON flight.to_flight=p1.id RIGHT JOIN city ON port.id_city=city.id RIGHT JOIN city AS city1 ON p1.id_city=city1.id where flight.id={$_REQUEST['flight_id']}")->fetch_assoc();
$сlients = [];
$userClients = $connect->query("SELECT * from client where id_user={$user['id']}")->fetch_all(MYSQLI_ASSOC);

if (isset($_GET['selected_clients'])) {
    $сlients = $connect->query("SELECT * from client where id_user={$user['id']} and id in (" . implode(",", $_GET['selected_clients']) . ")")->fetch_all(MYSQLI_ASSOC);
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
    <form method="post" id="booking">
        <input type="hidden" name="flight_id" value="<?= $_REQUEST['flight_id'] ?>">

        <h1>Информация о рейсе</h1>
        <table>
            <thead>
                <tr>
                    <th>Откуда</th>
                    <th>Куда</th>
                    <th>Время вылета</th>
                    <th>Время прилета</th>
                    <th>Цена</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $flight['port_from'] ?>, <?= $flight['city_namefrom'] ?></td>
                    <td><?= $flight['port_toflight'] ?>, <?= $flight['city_nameto'] ?></td>
                    <td><?= date_format(date_create($flight['departure_time']), "d.m.Y H:i") ?></td>
                    <td><?= date_format(date_create($flight['arrival_time']), "d.m.Y H:i") ?></td>
                    <td><?= $flight['price'] ?> Рублей</td>
                </tr>
            </tbody>
        </table>

        <h1>Информация о пассажирах</h1>
        <table>
            <thead>
                <tr>
                    <th>Фамилия</th>
                    <th>Имя</th>
                    <th>Отчество</th>
                    <th>Пол</th>
                    <th>День рождения</th>
                    <th>Паспорт</th>
                </tr>
            </thead>
            <tbody>
                <? foreach ($сlients as $client) : ?>
                    <tr>
                        <td>
                            <input type="hidden" name="clients[][id]" value="<?= $client['id'] ?>">
                            <?= $client['surname'] ?>
                        </td>
                        <td><?= $client['name'] ?></td>
                        <td><?= $client['patronymic'] ?></td>
                        <td><?= $client['sex'] ? "Мужской" : "Женский" ?></td>
                        <td><?= date_format(date_create($client['birthday']), "d.m.Y") ?></td>
                        <td><?= $client['passport'] ?></td>
                    </tr>
                <? endforeach ?>

                <? for ($i = 0; $i < ($_GET['add_count'] ?? 0); $i++) : ?>
                    <tr>
                        <td>
                            <input class="auto_style1" type="text" name="new_clients[<?= $i ?>][surname]" placeholder="Иванов">
                        </td>
                        <td><input class="auto_style1" type="text" name="new_clients[<?= $i ?>][name]" placeholder="Иван"></td>
                        <td><input class="auto_style1" type="text" name="new_clients[<?= $i ?>][patronymic]" placeholder="Иванович"></td>
                        <td><select class="auto_style1" name="new_clients[<?= $i ?>][sex]">
                                <option value="0">Мужской</option>
                                <option value="1">Женский</option>
                            </select></td>
                        <td><input class="auto_style1" type="date" name="new_clients[<?= $i ?>][birthday]" placeholder="01.01.2000"></td>
                        <td><input class="auto_style1" type="text" name="new_clients[<?= $i ?>][passport]" placeholder="0000 000000"></td>
                    </tr>
                <? endfor ?>

            </tbody>
        </table>
        </form>

        <div class="right-align">
            <div class="v-center">
                <form method="get" class="v-center">

                <select name="selected_clients[]" multiple>
                    <option value="" disabled>Не выбрано</option>
                    <? foreach ($userClients as $client) : ?>
                        <option value="<?= $client['id'] ?>" <?= in_array($client['id'], $_GET['selected_clients'] ?? []) ? "selected" : '' ?>><?= $client['surname'] ?> <?= $client['name'] ?> <?= $client['patronymic'] ?></option>
                    <? endforeach ?>
                </select>
                    <input type="hidden" name="flight_id" value="<?= $_REQUEST['flight_id'] ?>">
                    <input type="hidden" name="add_count" value="<?= ($_GET['add_count'] ?? 0) ?>">
                    <input class="save_style" type="submit" name="submit" value="Добавить выбранных">
                </form>

                <form method="get">
                    <? foreach ($userClients as $client) : ?>
                        <input type="hidden" name="selected_clients[]" value="<?= $client['id'] ?>">
                    <? endforeach ?>

                    <input type="hidden" name="flight_id" value="<?= $_REQUEST['flight_id'] ?>">
                    <input type="hidden" name="add_count" value="<?= ($_GET['add_count'] ?? 0) + 1 ?>">
                    <input class="save_style" type="submit" name="submit" value="Добавить пассажира">
                </form>
            </div>
        </div>
        <input class="auto_style" type="submit" name="submit" value="Забронировать" form="booking">

    </main>

    <? include('./templates/footer.php') ?>

</body>

</html>