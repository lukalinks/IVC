<?php
/**
 * Production example for internationalvacation.club (cPanel / Hostinger).
 *
 * Copy to ONE of these paths on the server (not both unless identical):
 *   /home/db-config2.php
 *   /home/internationalvacationclub/public_html/ivc/db-config2.php
 *
 * Get values from cPanel → MySQL Databases.
 * db-config2.php is gitignored and must be created on each server.
 */
if (!defined('DB_SERVER')) {
	define('DB_SERVER', 'localhost');
}
if (!defined('DB_USER')) {
	define('DB_USER', 'YOUR_CPANEL_MYSQL_USER');
}
if (!defined('DB_PASS')) {
	define('DB_PASS', 'YOUR_CPANEL_MYSQL_PASSWORD');
}
if (!defined('DB_NAME')) {
	define('DB_NAME', 'YOUR_CPANEL_MYSQL_DATABASE');
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

if (!defined('IVC_ADMIN_UIDS')) {
	define('IVC_ADMIN_UIDS', '1290033,1001290033');
}
