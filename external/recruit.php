<?php
/**
*
* @package Wowhead Tooltips
* @version recruit.php 4.0
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/functions.php');
require_once(dirname(__FILE__) . '/../includes/wowhead.php');
require_once(dirname(__FILE__) . '/../includes/wowhead_config.php');
require_once(dirname(__FILE__) . '/../includes/wowhead_cache.php');
require_once(dirname(__FILE__) . '/../includes/wowhead_patterns.php');
require_once(dirname(__FILE__) . '/../includes/wowhead_language.php');
require_once(dirname(__FILE__) . '/../includes/wowhead_log.php');
require_once(dirname(__FILE__) . '/../includes/wowhead_item.php');			// for glyphs
require_once(dirname(__FILE__) . '/../includes/wowhead_achievement.php');	// for the RSS feeds 

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

// cataclysm raid ids
$raid_cataclysm = array(
	// blackwing decent
	4842,	// kill all bosses on regular
	5310,	// aberrant behavoir
	5307,	// achieve-a-tron
	5309,	// full of sound and fury
	4849,	// keeping it in the family
	5306,	// parasite evening
	5308,	// silence is golden
	
	// blackwing decent (heroic)
	5109,	// heroic: atramedes
	5115,	// heroic: chimaeron
	5094,	// heroic: magmaw
	5108,	// heroic: maloriak
	5116,	// heroic: nefarian
	5107,	// heroic: omnotron defense system
	
	// the bastion of twilight
	4850,	// kill all bosses on regular
	5312,	// the abyss will gaze back into you
	5300,	// the only escape
	5313,	// i can't hear your over the sound of how awesome i am
	4852,	// double dragon
	5311,	// elementary
	
	// the bastion of twilight (heroic)
	5118,	// heroic: halfus wyrmbreaker
	5117,	// heroic: valiona and theralion
	5119,	// heroic: ascendant council
	5120,	// heroic: cho'gall
	5121,	// heroic: sinestra
	
	// throne of the four winds
	4851,	// kill all bosses on regular
	5304,	// stay chill
	5305,	// four play
	
	// throne of the four winds (heroic)
	5122,	// heroic: conclave of wind
	5123	// heroic: al'akir
);

// ten man raid ids
$raid_ten = array(
	
	// misc raids
	1876,	// os10
	622,	// malygos
	562,	// arachnid quarter
	564,	// construct quarter
	566,	// plague quarter
	568,	// military quarter
	576,	// fall of naxx
	4396,	// onyxia
	
	// ulduar
	2888,	// antechamber
	2892,	// decent into madness
	2890,	// keepers
	2886,	// siege
	2894,	// complete ulduar
	
	// call of the crusade
	3917,	// normal mode
	3918,	// heroic mode
	
	// icecrown citadel (normal)
	4531,	// storming the citadel
	4529,	// the crimson hall
	4527,	// the frostwing halls
	4530,	// the frozen throne
	4528,	// the plagueworks
	4532,	// fall of the lich king
	
	// icecrown citadel (heroic)
	4628,	// storming the citadel
	4630,	// the crimson hall
	4631,	// the frostwing halls
	4583,	// the frozen throne
	4629,	// the plagueworks
	4636,	// fall of the lich king
	
	// the ruby sanctum (halion)
	4817,	// twilight destroyer (10 man)
	4818,	// heroic: twilight destroyer (10 man)
);

// twenty-five man raid ids
$raid_twenty_five = array(
	
	// misc raids
	625,	// os25
	623,	// malygos
	563,	// arachnid quarter
	565,	// construct quarter
	567,	// plague quarter
	569,	// military quarter
	577,	// fall of naxx
	4397,	// onyxia
	
	// ulduar
	2889,	// antechamber
	2893,	// decent into madness
	2891,	// keepers
	2887,	// siege
	2895,	// complete ulduar
	
	// call of the crusade
	3916,	// normal mode
	3812,	// heroic mode
	
	// icecrown citadel (normal mode)
	4604,	// storming the citadel
	4606,	// the crimson hall
	4607,	// the frostwing halls
	4597,	// the frozen throne
	4605,	// the plagueworks
	4608,	// fall of the lich king
			
	// icecrown citadel (heroic mode)
	4632,	// storming the citadel
	4634,	// the crimson hall
	4635,	// the frostwing halls
	4584,	// the frozen throne
	4633,	// the plagueworks
	4637,	// fall of the lich king
	
	// ruby sanctum (halion)
	4815,	// twilight destroyer (25 man)
	4816,	// heroic: twilight destroyer (25 man)
);

// setup the config
$config = new wowhead_config();
$config->loadConfig();

// setup the correct wowhead URL and load the language pack
$wowhead_url = ($config->lang == 'en') ? 'http://www.wowhead.com/' : 'http://' . strtolower($config->lang) . '.wowhead.com/';
$language = new wowhead_language();
$language->loadLanguage($config->lang);

// get the shizzle from $_GET
$mode = urldecode($_GET['mode']);
$name = urldecode($_GET['name']);
$realm = (!array_key_exists('realm', $_GET)) ? $recruit_realm : urldecode($_GET['realm']);
$region = (!array_key_exists('region', $_GET)) ? $recriot_region : urldecode($_GET['region']);

// connect to mysql and select the database
$conn = mysql_connect(WHP_DB_HOST, WHP_DB_USER, WHP_DB_PASS) or die(mysql_error());
mysql_select_db(WHP_DB_NAME) or die(mysql_error());

if ($name == '')
{
	print 'No name provided.';
	mysql_close($conn);
	exit;
}

if ($mode == '')
{
	print 'No mode provided.';
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

if ($mode == 'gearlist')
{
	$query = mysql_query("SELECT gearlist FROM " . WHP_DB_PREFIX . "recruit WHERE uniquekey='$key' AND cache > UNIX_TIMESTAMP(NOW()) - {$config->recruit_cache} LIMIT 1");
	list($list) = @mysql_fetch_array($query);
	if (mysql_num_rows($query) == 0 || trim($list) == '')
	{
		// nothing in the cache, so we need to query
		$xml_data = getXML(characterURL($name, $region, $realm));
		if (!$xml = @simplexml_load_string($xml_data, 'SimpleXMLElement'))
		{
			print $language->words['invalid_xml'];
			mysql_close($conn);
			exit;
		}

		//print_r($xml); exit;
		if (!$xml->characterInfo->characterTab)
		{
			print $language->words['char_no_data'];
			mysql_close($conn);
			exit;	
		}
		
		// got the xml so now let's loop through the items and add them to the cache and build the display
		$out = $pcs = '';
		
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
		$dummy_text = "INSERT INTO `" . WHP_DB_PREFIX . "recruit` (
							`uniquekey`, 
							`cache`,
							`gearlist`
						) VALUES (
							'$key',
							UNIX_TIMESTAMP(NOW()),
							'" . addslashes($out) . "'
						)
						ON DUPLICATE KEY UPDATE
							gearlist='" . addslashes($out) . "',
							cache=UNIX_TIMESTAMP(NOW())";
		$dummy = mysql_query($dummy_text);
		print $out;
		
	}
	else
	{
		// get results from mysql
		print stripslashes($list);
	}
}
elseif ($mode == 'rss')
{
	// see if SQL already has it and its cache time hasn't elapsed
	$query = mysql_query("SELECT rss FROM `" . WHP_DB_PREFIX . "recruit` WHERE uniquekey='{$key}' AND cache > UNIX_TIMESTAMP(NOW()) - {$config->recruit_cache} LIMIT 1");
	list($rss_data) = mysql_fetch_array($query);
	if (mysql_num_rows($query) == 0 || trim($rss_data) == '')
	{
		$data = readURL(rssURL($name, $region, $realm));
		if (trim($data) == '' || !$rss = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA))
		{
			mysql_close($conn); unset($rss);
			print $language->words['invalid_xml'];
			exit;
		}
		
		// here's how its gonna go: (hopefully)
		//	1. Entries will be parsed and put into an array with the key being the timestamp of when the even was updated.
		//	2. Entries will then be sorted by their timestamps.
		//	3. An unordered list (<ul>) will be generated from the entries.
		
		// now that we have the RSS data, we need to make it readable
		$entries = array();
		foreach ($rss->entry as $entry)
		{
			$content = (string)$entry->title;
			// first get the timestamp (hopefully)
			$stamp = strtotime((string)$entry->updated);
			// now we need to determine what actually happened
			if (strpos($content, 'Completed step') !== false)	// completed step of achievement
			{
				// we'll use regular expressions to pull the information we need
				if (!preg_match('#Completed step \[(.+?)\] of achievement \[(.+?)\]#s', $content, $match))
					continue;	// if not found then continue the foreach
				else
				{
					$achievement = new wowhead_achievement($config);
					$entries[$stamp] = 'Completed step <span style="color: purple;">[' . (string)$match[1] . ']</span> of achievement ' . $achievement->parse((string)$match[2]);
					$achievement->close(); unset($achievement);
				}
			}
			elseif (strpos($content, 'Earned the achievement') !== false)	// completed an achievement
			{
				if (!preg_match('#\[(.+?)\]#s', $content, $match))
					continue;
				else
				{
					$name = (string)$match[1];
					$achievement = new wowhead_achievement($config);
					$entries[$stamp] = 'Earned achievement ' . $achievement->parse($name);
					$achievement->close(); unset($achievement);	
				}
			}
			elseif (strpos($content, 'Obtained') !== false)	// obtained an item
			{
				if (!preg_match('#Obtained \[(.+?)\]#s', $content, $match))
					continue;
				else
				{
					$item = new wowhead_item($config);
					$entries[$stamp] = 'Obtained ' . $item->parse((string)$match[1]);
					$item->close(); unset($item);
				}				
			}
			else	// something else happened, usually another kill of a boss
			{
				// for these we just use the content as is
				$entries[$stamp] = $content;
			}
		}
		
		// sort the array in descending order by the keys
		krsort($entries);
		
		// okay now we can format the actual HTML
		$output = '<ul>';
		foreach ($entries as $stamp => $value)
			$output .= '<li>(' . date($config->armory_date_format . ' ' . $config->armory_time_format, $stamp) . ') ' . $value . '</li>';	
		$output .= '</ul>';
		
		// insert/update mysql
		$dummy_text = "INSERT INTO `" . WHP_DB_PREFIX . "recruit` (
							`uniquekey`, 
							`cache`,
							`rss`
						) VALUES (
							'$key',
							UNIX_TIMESTAMP(NOW()),
							'" . addslashes($output) . "'
						)
						ON DUPLICATE KEY UPDATE
							rss='" . addslashes($output) . "',
							cache=UNIX_TIMESTAMP(NOW())";
		$dummy = mysql_query($dummy_text);
		
		print $output;
		unset($rss, $data);	// clean things up a bit
	}
	else
	{
		echo stripslashes($rss_data);
	}
}
elseif ($mode == 'faction')
{
	$query = mysql_query("SELECT faction FROM " . WHP_DB_PREFIX . "recruit WHERE uniquekey='$key' AND cache > UNIX_TIMESTAMP(NOW()) - {$config->recruit_cache} LIMIT 1");
	list($faction) = @mysql_fetch_array($query);
	if (mysql_num_rows($query) == 0 || trim($faction) == '')
	{
		$xml_data = getXML(reputationURL($name, $region, $realm));
		if (!$xml = @simplexml_load_string($xml_data, 'SimpleXMLElement'))
		{
			mysql_close($conn);
			print $language->words['invalid_xml'];
			exit;
		}
		$i = 0; $factions = array();
		foreach ($xml->characterInfo->reputationTab->faction[sizeof($xml->characterInfo->reputationTab->faction) - 1]->faction as $faction)
		{
			$factions['Cataclysm'][] = array(
				'name'	=>	(string)$faction['name'],
				'rep'	=>	(string)$faction['reputation']
			);
		}
		foreach ($xml->characterInfo->reputationTab->faction[sizeof($xml->characterInfo->reputationTab->faction) - 2]->faction as $faction)
		{
			$factions['Wrath of the Lich King'][] = array(
				'name'	=>	(string)$faction['name'],
				'rep'	=>	(string)$faction['reputation']
			);
		}
		ksort($factions);	// reverse sort it
		$out = '';
		foreach ($factions as $index => $value)
		{
			$out .= '<table class="rep-table" cellspacing="1" cellpadding="0">';
			$out .= '<tr><th colspan="3">' . $index . '</th></tr>';
			foreach ($value as $rep)
			{
				$standing = calculateStanding($rep['rep']);
				$percent = percentage((int)$standing['value'], (int)$standing['max']);;
				$out .= '<tr><td width="45%" style="text-align: center;">' . $rep['name'] . '</td>';
				$out .= '<td width="15%"><div class="rep-box"><span style="width: ' . $percent . '%;" class="rep-' . strtolower($standing['class']) . '">&nbsp;</span></div></td>';
				$out .= '<td width="40%" style="text-align: center;">' . $standing['value'] . '/' . $standing['max'] . ' ' . $standing['word'] . '</td></tr>';
			}
			$out .= '</table><br />';
		}

		// insert/update mysql
		$dummy_text = "INSERT INTO `" . WHP_DB_PREFIX . "recruit` (
							`uniquekey`, 
							`cache`,
							`faction`
						) VALUES (
							'$key',
							UNIX_TIMESTAMP(NOW()),
							'" . addslashes($out) . "'
						)
						ON DUPLICATE KEY UPDATE
							faction='" . addslashes($out) . "',
							cache=UNIX_TIMESTAMP(NOW())";
		$dummy = mysql_query($dummy_text);
		print $out;
	}
	else
	{
		print stripslashes($faction);	
	}
}
elseif ($mode == 'raid')
{
	$query = mysql_query("SELECT raid FROM " . WHP_DB_PREFIX . "recruit WHERE uniquekey='$key' AND cache > UNIX_TIMESTAMP(NOW()) - {$config->recruit_cache} LIMIT 1");
	list($raid) = @mysql_fetch_array($query);
	if (@mysql_num_rows($query) == 0 || trim($raid) == '')
	{
		$xml_data = getXML(achievementURL($name, $region, $realm));
		if (!$xml = @simplexml_load_string($xml_data, 'SimpleXMLElement'))
		{
			print $language->words['invalid_xml'];
			mysql_close($conn);
			exit;	
		}
		
		// to hold the completed raids
		$complete_ten = $complete_twenty = $complete_cata = array();
		foreach ($xml->category->category as $cat)
		{
			foreach ($cat->achievement as $chieve)
			{
				// cataclysm raids
				if ((string)$chieve['dateCompleted'] != '' && in_array((int)$chieve['id'], $raid_cataclysm))
				{
					$complete_cata[] = array(
						'id'	=>	(string)$chieve['id'],
						'name'	=>	(string)$chieve['title'],
						'icon'	=>	getAchievementIcon((int)$chieve['id'])
					);	
				}
				
				// 10 man raids
				if ((string)$chieve['dateCompleted'] != '' && in_array((int)$chieve['id'], $raid_ten))
				{
					$complete_ten[] = array(
						'id'	=>	(string)$chieve['id'],
						'name'	=>	(string)$chieve['title'],
						'icon'	=>	getAchievementIcon((int)$chieve['id'])
					);
				}
				// 25 man raids
				elseif ((string)$chieve['dateCompleted'] != '' && in_array((int)$chieve['id'], $raid_twenty_five))
				{
					$complete_twenty[] = array(
						'id'	=>	(string)$chieve['id'],
						'name'	=>	(string)$chieve['title'],
						'icon'	=>	getAchievementIcon((int)$chieve['id'])
					);
				}
			}
		}
		$out = '';
		// print cataclysm achievements
		if (sizeof($complete_cata) > 0)
		{
			$out .= '<br /><strong>Cataclysm Raid Progression</strong><br />';
			foreach ($complete_cata as $raid_prog)
				$out .= "<a class=\"achievement icontiny\" style=\"background-image: url({$raid_prog['icon']});\" href=\"{$wowhead_url}achievement={$raid_prog['id']}\" target=\"_blank\">[{$raid_prog['name']}]</a><br />";
		}
		
		// print 10 man achievements
		if (sizeof($complete_ten) > 0)
		{
			$out .= '<br /><strong>10 Man Raid Progression</strong><br />';
			foreach ($complete_ten as $raid_prog)
				$out .= "<a class=\"achievement icontiny\" style=\"background-image: url({$raid_prog['icon']});\" href=\"{$wowhead_url}achievement={$raid_prog['id']}\" target=\"_blank\">[{$raid_prog['name']}]</a><br />";
		}
		
		// print 25 man achievements
		if (sizeof($complete_twenty) > 0)
		{
			$out .= '<br/><strong>25 Man Raid Progression</strong><br />';
			foreach ($complete_twenty as $raid_prog)
				$out .= "<a class=\"achievement icontiny\" style=\"background-image: url({$raid_prog['icon']});\" href=\"{$wowhead_url}achievement={$raid_prog['id']}\" target=\"_blank\">[{$raid_prog['name']}]</a><br />";
		}
		
		// insert/update mysql
		$dummy_text = "INSERT INTO `" . WHP_DB_PREFIX . "recruit` (
							`uniquekey`, 
							`cache`,
							`raid`
						) VALUES (
							'$key',
							UNIX_TIMESTAMP(NOW()),
							'" . addslashes($out) . "'
						)
						ON DUPLICATE KEY UPDATE
							raid='" . addslashes($out) . "',
							cache=UNIX_TIMESTAMP(NOW())";
		$dummy = mysql_query($dummy_text);		
		print $out;
	}
	else
	{
		print stripslashes($raid);	
	}
}
elseif ($mode == 'talents')
{
	$query = mysql_query("SELECT talents FROM " . WHP_DB_PREFIX . "recruit WHERE uniquekey='$key' AND cache > UNIX_TIMESTAMP(NOW()) - {$config->recruit_cache} LIMIT 1");
	list($result) = @mysql_fetch_array($query);
	
	if (mysql_num_rows($query) == 0 || trim($result) == '')
	{
		$xml_data = getXML(talentsURL($name, $region, $realm));
		if (!$xml = @simplexml_load_string($xml_data, 'SimpleXMLElement'))
		{
			// invalid xml
			print $language->words['invalid_xml'];
			mysql_close($conn);
			exit;
		}
		elseif (!$xml->characterInfo->talents || !$xml->characterInfo->talents->talentGroup)
		{
			// no talents
			print $language->words['char_no_data'];
			mysql_close($conn);
			exit;	
		}
		elseif ((string)$xml->characterInfo->talents->talentGroup['prim'] == 'Untalented')
		{
			// untalented
			print $language->words['untalented'];
			mysql_close($conn);
			exit;	
		}
		//var_dump($xml); die;
		$specs = array();	// this will hold the info we get from XML
		
		$specs['class'] = strtolower(str_replace(' ', '', (string)$xml->characterInfo->character['class']));	// for the talent tree breakdown
		
		// make sure the talent tree breakdowns exist
		if (!file_exists(dirname(__FILE__) . '/../includes/talents/' . $specs['class'] . '.php'))
		{
			print $language->words['no_char_breakdown'];
			mysql_close($conn);
			exit;	
		}
		require(dirname(__FILE__) . '/../includes/talents/' . $specs['class'] . '.php');
		if (!isset($talents) || sizeof($talents) == 0)
		{
			print $language->words['no_char_breakdown'];
			mysql_close($conn);
			exit;	
		}
		
		// now loop through each spec and gather the necessary info
		foreach ($xml->characterInfo->talents->talentGroup as $spec)
		{
			$size = sizeof($specs);
			// talent trees and the "talent string"
			$specs['spec'][$size]['one'] 		=	array(	// tree one
				'val'	=>	(string)$spec->talentSpec['treeOne'],
				'name'	=>	$language->words[$specs['class'] . '_1'],
				'image'	=>	$config->armory_image_url . 'images/talents/' . $specs['class'] . '/1.gif'
			);
			$specs['spec'][$size]['two'] 		=	array(	// tree two
				'val'	=>	(string)$spec->talentSpec['treeTwo'],
				'name'	=>	$language->words[$specs['class'] . '_2'],
				'image'	=>	$config->armory_image_url . 'images/talents/' . $specs['class'] . '/2.gif'
			);
			$specs['spec'][$size]['three'] 		=	array(	// tree three
				'val'	=>	(string)$spec->talentSpec['treeThree'],
				'name'	=>	$language->words[$specs['class'] . '_3'],
				'image'	=>	$config->armory_image_url . 'images/talents/' . $specs['class'] . '/3.gif'
			);
			
			$specs['spec'][$size]['string']		=	(string)$spec->talentSpec['value'];
			$specs['spec'][$size]['image']		=	getTalentImage($specs['class'], (int)$spec->talentSpec['treeOne'], (int)$spec->talentSpec['treeTwo'], (int)$spec->talentSpec['treeThree']); 
		
			// glyphs
			if ($spec->glyphs)
			{
				foreach ($spec->glyphs->glyph as $glyph)
					$specs['spec'][$size]['glyphs'][(string)$glyph['type']][]	=	(string)$glyph['name'];	
			}
		}
		
		$patterns = new wowhead_patterns();

		if (sizeof($specs['spec']) > 1)
		{
			$out = '<div style="padding-left: 3px;"><input id="toggle_talents" type="radio" name="toggle_talents" value="1" checked="checked" style="margin-bottom: 3px;" />&nbsp;' . $specs['spec'][1]['one']['val'] . '/' . $specs['spec'][1]['two']['val'] . '/' . $specs['spec'][1]['three']['val'] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';	
			$out .= '<input id="toggle_talents" type="radio" name="toggle_talents" value="2" style="margin-bottom: 3px;" />&nbsp;' . $specs['spec'][2]['one']['val'] . '/' . $specs['spec'][2]['two']['val'] . '/' . $specs['spec'][2]['three']['val'] . '</div>';	
		}
		
		// build the initial wrapper
		$talents_wrapper = $patterns->pattern('talents_generic');
		$talents_wrapper = str_replace('{image_url}', $config->armory_image_url, $talents_wrapper);
		$talents_wrapper = str_replace('{class}', $specs['class'], $talents_wrapper);
		$talent_calc = blizzardToWowhead($specs['class'], $specs['spec'][1]['string'], true);
		$talent_icons = array(
			1	=>	$config->armory_image_url . 'images/talents/icons/' . $specs['class'] . '_1.jpg',
			2	=>	$config->armory_image_url . 'images/talents/icons/' . $specs['class'] . '_2.jpg',
			3	=>	$config->armory_image_url . 'images/talents/icons/' . $specs['class'] . '_3.jpg',
		);
		
		// now build first talent tree
		$tree_one_one = '<div style="clear:both;background-color:#111111;background-image: url(' . $config->armory_image_url . 'images/mesh.gif);background-repeat:repeat;border:1px solid #666666;margin:0;padding:3px;color:#555555;font-size:11px;font-weight:bold;"><img style="margin-bottom: -3px;" height="15" width="15" src="' . $specs['spec'][1]['one']['image'] . '" alt="" /> ' . $specs['spec'][1]['one']['name'] . ' (' . $specs['spec'][1]['one']['val'] . ')</div><br />';
		$i = 0;
		foreach ($talents['tree_one'] as $tier)
		{
			$row_content = '';
			foreach($tier as $spell)
			{
				if (is_array($spell))
				{
					// gotta calculate the background position of the talent icons, so they'll display properly
					$x = ($i * -36) . 'px';
					
					$val = $specs['spec'][1]['string'][0];
					if ($val != 0)
					{
						if ((int)$val < (int)$spell['max'])
							$row_content .= sprintf($patterns->pattern('talents_block_half'), $talent_icons[1], $x, '0', $wowhead_url . 'spell=' . $spell['id'], $config->armory_image_url, $val);
						else
							$row_content .= sprintf($patterns->pattern('talents_block_full'), $talent_icons[1], $x, '0', $wowhead_url . 'spell=' . $spell['id'], $config->armory_image_url, $val);
					}
					else
					{
						$row_content .= sprintf($patterns->pattern('talents_block_empty'), $talent_icons[1], $x, '-36px', $wowhead_url . 'spell=' . $spell['id']);
					}	
					$specs['spec'][1]['string'] = substr($specs['spec'][1]['string'], 1);
					$i++;
				}
				else
				{
					$row_content .= $patterns->pattern('talents_block_placeholder');
				}
			}
			$tree_one_one .= sprintf($patterns->pattern('talents_row'), $row_content);
		}
			
		// now build second talent tree
		$tree_two_one = '<div style="clear:both;background-color:#111111;background-image: url(' . $config->armory_image_url . 'images/mesh.gif);background-repeat:repeat;border:1px solid #666666;margin:0;padding:3px;color:#555555;font-size:11px;font-weight:bold;"><img style="margin-bottom: -3px;" height="15" width="15" src="' . $specs['spec'][1]['two']['image'] . '" alt="" /> ' . $specs['spec'][1]['two']['name'] . ' (' . $specs['spec'][1]['two']['val'] . ')</div><br />';
		$i = 0;
		foreach ($talents['tree_two'] as $tier)
		{
			$row_content = '';
			foreach($tier as $spell)
			{
				if (is_array($spell))
				{
					// gotta calculate the background position of the talent icons, so they'll display properly
					$x = ($i * -36) . 'px';
					
					$val = $specs['spec'][1]['string'][0];
					if ($val != 0)
					{
						if ((int)$val < (int)$spell['max'])
							$row_content .= sprintf($patterns->pattern('talents_block_half'), $talent_icons[2], $x, '0', $wowhead_url . 'spell=' . $spell['id'], $config->armory_image_url, $val);
						else
							$row_content .= sprintf($patterns->pattern('talents_block_full'), $talent_icons[2], $x, '0', $wowhead_url . 'spell=' . $spell['id'], $config->armory_image_url, $val);
					}
					else
					{
						$row_content .= sprintf($patterns->pattern('talents_block_empty'), $talent_icons[2], $x, '-36px', $wowhead_url . 'spell=' . $spell['id']);
					}	
					$specs['spec'][1]['string'] = substr($specs['spec'][1]['string'], 1);
					$i++;
				}
				else
				{
					$row_content .= $patterns->pattern('talents_block_placeholder');
				}
			}
			$tree_two_one .= sprintf($patterns->pattern('talents_row'), $row_content);
		}
		
		// now build third talent tree
		$tree_three_one = '<div style="clear:both;background-color:#111111;background-image: url(' . $config->armory_image_url . 'images/mesh.gif);background-repeat:repeat;border:1px solid #666666;margin:0;padding:3px;color:#555555;font-size:11px;font-weight:bold;"><img style="margin-bottom: -3px;" height="15" width="15" src="' . $specs['spec'][1]['three']['image'] . '" alt="" /> ' . $specs['spec'][1]['three']['name'] . ' (' . $specs['spec'][1]['three']['val'] . ')</div><br />';
		$i = 0;
		foreach ($talents['tree_three'] as $tier)
		{
			$row_content = '';
			foreach($tier as $spell)
			{
				if (is_array($spell))
				{
					// gotta calculate the background position of the talent icons, so they'll display properly
					$x = ($i * -36) . 'px';
					
					$val = $specs['spec'][1]['string'][0];
					if ($val != 0)
					{
						if ((int)$val < (int)$spell['max'])
							$row_content .= sprintf($patterns->pattern('talents_block_half'), $talent_icons[3], $x, '0', $wowhead_url . 'spell=' . $spell['id'], $config->armory_image_url, $val);
						else
							$row_content .= sprintf($patterns->pattern('talents_block_full'), $talent_icons[3], $x, '0', $wowhead_url . 'spell=' . $spell['id'], $config->armory_image_url, $val);
					}
					else
					{
						$row_content .= sprintf($patterns->pattern('talents_block_empty'), $talent_icons[3], $x, '-36px', $wowhead_url . 'spell=' . $spell['id']);
					}	
					$specs['spec'][1]['string'] = substr($specs['spec'][1]['string'], 1);
					$i++;
				}
				else
				{
					$row_content .= $patterns->pattern('talents_block_placeholder');
				}
			}
			$tree_three_one .= sprintf($patterns->pattern('talents_row'), $row_content);
		}
		$out .= sprintf($talents_wrapper, 'talentSpecOne', 'inline', $talent_calc, $tree_one_one, $tree_two_one, $tree_three_one);

		if (sizeof($specs['spec'][2]) > 0)
		{
			// build the initial wrapper
			$talents_wrapper = $patterns->pattern('talents_generic');
			$talents_wrapper = str_replace('{image_url}', $config->armory_image_url, $talents_wrapper);
			$talents_wrapper = str_replace('{class}', $specs['class'], $talents_wrapper);
			$talent_calc = blizzardToWowhead($specs['class'], $specs['spec'][2]['string'], true);
			$talent_icons = array(
				1	=>	$config->armory_image_url . 'images/talents/icons/' . $specs['class'] . '_1.jpg',
				2	=>	$config->armory_image_url . 'images/talents/icons/' . $specs['class'] . '_2.jpg',
				3	=>	$config->armory_image_url . 'images/talents/icons/' . $specs['class'] . '_3.jpg',
			);
		
			// now build first talent tree
			$tree_one_one = '<div style="clear:both;background-color:#111111;background-image: url(' . $config->armory_image_url . 'images/mesh.gif);background-repeat:repeat;border:1px solid #666666;margin:0;padding:3px;color:#555555;font-size:11px;font-weight:bold;"><img style="margin-bottom: -3px;" height="15" width="15" src="' . $specs['spec'][2]['one']['image'] . '" alt="" /> ' . $specs['spec'][2]['one']['name'] . ' (' . $specs['spec'][2]['one']['val'] . ')</div><br />';
			$i = 0;
			foreach ($talents['tree_one'] as $tier)
			{
				$row_content = '';
				foreach($tier as $spell)
				{
					if (is_array($spell))
					{
						// gotta calculate the background position of the talent icons, so they'll display properly
						$x = ($i * -36) . 'px';
					
						$val = $specs['spec'][2]['string'][0];
						if ($val != 0)
						{
							if ((int)$val < (int)$spell['max'])
								$row_content .= sprintf($patterns->pattern('talents_block_half'), $talent_icons[1], $x, '0', $wowhead_url . 'spell=' . $spell['id'], $config->armory_image_url, $val);
							else
								$row_content .= sprintf($patterns->pattern('talents_block_full'), $talent_icons[1], $x, '0', $wowhead_url . 'spell=' . $spell['id'], $config->armory_image_url, $val);
						}
						else
						{
							$row_content .= sprintf($patterns->pattern('talents_block_empty'), $talent_icons[1], $x, '-36px', $wowhead_url . 'spell=' . $spell['id']);
						}	
						$specs['spec'][2]['string'] = substr($specs['spec'][2]['string'], 1);
						$i++;
					}
					else
					{
						$row_content .= $patterns->pattern('talents_block_placeholder');
					}
				}
				$tree_one_one .= sprintf($patterns->pattern('talents_row'), $row_content);
			}
			
			// now build second talent tree
			$tree_two_one = '<div style="clear:both;background-color:#111111;background-image: url(' . $config->armory_image_url . 'images/mesh.gif);background-repeat:repeat;border:1px solid #666666;margin:0;padding:3px;color:#555555;font-size:11px;font-weight:bold;"><img style="margin-bottom: -3px;" height="15" width="15" src="' . $specs['spec'][2]['two']['image'] . '" alt="" /> ' . $specs['spec'][2]['two']['name'] . ' (' . $specs['spec'][2]['two']['val'] . ')</div><br />';
			$i = 0;
			foreach ($talents['tree_two'] as $tier)
			{
				$row_content = '';
				foreach($tier as $spell)
				{
					if (is_array($spell))
					{
						// gotta calculate the background position of the talent icons, so they'll display properly
						$x = ($i * -36) . 'px';
					
						$val = $specs['spec'][2]['string'][0];
						if ($val != 0)
						{
							if ((int)$val < (int)$spell['max'])
								$row_content .= sprintf($patterns->pattern('talents_block_half'), $talent_icons[2], $x, '0', $wowhead_url . 'spell=' . $spell['id'], $config->armory_image_url, $val);
							else
								$row_content .= sprintf($patterns->pattern('talents_block_full'), $talent_icons[2], $x, '0', $wowhead_url . 'spell=' . $spell['id'], $config->armory_image_url, $val);
						}
						else
						{
							$row_content .= sprintf($patterns->pattern('talents_block_empty'), $talent_icons[2], $x, '-36px', $wowhead_url . 'spell=' . $spell['id']);
						}	
						$specs['spec'][2]['string'] = substr($specs['spec'][2]['string'], 1);
						$i++;
					}
					else
					{
						$row_content .= $patterns->pattern('talents_block_placeholder');
					}
				}
				$tree_two_one .= sprintf($patterns->pattern('talents_row'), $row_content);
			}
		
			// now build third talent tree
			$tree_three_one = '<div style="clear:both;background-color:#111111;background-image: url(' . $config->armory_image_url . 'images/mesh.gif);background-repeat:repeat;border:1px solid #666666;margin:0;padding:3px;color:#555555;font-size:11px;font-weight:bold;"><img style="margin-bottom: -3px;" height="15" width="15" src="' . $specs['spec'][2]['three']['image'] . '" alt="" /> ' . $specs['spec'][2]['three']['name'] . ' (' . $specs['spec'][2]['three']['val'] . ')</div><br />';
			$i = 0;
			foreach ($talents['tree_three'] as $tier)
			{
				$row_content = '';
				foreach($tier as $spell)
				{
					if (is_array($spell))
					{
						// gotta calculate the background position of the talent icons, so they'll display properly
						$x = ($i * -36) . 'px';
					
						$val = $specs['spec'][2]['string'][0];
						if ($val != 0)
						{
							if ((int)$val < (int)$spell['max'])
								$row_content .= sprintf($patterns->pattern('talents_block_half'), $talent_icons[3], $x, '0', $wowhead_url . 'spell=' . $spell['id'], $config->armory_image_url, $val);
							else
								$row_content .= sprintf($patterns->pattern('talents_block_full'), $talent_icons[3], $x, '0', $wowhead_url . 'spell=' . $spell['id'], $config->armory_image_url, $val);
						}
						else
						{
							$row_content .= sprintf($patterns->pattern('talents_block_empty'), $talent_icons[3], $x, '-36px', $wowhead_url . 'spell=' . $spell['id']);
						}	
						$specs['spec'][2]['string'] = substr($specs['spec'][2]['string'], 1);
						$i++;
					}
					else
					{
						$row_content .= $patterns->pattern('talents_block_placeholder');
					}
				}
				$tree_three_one .= sprintf($patterns->pattern('talents_row'), $row_content);
			}
			// generate the wowhead talent calculator link
			$out .= sprintf($talents_wrapper, 'talentSpecTwo', 'none', $talent_calc, $tree_one_one, $tree_two_one, $tree_three_one);		
		}

		// now time to display glyphs
		if (array_key_exists('glyphs', $specs['spec'][1]) || array_key_exists('glyphs', $specs['spec'][2]))
		{
			$wow_item = new wowhead_item($config);
			$out .= '<br /><table class="glyph-table" cellspacing="0" cellpadding="0" style="margin-left: 3px;">';
			$out .= '<tr><td class="glyph-table-head">Glyphs</td></tr>';
			
			// spec one
			if (array_key_exists('glyphs', $specs['spec'][1]))
			{
				$out .= '<tr><td><div id="glyphOne">';
				// prime glyphs
				if (sizeof($specs['spec'][1]['glyphs']['prime']) > 0)
				{
					$out .= '<div class="glyph-title">Prime</div>';
					foreach ($specs['spec'][1]['glyphs']['prime'] as $glyph)
					{
						// if language isn't English we need to do an extra step
						if ($config->lang != 'en')
						{
							// we need to get the ID from the english version then use that to query for the language specific version
							$glyph_data = getXML('http://www.wowhead.com/item=' . convertString($glyph) . '&xml');
							
							// incase the XML is fubar'd
							if (!$glyph_data || !$glyph_xml = @simplexml_load_string($glyph_data, 'SimpleXMLElement', LIBXML_NOCDATA))
							{
								print $language->words['invalid_xml'];
								mysql_close($conn);
								exit;	
							}
							// glyph not found, not generally possible but you never know
							elseif ($glyph_xml->error != '')
							{
								$errorstr = $language->words['notfound'];
								$errorstr = str_replace('{type}', $language->words['item'], $errorstr);
								$errorstr = str_replace('{name}', $glyph, $errorstr);
								print $errorstr;
								mysql_close($conn);
								exit;	
							}
	
							// get the id and unset the xml object
							$glyph_id = (string)$glyph_xml->item['id'];
							unset($glyph_xml, $glyph_data);
							
							// print the results
							$out .= '<div style="padding-left: 15px; display: block;">' . $wow_item->parse($glyph_id, array('icon' => true)) . '</div>';
						}
						else
						{
							$out .= '<div style="padding-left: 15px; display: block;">' . $wow_item->parse($glyph, array('icon' => true)) . '</div>';
						}
					}
				}
			
				// major glyphs
				if (sizeof($specs['spec'][1]['glyphs']['major']) > 0)
				{
					$out .= '<div class="glyph-title">Major</div>';
					foreach ($specs['spec'][1]['glyphs']['major'] as $glyph)
					{
						// if language isn't English we need to do an extra step
						if ($config->lang != 'en')
						{
							// we need to get the ID from the english version then use that to query for the language specific version
							$glyph_data = getXML('http://www.wowhead.com/item=' . convertString($glyph) . '&xml');
							
							// incase the XML is fubar'd
							if (!$glyph_data || !$glyph_xml = @simplexml_load_string($glyph_data, 'SimpleXMLElement', LIBXML_NOCDATA))
							{
								print $language->words['invalid_xml'];
								mysql_close($conn);
								exit;	
							}
							// glyph not found, not generally possible but you never know
							elseif ($glyph_xml->error != '')
							{
								$errorstr = $language->words['notfound'];
								$errorstr = str_replace('{type}', $language->words['item'], $errorstr);
								$errorstr = str_replace('{name}', $glyph, $errorstr);
								print $errorstr;
								mysql_close($conn);
								exit;	
							}
	
							// get the id and unset the xml object
							$glyph_id = (string)$glyph_xml->item['id'];
							unset($glyph_xml, $glyph_data);
							
							// print the results
							$out .= '<div style="padding-left: 15px; display: block;">' . $wow_item->parse($glyph_id, array('icon' => true)) . '</div>';
						}
						else
						{
							$out .= '<div style="padding-left: 15px; display: block;">' . $wow_item->parse($glyph, array('icon' => true)) . '</div>';
						}
					}
				}
				
				// minor glyphs
				if (sizeof($specs['spec'][1]['glyphs']['minor']) > 0)
				{
					$out .= '<div class="glyph-title">Minor</div>';
					foreach ($specs['spec'][1]['glyphs']['minor'] as $glyph)
					{
						// if language isn't English we need to do an extra step
						if ($config->lang != 'en')
						{
							// we need to get the ID from the english version then use that to query for the language specific version
							$glyph_data = getXML('http://www.wowhead.com/item=' . convertString($glyph) . '&xml');
							
							// incase the XML is fubar'd
							if (!$glyph_data || !$glyph_xml = @simplexml_load_string($glyph_data, 'SimpleXMLElement', LIBXML_NOCDATA))
							{
								print $language->words['invalid_xml'];
								mysql_close($conn);
								exit;	
							}
							// glyph not found, not generally possible but you never know
							elseif ($glyph_xml->error != '')
							{
								$errorstr = $language->words['notfound'];
								$errorstr = str_replace('{type}', $language->words['item'], $errorstr);
								$errorstr = str_replace('{name}', $glyph, $errorstr);
								print $errorstr;
								mysql_close($conn);
								exit;	
							}
	
							// get the id and unset the xml object
							$glyph_id = (string)$glyph_xml->item['id'];
							unset($glyph_xml, $glyph_data);
							
							// print the results
							$out .= '<div style="padding-left: 15px; display: block;">' . $wow_item->parse($glyph_id, array('icon' => true)) . '</div>';
						}
						else
						{
							$out .= '<div style="padding-left: 15px; display: block;">' . $wow_item->parse($glyph, array('icon' => true)) . '</div>';
						}
					}
				}
				
				$out .= '</div>';
			}
			
			// spec two
			if (array_key_exists('glyphs', $specs['spec'][2]))
			{
				$out .= '<tr><td><div id="glyphTwo" style="display: none;">';
				
				// major glyphs
				if (sizeof($specs['spec'][2]['glyphs']['prime']) > 0)
				{
					$out .= '<div class="glyph-title">Prime</div>';
					foreach ($specs['spec'][2]['glyphs']['prime'] as $glyph)
					{
						// if language isn't English we need to do an extra step
						if ($config->lang != 'en')
						{
							// we need to get the ID from the english version then use that to query for the language specific version
							$glyph_data = getXML('http://www.wowhead.com/item=' . convertString($glyph) . '&xml');
							
							// incase the XML is fubar'd
							if (!$glyph_data || !$glyph_xml = @simplexml_load_string($glyph_data, 'SimpleXMLElement', LIBXML_NOCDATA))
							{
								print $language->words['invalid_xml'];
								mysql_close($conn);
								exit;	
							}
							// glyph not found, not generally possible but you never know
							elseif ($glyph_xml->error != '')
							{
								$errorstr = $language->words['notfound'];
								$errorstr = str_replace('{type}', $language->words['item'], $errorstr);
								$errorstr = str_replace('{name}', $glyph, $errorstr);
								print $errorstr;
								mysql_close($conn);
								exit;	
							}
	
							// get the id and unset the xml object
							$glyph_id = (string)$glyph_xml->item['id'];
							unset($glyph_xml, $glyph_data);
							
							// print the results
							$out .= '<div style="padding-left: 15px; display: block;">' . $wow_item->parse($glyph_id, array('icon' => true)) . '</div>';
						}
						else
						{
							$out .= '<div style="padding-left: 15px; display: block;">' . $wow_item->parse($glyph, array('icon' => true)) . '</div>';
						}
					}
				}
			
				// major glyphs
				if (sizeof($specs['spec'][2]['glyphs']['major']) > 0)
				{
					$out .= '<div class="glyph-title">Major</div>';
					foreach ($specs['spec'][2]['glyphs']['major'] as $glyph)
					{
						// if language isn't English we need to do an extra step
						if ($config->lang != 'en')
						{
							// we need to get the ID from the english version then use that to query for the language specific version
							$glyph_data = getXML('http://www.wowhead.com/item=' . convertString($glyph) . '&xml');
							
							// incase the XML is fubar'd
							if (!$glyph_data || !$glyph_xml = @simplexml_load_string($glyph_data, 'SimpleXMLElement', LIBXML_NOCDATA))
							{
								print $language->words['invalid_xml'];
								mysql_close($conn);
								exit;	
							}
							// glyph not found, not generally possible but you never know
							elseif ($glyph_xml->error != '')
							{
								$errorstr = $language->words['notfound'];
								$errorstr = str_replace('{type}', $language->words['item'], $errorstr);
								$errorstr = str_replace('{name}', $glyph, $errorstr);
								print $errorstr;
								mysql_close($conn);
								exit;	
							}
	
							// get the id and unset the xml object
							$glyph_id = (string)$glyph_xml->item['id'];
							unset($glyph_xml, $glyph_data);
							
							// print the results
							$out .= '<div style="padding-left: 15px; display: block;">' . $wow_item->parse($glyph_id, array('icon' => true)) . '</div>';
						}
						else
						{
							$out .= '<div style="padding-left: 15px; display: block;">' . $wow_item->parse($glyph, array('icon' => true)) . '</div>';
						}
					}
				}
				
				// minor glyphs
				if (sizeof($specs['spec'][2]['glyphs']['minor']) > 0)
				{
					$out .= '<div class="glyph-title">Minor</div>';
					foreach ($specs['spec'][2]['glyphs']['minor'] as $glyph)
					{
						// if language isn't English we need to do an extra step
						if ($config->lang != 'en')
						{
							// we need to get the ID from the english version then use that to query for the language specific version
							$glyph_data = getXML('http://www.wowhead.com/item=' . convertString($glyph) . '&xml');
							
							// incase the XML is fubar'd
							if (!$glyph_data || !$glyph_xml = @simplexml_load_string($glyph_data, 'SimpleXMLElement', LIBXML_NOCDATA))
							{
								print $language->words['invalid_xml'];
								mysql_close($conn);
								exit;	
							}
							// glyph not found, not generally possible but you never know
							elseif ($glyph_xml->error != '')
							{
								$errorstr = $language->words['notfound'];
								$errorstr = str_replace('{type}', $language->words['item'], $errorstr);
								$errorstr = str_replace('{name}', $glyph, $errorstr);
								print $errorstr;
								mysql_close($conn);
								exit;	
							}
							
							// get the id and unset the xml object
							$glyph_id = (string)$glyph_xml->item['id'];
							unset($glyph_xml, $glyph_data);
	
							// print the results
							$out .= '<div style="padding-left: 15px; display: block;">' . $wow_item->parse($glyph_id, array('icon' => true)) . '</div>';
						}
						else
						{
							$out .= '<div style="padding-left: 15px; display: block;">' . $wow_item->parse($glyph, array('icon' => true)) . '</div>';
						}
					}
				}
				
				$out .= '</div>';
			}
			$out .= '</table>';
			unset($wow_item);	
		}
		// insert/update mysql
		$dummy_text = "INSERT INTO `" . WHP_DB_PREFIX . "recruit` (
							`uniquekey`, 
							`cache`,
							`talents`
						) VALUES (
							'$key',
							UNIX_TIMESTAMP(NOW()),
							'" . addslashes($out) . "'
						)
						ON DUPLICATE KEY UPDATE
							talents='" . addslashes($out) . "',
							cache=UNIX_TIMESTAMP(NOW())";
		$dummy = mysql_query($dummy_text);
		print $out;
	}
	else
	{
		print stripslashes($result);	
	}
}

mysql_close($conn);
?>
