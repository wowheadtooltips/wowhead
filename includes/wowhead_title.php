<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_title.php 4.3
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

/**
 * Wowhead Title Module
 * @package Wowhead Tooltips
 * @extends wowhead
 */
class wowhead_title extends wowhead
{
	public $lang;
	public $patterns;
	public $language;
	public $config;
	
	private $faction;
	private $factionid;
	private $image_url;
	private $use_name;
	
	public function __construct()
	{
		$this->patterns = new wowhead_patterns();
		$this->language = new wowhead_language();
		$this->config = new wowhead_config();
		$this->config->loadConfig();
		$this->image_url = $this->config->armory_image_url . 'images/title/';
	}

	public function close()
	{
		unset($this->lang, $this->language, $this->patterns, $this->config);	
	}
	
	public function parse($name, $args = array())
	{
		if (trim($name) == '')
			return false;
		
		if (array_key_exists('horde', $args))
			$this->faction = 'horde';
		elseif (array_key_exists('alliance', $args))
			$this->faction = 'alliance';
		else
			$this->faction = '';
		$this->factionid = ($this->faction == 'horde') ? 0 : 1;
		$this->use_name = (array_key_exists('name', $args)) ? ucwords($args['name']) : '&lt;Name&gt;';
		$cache = new wowhead_cache();
		$this->lang = (!array_key_exists('lang', $args)) ? $this->config->lang : $args['lang'];
		$this->language->loadLanguage($this->lang);
		if (!$result = $cache->getTitle($name, $this->faction, $this->lang))
		{
			$result = $this->fetchTitle($name);
			if (!$result)
			{
				// not found
				$cache->close();
				return $this->notFound($this->language->words['title'], $name);
			}
			else
			{
				$cache->saveTitle($result);
				$cache->close();
				return $this->generateHTML($result, 'title');
			}
		}
		else
		{
			// grab from cache
			$result = $this->formatHTML($result);
			$cache->close();
			return $this->generateHTML($result, 'title');
		}
	}
	
	private function fetchTitle($name)
	{
		if (trim($name) == '')
			return false;
		
		$data = $this->readURL($name, 'title', false);
		if (!$data)
			return false;
		
		$line = $this->titleLine($data);
		if (!$line)
			return false;
		else
		{
			if (!$json = @json_decode($line, true))
				return false;
			foreach ($json as $title)
			{
				if (strpos(stripslashes(strtolower($title['name'])), stripslashes(strtolower($name))) !== false)
				{
					// make sure faction is set, if the title requires it
					if ($title['side'] < 3 && trim($this->faction) == '')
						return false;
					// if the faction is set but there's only one source (meaning no faction requirement), then return false
					if (trim($this->faction) != '' && sizeof($title['source'][12]) == 1)
						return false;

					$source = ($this->faction == '') ? $title['source'][12][0] : $title['source'][12][$this->factionid];
					// set up the initial result array
					$info = array(
						'search_name'	=>	$name,
						'lang'			=>	$this->lang,
						'faction'		=>	$this->faction,
						'pattern'		=>	$title['name'],
						'achievement'	=>	$source['ti'],
						'expansion'		=>	$title['expansion']
					);
				}
			}
			
			// something went wrong, generally title not found
			if (!isset($info) || sizeof($info) == 0)
				return false;
			else
			{
				// add the formatted html to the info array.
				$info = $this->formatHTML($info);
				return $info;
			}
		}
	}
	
	private function formatHTML($info)
	{
		if (sizeof($info) == 0)
			return false;
			
		// determine which expansion icon to use, if any
		if ($info['expansion'] == 0)
			$exp = '';
		else
			$exp = ($info['expansion'] == 1) ? '<img src="' . $this->image_url . 'bc.gif" height="12" width="29" alt="The Burning Crusade" />' : '<img src="' . $this->image_url . 'wotlk.gif" height="13" width="36" alt="Wrath of the Lich King" />';
		
		// format the title
		$info['sprintf_pattern'] = sprintf($info['pattern'], $this->use_name);
		// show faction icon, if requested
		$info['faction_html'] = (trim($this->faction) == '') ? '' : '<img src="' . $this->image_url . strtolower($this->faction) . '.gif" alt="' . $this->faction . '" />';
		// show the expansion, if it exists
		$info['expansion_html'] = $exp;
		// generate the achievement link
		$info['link'] = $this->generateLink($info['achievement'], 'achievement');
		
		return $info;
	}
	
	private function titleLine($data)
	{
		$parts = explode(chr(10), $data);
		foreach ($parts as $line)
		{
			if (strpos($line, "new Listview({template: 'title', id: 'titles',") !== false)
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
