<?php
$sub_cat_id = $_GET["id"];
$products = mysql_fetch_data("
	SELECT
		*
	FROM
		product_detail AS p
	WHERE
		p.sub_cat_id = {$sub_cat_id}
	ORDER BY
		date_created DESC
		, amount DESC
");


?>

<?php if(empty($_GET["pid"])) {?>
<div class="row">
	<?php foreach($products as $foo) { ?>
	<?php $img = json_decode($foo["img"], true); ?>
	<?php $sold_out = ($foo["amount"] <= 0) ? true : false; ?>
	<div class="col-xs-4 mb-20">
		<div class="col-xs-11 p-box">
			<?php if($sold_out) { ?>
			<img src="images/sold-out.png" class="p-sold-out"/>
			<?php } ?>
			<p class="p-box-title"><b><?php echo $foo["title"]; ?></b></p>
			<img src="<?php echo $img_path . $img[0]; ?>" width="100%" height="100" />
			<p class="mt-10">คงเหลือ : <?php echo $foo["amount"]; ?></p>
			<div align="center" class="mt-20">
				<a class="btn btn-default" href="products/<?php echo $sub_cat_id; ?>/product/<?php echo $foo["id"]; ?>">เพิ่มเติม</a>
				<?php if(!$sold_out) { ?>
				<a class="btn btn-danger" onclick="addToCart(<?php echo $product["id"]; ?>);" style="margin-left: 24px;">ใส่ลงตะกร้า</a>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php } ?>
</div>
<?php } else { ?>
<?php 
	$sub_cat_id = $_GET["id"];
	$pid = $_GET["pid"];
	$product = mysql_fetch_data("SELECT * FROM product_detail WHERE id = {$pid}");
	$product = $product[0];
	$img = json_decode($product["img"], true);
	$swap = (count($img) > 1) ? true : false;
?>

<script>
$(function() {
	$('#slides').slidesjs({
		width: 940,
		height: 528,
		play: {
			active: true,
			auto: true,
			interval: 5000,
			swap: true
		}
	});
});
</script>

<div class="row">
	<div class="col-md-12 mb-30">
		<?php if($swap) { ?>
		<div id="slides">
			<?php for($i = 0; $i < count($img); $i++){ ?>
			<img src="<?php echo $img_path . $img[$i]; ?>" />
			<?php } ?>
		</div>
		<?php } else { ?>
		<img src="<?php echo $img_path . $img[0]; ?>" width="100%" />
		<?php } ?>
	</div>
	<div class="col-md-12">
		<table class="p-description" width="75%" border="1" align="center">
			<col width="20%" />
			<col width="80%" />
			<tr>
				<td>ชื่อสินค้า</td>
				<td><?php echo $product["title"]; ?></td>
			</tr>
			<tr>
				<td>รายละเอียด</td>
				<td><?php echo $product["description"]; ?></td>
			</tr>
			<tr>
				<td>ลงขายเมื่อ</td>
				<td><?php echo $product["date_created"]; ?> น.</td>
			</tr>
			<tr>
				<td>คงเหลือ</td>
				<td><?php echo $product["amount"]; ?></td>
			</tr>
			<tr>
				<td>ราคา</td>
				<td><?php echo $product["price"]; ?></td>
			</tr>
		</table>
		<div align="center" class="mt-30">
			<a href="products/<?php echo $sub_cat_id; ?>/" class="btn btn-success">ย้อนกลับ</a>
			<?php if($product["amount"] > 0) { ?>
			<a onclick="addToCart(<?php echo $product["id"]; ?>);" class="btn btn-danger" style="margin-left: 20px;">ใส่ลงตะกร้า</a>
			<?php } ?>
		</div>
	</div>
</div>
<?php } ?>