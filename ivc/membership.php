<?
session_start();
if ($_SESSION['uid'] <= 0) {
    header("Location: index.php");
    exit();
}
include("config.php");
include("functions.php");
include('header.php');

$uid=$_SESSION['uid'];
$select = "select * from ivc_membership where uid=$uid and paid_status='paid' order by id desc limit 1";
//print $select;
$res = $GLOBALS ['mysqli']->query ($select) or die ($GLOBALS ['mysqli']->error . __LINE__);


$levels = array("3" => '3***', "4" => '4****', "5" => '5*****', "vip" => 'VIP LUXURY');
?>
    
    
    	
	<div class="row" style="margin:0 auto; max-width:1000px;  margin-top:70px; padding:15px;">
			<div class="col-md-12" style="text-align:center;">                  
						<img src="images/MEMBERSHIP.png" class="img img-responsive" style="margin:0 auto;">
			</div>
    </div>

    <?
    if ($res->num_rows > 0)
    {
        $row = $res->fetch_assoc ();
        $date_payment = date('Y-m-d', strtotime($row['date_payment']));
        $time = strtotime($row['date_payment']);
        $final = date("Y-m-d", strtotime("+1 month", $time));

        $today = date('Y-m-d');

        //print $final.'       '.$today;
        //exit;

        if($final<=$today)
        {
           ?>
            <div class="alert alert-danger" role="alert" style="max-width:1000px; margin:0 auto;">
            Membership Level: <?=$levels[$row['membership']]?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Category: <?=strtoupper($row['category'])?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Status: <a href="membership_status.php">EXPIRED</a>
            <?
            if($_SESSION['uid']>0 && $row['currency']=='YEM')
            {
            ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;AUTO RENEW: 
            <div class="form-check-inline">
                <label class="form-check-label">
                    <input type="radio" value='1' class="form-check-input" name="renew" <? if($row['auto_renew']=='1') { print 'checked'; } ?>>YES
                </label>
            </div>
            <div class="form-check-inline">
                <label class="form-check-label">
                    <input type="radio" value='0' class="form-check-input" name="renew" <? if($row['auto_renew']=='0') { print 'checked'; } ?>>NO
                </label>
            </div>
            <script>
                $('input:radio[name="renew"]').change(
                function(){
                    //alert(this.value);
                    $.post("renew.ajax.php",
                    {
                        val: this.value
                    },
                    function(data, status){
                        //document.getElementById('amount').value = data;
                        var res=data.split("||");
                        if(res[0]!=='success')
                        {
                            
                            
                            
                        }
                        else
                        {
                            
                            
                        }
                    });
                });
            </script>
            <?
            }
            ?>    
            </div>
           <?

        }
        else
        {
            ?>
            <div class="alert alert-primary" role="alert" style="max-width:1000px; margin:0 auto;">
            Membership Level: <?=$levels[$row['membership']]?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Category: <?=strtoupper($row['category'])?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Status: <a href="membership_status.php">ACTIVE</a>
            <?
            if($_SESSION['uid']>0 && $row['currency']=='YEM')
            {
            ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;AUTO RENEW: 
            <div class="form-check-inline">
                <label class="form-check-label">
                    <input type="radio" value='1' class="form-check-input" name="renew" <? if($row['auto_renew']=='1') { print 'checked'; } ?>>YES
                </label>
            </div>
            <div class="form-check-inline">
                <label class="form-check-label">
                    <input type="radio" value='0' class="form-check-input" name="renew" <? if($row['auto_renew']=='0') { print 'checked'; } ?>>NO
                </label>
            </div>
            <script>
                $('input:radio[name="renew"]').change(
                function(){
                    //alert(this.value);
                    $.post("renew.ajax.php",
                    {
                        val: this.value
                    },
                    function(data, status){
                        //document.getElementById('amount').value = data;
                        var res=data.split("||");
                        if(res[0]!=='success')
                        {
                            
                            
                            
                        }
                        else
                        {
                            
                            
                        }
                    });
                });
            </script>
            <?
            }
            ?>    
            </div>
           <?
        }
        ?>

        <?
    }
    ?>
    <div class="row" style="margin:0 auto; max-width:1000px;  margin-top:5px; padding:15px; border: 1px #650B14 solid; min-height:500px; color:#650B14;">
			<div class="col-md-12">     
                <p style="font-weight:bold; font-size: 1.3rem;">The International Vacation Club (IVC) has a travel membership to suit your needs.</p>
                <p style="font-size: 1rem;"><strong>Categories for your travel preferences:</strong><br> 

                3*** for 3-star accommodation, Economy flight, Economy/Compact car rental<br> 
                4**** for 4-star accommodation, Premium Economy flight, Intermediate/Full Size car rental<br> 
                5***** for 5-star accommodation, Business Class flight, Standard/Premium car rental<br> 
                VIP Luxury for Luxury suites, Private jet, Chauffeur
                </p>

                <p style="font-size: 1rem;"><strong>Depending on your party size: </strong><br>
                Singles for Individuals<br>
                Couples for 2 persons, regardless of sex and age<br>
                Families for up to 6 persons also without degree of kinship<br>
                Groups for large families, clubs, companies, organizations with 7 or more persons
                </p>
                <p style="font-weight:bold; font-size: 1rem;">
                Book immediate travel, long-term travel up to 3 years in advance, special member-only packages, cruises, hotels, flights and more. Collect extra travel perks the longer you are a member and even CashBack towards your next trip when booking through IVC.
                </p>

                <table class="table table-responsive table-striped table-bordered" style="margin: 0 auto; color: #650B14; border: #650B14; text-align: left; font-size: 14px; font-weight: bold; max-width:769px;">
                    <tbody>
                        <tr style="font-weight:bold; background: #650B14; color:#fff;">
                            <th style="text-align:center; width:20%;">TRAVEL LEVEL<br>& GROUP</th>
                            <th style="text-align:center; width:20%;">SINGLE<br>(1 Person)</th>
                            <th style="text-align:center; width:20%;">COUPLE<br>(2 People)</th>
                            <th style="text-align:center; width:20%;">FAMILY<br>(Up to 6 people)</th>
                            <th style="text-align:center; width:20%;">GROUP<br>(7 or more people)</th>                        
                        </tr>
                        
                        <tr>
                            <td style="text-align:left;vertical-align: bottom;">3***</td>
                            <td style="text-align:left;vertical-align: bottom;">39</td>
                            <td style="text-align:left;vertical-align: bottom;">69</td>
                            <td style="text-align:left;vertical-align: bottom;">89</td>
                            <td style="text-align:left;vertical-align: bottom;">159</td>
                            
                        </tr>

                        <tr>
                            <td style="text-align:left;vertical-align: bottom;">4****</td>
                            <td style="text-align:left;vertical-align: bottom;">59</td>
                            <td style="text-align:left;vertical-align: bottom;">99</td>
                            <td style="text-align:left;vertical-align: bottom;">129</td>
                            <td style="text-align:left;vertical-align: bottom;">209</td>
                            
                        </tr>

                        <tr>
                            <td style="text-align:left;vertical-align: bottom;">5*****</td>
                            <td style="text-align:left;vertical-align: bottom;">79</td>
                            <td style="text-align:left;vertical-align: bottom;">129</td>
                            <td style="text-align:left;vertical-align: bottom;">169</td>
                            <td style="text-align:left;vertical-align: bottom;">259</td>
                            
                        </tr>

                        <tr>
                            <td style="text-align:left;vertical-align: bottom;">VIP LUXURY</td>
                            <td style="text-align:left;vertical-align: bottom;">149</td>
                            <td style="text-align:left;vertical-align: bottom;">249</td>
                            <td style="text-align:left;vertical-align: bottom;">299</td>
                            <td style="text-align:left;vertical-align: bottom;">399</td>
                            
                        </tr>
                       
                        
                    </tbody>
                </table>

                <p style="font-size: 1rem; margin-top:15px;">Membership fees are paid monthly.<br>
