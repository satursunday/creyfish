<link rel="stylesheet" type="text/css" href="css/bootstrap.css?v=<?php echo rand(); ?>">
<link rel="stylesheet" type="text/css" href="css/style.css">
<link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<script src="js/jquery-3.1.1.min.js"></script>

<div class="container" style="background-color: #ffffff;">
	<div class="row">
		<div class="col-xs-12">
			<a href="index.php"><img src="images/banner.png" width="100%" /></a>
		</div>
	</div>
	<nav class="navbar navbar-inverse">
		<div class="container-fluid">
			<ul class="nav navbar-nav"> 
				<?php 
					if(empty($_GET["action"])){
						$_GET["action"] = "";
					}
				?>
				<li <?php if(empty($_GET["action"]) || $_GET["action"] == "") echo 'class="active"';?>><a href="index.php" class="btn">หน้าแรก</a></li>
				<li <?php if($_GET["action"] == "how-to-order") echo 'class="active"';?>><a href="how-to-order" class="btn">วิธีการสั่งซื้อสินค้า</a></li>
				<li <?php if($_GET["action"] == "payment-confirm") echo 'class="active"';?>><a href="payment-confirm" class="btn">แจ้งชำระเงิน</a></li>
				<li <?php if($_GET["action"] == "blog") echo 'class="active"';?>><a href="blog" class="btn">บทความ</a></li>
				<li <?php if($_GET["action"] == "about-us") echo 'class="active"';?>><a href="about-us" class="btn">เกี่ยวกับเรา</a></li>
				<li <?php if($_GET["action"] == "contact-us") echo 'class="active"';?>><a href="contact-us" class="btn">ติดต่อเรา</a></li>
			</ul>
			
			<ul class="nav navbar-nav navbar-right">
        		<li <?php if($_GET["action"] == "login") echo 'class="active"';?>><a href="login" class="btn">เข้าสู่ระบบ</a></li>
        	</ul>
		</div>
	</nav>
    <div class="row row-offcanvas row-offcanvas-left">
        <!-- sidebar -->
        <div class="column col-sm-3 col-xs-1" style="height: 100%;">
        	<nav class="navbar navbar-inverse" style="height: 100%;">
	            <ul class="nav navbar-inverse">
	                <li><a href="#">Link 1</a></li>
	            </ul>
            </nav>
        </div>
        <!-- /sidebar -->

        <!-- main right col -->
        <div class="column col-sm-9 col-xs-11" id="main">
            <p class="content">
            	<div class="container">
					<div class="row">
						<div class="col-xs-8">
		                <?php 
		                	$directory = dirname(__FILE__). "/templates/";
		                	if(isset($_GET["action"])){
			                	$_GET["action"] = trim($_GET["action"]);
		                	}
		                	if(file_exists($directory . $_GET["action"] . ".php")){
		                		require_once($directory . $_GET["action"] . ".php");
		                	} else if(file_exists($directory . $_GET["action"] . ".html")){
		                		require_once($directory . $_GET["action"] . ".html");
		                	} else if(empty($_GET["action"]) || $_GET["action"] == ""){
		                		require_once(dirname(__FILE__). "/" . "index.php");
		                	} else {
		                		require_once("404.html");
		                	}
		                ?>
                		</div>
                	</div>
                </div>
            </p>
        </div>
        <!-- /main -->
    </div>
</div>