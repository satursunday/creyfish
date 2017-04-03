<?php
$main_error = $email_error = $pass_error = $pass_confirm_error = $firstname_error = $lastname_error = $home_no_error = $moo_error = $province_error = $amphur_error = $district_error = $post_error = $tel_error = array("status" => false);
$error = false;

if(isset($_GET["province_id"]) && $_GET["province_id"] != ""){
	$data = mysql_fetch_data("SELECT * FROM address_amphur WHERE PROVINCE_ID= '{$_GET["province_id"]}' ORDER BY AMPHUR_NAME");
	$json_data = json_encode($data);
	echo $json_data;
	exit;
} else if(isset($_GET["amphur_id"]) && $_GET["amphur_id"] != ""){
	$data = mysql_fetch_data("SELECT * FROM address_district WHERE AMPHUR_ID= '{$_GET["amphur_id"]}' ORDER BY DISTRICT_NAME");
	$json_data = json_encode($data);
	echo $json_data;
	exit;
}
if(isset($_POST["submit"]) && $_POST["submit"] == "สมัครสมาชิก"){
	$user = $_POST["user"];
	$contact = $_POST["contact"];
	$email_validation = "/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/";
	$password_validation = "/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])([a-zA-Z0-9\!\@\#\$\%\^\&\*\(\)\_\+\-\=\[\]\{\}\|\;\:\'\"\,\<\>\.\/\?]{6,12})$/";
	
	if(!preg_match($email_validation, $user["email"])){
		$email_error = array("status" => true, "msg" => "อีเมล์ไม่ถูกต้อง");
		$error = true;
	}
	
	
	if(!preg_match($password_validation, $user["password"])){
		$pass_error = array("status" => true, "msg" => "รหัสผ่านไม่ถูกต้อง");
		$error = true;
	} else if($user["password"] != $user["password_confirm"]){
		$pass_error = array("status" => true, "msg" => "รหัสผ่านไม่ตรงกัน");
		$pass_confirm_error = array("status" => true, "msg" => "รหัสผ่านไม่ตรงกัน");
		$error = true;
	}
	
	if(trim($contact["firstname"]) == ""){
		$firstname_error = array("status" => true, "msg" => "กรุณากรอกข้อมูล"); 
		$error = true;
	}
	
	if(trim($contact["lastname"]) == ""){
		$lastname_error = array("status" => true, "msg" => "กรุณากรอกข้อมูล");
		$error = true;
	}
	
	if(trim($contact["house_number"]) == ""){
		$home_no_error = array("status" => true, "msg" => "กรุณากรอกข้อมูล");
		$error = true;
	}
	
	if(trim($contact["moo"]) == ""){
		$moo_error = array("status" => true, "msg" => "กรุณากรอกข้อมูล");
		$error = true;
	}
	
	if(trim($contact["province"]) == "0"){
		$province_error = array("status" => true, "msg" => "กรุณาเลือกข้อมูล");
		$error = true;
	}
	
	if(trim($contact["amphur"]) == "0"){
		$amphur_error = array("status" => true, "msg" => "กรุณาเลือกข้อมูล");
		$error = true;
	}
	
	if(trim($contact["district"]) == "0"){
		$district_error = array("status" => true, "msg" => "กรุณาเลือกข้อมูล");
		$error = true;
	}
	
	if(trim($contact["postcode"]) == ""){
		$post_error = array("status" => true, "msg" => "กรุณากรอกข้อมูล");
		$error = true;
	}
	
	if(trim($contact["tel"]) == ""){
		$tel_error = array("status" => true, "msg" => "กรุณากรอกข้อมูล");
		$error = true;
	}
	
	if(!$error){
		connect_db();
		$email = trim($_POST["user"]["email"]);
		$pass = trim($_POST["user"]["password"]);
		$password = md5($pass);
		$create_users = <<<EOF
			INSERT INTO 
				`person`(`email`, `password`, `date_created`) 
			VALUES ('{$email}','{$password}', NOW())
EOF;
		if(mysql_query($create_users)){
			$user_id = mysql_fetch_data("SELECT id FROM person WHERE email = '{$email}'");
			$user_id = $user_id[0]["id"];
			foreach($_POST["contact"] as $k => $v){
				${$k} = generate_insert_text($v);
			}
			$create_user_data = <<<EOF
			INSERT INTO 
				`users_data`(`uid`, `firstname`, `lastname`, `building`, `room`, `floor`, `house_number`, `moo`, `village`, `soi`, `road`, `PROVINCE_ID`, `AMPHUR_ID`, `DISTRICT_ID`, `post_code`, `phone`) 
			VALUES ('{$user_id}','{$firstname}','{$lastname}','{$building}','{$room}','{$floor}','{$house_number}','{$moo}','{$village}','{$soi}','{$road}',$province,$amphur,$district,'{$postcode}','{$tel}')		
EOF;
			if(mysql_query($create_user_data)){
				do_login($email, $pass);
				$msg = <<<EOF
ยินดีต้อนรับสู่ The Spirit of Crayfish
				
ขอบคุณที่สมัครสมาชิกกับเรา
EOF;
				send_mail("ยินดีต้อนรับสู่ Crayfish By Red Baron", $msg, $email, $firstname . ' ' . $lastname);
				do_redirect("login");
			} else {
				$main_error = array("status" => true, "msg" => "เกิดข้อผิดพลาด ไม่สามารถสมัครสมาชิกได้ โปรดติดต่อเจ้าหน้าที่");
			}
		} else {
			$main_error = array("status" => true, "msg" => "เกิดข้อผิดพลาด ไม่สามารถสมัครสมาชิกได้ โปรดติดต่อเจ้าหน้าที่");
		}
	}
}
?>
<script type="text/javascript">
	function get_location(type, that)
	{
		var result = {};
		switch(type){
			case 'amphur':
				call_data = {
					province_id: $(that).val()
				};
				$("#amphur").html("<option value=\"0\" selected>- เลือกอำเภอ -</option>");
				$("#district").html("<option value=\"0\" selected>- เลือกตำบล -</option>");
				break;
			case 'district':
				call_data = {
					amphur_id: $(that).val()
				};
				$("#district").html("<option value=\"0\" selected>- เลือกตำบล -</option>");
				break;
		}

		$.get( "register", call_data ).done(function( data ) {
			var data = data.split("\n");
			data = data[(data.length)-1];
			data = JSON.parse(data);

			for(i in data){
				if(type == "amphur"){
					var value = data[i].AMPHUR_ID;
					var text = data[i].AMPHUR_NAME;
				} else if(type == "district"){
					var value = data[i].DISTRICT_ID;
					var text = data[i].DISTRICT_NAME;
				}
				$("#" + type).append("<option value=\"" + value + "\">" + text + "</option>");
			}
		});
	}

	function show_mail_val(that)
	{
		if((/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/).test($(that).val())){
			$('#val_email').show(); 
		} else { 
			$('#val_email').hide();
		}
	}
