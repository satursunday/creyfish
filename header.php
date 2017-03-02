<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/bootstrap.css?v=<?php echo rand(); ?>">
<link rel="stylesheet" type="text/css" href="css/style.css">
<link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php 
$index = preg_replace("/(.*)index.php/", "$1" , $_SERVER["SCRIPT_NAME"]);
?>
<div class="container" style="background-color: #ffffff; height:100$;">
	<div class="row">
		<div class="col-xs-12">
			<a href="<?php echo $index; ?>"><img src="images/banner.png" width="100%" /></a>
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
				<li <?php if(empty($_GET["action"]) || $_GET["action"] == "") echo 'class="active"';?>><a href="<?php echo $index; ?>" class="btn">หน้าแรก</a></li>
				<li <?php if($_GET["action"] == "how-to-order") echo 'class="active"';?>><a href="how-to-order" class="btn">วิธีการสั่งซื้อสินค้า</a></li>
				<li <?php if($_GET["action"] == "payment-confirm") echo 'class="active"';?>><a href="payment-confirm" class="btn">แจ้งชำระเงิน</a></li>
				<li <?php if($_GET["action"] == "blog") echo 'class="active"';?>><a href="blog" class="btn">บทความ</a></li>
				<li <?php if($_GET["action"] == "about-us") echo 'class="active"';?>><a href="about-us" class="btn">เกี่ยวกับเรา</a></li>
				<li <?php if($_GET["action"] == "contact-us") echo 'class="active"';?>><a href="contact-us" class="btn">ติดต่อเรา</a></li>
			</ul>
			
			<ul class="nav navbar-nav navbar-right">
				<?php if(isset($_SESSION["user_login"]) && count($_SESSION["user_login"])) { ?>
				<li class="dropdown">
		        	<a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo $_SESSION["user_login"]["firstname"] . " " . $_SESSION["user_login"]["lastname"]; ?>
		        		<span class="caret"></span>
		        	</a>
			        <ul class="dropdown-menu navbar-inverse">
			          <?php if(check_staff()) { ?>
			          <li><a href="staff" class="btn">สำหรับเจ้าหน้าที่</a></li>
			          <?php } ?>
			          <li><a href="logout" class="btn">ออกจากระบบ</a></li>
			        </ul>
			      </li>
				<?php } else { ?>
        		<li <?php if($_GET["action"] == "login") echo 'class="active"';?>><a href="login" class="btn">เข้าสู่ระบบ</a></li>
        		<?php } ?>
        	</ul>
		</div>
	</nav>
    <div class="row row-offcanvas row-offcanvas-left" style="margin-bottom: 20px;">
        <!-- sidebar -->
        <div class="column col-sm-3 col-xs-1" style="height: 100%;">
        	<nav class="navbar navbar-inverse" style="height: 100%;">
				<ul id="nav-tabs-wrapper" class="nav navbar-nav navbar-inverse nav-tabs nav-pills nav-stacked" style="width: 100%;">
					<?php if(check_staff()) { ?>
	            	<li style="width: inherit;"><a href="management"><b>จัดการสินค้า</b></a></li>
	            	<?php } ?>
	            	<?php 
	            		$categories_list = mysql_fetch_data("SELECT * FROM product_categories ORDER BY sort");
	            		foreach($categories_list as $foo){
	            			echo '<li style="width: inherit;"><a href="products/' . $foo["id"] . '/">' . $foo["name"] . '</a></li>';
	            		}
	            	?>
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