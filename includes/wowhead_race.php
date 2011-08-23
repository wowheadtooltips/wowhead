<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_races.php 4.4
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/
class wowhead_race extends wowhead
{
	public $lang;
	public $patterns;
	public $config;
	public $language;
	
	// horde or alliance
	private $sides = array(
		1	=>	'Alliance',
		2	=>	'Horde'
	);
	
	// class combinations, colors, and IDs
	private $class_ids = array (
		'Warrior'		=>	'1.gif',
		'Paladin'		=>	'2.gif',
		'Hunter'		=>	'3.gif',
		'Rogue'			=>	'4.gif',
		'Priest'		=>	'5.gif',
		'Death Knight'	=>	'6.gif',
		'Shaman'		=>	'7.gif',
		'Mage'			=>	'8.gif',
		'Warlock'		=>	'9.gif',
		'Druid'			=>	'11.gif',
	);
	private $class_colors = array(
		'Death Knight'	=>	'#C41E3B',	// dark red
		'Druid'			=>	'#FF7C0A',	// orange
		'Hunter'		=>	'#AAD372',	// light green
		'Mage'			=>	'#68CCEF',	// light blue
		'Paladin'		=>	'#F48CBA',	// pink
		'Priest'		=>	'#FFFFFF',	// white
		'Rogue'			=>	'#FFF468',	// gold
		'Shaman'		=>	'#2359FF',	// blue
		'Warlock'		=>	'#9382C9',	// light purple
		'Warrior'		=>	'#C69B6D',	// brown
	);
	private $classes = array(
		447		=>	array('Death Knight', 'Hunter', 'Mage', 'Paladin', 'Priest', 'Rogue', 'Warlock', 'Warrior'),			// human
		493		=>	array('Death Knight', 'Hunter', 'Mage', 'Rogue', 'Shaman', 'Warlock', 'Warrior'),						// orc 
		511		=>	array('Death Knight', 'Hunter', 'Mage', 'Paladin', 'Priest', 'Rogue', 'Shaman', 'Warlock', 'Warrior'),	// dwarf
		1213	=>	array('Death Knight', 'Druid', 'Hunter', 'Mage', 'Priest', 'Rogue', 'Warrior'),							// night elf
		445		=>	array('Death Knight', 'Hunter', 'Mage', 'Priest', 'Rogue', 'Warlock', 'Warrior'),						// undead
		1143	=>	array('Death Knight', 'Druid', 'Hunter', 'Paladin', 'Priest', 'Shaman', 'Warrior'),						// tauren
		441		=>	array('Death Knight', 'Mage', 'Priest', 'Rogue', 'Warlock', 'Warrior'),									// gnome
		1533	=>	array('Death Knight', 'Druid', 'Hunter', 'Mage', 'Priest', 'Rogue', 'Shaman', 'Warlock', 'Warrior'),	// troll
		509		=>	array('Death Knight', 'Hunter', 'Mage', 'Priest', 'Rogue', 'Shaman', 'Warlock', 'Warrior'),				// goblin
		447		=>	array('Death Knight', 'Hunter', 'Mage', 'Paladin', 'Priest', 'Rogue', 'Warlock', 'Warrior'),			// blood elf
		247		=>	array('Death Knight', 'Hunter', 'Mage', 'Paladin', 'Priest', 'Shaman', 'Warrior'),						// draenei
		1469	=>	array('Death Knight', 'Druid', 'Hunter', 'Mage', 'Priest', 'Rogue', 'Warlock', 'Warrior')				// worgen
	);
	
	public function __construct()
	{
		$this->patterns = new wowhead_patterns();
		$this->language = new wowhead_language();
		$this->config = new wowhead_config();
		$this->config->loadConfig();
	}

	public function close()
	{
		unset($this->lang, $this->language, $this->patterns, $this->config);	
	}
	
