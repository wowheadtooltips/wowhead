<?php
/**
*
* @package Wowhead Tooltips
* @version functions.php 4.0
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

/**
 * Cleans HTML for output to Wowhead
 * @access public
 * @param string $string
 * @return string
 */
function cleanHTML($string)
{
    if (function_exists("mb_convert_encoding"))
        $string = mb_convert_encoding($string, "UTF-8", "HTML-ENTITIES");
    else
    {
       $conv_table = get_html_translation_table(HTML_ENTITIES);
       $conv_table = array_flip($conv_table);
       $string = strtr ($string, $conv_table);
       $string = preg_replace('/&#(\d+);/me', "chr('\\1')", $string);
    }
    return ($string);
}

/**
 * Converts string to UTF-8
 * @access public
 * @param string $str
 * @return string
 */
function convertString($str)
{
	// convert to utf8, if necessary
	if (!is_utf8($str))
	{
		$str = utf8_encode($str);
	}

	// clean up the html
	$str = cleanHTML($str);

	// return the url encoded string
	return urlencode($str);
}

/**
 * Determines if string is UTF-8
 * @access public
 * @param string $string
 * @return bool
 */
function is_utf8($string) {
	// From http://w3.org/International/questions/qa-forms-utf-8.html
	return (preg_match('%^(?:
		[\x09\x0A\x0D\x20-\x7E]            # ASCII
		| [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
		|  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
		| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
		|  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
		|  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
		| [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
		|  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
	)*$%xs', $string)) ? true : false;
}

/**
 * Generates Unique Key for MySQL
 * @access public
 * @param string $name
 * @param string $realm
 * @param string $region
 * @return string
 */
function generateKey($name, $realm, $region)
{
	$name = strtolower(str_replace(' ', '', $name));
	$realm = strtolower(str_replace(' ', '', $realm));
	$region = strtolower($region);
	return md5($name . $realm . $region);
}

/**
 * Attempts to Pull XML Data From Armory
 * @access public
 * @param string $uri
 * @return string
 */
function readURL($uri)
{
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_URL, $uri);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0);
	$html_data = curl_exec($curl);
	if (!$html_data)
	{ 
		return false; 
	}
	curl_close($curl);
	return $html_data;	
}

/**
 * Get XML From Armory
 * @access public
 * @param string $url
 * @param string $language [optional]
 * @return string
 */
function getXML($url)
{
	$timeout = 5;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.2) Gecko/20070219 Firefox/2.0.0.2");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
	curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
	curl_setopt($ch, CURLOPT_LOW_SPEED_LIMIT, 5);
	curl_setopt($ch, CURLOPT_LOW_SPEED_TIME, $timeout);
	curl_setopt($ch, CURLOPT_TIMEVALUE, $timeout * 3);

	$f = curl_exec($ch);
	curl_close($ch);
	return $f;
	/*
	$retries = 5;
	$lastdownload = 0;
	$timeout = 5;

	for($i = 1; $i <= $retries; $i++)
	{
		if (time() < $lastdownload + 1)
		{
			$delay = rand(1, 2);
			sleep($delay);    //random delay
		}
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.2) Gecko/20070219 Firefox/2.0.0.2");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($ch, CURLOPT_LOW_SPEED_LIMIT, 5);
		curl_setopt($ch, CURLOPT_LOW_SPEED_TIME, $timeout);
		curl_setopt($ch, CURLOPT_TIMEVALUE, $timeout * 3);

		$f = curl_exec($ch);
		$lastdownload = time();
		curl_close($ch);

		if (strpos($f, 'errCode="noCharacter"') !== false)
		{
			return $f;
			break;
		}
		if (strpos($f, 'errorhtml') !== false && $i <= $retries - 1)
			continue;
		else
		{
			return $f;
			break;
		}
	}
	*/
}

/**
 * Generates Armory Character URL
 * @access public
 * @param string $name
 * @param string $region
 * @param string $realm
 * @return string
 */
