<?php 
require_once 'products.php';
require_once 'statistic.php';
$prod=products();
$stat=statistic();
$__POST=['opt'=>1,
'min'=>1000,
'max'=>5000,
'more'=>1,
'count'=>20];

$link = mysqli_connect("localhost", "root", "root","dbprice");
mysqli_set_charset($link, 'utf8');
if ($link == false){
    return ("Ошибка: Невозможно подключиться к MySQL ");
}

if (isset($__POST['opt'])){
    $opt = htmlentities($__POST['opt']);
    $min = htmlentities($__POST['min']);
    $max = htmlentities($__POST['max']);
    $more = htmlentities($__POST['more']);
    $count = htmlentities($__POST['count']);
} 
if ($opt==1) {
    if($more==1){
        $sql = "SELECT * FROM price WHERE
        (`Стоимость, руб` BETWEEN {$min} AND {$max}) AND
        (`Наличие на складе 1, шт`>{$count} OR 'Наличие на складе 2, шт'>{$count}) ";
    } else { 
        $sql = "SELECT * FROM price WHERE
        (`Стоимость, руб` BETWEEN {$min} AND {$max}) AND
        (`Наличие на складе 1, шт`<{$count} OR 'Наличие на складе 2, шт'<{$count}) ";
    }
} elseif($opt==2) {
    if($more==1){
        $sql = "SELECT * FROM price WHERE
        ('Стоимость опт, руб' BETWEEN {$min} AND {$max}) AND
        (`Наличие на складе 1, шт`>{$count} OR 'Наличие на складе 2, шт'>{$count}) ";
    } else { 
        $sql = "SELECT * FROM price WHERE
        ('Стоимость опт, руб' BETWEEN {$min} AND {$max}) AND
        (`Наличие на складе 1, шт`<{$count} OR 'Наличие на складе 2, шт'<{$count}) ";
    }
} 
$result = mysqli_query($link, $sql);
$prod=[];
while ($row = mysqli_fetch_array ($result)) {
    $prod[] =[
        "id"=>$row ['id'] ,
        "name"=>$row['Наименование товара'],
        "price"=>$row['Стоимость, руб'],
        "priceopt"=>$row [ 'Стоимость опт, руб'],
        "store1"=>$row['Наличие на складе 1, шт'],
        "store2"=>$row['Наличие на складе 2, шт'],
        "country"=>$row['Страна производства'],
    ];
}
mysqli_close ($link); 
?>

</div>
      <table id="u11" class="ax_default box_1" >
          <tr>
              <th>Наименование товара</th>
              <th>Стоимость, руб</th>
              <th>Стоимость опт, руб</th>
              <th>Наличие на складе 1, шт</th>
              <th>Наличие на складе 2, шт</th>
              <th>Страна производства</th>
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
              <td><b>Средняя стоимость</b></td>
              <td><?=$stat["money1"]?></td>
              <td><?=$stat["moneyopt"]?></td>
          </tr>
          <tr >
              <td><b>Всего осталось</b></td>
              <td></td>
              <td></td>
              <td><?=$stat["store1"]?></td>
              <td><?=$stat["store2"]?></td>
          </tr>

      </table>