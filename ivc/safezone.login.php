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

$pernum = preg_replace('/\D/', '', (string) ($input['pernum'] ?? ''));
$password = trim((string) ($input['password'] ?? $input['pwd'] ?? ''));
$apiKey = trim((string) ($input['key'] ?? SAFEZONE_LOGIN_API_KEY));

if ($pernum === '' || $password === '') {
	echo json_encode(array('success' => false, 'message' => 'Please enter your account number and password.'));
	exit;
}

$postData = array(
	'pernum' => $pernum,
	'password' => $password,
	'key' => $apiKey,
);

list($response, $curlError) = safezone_curl_post('https://safe.zone/signup/login_api.php', $postData);

if ($curlError) {
	echo json_encode(array('success' => false, 'message' => 'Connection error. Please try again.'));
	exit;
}

if (trim($response) === 'invalid key') {
	echo json_encode(array('success' => false, 'message' => 'Login service configuration error.'));
	exit;
}

$result = safezone_clean_json_response($response);
$uid = 0;

if (is_array($result) && !empty($result['uid'])) {
	$uid = (int) $result['uid'];
} elseif (preg_match('/uid[=:]?\s*["\']?(\d+)["\']?/i', (string) $response, $matches)) {
	$uid = (int) $matches[1];
}

if ($uid <= 0) {
	$message = 'Invalid account number or password.';
	if (is_array($result) && !empty($result['message'])) {
		$message = (string) $result['message'];
	} elseif (stripos((string) $response, 'invalid') !== false) {
		$message = 'Invalid account number or password.';
	}
	echo json_encode(array('success' => false, 'message' => $message));
	exit;
}

$localUid = ivc_resolve_login_uid($pernum);
if ($localUid <= 0) {
	$localUid = $uid;
}

$banned = (int) getSingleValue('banned_users', "where uid=$localUid", 'uid');
if ($banned > 0) {
	echo json_encode(array('success' => false, 'message' => 'Your account has been suspended. Please contact support.'));
	exit;
}

$blocked = getSingleValue('pi_account', "where uid=$localUid", 'blocked');
if ((int) $blocked === 1) {
	$blockedMsg = getSingleValue('pi_account', "where uid=$localUid", 'blocked_msg');
	echo json_encode(array(
		'success' => false,
		'message' => $blockedMsg !== '' ? $blockedMsg : 'Your account is blocked, contact Support immediately at service@safezone.info.',
	));
	exit;
}

$pinKey = safezone_pin_key();
$positions = safezone_pin_positions_label($pinKey);

echo json_encode(array(
	'success' => true,
	'uid' => $uid,
	'local_uid' => $localUid,
	'pernum' => $pernum,
	'pin_key' => $pinKey,
	'positions' => $positions,
	'prompt' => 'Enter digits #' . implode(' #', $positions) . ' of your Master PIN (3 digits, in that order).',
));
