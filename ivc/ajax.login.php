<?php
include("config.php");
include("functions.php");

//print_r($_SESSION["skey"]);
//exit;

if($_POST)
{
    $pernum = isset($_POST['pernum']) ? preg_replace('/\D/', '', (string) $_POST['pernum']) : '';
    $pwd_raw = isset($_POST['pwd']) ? trim((string) $_POST['pwd']) : '';
    $pin = isset($_POST['pin']) ? (string)$_POST['pin'] : '';

    $uid = ivc_resolve_login_uid($pernum);

    if($uid<=0)
    {
        die("Error: Invalid Login Credentials (1001).");
    }

    if($pwd_raw==='')
    {
        die("Error: Invalid Login Credentials (1002).");
    }

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

    if(!ivc_password_matches($uid, $pwd_raw))
    {
        die("Error: Invalid Login Credentials (1005). The password does not match this account on the local database.");
    }

    $userpin = getSingleValue("pi_account", "where uid=$uid", "pin");

    $pinn = ivc_decrypt_master_pin($userpin);
    if($pinn==='')
    {
        die("Error: Invalid Login Credentials (1006).");
    }

    $tim=time();

    $skey = ivc_login_skey($_POST);
    if(empty($skey))
    {
        die("Error: Invalid Login Credentials (1008). Refresh the page and try again.");
    }

    if(!ivc_pin_challenge_ok($pinn, $pin, $skey))
    {
        $update="update pi_account set pin_tries=pin_tries+1, last_try=$tim where uid=$uid";
        $GLOBALS ['mysqli']->query ($update) or die ($GLOBALS ['mysqli']->error . __LINE__);
        die("Error: Invalid Login Credentials (1007). Enter the requested PIN positions in that order, or your full Master PIN.");
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