<?php
if(isset($_POST["id"]) && $_POST["id"] != ""){
	$total = mysql_fetch_data("
		SELECT 
			i.total
		FROM
			orders AS o
			, invoices AS i
		WHERE
			o.id = {$_POST["id"]}
			AND o.invoice_id = i.id
	");
	echo json_encode(array('total' => number_format($total[0]["total"], 2, ".", ",")));
	exit;
} else if(isset($_POST["order_id"])){
	$date = date("d-m-Y H:i:s", strtotime($_POST["transfer"]));
	$img = array();
	if($_FILES["confirm-img"]["size"] > 0){
		$file_content = file_get_contents($_FILES["confirm-img"]["tmp_name"]);
		preg_match_all('/' . platformSlashes('.*\/tmp\/(.*(\.tmp)?)') . '/', $_FILES["confirm-img"]["tmp_name"], $tmp_name, PREG_SET_ORDER);
		$tmp_name = str_replace('.tmp', '', $tmp_name[0][1]);
		$img_path = dirname(__FILE__) . platformSlashes('/../images/transfer/') . $tmp_name . '_' . $_FILES["confirm-img"]["name"];
		file_put_contents($img_path, $file_content);
		$file = $tmp_name . '_' . $_FILES["confirm-img"]["name"];
		mysql_query("
			INSERT INTO 
				`payment_transfer`(
					`order_id`
					, `total_transfer`
					, `date_transfer`
					, `date_created`
					, `image`
				) VALUES (
					{$_POST["order_id"]}
					,{$_POST["transfer"]}
					,'{$_POST["date"]}'
					,NOW()
					,'{$file}'
				)
		");
		
		$invoice_id = mysql_fetch_data("SELECT invoice_id FROM orders WHERE id = {$_POST["order_id"]}");
		
		mysql_query("UPDATE `invoices` SET `status`='pending_payment_confirm' WHERE id = {$invoice_id[0]["invoice_id"]}");
		send_mail_client("ยืนยันการแจ้งโอน", "ยืนยันการโอนเงิน ใบสั่งซื้อ #{$_POST["order_id"]}");
		send_mail_admin("แจ้งยอดโอน", "แจ้งยอดโอนเงิน #{$_POST["order_id"]}");
		do_redirect("orderlist");
	}
}

if(check_login()){
	$orders = mysql_fetch_data("
			SELECT 
				o.*
				, i.total
			FROM
				orders AS o
				, invoices AS i
			WHERE
				o.status = 'pending'
				AND o.invoice_id = i.id
				AND i.status = 'pending'
				AND o.uid = {$_SESSION["user_login"]["id"]}
				AND o.id NOT IN(
					SELECT order_id AS id FROM payment_transfer AS t
				)
	");
} else {
	$orders = array();
}

if(isset($_GET["id"])){
	foreach($orders as $ind => $foo){
		if($foo["id"] == $_GET["id"]){
			$index = $ind;
			break;
		}
	}
	$id = $_GET["id"];
} else {
	$id = 0;
}

?>
<?php if (count($orders) > 0) { ?>
<script src="js/updatefile.js"></script>
<div class="row">
	<div class="col-md-12">
		<div align='center'>
		<?php if(count($orders) > 0){ ?>
			<form method="post" action="payment-confirm" enctype="multipart/form-data">
				<table class="table" style="width: 70%;">
					<tr>
						<td><b>หมายเลขคำสั่งซื้อ</b></td>
						<td>
							<select id="order" name="order_id" onchange="changeConfirm($(this).val());">
						<?php foreach($orders as $c => $foo){ ?>
								<option <?php if($foo["id"] == $id) echo "selected"; ?> value="<?php echo $foo["id"]; ?>">#<?php echo $foo["id"]; ?></option>
						<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><b>เป็นเงิน</b></td>
						<td><span id="total"><?php if($id == 0) echo number_format($orders[0]["total"], 2, ".", ","); else echo number_format($orders[$index]["total"], 2, '.', ','); ?></span> บาท</td>
					</tr>
					<tr>
						<td><b>ยอดโอน</b></td>
						<td><input class="form-control" type="number" name="transfer" min="0.00" step="0.01" /></td>
					</tr>
					<tr>
						<td><b>หลักฐานการโอน</b></td>
						<td>
							<div class="input-group">
					            <span class="input-group-btn">
					                <span class="btn btn-default btn-file">
					                    Browse… <input type="file" name="confirm-img" id="imgInp1">
					                </span>
					            </span>
					            <input type="text" class="form-control" readonly>
					        </div>
					        <img width="50%" id='img-upload-1'/>
						</td>
					</tr>
					<tr>
						<td><b>วันที่</b></td>
						<td>
							<div class="input-group date form_datetime col-md-5" data-date="<?php echo date("Y-m-d H:i:s"); ?>" data-date-format="yyyy-mm-dd hh:ii:ss" data-link-field="dtp_input1" style="width: 100%;">
	                    		<input class="form-control" name="date" size="16" type="text" value="<?php echo date("Y-m-d H:i:s"); ?>" readonly>
	                    		<span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
								<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
	                		</div>
						</td>
					</tr>
					<tr>
						<td colspan="2"><div align="center"><input type="submit" class="btn btn-success" value="แจ้งโอน" /></div></td>
					</tr>
				</table>
			</form>
			<script type="text/javascript">
			$('.form_datetime').datetimepicker({
		        weekStart: 1,
		        todayBtn:  1,
				autoclose: 1,
				todayHighlight: 1,
				startView: 2,
				forceParse: 0,
		        showMeridian: 1,
		        minuteStep: 1
		    });
		    </script>	
		<?php } else { ?>
			<h3><font color='#ff0000'>ไม่พบรายการสั่งซื้อ</font></h3>
		<?php } ?>
		</div>
	</div>
</div>
<?php } else if(count($orders) == 0){ ?>
<div class="row">
	<div class="col-md-12" align="center">
		<h3><font color='#ff0000'>ไม่พบรายการสั่งซื้อ</font></h3>
	</div>
</div>
<?php } ?>