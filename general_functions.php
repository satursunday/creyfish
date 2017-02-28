<?php 
function pprint($data, $exit = false)
{
	echo "<pre>";
	if(is_array($data) || is_object($data)){
		print_r($data);
	} else {
		echo $data;
	}
	echo "</pre>";

	if($exit){
		exit;
	}
}

function mysql_fetch_data($query, $fetch_row = false)
{
	connect_db();
	$result = mysql_query($query);
	$output = array();

	if($fetch_row){
		return mysql_num_rows($result);
	}

	while($data = mysql_fetch_assoc($result)){
		array_push($output, $data);
	}

	return $output;
}

function generate_insert_text($data)
{
	$data = trim($data);
	$data = mysql_real_escape_string($data);
	return $data;
}

function do_login($user, $pass, $cookie = false)
{
	connect_db();
	$pass = md5($pass);
	$result = mysql_fetch_data("
		SELECT
			u.id
			, u.email
			, u.date_created
			, u.last_login
			, d.firstname
			, d.lastname
			, d.building
			, d.room
			, d.floor
			, d.house_number
			, d.moo
			, d.village
			, d.soi
			, d.road
			, d.post_code
			, d.phone
			, p.PROVINCE_ID AS province_id
			, p.PROVINCE_NAME AS province
			, am.AMPHUR_ID AS amphur_id
			, am.AMPHUR_NAME AS amphur
			, district.DISTRICT_ID AS district_id
			, district.DISTRICT_NAME AS district
		FROM 
			users AS u
		INNER JOIN
			users_data AS d
		ON
			u.id = d.uid
		INNER JOIN
			address_province AS p
		ON
			d.PROVINCE_ID = p.PROVINCE_ID
		INNER JOIN
			address_amphur AS am
		ON
			d.AMPHUR_ID = am.AMPHUR_ID
		INNER JOIN
			address_district AS district
		ON
			d.DISTRICT_ID = district.DISTRICT_ID
		WHERE 
			u.email = '{$user}' 
			AND u.password = '{$pass}'
	");
	if(count($result) > 0){
		$_SESSION["user_login"] = $result[0];
		mysql_query("UPDATE `users` SET `last_login`= NOW() WHERE id = '{$result[0]["id"]}'");
		if($cookie){
			setcookie("user_login", json_encode($result[0]), time() + (86400 * 30), "/");
		}
		return true;
	} else {
		return false;
	}
	
}
?>