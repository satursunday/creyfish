<?php
if(check_login()){
	if(isset($_POST["action"]) && $_POST["action"] == "cancel"){
		$description= mysql_fetch_data("SELECT i.description FROM orders AS o, invoices AS i WHERE o.id = {$_POST["id"]} AND o.invoice_id = i.id");
		$items = json_decode($description[0]["description"], true);
		foreach($items as $pid => $amount){
			$product = mysql_fetch_data("SELECT amount FROM product_detail WHERE id = {$pid}");
			$new_amount = $product[0]["amount"]+$amount;
			mysql_query("UPDATE `product_detail` SET `amount`={$new_amount} WHERE id = {$pid}");
		}
		mysql_query("UPDATE `orders` SET `status`='cancelled' WHERE id = {$_POST["id"]}");
		send_mail_client("ยกเลิกคำสั่งซื้อ", "ยกเลิกคำสั่งซื้อ #{$_POST["id"]}");
		send_mail_admin("ยกเลิกคำสั่งซื้อ", "ยกเลิกคำสั่งซื้อ #{$_POST["id"]}");
		exit;
	}
$orders = mysql_fetch_data("
			SELECT 
				o.id
				, o.date_created
				, o.status AS order_status
				, i.status AS invoice_status
				, i.total
				, i.description
				, o.tracking
			FROM 
				orders AS o
				, invoices AS i
			WHERE
				o.uid = {$_SESSION["user_login"]["id"]}
				AND o.invoice_id = i.id
			ORDER BY
				FIELD (i.status, 'pending', 'pending_payment_confirm', 'payment_transport', 'transported')
");

foreach($orders as $index => $order){
	$orders[$index]["items"] = json_decode($order["description"], true); 
	foreach($orders[$index]["items"] as $id => $amount){
		$title = mysql_fetch_data("SELECT sub_cat_id, title FROM product_detail WHERE id = {$id}");
		$orders[$index]["items"][$id] = array(
			"title" => $title[0]["title"]
			, "amount" => $amount
			, "sub_cat_id" => $title[0]["sub_cat_id"]
		);
	}
}
$count = count($orders);

?>

<div class="row">
	<div class="col-md-12">
		<div>
			<h3>คำสั่งซื้อทั้งหมด <?php echo $count; ?> รายการ</h3>
		</div>
		<?php if($count > 0){ ?>
		<table class="table" style="text-align: center;">
			<tr>
				<td>หมายเลขสั่งซื้อ</td>
				<td>รายละเอียด</td>
				<td>ยอดเงิน</td>
				<td>สถานะ</td>
				<td>เลขพัสดุ</td>
				<td></td>
			</tr>
			<?php foreach($orders as $o){ ?>
			<tr>
				<td><?php echo $o["id"]; ?></td>
				<td>
				<?php foreach($o["items"] as $id => $info){ ?>
					<p><a href="products/<?php echo $info["sub_cat_id"]; ?>/product/<?php echo $id; ?>" target="_blank"><?php echo $info["title"] . '</a> x ' . $info["amount"]; ?></p>
				<?php } ?>
				</td>
				<td><?php echo numberDecimal($o["total"]); ?></td>
				<td>
				<?php
					if($o["order_status"] == "cancelled"){
						echo "<p style='color: #ff0000;'>ยกเลิก</p>";
					} else if($o["invoice_status"] == "pending"){
						echo "<p style='color: #898989;'>รอชำระเงิน</p>";
					} else if($o["invoice_status"] == "pending_payment_confirm"){
						echo "<p style='color: #898989;'>รอยืนยันยอดโอน</p>";
					} else if($o["invoice_status"] == "pending_transport"){
						echo "<p style='color: #898989;'>รอจัดส่งสินค้า</p>";
					} else if($o["invoice_status"] == "transported"){
						echo "<p style='color: #00ff00;'>ส่งสินค้าแล้ว</p>";
					}
				?>
				</td>
				<td><?php echo ($o["tracking"] == "") ? '-' : $o["tracking"]; ?></td>
				<td>
					<?php if($o["invoice_status"] == "pending" && $o["order_status"] != "cancelled") { ?>
					<a href="payment-confirm/<?php echo $o["id"]; ?>/" class='btn btn-info'>แจ้งชำระเงิน</a>
					<br><br>
					<button class='btn btn-danger' onclick="if(confirm('ต้องการยกเลิกคำสั่งซื้อนี้หรือไม่?')){ cancelOrder(<?php echo $o["id"]; ?>) }">ยกเลิกคำสั่งซื้อ</button>
					<?php } ?>
				</td>
			</tr>
			<?php } ?>
		</table>
		<?php } ?>
	</div>
</div>
<?php } ?>