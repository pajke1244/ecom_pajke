<?php 
require_once("../../config.php");
if (isset($_GET['id'])) {
	$id = $_GET['id'];
	$query=query("DELETE FROM orders where order_id=" . escape_string($id) . " ");
	confirm($query);
	set_message("ORDER DELETED");
	redirect("../../../public/admin/index.php?orders");
}else{
	redirect("../../../public/admin/index.php?orders");
}


?>