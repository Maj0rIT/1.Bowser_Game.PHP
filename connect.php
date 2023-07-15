<?php

	$host = "localhost";
	$db_user = "root";
	$db_password = "";
	$db_name = "osadnicy";

	$connection = mysqli_connect($host, $db_user, $db_password, $db_name);

	if (!$connection) {
		die("Connection failed: " . mysqli_connect_error());
	}

?>
