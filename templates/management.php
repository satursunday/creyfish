<?php
if(!check_staff()){
	require(dirname(__FILE__) . "/../404.html");
} else {
	if(isset($_POST["action"]) && $_POST["action"] != ""){
		switch($_POST["action"]){
			case "remove_cat":
				mysql_query("DELETE FROM `product_categories` WHERE id = {$_POST["id"]}");
				break;
			case "remove_sub_cat":
				mysql_query("DELETE FROM `product_sub_categories` WHERE id = '{$_POST["id"]}'");
				break;
		}
		exit;
	}
	if(isset($_GET["id"]) && $_GET["id"] > 0) {
		require_once("manage-sub-categories.php");
	} else {
		require_once("manage-categories.php");
	}
}
	
?>