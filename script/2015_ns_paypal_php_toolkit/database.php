<?php
    $host = "192.249.78.249";
    $user = "root";
    $pass = "test1234";
    $db_name = "paypal_transactions";
    
    $link = mysql_connect($host, $user, $pass) or die("Database server connection failed.");
    
    $db_selected = mysql_select_db($db_name, $link) or die("There is no database.");
?>
