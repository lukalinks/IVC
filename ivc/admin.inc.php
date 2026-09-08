<?php
function ivc_legacy_admin_uids()
{
    return array(234601, 373764, 1286402);
}

function ivc_bootstrap_admin_uids()
{
    static $uids = null;
    if ($uids !== null) {
        return $uids;
    }

    $uids = ivc_legacy_admin_uids();

    foreach (array(1290032, 1290033) as $uid) {
        $uids[] = $uid;
    }

    if (defined('IVC_ADMIN_UIDS') && (string) IVC_ADMIN_UIDS !== '') {
        foreach (preg_split('/\s*,\s*/', (string) IVC_ADMIN_UIDS) as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }
            if (preg_match('/^\d{10,}$/', $part)) {
                $uids[] = max(0, (int) $part - 1000000000);
            } else {
                $uids[] = (int) $part;
            }
        }
    }

    $uids = array_values(array_unique(array_filter(array_map('intval', $uids), function ($uid) {
        return $uid > 0;
    })));

    return $uids;
}

function ivc_mysqli()
{
    return $GLOBALS['mysqli'];
}

function ivc_table_exists($table)
{
	$dbRes = $GLOBALS['mysqli']->query('SELECT DATABASE()');
	if (!$dbRes) {
		return false;
	}
	$dbRow = $dbRes->fetch_row();
	if (!$dbRow || !isset($dbRow[0]) || $dbRow[0] === '') {
		return false;
	}
	$db = $GLOBALS['mysqli']->real_escape_string($dbRow[0]);
	$table = $GLOBALS['mysqli']->real_escape_string($table);
	$res = $GLOBALS['mysqli']->query("SHOW TABLES FROM `$db` LIKE '$table'");
	return $res && $res->num_rows > 0;
}

function ivc_column_exists($table, $column)
{
    $table = $GLOBALS['mysqli']->real_escape_string($table);
    $column = $GLOBALS['mysqli']->real_escape_string($column);
    $res = $GLOBALS['mysqli']->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $res && $res->num_rows > 0;
}

