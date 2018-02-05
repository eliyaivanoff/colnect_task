DROP TABLE IF EXISTS `request` ;
CREATE TABLE `request` (
  `request_id` int(10) NOT NULL AUTO_INCREMENT,
  `domain_id` int(10) NOT NULL,
  `element_id` int(10) NOT NULL,
  `url_id` int(10) NOT NULL,
  `last_visit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `duration` int(5) NOT NULL NOT NULL DEFAULT 0,
  `count` int(5) NOT NULL DEFAULT 0,
  PRIMARY KEY (`request_id`),
  UNIQUE KEY dom_url_el (`request_id`, `domain_id`, `element_id`, `url_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `domain` ;
CREATE TABLE `domain` (
	`domain_id` int(10) NOT NULL AUTO_INCREMENT,
	`domain_name` varchar(100) NOT NULL DEFAULT '',
	PRIMARY KEY (`domain_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `element` ;
CREATE TABLE `element` (
	`element_id` int(10) NOT NULL AUTO_INCREMENT,
	`element_name` varchar(20) NOT NULL DEFAULT '',
	PRIMARY KEY (`element_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `url` ;
CREATE TABLE `url` (
	`url_id` int(10) NOT NULL AUTO_INCREMENT,
	`url_name` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`url_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;