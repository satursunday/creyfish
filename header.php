<?php 
if(isset($_POST["action"]) && isset($_GET["action"]) && $_GET["action"] == "cart"){
	require_once(dirname(__FILE__). "/templates/" . "cart.php");
}
?>
<base href="<?php echo $base_url; ?>" />
<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.slides.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/bootstrap.css?v=<?php echo rand(); ?>">
<link rel="stylesheet" type="text/css" href="css/style.css">
<link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<div class="container" style="background-color: #fff; height:100$;">
	<div class="row" align="center" style="background-color: #d9d9d9;">
		<div class="col-xs-12" style="cursor: pointer;" onclick="window.location = '<?php echo $index; ?>';">
			<img src="images/banner.png" width="45%" height="45%" style="margin-top: 60px; margin-bottom: 60px;"/>
		</div>
	</div>
	<nav class="navbar navbar-inverse mt-20">
		<div class="container-fluid">
			<ul class="nav navbar-nav"> 
				<?php 
					if(empty($_GET["action"])){
						$_GET["action"] = "";
					}
				?>
				<li <?php if(empty($_GET["action"]) || $_GET["action"] == "") echo 'class="active"';?>><a href="<?php echo $index; ?>" class="btn">หน้าแรก</a></li>
				<?php 
					foreach($menu_list as $des => $text){
						$active = ($_GET["action"] == $des) ? " class=\"active\"" : "";
						echo '<li' . $active . '><a href="' . $des . '" class="btn">' . $text . '</a></li>';
					}
				?>
			</ul>
			
			<ul class="nav navbar-nav navbar-right">
				<?php if(isset($_SESSION["user_login"]) && count($_SESSION["user_login"])) { ?>
				<li><a href="cart" class="btn">ตะกร้าสินค้า<span id="cart-badge" class="badge"><?php echo isset($_SESSION["cart"][$_SESSION["user_login"]["id"]]) ? count($_SESSION["cart"][$_SESSION["user_login"]["id"]]) : 0; ?></span></a></li>
				<li class="dropdown">
		        	<a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo $_SESSION["user_login"]["firstname"] . " " . $_SESSION["user_login"]["lastname"]; ?>
		        		<span class="caret"></span>
		        	</a>
			        <ul class="dropdown-menu navbar-inverse">
			          <?php if(check_staff()) { ?>
			          <li><a href="staff" class="btn">สำหรับเจ้าหน้าที่</a></li>
			          <?php } ?>
			          <li><a href="orderlog" class="btn">รายการสั่งซื้อ</a></li>
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
	            			echo '<li style="width: inherit;"><a class="newline-word cat-menu" href="javascript:void(0);" data-toggle="collapse" data-target="#demo_' . $foo["id"] . '"><h4><b>' . $foo["name"] . '</b></h4></a></li>';
	            			?>
	            			<ul id="demo_<?php echo $foo["id"]; ?>" class="nav navbar-nav navbar-inverse nav-tabs nav-pills nav-stacked collapse" style="width: 100%;">
	            			<?php 
	            				$sub_categories_list = mysql_fetch_data("SELECT * FROM product_sub_categories WHERE cat_id = {$foo["id"]} ORDER BY sort");
	            				foreach($sub_categories_list as $foo2){
	            					echo '<li style="width: inherit;"><a class="newline-word sub-cat-menu" href="products/' . $foo2["id"] . '/">' . $foo2["name"] . '</a></li>';
	            				}
	            			?>
							</ul>
	            			<?php 
	            		}
	            	?>
				</ul>
            </nav>
        </div>
        <!-- /sidebar -->

        <!-- main right col -->
        <div class="column col-sm-9 col-xs-11" id="main">
			<div class="row">
				<div class="col-xs-12">
					<?php if(isset($_GET["action"]) && trim($_GET["action"] != "")){ ?>
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="<?php echo $index; ?>">หน้าแรก</a></li>
						<?php 
						$not_found = true;
						foreach($menu_list as $des => $text){
							if($_GET["action"] == $des){
								echo '<li class="breadcrumb-item active">' . $text . '</li>';
								$not_found = false;
								break;
							}
						}
						
						if($not_found){
							switch($_GET["action"]){
								case "management":
									if(empty($_["cat_id"]) && empty($_GET["sub_cat_id"]) && empty($_GET["id"])){
										echo '<li class="breadcrumb-item newline-word active">จัดการสินค้า</li>';
									} else {
										echo '<li class="breadcrumb-item newline-word"><a href="management">จัดการสินค้า</a></li>';
									} 
									
									if(isset($_GET["id"]) && $_GET["id"] > 0){
										$categories = mysql_fetch_data("SELECT name FROM product_categories WHERE id = {$_GET["id"]}");
										echo '<li class="breadcrumb-item newline-word active">' . $categories[0]["name"] . '</li>';
									} else if(isset($_GET["cat_id"]) && $_GET["cat_id"] > 0 && isset($_GET["sub_cat_id"]) && $_GET["sub_cat_id"] > 0){
										$categories = mysql_fetch_data("SELECT name FROM product_categories WHERE id = {$_GET["cat_id"]}");
										$sub_categories = mysql_fetch_data("SELECT name FROM product_sub_categories WHERE id = {$_GET["sub_cat_id"]}");
										echo '<li class="breadcrumb-item newline-word"><a href="management/' . $_GET["cat_id"] . '">' . $categories[0]["name"] . '</a></li>';
										if(empty($_GET["pid"])){
											echo '<li class="breadcrumb-item newline-word active">' . $sub_categories[0]["name"] . '</li>';
										} else {
											$product= mysql_fetch_data("SELECT title FROM product_detail WHERE id = {$_GET["pid"]}");
											echo '<li class="breadcrumb-item newline-word"><a href="management/' . $_GET["cat_id"] . '/sub/' . $_GET["sub_cat_id"] . '/">' . $sub_categories[0]["name"] . '</a></li>';
											echo '<li class="breadcrumb-item newline-word active">' . $product[0]["title"] . '</li>';
										}
									}
									break;
							}
						}
						?>
					</ol>
					<?php } ?>
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
                		require_once($directory . "main.php");
                	} else {
                		require_once("404.html");
                	}
                ?>
                		</div>
                	</div>
        </div>
        <!-- /main -->
    </div>
</div>