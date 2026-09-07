-- IVC admin console tables. Safe to run more than once.

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
