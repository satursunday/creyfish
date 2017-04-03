<?php
define("DB_NAME", "tmpnwg_crayfish");
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
$base_url = siteURL() .  $index;
$img_path = $base_url . '/images/products/';

require_once("general_functions.php");
require_once("header.php");
require_once("footer.php");

function connect_db()
{
	$server = "localhost";
	$user = "root"; //tmpnwg_admin
	$pwd = ""; //anancrayfish
	
	$db = DB_NAME;
	
	$con = mysql_connect($server, $user, $pwd) or die("Could not connect to Database.");
	
	mysql_select_db($db, $con) or die("Database not found.");
	mysql_query("SET NAMES UTF8");
}

function siteURL()
{
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$domainName = $_SERVER['HTTP_HOST'];
	return $protocol.$domainName;
}

?>