<?php
/**
*
* @package Wowhead Tooltips
* @version parse.php 4.3
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

if (!defined('WOWHEAD_INSTALL'))
	define('WOWHEAD_INSTALL', dirname(__FILE__) . '/install/');
if (!defined('WOWHEAD_ROOT'))
	define('WOWHEAD_ROOT', dirname(__FILE__) . '/');
if (!defined('WOWHEAD_INCLUDES'))
	define('WOWHEAD_INCLUDE', dirname(__FILE__) . '/includes/');

require_once(WOWHEAD_ROOT . 'includes.php');

// make sure the SQL data is available
if (!defined('WHP_DB_USER'))
	die('[Wowhead Tooltips] You must first run the installation script before attempting to use the script.');
// check to make sure that the correct PHP version is used and SimpleXML is enabled
if (version_compare(PHP_VERSION, '5.2.0') < 0)
	die('This script requires PHP version 5.2.0 or higher.  You\'re using ' . PHP_VERSION . '.  Speak to your host about upgrading to PHP 5.2.0+.');
elseif (!function_exists('simplexml_load_string'))
	die('This script requires SimpleXML to be enabled.  Speak to your host about enabling it.');
// check to make sure the install directory has been deleted or renamed
if (file_exists(WOWHEAD_INSTALL) && defined('WOWHEAD_VERSION'))
	die('[Wowhead Tooltips] You must either delete or rename the install directory before you can use this script.');
elseif (!defined('WOWHEAD_VERSION'))
	die('[Wowhead Tooltips] You must first install the script by running <tt>index.php</tt> inside the <tt>install</tt> folder.');

// new arguments function
function whp_get_args($in)
{
	$args = array();
	
	// the arguments that get set to true
	$toggles = array(
		'nomats', 'noicons', 'noclass', 'norace', 'icon',
		'gearlist', 'rss', 'heroic', 'horde', 'alliance',
		'beta', 'short', 'parse'
	);
	
	if (strlen($in) == 0) { return array(); }

	if (preg_match('/loc="(.+?)"/', $in, $match))
	{
		$args['loc'] = $match[1];
		$in = str_replace($match[0], '', $in);
	}
	
	// handle npc maps
	if (preg_match('/map="(.+?)"/', $in, $match))
	{
		$parts = explode(':', $match[1]);
		$coord = explode(',', $parts[1]);
		$args['map']['name'] = $parts[0];
		$args['map']['x'] = $coord[0];
		$args['map']['y'] = $coord[1];
		$in = str_replace($match[0], '', $in);	
	}
	
	// handle name for the title module
	if (preg_match('/name="(.+?)"/', $in, $match))
	{
		$args['name'] = $match[1];
		$in = str_replace($match[0], '', $in);
	}
	
	$in = str_replace('"', '', $in);
	
	$in_array = explode(' ', $in);

	foreach ($in_array as $value)
	{
		if (in_array($value, $toggles))
			$args[$value] = true;
		else
		{
			$pre = substr($value, 0, strpos($value, '='));
			$post = substr($value, strpos($value, '=') + 1);
			$args[$pre] = html_entity_decode($post, ENT_QUOTES);
		}
	}
	return $args;
}

// builds the tags for the preg_match
// used for disabling modules
function whp_get_modules($whp_modules)
{
	if (!is_array($whp_modules))
		return false;
	$enabled = array();
	foreach ($whp_modules as $module => $enable)
	{
		if ($enable == true)
			$enabled[] = $module;
	}
	// combine all the enabled modules with a pipe (|)
	return implode('|', $enabled);
}

function whp_require_args($query)
{
	$required = array(
		'lang=', 'nomats', 'enchant=', 'size=', 'rank=', 'gems=',
		'loc=', 'noicons', 'noclass', 'norace', 'icon', 'gearlist',
		'pins=', 'map=', 'wowhead', 'rss', 'heroic', 'name=',
		'horde', 'alliance', 'beta', 'short', 'parse'
	);
	foreach ($required as $require)
	{
		if (strpos($query, $require) !== false)
			return true;
	}				
	return false;
}

function whp_parse($whp_message)
{
	$whp_config = new wowhead_config(); $whp_config->loadConfig();
	$parses = 0;
	// get the enabled modules
	$modules = whp_get_modules($whp_config->modules);

	if (!$modules)
		return $whp_message;

	while (($whp_config->max_parses == 0 || $parses < $whp_config->max_parses) &&
		preg_match('#\[(' . $modules . ') (.+?)\](.+?)\[/(' . $modules . ')\]#s', $whp_message, $match) ||
		preg_match('#\[(' . $modules . ')\](.+?)\[/(' . $modules . ')\]#s', $whp_message, $match))
	{
		// see if we require any arguments
		$args = (whp_require_args($match[2]) == true) ? whp_get_args(html_entity_decode($match[2], ENT_QUOTES)) : array();
		
		// make sure the dependencies are available
		require_dependencies($match[1]);

		// create the class
		switch ($match[1])
		{
			case 'achievement':
				$object = new wowhead_achievement();
				break;
			case 'armory':
				$object = new wowhead_armory();
				break;
			case 'class':
				$object = new wowhead_class();
				break;
			case 'craft':
				$object = new wowhead_craft();
				break;
			case 'currency':
				$object = new wowhead_currency();
				break;
			case 'enchant':
				$object = new wowhead_enchant();
				break;
			case 'event':
				$object = new wowhead_event();
				break;
			case 'faction':
				$object = new wowhead_faction();
				break;
			case 'guild':
				$object = new wowhead_guild();
				break;
			case 'item':
				$object = new wowhead_item();
				break;
			case 'itemico':
				$object = new wowhead_itemico();
				break;
			case 'itemset':
				$object = new wowhead_itemset();
				break;
			case 'npc':
				$object = new wowhead_npc();
				break;
			case 'object':
				$object = new wowhead_object();
				break;
			case 'pet':
				$object = new wowhead_pet();
				break;
			case 'prof':
				$object = new wowhead_profession();
				break;
			case 'profile':
				$object = new wowhead_profile();
				break;
			case 'quest':
				$object = new wowhead_quest();
				break;
			case 'race':
				$object = new wowhead_race();
				break;
			case 'recruit':
				$object = new wowhead_recruit();
				break;
			case 'spell':
				$object = new wowhead_spell();
				break;
			case 'stats':
				$object = new wowhead_stats();
				break;
			/*case 'talents':
				$object = new wowhead_talents();
				break;*/
			case 'title':
				$object = new wowhead_title();
				break;
			case 'zone':
				$object = new wowhead_zones();
				break;
			default:
				break;
		}

		$name = (sizeof($args) > 0) ? html_entity_decode($match[3], ENT_QUOTES) : html_entity_decode($match[2], ENT_QUOTES);

		// prevent any unwanted script execution or html formatting
		$name = trim(strip_tags($name));

		if (trim($name) == '')
		    $whp_message = str_replace($match[0], "<span class=\"notfound\">Illegal HTML/JavaScript found. Tags removed.</span>", $whp_message);
		else
		    $whp_message = str_replace($match[0], $object->parse($name, $args), $whp_message);
		$parses++;
		
		// clean things up
		$object->close(); unset($object);
	}
	unset($whp_config);
	return $whp_message;
}
?>
