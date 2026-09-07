<?php
define('IVC_JSON_API', true);

ob_start();
require __DIR__ . '/config.php';
require __DIR__ . '/functions.php';
require __DIR__ . '/safezone.config.php';
ob_end_clean();

header('Content-Type: application/json; charset=utf-8');

function ivc_json_exit($payload, $status = 200)
{
	http_response_code($status);
	echo json_encode($payload);
	exit;
}
