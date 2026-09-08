<?php
require __DIR__ . '/safezone.bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	ivc_json_exit(array('success' => false, 'message' => 'Method not allowed'), 405);
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
	$input = $_POST;
}

$uid = (int) ($input['uid'] ?? 0);
$key = preg_replace('/\D/', '', (string) ($input['key'] ?? ''));
$pin = preg_replace('/\D/', '', (string) ($input['pin'] ?? ''));
$pernum = preg_replace('/\D/', '', (string) ($input['pernum'] ?? ''));

if ($uid <= 0 || $key === '' || $pin === '') {
	ivc_json_exit(array('success' => false, 'message' => 'Missing login data. Please start again.'));
}

if (strlen($key) !== 3) {
	ivc_json_exit(array('success' => false, 'message' => 'Invalid PIN session. Please login again.'));
}

if (strlen($pin) === 6) {
	$extracted = '';
	foreach (str_split($key) as $digit) {
		$pos = (int) $digit;
		if ($pos < 1 || $pos > 6 || !isset($pin[$pos - 1])) {
			ivc_json_exit(array('success' => false, 'message' => 'Enter the 3 requested PIN digits, not the full PIN in one block.'));
		}
		$extracted .= $pin[$pos - 1];
	}
	$pin = $extracted;
}

if (strlen($pin) !== 3) {
	ivc_json_exit(array('success' => false, 'message' => 'Enter exactly 3 digits as shown in the PIN prompt.'));
}

$pinValid = safezone_remote_verify_pin($uid, $pin, $key);
$usedRemotePin = $pinValid;
if (!$pinValid) {
	$pinValid = safezone_local_verify_pin($uid, $pin, $key);
}

if (!$pinValid) {
	ivc_json_exit(array('success' => false, 'message' => 'PIN does not match. Enter the 3 requested digits in the order shown.'));
}

$localUid = ivc_resolve_login_uid($pernum);
if ($localUid <= 0) {
	$localUid = $uid;
}

$sessionUid = $localUid > 0 ? $localUid : $uid;
$email = ivc_get_value('pi_account', "where uid=$sessionUid and deleted=0", 'email', '');

if (!$usedRemotePin) {
	$accountUid = (int) ivc_get_value('pi_account', "where uid=$sessionUid and deleted=0", 'uid', 0);
	if ($accountUid <= 0) {
		ivc_json_exit(array('success' => false, 'message' => 'Account not found on this site.'));
	}
}

$_SESSION['uid'] = $sessionUid;
$_SESSION['authenticated'] = true;
$_SESSION['auth_time'] = time();
if ($pernum !== '') {
	$_SESSION['pernum'] = $pernum;
}
if ($email !== '') {
	$_SESSION['email'] = $email;
}

if (!empty($GLOBALS['mysqli']) && !$GLOBALS['mysqli']->connect_errno) {
	$accountUid = (int) ivc_get_value('pi_account', "where uid=$sessionUid and deleted=0", 'uid', 0);
	if ($accountUid > 0) {
		@$GLOBALS['mysqli']->query("update pi_account set pin_tries=0 where uid=$sessionUid");
	}
}

ivc_json_exit(array(
	'success' => true,
	'redirect' => 'home.php',
));