</script>
<div class="panel panel-default" >
	<div class="panel-heading">
		<div class="panel-title">สมัครสมาชิก</div>
	</div>     
	<div style="padding-top:30px" class="panel-body" >
		<?php if($main_error["status"]) echo "<div class=\"alert alert-danger\">{$main_error["msg"]}</div>"; ?>
		<form class="form-horizontal" action='register' method="POST">
	  		<fieldset>
				<legend class="">ข้อมูลล็อกอิน</legend>
		    	<div class="control-group">
		    		<div class="col-sm-6 col-xs-12">
			      		<label class="control-label" for="email">E-mail <font color="#ff000">*<?php if($email_error["status"]) { echo " " . $email_error["msg"]; } ?></font></label>
			      		<div class="controls">
			        		<input type="text" id="email" name="user[email]" class="form-control" onchange="show_mail_val(this);" value="<?php if(isset($_POST["user"]["email"])) echo $_POST["user"]["email"];?>" />
			      		</div>
		      		</div>
		      		<div class="col-sm-6 col-xs-12">
		      			<!-- <a id="val_email" style="display: none;" href="javascript:void(0);" onclick="validate_email();">ตรวจสอบ</a> -->
		      		</div>
		      		<div style="clear: both;"></div>
		    	</div>
		 
		    	<div class="control-group">
		    		<div class="col-sm-6 col-xs-12">
			      		<label class="control-label" for="password">Password <font color="#ff000">*<?php if($pass_error["status"]) { echo " " . $pass_error["msg"]; } ?></font></label>
			      		<div class="controls">
			        		<input type="password" id="password" name="user[password]" class="form-control" />
			      		</div>
					</div>
					<div class="col-sm-6 col-xs-12">
						<p style="font-size: 10px; top: 31px; position: absolute;">6-12 อักขระ ประกอบด้วย ตัวพิมพ์ใหญ่, ตัวพิมพ์เล็ก และตัวเลข ประกอบกันอย่างน้อยอย่างละ 1 ตัวอักษร</p>
					</div>
		      		<div style="clear: both;"></div>
		    	</div>
		 
		    	<div class="control-group">
		    		<div class="col-sm-6 col-xs-12">
			      		<label class="control-label"  for="password_confirm">Password (Confirm) <font color="#ff000">*<?php if($pass_confirm_error["status"]) { echo " " . $pass_confirm_error["msg"]; } ?></font></label>
			      		<div class="controls">
			        		<input type="password" id="password_confirm" name="user[password_confirm]" class="form-control" />
			      		</div>
		      		</div>
		      		<div style="clear: both;"></div>
		    	</div>
		    </fieldset>
		    	
		    <fieldset style="margin-top: 50px;">
				<legend class="">ที่อยู่จัดส่ง</legend>
				<div class="control-group">
					<div class="col-sm-6 col-xs-12">
			      		<label class="control-label" for="firstname">ชื่อ <font color="#ff000">*<?php if($firstname_error["status"]) { echo " " . $firstname_error["msg"]; } ?></font></label>
			      		<div class="controls">
			        		<input type="text" id="firstname" name="contact[firstname]" class="form-control" value="<?php if(isset($_POST["contact"]["firstname"])) echo $_POST["contact"]["firstname"];?>" />
			      		</div>
		      		</div>
		      		<div class="col-sm-6 col-xs-12">
			      		<label class="control-label" for="lastname">นามสกุล <font color="#ff000">*<?php if($lastname_error["status"]) { echo " " . $lastname_error["msg"]; } ?></font></label>
			      		<div class="controls">
			        		<input type="text" id="lastname" name="contact[lastname]" class="form-control" value="<?php if(isset($_POST["contact"]["lastname"])) echo $_POST["contact"]["lastname"];?>" />
			      		</div>
		      		</div>
		    	</div>
		    	<div style="clear: both;"></div>
		    	
		    	<div class="control-group">
		    		<div class="col-sm-6 col-xs-12">
			      		<label class="control-label" for="building">อาคาร</label>
			      		<div class="controls">
			        		<input type="text" id="building" name="contact[building]" class="form-control" value="<?php if(isset($_POST["contact"]["building"])) echo $_POST["contact"]["building"];?>" />
			      		</div>
		      		</div>
		      		<div class="col-sm-3 col-xs-6">
		      			<label class="control-label" for="room">ห้อง</label>
			      		<div class="controls">
			        		<input type="text" id="room" name="contact[room]" class="form-control" value="<?php if(isset($_POST["contact"]["room"])) echo $_POST["contact"]["room"];?>" />
			      		</div>
		      		</div>
		      		<div class="col-sm-3 col-xs-6">
						<label class="control-label" for="floor">ชั้น</label>
			      		<div class="controls">
			        		<input type="text" id="floor" name="contact[floor]" class="form-control" value="<?php if(isset($_POST["contact"]["floor"])) echo $_POST["contact"]["floor"];?>" />
			      		</div>
		      		</div>
		    	</div>
		    	<div style="clear: both;"></div>
		    	
		    	<div class="control-group">
		    		<div class="col-sm-3 col-xs-6">
			      		<label class="control-label" for="houseno">เลขที่ <font color="#ff000">*<?php if($home_no_error["status"]) { echo " " . $home_no_error["msg"]; } ?></font></label>
			      		<div class="controls">
			        		<input type="text" id="houseno" name="contact[house_number]" class="form-control" value="<?php if(isset($_POST["contact"]["house_number"])) echo $_POST["contact"]["house_number"];?>" />
			      		</div>
		      		</div>
		      		<div class="col-sm-3 col-xs-6">
		      			<label class="control-label" for="moo">หมู่ <font color="#ff000">*<?php if($moo_error["status"]) { echo " " . $moo_error["msg"]; } ?></font></label>
			      		<div class="controls">
			        		<input type="text" id="moo" name="contact[moo]" class="form-control" value="<?php if(isset($_POST["contact"]["moo"])) echo $_POST["contact"]["moo"];?>" />
			      		</div>
		      		</div>
		      		<div class="col-sm-6 col-xs-12">
		      			<label class="control-label" for="village">หมู่บ้าน</label>
			      		<div class="controls">
			        		<input type="text" id="village" name="contact[village]" class="form-control" value="<?php if(isset($_POST["contact"]["village"])) echo $_POST["contact"]["village"];?>" />
			      		</div>
		      		</div>
		    	</div>
		    	<div style="clear: both;"></div>
		    	
		    	<div class="control-group">
		    		<div class="col-sm-6 col-xs-12">
			      		<label class="control-label" for="email">ซอย</label>
			      		<div class="controls">
			        		<input type="text" id="soi" name="contact[soi]" class="form-control" value="<?php if(isset($_POST["contact"]["soi"])) echo $_POST["contact"]["soi"];?>" />
			      		</div>
		      		</div>
		      		<div class="col-sm-6 col-xs-12">
			      		<label class="control-label" for="road">ถนน</label>
			      		<div class="controls">
			        		<input type="text" id="road" name="contact[road]" class="form-control" value="<?php if(isset($_POST["contact"]["road"])) echo $_POST["contact"]["road"];?>" />
			      		</div>
		      		</div>
		    	</div>
		    	<div style="clear: both;"></div>
		    	
		    	<div class="control-group">
		    		<div class="col-sm-6 col-xs-12">
		      			<label class="control-label" for="province">จังหวัด <font color="#ff000">*<?php if($province_error["status"]) { echo " " . $province_error["msg"]; } ?></font></label>
			      		<div class="controls">
                    		<select class="form-control" id="province" name="contact[province]" onchange="get_location('amphur', this);">
                        		<option value="0">- เลือกจังหวัด -</option>
                        		<?php 
	                        		$province = mysql_fetch_data("SELECT * FROM address_province ORDER BY PROVINCE_NAME");
	                        		foreach($province as $foo){
		                        		echo "<option value='{$foo["PROVINCE_ID"]}' >{$foo["PROVINCE_NAME"]}</option>";
	                        		}
                        		?>
                    		</select>
			      		</div>
			    	</div>
		    	</div>
		    	<div style="clear: both;"></div>
		    	
		    	<div class="control-group">
		    		<div class="col-sm-6 col-xs-12">
			      		<label class="control-label" for="amphur">อำเภอ <font color="#ff000">*<?php if($amphur_error["status"]) { echo " " . $amphur_error["msg"]; } ?></font></label>
			      		<div class="controls">
                    		<select class="form-control" id="amphur" name="contact[amphur]" onchange="get_location('district', this);">
                        		<option value="0">- เลือกอำเภอ -</option>
                    		</select>
			      		</div>
		      		</div>
		    	</div>
		    	<div style="clear: both;"></div>
		    	
		    	<div class="control-group">
		    		<div class="col-sm-6 col-xs-12">
			      		<label class="control-label" for="district">ตำบล <font color="#ff000">*<?php if($district_error["status"]) { echo " " . $district_error["msg"]; } ?></font></label>
			      		<div class="controls">
                    		<select class="form-control" id="district" name="contact[district]">
                        		<option value="0">- เลือกตำบล -</option>
                    		</select>
			      		</div>
			      	</div>
		    	</div>
		    	<div style="clear: both;"></div>
		    	
		    	<div class="control-group">
		    		<div class="col-sm-6 col-xs-12">
			      		<label class="control-label" for="postcode">รหัสไปรษณีย์ <font color="#ff000">*<?php if($post_error["status"]) { echo " " . $post_error["msg"]; } ?></font></label>
			      		<div class="controls">
			        		<input type="text" id="postcode" name="contact[postcode]" class="form-control" value="<?php if(isset($_POST["contact"]["postcode"])) echo $_POST["contact"]["postcode"];?>" />
			      		</div>
		      		</div>
		    	</div>
		    	<div style="clear: both;"></div>
		    	
		    	<div class="control-group">
		    		<div class="col-sm-6 col-xs-12">
			      		<label class="control-label" for="tel">โทร <font color="#ff000">*<?php if($tel_error["status"]) { echo " " . $tel_error["msg"]; } ?></font></label>
			      		<div class="controls">
			        		<input type="text" id="tel" name="contact[tel]" class="form-control" value="<?php if(isset($_POST["contact"]["tel"])) echo $_POST["contact"]["tel"];?>" />
			      		</div>
		      		</div>
		    	</div>
		    	<div style="clear: both;"></div>
			</fieldset>
		    
		    <fieldset>	
		    	<div class="control-group" style="margin-top: 30px;">
		    		<div class="col-xs-12">
			      		<div class="controls">
			        		<input type="submit" name="submit" class="btn btn-success" value="สมัครสมาชิก" />
						</div>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
</div>