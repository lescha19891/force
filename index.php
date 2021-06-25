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
        #u0_div {
  border-width:0px;
  position:absolute;
  left:0px;
  top:0px;
  width:258px;
  height:22px;
  background:inherit;
  background-color:rgba(255, 255, 255, 0);
  border:none;
  border-radius:0px;
  -moz-box-shadow:none;
  -webkit-box-shadow:none;
  box-shadow:none;
  font-family:'Arial Полужирный', 'Arial';
  font-weight:700;
  font-style:normal;
}
#u0 {
  border-width:0px;
  position:absolute;
  left:122px;
  top:90px;
  width:258px;
  height:22px;
  font-family:'Arial Полужирный', 'Arial';
  font-weight:700;
  font-style:normal;
}
#u0_text {
  border-width:0px;
  position:absolute;
  left:0px;
  top:0px;
  width:258px;
  white-space:nowrap;
}
#u1 {
  border-width:0px;
  position:absolute;
  left:390px;
  top:82px;
  width:161px;
  height:38px;
}
#u1_input {
  position:absolute;
  left:0px;
  top:0px;
  width:161px;
  height:38px;
  font-family:'Arial Обычный', 'Arial';
  font-weight:400;
  font-style:normal;
  font-size:16px;
  text-decoration:none;
  color:#000000;
}
#u1_input:disabled {
  color:grayText;
}
#u2_div {
  border-width:0px;
  position:absolute;
  left:0px;
  top:0px;
  width:21px;
  height:22px;
  background:inherit;
  background-color:rgba(255, 255, 255, 0);
  border:none;
  border-radius:0px;
  -moz-box-shadow:none;
  -webkit-box-shadow:none;
  box-shadow:none;
  font-family:'Arial Полужирный', 'Arial';
  font-weight:700;
  font-style:normal;
}
#u2 {
  border-width:0px;
  position:absolute;
  left:561px;
  top:90px;
  width:21px;
  height:22px;
  font-family:'Arial Полужирный', 'Arial';
  font-weight:700;
  font-style:normal;
}
#u2_text {
  border-width:0px;
  position:absolute;
  left:0px;
  top:0px;
  width:21px;
  white-space:nowrap;
}
#u3 {
  border-width:0px;
  position:absolute;
  left:592px;
  top:82px;
  width:69px;
  height:38px;
}
#u3_input {
  position:absolute;
  left:0px;
  top:0px;
  width:69px;
  height:38px;
  font-family:'Arial Обычный', 'Arial';
  font-weight:400;
  font-style:normal;
  font-size:16px;
  text-decoration:none;
  color:#000000;
  text-align:left;
}
#u4_div {
  border-width:0px;
  position:absolute;
  left:0px;
  top:0px;
  width:23px;
  height:22px;
  background:inherit;
  background-color:rgba(255, 255, 255, 0);
  border:none;
  border-radius:0px;
  -moz-box-shadow:none;
  -webkit-box-shadow:none;
  box-shadow:none;
  font-family:'Arial Полужирный', 'Arial';
  font-weight:700;
  font-style:normal;
}
#u4 {
  border-width:0px;
  position:absolute;
  left:671px;
  top:90px;
  width:23px;
  height:22px;
  font-family:'Arial Полужирный', 'Arial';
  font-weight:700;
  font-style:normal;
}
#u4_text {
  border-width:0px;
  position:absolute;
  left:0px;
  top:0px;
  width:23px;
  white-space:nowrap;
}
#u5 {
  border-width:0px;
  position:absolute;
  left:704px;
  top:82px;
  width:69px;
  height:38px;
}
#u5_input {
  position:absolute;
  left:0px;
  top:0px;
  width:69px;
  height:38px;
  font-family:'Arial Обычный', 'Arial';
  font-weight:400;
  font-style:normal;
  font-size:16px;
  text-decoration:none;
  color:#000000;
  text-align:left;
}
#u6_div {
  border-width:0px;
  position:absolute;
  left:0px;
  top:0px;
  width:176px;
  height:22px;
  background:inherit;
  background-color:rgba(255, 255, 255, 0);
  border:none;
  border-radius:0px;
  -moz-box-shadow:none;
  -webkit-box-shadow:none;
  box-shadow:none;
  font-family:'Arial Полужирный', 'Arial';
  font-weight:700;
  font-style:normal;
}
#u6 {
  border-width:0px;
  position:absolute;
  left:783px;
  top:90px;
  width:176px;
  height:22px;
  font-family:'Arial Полужирный', 'Arial';
  font-weight:700;
  font-style:normal;
}
#u6_text {
  border-width:0px;
  position:absolute;
  left:0px;
  top:0px;
  width:176px;
  white-space:nowrap;
}
#u7 {
  border-width:0px;
  position:absolute;
  left:969px;
  top:82px;
  width:88px;
  height:38px;
}
#u7_input {
  position:absolute;
  left:0px;
  top:0px;
  width:88px;
  height:38px;
  font-family:'Arial Обычный', 'Arial';
  font-weight:400;
  font-style:normal;
  font-size:16px;
  text-decoration:none;
  color:#000000;
}
#u7_input:disabled {
  color:grayText;
}
#u8_div {
  border-width:0px;
  position:absolute;
  left:0px;
  top:0px;
  width:50px;
  height:22px;
  background:inherit;
  background-color:rgba(255, 255, 255, 0);
  border:none;
  border-radius:0px;
  -moz-box-shadow:none;
  -webkit-box-shadow:none;
  box-shadow:none;
  font-family:'Arial Полужирный', 'Arial';
  font-weight:700;
  font-style:normal;
}
#u8 {
  border-width:0px;
  position:absolute;
  left:1128px;
  top:90px;
  width:50px;
  height:22px;
  font-family:'Arial Полужирный', 'Arial';
  font-weight:700;
  font-style:normal;
}
#u8_text {
  border-width:0px;
  position:absolute;
  left:0px;
  top:0px;
  width:50px;
  white-space:nowrap;
}
#u9 {
  border-width:0px;
  position:absolute;
  left:1075px;
  top:82px;
  width:45px;
  height:38px;
}
#u9_input {
  position:absolute;
  left:0px;
  top:0px;
  width:45px;
  height:38px;
  font-family:'Arial Обычный', 'Arial';
  font-weight:400;
  font-style:normal;
  font-size:16px;
  text-decoration:none;
  color:#000000;
  text-align:left;
}
#u10_div {
  border-width:0px;
  position:absolute;
  left:0px;
  top:0px;
  width:140px;
  height:40px;
  background:inherit;
  background-color:rgba(255, 255, 255, 1);
  box-sizing:border-box;
  border-width:1px;
  border-style:solid;
  border-color:rgba(121, 121, 121, 1);
  border-radius:5px;
  -moz-box-shadow:none;
  -webkit-box-shadow:none;
  box-shadow:none;
}
#u10 {
  border-width:0px;
  position:absolute;
  left:1188px;
  top:80px;
  width:140px;
  height:40px;
}
#u10_text {
  border-width:0px;
  position:absolute;
  left:2px;
  top:12px;
  width:136px;
  word-wrap:break-word;
}
#u11_div {
  border-width:0px;
  position:absolute;
  left:0px;
  top:0px;
  width:1206px;
  height:358px;
  background:inherit;
  background-color:rgba(255, 255, 255, 1);
  box-sizing:border-box;
  border-width:1px;
  border-style:solid;
  border-color:rgba(121, 121, 121, 1);
  border-radius:0px;
  -moz-box-shadow:none;
  -webkit-box-shadow:none;
  box-shadow:none;
  font-size:20px;
}
#u11 {
  border-width:0px;
  position:absolute;
  left:122px;
  top:159px;
  width:1206px;
  height:358px;
  font-size:20px;
}
.ax_default {
  font-family:'Arial Обычный', 'Arial';
  font-weight:400;
  font-style:normal;
  font-size:13px;
  color:#333333;
  text-align:center;
  line-height:normal;
}
.heading_3 {
  font-family:'Arial Обычный', 'Arial';
  font-weight:bold;
  font-style:normal;
  font-size:18px;
  text-align:left;
}
.text_field {
  color:#000000;
  text-align:left;
}
.droplist {
  color:#000000;
  text-align:left;
}
.box_1 {
}
.button {
}
html,body,div,span,
applet,object,iframe,
h1,h2,h3,h4,h5,h6,p,blockquote,pre,
a,abbr,acronym,address,big,cite,code,
del,dfn,em,font,img,ins,kbd,q,s,samp,
small,strike,strong,sub,sup,tt,var,
dd,dl,dt,li,ol,ul,
fieldset,form,label,legend,
table,caption,tbody,tfoot,thead,tr,th,td {
	margin: 0;
	
}
select {
    -webkit-writing-mode: horizontal-tb !important;
    text-rendering: auto;
    color: -internal-light-dark(black, white);
    letter-spacing: normal;
    word-spacing: normal;
    text-transform: none;
    text-indent: 0px;
    text-shadow: none;
    display: inline-block;
    text-align: start;
    appearance: auto;
    box-sizing: border-box;
    align-items: center;
    white-space: pre;
    -webkit-rtl-ordering: logical;
    background-color: -internal-light-dark(rgb(255, 255, 255), rgb(59, 59, 59));
    cursor: default;
    margin: 0em;
    font: 400 13.3333px Arial;
    border-radius: 0px;
    border-width: 1px;
    border-style: solid;
    border-color: -internal-light-dark(rgb(118, 118, 118), rgb(133, 133, 133));
    border-image: initial;
}

    </style>
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
          <option value="Розничная цена">Розничная цена</option>
          <option value="Оптовая цена">Оптовая цена</option>
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
          <option value="Более">Более</option>
          <option value="Менее">Менее</option>
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
      <div id="u10" class="ax_default button">
        <div id="u10_div" class=""></div>
        <div id="u10_text" class="text ">
          <p><span>ПОКАЗАТЬ ТОВАРЫ</span></p>
        </div>
      </div>

        
    <div id="u11" class="ax_default box_1">

        <table id="u11_div" >
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
        
    </div>

</body>
</html>
