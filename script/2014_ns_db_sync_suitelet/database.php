<?php
    $host = "1.2.3.4.";
    $user = "abc";
    $pass = "xyz";
    $db = "wms";
    
    $link = mysql_connect($host, $user, $pass) or die("Database server connection failed!");
    
    $db_selected = mysql_select_db($db, $link) or die("There is no database");
?>
