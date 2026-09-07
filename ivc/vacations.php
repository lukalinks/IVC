<?
session_start();
if ($_SESSION['uid'] <= 0) {
    header("Location: index.php");
    exit();
}
include("config.php");
include("functions.php");
include('header.php');

$select = "select * from ivc_vacations where closed=0 order by id desc";
//print $select;
$res = $GLOBALS ['mysqli']->query ($select) or die ($GLOBALS ['mysqli']->error . __LINE__);
if ($res->num_rows > 0)
{
    while($row = $res->fetch_assoc ())
    {
      extract($row);

      $totreserved = getSingleValue('ivc_reservations',"where vid=$id",'sum(qty)');
?>
    
    
    	
	  <div class="row" style="margin:0 auto; max-width:1000px;  margin-top:70px; padding:15px;">
			<div class="col-md-12" style="text-align:center;">                  
						<img src="images/VACATIONS.png" class="img img-responsive" style="margin:0 auto;">
			</div>
    </div>

    <div class="alert alert-primary" style="margin:0 auto; max-width:1000px;">
    Notice to all International Vacation Club (IVC) Affiliates, you are not allowed to promote any specific travel or package seen on IVC. Simply promote IVC itself if you want to share. You can share the info on the landing page (before log in) without issue. If you have any questions, please contact Support. Thank you.
 
    </div>

    <div class="row" style="margin:0 auto; max-width:1000px;  margin-top:5px; padding:15px; border: 1px #650B14 solid; min-height:500px; background:url(assets/img/Picture5.jpg); background-size: cover;">
			<div class="col-md-6">                  
						
			</div>

      <div class="col-md-6" style="background: rgba(255, 255, 255, 0.3); font-size:18px; color:#650B14; text-align:center;">  
        <p style="font-weight:bold; font-size: 1.5rem;"><?=$title1?></p>

        <p style="font-weight:bold; font-size: 1.5rem;"><?=$title2?></p>
        <p style="font-weight:bold;"><?=$title3?></p>
        
        <p style="font-weight:bold;">Trip Date: <?=$trip_date?><br>
        Total Seats available: <?=$total_seats?><br>
        Value: US$<?=number_format($usd_value,2)?><br>
        

        </p>
        
        <p style="font-weight:bold;"><?=$title4?></p>
        <?
        if($totreserved>=$total_seats)
        {
        ?>
        <p><a href="vacation_details.php?id=<?=$id?>"><button type="button" class="btn btn-danger">CLOSED</button></a></p>
        <?
        }
        else 
        {
          
        ?>
        <p><a href="vacation_details.php?id=<?=$id?>"><button type="button" class="btn btn-success">AVAILABLE</button></a></p>
        <?
        }
        ?>
        <p><a href="vacation_details.php?id=<?=$id?>"><button type="button" class="btn btn-primary">MORE INFO</button></a></p>
        
        
                    
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
<?
    }
  }
include('footer.php');
?>