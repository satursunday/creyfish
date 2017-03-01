<?php
session_start();
require_once("general_functions.php");
require_once("header.php");

connect_db();

function connect_db()
{
	$server = "localhost";
	$user = "root"; //tmpnwg_admin
	$pwd = ""; //anancrayfish
	
	$db = "tmpnwg_crayfish";
	
	$con = mysql_connect($server, $user, $pwd) or die("Could not connect to Database.");
	
	mysql_select_db($db, $con) or die("Database not found.");
	mysql_query("SET NAMES UTF8");
}

?>