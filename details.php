<?php
 $name = $_POST['fname'];
 $rno =  $_POST['id'];
$con = mysqli_connect("localhost","root","root","school");
mysqli_set_charset($con, 'utf8');
if ($con == false){
    return ("Ошибка: Невозможно подключиться к MySQL ");
}
$sql = "SELECT * from students where rno = {$rno}";
$result = mysqli_query($con,$sql);
$r=json_encode(mysqli_fetch_array ($result), JSON_UNESCAPED_UNICODE);
mysqli_close ($con);
echo $r;
