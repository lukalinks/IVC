<?php
session_start();
if ($_SESSION['uid'] <= 0) {
    header("Location: index.php");
    exit();
}
include("config.php");
include("functions.php");
include('header.php');

$uid = $_SESSION['uid'];
$id=$GLOBALS ['mysqli']->real_escape_string($_GET['id']);

$select = "select * from ivc_membership where id=$id and uid=$uid";
//print $select;
$res = $GLOBALS ['mysqli']->query ($select) or die ($GLOBALS ['mysqli']->error . __LINE__);
if ($res->num_rows > 0)
{
    while($row = $res->fetch_assoc ())
    {
      extract($row);

      if($membership=='vip')
      {
          $membership='VIP LUXURY';
      }
      if($membership=='3')
      {
          $membership='3***';
      }
      if($membership=='4')
      {
          $membership='4****';
      }
      if($membership=='5')
      {
          $membership='5*****';
      }
?>
    
    
    	
	<div class="row" style="margin:0 auto; max-width:1000px;  margin-top:70px; padding:15px;">
			<div class="col-md-12" style="text-align:center;">                  
						<img src="images/MEMBERSHIP.png" class="img img-responsive" style="margin:0 auto;">
			</div>
    </div>
    <div class="row" style="margin:0 auto; max-width:1000px;  margin-top:5px; padding:15px; border: 1px #650B14 solid; min-height:500px; background:url(assets/img/Picture5.jpg); background-size: cover;">
			

      <div class="col-md-12" style="font-size:18px; color:#650B14;">  
            <p style="font-weight:bold; font-size: 1.5rem; text-align:center;">MAKE PAYMENT</p>

            <!--<p style="font-weight:bold; font-size: 1.5rem;"><?=$title2?></p>
            <p style="font-weight:bold;"><?=$title3?></p>-->

            <div class="input-group" style="max-width:450px; margin:0 auto;">
                <input type="text" readonly name="value" value="Category: <?=$category?>" class="form-control" required style="width: 300px; margin: 0 auto; height: 50px; border: 1px solid #000; text-align: center; font-size: 26px;">
            </div>
            <br>
            <div class="input-group" style="max-width:450px; margin:0 auto;">
                <input type="text" readonly name="value" value="Membership: <?=$membership?>" class="form-control" required style="width: 300px; margin: 0 auto; height: 50px; border: 1px solid #000; text-align: center; font-size: 26px;">
            </div>
            <br>

            <div class="input-group" style="max-width:450px; margin:0 auto;">
                <input type="text" readonly name="value" value="Amount: <?=$currency.' '.$amount?>" class="form-control" required style="width: 300px; margin: 0 auto; height: 50px; border: 1px solid #000; text-align: center; font-size: 26px;">
            </div>
            <br>

            <div class="input-group" style="max-width:450px; margin:0 auto;">
                <input type="text" readonly name="value" value="Reference: <?=$id?>" class="form-control" required style="width: 300px; margin: 0 auto; height: 50px; border: 1px solid #000; text-align: center; font-size: 26px;">
            </div>
        
            
                <?php
                if($currency=='YEM')
                {    
                    ?>
                    <p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; text-align: center; font-size: 26px; background: rgba(255, 255, 255, 0.7); margin-top:20px;">
                    Membership Paid Successfully.
                    </p>
                    <?php
                }
                else
                {
                    
                    if($currency!='BTC')
                    {
                    ?>
                    <p style="text-align:center; margin:0 auto;">
                        <button class="btn btn-lg greenbtn btn-success" id="paynow" type="button" style="margin: 0 auto; margin-top:10px;" onClick="$('#paynow').hide(); $('.paybuttons').show();">PAY NOW</button>
                    </p>
                    <p>    
                    <form action="paypal.php" method="post">
                        <input type="hidden" name="id" value="<?=$id?>">
                        <!--<input type="submit" value="Pay with PayPal" style="margin: 0 auto; margin-bottom:20px;">-->
                        <p style="text-align:center; margin:0 auto;"><button class="btn btn-lg greenbtn paybuttons btn-success" type="submit" style="margin-top:20px; display:none;">PAY WITH PAYPAL</button></p>
                        <p style="text-align:center; margin:0 auto;"><button class="btn btn-lg greenbtn paybuttons btn-success" type="button" style="margin-top:20px; margin-bottom:20px; display:none;" onClick="$('#bankinfo').show();">BANK WIRE</button></p>
                    </form>
                    <?php
                    }
                    if($currency=='BTC')
                    {
                    ?>    
                    <!--<input type="button" value="Pay with BTC" style="width:300px; margin: 0 auto; margin-bottom:20px;" onClick="generate_btc(<?=$_GET['ref']?>);">-->
                    <button class="btn btn-lg greenbtn paybuttons" type="button" style="margin-top:20px; margin-bottom:20px;" onClick="generate_btc(<?=$_GET['ref']?>);">PAY WITH BTC</button>
                    <p id="btc" style="width: 300px; padding-top:20px; padding-bottom:20px; margin: 0 auto; height: auto; border: 1px solid #000; text-align: center; font-size: 16px; margin-bottom:20px; display:none;"></p>
                    
                    <?php
                    }
                    

                }
                ?>
                
                <div id="bankinfo" style="display:none; background: rgba(255, 255, 255, 0.7); max-width:300px; margin:0 auto;">
                <?php
                if($currency!='BTC')
                {
                ?>
                <p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; text-align: center; font-size: 26px;">
                Make your payment to
                </p>
                <?php
                }
                if($currency=='USD')
                {    
                ?>
                <p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-bottom:0px; border-top:0px; text-align: center; font-size: 22px;">
                USD Bank details
                </p><p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-bottom:0px; border-top:0px; text-align: center; font-size: 22px;">    
                UAC Holding GmbH
                </p><p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-bottom:0px; border-top:0px; text-align: center; font-size: 22px;">
                Account number    8310328584
                </p><p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-bottom:0px; border-top:0px; text-align: center; font-size: 22px;">
                Routing Number (Wire)   026073008
                </p><p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-bottom:0px; border-top:0px; text-align: center; font-size: 22px;"> 
                Routing number (ACH or ABA)   026073150
                </p><p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-bottom:0px; border-top:0px; text-align: center; font-size: 22px;">
                SWIFT / BIC    CMFGUS33
                </p><p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-top:0px; text-align: center; font-size: 22px;"> 
                    TransferWise<br>
                    19 W 24th Street<br>
                    New York, NY<br>
                    10010, USA
                </p>
                <?php
                }
                ?>

<?php
                if($currency=='EUR' || $currency=='CHF')
                {    
                ?>
                <p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-bottom:0px; border-top:0px; text-align: center; font-size: 22px;">
                EUR Bank details
                </p>
                <p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-bottom:0px; border-top:0px; text-align: center; font-size: 22px;">
                UAC Holding GmbH
                </p><p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-bottom:0px; border-top:0px; text-align: center; font-size: 22px;">
                IBAN<br>DE82 7001 1110 6056 4150 40
                </p><p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-bottom:0px; border-top:0px; text-align: center; font-size: 22px;">
                SWIFT / BIC    DEKTDE7GXXX
                </p><p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-top:0px; text-align: center; font-size: 22px;">
                    Handelsbank<br>
                    Elsenheimer Str. 41<br>
                    80687 München<br>
                    Germany
                </p>
                <?php
                }
                ?>

<?php
                if($currency=='GBP')
                {    
                ?>
                <p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-bottom:0px; border-top:0px; text-align: center; font-size: 22px;">
                GBP Bank details
                </p>
                <p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-bottom:0px; border-top:0px; text-align: center; font-size: 22px;">
                UAC Holding GmbH
                </p><p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-bottom:0px; border-top:0px; text-align: center; font-size: 22px;">
                Sort Code    23-14-70
                </p><p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-bottom:0px; border-top:0px; text-align: center; font-size: 22px;">
                Account number    41991110
                </p><p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-bottom:0px; border-top:0px; text-align: center; font-size: 22px;">
                IBAN (from UK only) <br>
                GB81 TRWI 2314 7041 9911 10
                </p><p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-top:0px; text-align: center; font-size: 22px;">
                Address<br>
                    TransferWise<br>
                    56 Shoreditch High Street<br>
                    London<br>
                    E1 6JJ<br>
                    United Kingdom
                </p>
                <?php
                }
                ?>

<?php
                if($currency=='AUD')
                {    
                ?>
                <p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-bottom:0px; border-top:0px; text-align: center; font-size: 22px;">
                AUD Bank details
                </p>
                <p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-bottom:0px; border-top:0px; text-align: center; font-size: 22px;">
                UAC Holding GmbH
                </p><p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-bottom:0px; border-top:0px; text-align: center; font-size: 22px;">
                Account number    216070314
                </p><p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-bottom:0px; border-top:0px; text-align: center; font-size: 22px;">
                BSB Code    802-985
                </p><p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-top:0px; text-align: center; font-size: 22px;">
                Address<br> 
                    Transferwise<br> 
                    36-38 Gipps Street<br> 
                    Collingwood VIC 3066<br> 
                    Australia
                </p>
                <?php
                }
                ?>

<?php
                if($currency=='NZD')
                {    
                ?>
                <p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-bottom:0px; border-top:0px; text-align: center; font-size: 22px;">
                NZD Bank details
                </p>
                <p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-bottom:0px; border-top:0px; text-align: center; font-size: 22px;">
                UAC Holding GmbH
                </p><p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-bottom:0px; border-top:0px; text-align: center; font-size: 22px;">
                Account number<br>
                02-1291-0347227-000
                </p><p style="width: 300px; margin: 0 auto; height: auto; border: 1px solid #000; border-top:0px; text-align: center; font-size: 22px;">
                Address<br>
                    TransferWise<br>
                    56 Shoreditch High Street<br>
                    London<br>
                    E1 6JJ<br>
                    United Kingdom
                </p>
                <?php
                }
                ?>
                </div>

            </div>
            
            
        </div>
                    
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
    }
}
else
{

    header("Location: membership_status.php");
    exit;
}
include('footer.php');
?>