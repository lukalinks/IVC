<?php
session_start();

include("config.php");
include("functions.php");
// //////////GET REFERAL//////////////////

if ($_SESSION['uid'] > 0) {
    header("Location: home.php");
    exit();
}

$time = time() + 3600 * 24 * 365 * 10;
// print $_GET['pernum'];

if ($_GET['pernum'] != '' && ! strstr($_GET['pernum'], '.shtml') && ! strstr($_GET['pernum'], '.ico') && ! strstr($_GET['pernum'], '.php') && ! strstr($_GET['pernum'], '.png')) {
    // $_GET['pernum'] = getNumbersFromText($_GET['pernum']);
    setcookie("ref_pernum", $_GET['pernum'], $time, "/", '.ivc.travel');
    
    $pernum = str_pad($_GET['pernum'], 10, '0', STR_PAD_LEFT);
    
    // $U_Qry="SELECT uid from pernum where pernum='$pernum'";
    $user = getSingleValue("pernum", "where pernum='$pernum'", "uid");
    // $user = $mysqli->query($U_Qry);
    
    // $user = $mysqli->fassoc($user);
    
    if ($user > 0) {
        setcookie("ref_uid", $user, $time, "/", '.ivc.travel');
        $ref_uid = $user;
    } else {
        // $pernum = ltrim($_GET['pernum'], '0');
        $pernum2 = ltrim($_GET['pernum'], '1');
        $pernum2 = ltrim($pernum2, '0');
        setcookie("ref_uid", $pernum2, $time, "/", '.ivc.travel');
        $ref_uid = $pernum2;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>IVC</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<!--<script
	src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>-->
<!-- Include all compiled plugins (below), or include individual files as needed -->
<!--<script src="assets/bootstrap/js/bootstrap.min.js"></script>-->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>




<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#ffffff">
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
<!--<link rel="stylesheet" href="assets/css/styles.css">-->
<style>
@media ( max-width :1199px) {
}

@media ( max-width :991px) {
}

@media ( max-width :754px) {
}

@media ( max-width :644px) {
}

@media ( max-width :524px) {
}

@media ( max-width :480px) {
}

body{
  font-family: Century Gothic, Calibri;
}
.row{ max-width:1920px; }
/* The sticky class is added to the navbar with JS when it reaches its scroll position */
.sticky {
  position: fixed;
  top: 0;
  z-index: 9999;
  width:100%;
  /*max-width: 1068px;
  background: #fff !important;
  left: 50%;
  transform: translate(-50%, 0);*/
}

.whitebg{
    background: #fff !important;
}

/* Add some top padding to the page content to prevent sudden quick movement (as the navigation bar gets a new position at the top of the page (position:fixed and top:0) */
.sticky + .content {
  padding-top: 60px;
}

.tickercontainer .mask {
    
    height: 24px !important;

}
ul.newsticker li {
	font-size:22px !important;
}

.tickercontainer {
    height: 32px !important;
    }
    
.tickercontainer .mask {
    top: 5px !important;
    }    
</style>

<style>
#carousel-custom {
    margin-top:  0px;
    width: 100%;
}
#carousel-custom .carousel-indicators {
    margin: 10px 0 0;
    overflow: auto;
    position: static;
    text-align: left;
    white-space: nowrap;
    width: 100%;
}
#carousel-custom .carousel-indicators li {
    background-color: transparent;
    -webkit-border-radius: 0;
    border-radius: 0;
    display: inline-block;
    height: auto;
    margin: 0 !important;
    width: auto;
}
#carousel-custom .carousel-indicators li img {
    display: block;
    opacity: 0.5;
}
#carousel-custom .carousel-indicators li.active img {
    opacity: 1;
}
#carousel-custom .carousel-indicators li:hover img {
    opacity: 0.75;
}
#carousel-custom .carousel-outer {
    position: relative;
}
.carousel-control.left {
    background-image: none;
    width: 54px;
    height: 54px;
    top: 50%;
    left: 20px;
    margin-top: -27px;
    line-height: 54px;
    border: 2px solid #fff;
    opacity: 1;
    text-shadow: none;
    -webkit-transition: all 0.2s ease-in-out 0s;
    -o-transition: all 0.2s ease-in-out 0s;
    transition: all 0.2s ease-in-out 0s;
}
.carousel-control.right {
    background-image: none;
    width: 54px;
    height: 54px;
    top: 50%;
    right: 20px;
    margin-top: -27px;
    line-height: 54px;
    border: 2px solid #fff;
    opacity: 1;
    text-shadow: none;
    -webkit-transition: all 0.2s ease-in-out 0s;
    -o-transition: all 0.2s ease-in-out 0s;
    transition: all 0.2s ease-in-out 0s;
}
/* The sticky class is added to the navbar with JS when it reaches its scroll position */
.sticky {
  position: fixed;
  top: 0;
  z-index: 9999;
  /*max-width: 1068px;
  background: #fff !important;
  left: 50%;
  transform: translate(-50%, 0);*/
}

