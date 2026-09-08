<?php
session_start();
include("config.php");
include("functions.php");

if (empty($_SESSION['uid'])) {
    header("Location: login.php");
    exit();
}

$uid=$_SESSION['uid'];
$err='';
$msg='';
$bookingsOpen = !function_exists('ivc_setting') || ivc_setting('bookings_enabled', '1') === '1';
$resortOptions = function_exists('ivc_active_resorts') ? ivc_active_resorts() : array();
if (!$resortOptions) {
    $resortOptions = array(
        array('code' => 'LAGUNA PALACE', 'name' => 'LAGUNA PALACE'),
        array('code' => "GATOR'S HIDEAWAY", 'name' => "GATOR'S HIDEAWAY"),
    );
}
$selectedResort = isset($_GET['resort']) ? $_GET['resort'] : '';
if($_POST){
    if (!$bookingsOpen) {
        $err = 'Booking requests are temporarily closed.';
    } else {
    foreach($_POST as $k=>$v)
    {
        $_POST[$k]=$GLOBALS ['mysqli']->real_escape_string ($v);
    }

    extract($_POST);

    if($arrival_date<date('Y-m-d')){
        $err="Please enter valid Arrival Date.";
    }

    if($err=='')
    {
        $qry="INSERT INTO `ivc_bookings` (`uid`, `resort`, `no_of_guests`, `arrival_date`, `no_of_nights`, `accomodation`, `restaurant`, `date`, email, status) VALUES ('$uid', '$resort', '$no_of_guests', '$arrival_date', '$no_of_nights', '$accomodation', '$restaurant', NOW(), '$email', 'pending')";

        $GLOBALS ['mysqli']->query ($qry) or die ($GLOBALS ['mysqli']->error . __LINE__);

        $msg="Your Booking Request has been sent successfully!";

    }
    }
    

}
include('header.php');
?>
    
<style>
.table-striped tbody tr:nth-of-type(odd) {
    background-color: #F8D7CD;
}

.table-striped tbody tr:nth-of-type(even) {
    background-color: #FCECE8;
}
</style>    
    	
	<div class="row" style="max-width:1000px; margin:0 auto; border: 1px #650B14 solid; padding: 20px; margin-top: 100px;">

					<div class="col-md-12" style="text-align: center;">
						<!--p class="alert alert-warning">We are currently updating our website, please check back periodically for updates. Thank you for your understanding.</p-->
                        <?php
                        if(!$bookingsOpen)
                        {
                        ?>
                            <p class="alert alert-warning">Booking requests are temporarily closed.</p>
                        <?php
                        }
                        if($err!='')
                        {
                        ?>
						    <p id="err" class="alert alert-danger"><?=$err?></p>
                        <?php
                        }
                        if($msg!='')
                        {
                        ?>
                            <p id="msg" class="alert alert-success"><?=$msg?></p>
                        <?php
                        }
                        ?>
						<div class="row">
							<div class="col-md-12" style="text-align: center;">
                                <form action="" method="post">
                                <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead style="background:#ED7D31; font-size:30px; color:#fff;">
                                        <tr>
                                            <th class="text-center" scope="col" colspan=2>BOOKING REQUEST</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size:22px;">
                                        <tr>
                                            
                                            <td class="text-right">RESORT:</td>
                                            <td class="text-left">
                                                <select name="resort" class="form-control" required <?= empty($bookingsOpen) ? 'disabled' : '' ?>>
                                                    <option value="">SELECT</option>
                                                    <?php foreach ($resortOptions as $opt): ?>
                                                    <option value="<?= htmlspecialchars($opt['code']) ?>"<?= $selectedResort === $opt['code'] ? ' selected' : '' ?>><?= htmlspecialchars($opt['name']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            
                                            <td class="text-right">NUMBER OF GUESTS:</td>
                                            <td class="text-left">
                                                <select name="no_of_guests" class="form-control" required>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            
                                            <td class="text-right">ARRIVAL DATE:</td>
                                            <td class="text-left">
                                                <input type="date" name="arrival_date" value="" class="form-control" min="<?=date('Y-m-d')?>" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            
                                            <td class="text-right">NUMBER OF NIGHTS:</td>
                                            <td class="text-left">
                                                <select name="no_of_nights" class="form-control" required>
                                                <?php
                                                for($i=1; $i<=30; $i++)
                                                {
                                                ?>
                                                    <option value="<?=$i?>"><?=$i?></option>
                                                <?php
                                                }
                                                ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <!--tr>
                                            
                                            <td class="text-right">DEPARTURE DATE:</td>
                                            <td class="text-left">
                                                <input type="date" name="departure_date" value="" class="form-control" min="<?=date('d/m/Y')?>">
                                            </td>
                                        </tr-->
                                        <tr>
                                            
                                            <td class="text-right">ACCOMMODATION:</td>
                                            <td class="text-left">
                                                <select name="accomodation" id="accomodation" class="form-control" required onchange="changeOptions()">
                                                    <option value="50% YEM">50% YEM</option>
                                                    <option value="100% YEM">100% YEM</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            
                                            <td class="text-right">RESTAURANT:</td>
                                            <td class="text-left">
                                                <select name="restaurant" id="restaurant" class="form-control" required>
                                                    <option value="HALF BOARD 50% YEM">HALF BOARD 50% YEM</option>
                                                    <option value="FULL BOARD 50% YEM">FULL BOARD 50% YEM</option>
                                                </select>
                                            </td>
                                        </tr>

                                        <tr>
                                            
                                            <td class="text-right">EMAIL:</td>
                                            <td class="text-left">
                                                <input type="email" name="email" value="<?=htmlspecialchars($_SESSION['email'] ?? '')?>" class="form-control" required>
                                            </td>
                                        </tr>

                                        
                                    </tbody>
                                </table>
                                </div>

                                <button type="submit" class="btn btn-success" style="max-width:400px; width:100%; background:#E3986E; font-size:24px;" <?= empty($bookingsOpen) ? 'disabled' : '' ?>>SEND BOOKING REQUEST</button>
                                </form>
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
if (navbar) {
    navbar.classList.add("sticky");
}

function changeOptions(){
    if($('#accomodation').val()=='50% YEM')
    {
        $('#restaurant')
        .empty()
        .append('<option value="HALF BOARD 50% YEM">HALF BOARD 50% YEM</option>')
        .append('<option value="FULL BOARD 50% YEM">FULL BOARD 50% YEM</option>');
    }
    else
    {
        $('#restaurant')
        .empty()
        .append('<option value="HALF BOARD 100% USD">HALF BOARD 100% USD</option>')
        .append('<option value="FULL BOARD 100% USD">FULL BOARD 100% USD</option>');
    }
    
}
</script>				
<?php
include('footer.php');
?>