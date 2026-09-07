<?php
if (!defined('SAFEZONE_LOGIN_API_KEY')) {
	define('SAFEZONE_LOGIN_API_KEY', 'Dmjfk78Ckjksj23KlmdMMszcX');
}

function safezone_clean_json_response($response)
{
	$cleanedResponse = trim((string) $response);
	$firstBrace = strpos($cleanedResponse, '{');
	if ($firstBrace !== false) {
		$cleanedResponse = substr($cleanedResponse, $firstBrace);
		$lastBrace = strrpos($cleanedResponse, '}');
		if ($lastBrace !== false) {
			$cleanedResponse = substr($cleanedResponse, 0, $lastBrace + 1);
		}
	} elseif (preg_match('/^string\(\d+\)\s*"(.*)"\s*$/s', $cleanedResponse, $matches)) {
		$cleanedResponse = stripcslashes($matches[1]);
	}

	return json_decode($cleanedResponse, true);
}

function safezone_curl_available()
{
	return function_exists('curl_init');
}

function safezone_curl_post($url, $postData)
{
	if (!safezone_curl_available()) {
		return array(false, 'cURL is not enabled on this server.');
	}

	$ch = curl_init($url);
	if ($ch === false) {
		return array(false, 'Unable to start connection.');
	}

	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($postData) ? http_build_query($postData) : $postData);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/x-www-form-urlencoded',
		'Accept: application/json',
	));

	$response = curl_exec($ch);
	$curlError = curl_error($ch);
	curl_close($ch);

	return array($response, $curlError);
}

function safezone_remote_login($pernum, $password, $apiKey)
{
	$urls = array(
		'https://safe.zone/signup/login_api.php',
		'https://safe.zone/api/login_api.php',
	);
	$postData = array(
		'pernum' => $pernum,
		'password' => $password,
		'key' => $apiKey,
	);

	foreach ($urls as $url) {
		list($response, $curlError) = safezone_curl_post($url, $postData);
		if ($curlError) {
			continue;
		}
		if (trim((string) $response) === 'invalid key') {
			return array('error' => 'Login service configuration error.');
		}

		$result = safezone_clean_json_response($response);
		if (is_array($result) && !empty($result['uid'])) {
			return array('uid' => (int) $result['uid']);
		}
		if (preg_match('/uid[=:]?\s*["\']?(\d+)["\']?/i', (string) $response, $matches)) {
			return array('uid' => (int) $matches[1]);
		}
	}

	return array('error' => 'Unable to reach SafeZone login service.');
}

function safezone_remote_verify_pin($uid, $pin, $key)
{
	$urls = array(
		'https://safe.zone/signup/check_pin_api.php',
		'https://safe.zone/api/verify_pin_api.php',
	);

	foreach ($urls as $url) {
		$postData = array(
			'uid' => $uid,
			'pin' => $pin,
			'key' => $key,
		);
		if (strpos($url, '/api/verify_pin_api.php') !== false) {
			$postData['apikey'] = SAFEZONE_LOGIN_API_KEY;
		}

		list($response, $curlError) = safezone_curl_post($url, $postData);
		if ($curlError) {
			continue;
		}

		$trimmed = trim((string) $response);
		$lower = strtolower($trimmed);
		if ($lower === 'valid' || $lower === 'success' || $lower === 'ok' || $trimmed === '1' || $trimmed === 'true') {
			return true;
		}
		if (stripos($trimmed, 'valid') !== false && stripos($trimmed, 'does not match') === false) {
			return true;
		}
	}

	return false;
}

function safezone_pin_key()
{
	$validKeyFound = false;
	$attempts = 0;
	$finalKey = '135';

	while (!$validKeyFound && $attempts < 100) {
		$pool = array(1, 2, 3, 4, 5, 6);
		shuffle($pool);
		$candidates = array_slice($pool, 0, 3);
		sort($candidates);
		$hasConsecutive = ($candidates[1] == $candidates[0] + 1) || ($candidates[2] == $candidates[1] + 1);
		if (!$hasConsecutive) {
			$finalKey = implode('', $candidates);
			$validKeyFound = true;
		}
		$attempts++;
	}

	return $finalKey;
}

function safezone_pin_positions_label($key)
{
	$labels = array();
	foreach (str_split((string) $key) as $digit) {
		$labels[] = (int) $digit;
	}

	return $labels;
}

function safezone_key_to_skey($key)
{
	$skey = array();
	foreach (str_split((string) $key) as $digit) {
		$pos = (int) $digit - 1;
		if ($pos >= 0 && $pos <= 5) {
			$skey[$pos] = $pos;
		}
	}

	return $skey;
}

function safezone_local_login($pernum, $password)
{
	$uid = ivc_resolve_login_uid($pernum);
	if ($uid <= 0 || !ivc_password_matches($uid, $password)) {
		return 0;
	}

	return (int) $uid;
}

function safezone_local_verify_pin($uid, $pin, $key)
{
	$uid = (int) $uid;
	if ($uid <= 0) {
		return false;
	}

	$userpin = ivc_get_value('pi_account', "where uid=$uid", 'pin', '');
	$masterPin = ivc_decrypt_master_pin($userpin);
	if ($masterPin === '') {
		return false;
	}

	$skey = safezone_key_to_skey($key);
	if (count($skey) !== 3) {
		return false;
	}

	return ivc_pin_challenge_ok($masterPin, $pin, $skey);
}

function safezone_login_success_payload($uid, $pernum, $localUid = 0)
{
	$pinKey = safezone_pin_key();
	$positions = safezone_pin_positions_label($pinKey);

	return array(
		'success' => true,
		'uid' => (int) $uid,
		'local_uid' => $localUid > 0 ? (int) $localUid : (int) $uid,
		'pernum' => $pernum,
		'pin_key' => $pinKey,
		'positions' => $positions,
		'prompt' => 'Enter digits #' . implode(' #', $positions) . ' of your Master PIN (3 digits, in that order).',
	);
}
