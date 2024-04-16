<?php
require_once 'connect.php';
require_once 'auth.php';

$user = getCurrentUser();

$sort_filed = $_POST['sort_filed'] ?? 'price';
$sort_direction = isset($_POST['sort_direction']) && $_POST['sort_direction'] == "↑" ? "ASC" : "DESC";

if(isset($_POST['submit'])){
	if($_POST['submit'] == 'В корзину')
		mysqli_query($connect, "INSERT IGNORE  INTO cart (id_user, id_flight) VALUES ({$user['id']}, {$_POST['flight_id']})");

	if($_POST['submit'] == 'В корзине')
		mysqli_query($connect, "DELETE FROM cart WHERE id_user={$user['id']} AND id_flight={$_POST['flight_id']}");
}

$flight = mysqli_query($connect, "SELECT *, flight.id AS flight_id, port.name_air AS port_from, p1.name_air AS port_toflight, city.city_name AS city_namefrom, city1.city_name AS city_nameto, EXISTS(SELECT * from cart where id_user=".($user['id'] ?? 0) ." and id_flight=flight.id) as in_cart FROM flight RIGHT JOIN port ON flight.from_flight=port.id RIGHT JOIN port AS p1 ON flight.to_flight=p1.id RIGHT JOIN city ON port.id_city=city.id RIGHT JOIN city AS city1 ON p1.id_city=city1.id WHERE flight.from_flight IN (SELECT id FROM port WHERE id_city={$_POST['cityfrom']}) AND flight.to_flight IN (SELECT id FROM port WHERE id_city={$_POST['cityto']}) AND DATE(flight.departure_time)='{$_POST['date']}' order by $sort_filed $sort_direction");

?>

<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<link rel="stylesheet" href="css/main.css">

</head>

<body>
	<? include('./templates/header.php') ?>
	<main>
		<table>
			<thead>
				<tr>
					<th>Откуда</th>
					<th>Куда</th>
					<th>Время вылета</th>
					<th>Время прилета</th>
					<th>
						<div class="sort_th">
							Цена
							<form action="" method="post">
								<input type="hidden" name="sort_field" value="price">
								<input type="hidden" name="cityfrom" value="<?= $_POST['cityfrom'] ?>">
								<input type="hidden" name="cityto" value="<?= $_POST['cityto'] ?>">
								<input type="hidden" name="date" value="<?= $_POST['date'] ?>">

								<input type="submit" name="sort_direction" value="↑">
								<input type="submit" name="sort_direction" value="↓">
							</form>
						</div>
					</th>
					<th>Наличие билетов</th>
					<? if($user): ?>
						<th>Корзину</th>
					<? endif; ?>
				</tr>
			</thead>
			<tbody>


				<?php while ($row = mysqli_fetch_array($flight)) { ?>
					<tr>

							<td><?php echo $row['port_from'] ?>, <?php echo $row['city_namefrom'] ?></td>
							<td><?php echo $row['port_toflight'] ?>, <?php echo $row['city_nameto'] ?></td>
							<td><?= date_format(date_create($row['departure_time']), "d.m.Y H:i") ?></td>
							<td><?= date_format(date_create($row['arrival_time']), "d.m.Y H:i") ?></td>
							<td><?php echo $row['price'] ?> Рублей</td>
							<td>
								<form action="booking.php" method="get" id="booking">
									<input type="hidden" name="flight_id" value="<?= $row['flight_id'] ?>">
									<input class="auto_style" type="submit" value="Забронировать">
								</form>
							</td>
						<? if($user): ?>
							<td>
								<form action="" method="post">
									<input type="hidden" name="flight_id" value="<?= $row['flight_id'] ?>">
									<input type="hidden" name="cityfrom" value="<?= $_POST['cityfrom'] ?>">
									<input type="hidden" name="cityto" value="<?= $_POST['cityto'] ?>">
									<input type="hidden" name="date" value="<?= $_POST['date'] ?>">
									<input class="auto_style" type="submit" name="submit" value="<?= $row['in_cart'] ? "В корзине" : "В корзину"  ?>">
								</form>
							</td>
						<? endif; ?>
					</tr>
				<?php } ?>
			</tbody>
		</table>

	</main>

	<? include('./templates/footer.php') ?>

</body>