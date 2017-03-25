<script src="js/updatefile.js"></script>
<?php
$cat_id = $_GET["cat_id"];
$sub_cat_id = $_GET["sub_cat_id"];
$products = mysql_fetch_data("SELECT * FROM product_detail");
if(isset($_POST["product-name"])){
	$date = date("d-M-Y H:i:s");
	$img = array();
	foreach($_FILES as $foo){
		if($foo["size"] > 0){
			$file_content = file_get_contents($foo["tmp_name"]);
			preg_match_all('/.*\\\tmp\\\(.*\.tmp)/', $foo["tmp_name"], $tmp_name, PREG_SET_ORDER);
			$tmp_name = str_replace('.tmp', '', $tmp_name[0][1]);
			$img_path = dirname(__FILE__) . '\\..\\images\\products\\' . $tmp_name . '_' . $foo["name"];
			file_put_contents($img_path, $file_content);	
			array_push($img, $tmp_name . '_' . $foo["name"]);
		}
	}
	$img = json_encode($img);
	mysql_query("INSERT INTO `product_detail`(`sub_cat_id`, `title`, `description`, `img`, `amount`, `sell`, `price`, `date_create`) VALUES ('{$sub_cat_id}', {$_POST['product-name']}', '{$_POST['description']}', '{$img}', '{$_POST['amount']}', 0, '{$_POST['price']}', NOW())");
}
?>
<?php if(empty($_GET["pid"])){ ?>
<table width="100%">
	<col width="80%" />
	<col width="20%" />
	<?php foreach($products as $p){ ?>
	<tr>
		<td style="padding: 10px; 20px;">
		<a href="<?php echo "management/{$cat_id}/sub/{$sub_cat_id}/product/{$p["id"]}/"; ?>"><?php echo $p["title"]; ?></a>
		</td>
		<td>
		<button onclick="if(confirm('คุณต้องการลบหมวดหมู่นี้ หรือไม่?')){ remove_product('<?php echo $p["id"]; ?>'); }" class="btn btn-danger">ลบ</button>
		</td>
	</tr>
	<?php } ?>
</table>
	
<div class="col-md-12" style="border: 1px solid #DDDDDD; border-radius: 5px; margin-top: 30px;">
	<form action="management/<?php echo $cat_id; ?>/sub/<?php echo $sub_cat_id; ?>/" method="POST" enctype="multipart/form-data">
		<h3 class="dark-grey">เพิ่มสินค้า</h3>
			
		<div class="form-group col-xs-12">
			<label>ชื่อสินค้า</label>
			<input type="" name="product-name" class="form-control" />
		</div>
		
		<?php for($i = 1; $i <= 5; $i++){ ?>
		<div class="form-group col-xs-12">
        	<label>รูปสินค้า <?php echo $i; ?></label>
	        <div class="input-group">
	            <span class="input-group-btn">
	                <span class="btn btn-default btn-file">
	                    Browse… <input type="file" name="product-img-<?php echo $i; ?>" id="imgInp<?php echo $i; ?>">
	                </span>
	            </span>
	            <input type="text" class="form-control" readonly>
	        </div>
	        <img width="50%" id='img-upload-<?php echo $i; ?>'/>
    	</div>
    	<?php } ?>
    	
		<div class="form-group col-xs-12">
			<label>รายละเอียด</label>
			<textarea name="description" style="height: 200px;" class="form-control" ></textarea>
		</div>
		
		<div class="form-group col-lg-6">
			<label>จำนวน</label>
			<input type="number" name="amount" class="form-control" />
		</div>
						
		<div class="form-group col-lg-6">
			<label>ราคา</label>
			<input type="number" name="price" class="form-control" step="0.01" />
		</div>
		
		<div class="form-group col-xs-12">
			<input class="btn btn-success" type="submit" value="เพิ่ม" />
		</div>
	</form>
</div>
<?php } else { ?>
<?php 
	$pid = $_GET["pid"];
	$product = mysql_fetch_data("SELECT * FROM product_detail WHERE id = '{$pid}'");
	$product = $product[0];
	$img = json_decode($product["img"], true);
?>
<table width="100%">
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
	<?php foreach($img as $i => $fi){ ?>
	<tr>
		<td>รูปสินค้า <?php echo $i+1; ?></td>
		<td><img src="<?php echo $img_path . $fi; ?>" /></td>
	</tr>
	<tr>
	<?php } ?>
		<td>คงเหลือ</td>
		<td><?php echo ($product["amount"]-$product["sell"] > 0) ? $product["amount"]-$product["sell"] : '<font color="red">0</font>'; ?></td>
	</tr>
	<tr>
		<td>ราคา</td>
		<td><?php echo $product["price"]; ?></td>
	</tr>
	<tr>
		<td>สร้างเมื่อ</td>
		<td><?php echo $product["date_create"]; ?></td>
	</tr>
</table>
<?php } ?>

<script type="text/javascript">
function remove_product(pid)
{
	$.post("management", {action: 'remove_product', pid: pid}).done(function(data){
		location.reload();
	});
}
</script>