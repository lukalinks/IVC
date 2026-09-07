<?php
/**
 * Copy this file to db-config2.php and set your database credentials.
 * db-config2.php is gitignored and stays on each server.
 */
if (!defined('DB_SERVER')) {
	define('DB_SERVER', '127.0.0.1');
}
if (!defined('DB_USER')) {
	define('DB_USER', 'root');
}
if (!defined('DB_PASS')) {
	define('DB_PASS', '');
}
if (!defined('DB_NAME')) {
	define('DB_NAME', 'bank_dingo');
}

if (!defined('SERVER_IVC')) {
	define('SERVER_IVC', DB_SERVER);
}
if (!defined('USER_IVC')) {
	define('USER_IVC', DB_USER);
}
if (!defined('PASS_IVC')) {
	define('PASS_IVC', DB_PASS);
}
if (!defined('NAME_IVC')) {
	define('NAME_IVC', DB_NAME);
}

if (!defined('YEMCHAIN_API_URL')) {
	define('YEMCHAIN_API_URL', 'https://yemchain.com');
}
