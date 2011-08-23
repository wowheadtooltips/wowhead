<?php
/**
*
* @package Wowhead Tooltips
* @version gearlist.php 4.0
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/functions.php');
require_once(dirname(__FILE__) . '/../includes/wowhead_config.php');
require_once(dirname(__FILE__) . '/../includes/wowhead_cache.php');
require_once(dirname(__FILE__) . '/../includes/wowhead_language.php');

$config = new wowhead_config();
$config->loadConfig();

$slot_ids = array(
	'ammo',
	'head',
	'neck',
	'shoulder',
	'shirt',
	'chest',
	'belt',
	'legs',
	'feet',
	'wrist',
	'gloves',
	'ring1',
	'ring2',
	'trinket1',
	'trinket2',
	'back',
	'main_hand',
	'off_hand',
	'ranged',
	'tabard'
);

// connect to mysql and select the database
$conn = mysql_connect(WHP_DB_HOST, WHP_DB_USER, WHP_DB_PASS) or die(mysql_error());
mysql_select_db(WHP_DB_NAME) or die(mysql_error());

// get what we need from $_GET global
$name = stripslashes(html_entity_decode($_GET['name'], ENT_QUOTES));
$realm = stripslashes(html_entity_decode($_GET['realm'], ENT_QUOTES));
$region = stripslashes(html_entity_decode($_GET['region'], ENT_QUOTES));
if ($name == '')
{
	print 'No name provided.';
	mysql_close($conn);
	exit;
}

$key = generateKey($name, $realm, $region);
if (trim($key) == '')
{
	print 'Unique key not provided.';
	mysql_close($conn);
	exit;	
}

$query = mysql_query("SELECT list FROM " . WHP_DB_PREFIX. "gearlist WHERE uniquekey='$key' AND cache > UNIX_TIMESTAMP(NOW()) - " . $config->armory_char_cache . " LIMIT 1");

if (mysql_num_rows($query) == 0)
{
	// nothing in the cache, so we need to query
	$xml_data = getXML(characterURL($name, $region, $realm));
	
	if (!$xml = @simplexml_load_string($xml_data, 'SimpleXMLElement'))
	{
		print 'Failed to get XML.  You may be blocked by the armory.';	
	}
	else
	{
		//print_r($xml); exit;
		if (!$xml->characterInfo->characterTab)
		{
			print 'Character not found or some other problem.';
			mysql_close($conn);
			exit;	
		}
		
		$language = new wowhead_language();
		$language->loadLanguage($config->lang);
		
		// got the xml so now let's loop through the items and add them to the cache and build the display
		$out = $pcs = '';
		$wowhead_url = ($config->lang == 'en') ? 'http://www.wowhead.com/' : 'http://' . strtolower($config->lang) . '.wowhead.com/';
		
		// build set bonuses
		foreach ($xml->characterInfo->characterTab->items->item as $id)
		{
			$pcs .= (string)$id['id'] . ':';	
		}
		
		foreach ($xml->characterInfo->characterTab->items->item as $item)
		{
			$id = (int)$item['id'];
			$gem1 = (int)$item['gem0Id'];
			$gem2 = (int)$item['gem1Id'];
			$gem3 = (int)$item['gem2Id'];
			$enchant = (int)$item['permanentenchant'];
			$slot = $language->words[$slot_ids[(int)$item['slot'] + 1]];
			$icon = 'http://static.wowhead.com/images/wow/icons/tiny/' . (string)$item['icon'] . '.gif';
			
			// trim off the ending colon
			$pcs = (substr($pcs, strlen($pcs) - 1, 1) == ':') ? substr($pcs, 0, strlen($pcs) - 2) : $pcs; 
			
			// query wowhead to get the rest of the info we need
			$item_xml_data = getXML($wowhead_url . 'item=' . $id . '&xml');

			if (!$item_xml = @simplexml_load_string($item_xml_data, 'SimpleXMLElement', LIBXML_NOCDATA))
			{
				echo 'There was a problem.';
				mysql_close($conn);
				exit;	
			}
			$item_name = (string)$item_xml->item->name;
			$quality = (int)$item_xml->item->quality['id'];
			
			//$out .= "<div style=\"padding-left: 5px; display: block;\"><div class=\"iconsmall\" style=\"background: url({$icon}) no-repeat scroll 4px 4px; top: 5px;\"><div class=\"tile\"><a href=\"{$wowhead_url}item=$id\" rel=\"gems=$gem1:$gem2:$gem3&amp;ench=$enchant&amp;pcs=$pcs\" target=\"_blank\" /></a></div></div><span style=\"height: 20px; display: inline; padding-top: 3px;\"><a class=\"q{$quality}\" href=\"{$wowhead_url}item=$id\" rel=\"gems=$gem1:$gem2:$gem3&amp;ench=$enchant&amp;pcs=$pcs\" target=\"_blank\">[$item_name]</a></span> ($slot)</div>\n";
			$out .= "<a class=\"q{$quality} icontiny\" style=\"background-image: url({$icon});\" rel=\"gems=$gem1:$gem2:$gem3&amp;ench=$enchant&amp;pcs=$pcs\" href=\"{$wowhead_url}item=$id\" target=\"_blank\">[{$item_name}]</a><br />";
		}

			
		// insert/update mysql
		$dummy_text = "INSERT INTO `" . WHP_DB_PREFIX . "gearlist` (
							`uniquekey`, 
							`cache`,
							`list`
						) VALUES (
							'$key',
							UNIX_TIMESTAMP(NOW()),
							'" . addslashes($out) . "'
						)
						ON DUPLICATE KEY UPDATE
							list='" . addslashes($out) . "',
							cache=UNIX_TIMESTAMP(NOW())";
		$dummy = mysql_query($dummy_text);
		print $out;
	}
}
else
{
	// get results from mysql
	list($list) = mysql_fetch_array($query);
	print stripslashes($list);
}
$config->close();
mysql_close($conn);
?>
