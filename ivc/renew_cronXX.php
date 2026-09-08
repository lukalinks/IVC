<?php

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

        
$select = "select * from ivc_membership where id IN (select max(id) as id from ivc_membership where paid_status='paid' and auto_renew=1 group by uid order by id desc)";
//print $select;
$res = $GLOBALS ['mysqli']->query ($select) or die ($GLOBALS ['mysqli']->error . __LINE__);//print $select;

if ($res->num_rows > 0)
{

    ///////////////////////////////////
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


    ///////////////////////////////////

    while($row = $res->fetch_assoc ())
    {
        $date_payment = date('Y-m-d', strtotime($row['date_payment']));
        $time = strtotime($row['date_payment']);
        $final = date("Y-m-d", strtotime("+1 month", $time));

        $today = date('Y-m-d');
        //print $row['id'].'==='.$final.'==='.$today; exit;
        if($final<=$today && $row['currency']=='YEM')
        {
            

            /////////////////////////////////
            $category=$row['category'];
            $membership=$row['membership'];
            $currency=$row['currency'];
            if($category=='single')
            {
                if($membership=='3')
                {
                    $amount=39;
                }
                else if($membership=='4')
                {
                    $amount=59;
                }
                else if($membership=='5')
                {
                    $amount=79;
                }
                else if($membership=='vip')
                {
                    $amount=149;
                }
            }
            else if($category=='couple')
            {
                if($membership=='3')
                {
                    $amount=69;
                }
                else if($membership=='4')
                {
                    $amount=99;
                }
                else if($membership=='5')
                {
                    $amount=129;
                }
                else if($membership=='vip')
                {
                    $amount=249;
                }
            }
            else if($category=='family')
            {
                if($membership=='3')
                {
                    $amount=89;
                }
                else if($membership=='4')
                {
                    $amount=129;
                }
                else if($membership=='5')
                {
                    $amount=169;
                }
                else if($membership=='vip')
                {
                    $amount=299;
                }
            }
            else if($category=='group')
            {
                if($membership=='3')
                {
                    $amount=159;
                }
                else if($membership=='4')
                {
                    $amount=209;
                }
                else if($membership=='5')
                {
                    $amount=259;
                }
                else if($membership=='vip')
                {
                    $amount=399;
                }
            }

            $amount = number_format($amount, 2, '.', '');
    


            /////////////////////////////////
            $discount=$amount*10/100;
            $amount = $amount-$discount;
            $amount=$amount/$twvalue;
            $amount = number_format($amount, 3, '.', '');


            ////////GET BALANCE/////////
            $submit_vars = array(
            
                'apikey'			=> "59u8Rn93626fCju99J9WA94n",
                'uid'         => $row['uid'],
                'asset'             => 'YEM'
                
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

            $suid=$row['uid'];

            $ftw = getSingleValue('pi_account',"where uid='$suid'",'free_yem');
            $tbalance = $tbalance-$ftw;
            if($tbalance<0)
            {
                $tbalance=0;
            }    

            if($tbalance<$amount)
            {

                print $row['uid'].'     '.$row['id'].'     no balance<br>';
            }
            else
            {

                //////////////////////////////////////////
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

                $fiatAmount = round($amount * $twvalue, 2);

                //print $twvalue; exit;

                $from_uid = $row['uid'];
                $to_uid = 898215;
                $submit_vars = array(
                    
                    'apikey'			=> "59u8Rn93626fCju99J9WA94n",
                    'txnAmount'         => $amount,
                    'valueUSD'         => $fiatAmount,
                    'countryFrom'		    => $countryfrom,
                    'countryTo'		    => $countryto,
                    'reason'			=> urlencode("IVC Membership"),
                    'accountFrom'		=> $from_uid,
                    'accountTo'			=> $to_uid,
                    'asset'             => "YEM"
                    
                );

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
                    
                    $select = "insert into ivc_membership (uid, category, membership, currency, amount, date_added, date_payment, hash, paid_status, auto_renew, txnid) values ($suid, '$category', '$membership', '$currency', $amount, NOW(), '$final', '$hash', 'paid', 1, 'auto')";
                    //print $select;
                    $GLOBALS ['mysqli']->query ($select) or die ($GLOBALS ['mysqli']->error . __LINE__);

                    $id=$GLOBALS ['mysqli']->insert_id;
    
    
                    $amount=number_format($amount,3);

                    $item = $category."-IVC-$id";
                    $buyerPernum = '1'.str_pad($from_uid, 9, '0', STR_PAD_LEFT);
                    $cust=$id;
                    $urlcom = "https://wesharesuccess.com/book_commission_tw.php?api_key=VXKyVP9Xtur8GQBt7chOZIddEsZtuuzG&amount=".$amount."&percent=10&item=".$item."&website=IVC&buyer_pernum=".$buyerPernum."&refid=".$buyerRefer."&custom=".$cust;
                    
                    $curl = curl_init($urlcom);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_HEADER, false);
                    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    
                    $response = curl_exec($curl);
                    //print $response;
                    curl_close($curl);






                    print $row['uid'].'     '.$row['id'].'     renewed<br>';
                    //$id=$GLOBALS ['mysqli']->insert_id;

                    //$amount=number_format($amount,3);
                    //print "success||$id";
                    //exit;
                }
                else 
                {
                    print $row['uid'].'     '.$row['id'].'     error<br>';
                    
                }    


                //////////////////////////////////////////
            }
            /////////////////////////////////
        }
    }
}
?>