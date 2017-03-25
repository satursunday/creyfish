<?php
$sub_cat_id = $_GET["id"];
$products = mysql_fetch_data("
	SELECT
		*
	FROM
		product_detail AS p
	WHERE
		p.sub_cat_id = {$sub_cat_id}
");

for($i = 1; $i < 12; $i++){
	array_push($products, $products[0]);
}

?>

<?php if(empty($_GET["pid"])) {?>
<div class="row">
	<?php foreach($products as $foo) { ?>
	<?php $img = json_decode($foo["img"], true); ?>
	<div class="col-xs-4 mb-20">
		<div class="col-xs-11 p-box">	
			<p class="p-box-title"><b><?php echo $foo["title"]; ?></b></p>
			<img src="<?php echo $img_path . $img[0]; ?>" width="100%" />
			<p class="mt-10">คงเหลือ : <?php echo $foo["amount"]-$foo["sell"]; ?></p>
			<div align="center" class="mt-20">
				<a class="btn btn-default" onclick="window.location = 'products/<?php echo $sub_cat_id; ?>/product/<?php echo $foo["id"]; ?>';" style="margin-right: 24px;">เพิ่มเติม</a>
				<a class="btn btn-danger">สั่งซื้อ</a>
			</div>
		</div>
	</div>
	<?php } ?>
</div>
<?php } else { ?>
<?php 
	$pid = $_GET["pid"];
	$product = mysql_fetch_data("SELECT * FROM product_detail WHERE id = {$pid}");
	$product = $product[0];
	$img = json_decode($product["img"], true);
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
		<div id="slides">
			<?php for($i = 0; $i < count($img); $i++){ ?>
			<img src="<?php echo $img_path . $img[$i]; ?>" />
			<?php } ?>
		</div>
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
				<td>คงเหลือ</td>
				<td><?php echo $product["amount"]-$product["sell"]; ?></td>
			</tr>
			<tr>
				<td>ราคา</td>
				<td><?php echo $product["price"]; ?></td>
			</tr>
		</table>
		<div align="center" class="mt-30">
			<a class="btn btn-danger">สั่งซื้อ</a>
		</div>
	</div>
</div>
<?php } ?>