function characterURL($name, $region, $realm)
{
	return armoryURL($region) . 'character-sheet.xml?r=' . str_replace(' ', '+', $realm) . '&cn=' . $name;
}

/**
 * Generates Armory URL
 * @access public
 * @param string $region
 * @return string
 */
function armoryURL($region)
{
	$prefix = ($region == 'us') ? 'www' : $region;
	return "http://{$prefix}.wowarmory.com/";
}

/**
 * Generates Armory Reputation URL
 * @access public
 * @param string $name
 * @param string $region
 * @param string $realm
 * @return string
 */
function reputationURL($name, $region, $realm)
{
	return armoryURL($region) . 'character-reputation.xml?r=' . str_replace(' ', '+', $realm) . '&cn=' . $name;	
}

/**
 * Generates Armory Achievement URL
 * @access public
 * @param string $name
 * @param string $region
 * @param string $realm
 * @return string
 */
function achievementURL($name, $region, $realm)
{
	return armoryURL($region) . 'character-achievements.xml?r=' . str_replace(' ', '+', $realm) . '&cn=' . $name . '&c=168';	
}

/**
 * Generates Armory Talents URL
 * @access public
 * @param string $name
 * @param string $region
 * @param string $realm
 * @return string
 */
function talentsURL($name, $region, $realm)
{
	return armoryURL($region) . 'character-talents.xml?r=' . str_replace(' ', '+', $realm) . '&cn=' . $name;	
}

/**
 * Generates Armory RSS URL
 * @access public
 * @param string $name
 * @param string $region
 * @param string $realm
 * @return string
 */
function rssURL($name, $region, $realm)
{
	$realm = (strpos($realm, ' ')) ? str_replace(' ', '+', $realm) : $realm;	// replace spaces with (+)
	return armoryURL($region) . "character-feed.atom?r={$realm}&cn={$name}&locale=" . getLocale($region);	
}

/**
 * Generates Locale Based on Language
 * @access public
 * @param string $region
 * @return string
 */
function getLocale($region)
{
	return ($region == 'en') ? 'en_US' : strtolower($region) . '_' . strtoupper($region);
}

/**
 * Build Factions Array
 * @access public
 * @param array $wrath
 * @return array
 */
function buildFactions($cata)
{
	$factions = array();
	
	foreach ($cata->faction as $fact)
	{
		$factions[(string)$fact['name']][] = array(
			'name'	=>	(string)$fact['name'],
			'rep'	=>	(string)$fact['reputation']
		);
	}
	sort($factions);
	return $factions;
}

/**
 * Calculates Reputation Standing
 * @access public
 * @param int $val
 * @return array
 */
function calculateStanding($val)
{
	global $language;
	
	// make sure it's an integer
	$val = (int)$val;
	
	// hated (42000 + value)
	if ($val >= -42000 && $val <= -6001)
	{
		$value = 42000 + $val;
		return array(
			'word'		=>	$language->words['hated'],
			'max'		=>	'36000',
			'value'		=>	$value,
			'class'		=>	'hated'
		);
	}
	// hostile (6000 + value)
	elseif ($val >= -6000 && $val <= -3001)
	{
		$value = 6000 + $val;
		return array(
			'word'		=>	$language->words['hostile'],
			'max'		=>	'3000',
			'value'		=>	$value,
			'class'		=>	'hostile'
		);		
	}
	// unfriendly (3000 + value)
	elseif ($val >= -3000 && $val <= -1)
	{
		$value = 3000 + $val;
		return array(
			'word'		=>	$language->words['unfriendly'],
			'max'		=>	'3000',
			'value'		=>	$value,
			'class'		=>	'unfriendly'
		);
	}
	// neutral (value)
	elseif ($val >= 0 && $val <= 2999)
	{
		return array(
			'word'		=>	$language->words['neutral'],
			'max'		=>	'3000',
			'value'		=>	$val,
			'class'		=>	'neutral'
		);
	}
	// friendly (value - 3000)
	elseif ($val >= 3000 && $val <= 8999)
	{
		$value = $val - 3000;
		return array(
			'word'		=>	$language->words['friendly'],
			'max'		=>	'6000',
			'value'		=>	$value,
			'class'		=>	'friendly'
		);
	}
	// honored (value - 9000)
	elseif ($val >= 9000 && $val <= 20999)
	{
		$value = $val - 9000;
		return array(
			'word'		=>	$language->words['honored'],
			'max'		=>	'12000',
			'value'		=>	$value,
			'class'		=>	'honored'
		);
	}
	// revered (value - 21000)
	elseif ($val >= 21000 && $val <= 41999)
	{
		$value = $val - 21000;
		return array(
			'word'		=>	$language->words['revered'],
			'max'		=>	'21000',
			'value'		=>	$value,
			'class'		=>	'revered'
		);
	}
	// exalted (value - 42000)
	elseif ($val >= 42000 && $val <= 42999)
	{
		$value = $val - 42000;
		return array(
			'word'		=>	$language->words['exalted'],
			'max'		=>	'1000',
			'value'		=>	$value,
			'class'		=>	'exalted'
		);
	}
}

