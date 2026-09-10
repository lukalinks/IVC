<?php
define('IVC_JSON_API', true);

function ivc_json_exit($payload, $status = 200)
{
	while (ob_get_level() > 0) {
		ob_end_clean();
	}
	if (!headers_sent()) {
		header('Content-Type: application/json; charset=utf-8');
	}
	http_response_code($status);
	echo json_encode($payload);
	exit;
}

function ivc_json_shutdown_handler()
{
	$error = error_get_last();
	if (!$error || !in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR), true)) {
		return;
	}
	ivc_json_exit(array(
		'success' => false,
		'message' => 'Server error: ' . $error['message'],
	), 500);
}

set_exception_handler(function ($e) {
	ivc_json_exit(array(
		'success' => false,
		'message' => 'Server error: ' . $e->getMessage(),
	), 500);
});
register_shutdown_function('ivc_json_shutdown_handler');

ob_start();
try {
	require __DIR__ . '/config.php';
	require __DIR__ . '/functions.php';
	require __DIR__ . '/safezone.config.php';
} catch (Throwable $e) {
	ob_end_clean();
	ivc_json_exit(array(
		'success' => false,
		'message' => 'Server error: ' . $e->getMessage(),
	), 500);
}
ob_end_clean();

header('Content-Type: application/json; charset=utf-8');
