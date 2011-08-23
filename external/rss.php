<?php
/**
*
* @package Wowhead Tooltips
* @version rss.php 4.0
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/functions.php');
require_once(dirname(__FILE__) . '/../includes/wowhead_log.php');
require_once(dirname(__FILE__) . '/../includes/wowhead_config.php');
require_once(dirname(__FILE__) . '/../includes/wowhead_cache.php');
require_once(dirname(__FILE__) . '/../includes/wowhead_patterns.php');
require_once(dirname(__FILE__) . '/../includes/wowhead_language.php');
require_once(dirname(__FILE__) . '/../includes/wowhead_item.php');
require_once(dirname(__FILE__) . '/../includes/wowhead_achievement.php');

// load the config
$config = new wowhead_config();
$config->loadConfig();

// connect to sql and select the database
if (!$conn = @mysql_connect(WHP_DB_HOST, WHP_DB_USER, WHP_DB_PASS))
{
	print mysql_error();
	exit;
}
elseif (!@mysql_select_db(WHP_DB_NAME, $conn))
{
	print mysql_error();
	exit;
}

// pull the data from the GET global
$name 	=	(array_key_exists('name', $_GET))	? stripslashes(html_entity_decode($_GET['name'], ENT_QUOTES))	: '';
$region =	(array_key_exists('region', $_GET))	? stripslashes(html_entity_decode($_GET['region'], ENT_QUOTES))	: '';
$realm 	=	(array_key_exists('realm', $_GET))	? stripslashes(html_entity_decode($_GET['realm'], ENT_QUOTES))	: '';

if (trim($name) == '')
{
	mysql_close($conn);
	echo 'No name given.';
	exit;
}
elseif (trim($region) == '')
{
	mysql_close($conn);
	echo 'No region given.';
	exit;
}
elseif (trim($realm) == '')
{
	mysql_close($conn);
	echo 'No realm given.';
	exit;
}

// generate the unique key for SQL
$key = generateKey($name, $realm, $region);
if (trim($key) == '')
{
	mysql_close($conn);
	echo 'Unable to generate the unique key.';
	exit;
}

// see if SQL already has it and its cache time hasn't elapsed
$query = mysql_query("SELECT rss FROM `" . WHP_DB_PREFIX . "rss` WHERE uniquekey='{$key}' AND cache > UNIX_TIMESTAMP(NOW()) - {$config->recruit_cache} LIMIT 1");
if (@mysql_num_rows($query) == 0)
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
		$content = (string)$entry->content;
		// first get the timestamp (hopefully)
		$stamp = strtotime((string)$entry->updated);
		
		// now we need to determine what actually happened
		if (strpos($content, 'Completed step') !== false)	// completed step of achievement
		{
			// we'll use regular expressions to pull the information we need
			if (!preg_match('#Completed step \[<strong>(.+?)</strong>\] of achievement#s', $content, $match))
				continue;	// if not found then continue the foreach
			else
			{
				$step = $match[1];	// get the step
				
				// now get the achievement
				if (!preg_match('/#ach([0-9]{1,10})/s', $content, $match))
					continue;	// if not found then continue the foreach
				else
				{
					$id = (int)$match[1];
					$achievement = new wowhead_achievement();
					$entries[$stamp] = 'Completed step <span style="color: purple;">[' . $step . ']</span> of achievement ' . $achievement->parse($id);
					unset($achievement);
				}
			}
		}
		elseif (strpos($content, 'Earned the achievement') !== false)	// completed an achievement
		{
			if (!preg_match('/#ach([0-9]{1,10})/s', $content, $match))
				continue;
			else
			{
				$id = (int)$match[1];
				$achievement = new wowhead_achievement();
				$entries[$stamp] = 'Earned achievement ' . $achievement->parse($id);
				unset($achievement);
			}
		}
		elseif (strpos($content, 'Obtained') !== false)	// obtained an item
		{
			if (!preg_match('/xml\?i=([0-9]{1,10})/s', $content, $match))
				continue;
			else
			{
				$id = (int)$match[1];
				$item = new wowhead_item();
				$entries[$stamp] = 'Obtained ' . $item->parse($id);
				unset($item);
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
	$dummy_text = "INSERT INTO `" . WHP_DB_PREFIX . "rss` (
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
	list($rss_data) = mysql_fetch_array($query);
	echo stripslashes($rss_data);	
}
mysql_close($conn);
?>
