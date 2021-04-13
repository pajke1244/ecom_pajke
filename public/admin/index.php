<?php 
//vracamo se dva koraka
require_once("../../resources/config.php");
include(TEMPLATE_BACK . DS . "/admin_header.php" ); 
?>


<?php 

//sigurnost za logovanje, ako nije logovan admin, ne moze se pristupiti admin panelu ili preko URL-a
if (!isset($_SESSION['username'])) {
    redirect("../../public");
}

?>

<div id="page-wrapper">

    <div class="container-fluid">

        <!-- Page Heading -->

        <!-- /.row -->
        <?php 
    //u zavisnosti od URL stranice, otvramo drugaciji sadrzaj
        if ($_SERVER['REQUEST_URI'] == "/ecom/public/admin/" || $_SERVER['REQUEST_URI'] == "/ecom/public/admin/index.php") {

            include(TEMPLATE_BACK . DS . "/admin_content.php" );
        }
        if(isset($_GET['orders'])){
            include(TEMPLATE_BACK . DS . "/orders.php" );
        }
        if (isset($_GET['products'])) {
         include(TEMPLATE_BACK . DS . "/products.php" );
     }
     if (isset($_GET['add_product'])) {
        include(TEMPLATE_BACK . DS . "/add_product.php" );
    }
    if (isset($_GET['edit_product'])) {
        include(TEMPLATE_BACK . DS . "/edit_product.php" );
    }
    if (isset($_GET['categories'])) {
        include(TEMPLATE_BACK . DS . "/categories.php" );
    }


    ?>
    
    <!-- /.row -->

</div>
<!-- /.container-fluid -->

</div>
<!-- /#page-wrapper -->
<?php include(TEMPLATE_BACK . DS . "/admin_footer.php" );  ?>

