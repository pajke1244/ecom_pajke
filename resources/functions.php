<?php 

//helper functions

$uploads = "uploads";

function last_id(){
	global $connection;
	return mysqli_insert_id($connection);
	

}


//funkcija za setovanje poruke
function set_message($msg){
	if (!empty($msg)) {
		$_SESSION['message']=$msg;
	}else{

		$msg="";
	}
}

//function za ispis obavestenja

function display_message(){
	if (isset($_SESSION['message'])) {

		echo $_SESSION['message'];
		unset($_SESSION['message']);
	}
}

//funkcija za redirekciju, prosledjujemo lokaciju
function redirect($location){
	return header("Location: $location");
}

//funckija za query!!!
function query($sql){

	global $connection;
	return mysqli_query($connection,$sql);
}

//funkcija za potvrdu
function confirm($result){
	global $connection;
	if (!$result) {
		die("QUERY FAILED " . mysqli_error($connection));
	}
}

//funkcija escape za sigurnije logovanje

function escape_string($string){

	global $connection;

	return mysqli_real_escape_string($connection,$string);
}

function fetch_array($result){

	return mysqli_fetch_array($result);
}
/*********************** FRONT END FUNCTIONS ***************************************************/
//get products

function get_products(){
	$query=query(" SELECT * FROM products");
	confirm($query);
	while ($row=fetch_array($query)) {

		$product_image=display_image($row['product_image']);

		$product = <<<DELIMETER
		<div class="col-sm-4 col-lg-4 col-md-4">
		<div class="thumbnail">
		<a href="item.php?id={$row['product_id']}"><img style="width:300px; height:150px;" src="../resources/{$product_image}" alt="">
		<div class="caption">
		<h4 class="pull-right">&#1044;&#1080;&#1085;&#46; {$row['product_price']}</h4>
		<h4><a href="item.php?id={$row['product_id']}">{$row['product_title']}</a>
		</h4>
		<p>See more snippets like this online store item at <a target="_blank" href="http://www.bootsnipp.com">Bootsnipp - http://bootsnipp.com</a>.</p>
		<a class="btn btn-primary" target="_blank" href="../resources/cart.php?add={$row['product_id']}">Add to cart</a>
		</div>
		</div>
		</div>

		DELIMETER;

		echo $product;
	}

}

//get categories by id
function get_products_cat_page(){
	$query=query(" SELECT * FROM products where product_category_id =". escape_string($_GET['id']) ."");
	confirm($query);
	while ($row=fetch_array($query)) {
		$product_image=display_image($row['product_image']);
		$cat_id = <<<DELIMETER
		<div class="col-md-3 col-sm-6 hero-feature">
		<div class="thumbnail">
		<img style="width:300px; height:150px;" src="../resources/{$product_image}"" alt="">
		<div class="caption">
		<h3>{$row['product_title']}</h3>
		<p>{$row['product_short_desc']}</p>
		<p>
		<a href="../resources/cart.php?add={$row['product_id']}" class="btn btn-primary">Buy Now!</a> <a href="item.php?id={$row['product_id']}" class="btn btn-default">More Info</a>
		</p>
		</div>
		</div>
		</div>

		DELIMETER;

		echo $cat_id;
	}

}



//get categroies
function get_categories(){
	$query= query("SELECT * FROM categories");
	confirm($query);
	while ($row=fetch_array($query)){

		$category_links = <<<DELIMETER
		<a href='category.php?id={$row['cat_id']}' class='list-group-item'>{$row['cat_title']}</a>

		DELIMETER;

		echo $category_links;
	}

}


//get get_products_shop_page prikazujemo proizvodu na shop.php strani
function get_products_shop_page(){
	$query=query(" SELECT * FROM products");
	confirm($query);
	while ($row=fetch_array($query)) {
		$product_image=display_image($row['product_image']);
		$cat_id = <<<DELIMETER
		<div class="col-md-3 col-sm-6 hero-feature">
		<div class="thumbnail">
		<img style="width:300px; height:150px;" src="../resources/{$product_image}" alt="">
		<div class="caption">
		<h3>{$row['product_title']}</h3>
		<p>{$row['product_short_desc']}</p>
		<p>
		<a href="../resources/cart.php?add={$row['product_id']}" class="btn btn-primary">Buy Now!</a> <a href="item.php?id={$row['product_id']}" class="btn btn-default">More Info</a>
		</p>
		</div>
		</div>
		</div>

		DELIMETER;

		echo $cat_id;
	}

}

//funkcija za logovanje!!!
function user_login(){
	if (isset($_POST['submit'])) {
		$username= escape_string($_POST['username']);
		$password= escape_string($_POST['password']);
		$query=query("SELECT * from users where username = '{$username}' and password='{$password}'");
		confirm($query);

		if (mysqli_num_rows($query) == 0 ) {
			set_message("Your password or Username are wrong");
			redirect("login.php");
		}else{
			$_SESSION['username']=$username;
			// set_message("welcome to admin {$username}");
			redirect("admin");
		}

	}

}

//funkcija za slanje poruke
function send_message(){
	if (isset($_POST['send'])) {
		$to= "some@gmail.com";
		$from_name = $_POST['name'];
		$email = $_POST['email'];
		$subject = $_POST['subject'];
		$message = $_POST['message'];

		$headers= "From: {$from_name} {$email}";
		$result = mail($to,$subject,$message, $headers);
		if (!$result) {
			set_message("Sorry we could't send your message");
		}else{
			set_message("Your mesagge has been send");
		}
	}
}
//funckija za poruku dok ne kupim hosting
function send_message_my(){
	if (isset($_POST['send'])) {

		$name =escape_string( $_POST['name']);
		$email = escape_string($_POST['email']);
		$subject =escape_string( $_POST['subject']);
		$message = escape_string($_POST['message']);
		$query=query("INSERT INTO message (msg_name, msg_mail, msg_phone, msg_message) values ('$name', '$email', '$subject', '$message')");

		if ($query) {
			echo "<h1>Uspesno ste poslali poruku</h1>";
		}else{
			echo "<h1>Neuspesno ste poslali poruku</h1>";
		}

	}
}

