<?php require_once("../resources/config.php") ?>

<?php 

//add je iz funkcije get_all_products
if (isset($_GET['add'])) {

  $query=query("SELECT * from products where product_id = " . escape_string($_GET['add'])." ");
  confirm($query);
  while ($row=fetch_array($query)) {

    if ($row['product_quantity'] != $_SESSION['product_' . $_GET['add']]) {
      $_SESSION['product_' . $_GET['add']] +=1;
      redirect("../public/checkout.php");
    }else{
      set_message("We only have " . $row['product_quantity'] . " " . "available");
      redirect("../public/checkout.php");
    }

  }

}

//funkcionalnost za + i -

if (isset($_GET['remove'])) {

  $_SESSION['product_' . $_GET['remove']] --;

  if ($_SESSION['product_' . $_GET['remove']]< 1) {
    //vracamo na nulu 
    unset($_SESSION['item_total']);
    unset($_SESSION['item_quantity']);
    set_message("Izbirsali ste sve iz korpe");
    redirect("../public/checkout.php");

  }else{

    redirect("../public/checkout.php");
  }


}

if (isset($_GET['delete'])) {
  $_SESSION['product_' . $_GET['delete']]= '0';
  //setujem koicinu i cenu na  0
  unset($_SESSION['item_total']);
  unset($_SESSION['item_quantity']);
  redirect("../public/checkout.php");
}


function cart(){

  $total = 0 ;
  $item_quantity=0;
  $item_name = 1;
  $item_number=1;
  $amount=1;
  $quantity=1;

  foreach ($_SESSION as $name => $value) {
    if ($value > 0) {
        // od 0 do 7 je zato sto product_ ima 8 karakter 0 je prvi index
      if (substr($name, 0, 8) == "product_") {

        $length = strlen((int)$name - 8);
        $id = substr($name, 8 , $length);

        $query = query("SELECT * FROM products where product_id = " . escape_string($id) . " ");
        confirm($query);

        while ($row=fetch_array($query)) { 
          $sub= $row['product_price'] *  $value;
          $item_quantity += $value;
          $product_image=display_image($row['product_image']);
          $product = <<<DELIMETER
          <tr>
          <td>{$row['product_title']}<br>   
          <img style="width:100px; height:120px;" src='../resources/{$product_image}'>
          </td>
          <td>{$row['product_price']}</td>
          <td>{$value}</td>
          <td>{$sub}</td>
          <td><a class='btn btn-warning' href="../resources/cart.php?remove={$row['product_id']}"><span class='glyphicon glyphicon-minus'></span></a>
          <a class='btn btn-success' href="../resources/cart.php?add={$row['product_id']}"><span class='glyphicon glyphicon-plus'></span></a>
          <a class='btn btn-danger' href="../resources/cart.php?delete={$row['product_id']}"><span class='glyphicon glyphicon-remove'></span></a>
          </td>
          </tr>
          <input type="hidden" name="item_name_{$item_name}" value="{$row['product_title']}">
          <input type="hidden" name="item_number_{$item_number}" value="{$row['product_id']}">
          <input type="hidden" name="amount_{$amount}" value="{$row['product_price']}">
          <input type="hidden" name="quantity_{$quantity}" value="$value">

          DELIMETER;

          echo $product;
          $item_name++;
          $item_number++;
          $amount++;
          $quantity++;


        }
//promenljiva u koju smestamo total
        $_SESSION['item_total']= $total +=$sub;
        //promenljiva za ispi ukupne kolicine narucenih proizvoda
        $_SESSION['item_quantity'] = $item_quantity;

      }
    }


  }

}

//funkcija za prikaz dugmeta paypall
function show_paypal(){

//ako imamo neki item ubacen u korpu, pokazace se dugme!!!
  if (isset($_SESSION['item_quantity']) && $_SESSION['item_quantity']>=1) {

    $paypal_button= <<<DELIMETER
    <input type="image" name="upload"
    src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif"
    alt="PayPal - The safer, easier way to pay online">

    DELIMETER;

    return $paypal_button;
  }

}

function process_transaction(){

  if (isset($_GET['tx'])) {

    $amount=$_GET['amt'];
    $currency=$_GET['cc'];
    $transaction=$_GET['tx'];
    $status=$_GET['st'];


    $total = 0 ;
    $item_quantity=0;


    foreach ($_SESSION as $name => $value) {
      if ($value > 0) {
        // od 0 do 7 je zato sto product_ ima 8 karakter 0 je prvi index
        if (substr($name, 0, 8) == "product_") {

          $length = strlen((int)$name - 8);
          $id = substr($name, 8 , $length);

          
          $send_order=query("INSERT INTO orders (order_amount, order_tx, order_status, order_currency) values ('{$amount}', '{$currency}','{$transaction}','{$status}')");

          $last_id=last_id();    

          confirm($send_order);





          $query = query("SELECT * FROM products where product_id = " . escape_string($id) . " ");
          confirm($query);

          while ($row=fetch_array($query)) { 
            $product_price=$row['product_price'];          
            $product_title=$row['product_title'];          
            $sub= $row['product_price'] *  $value;
            $item_quantity += $value;  

            $insert_into_report=query("INSERT INTO reports (product_id,order_id, product_price, product_title,product_quantity) values ('{$id}', {$last_id} ,'{$product_price}','{$product_title}','{$value}')");
            confirm($insert_into_report);



          }
          $total +=$sub;
          echo $item_quantity;

        }
      }


    }
    session_destroy();
  }else{

    redirect("index.php");
  }

}



?>