<?php
session_start();
if ($_SESSION['uid'] <= 0) {
    header("Location: index.php");
    exit();
}
include('header.php');
?>
    
    
    	
	<div class="row" style="margin:0 auto; max-width:1000px;  margin-top:70px; padding:15px;">
			<div class="col-md-12" style="text-align:center;">                  
						<img src="images/11STEPS.png" class="img img-responsive" style="margin:0 auto;">
			</div>
    </div>
    <div class="row" style="margin:0 auto; max-width:1000px;  margin-top:5px; padding:15px; border: 1px #650B14 solid; min-height:500px; color:#650B14;">
			<div class="col-md-12" style="text-align:center;">     
                <img src="images/IVC-11-Steps.jpg" class="img img-responsive" style="margin:0 auto;">
            </div>
    </div>    

<style>
.container {
    padding-left: 0px !important;
}
</style>
<script>
var navbar = document.getElementById("navbar");

navbar.classList.add("sticky");
</script>
<?php
include('footer.php');
?>