<?php
session_start();
connect_db();

$menu_list = array(
		"how-to-order" => "วิธีการสั่งซื้อสินค้า"
		, "payment-confirm" => "แจ้งชำระเงิน"
		, "blog" => "บทความ"
		, "about-us" => "เกี่ยวกับเรา"
		, "contact-us" => "ติดต่อเรา"
);

$index = preg_replace("/(.*)index.php/", "$1" , $_SERVER["SCRIPT_NAME"]);
$base_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] .  $index;
$img_path = $base_url . '/images/products/';

require_once("general_functions.php");
require_once("header.php");
require_once("footer.php");

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