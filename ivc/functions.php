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
		$rows = @$GLOBALS ['mysqli']->query ($select);
		if (!$rows) {
			return 0;
		}
		return $rows->num_rows;
	}
	
function getSingleValue($table,$where,$field)
{
	if (empty($GLOBALS['mysqli']) || $GLOBALS['mysqli']->connect_errno) {
		return '';
	}
	$select = "SELECT ".$field." FROM `".$table."` ".$where;
	$res = @$GLOBALS['mysqli']->query($select);
	if (!$res || $res->num_rows === 0) {
		return '';
	}
	$row = $res->fetch_assoc();
	return isset($row[$field]) ? $row[$field] : '';
	
}	

function ivc_get_value($table, $where, $field, $default = '')
{
	if (empty($GLOBALS['mysqli']) || $GLOBALS['mysqli']->connect_errno) {
		return $default;
	}
	$select = 'SELECT ' . $field . ' FROM `' . $table . '` ' . $where;
	$res = @$GLOBALS['mysqli']->query($select);
	if (!$res || $res->num_rows === 0) {
		return $default;
	}
	$row = $res->fetch_assoc();
	return isset($row[$field]) ? $row[$field] : $default;
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

function ivc_decrypt_master_pin($stored)
{
	$stored = (string) $stored;
	if ($stored === '') {
		return '';
	}
	$passcode = '1234';
	$ivcode = '1234567812345678';
	foreach (array('AES-128-CBC', 'aes128') as $method) {
		$plain = @openssl_decrypt($stored, $method, $passcode, false, $ivcode);
		if ($plain !== false && $plain !== '' && preg_match('/^[0-9]+$/', $plain)) {
			return $plain;
		}
	}
	if (preg_match('/^[0-9]+$/', $stored)) {
		return $stored;
	}
	return '';
}

function ivc_pin_challenge_ok($masterPin, $entered, $skey)
{
	$masterPin = preg_replace('/\D/', '', (string) $masterPin);
	$entered = preg_replace('/\D/', '', (string) $entered);
	if ($masterPin === '' || $entered === '' || !is_array($skey) || count($skey) === 0) {
		return false;
	}
	if ($entered === $masterPin) {
		return true;
	}
	$needed = '';
	foreach ($skey as $index => $value) {
		$pos = is_numeric($index) ? (int) $index : (int) $value;
		if (!isset($masterPin[$pos])) {
			return false;
		}
		$needed .= $masterPin[$pos];
	}
	return $entered === $needed;
}

function ivc_login_skey($post = null)
{
	if (!is_array($post)) {
		$post = $_POST;
	}
	if (!empty($_SESSION['skey']) && is_array($_SESSION['skey'])) {
		return $_SESSION['skey'];
	}
	if (empty($post['pinkey'])) {
		return null;
	}
	$raw = base64_decode((string) $post['pinkey'], true);
	if ($raw === false) {
		return null;
	}
	$skey = json_decode($raw, true);
	if (!is_array($skey) || count($skey) === 0) {
		return null;
	}
	$clean = array();
	foreach ($skey as $index => $value) {
		if (is_numeric($index) && is_numeric($value)) {
			$clean[(int) $index] = (int) $value;
		}
	}
	return count($clean) > 0 ? $clean : null;
}

function ivc_resolve_login_uid($pernumRaw)
{
	$digits = preg_replace('/\D/', '', (string) $pernumRaw);
	if ($digits === '') {
		return 0;
	}
	$pernum = (int) $digits;
	$pernum2 = str_pad($digits, 10, '0', STR_PAD_LEFT);

	$uid = (int) ivc_get_value('pernum', "where pernum='$pernum2'", 'uid', 0);
	if ($uid > 0) {
		return $uid;
	}

	if ($pernum > 0 && $pernum < 1000000000) {
		$uid = (int) ivc_get_value('pi_account', "where uid=$pernum and deleted=0", 'uid', 0);
		if ($uid > 0) {
		 return $uid;
		}
	}

	if ($pernum > 1000000000) {
		$uid = $pernum - 1000000000;
		if ($uid > 0) {
			$found = (int) ivc_get_value('pi_account', "where uid=$uid and deleted=0", 'uid', 0);
			if ($found > 0) {
				return $found;
			}
			return $uid;
		}
	}

	return 0;
}

function ivc_web_base()
{
	static $base = null;
	if ($base !== null) {
		return $base;
	}

	$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
	if (preg_match('#^(.*)/ivc(/.*)?$#', $scriptDir, $matches)) {
		$base = rtrim($matches[1], '/') . '/ivc';
	} else {
		$base = rtrim($scriptDir, '/');
		if (substr($base, -6) === '/admin') {
			$base = dirname($base);
		}
	}

	if ($base === '' || $base === '.') {
		$base = '/ivc';
	}

	return $base;
}

function ivc_asset($relative)
{
	$relative = ltrim(str_replace('\\', '/', (string) $relative), '/');
	return ivc_web_base() . '/assets/' . $relative;
}

function ivc_url($relative)
{
	$relative = ltrim(str_replace('\\', '/', (string) $relative), '/');
	return ivc_web_base() . '/' . $relative;
}

function ivc_password_matches($uid, $plain)
{
	$uid = (int) $uid;
	$plain = (string) $plain;
	if ($uid <= 0 || $plain === '') {
		return false;
	}
	$stored = ivc_get_value('pi_account', "where uid=$uid", 'password', '');
	if ($stored === '' || $stored === null) {
		return false;
	}
	$candidates = array($plain, trim($plain));
	foreach ($candidates as $candidate) {
		if ($candidate === '') {
			continue;
		}
		if (hash_equals($stored, md5($candidate))) {
			return true;
		}
		if (hash_equals($stored, $candidate)) {
			return true;
		}
	}
	return false;
}

?>