Currencies accepted: USD, EUR, GBP (same prices for each).<br>
Membership fees can be paid in YEM (at the current rate) as well with a 10% discount.

                </p>

                <p style="font-size: 1rem;">IVC is not a travel agency or travel provider, the bookings will always be made directly with licensed travel agencies and providers. Membership must be maintained in order to book any travel through IVC including Smart Deals. Official dates are booked after Smart Contracts activated, in the event any of the itinerary is unavailable at the time of booking a comparable option will be chosen. Membership is required for length of Smart Contract through travel dates. If membership lapses during this time, the Smart Contract and reservation will be canceled and TVC refunded. Any and all additional fees, taxes, charges will be paid to the travel fulfillment vendors in fiat currency when the Smart Contract is activated. All fiat currency costs will be disclosed and a licensed travel expert will contact you to confirm booking when the Smart Contract is activated. Any visa or other travel requirements are your responsibility and must be confirmed at the time of booking before traveling.

                </p>

                <p style="text-align:center;" id="signupbtn">   
                        <a href="javascript:void(0)" onclick="$('#signupbtn').hide(); $('#order').show();"><button class="btn btn-success" style="margin:0 auto; width:180px; color:#fff; cursor:default;">SIGNUP</button></a>     
                        
                </p> 

                <div id="order" style="max-width: 400px; margin: 0 auto; display:none;">
                    <div class="input-group steps" style="display: block;">
                        <p style="text-align:justify; font-size:18px; font-weight:bold;">Choose Category:</p>
                        
                        <select name="category" id="category" class="form-control" required="" style="width: 100%; margin: 0 auto; height: 40px; border: 1px solid #000; text-align: center; font-size: 18px;">
                            
                            <option value="single">SINGLE</option>
                            <option value="couple">COUPLE</option>
                            <option value="family">FAMILY</option>
                            <option value="group">GROUP</option>
                        </select>

                        
                        
                    </div>

                    <div class="input-group steps" style="display: block; margin-top:20px;">
                        <p style="text-align:justify; font-size:18px; font-weight:bold;">Choose Membership:</p>
                        
                        <select name="membership" id="membership" class="form-control" required="" style="width: 100%; margin: 0 auto; height: 40px; border: 1px solid #000; text-align: center; font-size: 18px;">
                            
                            <option value="3">3***</option>
                            <option value="4">4****</option>
                            <option value="5">5*****</option>
                            <option value="vip">VIP LUXURY	</option>
                        </select>

                        
                        
                    </div>

                    <div class="input-group steps" style="display: block; margin-top:20px;">
                        <p style="text-align:justify; font-size:18px; font-weight:bold;">Choose Currency to pay fee:</p>
                        
                        <select name="currency" id="currency" class="form-control" required="" style="width: 100%; margin: 0 auto; height: 40px; border: 1px solid #000; text-align: center; font-size: 18px;">
                            
                            
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                            <option value="GBP">GBP</option>
                            
                            <option value="YEM">YEM</option>
                            
                        </select>

                        
                        
                    </div>

                    <div class="input-group steps" style="display: block; margin-top:20px; margin-bottom:20px;">
                        <p style="text-align:justify; font-size:18px; font-weight:bold;">Total Membership fee (paid monthly):</p>
                        <p style="display:none; text-align:center;" id="discount"><span id="oldprice" style="text-decoration:line-through;"></span>&nbsp;&nbsp;&nbsp;<span id="newprice"></span>&nbsp;&nbsp;&nbsp;(10% off)</p>
                        <input type="text" name="amount" placeholder="0.00" id="amount" class="form-control" required="" style="width: 100%; margin: 0 auto; height: 40px; border: 1px solid #000; text-align: center; font-size: 28px;">

                        
                        
                    </div>

                    <div class="alert alert-danger" role="alert" id="err" style="margin-top:10px; display:none;">
                        
                    </div>

                    <p style="text-align:center;" id="confirmbtn">   
                        <a href="javascript:void(0)" onclick="confirm2();"><button class="btn btn-success" style="margin:0 auto; width:180px; color:#fff; cursor:default;">CONFIRM</button></a>     
                        
                    </p> 
                </div>

                <p style="text-align:center;">   
                            
                        <a href="home.php"><button class="btn btn-primary" style="margin:0 auto; width:180px; background:#650B14; color:#fff; cursor:default;">BACK</button></a>
                </p> 
						
			</div>
      
      
    </div>  
