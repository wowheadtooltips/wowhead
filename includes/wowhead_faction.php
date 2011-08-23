<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_faction.php 4.4
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

/**
 * Wowhead Faction Module
 * @package Wowhead Tooltips
 * @extends wowhead
 */
class wowhead_faction extends wowhead
{
	public $lang;
	public $patterns;
	public $config;
	public $language;
	
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
		
		if (!$result = $cache->getFaction($name, $this->lang))
		{
			$result = (is_numeric($name)) ? $this->getFactionByID($name) : $this->getFactionByName($name);
			if (!$result)
			{
				$cache->close();
				return $this->notFound($this->language->words['faction'], $name);
			}
			else
			{
				$cache->saveFaction($result);
				$cache->close();
				return $this->generateHTML($result, 'faction');
			}
		}
		else
		{
			$cache->close();
			return $this->generateHTML($result, 'faction');		
		}
	}
	
	private function getFactionByID($id, $result = array())
	{
		if (trim($id) == '' || !is_numeric($id))
			return false;
		
		$data = $this->readURL($id, 'faction', false);
		if (!$data)
			return false;
			
		// no prior information was provided, so we need to get it ourselves
		if (sizeof($result) == 0)
		{
			if (!preg_match('#<title>(.+?) - Faction - World of Warcraft</title>#s', $data, $name_match))
				return false;
			$result = array(
				'id'			=>	$id,
				'name'			=>	$name_match[1],
				'search_name'	=>	$id,
				'lang'			=>	$this->lang,
				
			);
		}
		
		$result['tooltip'] = $this->generateTooltip($result['name'], $this->getTooltipInformation($data));
		return (!$result['tooltip']) ? false : $result;
	}
	
	private function getFactionByName($name)
	{
		if (trim($name) == '')
			return false;
		
		$data = $this->readURL($name, 'faction', false);
		if (!$data)
			return false;
		
		$line = $this->factionLine($data);
		if (!$line)
			return false;

		if (!$json = json_decode($line, true))
			return false;
		else
		{
			foreach ($json as $faction)
			{
				if (strtolower(stripslashes($name)) == strtolower(stripslashes($faction['name'])))
				{
					return $this->getFactionByID($faction['id'], array(
						'id'			=>	$faction['id'],
						'name'			=>	stripslashes($faction['name']),
						'search_name'	=>	$name,
						'lang'			=>	$this->lang
					));	
				}	
			}
		}
		return false;
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

		if (trim($data) == '')
			return false;
		$lines = explode(chr(10), $data);
		$i = 0;
		foreach ($lines as $line)
		{
			if (strpos($line, '<h1 class="h1-icon">') !== false)
			{
				// prepare the line for output
				$search = array('<', '>', '"', '&lt;h2 class=&quot;clear&quot;&gt;Related&lt;/h2&gt;', '\'');
				$replace = array('&lt;', '&gt;', '&quot;', '', '');
				return str_replace($search, $replace, $lines[$i + 2]);
			}
			$i++;
		}
		return false;
	}
	
	private function factionLine($data)
	{
		if (trim($data) == '')
			return false;
		$lines = explode(chr(10), $data);
		foreach ($lines as $line)
		{
			if (strpos($line, "new Listview({template: 'faction', id: 'factions',") !== false)
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