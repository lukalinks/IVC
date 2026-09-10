<?php
require __DIR__ . '/safezone.bootstrap.php';

try {
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		ivc_json_exit(array('success' => false, 'message' => 'Method not allowed'), 405);
	}

	$rawInput = file_get_contents('php://input');
	$input = is_string($rawInput) && $rawInput !== '' ? json_decode($rawInput, true) : null;
	if (!is_array($input)) {
		$input = $_POST;
	}
	if (!is_array($input)) {
		$input = array();
	}

	$pernum = preg_replace('/\D/', '', (string) ($input['pernum'] ?? ''));
	$password = trim((string) ($input['password'] ?? $input['pwd'] ?? ''));
	$apiKey = trim((string) ($input['key'] ?? SAFEZONE_LOGIN_API_KEY));

	if ($pernum === '' || $password === '') {
		ivc_json_exit(array('success' => false, 'message' => 'Please enter your account number and password.'));
	}

	$uid = 0;
	$remote = safezone_remote_login($pernum, $password, $apiKey);
	if (!empty($remote['uid'])) {
		$uid = (int) $remote['uid'];
	} else {
		$uid = safezone_local_login($pernum, $password);
		if ($uid <= 0) {
			$message = !empty($remote['error']) ? $remote['error'] : 'Invalid account number or password.';
			ivc_json_exit(array('success' => false, 'message' => $message));
		}
	}

	$localUid = ivc_resolve_login_uid($pernum);
	if ($localUid <= 0) {
		$localUid = $uid;
	}

	$banned = (int) ivc_get_value('banned_users', "where uid=$localUid", 'uid', 0);
	if ($banned > 0) {
		ivc_json_exit(array('success' => false, 'message' => 'Your account has been suspended. Please contact support.'));
	}

	$blocked = (int) ivc_get_value('pi_account', "where uid=$localUid", 'blocked', 0);
	if ($blocked === 1) {
		$blockedMsg = ivc_get_value('pi_account', "where uid=$localUid", 'blocked_msg', '');
		ivc_json_exit(array(
			'success' => false,
			'message' => $blockedMsg !== '' ? $blockedMsg : 'Your account is blocked, contact Support immediately at service@safezone.info.',
		));
	}

	ivc_json_exit(safezone_login_success_payload($uid, $pernum, $localUid));
} catch (Throwable $e) {
	ivc_json_exit(array(
		'success' => false,
		'message' => 'Login error: ' . $e->getMessage(),
	), 500);
}
