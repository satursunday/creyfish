<script src="js/updatefile.js"></script>
<?php
$cat_id = $_GET["cat_id"];
$sub_cat_id = $_GET["sub_cat_id"];
if(isset($_POST["product-name"])){
	pprint($_POST);
	pprint($_FILES);
}
?>
<div class="col-md-12" style="border: 1px solid #DDDDDD; border-radius: 5px;">
	<form action="#" method="POST" enctype="multipart/form-data">
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
			<input type="number" name="price" class="form-control" />
		</div>
		
		<div class="form-group col-xs-12">
			<input class="btn btn-success" type="submit" value="เพิ่ม" />
		</div>
	</form>
</div>