<?

$json = file_get_contents('https://cryptorank.online/rankdata_with_price.php?apikey=tkfNseYgYsEE32n4uvxb');
$jsonIterator = json_decode($json, TRUE);

foreach ($jsonIterator as $key => $value)
{
	//print $value['asset']."<br>";
	if($value['asset']=='YEM')
	{
		$yemval = $value['value'];
		$yemval =  explode('|',$yemval);
		$twvalue = number_format($yemval[1],3);
		
	}
	elseif($value['asset']=='TEC')
	{
		$tecval = $value['value'];
		$tecval =  explode('|',$tecval);
		$tecval = number_format($tecval[1],5,'.','');
		
	}
	
}



if($twvalue<=0)
{
    $twvalue=0.01;
}
?>    
<script>
function confirm2()
{

    var currency = $("#currency :selected").val();
    var category = $("#category :selected").val();
    var membership = $("#membership :selected").val();
    if(confirm("Are your sure your want to order "+category+" and "+membership+" star membership?"))
	{
		if(currency!=='' && category!=='' && membership!=='')
		{
            $('#confirmbtn').hide();
            $('#err').hide();
			$.post("confirm.ajax.php",
			{
				currency: currency,
				category: category,
				membership: membership
			},
			function(data, status){
				//document.getElementById('amount').value = data;
				var res=data.split("||");
				if(res[0]!=='success')
				{
					$('#err').html(res[1]);
					
                    $('#err').show();

                    $('#confirmbtn').show();
                    
                    
				}
				else
				{
					window.location.href = "makepayment.php?id="+res[1];

					//redirect
					
				}
			});
		}  
	}

}
function calculate()
{
 		
   
    var currency = $("#currency :selected").val();
    var category = $("#category :selected").val();
    var membership = $("#membership :selected").val();
    var amount;
    
    

    if(category==='single')
    {
        if(membership==='3')
        {
            amount=39;
        }
        else if(membership==='4')
        {
            amount=59;
        }
        else if(membership==='5')
        {
            amount=79;
        }
        else if(membership==='vip')
        {
            amount=149;
        }
    }
    else if(category==='couple')
    {
        if(membership==='3')
        {
            amount=69;
        }
        else if(membership==='4')
        {
            amount=99;
        }
        else if(membership==='5')
        {
            amount=129;
        }
        else if(membership==='vip')
        {
            amount=249;
        }
    }
    else if(category==='family')
    {
        if(membership==='3')
        {
            amount=89;
        }
        else if(membership==='4')
        {
            amount=129;
        }
        else if(membership==='5')
        {
            amount=169;
        }
        else if(membership==='vip')
        {
            amount=299;
        }
    }
    else if(category==='group')
    {
        if(membership==='3')
        {
            amount=159;
        }
        else if(membership==='4')
        {
            amount=209;
        }
        else if(membership==='5')
        {
            amount=259;
        }
        else if(membership==='vip')
        {
            amount=399;
        }
    }

    amount = amount.toFixed(2);
    $('#discount').hide();
    if(currency==='YEM')
    {
        var discount=amount*10/100;
        var oldamount = amount;
        amount = amount-discount;
        $('#oldprice').html('US$'+oldamount);
        $('#newprice').html('US$'+amount.toFixed(2));
        amount=amount/<?=$twvalue?>;
        amount = amount.toFixed(3);
        
        $('#discount').show();
    }
    


    $('#amount').val(amount);
    
}
setInterval(calculate,500);
</script>	

<style>
.container {
    padding-left: 0px !important;
}
</style>
<script>
var navbar = document.getElementById("navbar");

navbar.classList.add("sticky");
</script>
<?
include('footer.php');
?>