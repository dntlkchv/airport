<?php
require_once 'connect.php';
require_once 'auth.php';

$user = getCurrentUser();

if (!$user) {
    setcookie('booking_flight_id', $_POST['flight_id']);
    header("Location: switch_auth.php");
    exit();
}

$client_filter = "";

if(isset($_POST['submit'])){
    $stmt = mysqli_stmt_init($connect);
    $stmt->prepare("UPDATE `user` SET `email`=?,`telephone`=? WHERE id=?");
    $stmt->bind_param('ssi', $_POST['email'], $_POST['phone'], $user['id']);
    $stmt->execute();

    header("Location: cabinet.php");
    exit();
}

if(isset($_GET['show_by_client_id'])){
    $client_filter = "AND client.id = {$_GET['show_by_client_id']}";
}
if(isset($_GET['show_by_clients']) && count($_GET['filter_client'])){
    $client_filter = "AND client.id IN (".implode(",", $_GET['filter_client']).")";
}


$flights = $connect->query("SELECT flight.*, CONCAT(client.name, ' ', client.surname, ' ', client.patronymic) AS passanger_name, SUM(ticket.id), flight.id AS flight_id, port.name_air AS port_from, p1.name_air AS port_toflight, city.city_name AS city_namefrom, city1.city_name AS city_nameto FROM flight left join ticket on flight.id = ticket.id_flight left join reserve on reserve.id_ticket = ticket.id left join client on client.id = reserve.id_client RIGHT JOIN port ON flight.from_flight=port.id RIGHT JOIN port AS p1 ON flight.to_flight=p1.id RIGHT JOIN city ON port.id_city=city.id RIGHT JOIN city AS city1 ON p1.id_city=city1.id where client.id_user={$user['id']} $client_filter group by flight.id, client.id")->fetch_all(MYSQLI_ASSOC);
$сlients = $connect->query("SELECT * from client where id_user={$user['id']}")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <? include('./templates/head.php') ?>
</head>

<body>
    <? include('./templates/header.php') ?>
    <main>
    <h1>Информация о вас</h1>

    <form action="" method="POST">
        <table>
            <tbody>
                <tr>
                    <td>Почта</td>
                    <td>
                        <? if(!isset($_GET['mode']) || $_GET['mode'] != 'edit'): ?>
                            <?= $user['email'] ?>
                        <? else: ?>
                            <input class="auto_style1" type="email" name="email" value="<?= $user['email'] ?>">
                        <? endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>Телефон</td>
                    <td>
                        <? if(!isset($_GET['mode']) || $_GET['mode'] != 'edit'): ?>
                            <?= $user['telephone'] ?>
                        <? else: ?>
                            <input class="auto_style1" type="tel" name="phone" value="<?= $user['telephone'] ?>">
                        <? endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <? if(isset($_GET['mode']) && $_GET['mode'] == 'edit'): ?>
            <input class="save_style"  type="submit" name="submit" value="Сохранить">
        <? endif; ?>
    </form>

    <? if(!isset($_GET['mode']) || $_GET['mode'] != 'edit'): ?>
        <form action="" method="GET">
            <input type="hidden" name="mode" value="edit">
            <input class="save_style"  type="submit" value="Редактировать">
        </form>
    <? endif; ?>
 
    <h1>Информация о пассажирах</h1>
    <table>
        <thead>
            <tr>
                <th></th>
                <th>Фамилия</th>
                <th>Имя</th>
                <th>Отчество</th>
                <th>Пол</th>
                <th>День рождения</th>
                <th>Паспорт</th>
                <th>Показать рейсы</th>
            </tr>
        </thead>
        <tbody>
            <? foreach ($сlients as $client) : ?>
                <tr>
                    <td>
                        <input type="checkbox" name="filter_client[]" id="checkbox-<?= $client['id'] ?>" value="<?= $client['id'] ?>" form="filter-form" <?= in_array($client['id'], $_GET['filter_client'] ?? []) ? "checked" : "" ?>>
                    </td>
                    <td>
                        <input type="hidden" name="clients[][id]" value="<?= $client['id'] ?>">
                        <?= $client['surname'] ?>
                    </td>
                    <td><?= $client['name'] ?></td>
                    <td><?= $client['patronymic'] ?></td>
                    <td><?= $client['sex'] ? "Мужской" : "Женский" ?></td>
                    <td><?= date_format(date_create($client['birthday']), "d.m.Y") ?></td>
                    <td><?= $client['passport'] ?></td>
                    <td>
                        <form action="" method="GET">
                            <input type="hidden" name="show_by_client_id" value="<?= $client['id'] ?>">
                            <? if(!isset($_GET['show_by_client_id']) || $_GET['show_by_client_id'] != $client['id']): ?>
                                <input class="save_style" type="submit" name="show_by_client" value="Показать рейсы">
                            <? else: ?>
                                <input type="reset" name="show_by_client" value="Отключить фильтр" onclick="window.location.replace('?')">
                            <? endif; ?>
                        </form>
                    </td>
                </tr>
            <? endforeach ?>
        </tbody>
    </table>

    <form action="" method="GET" id="filter-form">
        <input class="save_style" type="submit" name="show_by_clients" value="Показать рейсы выбранных пассажиров">
    </form>

    <h1>Мои рейсы</h1>
    

    <table>
		<thead>
			<tr>
				<th>Откуда</th>
				<th>Куда</th>
				<th>Время вылета</th>
				<th>Время прилета</th>
				<th>Пассажир</th>
				<th>Цена</th>
			</tr>
		</thead>
		<tbody>
            <?php foreach ($flights as $flight) : ?>
                <tr>
                    <td><?php echo $flight['port_from'] ?>, <?php echo $flight['city_namefrom'] ?></td>
                    <td><?php echo $flight['port_toflight'] ?>, <?php echo $flight['city_nameto'] ?></td>
                    <td><?= date_format(date_create($flight['departure_time']), "d.m.Y H:i") ?></td>
                    <td><?= date_format(date_create($flight['arrival_time']), "d.m.Y H:i") ?></td>
                    <td><?php echo "{$flight['passanger_name']}" ?></td>
                    <td><?php echo $flight['price'] ?> Рублей</td>
                </tr>
            <?php endforeach ?>
            <tr>
                <td colspan="4"></td>
                <td>Сумма:</td>
                <td><?= array_reduce($flights, fn($prev, $curr) => $prev + $curr['price'], 0) ?> Рублей</td>
            </tr>
		</tbody>
	</table>
    
    </main>
    
   <? include('./templates/footer.php') ?>

</body>

</html>