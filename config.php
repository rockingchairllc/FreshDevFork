<?php
session_start();

$username='root';
$password='tamkhong0';
$server = 'localhost';
$dbname = 'fresh';
$mysqlConn = mysql_connect($server, $username, $password);
if (!$mysqlConn) {
    die('Could not connect to DB: ' . mysql_error());
}
else{
	//echo "Connection is OK";
	mysql_select_db($dbname);
}
?>