<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
session_start();
if ($_SESSION['uid'] <= 0) {
    print "err||Please login first.";
    exit();
}

////////CHECK STATUS////////////

$curlURL = YEMCHAIN_API_URL."/checkstatus.php";
            
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $curlURL);
curl_setopt($ch, CURLOPT_HEADER, 0);
//curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim( $fields, "& " ));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout in seconds
$resultCurl = curl_exec($ch);
if($resultCurl!='ok')
{
    print 'An error occurred. Please try again later.<br>';
    exit;
}

include("config.php");
include("functions.php");

$GLOBALS ['mysqli']->query ("SET NAMES UTF8") or die ($GLOBALS ['mysqli']->error . __LINE__);

$uid = $_SESSION['uid'];
$qty=$GLOBALS ['mysqli']->real_escape_string($_POST['qty']);
$vid=$GLOBALS ['mysqli']->real_escape_string($_POST['id']);

if($qty<1 || !is_numeric($qty))
{
    print "err||Please enter quantity.";
    exit;
}
if($vid<1 || !is_numeric($vid))
{
    print "err||Please select valid ID.";
    exit;
}


$already = getSingleValue('ivc_reservations',"where uid='$uid' and vid=$vid",'sum(qty)');

$already2 = getSingleValue('ivc_reservations',"where uid='$uid' and vid=1",'sum(qty)');

if($vid==2 && $already2>0)
{
    print "err||You already have a vacation reserved of this type.";
    exit;
}

$totreserved = getSingleValue('ivc_reservations',"where vid=$vid",'sum(qty)');

$allowed_member_type = getSingleValue('ivc_vacations',"where id='$vid'",'member_type');

$total_seats = getSingleValue('ivc_vacations',"where id='$vid'",'total_seats');
$tvc_price = getSingleValue('ivc_vacations',"where id='$vid'",'tvc_price');
$target_value = getSingleValue('ivc_vacations',"where id='$vid'",'target_value');
$target_date = getSingleValue('ivc_vacations',"where id='$vid'",'target_date');

$select = "select * from ivc_membership where uid=$uid and paid_status='paid' and membership='$allowed_member_type' order by id desc";

$res = $GLOBALS ['mysqli']->query ($select) or die ($GLOBALS ['mysqli']->error . __LINE__);
if ($res->num_rows > 0)
{
    $row = $res->fetch_assoc ();
    extract($row);
}
else
{
    print "err||You do not have the required membership level for this reservation.";
    exit;
}

if($category=='group')
    $category='grou';

$per_account = getSingleValue('ivc_vacations',"where id='$vid'","$category");

$single_limit = getSingleValue('ivc_vacations',"where id='$vid'",'single');
$couple_limit = getSingleValue('ivc_vacations',"where id='$vid'",'couple');
$family_limit = getSingleValue('ivc_vacations',"where id='$vid'",'family');
$group_limit = getSingleValue('ivc_vacations',"where id='$vid'",'grou');
print $group_limit;

$total_remaining = $total_seats-$totreserved;
if($total_remaining<0)
{
    $total_remaining=0;
}



if($qty>$per_account)
{
    print "err||Maximum $per_account tickets per account allowed. You have entered $qty.";
    if($qty<=$couple_limit)
        print " If you wish to reserve $qty tickets, upgrade your membership to the COUPLE category.";
    elseif($qty<=$family_limit)
        print " If you wish to reserve $qty tickets, upgrade your membership to the FAMILY category.";
    elseif($category!='grou')
        print " If you wish to reserve $qty tickets, upgrade your membership to the GROUP category.";    
    exit;
}

if($qty>$total_remaining)
{
    print "err||$total_remaining seats are available. You have entered $qty.";
    exit;
}

$remaining = $per_account-$already;
if($remaining<0)
{
    $remaining=0;
}



if($qty>$remaining)
{
    print "err||You can have $remaining tickets only. You have entered $qty.";
    exit;
}

//print $remaining; exit;

$date_payment = date('Y-m-d', strtotime($date_payment));
$time = strtotime($date_payment);
$final = date("Y-m-d", strtotime("+1 month", $time));

$today = date('Y-m-d');

