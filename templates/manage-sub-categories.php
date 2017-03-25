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
	$categories_size = mysql_fetch_data("SELECT * FROM product_sub_categories WHERE cat_id = {$cat_id}", true);
	$categories_size++;
	$new_cat = generate_insert_text($_POST["new_sub_cat"]);
	mysql_query("INSERT INTO `product_sub_categories`(`name`, `sort`, `cat_id`) VALUES ('{$new_cat}','{$categories_size}','{$cat_id}')");
}
$categories = mysql_fetch_data("SELECT * FROM product_sub_categories WHERE cat_id = {$cat_id} ORDER BY sort");
echo <<<EOF
<form action="management/{$cat_id}/" method="POST">
EOF;

if(count($categories) > 0){
	echo <<<EOF
	<div class="row">
		<div class="col-xs-8">
			<p>หมวดหมู่</p>
		</div>
		<div class="col-xs-2">
			<p>ลำดับแสดง</p>
		</div>
		<div class="col-xs-2">
		</div>
	</div>
EOF;
}
foreach($categories as $foo){
	echo <<<EOF
	<div class="row mt20">
		<div class="col-xs-8">
			<a class="newline-word" href="management/{$cat_id}/sub/{$foo["id"]}/">{$foo["name"]}</a>
		</div>
		<div class="col-xs-2">
			<table>
				<col width="80%" />
				<col width="20%" />
				<tr>
					<td>
						<input class="form-control" onchange="$('#sort_btn_{$foo["id"]}').show();" style="width: 100%;" type="text" id="sort_{$foo["id"]}" name="sort" value="{$foo["sort"]}" />
					</td>
					<td>
						<a href="javascript:void(0);" onclick="sort_save({$foo["id"]});"><i class="fa fa-floppy-o" id="sort_btn_{$foo["id"]}" style="color: #0000ff; margin-left: 10px; display: none;" aria-hidden="true"></i></a>
					</td>
				</tr>
			</table>
		</div>
		<div class="col-xs-2">
			<button class="btn btn-danger" onclick="if(confirm('คุณต้องการลบหมวดหมู่นี้ หรือไม่?')){ remove_sub_cat('{$foo["id"]}'); }" type="button">ลบ</button>
		</div>
	</div>
EOF;
}
echo <<<EOF
	<div class="row mt30">
		<div class="col-xs-10">
			<input type="text" name="new_sub_cat" class="form-control" />
		</div>
		<div class="col-xs-2">
			<button class="btn btn-primary" type="submit">เพิ่มหมวดหมู่รอง</button>
		</div>
	</div>
</form>
EOF;
?>
<script type="text/javascript">
function remove_sub_cat(id)
{
	$.post("management", {action: 'remove_sub_cat', id: id}).done(function(data){
		location.reload();
	});
}
function sort_save(id, that)
{
	$.post("management", {action: 'save_sort_sub_cat', id: id, sort: $("#sort_" + id).val()}).done(function(data){
		location.reload();
	});
}
</script>