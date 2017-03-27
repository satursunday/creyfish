<?php
if(isset($_POST)){
	if(isset($_POST["action"])){
		switch($_POST["action"]){
			case 'checkLogin':
				$login = isset($_SESSION["user_login"]) ? true : false;
				echo json_encode(array('login' => $login));
				exit;
			case 'add':
				$user_id = $_SESSION["user_login"]["id"];
				if(empty($_SESSION["cart"])){
					$_SESSION["cart"][$user_id] = array();
				}
				$_SESSION["cart"][$user_id][$_POST["pid"]] = 1;
				echo json_encode(array("badge" => count($_SESSION["cart"][$_SESSION["user_login"]["id"]])));
				exit;
			case 'remove':
				$user_id = $_SESSION["user_login"]["id"];
				unset($_SESSION["cart"][$user_id][$_POST["pid"]]);
				if(count($_SESSION["cart"][$user_id]) == 0){
					unset($_SESSION["cart"][$user_id]);
				}
				exit;
			case 'clear':
				$user_id = $_SESSION["user_login"]["id"];
				unset($_SESSION["cart"][$user_id]);
				echo json_encode(array("badge" => 0));
				exit;
			case 'changeAmount':
				$user_id = $_SESSION["user_login"]["id"];
				$_SESSION["cart"][$user_id][$_POST["pid"]] = $_POST["amount"];
				$total = 0.00;
				$product_price = 0.00;
				foreach($_SESSION["cart"][$user_id] as $pid => $need){
					$price = mysql_fetch_data("SELECT * FROM product_detail WHERE id = {$pid}");
					$total += $price[0]["price"]*$need;
					if($pid == $_POST["pid"]){
						$product_price = $price[0]["price"];
					}
				}
				echo json_encode(array("total" => number_format($total, 2, ".", ","), "price" => number_format($product_price*$_POST["amount"], 2, ".", ",")));
				exit;
		}
	}
}

if(isset($_SESSION["cart"][$_SESSION["user_login"]["id"]])){
	$products = array();
	$cart = $_SESSION["cart"][$_SESSION["user_login"]["id"]];
	$total_price = 0.00;
	foreach($cart as $pid => $need){
		$p = mysql_fetch_data("SELECT * FROM product_detail WHERE id = {$pid}");
		$p[0]["need"] = $need;
		$p[0]["img"] = json_decode($p[0]["img"], true);
		array_push($products, $p[0]);
		$total_price += $need*$p[0]["price"];
	}
?>

<div class="row">
	<div class="col-md-12">
		<div style="float: right; margin-bottom: 5px;">
			<button class="btn btn-danger" onclick="if(confirm('คุณต้องการจะเคลียสินค้าในตะกร้าหรือไม่?')){ clearCart(); }">ล้างตะกร้า</button>
		</div>
		<table class="table">
			<col width="70%" />
			<col width="10%" />
			<col width="20%" />
			<tr>
				<td style="text-align: center;"><b>สินค้า</b></td>
				<td style="text-align: center;"><b>จำนวน</b></td>
				<td style="text-align: center;"><b>ราคา</b></td>
			</tr>
			<?php foreach($products as $foo) { ?>
			<?php 
			$total = number_format($foo["price"]*$foo["need"], 2, ".", ",");
			echo <<<EOF
			<tr>
				<td>
					<p>
						<img src="{$img_path}{$foo["img"][0]}" width="200" />
						<span style="margin-left: 20px;"><b><a href="products/{$foo["sub_cat_id"]}/product/{$foo["id"]}/" target="_blank">{$foo["title"]}</a></b></span>
					</p>
				</td>
				<td><p style="margin-top: 50%;"><input style="text-align: center;" class="form-control" onchange="calculatePrice($(this).val(), {$foo["price"]}, {$foo["id"]});" type="number" value="{$foo["need"]}" min="0" max="{$foo["amount"]}" /></p></td>
				<td><p style="margin-top: 25%; text-align: center;"><span id="p_{$foo["id"]}">{$total}</span><a href="javascript:void(0);" onclick="removeCartItem({$foo["id"]});" style="margin-left: 20px;">x</a></td>
			</tr>
EOF;
			?>
			<?php } ?>
			<tr>
				<td></td>
				<td style="text-align: right;"><b>รวม</b></td>
				<td><p style="text-align: center;" id="total-price"><?php echo number_format($total_price, 2, ".", ","); ?></p></td>
			</tr>
		</table>
		<div align="center">
			<button class="btn btn-success">สั่งซื้อ</button>
		</div>
	</div>
</div>
<?php } else { ?>
<div class="row">
	<div class="col-md-12">
		<p style="text-align: center; color: #ff0000;">ไม่มีสินค้าในตะกร้า</p>
	</div>
</div>
<?php } ?>