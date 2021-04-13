<?php 
require_once("../../config.php");
if (isset($_GET['id'])) {
	$id = $_GET['id'];
	$query=query("DELETE FROM products where product_id=" . escape_string($id) . " ");
	confirm($query);
	set_message("PRODUCT DELETED");
	redirect("../../../public/admin/index.php?products");
}else{
	redirect("../../../public/admin/index.php?products");
}


?>