//print $final.'       '.$today;
//exit;

if($final<=$today)
{
    print "err||Your membership has expired.";
    exit;

}

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

    

    ////////GET BALANCE/////////
    $submit_vars = array(
    
        'apikey'			=> "59u8Rn93626fCju99J9WA94n",
        'uid'         => $_SESSION['uid'],
        'asset'             => 'TVC'
        
    );
    
    $fields = "";
    foreach( $submit_vars as $key => $value ) $fields .= "$key=" . urlencode( $value ) . "&";
    
    $curlURL = YEMCHAIN_API_URL."/api/get-balance.php";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $curlURL);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim( $fields, "& " ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $resultCurl = curl_exec($ch);
    
    $twnkl = explode(':', $resultCurl);
    if($twnkl[0]=='success')
    {
        $tbalance=number_format($twnkl[1],3,'.','');
    }
    else
    {
        $tbalance=0;
    }

    $suid=$_SESSION['uid'];

    $amount = $qty*$tvc_price;

    if($tbalance<$amount)
    {

        print "err||You do not have sufficient TVC balance.";
        exit;
    }

    $json = file_get_contents('https://cryptorank.online/rankdata_with_price.php?apikey=tkfNseYgYsEE32n4uvxb');
    $jsonIterator = json_decode($json, TRUE);

    foreach ($jsonIterator as $key => $value)
    {
        //print $value['asset']."<br>";
        if($value['asset']=='TVC')
        {
            $yemval = $value['value'];
            $yemval =  explode('|',$yemval);
            $tvcvalue = number_format($yemval[1],4);
            
        }
        elseif($value['asset']=='TEC')
        {
            $tecval = $value['value'];
            $tecval =  explode('|',$tecval);
            $tecval = number_format($tecval[1],5,'.','');
            
        }
        
    }

    

    if($tvcvalue<=0)
    {
        $tvcvalue=100;
    }

    $con_id = getSingleValue('pi_profile',"where uid='$suid'",'tax_con_id');
    if($con_id>0)
    {
        $countryfrom = getSingleValue('geo_countries',"where con_id='$con_id'",'name');
        
    }

    //print $suid."-".$con_id."-".$countryfrom;
    //exit;
    if($countryfrom!='')
    {
        $countryfrom = urlencode($countryfrom);
    }

    $countryto = urlencode("United States");


    /////////CREATE TRANSACTION//////////////

    $fiatAmount = round($amount * $tvcvalue * $twvalue, 2);

    //print $twvalue; exit;

    $from_uid = $_SESSION['uid'];
    $to_uid = 898215;
    $submit_vars = array(
        
        'apikey'			=> "59u8Rn93626fCju99J9WA94n",
        'txnAmount'         => $amount,
        'valueUSD'         => $fiatAmount,
        'countryFrom'		    => $countryfrom,
        'countryTo'		    => $countryto,
        'reason'			=> urlencode("IVC Vacation Escrow vid $vid"),
        'accountFrom'		=> $from_uid,
        'accountTo'			=> $to_uid,
        'asset'             => "TVC"
        
    );

    //print_r($submit_vars);
    //exit;

    $fields = "";
    foreach( $submit_vars as $key => $value ) $fields .= "$key=" . urlencode( $value ) . "&";

    $curlURL = YEMCHAIN_API_URL."/api/create-trans.php";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $curlURL);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim( $fields, "& " ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $resultCurl = curl_exec($ch);

    $result = explode(':', $resultCurl);

    if($result[0]=='success')
    {
        $hash=$result[1];
        
        $select = "insert into ivc_reservations (uid, vid, qty, hash, tvc, date, target, target_date) values ($uid, '$vid', '$qty', '$hash', $amount, NOW(), '$target_value', '$target_date')";
        //print $select;
        $GLOBALS ['mysqli']->query ($select) or die ($GLOBALS ['mysqli']->error . __LINE__);
    
        $id=$GLOBALS ['mysqli']->insert_id;

        ///$amount=number_format($amount,3);
        print "success||$id";
        exit;
    }
    else 
    {
        print "err||An error occurred. Please try later.";
        exit;
        
    }


    ////////////////////////////

