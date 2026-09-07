<?php 
header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' ); 
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' ); 
header( 'Cache-Control: no-store, no-cache, must-revalidate' ); 
header( 'Cache-Control: post-check=0, pre-check=0', false ); 
header( 'Pragma: no-cache' ); 

$host = strtolower(preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST'] ?? ''));
$cookieDomain = '';
if ($host && $host !== 'localhost' && !filter_var($host, FILTER_VALIDATE_IP)) {
	if (preg_match('/(^|\.)internationalvacation\.club$/', $host)) {
		$cookieDomain = '.internationalvacation.club';
	} elseif (preg_match('/(^|\.)ivc\.travel$/', $host)) {
		$cookieDomain = '.ivc.travel';
	}
}
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
if (session_status() !== PHP_SESSION_ACTIVE) {
	if (PHP_VERSION_ID >= 70300) {
		session_set_cookie_params(array(
			'lifetime' => 0,
			'path' => '/',
			'domain' => $cookieDomain,
			'secure' => $isHttps,
			'httponly' => true,
			'samesite' => 'Lax',
		));
	} else {
		session_set_cookie_params(0, '/', $cookieDomain);
	}
	session_start();
}
date_default_timezone_set('America/New_York');

// Database Constants
include("/home/db-config2.php");

$mysqli = new mysqli (SERVER_IVC, USER_IVC, PASS_IVC, NAME_IVC);
	

if ($mysqli->connect_errno)

{

	echo ("Failed to connect to MySQL: " . $mysqli->connect_error);

}



$GLOBALS ['mysqli'] = $mysqli;
if (!$mysqli->connect_errno) {
	include_once __DIR__ . '/admin.inc.php';
	if (function_exists('ivc_ensure_admin_schema')) {
		ivc_ensure_admin_schema();
	}
}

?>