/**
 * Calculates Percentage
 * @access public
 * @param int $first
 * @param int $second
 * @return int
 */
function percentage($first, $second)
{
	return number_format((($first / $second) * 100), 0);	
}

/**
 * Gets Achievement Icon from Wowhead
 * @access public
 * @param int $id
 * @return icon
 */
function getAchievementIcon($id)
{
	global $wowhead_url;
	
	$data = getXML($wowhead_url . 'achievement=' . $id . '&power');
	
	if (preg_match('#icon: \'(.+?)\'#', $data, $match))
	{
		// icon found
		return 'http://static.wowhead.com/images/wow/icons/tiny/' . strtolower($match[1]) . '.gif';	
	}
	else
	{
		return false;	
	}
}

/**
 * Get Talent Images Based on Class
 * @access private
 * @param string $class
 * @param int $one
 * @param int $two
 * @param int $three
 * @return string
 */
function getTalentImage($class, $one, $two, $three)
{
	global $config;
	
	$val = max($one, $two, $three);
	
	if ($val == $one)
		return $config->armory_image_url . 'images/talents/' . $class . '/1.gif';
	elseif ($val == $two)
		return $config->armory_image_url . 'images/talents/' . $class . '/2.gif';
	else
		return $config->armory_image_url . 'images/talents/' . $class . '/3.gif';
}

function stripHeaders($data)
{
	// split the string
	$chunks = explode(chr(10), $data);

	// return the last index in the array, aka our xml
	return $chunks[sizeof($chunks) - 1];
}

function getDomain($lang)
{
	return ($lang == 'en') ? 'http://www.wowhead.com/' : 'http://' . $lang . '.wowhead.com/';
}

function getRewardLine($data)
{
	$lines = explode(chr(10), $data);
	
	foreach ($lines as $line)
	{
		if (strpos($line, "new Listview({template: 'item', id: 'items',") !== false)
		{
			return $line;
			break;
		}
	}
	
	return false;
}

function rewardsFound($items)
{
	$found = false;
	foreach ($items as $standing)
	{
		if (sizeof($standing) > 0)
			$found = true;	
	}
	return $found;
}

