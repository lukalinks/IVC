<?php
include("config.php");
include("functions.php");

//print_r($_SESSION["skey"]);
//exit;

if($_POST)
{
    $pernum = isset($_POST['pernum']) ? $_POST['pernum'] : '';
    $pwd_raw = isset($_POST['pwd']) ? $_POST['pwd'] : '';
    $pin = isset($_POST['pin']) ? (string)$_POST['pin'] : '';

    $pernum=intval($pernum);
    $pernum2=str_pad($pernum, 10, "0", STR_PAD_LEFT);
    
    //$pin=intval($pin);

    $uid = getSingleValue("pernum", "where pernum='$pernum2'", "uid");

    if($uid<=0)
    {
        $uid=$pernum-1000000000;
    }

    if($uid<=0)
    {
        die("Error: Invalid Login Credentials (1001).");
    }

    if($pwd_raw==='')
    {
        die("Error: Invalid Login Credentials (1002).");
    }

    $pwd=md5($pwd_raw);
    $pwd_esc = $GLOBALS ['mysqli']->real_escape_string ($pwd_raw);

    if($pin==='')
    {
        die("Error: Invalid Login Credentials (1003).");
    }

    $email = getSingleValue("pi_account", "where uid=$uid and deleted=0", "email");
    $account_uid = getSingleValue("pi_account", "where uid=$uid and deleted=0", "uid");

    if($account_uid<=0)
    {
        die("Error: Invalid Login Credentials (1004).");
    }

    $banned = getSingleValue("banned_users", "where uid=$uid", "uid");

    if($banned>0)
    {
        print 'Error: Your account has been suspended. Please contact support.';
        exit;
    }

    $blocked = getSingleValue("pi_account", "where uid=$uid", "blocked");
    if($blocked==1)
    {
        $blocked_msg = getSingleValue("pi_account", "where uid=$uid", "blocked_msg");
        if($blocked_msg=='')
            print 'Your account is blocked, contact Support immediately at service@safezone.info.';
        else
            print $blocked_msg;    
        exit;
    }

    $pin_tries = getSingleValue("pi_account", "where uid=$uid", "pin_tries");

    if($pin_tries>=3)
    {
        //print 'Your account is locked for 30 minutes.';
		//exit;
    }

    $valid = getSingleValue("pi_account", "where uid=$uid and password='$pwd'", "uid");
    if($valid<=0)
    {
        $valid = getSingleValue("pi_account", "where uid=$uid and password='$pwd_esc'", "uid");
    }

    if($valid<=0)
    {
        die("Error: Invalid Login Credentials (1005).");
    }

    $userpin = getSingleValue("pi_account", "where uid=$uid", "pin");

    if($userpin=='')
    {
        die("Error: Invalid Login Credentials (1006).");
    }

    $passcode = '1234';
	$methodcode = 'aes128';

	$ivcode = "1234567812345678";

    $short_pin = $pin;

    $tim=time();
				
	$pinn =  openssl_decrypt($userpin, $methodcode, $passcode,false,$ivcode);
    if($pinn===false || $pinn==='')
    {
        $pinn = $userpin;
    }

    $u = 0;
    if(!empty($_SESSION["skey"]))
    {

    
        foreach($_SESSION["skey"] as $i=>$v)
        {
            //echo $data;exit();
            if(!isset($pinn[$i]) || !isset($short_pin[$u]) || $pinn[$i]!=$short_pin[$u])
            {
                
                $update="update pi_account set pin_tries=pin_tries+1, last_try=$tim where uid=$uid";
                
                $GLOBALS ['mysqli']->query ($update) or die ($GLOBALS ['mysqli']->error . __LINE__);
                
                die("Error: Invalid Login Credentials (1007).");
            }	
                $u++;			
        }
    }
    else
    {
        die("Error: Invalid Login Credentials (1008).");
    }

    if($uid>0)
    {
        $_SESSION['uid']=$uid;

        
        $_SESSION['email']=$email;

        $GLOBALS ['mysqli']->query ("update pi_account set pin_tries=0 where uid=$uid");

        print "success";
    }
    else
    {
        die("Error: Not allowed.");
    }

}
else
{
    die("Error: Invalid Login Credentials (1009).");
}
?>