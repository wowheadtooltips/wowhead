<?php
/**
*
* @package Wowhead Tooltips
* @version event.php 4.3
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../includes/wowhead_cache.php');

// create the cache
$cache = new wowhead_cache();

// get the ID and language from $_GET
$id		=	(array_key_exists('id', $_GET))		?	$_GET['id']		:	null;
$lang	=	(array_key_exists('lang', $_GET))	?	$_GET['lang']	:	'en';

if ($id == null)
{
	$cache->close();
	echo 'Event ID not given.';
	exit;
}

if (!$result = $cache->getEventTooltip($id, $lang))
{
	$cache->close();
	echo 'Event Tooltip not found in the cache.';
	exit;
}
else
{
	echo stripslashes($result['tooltip']);
}
$cache->close(); unset($cache);
?>
