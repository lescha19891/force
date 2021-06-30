<?php
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
require '../../vendor/autoload.php'; // путь до библиотеки PhpSpreadsheet
require_once 'products.php';
require_once 'statistic.php';

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
<link rel="stylesheet" href="style.css" type="text/css"/>
<script src="http://code.jquery.com/jquery-latest.js"></script> 
  <script src="ajax.js"></script>

</head>
<body>
<div id="base">
    <!-- Unnamed (Rectangle) -->
    <div id="u0" class="ax_default heading_3">
      <div id="u0_div" ></div>
      <div id="u0_text" class="text ">
        <p><span>Показать товары, у которых </span></p>
      </div>
    </div>

    <!-- Unnamed (Droplist) -->
    <div id="u1" class="ax_default droplist">
      <select id="u1_input">
        <option value="1">Розничная цена</option>
        <option value="2">Оптовая цена</option>
      </select>
    </div>

    <!-- Unnamed (Rectangle) -->
    <div id="u2" class="ax_default heading_3">
      <div id="u2_div" ></div>
      <div id="u2_text" class="text ">
        <p><span>от</span></p>
      </div>
    </div>

    <!-- Unnamed (Text Field) -->
    <div id="u3" class="ax_default text_field">
      <input id="u3_input" type="text" value="1000">
    </div>

    <!-- Unnamed (Rectangle) -->
    <div id="u4" class="ax_default heading_3">
      <div id="u4_div" ></div>
      <div id="u4_text" class="text ">
        <p><span>до</span></p>
      </div>
    </div>

    <!-- Unnamed (Text Field) -->
    <div id="u5" class="ax_default text_field">
      <input id="u5_input" type="text" value="3000">
    </div>

    <!-- Unnamed (Rectangle) -->
    <div id="u6" class="ax_default heading_3">
      <div id="u6_div" class=""></div>
      <div id="u6_text" class="text ">
        <p><span>рублей и на складе </span></p>
      </div>
    </div>

    <!-- Unnamed (Droplist) -->
    <div id="u7" class="ax_default droplist">
      <select id="u7_input">
        <option value="1">Более</option>
        <option value="2">Менее</option>
      </select>
    </div>

    <!-- Unnamed (Rectangle) -->
    <div id="u8" class="ax_default heading_3">
      <div id="u8_div" class=""></div>
      <div id="u8_text" class="text ">
        <p><span>штук.</span></p>
      </div>
    </div>

    <!-- Unnamed (Text Field) -->
    <div id="u9" class="ax_default text_field">
      <input id="u9_input" type="text" value="20">
    </div>

    <!-- Unnamed (Rectangle) -->
    <div id="u10" class="ax_default button" onClick = "getfilter()">
      <div id="u10_div" class=""></div>
      <div id="u10_text" class="text ">
        <p><span>ПОКАЗАТЬ ТОВАРЫ</span></p>
      </div>
    </div>
      <table id="u11" class="ax_default box_1" >
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
      
</body>
</html>
