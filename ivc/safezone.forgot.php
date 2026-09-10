<?php
require __DIR__ . '/safezone.bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	ivc_json_exit(array('success' => false, 'message' => 'Method not allowed'), 405);
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
	$input = $_POST;
}

$action = isset($input['action']) ? trim((string) $input['action']) : '';

if ($action === 'password_init') {
	ivc_json_exit(safezone_login_success_payload(0, '', 0));
}

if ($action === 'pernum') {
	$email = trim((string) ($input['email'] ?? ''));
	$password = trim((string) ($input['password'] ?? ''));
	if ($email === '' || $password === '') {
		ivc_json_exit(array('success' => false, 'message' => 'Please enter your email and password.'));
	}
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		ivc_json_exit(array('success' => false, 'message' => 'Please enter a valid email address.'));
	}

	$result = safezone_forgot_pernum($email, $password);
	if (!$result['ok']) {
		ivc_json_exit(array('success' => false, 'message' => $result['error']));
	}
	if (!safezone_response_is_success($result['body'])) {
		ivc_json_exit(array('success' => false, 'message' => 'Invalid email/password.'));
	}

	ivc_json_exit(array(
		'success' => true,
		'message' => 'Your account number has been sent to your registered email address.',
	));
}

if ($action === 'password') {
	$pernum = preg_replace('/\D/', '', (string) ($input['pernum'] ?? ''));
	$pin = preg_replace('/\D/', '', (string) ($input['pin'] ?? ''));
	$match = preg_replace('/\D/', '', (string) ($input['match'] ?? ''));
	if ($pernum === '' || $pin === '' || $match === '') {
		ivc_json_exit(array('success' => false, 'message' => 'Please enter your account number and PIN digits.'));
	}
	if (strlen($match) !== 3) {
		ivc_json_exit(array('success' => false, 'message' => 'Invalid PIN challenge. Please try again.'));
	}

	$result = safezone_forgot_password($pernum, $pin, $match);
	if (!$result['ok']) {
		ivc_json_exit(array('success' => false, 'message' => $result['error']));
	}
	if (!safezone_response_is_success($result['body'])) {
		$payload = safezone_login_success_payload(0, $pernum, 0);
		ivc_json_exit(array(
			'success' => false,
			'message' => 'PIN does not match.',
			'pin_key' => $payload['pin_key'],
			'prompt' => $payload['prompt'],
		));
	}

	ivc_json_exit(array(
		'success' => true,
		'message' => 'Your password reset request has been submitted. Check your registered email for further instructions.',
	));
}

if ($action === 'mp') {
	$email = trim((string) ($input['email'] ?? ''));
	$password = trim((string) ($input['password'] ?? ''));
	if ($email === '' || $password === '') {
		ivc_json_exit(array('success' => false, 'message' => 'Please enter your email and password.'));
	}
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		ivc_json_exit(array('success' => false, 'message' => 'Please enter a valid email address.'));
	}

	$result = safezone_forgot_mp($email, $password);
	if (!$result['ok']) {
		ivc_json_exit(array('success' => false, 'message' => $result['error']));
	}
	if (!safezone_response_is_success($result['body'])) {
		ivc_json_exit(array('success' => false, 'message' => 'Invalid email address or password.'));
	}

	ivc_json_exit(array(
		'success' => true,
		'message' => 'Your request has been submitted. An email has been sent to our Support Team and they will contact you at your registered email for assistance. Please allow up to 48 hours for a response, emails are processed in the order they are received.',
	));
}

ivc_json_exit(array('success' => false, 'message' => 'Unknown action.'), 400);
