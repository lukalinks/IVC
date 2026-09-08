-- IVC database schema (single file — everything the app needs).
-- Safe to run more than once (CREATE IF NOT EXISTS / INSERT IGNORE).
--
-- Import:
--   mysql -u USER -p DATABASE_NAME < ivc/schema.sql
--
-- Local XAMPP example:
--   C:\xampp\mysql\bin\mysql.exe -u root bank_dingo < ivc\schema.sql
--
-- Requires existing SafeZone tables: pi_account, pernum (geo_countries optional).

-- ---------------------------------------------------------------------------
-- Core IVC application tables
-- ---------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `ivc_bookings` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- ---------------------------------------------------------------------------
-- Partner directory
-- ---------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `ivc_listings` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `ivc_listings` (`uid`, `business_type`, `name`, `description`, `country`, `city`, `address`, `phone`, `email`, `website`, `status`, `created_at`)
SELECT 0, 'resort', 'Laguna Palace Resort', 'IVC partner resort in Zanzibar.', 'Tanzania', 'Zanzibar', '', '', '', '', 'approved', NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `ivc_listings` WHERE `name` = 'Laguna Palace Resort' LIMIT 1);

INSERT INTO `ivc_listings` (`uid`, `business_type`, `name`, `description`, `country`, `city`, `address`, `phone`, `email`, `website`, `status`, `created_at`)
SELECT 0, 'resort', 'Gator''s Hideaway', 'IVC partner resort in Uganda.', 'Uganda', '', '', '', '', '', 'approved', NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `ivc_listings` WHERE `name` = 'Gator''s Hideaway' LIMIT 1);

-- ---------------------------------------------------------------------------
-- Admin console
-- ---------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `ivc_admins` (
  `uid` int(10) unsigned NOT NULL,
  `added_at` datetime NOT NULL,
  `added_by` int(10) unsigned NOT NULL DEFAULT 0,
  `note` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `ivc_admins` (`uid`, `added_at`, `added_by`, `note`) VALUES
(234601, NOW(), 0, 'Legacy platform admin'),
(373764, NOW(), 0, 'Legacy platform admin'),
(1286402, NOW(), 0, 'Legacy platform admin');

CREATE TABLE IF NOT EXISTS `ivc_resorts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL DEFAULT '',
  `code` varchar(64) NOT NULL DEFAULT '',
  `location` varchar(128) NOT NULL DEFAULT '',
  `description` text,
  `image` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(16) NOT NULL DEFAULT 'active',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `ivc_resorts` (`name`, `code`, `location`, `description`, `image`, `status`, `sort_order`, `created_at`)
SELECT 'LAGUNA PALACE', 'LAGUNA PALACE', 'Zanzibar, Tanzania', 'Laguna Palace Resort', 'laguna.png', 'active', 1, NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `ivc_resorts` WHERE `code` = 'LAGUNA PALACE' LIMIT 1);

INSERT INTO `ivc_resorts` (`name`, `code`, `location`, `description`, `image`, `status`, `sort_order`, `created_at`)
SELECT 'GATOR''S HIDEAWAY', 'GATOR''S HIDEAWAY', 'Uganda', 'Gator''s Hideaway', 'gator.png', 'active', 2, NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `ivc_resorts` WHERE `code` = 'GATOR''S HIDEAWAY' LIMIT 1);

CREATE TABLE IF NOT EXISTS `ivc_admin_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_uid` int(10) unsigned NOT NULL,
  `action` varchar(64) NOT NULL DEFAULT '',
  `entity` varchar(32) NOT NULL DEFAULT '',
  `entity_id` varchar(32) NOT NULL DEFAULT '',
  `details` text,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_uid` (`admin_uid`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `ivc_settings` (
  `k` varchar(64) NOT NULL,
  `v` text NOT NULL,
  PRIMARY KEY (`k`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `ivc_settings` (`k`, `v`) VALUES
('bookings_enabled', '1'),
('directory_submissions_enabled', '1'),
('site_notice', '');

-- ---------------------------------------------------------------------------
-- SafeZone compatibility helpers (optional; skip if tables missing)
-- ---------------------------------------------------------------------------

SET @db_name := DATABASE();
SET @name_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'geo_countries' AND COLUMN_NAME = 'name'
);
SET @geo_exists := (
  SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'geo_countries'
);
SET @sql := IF(@geo_exists > 0 AND @name_exists = 0,
  'ALTER TABLE `geo_countries` ADD COLUMN `name` VARCHAR(255) GENERATED ALWAYS AS (`country`) VIRTUAL',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT IGNORE INTO `pernum` (`pernum`, `uid`)
SELECT LPAD(CAST(uid AS UNSIGNED) + 1000000000, 10, '0'), uid
FROM `pi_account`;

SET @bookings_exists := (
  SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'ivc_bookings'
);
SET @status_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'ivc_bookings' AND COLUMN_NAME = 'status'
);
SET @sql := IF(@bookings_exists > 0 AND @status_exists = 0,
  'ALTER TABLE `ivc_bookings` ADD COLUMN `status` varchar(24) NOT NULL DEFAULT ''pending''',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @notes_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'ivc_bookings' AND COLUMN_NAME = 'admin_notes'
);
SET @sql := IF(@bookings_exists > 0 AND @notes_exists = 0,
  'ALTER TABLE `ivc_bookings` ADD COLUMN `admin_notes` text DEFAULT NULL',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @updated_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'ivc_bookings' AND COLUMN_NAME = 'updated_at'
);
SET @sql := IF(@bookings_exists > 0 AND @updated_exists = 0,
  'ALTER TABLE `ivc_bookings` ADD COLUMN `updated_at` datetime DEFAULT NULL',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