	public function parse($name, $args = array())
	{
		if (trim($name) == '')
			return false;
		
		// setup variables we'll use shortly
		$cache = new wowhead_cache();
		$this->lang = (!array_key_exists('lang', $args)) ? $this->config->lang : $args['lang'];
		$this->language->load($this->lang);
		
		if (!$result = $cache->getRace($name, $this->lang))
		{
			$result = (is_numeric($name)) ? $this->getRaceByID($name) : $this->getRaceByName($name);
			if (!$result)
			{
				$cache->close();
				return $this->notFound($this->language->words['race'], $name);
			}
			else
			{
				$cache->saveRace($result);
				$cache->close();
				return $this->generateOutput($result, 'race');
			}
		}
		else
		{
			$result['link'] = $this->generateLink($result['id'], 'race');
			$cache->close();
			return $this->generateOutput($result, 'race');
		}
	}
	
	private function getRaceByName($name)
	{
		if (trim($name) == '')
			return false;
		$data = $this->readURL($name, 'race', false);
		if (!$data)
			return false;
		
		$line = $this->raceLine($data);
		if (!$line)
			return false;
		if (!$json = json_decode($line, true))
			return false;
		else
		{
			foreach ($json as $race)
			{
				if (strtolower(stripslashes($race['name'])) == strtolower(stripslashes($name)))
				{	
					// build the initial result array
					$result = array(
						'id'			=>	$race['id'],
						'name'			=>	$race['name'],
						'search_name'	=>	$name,
						'lang'			=>	$this->lang
					);
					
					// now get the tooltip information
					$info = $this->getTooltipInfo($race['id'], $race['classes']);
					if (!$info)
						return false;
					
					// and then generate the rest
					$result['tooltip'] = $this->generateTooltip($this->patterns->pattern('race_tooltip'), $result['name'], $info['side'], $info['faction'], $info['zone'], $info['leader'], $info['racials'], $info['classes']);
					$result['link'] = $this->generateLink($result['id'], 'race');
					$result['tipicon'] = 'race_' . strtolower(str_replace(' ', '', $result['name'])) . '_' . $this->config->race_gender;
					$result['icon'] = 'race_' . strtolower(str_replace(' ', '', $result['name'])) . '_' . $this->config->race_gender;
					return $result;
				}
			}
			return false;
		}
	}
	
	private function getRaceByID($id)
	{
		if (trim($id) == '' || !is_numeric($id))
			return false;
		$data = $this->readURL($id, 'race', false);
		if (!$data)
			return false;
		$line = $this->raceLine($data);
		if (!$line)
			return false;
		if (!$json = json_decode($line, true))
			return false;
		else
		{
			var_dump($json); die;
			foreach ($json as $race)
			{
				if ($race['id'] == $id)
				{
					// build the initial result array
					$result = array(
						'id'			=>	$race['id'],
						'name'			=>	$race['name'],
						'search_name'	=>	$id,
						'lang'			=>	$this->lang
					);
					
					// now get the tooltip information
					$info = $this->getTooltipInfo($race['id'], $race['classes']);
					if (!$info)
						return false;
					
					// and then generate the rest
					$result['tooltip'] = $this->generateTooltip($this->patterns->pattern('race_tooltip'), $result['name'], $info['side'], $info['faction'], $info['zone'], $info['leader'], $info['racials'], $info['classes']);
					$result['link'] = $this->generateLink($result['id'], 'race');
					if (strtolower($result['name']) == 'undead')
					{
						$result['tipicon'] = 'race_scourge_' . $this->config->race_gender;
						$result['icon'] = 'race_scourge_' . $this->config->race_gender;
					}
					else
					{
						$result['tipicon'] = 'race_' . strtolower(str_replace(' ', '', $result['name'])) . '_' . $this->config->race_gender;
						$result['icon'] = 'race_' . strtolower(str_replace(' ', '', $result['name'])) . '_' . $this->config->race_gender;
					}
					return $result;
				}
			}
			return false;
		}		
	}
	
