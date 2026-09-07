<?
session_start();
if ($_SESSION['uid'] <= 0) {
    header("Location: index.php");
    exit();
}
include("config.php");
include("functions.php");
include('header.php');

$GLOBALS ['mysqli']->query ("SET NAMES UTF8") or die ($GLOBALS ['mysqli']->error . __LINE__);

$id=$GLOBALS ['mysqli']->real_escape_string($_GET['id']);
$select = "select * from ivc_vacations where id='".$id."'";
//print $select;
$res = $GLOBALS ['mysqli']->query ($select) or die ($GLOBALS ['mysqli']->error . __LINE__);
if ($res->num_rows > 0)
{
    $row = $res->fetch_assoc ();
    extract($row);
}
?>
    
    <div class="row" style="margin:0 auto; max-width:1000px;  margin-top:70px; padding:15px;">
			<div class="col-md-12" style="text-align:center;">                  
						<img src="images/VACATIONS.png" class="img img-responsive" style="margin:0 auto;">
			</div>
    </div>
    	
	<div class="row" style="margin:0 auto; max-width:1000px;  margin-top:5px; padding:15px; border: 1px #650B14 solid; min-height:500px;">
        <div class="col-md-12">
            <div class="row" style="margin:0 auto; min-height:500px; background:url(assets/img/Picture6.jpg); background-size: cover;">
                    
            
                <div class="col-md-6" style="background: rgba(255, 255, 255, 0.3); font-size:18px; color:#650B14; text-align:center;">  
                    <p style="font-weight:bold; font-size: 1.5rem; margin-top:15px;"><?=$title1?></p>

                    <p style="font-weight:bold; font-size: 1.5rem;"><?=$title2?></p>
                    <p style="font-weight:bold;"><?=$title3?></p>
                    
                    <p style="font-weight:bold;">Trip Date: <?=$trip_date?><br>
                    Total Seats available: <?=$total_seats?><br>
                    Value: US$<?=number_format($usd_value,2)?><br>
                    TVC Price: <?=$tvc_price?><br>
                    <!--Limit <?=$per_account?> tickets per account-->
                    Limit 10 tickets max. based on Membership

                    </p>
                    
                    <p style="font-weight:bold;"><?=$title4?></p>
                    
                    
                                
                </div>
                <div class="col-md-6">                  
                                
                </div>
            </div>

            <div class="row" style="color:#650B14; margin-top:15px;">
                <div class="col-md-12">
                    <?
                    if($member_type=='vip')
                    {
                    ?>
                    <p style="font-weight:bold;">This luxury world adventure is only available for VIP Luxury Members.</p>
                    <?
                    }
                    ?>
                    <div>
                        <?=$description?>
                    </div>

                    <p style="font-size: .9rem;">
                    By clicking to make a reservation with the selected quantity, you agree the required TVC will be put in escrow and to the Smart Contract terms, additionally you agree that any and all additional fees, taxes, charges will be paid to the travel fulfillment vendors in fiat currency after the Smart Contract is activated. All fiat currency costs will be disclosed and a licensed travel expert will contact you to confirm booking after the Smart Contract is activated. Any visa or other travel requirements are your responsibility and must be confirmed at the time of booking before traveling.
    
                    </p>

                    <p style="font-size: .9rem; font-style:italic;">IVC is not a travel agency or travel provider, the bookings will always be made directly with licensed travel agencies and providers. Official date to be booked after Smart Contract activated,  in the event any of the itinerary is unavailable at the time of booking a comparable option will be chosen. Current VIP Luxury IVC Membership is required for length of Smart Contract through travel dates. If membership lapses during this time, the Smart Contract and reservation will be canceled and TVC refunded. Travel to and from London is included in business class, alternately private jet flight accommodations can be booked separately at own expense.</p>
                    <div class="alert alert-danger" role="alert" id="err" style="margin-top:10px; display:none;"></div>
                    <div class="alert alert-success" role="alert" id="suc" style="margin-top:10px; display:none;"></div>
                        
                    <?
                    $totreserved = getSingleValue('ivc_reservations',"where vid=$id",'sum(qty)');
                    if($totreserved<$total_seats)
                    {
                    ?>
                    <table class="table table-responsive" style="color: #650B14; border: 1px #650B14 solid; text-align: left; font-size: 18px; font-weight: bold;">
                        <tbody>
                            <tr style="font-weight:bold; color:#650B14;">
                                <th style="text-align:center;">&nbsp;</th>
                                <th style="text-align:center;">Price/ea.</th>
                                <th style="text-align:center;">Qty.</th>
                                <th style="text-align:center;">&nbsp;</th>   
                                                        
                            </tr>
                            <?php 
                            if($tvc_price>0)
                            {
                            ?>
                            <tr>
                                <td style="text-align:left; vertical-align: bottom;">Smart Contract: Target Value US$<?=number_format($target_value,2)?>/YEM.</td>
                                <td style="text-align:center;vertical-align: bottom;"><?=number_format($tvc_price,0)?> TVC</td>
                                <td style="text-align:center;vertical-align: bottom;"><input type="number" class="form-control" id="qty" style="width:100px;"></td>
                                <td style="text-align:center;vertical-align: bottom;"><a href="javascript:void(0)" onclick="confirm2(<?=$id?>)" id="confirmbtn"><button class="btn btn-primary" style="width:180px; background:#650B14; color:#fff; cursor:default;">MAKE RESERVATION</button></a></td>   
                            </tr>
                            <?php 
                            }
                            ?>
                            
                        </tbody>
                    </table>	
                    <?
                    }
                    else 
                    {
                    ?>
                    <div class="alert alert-danger" role="alert" style="margin-top:10px; text-align:center;">CLOSED</div>
                    <?    
                    }
                    ?>            
                    <p style="font-weight:bold; font-size: 1.5rem;">Route Schedule</p>

                    <table class="table table-responsive table-striped" style="color: #650B14; border: 1px #650B14 solid; text-align: left; font-size: 14px; font-weight: bold;">
                        <tbody>
                            <tr style="font-weight:bold; background: #650B14; color:#fff;">
                                <th style="text-align:center;">DAY</th>
                                <th style="text-align:center;">CITY</th>
                                <th style="text-align:center;">ACTIVITY</th>
                                
                                                        
                            </tr>
                            <?php 
                            $select = "select * from ivc_route_schedule where vid='".$id."'";
                            //print $select;
                            $res = $GLOBALS ['mysqli']->query ($select) or die ($GLOBALS ['mysqli']->error . __LINE__);
                            if ($res->num_rows > 0)
                            {
                                while($row2 = $res->fetch_assoc ())
                                {
                                    extract($row2);
                            ?>
                            <tr>
                                <td style="text-align:left; vertical-align: bottom;" nowrap><?=$day?></td>
                                <td style="text-align:left;vertical-align: bottom;" nowrap><?=$city?></td>
                                <td style="text-align:left;vertical-align: bottom;"><?=$activity?></td>
                                
                            </tr>
                            <?php
                                } 
                            }
                            ?>
                            
                        </tbody>
                    </table>

                    <p style="font-size: .9rem; font-style:italic;">All trademarks, service marks, trade names, brand names, destinations, pictures, and logos are the property of their respective owners. International Vacation Club (IVC) is not affiliated with these owners. IVC is only providing this travel service, all vacations will be booked via local, national, regional, and international officially approved and licensed travel agents and providers. Only genuine vacations will be booked-guaranteed! If for any reason the Smart Contract is canceled, the TVC will be fully refunded. Travel is booked with the current available options and features based on participating vendors, destinations, services and location at the time the Smart Contract is activated within the allotted budget of the Smart Contract.</p>

                    <p style="text-align:center;">        
                        <a href="vacation.php"><button class="btn btn-primary" style="margin:0 auto; width:180px; background:#650B14; color:#fff; cursor:default;">BACK</button></a>
                    </p>            
                </div>
            </div>  
        </div>
    </div>
<script>
function confirm2(id)
{

    var qty = $("#qty").val();
    
    if(confirm("Are your sure your want to order "+qty+" tickets?"))
	{
		if(qty>0 && id>0)
		{
            $('#confirmbtn').hide();
            $('#err').hide();
            $('#suc').hide();
			$.post("confirm2.ajax.php",
			{
				id: id,
				qty: qty
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
                    $('#err').hide();

                    $('#suc').html(qty+' Tickets have been reserved successfully.');
					
                    $('#suc').show();
                    

                    //$('#confirmbtn').show();
					
				}
			});
		}  
	}

}
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