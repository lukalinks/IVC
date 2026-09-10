<?php
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

function ivc_config_fail($message, $status = 503)
{
	if (defined('IVC_JSON_API') && IVC_JSON_API) {
		if (!headers_sent()) {
			header('Content-Type: application/json; charset=utf-8');
		}
		http_response_code($status);
		echo json_encode(array('success' => false, 'message' => $message));
		exit;
	}

	if (!headers_sent()) {
		http_response_code($status);
		header('Content-Type: text/plain; charset=utf-8');
	}
	echo $message;
	exit;
}

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
		$params = array(
			'lifetime' => 0,
			'path' => '/',
			'secure' => $isHttps,
			'httponly' => true,
			'samesite' => 'Lax',
		);
		if ($cookieDomain !== '') {
			$params['domain'] = $cookieDomain;
		}
		session_set_cookie_params($params);
	} else {
		session_set_cookie_params(0, '/', $cookieDomain);
	}
	session_start();
}
date_default_timezone_set('America/New_York');

$dbConfigPaths = array();
if (DIRECTORY_SEPARATOR === '/') {
	$dbConfigPaths[] = '/home/db-config2.php';
}
$dbConfigPaths[] = __DIR__ . '/db-config2.php';

$dbConfigLoaded = false;
foreach ($dbConfigPaths as $dbConfigPath) {
	if (is_readable($dbConfigPath)) {
		require $dbConfigPath;
		$dbConfigLoaded = true;
		break;
	}
}

if (!$dbConfigLoaded) {
	ivc_config_fail('Database configuration not found. Copy ivc/db-config.example.php to ivc/db-config2.php or create /home/db-config2.php on the server.');
}

foreach (array('SERVER_IVC', 'USER_IVC', 'PASS_IVC', 'NAME_IVC') as $dbConstant) {
	if (!defined($dbConstant)) {
		ivc_config_fail('Database configuration is incomplete. Missing ' . $dbConstant . '.');
	}
}

mysqli_report(MYSQLI_REPORT_OFF);
$mysqli = mysqli_init();
if (!$mysqli) {
	ivc_config_fail('Could not initialize the database driver.');
}

$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 8);
$connected = @$mysqli->real_connect(SERVER_IVC, USER_IVC, PASS_IVC, NAME_IVC);
if (!$connected) {
	$detail = $mysqli->connect_error ? trim((string) $mysqli->connect_error) : 'unknown error';
	ivc_config_fail(
		'Database connection failed (' . $detail . '). '
		. 'Update /home/db-config2.php or ivc/db-config2.php with your cPanel MySQL host, database name, username, and password.'
	);
}

$GLOBALS['mysqli'] = $mysqli;

if (!(defined('IVC_JSON_API') && IVC_JSON_API)) {
	include_once __DIR__ . '/admin.inc.php';
	if (function_exists('ivc_ensure_admin_schema')) {
		try {
			ivc_ensure_admin_schema();
		} catch (Throwable $e) {
			error_log('IVC schema bootstrap failed: ' . $e->getMessage());
		}
	}
}

?>
