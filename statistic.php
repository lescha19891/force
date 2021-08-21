<?php
function statistic(){
	$link = mysqli_connect("localhost", "root", "root","dbprice");
	mysqli_set_charset($link, 'utf8');
	if ($link == false){
		return ("Ошибка: Невозможно подключиться к MySQL ");
	} else {
		$sql = "SELECT SUM(`Наличие на складе 1, шт`) AS store1 FROM price";
		$stat[]=mysqli_fetch_assoc(mysqli_query($link, $sql));
		$sql = "SELECT SUM(`Наличие на складе 2, шт`) AS store2 FROM price";
		$stat[] = mysqli_fetch_assoc(mysqli_query($link, $sql));
		$sql="SELECT AVG(`Стоимость, руб`	) AS money1  FROM price";
		$stat[] = mysqli_fetch_assoc(mysqli_query($link, $sql));
		$sql="SELECT AVG(`Стоимость опт, руб`	) AS moneyopt FROM price";
		$stat[] = mysqli_fetch_assoc(mysqli_query($link, $sql));
		$sql="SELECT MAX(`Стоимость, руб`) AS max  FROM price";
		$stat[] = mysqli_fetch_assoc(mysqli_query($link, $sql));
		$sql="SELECT MIN(`Стоимость опт, руб`) as min  FROM price";
		$stat[] = mysqli_fetch_assoc(mysqli_query($link, $sql));
		$stat = call_user_func_array('array_merge', $stat);
  	}
	mysqli_close ($link);
	return $stat;
}