</style>
</head>

<body style="background: #fff;">


	<div class="container" style="width: 100%; margin-top: 0px; max-width: 1920px;">
	
		<div style="width:100%; max-width: 1920px; text-align:center;" id="topmenu" class="sticky"> 
			<img src="logo2.png" class="img-responsive" style="margin: 0 auto; width:240px; margin-top:10px;">
		</div>
		
		<div class="row" style="margin-top: 0px; padding:0px;">
			<div class="col-md-12" style="text-align: center; padding:0px;">
				<div id="carousel-custom" class="carousel slide" data-ride="carousel">
                  <!-- Wrapper for slides -->
                  <div class="carousel-inner" role="listbox">
                    <div class="carousel-item active">
                      <img src="slider1.jpg" class="img-responsive">
                      <div class="carousel-caption d-none d-md-block">
                        <img src="ivc2.png" class="img-responsive" style="max-width:600px;">
                      </div>
                    </div>
                    
                    <div class="carousel-item">
                      <img src="slider2.jpg" class="img-responsive">
                      <div class="carousel-caption d-none d-md-block">
                        <img src="ivc2.png" class="img-responsive" style="max-width:600px;">
                      </div>
                    </div>
                    <div class="carousel-item">
                      <img src="slider3.jpg" class="img-responsive">
                      <div class="carousel-caption d-none d-md-block">
                        <img src="ivc2.png" class="img-responsive" style="max-width:600px;">
                      </div>
                    </div>
                    <div class="carousel-item">
                      <img src="slider4.jpg" class="img-responsive">
                      <div class="carousel-caption d-none d-md-block">
                        <img src="ivc2.png" class="img-responsive" style="max-width:600px;">
                      </div>
                    </div>
                    <div class="carousel-item">
                      <img src="slider5.jpg" class="img-responsive">
                      <div class="carousel-caption d-none d-md-block">
                        <img src="ivc2.png" class="img-responsive" style="max-width:600px;">
                      </div>
                    </div>
                    
                    
                  </div>
                
                  <!-- Controls 
                  <a class="left carousel-control" href="#carousel-custom" role="button" data-slide="prev">
                    <i class="fa fa-chevron-left"></i>
                    <span class="sr-only">Previous</span>
                  </a>
                  <a class="right carousel-control" href="#carousel-custom" role="button" data-slide="next">
                    <i class="fa fa-chevron-right"></i>
                    <span class="sr-only">Next</span>
                  </a> -->
                  
                  
                  <!-- Indicators 
                  <ol class="carousel-indicators visible-sm-block hidden-xs-block visible-md-block visible-lg-block">
                    <li data-target="#carousel-custom" data-slide-to="0" class="active">
                      <img src="" style="width: 54px;" class="img-responsive">
                    </li>
                    
                    
        
                  </ol> -->
                </div>
			</div>
		</div>	

	 
				
		<div class="row" style="padding: 20px; margin-top: 15px;">

			<div class="col-md-12" style="text-align: center;">
				<img src="assets/img/sz_seal.png" class="img img-responsive" style="margin: 0 auto; width:180px;">
				
				<p style="color: #000; text-align: center; font-size: 14px; font-weight: normal; margin-top:20px;">© 2018-<?=date('Y')?> IVC. All Rights Reserved.</p>
			</div>
			
		</div>	
	</div>
		
<script type="text/javascript">

$(document).ready(function() {
		
	});
	
//When the user scrolls the page, execute myFunction 
window.onscroll = function() {myFunction()};

// Get the navbar
var navbar = document.getElementById("navbar");
//var navbar2 = document.getElementById("navbar");

// Get the offset position of the navbar
var sticky = navbar.offsetTop;

// Add the sticky class to the navbar when you reach its scroll position. Remove "sticky" when you leave the scroll position
function myFunction() {
  if (window.pageYOffset >= sticky) {
    navbar.classList.add("sticky");
    $('#topmenu').hide();
    //navbar2.classList.add("whitebg");
  } else {
    navbar.classList.remove("sticky");
    $('#topmenu').show();
    //navbar2.classList.remove("whitebg")
  }
}

</script>    

</body>

</html>