<?php
function createBase($name){
    $link = new mysqli('localhost','root', 'root');
    $sql = " CREATE DATABASE IF NOT EXISTS  $name";
    mysqli_query($link, $sql);
    return 'base created';
}
function connect($name) {
    $link=mysqli_connect('localhost',"root", "root",$name);
    if ($link == false){
        return ("Ошибка: Невозможно подключиться к MySQL ");
    }
    mysqli_set_charset($link, 'utf8');
    return $link;
} 
function deleteTable($link){
    $sql="DROP TABLE IF EXISTS price";
    mysqli_query($link, $sql);
    return 'table deleted';
}  
function createTable($link, $array, $name){
    $sql="CREATE TABLE IF NOT EXISTS  $name   ( 
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `{$array['name']}`  varchar(100),
        `{$array['price']}` decimal(15,2),
        `{$array['priceopt']}` decimal(15,2),
        `{$array['store1']}`int(10),
        `{$array['store2']}` int(10),
        `{$array['country']}` char(50)
    )";
    mysqli_query($link, $sql);
    return 'Table Created';
}

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
    } else {
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
    $statistic = call_user_func_array('array_merge', $stat);
    return $statistic;
}
function filterStat($products){
    $stat=[];
    $stat['min']=$products[0]['priceopt'];

    foreach($products as $item){
        $stat['store1']+=$item['store1'];
        $stat['store2']+=$item['store2'];
        $stat['price']+=$item['price'];
        $stat['priceopt']+=$item['priceopt'];

        if($stat['min']>$item['priceopt']){
            $stat['min']=$item['priceopt'];
        }
        if($stat['max']<$item['price']){
            $stat['max']=$item['price'];
        }
    }
    $stat['money1']=$stat['price']/count($products);
    $stat['moneyopt']=$stat['priceopt']/count($products);
    return $stat;
}


