<div class="panel panel-default" >
	<div class="panel-heading">
		<div class="panel-title">เข้าสู่ระบบ<a href="/register"><button type="button" class="btn btn-success right" style="margin: -6px auto;">สมัครสมาชิก</button></a></div>
	</div>     
	<div style="padding-top:30px" class="panel-body" >
		<form action="#" method="post" class="form-horizontal">
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
						<input id="login-remember" type="checkbox" name="remember" value="1"> Remember me
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


<?php 
if(isset($_POST["username"]) && isset($_POST["password"])){
}
?>