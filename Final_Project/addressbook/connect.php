<?php
function doDB() {
	global $mysqli;

	//connect to server and select database; you may need it

	//production server
	//$mysqli = mysqli_connect("localhost", "lisabalbach_yanickn", "CIT190102", "lisabalbach_Yanick");

	//test
	$mysqli = mysqli_connect("localhost", "root", "", "players");

	//if connection fails, stop script execution
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
}
?>