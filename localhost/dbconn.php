<?php
 
    $server = "localhost"; 
    $username = "root"; 
    $password = ""; 
    $database = "testtask"; 

    $mysqli = new mysqli($server, $username, $password, $database);

    if (mysqli_connect_errno()) { 
        echo "<p><strong>Ошибка подключения к БД</strong>. Описание ошибки: ".mysqli_connect_error()."</p>";
        exit(); 
    }

    $mysqli->set_charset('utf8');

    $address_site = "http://localhost";
?>