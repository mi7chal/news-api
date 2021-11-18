<?php

	$host = "localhost";
	$db_user = "root";
	$db_password = "";
	$db_name = "blog";
    
    $link=mysqli_connect($host, $db_user, $db_password, $db_name);
    mysqli_query($link, "SET NAMES UTF8");

?>