<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_profession.php 4.4
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

/**
 * Wowhead Profession Module
 * @package Wowhead Tooltips
 * @extends wowhead
 */
class wowhead_profession extends wowhead
{
	public $lang;
	public $patterns;
	public $config;
	public $language;
	
	// parse the item/spell in the tooltip
	private $parse = false;
	
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
		if (isset($args['parse']))
			$this->parse = true;
		
		if (!$result = $cache->getFaction($name, $this->lang))
		{
			$result = (is_numeric($name)) ? $this->getProfessionByID($name) : $this->getProfessionByName($name);
			if (!$result)
			{
				$cache->close();
				return $this->notFound($this->language->words['profession'], $name);
			}
			else
			{
				//$cache->saveProfession($result);
				$cache->close();
				return $this->generateHTML($result, 'profession');
			}
		}
		else
		{
			$cache->close();
			return $this->generateHTML($result, 'faction');		
		}	
	}
	
	private function getProfessionByID($id, $result = array())
	{
		if (trim($id) == '' || !is_numeric($id))
			return false;

		$data = $this->readURL($id, 'profession', false);
		if (!$data) { return false; }
				
		// we may need to grab some basic info first
		if (sizeof($result) == 0)
		{
			if (!preg_match('#<title>(.+?) - Skill - World of Warcraft</title>#s', $data, $name_match))
				return false;
			$result = array(
				'id'			=>	$id,
				'name'			=>	stripslashes($name_match[1]),
				'search_name'	=>	$id,
				'lang'			=>	$this->lang
			);
		}

		$result['tooltip'] = $this->generateTooltip($result['name'], $this->getTooltipInformation($data));
		return (!$result['tooltip']) ? false : $result;
	}
	
	private function getProfessionByName($name)
	{
		if (trim($name) == '')
			return false;
		
		$data = $this->readURL($name, 'profession', false);
		if (!$data) { return false; }
		
		$line = $this->professionLine($data);
		if (!$line) { return false; }
		//var_dump($line); die;
		if (!$json = json_decode($line, true))
			return false;
		else
		{
			//var_dump($json); die;
			foreach ($json as $prof)
			{
				if (stripslashes(strtolower($name)) == stripslashes(strtolower($prof['name'])))
				{
					return $this->getProfessionByID($prof['id'], array(
						'id'			=>	$prof['id'],
						'name'			=>	stripslashes($prof['name']),
						'search_name'	=>	$name,
						'lang'			=>	$this->lang
					));	
				}	
			}
			return false;
		}
	}
	
	private function generateTooltip($name, $tooltip)
	{
		if (trim($name) == '' || trim($tooltip) == '' || !$tooltip)
			return false;
		$search = array('{name}', '{tooltip}');
		$replace = array($name, $tooltip);
		return str_replace($search, $replace, $this->patterns->pattern('faction_tooltip'));
	}
	
	private function getTooltipInformation($data)
	{
		if (trim($data) == '' || !preg_match('#Markup.printHtml\("(.+?)", "efhjkdsoicjx", \{ allow: Markup.CLASS_ADMIN, dbpage: true \}\);#s', $data, $match))
			return false;
		
		if ($this->parse == true)
		{
			// they want to parse out the items or spells
			
		}
		else
		{
			// default is to remove the spell and item tags
			$search = array(
				'#\[spell=(.+?)\]#s', '#\[item=(.+?)\]#s'
			);
			$match[1] = preg_replace($search, '', $match[1]);
		}
		
		// remove url and icon tags
		$search = array(
			'#\[url=(.+?)\]#s', '#\[\/url\]#s',
			'#\[icon=(.+?)\]#s', '#\[\/icon\]#s'
		);
		$match[1] = preg_replace($search, '', stripslashes($match[1]));
		// keep the remaining BBCode and convert into HTML
		return str_replace(
			array('[', ']'),
			array('<', '>'),
			$match[1]
		);
	}
	
	private function professionLine($data)
	{
		if (trim($data) == '') { return false; }
		$lines = explode(chr(10), $data);
		foreach ($lines as $line)
		{
			if (strpos($line, "new Listview({template: 'skill', id: 'skills',") !== false)
			{
				// format it for valid JSON
				$line = substr($line, strpos($line, 'data: [{') + 6);
				
				// wowhead devs got lazy, so we need to properly format the JSON so PHP won't bitch and moan
				$line = preg_replace('#icon:\'(.+?)\'\}#s', '"icon":"\1"}', $line);
				
				$line = str_replace('});', '', $line);
				return $line;	
			}	
		}
		return false;
	}
}
?>