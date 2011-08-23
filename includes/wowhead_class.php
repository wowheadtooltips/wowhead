<?php
// for translations
require_once(dirname(__FILE__) . '/translate/GTranslate.php');

/**
*
* @package Wowhead Tooltips
* @version wowhead_races.php 4.3.1
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/
class wowhead_class extends wowhead
{
	public $lang;
	public $patterns;
	public $config;
	public $language;
	
	private $translate;		// google translate wrapper
	private $gender = 1;	// default gender icon to use, 0 = male, 1 = female
	private $imageurl;		// image URL, pulled from the config
	private $class_ids = array(
		'warrior'		=>	1,
		'paladin'		=>	2,
		'hunter'		=>	3,
		'rogue'			=>	4,
		'priest'		=>	5,
		'deathknight'	=>	6,
		'shaman'		=>	7,
		'mage'			=>	8,
		'warlock'		=>	9,
		'druid'			=>	11
	);
	
	private $from = array(
		'en'	=>	'english',
		'es'	=>	'spanish',
		'fr'	=>	'french',
		'ru'	=>	'russian',
		'de'	=>	'german'
	);
	
	private $class_races = array(
		1	=>	array('bloodelf', 'draenei', 'dwarf', 'gnome', 'goblin', 'human', 'nightelf', 'orc', 'tauren', 'troll', 'undead', 'worgen'),	// warrior
		2	=>	array('bloodelf', 'draenei', 'dwarf', 'human', 'tauren'),																		// paladin
		3	=>	array('bloodelf', 'draenei', 'dwarf', 'goblin', 'hunter', 'nightelf', 'orc', 'tauren', 'troll', 'undead', 'worgen'),			// hunter
		4	=>	array('bloodelf', 'dwarf', 'gnome', 'goblin', 'human', 'nightelf', 'orc', 'troll', 'undead', 'worgen'),							// rogue
		5	=>	array('bloodelf', 'draenei', 'dwarf', 'gnome', 'goblin', 'human', 'nightelf', 'tauren', 'troll', 'undead', 'worgen'),			// priest
		6	=>	array('bloodelf', 'draenei', 'dwarf', 'gnome', 'goblin', 'human', 'nightelf', 'orc', 'tauren', 'troll', 'undead', 'worgen'),	// death knight
		7	=>	array('draenei', 'dwarf', 'goblin', 'orc', 'tauren', 'troll'),																	// shaman
		8	=>	array('bloodelf', 'draenei', 'dwarf', 'gnome', 'goblin', 'human', 'nightelf', 'orc', 'troll', 'undead', 'worgen'),				// mage
		9	=>	array('bloodelf', 'dwarf', 'gnome', 'goblin', 'human', 'orc', 'troll', 'undead', 'worgen'),										// warlock
		11	=>	array('nightelf', 'tauren', 'troll', 'worgen')																					// druid
	);
	
	private $races = array(
		'bloodelf'	=>	array(
			'name'	=>	'Blood Elf',
			'image'	=>	'10-%s.gif'
		),
		'draenei'	=>	array(
			'name'	=>	'Draenei',
			'image'	=>	'11-%s.gif'
		),
		'dwarf'		=>	array(
			'name'	=>	'Dwarf',
			'image'	=>	'3-%s.gif'
		),
		'gnome'		=>	array(
			'name'	=>	'Gnome',
			'image'	=>	'7-%s.gif'
		),
		'goblin'	=>	array(
			'name'	=>	'Goblin',
			'image'	=>	'9-%s.gif'
		),
		'human'		=>	array(
			'name'	=>	'Human',
			'image'	=>	'1-%s.gif'
		),
		'nightelf'	=>	array(
			'name'	=>	'Night Elf',
			'image'	=>	'4-%s.gif'
		),
		'orc'		=>	array(
			'name'	=>	'Orc',
			'image'	=>	'2-%s.gif'
		),
		'tauren'	=>	array(
			'name'	=>	'Tauren',
			'image'	=>	'6-%s.gif'
		),
		'troll'		=>	array(
			'name'	=>	'Troll',
			'image'	=>	'8-%s.gif'
		),
		'undead'	=>	array(
			'name'	=>	'Undead',
			'image'	=>	'5-%s.gif'
		),
		'worgen'	=>	array(
			'name'	=>	'Worgen',
			'image'	=>	'22-%s.gif'
		)
	);
	
	private $talentTrees = array(
		'deathknight'	=>	array('Blood', 'Frost', 'Unholy'),
		'druid'			=>	array('Balanced', 'Feral', 'Restoration'),
		'hunter'		=>	array('Beast Mastery', 'Marksmanship', 'Survival'),
		'mage'			=>	array('Arcane', 'Fire', 'Frost'),
		'paladin'		=>	array('Holy', 'Protection', 'Retribution'),
		'priest'		=>	array('Discipline', 'Holy', 'Shadow'),
		'rogue'			=>	array('Assassination', 'Combat', 'Subtlety'),
		'shaman'		=>	array('Elemental', 'Enhancement', 'Restoration'),
		'warlock'		=>	array('Affliction', 'Demonology', 'Destruction'),
		'warrior'		=>	array('Arms', 'Fury', 'Protection')	
	);
	
	// terms that will need to be translated
	private $words = array(
		// resources
		'Mana', 'Energy', 'Rage', 'Runes and Runic Power',
	
		// roles
		'Ranged DPS', 'Melee DPS', 'Tank', 'Healer',
		
		// classes
		'Warlock', 'Shaman', 'Rogue',
		'Mage', 'Druid', 'Death Knight',
		'Warrior', 'Paladin', 'Priest',
		'Hunter',
		
		// class specs
		'Blood', 'Frost', 'Unholy',
		'Balanced', 'Feral', 'Restoration',
		'Beast Mastery', 'Marksmanship', 'Survival',
		'Arcane', 'Fire', 'Frost',
		'Holy', 'Retribution', 'Protection',
		'Discipline', 'Shadow',
		'Assassination', 'Combat', 'Subtlety',
		'Elemental', 'Enhancement',
		'Affliction', 'Demonology', 'Destruction',
		'Arms', 'Fury',
		
		// races
		'Blood Elf', 'Draenei', 'Dwarf',
		'Gnome', 'Goblin', 'Human',
		'Night Elf', 'Orc', 'Tauren',
		'Troll', 'Undead', 'Worgen',
		
		// misc words
		'Resources', 'Specializations', 'Roles', 'Playable By Races'
	);

	public function __construct()
	{
		$this->patterns = new wowhead_patterns();
		$this->language = new wowhead_language();
		$this->config = new wowhead_config();
		$this->config->loadConfig();
		
		// initialize the translator wrapper and set the request type to curl
		$this->translate = new Gtranslate;
		$this->translate->setRequestType('curl');
		
		// set our custom vars
		$this->imageurl = $this->config->armory_image_url;
		$this->gender = ($this->config->race_gender == 'male') ? 0 : 1;		
	}
	
	public function close()
	{
		unset($this->lang, $this->language, $this->patterns, $this->config, $this->translate);	
	}
	
	public function parse($name, $args = array())
	{
		if (trim($name) == '')
			return false;
		
		// setup variables we'll use shortly
		$cache = new wowhead_cache();
		$this->lang = (!array_key_exists('lang', $args)) ? $this->config->lang : $args['lang'];
		$this->language->load($this->lang);
		
		if (!$result = $cache->getClass($name, $this->lang))
		{
			if (is_numeric($name))
				$result = $this->getClass($name);
			else
			{
				$tmp = strtolower(str_replace(' ', '', $name));
				if (!array_key_exists($tmp, $this->class_ids))
				{
					$cache->close();
					return $this->notFound('class', $name);
				}
				else
					$result = $this->getClass($this->class_ids[$tmp]);
			}
			
			if (!$result)
			{
				$cache->close();
				return $this->notFound('class', $name);
			}
			else
			{
				$result['search_name'] = $name;
				$cache->saveClass($result);
				$cache->close();
				return $this->generateOutput($result, 'class');
			}
		}
		else
		{
			$result['link'] = $this->generateLink($result['id'], 'class');
			$result['class'] = strtolower(str_replace(' ', '', $result['search_name']));
			$cache->close();
			return $this->generateOutput($result, 'class');
		}
	}
	
	private function getClass($id)
	{
		if (trim($id) == '' || !is_numeric($id))
			return false;
		
		$data = $this->readURL($id, 'class', false);
		if (!$data)
			return false;
			
		// first we'll pull the properly formatted class name
		$result = array();
		if (!preg_match('#<title>(.+?) - (.+?) - World of Warcraft</title>#s', $data, $match))
			return false;
		else
		{
			// build the initial result array
			$result = array(
				'id'	=>	$id,
				'name'	=>	$match[1],
				'class'	=>	strtolower(str_replace(' ', '', $match[1])),
				'icon'	=>	$this->imageurl . 'images/class/' . $id . '.gif',
				'lang'	=>	$this->lang
			);
		}
		
		// now we need to pull the information for the tooltip
		$facts = $this->quickFacts($data);
		if (!$facts || sizeof($facts) == 0)
			return false;
			
		// build the tooltip
		$tooltip = array(
			'name'		=>	$result['name'],
			'resource'	=>	$this->colorizeResources($facts[0]),
			'role'		=>	$facts[1],
			'races'		=>	$this->generateRaceHTML($id),
			'specs' 	=>	array(
				1	=>	array(
					'icon'	=>	$this->imageurl . 'images/talents/' . strtolower(str_replace(' ', '', $result['name'])) . '/1.gif',
					'name'	=>	$facts[2]	
				),
				2	=>	array(
					'icon'	=>	$this->imageurl . 'images/talents/' . strtolower(str_replace(' ', '', $result['name'])) . '/2.gif',
					'name'	=>	$facts[3]
				),
				3	=>	array(
					'icon'	=>	$this->imageurl . 'images/talents/' . strtolower(str_replace(' ', '', $result['name'])) . '/3.gif',
					'name'	=>	$facts[4]
				)
			)
		);
		$result['tooltip'] = $this->generateTooltip($this->patterns->pattern('class_tooltip'), $tooltip['name'], $tooltip['resource'], $tooltip['role'], $tooltip['specs'][1]['icon'], $tooltip['specs'][1]['name'], $tooltip['specs'][2]['icon'], $tooltip['specs'][2]['name'], $tooltip['specs'][3]['icon'], $tooltip['specs'][3]['name'], $tooltip['races']);
		$result['link'] = $this->generateLink($id, 'class');
		
		// translate the tooltip and class name, if necessary
		if ($this->lang != 'en')
		{
			$result['search_name'] = $result['name'];
			try
			{
				$from_lang = (array_key_exists($this->lang, $this->from)) ? $this->from[$this->lang] : 'german';
				$function = 'english_to_' . $from_lang;
				$result['name'] = $this->translate->$function($result['search_name']);	
				// trans
				foreach ($this->words as $word)
					$result['tooltip'] = str_replace($word, $this->translate->$function($word), $result['tooltip']);	
			}
			catch (GTranslateException $ge)
			{
				trigger_error($gt->getMessage(), E_USER_WARNING);
				return false;
			}	
		}
		
		return $result;
	}
	
	private function colorizeResources($in)
	{
		// colorize mana, rage, etc.
		$line = '&lt;span style=&quot;color:%s;&quot&gt;%s&lt;/span&gt;';
		$search = array('Mana', 'Rage', 'Energy', 'Runes', 'Runic Power');
		$replace = array(
			sprintf($line, '#0075FF', 'Mana'),	// #0000FF is too dark
			sprintf($line, '#FF0000', 'Rage'),
			sprintf($line, '#FDFF00', 'Energy'),
			sprintf($line, '#00FFFA', 'Runes'),
			sprintf($line, '#00FFFA', 'Runic Power')
		); 
		return str_replace($search, $replace, $in);
	}
	
	private function generateOutput($info)
	{
		$search = array('{icon}', '{name}', '{tooltip}', '{link}', '{class}');
		$replace = array($info['icon'], $info['name'], $info['tooltip'], $info['link'], $info['class']);
		$pattern = $this->patterns->pattern('class');
		$pattern = str_replace($search, $replace, $pattern);
		$pattern = str_replace(chr(10), '', $pattern);
		return $pattern;
	}
	
	private function generateRaceHTML($id)
	{
		$line = '&lt;div class=&quot;icontiny&quot; style=&quot;padding-bottom: 5px;background-image: url(%s);&quot;&gt;&nbsp;%s&lt;/div&gt;';
		$html = '';
		
		// pull the allowed races from our array
		$races = $this->class_races[$id];

		// loop through it and build the HTML
		foreach ($races as $race)
		{
			$image = $this->imageurl . 'images/race/' . sprintf($this->races[$race]['image'], $this->gender);
			$html .= sprintf($line, $image, $this->races[$race]['name']);
		}
		return $html;
	}
	
	private function quickFacts($data)
	{
		$parts = explode(chr(10), $data);
		foreach ($parts as $line)
		{
			if (strpos($line, 'Markup.printHtml("') !== false)
			{
				// found the line, now format it before sending it back
				$line = substr($line, strpos($line, '[ul]') + 4, (strpos($line, '[/ul]') - strpos($line, '[ul]')) - 4);
	
				// death knight has an added line, so strip that too
				$line = str_replace('[li][tooltip=tooltip_heroclass]Hero class[/tooltip][/li]', '', $line);
				$line = str_replace(' and ', ', ', $line);
				$line = str_replace('s ,', 's,', $line);
	
				// strip random BBCode wowhead puts in there
				$search = array(
					'#\[icon (.+?)\]#', '#\[\/icon\]#', // icons
					'#\[url=(.+?)\]#', '#\[\/url\]#', // urls
					'#\[span\]#', '#\[\/span\]#', // spans
					'#\[ul\]#', // random unordered list (for DK's)
					'#Resource([s]?): #', // mana, power, etc.
					'#Role([s]?): #', // tank, dps, etc.
					'#Specs: \[li\]#', // talent trees
					'#\[br\]#', // line breaks
					'#\[tooltip (.+?)\](.+?)\[\/tooltip\]#', // tooltips (for druids)
					'#\[span class=tip (.+?)([1,2,8]\])#' // spans (for druids, again)
				);
	
				$line = preg_replace($search, '', $line);
	
				// now strip the first [li] and last [/li]
				$line = substr($line, strpos($line, '[li]') + 4);
				$line = substr($line, 0, -5);
				// finally split the line by [/li][/li] into our beautifully formed array! =D
				return explode('[/li][li]', $line);
			}
		}
		return false;
	}
	
	private function generateTooltip($pattern)
	{
		$arg_list = func_get_args();
		$num_args = sizeof($arg_list);
		for ($i = 0; $i < $num_args; $i++)
			$pattern = str_replace('{' . $i . '}', $arg_list[$i], $pattern);
		return $pattern;
	}
}
?>
