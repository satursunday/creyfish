<?php
if(empty($_SESSION["user_login"])){
	require_once($directory . "login.php");
} else {
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
		if(isset($_GET["confirm"]) && $_GET["confirm"]){
			$description = json_encode($_SESSION["cart"][$_SESSION["user_login"]["id"]]);
			$next_invoice = mysql_fetch_data("SELECT `AUTO_INCREMENT` FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '" . DB_NAME . "' AND TABLE_NAME = 'invoices'");
			$next_invoice = $next_invoice[0]["AUTO_INCREMENT"];
			
			mysql_query("
				INSERT INTO 
					`orders`(
						`id`
						, `uid`
						, `status`
						, `date_created`
						, `invoice_id`
					) VALUES (
						NULL
						,{$_SESSION["user_login"]["id"]}
						,'pending'
						,NOW()
						,{$next_invoice}
					)
			");
			
			mysql_query("
					INSERT INTO 
						`invoices`(
							`id`
							, `description`
							, `total`
							, `status`
						) VALUES (
							{$next_invoice}
							, '{$description}'
							, {$total_price}
							, 'pending'
						)
			");
			
			foreach($products as $p){
				$product = mysql_fetch_data("SELECT amount FROM product_detail WHERE id = {$p["id"]}");
				$amount = $product[0]["amount"]-$p["need"];
				mysql_query("UPDATE `product_detail` SET `amount`={$amount} WHERE id = {$p["id"]}");
			}
			
			send_mail_client("ยืนยันคำสั่งซื้อ", "ยืนยันคำสั่งซื้อสินค้า invoice#{$next_invoice}");
			send_mail_admin("ยืนยันคำสั่งซื้อ", "มีลูกค้าสั่งซื้อสินค้า invoice#{$next_invoice}");
			unset($_SESSION["cart"][$_SESSION["user_login"]["id"]]);
			
			echo <<<EOF
<div class="row">
	<div class="col-md-12" align="center">
		<h2>สั่งซื้อเรียบร้อย</h2>		
	</div>
</div>
EOF;
		} else {
		
?>
<div class="row">
	<div class="col-md-12">
		<div class="col-md-12">
			<h2>รายการสั่งซื้อ</h2>
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
				<td><p style="margin-top: 50%;">{$foo["amount"]}</p></td>
				<td><p style="margin-top: 25%; text-align: center;"><span id="p_{$foo["id"]}">{$total}</span></td>
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
			<a href="order?confirm=true" class="btn btn-success">ยืนยันคำสั่งซื้อ</a>
		</div>
	</div>
</div>
<?php 
		}
	}
}