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

