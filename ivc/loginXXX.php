<?php
session_start();
include('header.php');
?>
    
    <div class="row" style="max-width:1000px; margin-top:100px; margin:0 auto;">
    	
			<div class="col-md-12" style="text-align: center;">
        <p style="color: #650B14; text-align: center; font-size: 20px; font-weight: bold; margin-top:30px;">IVC.TRAVEL is part of the SafeZone</p>
      </div>		
      <div class="col-md-5" style="text-align: center;">
        <img src="sz_seal.png" class="img img-responsive" style="margin: 0 auto; margin-top:20px; width:180px;">
      </div>
      
      <div class="col-md-7" style="text-align: center;">
        <a href="https://safe.zone/login.php?domain=ivc.travel"><button type="button" class="btn btn-primary btn-lg" style="margin: 0 auto; margin-top:50px;">LOGIN WITH YOUR SAFEZONE PASS</button></a>
        
        <a href="https://www.safe.zone/signup_l.php?ref_pernum=<?=$_COOKIE['ref_pernum']?>&domain=ivc.travel"><button type="button" class="btn btn-primary btn-lg" style="margin: 0 auto; margin-top:50px;">GET YOUR FREE SAFEZONE PASS</button></a>
      </div>

    </div>
    
				
<?php
include('footer.php');
?>