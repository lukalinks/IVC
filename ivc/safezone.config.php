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

function safezone_curl_post($url, $postData)
{
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($postData) ? http_build_query($postData) : $postData);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	$response = curl_exec($ch);
	$curlError = curl_error($ch);
	curl_close($ch);

	return array($response, $curlError);
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
