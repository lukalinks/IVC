<?php
include('config.php');
include('functions.php');
include('safezone.config.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	echo json_encode(array('success' => false, 'message' => 'Method not allowed'));
	exit;
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
	echo json_encode(array('success' => false, 'message' => 'Missing login data. Please start again.'));
	exit;
}

if (strlen($key) !== 3) {
	echo json_encode(array('success' => false, 'message' => 'Invalid PIN session. Please login again.'));
	exit;
}

if (strlen($pin) === 6) {
	$extracted = '';
	foreach (str_split($key) as $digit) {
		$pos = (int) $digit;
		if ($pos < 1 || $pos > 6 || !isset($pin[$pos - 1])) {
			echo json_encode(array('success' => false, 'message' => 'Enter the 3 requested PIN digits, not the full PIN in one block.'));
			exit;
		}
		$extracted .= $pin[$pos - 1];
	}
	$pin = $extracted;
}

if (strlen($pin) !== 3) {
	echo json_encode(array('success' => false, 'message' => 'Enter exactly 3 digits as shown in the PIN prompt.'));
	exit;
}

$postData = array(
	'uid' => $uid,
	'pin' => $pin,
	'key' => $key,
);

list($response, $curlError) = safezone_curl_post('https://safe.zone/signup/check_pin_api.php', $postData);

if ($curlError) {
	echo json_encode(array('success' => false, 'message' => 'Connection error. Please try again.'));
	exit;
}

$trimmed = trim((string) $response);
$lower = strtolower($trimmed);
$isValid = ($lower === 'valid' || $lower === 'success' || $lower === 'ok' || $trimmed === '1' || $trimmed === 'true');

if (!$isValid && stripos($trimmed, 'valid') !== false && stripos($trimmed, 'does not match') === false) {
	$isValid = true;
}

if (!$isValid) {
	echo json_encode(array('success' => false, 'message' => 'PIN does not match. Enter the 3 requested digits in the order shown.'));
	exit;
}

$localUid = ivc_resolve_login_uid($pernum);
if ($localUid <= 0) {
	$localUid = $uid;
}

$accountUid = (int) getSingleValue('pi_account', "where uid=$localUid and deleted=0", 'uid');
if ($accountUid <= 0) {
	echo json_encode(array('success' => false, 'message' => 'Account not found on this site.'));
	exit;
}

$email = getSingleValue('pi_account', "where uid=$localUid and deleted=0", 'email');

$_SESSION['uid'] = $localUid;
$_SESSION['authenticated'] = true;
$_SESSION['auth_time'] = time();
if ($pernum !== '') {
	$_SESSION['pernum'] = $pernum;
}
if ($email !== '') {
	$_SESSION['email'] = $email;
}

$GLOBALS['mysqli']->query("update pi_account set pin_tries=0 where uid=$localUid");

echo json_encode(array(
	'success' => true,
	'redirect' => 'home.php',
));
