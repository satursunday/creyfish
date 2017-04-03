<?php
if(isset($_POST["action"]) && $_POST["action"] != ""){
	switch($_POST["action"]){
		case "confirm":
			$invoice = mysql_fetch_data("SELECT invoice_id FROM orders WHERE id = {$_POST["id"]}");
			mysql_query("UPDATE `invoices` SET `status`='pending_transport' WHERE id = {$invoice[0]["invoice_id"]}");
			send_mail_client("ยืนยันยอดโอน", "ยืนยันการแจ้งโอนเงิน คำสั่งซื้อ#{$_POST["id"]}");
			break;
		case "reject":
			$invoice = mysql_fetch_data("SELECT invoice_id FROM orders WHERE id = {$_POST["id"]}");
			mysql_query("UPDATE `invoices` SET `status`='pending' WHERE id = {$invoice[0]["invoice_id"]}");
			$img_location = mysql_fetch_data("SELECT image FROM payment_transfer WHERE order_id = {$_POST["id"]}");
			unlink(dirname(__FILE__) . platformSlashes("/../images/transfer/{$img_location[0]["image"]}"));
			mysql_query("DELETE FROM `payment_transfer` WHERE order_id = {$_POST["id"]}");
			send_mail_client("ยอดโอนเกิดข้อผิดพลาด", "คำสั่งซื้อ#{$_POST["id"]}<br><br>การแจ้งโอนเงินไม่ถูกต้อง กรุณาทำรายการใหม่");
			break;
		case "add-tracking-no":
			$invoice = mysql_fetch_data("SELECT invoice_id FROM orders WHERE id = {$_POST["orderId"]}");
			mysql_query("UPDATE `invoices` SET `status`='transported' WHERE id = {$invoice[0]["invoice_id"]}");
			mysql_query("UPDATE `orders` SET `status`='completed', `tracking`= '{$_POST["tracking"]}' WHERE id = {$_POST["orderId"]}");
			send_mail_client("ส่งสินค้า", "ยืนยันการส่งสินค้า คำสั่งซื้อ#{$_POST["orderId"]}<br><b>Tracking NO.</b> : {$_POST["tracking"]}");
			break;
	}
}
$payment_confirm_list = mysql_fetch_data("
	SELECT
		t.total_transfer
		, t.image
		, t.date_transfer
		, o.date_created
		, i.total
		, i.description
		, o.id
	FROM
		orders AS o
		, invoices AS i
		, payment_transfer AS t
	WHERE
		o.invoice_id = i.id
		AND i.status = 'pending_payment_confirm'
		AND o.id = t.order_id
");

$tracking_list = mysql_fetch_data("
	SELECT
		t.total_transfer
		, t.image
		, t.date_transfer
		, o.date_created
		, i.total
		, i.description
		, o.id
	FROM
		orders AS o
		, invoices AS i
		, payment_transfer AS t
	WHERE
		o.invoice_id = i.id
		AND i.status = 'pending_transport'
		AND o.id = t.order_id
");
?>

<div class="row">
	<?php if(count($payment_confirm_list) > 0) { ?>
	<div class="col-md-12">
		<h3>รายการคำสั่งซื้อที่รอการยืนยันยอดโอนเงิน</h3>
		<table class="table" style="text-align: center;">
			<tr>
				<td><b>คำสั่งซื้อ #</b></td>
				<td><b>รายละเอียด</b></td>
				<td><b>แจ้งโอนเมื่อ</b></td>
				<td><b>ยอดโอน</b></td>
				<td><b>ยอดรวม</b></td>
				<td><b>หลักฐานการโอน</b></td>
				<td></td>
			</tr>
			<?php foreach($payment_confirm_list as $o){ ?>
			<?php 
			$description = "";
			$o["description"] = json_decode($o["description"], true);
			foreach($o["description"] as $pid => $d){
				$ptitle = mysql_fetch_data("SELECT sub_cat_id, title from product_detail WHERE id = {$pid}");
				$description .= "<p><a href=\"products/{$ptitle[0]["sub_cat_id"]}/product/{$pid}/\" target=\"_blank\">{$ptitle[0]["title"]}</a> x {$d}</p>";				
			}
			$total_transfer = number_format($o["total_transfer"], 2, ".", ",");
			$total = number_format($o["total"], 2, ".", ",");
			echo <<<EOF
			<tr>
				<td style="vertical-align: middle;">{$o["id"]}</td>
				<td style="vertical-align: middle;">{$description}</td>
				<td style="vertical-align: middle;">{$o["date_transfer"]}</td>
				<td style="vertical-align: middle;">{$total_transfer}</td>
				<td style="vertical-align: middle;">{$total}</td>
				<td><img style="width: 100%;"src="images/transfer/{$o["image"]}" /></td>
				<td style="vertical-align: middle;">
					<button class="btn btn-success" onclick="if(confirm('ยืนยันยอดโอนนี้หรือไม่?')){ confirmPayment({$o["id"]}); }">ยืนยันยอดโอน</button>
					<br><br>
					<button class="btn btn-danger" onclick="if(confirm('ต้องการยกเลิกยอดโอนนี้หรือไม่?')){ rejectPayment({$o["id"]}); }">ยอดโอนไม่ถูกต้อง</button>
				</td>
			</tr>
EOF;
			?>
			<?php } ?>
		</table>
	</div>
	<?php } ?>
	<?php if(count($tracking_list) > 0) { ?>
	<div class="col-md-12">
		<h3>รายการคำสั่งซื้อที่รอการส่งสินค้า</h3>
		<table class="table" style="text-align: center;">
			<tr>
				<td><b>คำสั่งซื้อ #</b></td>
				<td><b>รายละเอียด</b></td>
				<td><b>แจ้งโอนเมื่อ</b></td>
				<td><b>ยอดโอน</b></td>
				<td><b>ยอดรวม</b></td>
				<td><b>เลขพัสดุ</b></td>
				<td></td>
			</tr>
			<?php foreach($tracking_list as $o){ ?>
			<?php 
			$description = "";
			$o["description"] = json_decode($o["description"], true);
			foreach($o["description"] as $pid => $d){
				$ptitle = mysql_fetch_data("SELECT sub_cat_id, title from product_detail WHERE id = {$pid}");
				$description .= "<p><a href=\"products/{$ptitle[0]["sub_cat_id"]}/product/{$pid}/\" target=\"_blank\">{$ptitle[0]["title"]}</a> x {$d}</p>";				
			}
			$total_transfer = number_format($o["total_transfer"], 2, ".", ",");
			$total = number_format($o["total"], 2, ".", ",");
			echo <<<EOF
			<form action="staff" method="POST">
			<input type="hidden" name="orderId" value="{$o["id"]}" />
			<input type="hidden" name="action" value="add-tracking-no" />
			<tr>
				<td style="vertical-align: middle;">{$o["id"]}</td>
				<td style="vertical-align: middle;">{$description}</td>
				<td style="vertical-align: middle;">{$o["date_transfer"]}</td>
				<td style="vertical-align: middle;">{$total_transfer}</td>
				<td style="vertical-align: middle;">{$total}</td>
				<td style="vertical-align: middle;"><input type="text" name="tracking" /></td>
				<td style="vertical-align: middle;"><input class="btn btn-success" type="submit" value="ส่งสินค้า" /></td>
			</tr>
			</form>
EOF;
			?>
			<?php } ?>
		</table>
	</div>
	<?php } ?>
	<?php if(count($payment_confirm_list) == 0 && count($tracking_list) == 0) { ?>
	<div class="col-md-12" align="center">
		<h3><font color="#ff0000;">ไม่มีคำสั่งซื้อที่รอจัดการ</font></h3>
	</div>
	<?php } ?>
</div>