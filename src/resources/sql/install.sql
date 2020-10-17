DROP TABLE IF EXISTS `pre__lake_redis_connection`;
CREATE TABLE `pre__lake_redis_connection` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL COMMENT '链接标题',
  `name` varchar(32) NOT NULL COMMENT '链接名称',
  `description` varchar(255) DEFAULT NULL COMMENT '授权描述',
  `url` varchar(150) DEFAULT NULL COMMENT 'REDIS_URL',
  `host` varchar(50) NOT NULL COMMENT 'REDIS_HOST',
  `password` varchar(100)  DEFAULT NULL COMMENT 'REDIS_PASSWORD',
  `port` varchar(5) NOT NULL COMMENT 'REDIS_PORT',
  `database` varchar(50) NOT NULL COMMENT 'REDIS_CACHE_DB',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态，1-开启',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
