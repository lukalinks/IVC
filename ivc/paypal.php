<?php
session_start(); 
include("config.php");

include("functions.php");

//$qry="insert into paypal set val='".implode($_POST)."'";
//$GLOBALS ['mysqli']->query ($qry) or die ($GLOBALS ['mysqli']->error . __LINE__);

$paypal_email = 'info@vip-solutions.co';
//$paypal_email = 'sb-acild149838@business.example.com';
$return_url = 'https://ivc.travel/home.php';

$cancel_url = 'https://ivc.travel/home.php';

$notify_url = 'https://ivc.travel/paypal.php';

if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"])){

	

	if(!$_SESSION['uid']){

		//require_once "login.html";exit;

		//echo "Please login!";exit;
		header('location: https://www.ivc.travel/login.php');
		exit;
		//header("Location: https://www.perfectinter.net/login.php");

	}else{

		//Firstly Append paypal account to querystring
		if(!is_numeric($_POST['id']))
		{
			header("Location: https://www.ivc.travel/home.php");	
			exit;
			
        }
        
        $id = $GLOBALS ['mysqli']->real_escape_string ($_POST['id']); 


        $select = "SELECT * FROM ivc_membership where id=$id";
        //print $select.'<br>';
        $res = $GLOBALS ['mysqli']->query ($select) or die ($GLOBALS ['mysqli']->error . __LINE__);
        if ($res->num_rows > 0)
        {
            $row = $res->fetch_assoc ();
            //print $row[$field].'<br>';
            $category = $row['category'];
            $currency = $row['currency'];
            $membership = $row['membership'];
            $amount = $row['amount'];
        }
        else{
            header("Location: https://www.ivc.travel/home.php");	
			exit;
        }
        //print $amount; exit;
		
		
		$timee=time();	
		
			
		
		$querystring .= "?business=".urlencode($paypal_email)."&";

		//Append amount& currency (�? to quersytring so it cannot be edited in html

		//The item name and amount can be brought in dynamically by querying the $_POST['item_number'] variable.

		//loop for posted values and append to querystring

	 	foreach($_POST as $key => $value){

			$value = urlencode(stripslashes($value));

			$querystring .= "$key=$value&";

		}

		//Append paypal return addresses
		$querystring .= "item_name=IVC Membership $category $membership star&";
		$querystring .= "amount=$amount&";
		$querystring .= "quantity=1&";
		//querystring .= "&on0=".$_POST['hours'];
		//$querystring .= "&os0=".$_POST['hours'];
		$querystring .= "custom=".$_SESSION['uid']."_".$id."_".$membership."&";
		
		$querystring .= "return=".urlencode(stripslashes($return_url))."&";

	    $querystring .= "cancel_return=".urlencode(stripslashes($cancel_url))."&";

		$querystring .= "notify_url=".urlencode($notify_url);	

		//Append querystring with custom field

		?>
        <form action="https://www.paypal.com/cgi-bin/webscr" id="frm" method="post" target="_top">
            <input type="hidden" name="cmd" value="_xclick">
            <input type="hidden" name="business" value="info@vip-solutions.co">
            <input type="hidden" name="lc" value="GB">
            <input type="hidden" name="item_name" value="Membership Id <?=$id?>">
            <input type="hidden" name="amount" value="<?=$amount?>">
            <input type="hidden" name="currency_code" value="<?=$currency?>">
            <input type="hidden" name="button_subtype" value="services">
            <input type="hidden" name="no_note" value="1">
            <input type="hidden" name="no_shipping" value="1">
            <input type="hidden" name="custom" value="<?=$_SESSION['uid'].'_'.$id.'_'.$membership?>">
            <input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHosted">
            <input type="hidden" name="notify_url" value="<?=$notify_url?>">
            <input type="hidden" name="return" value="<?=$return_url?>">
            <input type="hidden" name="cancel_return" value="<?=$cancel_url?>">
        </form>
        <script>
            document.getElementById('frm').submit();
        </script>    

        <?php
        //print "https://www.paypal.com/cgi-bin/webscr".$querystring;
        exit;
		

		//Redirect to paypal IPN

		//header('location:https://www.sandbox.paypal.com/cgi-bin/webscr'.$querystring);

		//header('location:https://www.paypal.com/cgi-bin/webscr'.$querystring);

		//exit(); 

	}



}else{

	 // Response from PayPal

	// read the post from PayPal system and add 'cmd'

	$req = "cmd=_notify-validate";

	

	foreach ($_POST as $key => $value) {

		$value = urlencode(stripslashes($value));

		$value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i','${1}%0D%0A${3}',$value);// IPN fix

		$req .= "&$key=$value";

	}
    $qry="insert into paypal set val='$req'";
    $GLOBALS ['mysqli']->query ($qry) or die ($GLOBALS ['mysqli']->error . __LINE__);
	// assign posted variables to local variables

	$item_name = $_POST['item_name'];

	$item_number = $_POST['item_number'];

	$payment_status = $_POST['payment_status'];

	$payment_amount = $_POST['mc_gross'];

	$payment_currency = $_POST['mc_currency'];

	$txn_id = $_POST['txn_id'];

	$receiver_email = $_POST['receiver_email'];

	$payer_email = $_POST['payer_email'];

	
    //$_SESSION['uid']."_".$ref."_".$code
	$temp = explode("_",$_POST['custom']);//uid

	$uid = $temp[0];//uid

	$refnum = $temp[1];//fid
	
	$code = $temp[2];//consumed dp
	
	$price = $_POST['mc_gross'];//price

	$txn_id = $_POST['txn_id'];//txn_id

	$currency = $payment_currency;

	//$phone = $_POST['phone'];

	$firstname = $_POST['first_name'];

	

	// post back to PayPal system to validate

	$header  = "POST /cgi-bin/webscr HTTP/1.1\r\n";
	
	$header .= "Host: www.paypal.com\r\n";

	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";

	$header .= "Content-Length:" . strlen($req) . "\r\n\r\n";

	$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);

	//$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);

	

	if (!$fp) {

		// HTTP ERROR
		

	}else {

	 	
		
		
		fputs ($fp, $header . $req);
		
		while (!feof($fp)) {

			$res = fgets ($fp, 1024);
			
            //$GLOBALS["DB"]->execute("insert into paypal set val='$res'");
            $qry="insert into paypal set val='$res'";
            $GLOBALS ['mysqli']->query ($qry) or die ($GLOBALS ['mysqli']->error . __LINE__);
			
			if (strcmp ($res, "VERIFIED") >= 0) {
				
				// check the payment_status is Completed

				// check that txn_id has not been previously processed

				// check that receiver_email is your Primary PayPal email

				// check that payment_amount/payment_currency are correct

				// process payment

				//$data['uid'] = $uid;

				//$data['refnum'] = $refnum;
				
				//$data['hours'] = $hours;

				//$data['payment_amount'] = $price;

				//$data['payment_status'] = 0;

				//$data['txnid'] = $txn_id;

				$createdate = time();

				
				if($payment_status=="Completed"){//pay success
					
					// txn_id:validate the returned data and store the response in the database.

					//$txn = $GLOBALS["DB"]->row("SELECT id,txnid FROM aap_orders WHERE txnid='$txn_id' LIMIT 1");

					//if(!isset($txn['id'])){

						
						$qry="update ivc_membership set paid_status='paid', txnid='$txn_id' where id=$refnum and uid1=$uid";
                        $GLOBALS ['mysqli']->query ($qry) or die ($GLOBALS ['mysqli']->error . __LINE__);

					//}

				}else{//pay error

					//$rid = $GLOBALS["DB"]->insert("INSERT INTO ids_deal_records set ".implode_field_value($data));

					

				}

				

			

			}else if (strcmp ($res, "INVALID") == 0) {

				// log for manual investigation
				

			}

			

		}

		fclose ($fp);

	}

}