/**
* Blizzard to Wowhead converter
*
* Converts Blizzard's talent string and class id into Wowhead's (shorter) talent string
*
* @author Gizzmo <justgiz@gmail.com>
*
* @param string|int $class Blizzard's class id or class name
* @param string $build Blizzard's build string
* @param boolean $to_url return a Wowhead talent URL
* @return string Wowhead's talent string
* @return string Wowhead's talent url
*/
function blizzardToWowhead($class, $build, $to_url = false)
{
	// for language support
	$warrior = 'warrior';
	$paladin = 'paladin';
	$hunter = 'hunter';
	$rogue = 'rogue';
	$priest = 'priest';
	$death_knight = 'death knight';
	$shaman = 'shaman';
	$mage = 'mage';
	$warlock = 'warlock';
	$druid = 'druid';

	// blizzard and wowhead have different order for the classes witch result in different ids
	$blizzard_classes = array(null, $warrior, $paladin, $hunter, $rogue, $priest, $death_knight, $shaman, $mage, $warlock, null, $druid);
	$wowhead_classes = array($druid, $hunter, $mage, $paladin, $priest, $rogue, $shaman, $warlock, $warrior, $death_knight);

	// tree count order by wowhead order
	$tree_counts = array(
		array(20, 22, 21),	// druid
		array(19, 19, 20),	// hunter
		array(21, 21, 19),	// mage
		array(20, 20, 20),	// paladin
		array(21, 20, 21),	// priest
		array(19, 19, 19),	// rogue
		array(19, 19, 19),	// shaman
		array(18, 19, 19),	// warlock
		array(20, 21, 20),	// warrior
		array(20, 20, 19)	// death knight
	);

	// the string of which wowhead uses to encode their string
	$encrypt_string = '0zMcmVokRsaqbdrfwihuGINALpTjnyxtgevElBCDFHJKOPQSUWXYZ123456789Z';

	// clean up the class
	$class = trim($class);

	// we are assuming the id number privided is the blizzard class id
	if (is_numeric($class))
		$class = $blizzard_classes[$class];

	// now just convert the name to class id
	foreach ($wowhead_classes as $i => $wowhead_class)
	{
		if (strtolower($class) == strtolower($wowhead_class))
		{
			$class_id = $i;
			break;
		}
	}

	// check to make sure the class id is set
	if (!isset($class_id))
		trigger_error('The provided class is invalid.', E_USER_ERROR);
	// check to make sure the build string is the correct length
	if (strlen($build) != array_sum($tree_counts[$class_id]))
	{
		if (strtolower($class) == 'druid')
			$build = substr($build, 0, ((int)$tree_counts[$class_id][0] + (int)$tree_counts[$class_id][1] + (int)$tree_counts[$class_id][2]) - 1);
		else
			trigger_error('The provided build string is not the correct length for the provided class.', E_USER_ERROR);
	}
	
	// now break the build string down into the different trees
	$build_trees = array(
		substr($build, 0, $tree_counts[$class_id][0]),
		substr($build, $tree_counts[$class_id][0], $tree_counts[$class_id][1]),
		substr($build, $tree_counts[$class_id][0] + $tree_counts[$class_id][1], $tree_counts[$class_id][2])
	);

	// start the string with the class
	$string = $encrypt_string[$class_id*3];

	// loop though each tree to encode it
	foreach ($build_trees as $cur_tree)
	{
		$etree = '';
		$b = rtrim($cur_tree, '0');
		for ($i=0; $i<strlen($b); $i++)
		{
			$tens = intval($b[$i]);
			$ones = (++$i == strlen($b))? 0: intval($b[$i]);
			$etree .= $encrypt_string[$tens*6 + $ones];
		}

		$string .= (strlen($b)==strlen($cur_tree))? $etree: $etree.$encrypt_string[62];
	}

	// remove any extra Z's
	$string = rtrim($string, $encrypt_string[62]);

	// return the wowhead talent url?
	if ($to_url)
		$string = 'http://www.wowhead.com/talent#'.$string;

	return $string;
}

function getTalentIcon($id)
{
	if (trim($id) == '' || !is_numeric($id))
		return '';
	$data = readURL('http://www.wowhead.com/?spell=' . $id . '&power');	
	if (!$data)
		return false;
	elseif (!preg_match('#icon: \'(.+?)\',#s', $data, $match))
		return false;
	else
		return 'http://static.wowhead.com/images/wow/icons/large/' . strtolower($match[1]) . '.jpg';
}

if (!function_exists('bcdiv'))
{
	function bcdiv($first, $second, $scale = 0)
	{
		$res = $first / $second;
		return round($res, $scale);
	}
}

?>
