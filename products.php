<?php
function products($link){
        if (isset($_POST['opt'])){
        $opt = (int)(htmlentities($_POST['opt']));
        $min = (int)(htmlentities($_POST['min']));
        $max = (int)(htmlentities($_POST['max']));
        $more = (int)(htmlentities($_POST['more']));
        $count = (int)(htmlentities($_POST['count']));
    } 
    if ($opt==1) {
        if($more==1){
            $sql = "SELECT * FROM price WHERE
            (`Стоимость, руб` BETWEEN {$min} AND {$max}) AND
            (`Наличие на складе 1, шт`>{$count} OR `Наличие на складе 2, шт`>{$count}) ";
        } else { 
            $sql = "SELECT * FROM price WHERE
            (`Стоимость, руб` BETWEEN {$min} AND {$max}) AND
            (`Наличие на складе 1, шт`<{$count} OR `Наличие на складе 2, шт`<{$count}) ";
        }
    } elseif($opt==2) {
        if($more==1){
            $sql = "SELECT * FROM price WHERE
            (`Стоимость опт, руб` BETWEEN {$min} AND {$max}) AND
            (`Наличие на складе 1, шт`>{$count} OR `Наличие на складе 2, шт`>{$count}) ";
        } else { 
            $sql = "SELECT * FROM price WHERE
            (`Стоимость опт, руб` BETWEEN {$min} AND {$max}) AND
            (`Наличие на складе 1, шт`<{$count} OR `Наличие на складе 2, шт`<{$count}) ";
        }
    } else{
        $sql = "SELECT * FROM price";
    }

    $result = mysqli_query($link, $sql);
    $products=[];
    while ($row = mysqli_fetch_array ($result)) {
        $products[] =[
            "id"=>$row ['id'] ,
            "name"=>$row['Наименование товара'],
            "price"=>$row['Стоимость, руб'],
            "priceopt"=>$row [ 'Стоимость опт, руб'],
            "store1"=>$row['Наличие на складе 1, шт'],
            "store2"=>$row['Наличие на складе 2, шт'],
            "country"=>$row['Страна производства'],
        ];
    }
    return $products;
}


function statistic($link){
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
	return $stat;
}