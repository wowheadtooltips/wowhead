CREATE TABLE IF NOT EXISTS `{PREFIX}achievement` (
	`itemid` mediumint(8) unsigned NOT NULL,
	`name` varchar(255) NOT NULL,
	`search_name` varchar(255) NOT NULL,
	`icon` varchar(255) NOT NULL,
	`lang` varchar(4) NOT NULL
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}armory` (
	`uniquekey` varchar(100) NOT NULL,
	`name` varchar(32) NOT NULL,
	`class` varchar(25) NOT NULL,
	`raceid` smallint(2) NOT NULL,
	`classid` smallint(2) NOT NULL,
	`genderid` smallint(2) NOT NULL,
	`realm` varchar(255) NOT NULL,
	`region` varchar(2) NOT NULL,
	`tooltip` text NOT NULL,
	`cache` int(11) unsigned NOT NULL,
	PRIMARY KEY (`uniquekey`)
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}class` (
	`id` smallint(2) unsigned NOT NULL,
	`name` varchar(50) NOT NULL,
	`search_name` varchar(50) NOT NULL,
	`icon` varchar(255) NOT NULL,
	`tooltip` text NOT NULL,
	`lang` varchar(4) NOT NULL
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}config` (
	`name` varchar(255) NOT NULL,
	`setting` text NOT NULL,
	UNIQUE KEY (`name`)
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}craftable` (
	`itemid` int(10) unsigned NOT NULL,
	`name` varchar(255) NOT NULL default '',
	`search_name` varchar(255) NOT NULL default '',
	`quality` smallint(2) unsigned default NULL,
	`lang` varchar(255) default NULL,
	`icon` varchar(255) default NULL
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}craftable_reagent` (
	`itemid` int(8) unsigned NOT NULL default '0',
	`reagentof` int(11) unsigned NOT NULL,
	`name` varchar(255) NOT NULL default '',
	`quantity` smallint(2) unsigned NOT NULL,
	`quality` smallint(1) unsigned NOT NULL,
	`icon` varchar(255) NOT NULL
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}craftable_spell` (
	`reagentof` mediumint(8) unsigned NOT NULL,
	`spellid` mediumint(8) unsigned NOT NULL,
	`name` varchar(255) NOT NULL
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}currency` (
	`id` mediumint(8) NOT NULL,
	`name` varchar(255) NOT NULL,
	`search_name` varchar(255) NOT NULL,
	`icon` varchar(255) NOT NULL,
	`lang` varchar(4) NOT NULL,
	`currency` text NOT NULL,
	`tooltip` text NOT NULL
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}enchant` (
	 `id` mediumint(8) unsigned NOT NULL,
	 `name` varchar(255) NOT NULL,
	 `search_name` varchar(255) NOT NULL,
	 `lang` varchar(4) NOT NULL
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}enchant_reagent` (
	 `id` mediumint(8) unsigned NOT NULL,
	 `reagentof` mediumint(8) unsigned NOT NULL,
	 `name` varchar(255) NOT NULL,
	 `quantity` smallint(2) unsigned NOT NULL,
	 `quality` smallint(1) unsigned NOT NULL,
	 `icon` varchar(255) NOT NULL,
	 `lang` varchar(4) NOT NULL
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}event` (
	`id` mediumint(8) unsigned NOT NULL,
	`name` varchar(255) NOT NULL,
	`search_name` varchar(255) NOT NULL,
	`tooltip` text NOT NULL,
	`lang` varchar(4) NOT NULL,
	`cache` int(11) unsigned NOT NULL,
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}faction` (
	`id` mediumint(8) unsigned NOT NULL,
	`name` varchar(255) NOT NULL,
	`search_name` varchar(255) NOT NULL,
	`tooltip` text NOT NULL,
	`lang` varchar(4) NOT NULL
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}gearlist` (
	`uniquekey` varchar(32) NOT NULL,
	`cache` int(10) unsigned NOT NULL,
	`list` text NOT NULL,
	PRIMARY KEY (`uniquekey`)
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}guild` (
	`uniquekey` varchar(32) NOT NULL,
	`name` varchar(75) NOT NULL,
	`realm` varchar(20) NOT NULL,
	`region` varchar(2) NOT NULL,
	`tooltip` text NOT NULL,
	`cache` int(11) unsigned NOT NULL,
	PRIMARY KEY  (`uniquekey`)
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}item` (
	`itemid` MEDIUMINT( 8 ) NOT NULL ,
	`name` varchar( 255 ) NOT NULL ,
	`search_name` varchar( 255 ) NOT NULL ,
	`heroic` SMALLINT( 1 ) NOT NULL ,
	`quality` SMALLINT( 1 ) NOT NULL ,
	`icon` varchar( 255 ) NOT NULL ,
	`lang` varchar( 4 ) NOT NULL
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}itemico` (
	`itemid` MEDIUMINT( 8 ) NOT NULL ,
	`name` varchar( 255 ) NOT NULL ,
	`search_name` varchar( 255 ) NOT NULL ,
	`heroic` SMALLINT( 1 ) NOT NULL ,
	`icon` varchar( 255 ) NOT NULL ,
	`icon_size` varchar( 10 ) NOT NULL ,
	`lang` varchar( 4 ) NOT NULL
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}itemset` (
	`setid` mediumint(8) NOT NULL,
	`name` varchar(255) NOT NULL,
	`heroic` smallint(1) NOT NULL,
	`search_name` varchar(255) NOT NULL,
	`lang` varchar(4) NOT NULL
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}itemset_reagent` (
	`setid` mediumint(8) NOT NULL,
	`itemid` mediumint(8) unsigned NOT NULL,
	`name` varchar(255) NOT NULL,
	`quality` smallint(1) NOT NULL,
	`icon` varchar(255) NOT NULL
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}log` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `stamp` int(11) unsigned NOT NULL,
  `entry` varchar(255) NOT NULL,
  `module` varchar(15) NOT NULL,
  `page` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}npc` (
	`npcid` int(8) unsigned NOT NULL,
	`name` varchar(255) NOT NULL,
	`search_name` varchar(255) NOT NULL,
	`lang` varchar(4) NOT NULL
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}object` (
	`itemid` MEDIUMINT( 8 ) NOT NULL ,
	`name` varchar( 255 ) NOT NULL ,
	`search_name` varchar( 255 ) NOT NULL ,
	`lang` varchar( 4 ) NOT NULL
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}pet` (
	`id` mediumint(8) unsigned NOT NULL,
	`name` varchar(255) NOT NULL,
	`search_name` varchar(255) NOT NULL,
	`tipicon` varchar(255) NOT NULL,
	`lang` varchar(4) NOT NULL,
	`tooltip` text NOT NULL
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}profession` (
	`id` mediumint(8) unsigned NOT NULL,
	`name` varchar(255) NOT NULL,
	`search_name` varchar(255) NOT NULL,
	`tooltip` text NOT NULL,
	`lang` varchar(4) NOT NULL
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}quest` (
	`itemid` MEDIUMINT( 8 ) NOT NULL ,
	`name` varchar( 255 ) NOT NULL ,
	`search_name` varchar( 255 ) NOT NULL ,
	`lang` varchar( 4 ) NOT NULL
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}race` (
	`id` mediumint(8) unsigned NOT NULL,
	`name` varchar(255) NOT NULL,
	`search_name` varchar(255) NOT NULL,
	`icon` varchar(255) NOT NULL,
	`tipicon` varchar(255) NOT NULL,
	`tooltip` text NOT NULL,
	`lang` varchar(4) NOT NULL
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}recruit` (
	`uniquekey` varchar(32) NOT NULL,
	`cache` int(10) unsigned NOT NULL,
	`gearlist` text NOT NULL,
	`raid` text NOT NULL,
	`faction` text NOT NULL,
	`talents` text NOT NULL,
	`rss` text NOT NULL,
	PRIMARY KEY (`uniquekey`)
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}rss` (
	`uniquekey` varchar(32) NOT NULL,
	`cache` int(10) unsigned NOT NULL,
	`rss` text NOT NULL,
	PRIMARY KEY (`uniquekey`)
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}spell` (
	`itemid` MEDIUMINT(8) UNSIGNED NOT NULL ,
	`name` varchar(255) NOT NULL ,
	`search_name` varchar(255) NOT NULL ,
	`icon` varchar(255) NOT NULL,
	`rank` SMALLINT(2) NULL ,
	`lang` varchar(4) NOT NULL
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}stats` (
	`id` mediumint(8) unsigned NOT NULL,
	`name` varchar(255) NOT NULL,
	`search_name` varchar(255) NOT NULL,
	`lang` varchar(4) NOT NULL
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}title` (
	`pattern` varchar( 255 ) NOT NULL ,
	`achievement` mediumint( 8 ) unsigned NOT NULL,
	`search_name` varchar(255) NOT NULL,
	`faction` varchar(10) NOT NULL,
	`expansion` varchar(255) NOT NULL,
	`lang` varchar(4) NOT NULL
) DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{PREFIX}zones` (
	`id` mediumint(8) unsigned NOT NULL,
	`name` varchar(255) NOT NULL,
	`search_name` varchar(255) NOT NULL,
	`map` varchar(255) NOT NULL,
	`lang` varchar(4) NOT NULL
) DEFAULT CHARSET=utf8;