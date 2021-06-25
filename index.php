<?php
    require '../../vendor/autoload.php'; // путь до библиотеки PhpSpreadsheet

    function products(){
        $link = mysqli_connect("localhost", "root", "root","dbprice");
        mysqli_set_charset($link, 'utf8');
    
        if ($link == false){
            return ("Ошибка: Невозможно подключиться к MySQL ");
        }
        else {
            $sql = "SELECT * FROM price";
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
        }
        mysqli_close ($link); 
        return $products;
    }

    function statistic(){
        $link = mysqli_connect("localhost", "root", "root","dbprice");
        mysqli_set_charset($link, 'utf8');
        if ($link == false){
            return ("Ошибка: Невозможно подключиться к MySQL ");
        }
        else {
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
        
        print_r ($stat);
        mysqli_close ($link);
        return $stat;
    }

    use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
    use PhpOffice\PhpSpreadsheet\IOFactory;
    $file=__DIR__ .'/pricelist.xls';
    $reader = IOFactory::createReaderForFile($file);
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load($file); //читаем файл
    $cells = $spreadsheet->getActiveSheet()->getCellCollection();
    $array=[];    
    for ($row = 1; $row <= $cells->getHighestRow(); $row++){
        $array[$row-1]['name']=($cells->get('A'.$row))?$cells->get('A'.$row)->getValue():'';
        $array[$row-1]['price']=($cells->get('B'.$row))?$cells->get('B'.$row)->getValue():'';
        $array[$row-1]['priceopt']=($cells->get('C'.$row))?$cells->get('C'.$row)->getValue():'';
        $array[$row-1]['store1']=($cells->get('D'.$row))?$cells->get('D'.$row)->getValue():'';
        $array[$row-1]['store2']=($cells->get('E'.$row))?$cells->get('E'.$row)->getValue():'';
        $array[$row-1]['country']=($cells->get('F'.$row))?$cells->get('F'.$row)->getValue():'';
    }
        
    // Создание  БД
    $conn = new mysqli('localhost','root', 'root');
    $sql = " CREATE DATABASE IF NOT EXISTS  dbprice";
    mysqli_query($conn, $sql);    

    // Перезагрузка таблицы
    $conn = mysqli_connect('localhost',"root", "root","dbprice");
    mysqli_set_charset($conn, 'utf8');
    $sql="DROP TABLE IF EXISTS price";
    mysqli_query($conn, $sql);

    $sql="CREATE TABLE  price   ( 
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `{$array[0]['name']}`  varchar(100),
        `{$array[0]['price']}` decimal(15,2),
        `{$array[0]['priceopt']}` decimal(15,2),
        `{$array[0]['store1']}`int(10),
        `{$array[0]['store2']}` int(10),
        `{$array[0]['country']}` char(50)
    )";
    mysqli_query($conn, $sql);

    //Загрузка данных
    $slice = array_slice($array, 1);
    foreach ($slice as $i){
        $sql= "INSERT INTO `price` VALUES 
        (NULL, '{$i['name']}','{$i['price']}','{$i['priceopt']}','{$i['store1']}','{$i['store2']}','{$i['country']}')";
              
        mysqli_query($conn, $sql);
    }     
    mysqli_close ($conn);
    $prod=products();
    $stat=statistic();
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        table{
            border-collapse: collapse;
        }
        table, td, th{
            border: 1px solid ;
            padding: 2px;
        }
        .min{
            background-color:green;
        }
        .max{
            background-color:red;
        }
    </style>
</head>
<body>
<table >
    <tr>
        <th><?=$array[0]['name']?></th>
        <th><?=$array[0]['price']?></th>
        <th><?=$array[0]['priceopt']?></th>
        <th><?=$array[0]['store1']?></th>
        <th><?=$array[0]['store2']?></th>
        <th><?=$array[0]['country']?></th>
        <th>Примечание</th>
    </tr>
    <?php foreach($prod as $item):?>
    <tr>
        <td><?=$item['name']?></td>
        <?php
            if ($item['price']==$stat["max"]){
                echo '<td class = "max">'. $item['price'].'</td>';
            } else {
                echo '<td>'. $item['price']. '</td>';
            }

            if ($item['priceopt']==$stat["min"]){
                echo '<td class = "min">'. $item['priceopt'].'</td>';
            } else {
                echo '<td>'. $item['priceopt']. '</td>';
            }
        ?>
        
        <td><?=$item['store1']?></td>
        <td><?=$item['store2']?></td>
        <td><?=$item['country']?></td>
        <td><?php if($item['store1']<20 or $item['store2']<20) echo "Осталось мало!! Срочно докупите!!!"?></td>
    </tr>
    <?php endforeach;?>
    <tr>
        <td>Средняя стоимость</td>
        <td><?=$stat["money1"]?></td>
        <td><?=$stat["moneyopt"]?></td>
    </tr>
    <tr>
        <td>Всего осталось</td>
        <td></td>
        <td></td>
        <td><?=$stat["store1"]?></td>
        <td><?=$stat["store2"]?></td>
    </tr>

</table>


</body>
</html>