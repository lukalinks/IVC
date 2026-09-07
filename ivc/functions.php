<?php 
function getNumbersFromText($inp){
	$result=array();
	$inp = strtolower($inp);
	$keypad = array('a' => '2', 'b' => '2', 'c' => '2', 'd' => '3',
		'e' => '3', 'f' => '3', 'g' => '4', 'h' => '4',
		'i' => '4', 'j' => '5', 'k' => '5', 'l' => '5',
		'm' => '6', 'n' => '6', 'o' => '6', 'p' => '7',
		'q' => '7', 'r' => '7', 's' => '7', 't' => '8',
		'u' => '8', 'v' => '8', 'w' => '9', 'x' => '9',
		'y' => '9', 'z' => '9', '0' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9');

	for ($x=0; $x<strlen($inp); $x++){
		$letter = $inp[$x];
		if($letter=='0')
		{
			$result[]='0';
		}
		elseif ($keypad[$letter]) 
		{
			$result[]= $keypad[$letter];
		}
	}
	return implode('',$result);
}


function getTotRecords($field,$table,$where)
	{
		$select = "SELECT ".$field." FROM `".$table."` ".$where;
		$rows = $GLOBALS ['mysqli']->query ($select) or die ($GLOBALS ['mysqli']->error . __LINE__);
		return $rows->num_rows;
	}
	
function getSingleValue($table,$where,$field)
{
	$select = "SELECT ".$field." FROM `".$table."` ".$where;
	//print $select.'<br>';
	$res = $GLOBALS ['mysqli']->query ($select) or die ($GLOBALS ['mysqli']->error . __LINE__);
	if ($res->num_rows > 0)
	{
		$row = $res->fetch_assoc ();
		//print $row[$field].'<br>';
		return $row[$field];
	}
	else
	{
		return '';
	}
	
}	

function getRows($table,$where,$field)
{
	$select = "SELECT ".$field." FROM `".$table."` ".$where;
	$res = $GLOBALS ['mysqli']->query ($select) or die ($GLOBALS ['mysqli']->error . __LINE__);
	return $res;
	
	
}	

function userLogout()
{
	@session_start();

	// Unset all of the session variables.
	$_SESSION = array();
	$time = time()-3600;
	setcookie("uid",'',$time,"/");
	setcookie("password",'',$time,"/");
	setcookie("token",'',$time,"/");
	
	// Finally, destroy the session.
	
	$_SESSION = array();
	
}

function GetIP()
{ 
	if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) 
		$ip = getenv("HTTP_CLIENT_IP"); 
	else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) 
		$ip = getenv("HTTP_X_FORWARDED_FOR"); 
	else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) 
		$ip = getenv("REMOTE_ADDR"); 
	else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) 
		$ip = $_SERVER['REMOTE_ADDR']; 
	else 
		$ip = "unknown"; 
	return  $ip; 
 }


function login ($uid,$password, $autologin=0)
{
	
	
	if($autologin==1)
	{
		
						
		$uid = $GLOBALS ['mysqli']->real_escape_string ($uid);
		$pwd = $GLOBALS ['mysqli']->real_escape_string ($password);
		$pwd_md5 = $GLOBALS ['mysqli']->real_escape_string (md5($password));
						
		$select = "SELECT a.url, a.psm, a.psa, a.factor_fixed, a.pin,a.invitedby,a.username,a.password,a.uid,a.email,p.mname, p.fname,p.lname,p.gender,p.con_id,p.address,p.city,p.state,p.zip,p.dob,p.pic,a.deal_points FROM pi_account a left join pi_profile p on a.uid=p.uid  WHERE (a.uid='$uid') AND (a.password='$pwd' OR a.password='$pwd_md5')";
	}
	else
	{
		
	}
	$res = $GLOBALS ['mysqli']->query ($select) or die ($GLOBALS ['mysqli']->error . __LINE__);
	if ($res->num_rows > 0)
	{
		$row = $res->fetch_assoc ();
		extract($row);
		
		$_SESSION['uid']=$uid;

		$_SESSION['url']=$url;
		$_SESSION['psm']=$psm;
		$_SESSION['psa']=$psa;
		$_SESSION['factor']=$factor_fixed;

		$_SESSION['invitedby']=$invitedby;

		$_SESSION['fname']=$fname;

		$_SESSION['lname']=$lname;

		$_SESSION['mname']=$mname;

		$_SESSION['username']=$username;

		$_SESSION['email']=$email;

		$_SESSION['gender']=$gender;

		$_SESSION['con_id']=$con_id;

		$_SESSION['country']=$con_id;

		$address=explode('||', (string)$address);

		$_SESSION['address']=$address[0];

		$_SESSION['address2']=$address[1];

		$_SESSION['city']=$city;

		$_SESSION['state']=$state;

		$_SESSION['zip']=$zip;

		$_SESSION['dob']=$dob;
		$remember=1;
		$time = time()+3600*24*365*10;
		
		
		setcookie("uid", $uid, $time, "/", '.twnklchain.com');
		
		$token = $uid."||".$password."||".$_SERVER['REMOTE_ADDR'];
		
		$cipher_method = 'AES-128-CTR';
		$enc_key = openssl_digest(php_uname(), '@786ALLAHISTHEGREATEST786@', TRUE);
		$enc_iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher_method));
		$crypted_token = openssl_encrypt($token, $cipher_method, $enc_key, 0, $enc_iv) . "::" . bin2hex($enc_iv);
		unset($token, $cipher_method, $enc_key, $enc_iv);
		
		
		
		setcookie("token",$crypted_token,$time,"/", '.twnklchain.com');
		setcookie("remember",$remember,$time,"/", '.twnklchain.com');
		//$_SESSION["pernum"]	  = get_userPerNum($uid);	

		$_SESSION['password']= $password;//md5($e_passsword);
		return true;
	}
	else
	{
		userLogout();
		return false;
	}
}

?>