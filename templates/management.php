<?php
if(!check_staff()){
	require(dirname(__FILE__) . "/../404.html");
} else {
	if(isset($_POST["action"]) && $_POST["action"] != ""){
		switch($_POST["action"]){
			case "remove_cat":
				mysql_query("DELETE FROM `product_categories` WHERE id = {$_POST["id"]}");
				mysql_query("DELETE FROM `product_sub_categories` WHERE cat_id = {$_POST["id"]}");
				break;
			case "save_sort_cat":
				mysql_query("UPDATE `product_categories` SET `sort`= \"{$_POST["sort"]}\" WHERE `id` = {$_POST["id"]}");
				break;
			case "remove_sub_cat":
				mysql_query("DELETE FROM `product_sub_categories` WHERE id = '{$_POST["id"]}'");
				break;
			case "save_sort_sub_cat":
				mysql_query("UPDATE `product_sub_categories` SET `sort`= \"{$_POST["sort"]}\" WHERE `id` = {$_POST["id"]}");
				break;
			case "remove_product":
				mysql_query("DELETE FROM `product_detail` WHERE id = {$_POST["pid"]}");
				break;
		}
		exit;
	}
	if(isset($_GET["cat_id"]) && $_GET["cat_id"] > 0 && isset($_GET["sub_cat_id"]) && $_GET["sub_cat_id"] > 0){
		require_once("manage-products.php");
	} else if(isset($_GET["id"]) && $_GET["id"] > 0) {
		require_once("manage-sub-categories.php");
	} else {
		require_once("manage-categories.php");
	}
}
	
?>