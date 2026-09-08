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


$select = "select * from ivc_reservations where uid=$uid";
//print $select;
$res = $GLOBALS ['mysqli']->query ($select) or die ($GLOBALS ['mysqli']->error . __LINE__);
?>

<div class="row" style="margin:0 auto; max-width:1000px;  margin-top:70px; padding:15px;">
			<div class="col-md-12" style="text-align:center;">                  
						<img src="images/RESERVATIONS.png" class="img img-responsive" style="margin:0 auto;">
			</div>
</div>
<div class="row" style="margin:0 auto; max-width:1000px;  margin-top:5px; padding:15px; border: 1px #650B14 solid; min-height:500px; background:url(assets/img/Picture5.jpg); background-size: cover;">
			

            <div class="col-md-12" style="font-size:18px; color:#650B14;  background: rgba(255, 255, 255, 0.7);">  
                  <p style="font-weight:bold; font-size: 1.5rem; text-align:center;">IVC RESERVATIONS</p>
                  <table class="table table-responsive table-striped table-bordered" style="margin: 0 auto; color: #650B14; border: #650B14; text-align: left; font-size: 14px; font-weight: bold;">
                      <tbody>
                          <tr style="font-weight:bold; background: #650B14; color:#fff;">
                              <th style="text-align:center;">ID</th>
                              <th style="text-align:center;">VID</th>
                              <th style="text-align:center;">QTY</th>
                              <th style="text-align:center;">HASH</th>
                              <th style="text-align:center;">DATE</th>
                              <th style="text-align:center;">TVC</th>
                              <th style="text-align:center;">TARGET VALUE</th>
                              <th style="text-align:center;">TARGET DATE</th>
                                                     
                          </tr>
<?php
if ($res->num_rows > 0)
{
    while($row = $res->fetch_assoc ())
    {
      extract($row);

     
      
?>
    
    
    	
	
    
                    
                    <tr>
                        <td style="text-align:left;vertical-align: bottom;"><?=$id?></td>
                        <td style="text-align:left;vertical-align: bottom;"><?=$vid?></td>
                        <td style="text-align:left;vertical-align: bottom;"><?=$qty?></td>
                        <td style="text-align:left;vertical-align: bottom;"><?=$hash?></td>
                        <td style="text-align:left;vertical-align: bottom;"><?=$date?></td>
                        <td style="text-align:left;vertical-align: bottom;"><?=$tvc?></td>
                        <td style="text-align:left;vertical-align: bottom;">USD <?=$target?></td>
                        <td style="text-align:left;vertical-align: bottom;"><?=$target_date?></td>
                        
                    </tr>
                    
           
                    
       
    
				
<?php
    }
}
else
{

   
}

?>
</tbody>
            </table>    
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