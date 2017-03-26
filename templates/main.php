<?php
$cat = mysql_fetch_data("SELECT * FROM product_categories ORDER BY id");

foreach($cat as $c){
	$sub_cat = mysql_fetch_data("SELECT * FROM product_sub_categories WHERE cat_id = {$c["id"]} ORDER BY id");
	foreach($sub_cat as $s){
		$products = mysql_fetch_data("SELECT * FROM product_detail WHERE sub_cat_id = {$s["id"]} AND amount > 0 ORDER BY date_created DESC, amount DESC LIMIT 0, 3");
		echo <<<EOF
		<div class="row">
			<div class="col-md-12">
				<div class="col-md-12" style="background-color: #333; color: #fff; border-radius: 7px;">
					<p style="font-size: 18px; padding-top: 10px;">{$c["name"]} > {$s["name"]}<span style="float: right;"><a href="products/{$s["id"]}/">ดูสินค้าทั้งหมด</a></span></p>
				</div>
			</div>
			<div class="col-md-12">
				<div class="col-md-12" style="padding-top: 20px; background-color: #DDD;">
EOF;
		foreach($products as $p){
			$img = json_decode($p["img"], true);
			echo <<<EOF
				<div class="col-xs-4 mb-20">
					<div class="col-xs-11 p-box" style="background-color: #fff;">
						<p class="p-box-title"><b>{$p["title"]}</b></p>
						<img src="{$img_path}{$img[0]}" width="100%" height="100" />
						<p class="mt-10">คงเหลือ : {$p["amount"]}</p>
						<div align="center" class="mt-20">
							<a class="btn btn-default" href="products/{$s["id"]}/product/{$p["id"]}/">เพิ่มเติม</a>
							<a class="btn btn-danger" onclick="addToCart({$p["id"]});" style="margin-left: 24px;">ใส่ลงตะกร้า</a>
						</div>
					</div>
				</div>
EOF;
		}
		echo '</div>';
		echo '<div class="clearit"></div>';
		echo '<hr />';
		echo '</div>';
		echo '</div>';
	}	
}
