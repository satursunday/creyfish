<?php
if(empty($_SESSION["user_login"])){
	require_once($directory . "login.php");
}