function ivc_ensure_admin_schema()
{
    $db = $GLOBALS['mysqli'];
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $db->query("CREATE TABLE IF NOT EXISTS `ivc_admins` (
      `uid` int(10) unsigned NOT NULL,
      `added_at` datetime NOT NULL,
      `added_by` int(10) unsigned NOT NULL DEFAULT 0,
      `note` varchar(255) NOT NULL DEFAULT '',
      PRIMARY KEY (`uid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->query("CREATE TABLE IF NOT EXISTS `ivc_resorts` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `name` varchar(128) NOT NULL DEFAULT '',
      `code` varchar(64) NOT NULL DEFAULT '',
      `location` varchar(128) NOT NULL DEFAULT '',
      `description` text,
      `image` varchar(255) NOT NULL DEFAULT '',
      `status` varchar(16) NOT NULL DEFAULT 'active',
      `sort_order` int(11) NOT NULL DEFAULT 0,
      `created_at` datetime NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->query("CREATE TABLE IF NOT EXISTS `ivc_admin_log` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `admin_uid` int(10) unsigned NOT NULL,
      `action` varchar(64) NOT NULL DEFAULT '',
      `entity` varchar(32) NOT NULL DEFAULT '',
      `entity_id` varchar(32) NOT NULL DEFAULT '',
      `details` text,
      `created_at` datetime NOT NULL,
      PRIMARY KEY (`id`),
      KEY `admin_uid` (`admin_uid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->query("CREATE TABLE IF NOT EXISTS `ivc_settings` (
      `k` varchar(64) NOT NULL,
      `v` text NOT NULL,
      PRIMARY KEY (`k`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->query("CREATE TABLE IF NOT EXISTS `pernum` (
      `pernum` varchar(32) NOT NULL,
      `uid` int(11) NOT NULL,
      PRIMARY KEY (`pernum`),
      KEY `uid` (`uid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

    $db->query("CREATE TABLE IF NOT EXISTS `banned_users` (
      `uid` int(11) NOT NULL,
      PRIMARY KEY (`uid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

    $db->query("CREATE TABLE IF NOT EXISTS `ivc_bookings` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `uid` int(10) unsigned NOT NULL,
      `resort` varchar(128) NOT NULL DEFAULT '',
      `no_of_guests` tinyint(3) unsigned NOT NULL DEFAULT 1,
      `arrival_date` date NOT NULL,
      `no_of_nights` tinyint(3) unsigned NOT NULL DEFAULT 1,
      `accomodation` varchar(64) NOT NULL DEFAULT '',
      `restaurant` varchar(64) NOT NULL DEFAULT '',
      `date` datetime NOT NULL,
      `email` varchar(255) NOT NULL DEFAULT '',
      `status` varchar(24) NOT NULL DEFAULT 'pending',
      `admin_notes` text DEFAULT NULL,
      `updated_at` datetime DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `uid` (`uid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->query("CREATE TABLE IF NOT EXISTS `ivc_listings` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `uid` int(10) unsigned NOT NULL DEFAULT 0,
      `business_type` varchar(32) NOT NULL DEFAULT 'other',
      `name` varchar(190) NOT NULL DEFAULT '',
      `description` text,
      `country` varchar(100) NOT NULL DEFAULT '',
      `city` varchar(100) NOT NULL DEFAULT '',
      `address` varchar(255) NOT NULL DEFAULT '',
      `phone` varchar(50) NOT NULL DEFAULT '',
      `email` varchar(190) NOT NULL DEFAULT '',
      `website` varchar(255) NOT NULL DEFAULT '',
      `status` varchar(16) NOT NULL DEFAULT 'pending',
      `created_at` datetime NOT NULL,
      PRIMARY KEY (`id`),
      KEY `status` (`status`),
      KEY `business_type` (`business_type`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    if (ivc_table_exists('ivc_bookings') && !ivc_column_exists('ivc_bookings', 'status')) {
        $db->query("ALTER TABLE `ivc_bookings` ADD COLUMN `status` varchar(24) NOT NULL DEFAULT 'pending'");
    }
    if (ivc_table_exists('ivc_bookings') && !ivc_column_exists('ivc_bookings', 'admin_notes')) {
        $db->query("ALTER TABLE `ivc_bookings` ADD COLUMN `admin_notes` text");
    }
    if (ivc_table_exists('ivc_bookings') && !ivc_column_exists('ivc_bookings', 'updated_at')) {
        $db->query("ALTER TABLE `ivc_bookings` ADD COLUMN `updated_at` datetime DEFAULT NULL");
    }

    foreach (ivc_bootstrap_admin_uids() as $uid) {
        $uid = (int) $uid;
        $db->query("INSERT IGNORE INTO `ivc_admins` (`uid`, `added_at`, `added_by`, `note`) VALUES ($uid, NOW(), 0, 'Platform admin')");
    }

    $db->query("INSERT IGNORE INTO `ivc_settings` (`k`, `v`) VALUES
        ('bookings_enabled', '1'),
        ('directory_submissions_enabled', '1'),
        ('site_notice', '')");

    $seedCheck = $db->query("SELECT r.id FROM ivc_resorts r LIMIT 1");
    if ($seedCheck && $seedCheck->num_rows === 0) {
        $db->query("INSERT INTO `ivc_resorts` (`name`, `code`, `location`, `description`, `image`, `status`, `sort_order`, `created_at`) VALUES
            ('LAGUNA PALACE', 'LAGUNA PALACE', 'Zanzibar, Tanzania', 'Laguna Palace Resort', 'laguna.png', 'active', 1, NOW()),
            ('GATOR''S HIDEAWAY', 'GATOR''S HIDEAWAY', 'Uganda', 'Gator''s Hideaway', 'gator.png', 'active', 2, NOW())");
    }

    $listingCheck = $db->query("SELECT id FROM ivc_listings LIMIT 1");
    if ($listingCheck && $listingCheck->num_rows === 0) {
        $db->query("INSERT INTO `ivc_listings` (`uid`, `business_type`, `name`, `description`, `country`, `city`, `address`, `phone`, `email`, `website`, `status`, `created_at`) VALUES
            (0, 'resort', 'Laguna Palace Resort', 'IVC partner resort in Zanzibar.', 'Tanzania', 'Zanzibar', '', '', '', '', 'approved', NOW()),
            (0, 'resort', 'Gator''s Hideaway', 'IVC partner resort in Uganda.', 'Uganda', '', '', '', '', '', 'approved', NOW())");
    }

    $hasProdAdmin = false;
    $in = implode(',', array_map('intval', ivc_legacy_admin_uids()));
    $res = $db->query("SELECT uid FROM pi_account WHERE uid IN ($in) LIMIT 1");
    if ($res && $res->num_rows > 0) {
        $hasProdAdmin = true;
    }
    if (!$hasProdAdmin) {
        $res = $db->query("SELECT uid FROM pi_account WHERE deleted=0 ORDER BY uid ASC LIMIT 1");
        if ($res && $row = $res->fetch_assoc()) {
            $uid = (int) $row['uid'];
            $db->query("INSERT IGNORE INTO `ivc_admins` (`uid`, `added_at`, `added_by`, `note`) VALUES ($uid, NOW(), 0, 'Local bootstrap admin')");
        }
    }
}

function ivc_is_admin($uid)
{
    $uid = (int) $uid;
    if ($uid <= 0) {
        return false;
    }
    if (in_array($uid, ivc_bootstrap_admin_uids(), true)) {
        return true;
    }
    if (in_array($uid, ivc_legacy_admin_uids(), true)) {
        return true;
    }
    if (!isset($GLOBALS['mysqli']) || !($GLOBALS['mysqli'] instanceof mysqli)) {
        return false;
    }
    ivc_ensure_admin_schema();
    $stmt = $GLOBALS['mysqli']->prepare("SELECT uid FROM ivc_admins WHERE uid=? LIMIT 1");
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $stmt->store_result();
    $ok = $stmt->num_rows > 0;
    $stmt->close();
    return $ok;
}

function ivc_setting($key, $default = '')
{
    ivc_ensure_admin_schema();
    $stmt = $GLOBALS['mysqli']->prepare("SELECT v FROM ivc_settings WHERE k=? LIMIT 1");
    if (!$stmt) {
        return $default;
    }
    $stmt->bind_param('s', $key);
    $stmt->execute();
    $value = null;
    $stmt->bind_result($value);
    if (!$stmt->fetch()) {
        $stmt->close();
        return $default;
    }
    $stmt->close();
    return $value !== null ? (string) $value : $default;
}

function ivc_set_setting($key, $value)
{
    ivc_ensure_admin_schema();
    $stmt = $GLOBALS['mysqli']->prepare("INSERT INTO ivc_settings (k, v) VALUES (?, ?) ON DUPLICATE KEY UPDATE v=VALUES(v)");
    $stmt->bind_param('ss', $key, $value);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function ivc_admin_log($adminUid, $action, $entity = '', $entityId = '', $details = '')
{
    ivc_ensure_admin_schema();
    $adminUid = (int) $adminUid;
    $stmt = $GLOBALS['mysqli']->prepare("INSERT INTO ivc_admin_log (admin_uid, action, entity, entity_id, details, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $entityId = (string) $entityId;
    $stmt->bind_param('issss', $adminUid, $action, $entity, $entityId, $details);
    $stmt->execute();
    $stmt->close();
}

function ivc_booking_statuses()
{
    return array(
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'cancelled' => 'Cancelled',
        'completed' => 'Completed',
    );
}

function ivc_active_resorts()
{
    ivc_ensure_admin_schema();
    $rows = array();
    $res = $GLOBALS['mysqli']->query("SELECT * FROM ivc_resorts WHERE status='active' ORDER BY sort_order ASC, name ASC");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }
    }
    return $rows;
}

function ivc_encrypt_pin($plain)
{
    return openssl_encrypt((string) $plain, 'aes128', '1234', false, '1234567812345678');
}

function ivc_account_number($uid)
{
    $uid = (int) $uid;
    $pernum = getSingleValue('pernum', "where uid=$uid", 'pernum');
    if ($pernum !== '' && $pernum !== null) {
        return $pernum;
    }
    return str_pad((string) ($uid + 1000000000), 10, '0', STR_PAD_LEFT);
}
