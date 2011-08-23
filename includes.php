<?php
/**
*
* @package Wowhead Tooltips
* @version includes.php 4.3
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/
if (!defined('WOWHEAD_ROOT'))
	define('WOWHEAD_ROOT', dirname(__FILE__) . '/');
if (!defined('WOWHEAD_INCLUDES'))
	define('WOWHEAD_INCLUDES', WOWHEAD_ROOT . 'includes/');

require_once(WOWHEAD_ROOT . 'config.php');
require_once(WOWHEAD_INCLUDES . 'wowhead_config.php');

// function which will require the necessary classes, depending on module
function require_dependencies($module)
{
	if ($module == 'zone')		// zone module is named different
		$module .= 's';
	elseif ($module == 'prof')	// as is profession
		$module .= 'ession';
	$classes = array(
		'achievement'	=>	array('patterns', 'language', 'cache'),
		'armory'		=>	array('patterns', 'language', 'cache'),				
		'class'			=>	array('patterns', 'language', 'cache'),
		'craft'			=>	array('patterns', 'language', 'cache'),
		'currency'		=>	array('patterns', 'language', 'cache'),
		'enchant'		=>	array('patterns', 'language', 'cache'),
		'event'			=>	array('patterns', 'language', 'cache'),
		'faction'		=>	array('patterns', 'language', 'cache'),
		'guild'			=>	array('patterns', 'language', 'cache'),
		'item'			=>	array('patterns', 'language', 'cache'),
		'itemico'		=>	array('patterns', 'language', 'cache'),
		'itemset'		=>	array('patterns', 'language', 'cache'),
		'npc'			=>	array('patterns', 'language', 'cache'),
		'object'		=>	array('patterns', 'language', 'cache'),
		'pet'			=>	array('patterns', 'language', 'cache'),
		'profession'	=>	array('patterns', 'language', 'cache'),
		'profile'		=>	array('patterns', 'language', 'cache'),
		'quest'			=>	array('patterns', 'language', 'cache'),
		'race'			=>	array('patterns', 'language', 'cache'),
		'recruit'		=>	array('patterns', 'language', 'cache', 'armory'),
		'spell'			=>	array('patterns', 'language', 'cache'),
		'stats'			=>	array('patterns', 'language', 'cache'),
		//'talents'		=>	array('patterns', 'language', 'cache'),
		'title'			=>	array('patterns', 'language', 'cache'),
		'zones'			=>	array('patterns', 'language', 'cache')
	);
	
	if (!array_key_exists($module, $classes))
		return false;
	
	require_once(WOWHEAD_INCLUDES . 'wowhead.php');	// every module needs the base class
	
	// loop through the required dependencies and require them
	foreach ($classes[$module] as $class)
	{
		if (file_exists(WOWHEAD_INCLUDES . "wowhead_{$class}.php"))
			require_once(WOWHEAD_INCLUDES . "wowhead_{$class}.php");
		else
			echo "<tt>" . WOWHEAD_INCLUDES . "wowhead_{$class}.php</tt> does not exist.<br />";	
	}
	require_once(WOWHEAD_INCLUDES . "wowhead_{$module}.php");
}

?>
