<?php
$error = false;
if(check_login()){
	do_redirect("index");
}
if(isset($_POST["username"]) && isset($_POST["password"])){
	if(empty($_POST["remember"])){
		$_POST["remember"] = false;
	}
	if(do_login($_POST["username"], $_POST["password"], $_POST["remember"])){
		/*
		if(true || preg_match("/order/", $_SERVER["HTTP_REFERER"])){
			do_redirect($_SERVER["HTTP_REFERER"]);
		} else {
			do_redirect("index");
		}
		*/
		do_redirect("index");
	} else {
		$error = true;
	}
}
?>
<div class="panel panel-default" >
	<div class="panel-heading">
		<div class="panel-title">เข้าสู่ระบบ<a href="register"><button type="button" class="btn btn-success pull-right" style="margin: -6px auto;">สมัครสมาชิก</button></a></div>
	</div>     
	<div style="padding-top:30px" class="panel-body" >
		<?php if($error) { ?><div class="alert alert-danger">อีเมล์หรือรหัสผ่าน ผิดพลาด</div><?php } ?>
		<form action="login" method="post" class="form-horizontal">
			<div style="margin-bottom: 25px" class="input-group">
				<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
				<input class="form-control" type="text" name="username" placeholder="E-mail" style="width: 50%;" />
			</div>
			<div style="margin-bottom: 25px" class="input-group">
				<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
				<input class="form-control" type="password" name="password" placeholder="Password" style="width: 50%;" />
			</div>
			<div class="input-group">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="remember" value="1"> Remember me
					</label>
				</div>
			</div>
			<div style="margin-top:10px" class="form-group">
				<div class="col-xs-6 controls">
					<button type="submit" class="btn btn-primary">เข้าสู่ระบบ</button>
					&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="reset-password">ลืมรหัสผ่าน</a>
				</div>
			</div>
		</form>
	</div>
</div>