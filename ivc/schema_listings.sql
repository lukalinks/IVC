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
