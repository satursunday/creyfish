<?php 
require_once("header.php");

$server = "localhost";
$user = "tmpnwg_admin";
$pwd = "anancrayfish";

$db = "tmpnwg_crayfish";

$con = mysql_connect($server, $user, $pwd) or die("Could not connect to Database.");

mysql_select_db($db, $con) or die("Database not found.");
mysql_query("SET NAMES UTF8");

function pprint($data)
{
	echo "<pre>";
	if(is_array($data) || is_object($data)){
		print_r($data);
	} else {
		echo $data;
	}
	echo "</pre>";
}

?>