	private function getTooltipInfo($id, $class)
	{
		$info = array(); $icon_html = '&lt;div class=&quot;icontiny&quot; style=&quot;padding-bottom: 5px;background-image: url(%s);color: %s !important;&quot;&gt;&nbsp;%s&lt;/div&gt;';
		$data = $this->readURL($id, 'race_query', false);
		if (!$data)
			return false;
		elseif (!$info_line = $this->quickInfoLine($data))
			return false;
		elseif (!$racial_line = $this->racialLine($data))
			return false;
		else
		{
			// horde or alliance?
			if (!preg_match('#\[span class=(.+?)-icon\](.+?)\[\/span\]#s', $info_line, $match))
				return false;
			else
				$info['side'] = stripslashes($match[2]);
				
			// faction
			if (!preg_match('#\[url=\/faction=(.+?)\](.+?)\[\/url\]#s', $info_line, $match))
				return false;
			else
				$info['faction'] = stripslashes($match[2]);
			
			// faction leader
			if (!preg_match('#\[url=\/npc=(.+?)\](.+?)\[\/url\]#s', $info_line, $match))
				return false;
			else
				$info['leader'] = stripslashes($match[2]);
			
			// starting zone
			if (!preg_match('#\[url=\/zone=(.+?)\](.+?)\[\/url\]#s', $info_line, $match))
				return false;
			else
				$info['zone'] = stripslashes($match[2]);
			
			// grab the racials
			if (!$json = json_decode($racial_line, true))
				return false;
			else
			{
				$info['racials'] = '';
				foreach ($json as $racial)
				{
					$racial['name'] = substr($racial['name'], 1);
					// gotta get the icon from the spell's wowhead page
					$racial_data = $this->readURL($racial['id'], 'spell', false);
					if ($racial_data != '$WowheadPower.registerSpell' && preg_match('#icon: \'(.+?)\',#s', $racial_data, $match))
						$info['racials'] .= sprintf($icon_html, 'http://static.wowhead.com/images/wow/icons/tiny/' . strtolower($match[1]) . '.gif', '#FFFFFF', $racial['name']);
				}
				
				if ($info['racials'] == '')
					return false;
			}
			
			// finally generate the classes
			foreach ($this->classes[$class] as $cname)
				$info['classes'] .= sprintf($icon_html, $this->config->armory_image_url . 'images/class/' . $this->class_ids[$cname], $this->class_colors[$cname], $cname);
				
			if ($info['classes'] == '')
				return false;
			else
				return $info;
		}
	}

	private function generateOutput($info)
	{
		$search = array('{name}', '{icon}', '{tipicon}', '{tooltip}', '{link}');
		$replace = array($info['name'], $info['icon'], $info['tipicon'], $info['tooltip'], $info['link']);
		$pattern = $this->patterns->pattern('race');
		$pattern = str_replace($search, $replace, $pattern);
		$pattern = str_replace(chr(10), '', $pattern);
		return $pattern;
	}
	
	private function generateTooltip($pattern)
	{
		$arg_list = func_get_args();
		$num_args = sizeof($arg_list);
		for ($i = 0; $i < $num_args; $i++)
			$pattern = str_replace('{' . $i . '}', $arg_list[$i], $pattern);
		return $pattern;
	}
	
	private function quickInfoLine($data)
	{
		$parts = explode(chr(10), $data);
		foreach ($parts as $line)
		{
			if (strpos($line, 'Markup.printHtml("') !== false)
				return $line;
		}
		return false;
	}
	
	private function racialLine($data)
	{
		$parts = explode(chr(10), $data);
		foreach ($parts as $line)
		{
			if (strpos($line, "new Listview({template: 'spell', id: 'racial-traits',") !== false)
			{
				// format it for valid JSON
				$line = substr($line, strpos($line, 'data: [{') + 6);
				$line = str_replace('});', '', $line);
				return $line;
			}
		}
		return false;
	}
	
	private function raceLine($data)
	{
		$parts = explode(chr(10), $data);
		foreach ($parts as $line)
		{
			if (strpos($line, "new Listview({template: 'race', id: 'races',") !== false)
			{
				// format it for valid JSON
				$line = substr($line, strpos($line, 'data: [{') + 6);
				$line = str_replace('});', '', $line);
				return $line;
			}
		}
		return false;
	}
}
?>
