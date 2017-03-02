<?php
$cat_id = $_GET["id"];
$cat_name = mysql_fetch_data("SELECT name FROM product_categories WHERE id = {$cat_id}");
$cat_name = $cat_name[0]["name"];

if(isset($_POST["action"]) && $_POST["action"] != ""){
	switch($_POST["action"]){
		case "remove":
			mysql_query("DELETE FROM `product_categories` WHERE id = {$_POST["id"]}");
			break;
	}
	exit;
}
if(isset($_POST["new_sub_cat"]) && trim($_POST["new_sub_cat"]) != ""){
	$categories_size = mysql_fetch_data("SELECT * FROM product_sub_categories", true);
	$categories_size++;
	$new_cat = generate_insert_text($_POST["new_sub_cat"]);
	mysql_query("INSERT INTO `product_sub_categories`(`name`, `sort`, `cat_id`) VALUES ('{$new_cat}','{$categories_size}','{$cat_id}')");
}
$categories = mysql_fetch_data("SELECT * FROM product_sub_categories WHERE cat_id = {$cat_id} ORDER BY sort");
echo <<<EOF
<div class="container">
	<form action="#" method="POST">
EOF;

if(count($categories) > 0){
	echo <<<EOF
		<div class="row">
			<div class="col-xs-6">
				<p>หมวดหมู่</p>
			</div>
			<div class="col-xs-1">
				<p>ลำดับแสดง</p>
			</div>
			<div class="col-xs-1">
			</div>
		</div>
EOF;
}
foreach($categories as $foo){
	echo <<<EOF
		<div class="row mt20">
			<div class="col-xs-6">
				<a href="?id={$foo["id"]}">{$foo["name"]}</a>
			</div>
			<div class="col-xs-1">
				<select name="">
EOF;
	for($i = 1; $i < count($categories)+1; $i++){
		$select = ($foo["sort"] == $i) ? "selected" : "";
		echo "<option value=\"{$i}\" {$select}>{$i}</option>";
	}

	echo <<<EOF
				</select>
			</div>
			<div class="col-xs-1">
				<button class="btn btn-danger" onclick="if(confirm('คุณต้องการลบหมวดหมู่นี้ หรือไม่?')){ remove_sub_cat('{$foo["id"]}'); }" type="button">ลบ</button>
			</div>
		</div>
EOF;
}
echo <<<EOF
		<div class="row mt30">
			<div class="col-xs-7">
				<input type="text" name="new_sub_cat" class="form-control" />
			</div>
			<div class="col-xs-1">
				<button class="btn btn-primary" type="submit">เพิ่มหมวดหมู่รอง</button>
			</div>
		</div>
	</form>
</div>
EOF;
?>
<script type="text/javascript">
function remove_sub_cat(id)
{
	$.post("management", {action: 'remove_sub_cat', id: id}).done(function(data){
		location.reload();
	});
}
</script>