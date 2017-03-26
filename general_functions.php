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

function check_login()
{
	return (isset($_SESSION["user_login"]) && count($_SESSION["user_login"]) > 0) ? true : false;
}

function check_staff()
{
	return (check_login() && mysql_fetch_data("SELECT * FROM staff_list WHERE uid = '{$_SESSION["user_login"]["id"]}'", true) > 0) ? true : false;
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
			person AS u
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
		mysql_query("UPDATE `person` SET `last_login`= NOW() WHERE id = '{$result[0]["id"]}'");
		if($cookie){
			setcookie("user_login", json_encode($result[0]), time() + (86400 * 30), "/");
		}
		return true;
	} else {
		return false;
	}
}

function send_mail($subject, $msg, $to, $name)
{
	require_once("libs/phpmailer/PHPMailerAutoload.php");
	
	$mail = new PHPMailer;
	$mail->IsSMTP();
	$mail->CharSet = 'UTF-8';
	$mail->SMTPDebug = 0;
	$mail->Host = "smtp.gmail.com";
	$mail->Port = 587;
	$mail->SMTPSecure = "tls";
	$mail->SMTPAuth = true;
	$mail->Username = 'd.tongkampan@gmail.com';
	$mail->Password = 'vmbetibxiyzesstp';
	
	$mail->setFrom('no-reply@thespiritofcrayfish.com', 'The Spirit of Crayfish');
	$mail->addAddress($to, $name);
	$mail->Subject = $subject;
	$mail->msgHTML(str_replace("\n", "<br>", $msg));
	
	return ($mail->send()) ? true : false;
}

function do_redirect($location)
{
	if($location = "index"){
		$location = preg_replace("/(.*)index.php/", "$1" , $_SERVER["SCRIPT_NAME"]);
	}
	echo <<<EOF
<script type="text/javascript">
	window.location = '{$location}';
</script>
EOF;
}
?>

<script type="text/javascript">
	function badgeBlink()
	{
		for(i = 1; i <= 3; i++){
			$('#cart-badge').fadeOut(400);
		    $('#cart-badge').fadeIn(500);
		}
	}

	function scrollToBadge()
	{
		$('html, body').animate({
	        scrollTop: $("#cart-badge").offset().top-50
	    }, 300);
	}

	function parseData(data)
	{
		data = data.substring(data.indexOf("<\/script>")+9);
		data = JSON.parse(data);
		return data;
	}
	
	function addToCart(pid)
	{
		$.post("cart", {action: 'checkLogin'}).done(function(c){
			console.log(c);
			c = parseData(c);
			if(c.login){
				$.post("cart", {action: 'add', pid: pid}).done(function(data){
					data = parseData(data);
					$("#cart-badge").text(data.badge);
					scrollToBadge();
					badgeBlink();
				});
			} else {
				window.location = 'login';
			}
		});
	}

	function removeCartItem(pid)
	{
		$.post("cart", {action: 'remove', pid: pid}).done(function(data){
			window.location = window.location.href;
		});
	}

	function calculatePrice(amount, price, pid)
	{
		$.post("cart", {action: 'changeAmount', pid: pid, amount: amount}).done(function(data){
			data = parseData(data);
			$("#p_" + pid).html(data.price);
			$("#total-price").html(data.total);
		});
	}

	function clearCart()
	{
		$.post("cart", {action: 'clear'}).done(function(data){
			data = parseData(data);
			$("#cart-badge").text(data.badge);
			scrollToBadge();
			badgeBlink();
			window.location = window.location.href;
		});
	}
</script>