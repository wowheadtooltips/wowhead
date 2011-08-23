<?php
/**
*
* @package Wowhead Tooltips
* @version install.php 4.0
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

/**
 * Installation Class
 * @package Wowhead Tooltips
 */
class install
{
	private $doneheader;
	private $openedform;
	public $steps = array();
	public $script = 'index.php';
	public $title = 'Wowhead Tooltips Installation Wizard';
	public $input = array();	// holds GET and POST data
	public $session = array();	// holds session data

	public $modules = array(	// names of the modules
		'achievement'	=>	'Achievement Module',	// achievement module
		'armory'		=>	'Armory Module',		// armory module (includes gearlist)
		'class'			=>	'Class Module',			// class module
		'craft'			=>	'Craftable Module',		// craftable module
		'currency'		=>	'Currency Module',		// currency module
		'enchant'		=>	'Enchant Module',		// enchant module
		'event'			=>	'Event Module',			// event module (world events)
		'faction'		=>	'Faction Module',		// faction module
		'guild'			=>	'Guild Module',			// guild module
		'item'			=>	'Item Module',			// item module
		'itemico'		=>	'Item Icon Module',		// item icon module
		'itemset'		=>	'Item Set Module',		// item set module
		'npc'			=>	'NPC Module',			// npc module
		'object'		=>	'Object Module',		// object module
		'pet'			=>	'Pet Module',			// pet module
		'prof'			=>	'Profession Module',	// profession module
		'profile'		=>	'Profile Module',		// profile module
		'quest'			=>	'Quest Module',			// quest module
		'race'			=>	'Race Module',			// race module
		'recruit'		=>	'Recruit Module',		// recruit module
		'spell'			=>	'Spell Module',			// spell module
		'stats'			=>	'Statistics Module',	// statistics module
		//'talents'		=>	'Talents Module',		// talents module
		'title'			=>	'Title Module',			// title module
		'zone'			=>	'Zone Module'			// zone module
	);
	
	public $tables = array(		// sql table names
		'achievement',
		'armory',
		'class',
		'config',
		'craftable',
		'craftable_reagent',
		'craftable_spell',
		'currency',
		'enchant',
		'enchant_reagent',
		'event',
		'faction',
		'gearlist',
		'guild',
		'item',
		'itemico',
		'itemset',
		'itemset_reagent',
		'log',
		'npc',
		'object',
		'pet',
		'quest',
		'race',
		'recruit',
		'rss',
		'spell',
		'stats',
		//'talents',
		'title',
		'talent_names',
		'zones'
	);
	
	public function __construct()
	{
		// pull the data from GET and POST
		$this->parse_incoming($_GET);
		$this->parse_incoming($_POST);
		$this->parse_sessions();
	}
	
	public function print_header($title = "Welcome", $image = "welcome", $form = 1, $error = 0)
	{
		global $language;
		if($language->title)
		{
			$this->title = $language->title;
		}

		@header("Content-type: text/html; charset=utf-8");

		$this->doneheader = 1;

		echo <<<END
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>$this->title &gt; $title</title>
	<link rel="stylesheet" href="./css/stylesheet.css" type="text/css" />
</head>
<body>
<br /><br />
END;
		if($form)
		{
			echo "\n	<form method=\"post\" action=\"" . $this->script . "\">\n";
			$this->openedform = 1;
		}
		
		echo <<<END
		<div id="container">
		<div id="inner_container">
		<div id="header">$this->title</div>
END;
		if(empty($this->steps))
		{
			$this->steps = array();
		}
		
		if(is_array($this->steps))
		{
			echo "\n		<div id=\"progress\">";
			echo "\n			<ul>\n";
			foreach($this->steps as $action => $step)
			{
				if($action == $this->input['action'])
					echo "				<li class=\"active\"><strong>$step</strong></li>\n";
				else
					echo "				<li>$step</li>\n";
			}
			echo "			</ul>";
			echo "\n		</div>";
			echo "\n		<div id=\"content\">\n";
		}
		else
		{
			echo "\n		<div id=\"progress_error\"></div>";
			echo "\n		<div id=\"content_error\">\n";
		}
		if($title != "")
		{
			echo <<<END
				<h2 class="$image">$title</h2>\n
END;
		}
	}

	public function print_contents($contents)
	{
		echo $contents;
	}

	public function print_error($message)
	{
		global $language;
		if(!$this->doneheader)
		{
			$this->print_header($language->error, "errormsg", 0, 1);
		}
		echo "			<div class=\"error\">\n				";
		echo "<h3>".$language->error."</h3>";
		$this->print_contents($message);
		echo "\n			</div>";
		$this->print_footer();
	}


	function print_footer($nextact = "")
	{
		global $language, $footer_extra;
		if($nextact && $this->openedform)
		{
			echo "\n			<input type=\"hidden\" name=\"action\" value=\"$nextact\" />";
			echo "\n				<div id=\"next_button\"><input type=\"submit\" class=\"submit_button\" value=\"".$language->next." &raquo;\" /></div><br style=\"clear: both;\" />\n";
			$formend = "</form>";
		}
		else
		{
			$formend = "";
		}

		echo <<<END
		</div>
		<div id="footer">
END;

		$copyyear = date('Y');
		echo <<<END
			<div id="copyright">
				<a href="http://wowhead-tooltips.com">Wowhead Tooltips</a> &copy; 2008-$copyyear <a href="mailto:support@wowhead-tooltips.com">Adam Koch</a>.  Wowhead Name &copy; 2010 <a href="http://wowhead.com">Wowhead</a>/<a href="http://zam.com">Zam</a>, used with permission.
			</div>
		</div>
		</div>
		</div>
		$formend
		$footer_extra
</body>
</html>
END;
		exit;
	}
	
	private function parse_incoming($array)
	{
		if(!is_array($array))
		{
			return;
		}

		foreach($array as $key => $val)
		{
			$this->input[$key] = $val;
		}
	}
	
	private function parse_sessions()
	{
		if (sizeof($_SESSION) > 0)
		{
			foreach ($_SESSION as $key => $value)
			{
				if (!array_key_exists($key, $this->session))
					$this->session[strtolower($key)] = $value;	
			}
		}
	}
	
	public function get_location()
	{
		// we'll attempt to determine the script's url
		$pageURL = 'http';
		if (array_key_exists('HTTPS', $_SERVER) && $_SERVER["HTTPS"] == "on")
			$pageURL .= "s";
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80")
		{
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} 
		else
		{
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		
		// remove index.php and install
		$pageURL = substr($pageURL, 0, strrpos($pageURL, '/'));
		$pageURL = substr($pageURL, 0, strrpos($pageURL, '/') + 1);
		
		return $pageURL;	
	}
	
	public function add_to_session($array)
	{
		foreach ($array as $key => $value)
		{
			if (trim($key) != '' && !array_key_exists($key, $_SESSION))
				$_SESSION[strtolower($key)] = addslashes($value);
		}
		// update the sessions array
		$this->parse_sessions();
	}
}
?>