/*********************** BACK END FUNCTIONS ***************************************************/

function display_orders(){

	$query=query("SELECT * FROM orders");
	confirm($query);
	while ($row=fetch_array($query)) {

		$orders=<<<DELIMETER
		<tr>
		<td>{$row['order_id']}</td>
		<td>{$row['order_amount']}</td>
		<td>{$row['order_tx']}</td>
		<td>{$row['order_currency']}</td>
		<td>{$row['order_status']}</td>
		<td><a class="btn btn-danger" href="../../resources/templates/back/delete_order.php?id={$row['order_id']}"><span class="glyphicon glyphicon-remove"></span></a></td>
		</tr>

		DELIMETER;

		echo $orders;

	}

}

/*************************************************ADMIN PRODUCTS PAGE ****************************************/
//funckija za putanju slike
function display_image($picture){
	global $uploads;
	return $uploads . DS . $picture;

}


function get_products_in_admin(){

	$query=query(" SELECT * FROM products");
	confirm($query);
	while ($row=fetch_array($query)) {
	//promenljiva u koju smestamo cat_title
		$category_name=show_product_category_title($row['product_category_id']);
		$product_image=display_image($row['product_image']);

		$product = <<<DELIMETER
		<tr>
		<td>{$row['product_id']}</td>
		<td>{$row['product_title']} <br>
		<a href="index.php?edit_product&id={$row['product_id']}"><img src="../../resources/{$product_image}" style="width:300px; height:150px;" alt="image"><a/>
		</td>
		<td>{$category_name}</td>
		<td>{$row['product_price']}</td>		
		<td>{$row['product_quantity']}</td>	
		<td><a class="btn btn-danger" href="../../resources/templates/back/delete_product.php?id={$row['product_id']}"><span class="glyphicon glyphicon-remove"></span></a></td>	
		</tr>

		DELIMETER;

		echo $product;
	}



}

function show_product_category_title($product_category_id){

	$category_query=query("SELECT * from categories where cat_id = '{$product_category_id}'");
	confirm($category_query);

	while ($category_row=fetch_array($category_query)) {

		return $category_row['cat_title'];
	}

}

/****************************** ADD PRODUCT in ADMIN **********************************/
function add_product(){
	//gledamo da li je nesto poslato
	if (isset($_POST['publish'])) {
		$product_title=escape_string($_POST['product_title']);
		$product_category_id=escape_string($_POST['product_category_id']);
		$product_price=escape_string($_POST['product_price']);
		$product_description=escape_string($_POST['product_description']);
		$product_short_desc=escape_string($_POST['product_short_desc']);
		$product_quantity=escape_string($_POST['product_quantity']);

		//za sliku.Prvi parametar file je naziv atribura(name) iz forme add_product
		$product_image=$_FILES['file']['name'];
		$image_temp_location=$_FILES['file']['tmp_name'];

		move_uploaded_file($image_temp_location, UPLOAD_DIR . DS . $product_image);

		$query=query("INSERT INTO products (product_title, product_category_id, product_price, product_quantity, product_description, product_short_desc, product_image) values (
			'{$product_title}','{$product_category_id}','{$product_price}','{$product_quantity}','{$product_description}','{$product_short_desc}','{$product_image}');");
		confirm($query);
		$last_id=last_id();
		set_message("New product with id {$last_id} was Added");
		redirect("index.php?products");


	}


}

//updating product code

function update_product(){
	//gledamo da li je nesto poslato
	if (isset($_POST['update'])) {
		$product_title=escape_string($_POST['product_title']);
		$product_category_id=escape_string($_POST['product_category_id']);
		$product_price=escape_string($_POST['product_price']);
		$product_description=escape_string($_POST['product_description']);
		$product_short_desc=escape_string($_POST['product_short_desc']);
		$product_quantity=escape_string($_POST['product_quantity']);

		//za sliku.Prvi parametar file je naziv atribura(name) iz forme add_product
		$product_image=$_FILES['file']['name'];
		$image_temp_location=$_FILES['file']['tmp_name'];

		if (empty($product_image)) {
		   $get_pic = query("SELECT product_image from products where product_id = " . escape_string($_GET['id']) ."");
		   confirm($get_pic);

		   while($pic=fetch_array($get_pic)){
		   	 $product_image= $pic['product_image'];
		   }
		}

		move_uploaded_file($image_temp_location, UPLOAD_DIR . DS . $product_image);

		$query="UPDATE products SET ";
		$query.="product_title = '{$product_title }', ";
		$query.="product_category_id = '{$product_category_id }', ";
		$query.="product_price = '{$product_price }', ";
		$query.="product_description = '{$product_description }', ";
		$query.="product_short_desc = '{$product_short_desc }', ";
		$query.="product_quantity = '{$product_quantity }', ";
		$query.="product_image = '{$product_image }'";
		$query.="WHERE product_id=" . escape_string($_GET['id']);
		$send_update_query = query($query);
		confirm($send_update_query);
		set_message("New product with id {$last_id} was updated");
		redirect("index.php?products");


	}


}


//get categroies
function show_categories_add_product(){
	$query= query("SELECT * FROM categories");
	confirm($query);
	while ($row=fetch_array($query)){

		$category_options = <<<DELIMETER
		<option value="{$row['cat_id']}">{$row['cat_title']}</option>

		DELIMETER;

		echo $category_options;
	}

}




?>