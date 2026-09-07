-- Local IVC tables in the existing bank_dingo database.
-- Does not rename the database or change the MySQL user/password.

CREATE TABLE IF NOT EXISTS `ivc_membership` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `uid1` int(10) unsigned GENERATED ALWAYS AS (`uid`) STORED,
  `category` varchar(32) NOT NULL DEFAULT '',
  `membership` varchar(32) NOT NULL DEFAULT '',
  `currency` varchar(16) NOT NULL DEFAULT '',
  `amount` decimal(20,6) NOT NULL DEFAULT 0.000000,
  `date_added` datetime DEFAULT NULL,
  `date_payment` datetime DEFAULT NULL,
  `hash` varchar(128) NOT NULL DEFAULT '',
  `paid_status` varchar(16) NOT NULL DEFAULT '',
  `auto_renew` tinyint(1) NOT NULL DEFAULT 0,
  `txnid` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `uid1` (`uid1`),
  KEY `paid_status` (`paid_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `ivc_vacations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title1` varchar(255) NOT NULL DEFAULT '',
  `title2` varchar(255) NOT NULL DEFAULT '',
  `title3` varchar(255) NOT NULL DEFAULT '',
  `title4` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `trip_date` varchar(64) NOT NULL DEFAULT '',
  `total_seats` int(11) NOT NULL DEFAULT 0,
  `usd_value` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tvc_price` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `member_type` varchar(16) NOT NULL DEFAULT '',
  `per_account` int(11) NOT NULL DEFAULT 0,
  `target_value` decimal(12,2) NOT NULL DEFAULT 0.00,
  `target_date` date DEFAULT NULL,
  `single` int(11) NOT NULL DEFAULT 0,
  `couple` int(11) NOT NULL DEFAULT 0,
  `family` int(11) NOT NULL DEFAULT 0,
  `grou` int(11) NOT NULL DEFAULT 0,
  `closed` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `ivc_reservations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `vid` int(10) unsigned NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `hash` varchar(128) NOT NULL DEFAULT '',
  `tvc` decimal(20,6) NOT NULL DEFAULT 0.000000,
  `date` datetime DEFAULT NULL,
  `target` decimal(12,2) NOT NULL DEFAULT 0.00,
  `target_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `vid` (`vid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `ivc_route_schedule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vid` int(10) unsigned NOT NULL,
  `day` varchar(64) NOT NULL DEFAULT '',
  `city` varchar(128) NOT NULL DEFAULT '',
  `activity` text,
  PRIMARY KEY (`id`),
  KEY `vid` (`vid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `paypal` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `val` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `rb_invitation_codes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(64) NOT NULL DEFAULT '',
  `used_by` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- PHP reads geo_countries.name; local table already has country.
SET @name_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = 'bank_dingo' AND TABLE_NAME = 'geo_countries' AND COLUMN_NAME = 'name'
);
SET @sql := IF(@name_exists = 0,
  'ALTER TABLE `geo_countries` ADD COLUMN `name` VARCHAR(255) GENERATED ALWAYS AS (`country`) VIRTUAL',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Account numbers used at login: 1 + zero-padded uid.
INSERT IGNORE INTO `pernum` (`pernum`, `uid`)
SELECT LPAD(CAST(uid AS UNSIGNED) + 1000000000, 10, '0'), uid
FROM `pi_account`;
