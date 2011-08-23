CREATE TABLE IF NOT EXISTS `{PREFIX}class` (
	`id` smallint(2) unsigned NOT NULL,
	`name` varchar(50) NOT NULL,
	`search_name` varchar(50) NOT NULL,
	`icon` varchar(255) NOT NULL,
	`tooltip` text NOT NULL,
	`lang` varchar(4) NOT NULL
) DEFAULT CHARSET=utf8;
INSERT INTO `{PREFIX}config` (name, setting) VALUES ('class', 'true');
