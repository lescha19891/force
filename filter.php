<?php 
require_once 'products.php';
require_once 'statistic.php';
$prod=products();
$stat=statistic();
?>

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