<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_quest.php 4.0
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

class wowhead_quest extends wowhead
{
	public $lang;
	public $patterns;
	public $language;
	public $config;

	/**
	* Constructor
	* @access public
	**/
	public function __construct()
	{
		$this->config = new wowhead_config();
		$this->config->loadConfig();
		$this->patterns = new wowhead_patterns();
		$this->language = new wowhead_language();
	}

	public function close()
	{
		unset($this->lang, $this->language, $this->patterns, $this->config);	
	}

	/**
	* Parses quests
	* @access public
	**/
	public function parse($name, $args = array())
	{
		if (trim($name) == '')
			return false;

		$this->lang = (!array_key_exists('lang', $args)) ? $this->config->lang : $args['lang'];
		$this->language->loadLanguage($this->lang);
		$cache = new wowhead_cache();

		if (!$result = $cache->getQuest($name, $this->lang))
		{
			$result = (is_numeric($name)) ? $this->getQuestByID($name) : $this->getQuestByName($name);
			if (!$result)
			{
				// not found
				$cache->close();
				return $this->notFound($this->language->words['quest'], $name);
			}
			else
			{
				$cache->saveQuest($result);
				$cache->close();
				return $this->generateHTML($result, 'quest');
			}
		}
		else
		{
			// found in cache
			$cache->close();
			return $this->generateHTML($result, 'quest');
		}
	}

	/**
	* Queries Wowhead for Quest info by ID
	* @access private
	**/
	private function getQuestByID($id)
	{
		if (!is_numeric($id))
			return false;

		$data = $this->readURL($id, 'quest', false);

		// wowhead doesn't have the info
		if ($data == '$WowheadPower.registerQuest(' . $id . ', {});')
		{
			return false;
		}
		else
		{
			// gets the quest's name
			if (preg_match('#<b class="q">(.+?)</b>#s', $data, $match))
			{
				return array(
					'name'			=>	stripslashes($match[1]),
					'itemid'		=>	$id,
					'search_name'	=>	$id,
					'lang'			=> $this->lang
				);
			}
			else
			{
				return false;
			}
		}
	}

	/**
	* Queries Wowhead for Quest by Name
	* @access private
	**/
	private function getQuestByName($name)
	{
		if (trim($name) == '')
			return false;

		$data = $this->readURL($name, 'quest', false);
		
		if (!$data)
			return false;
			
		// make sure it didn't redirect
		if (preg_match('#Location: \/quest=([0-9]{1,10})#s', $data, $match))
		{
			return array(
				'name'			=>	ucwords(strtolower($name)),
				'search_name'	=>	$name,
				'itemid'		=>	$match[1],
				'lang'			=>	$this->lang
			);	
		}
		
		// get the JSON line from the data
		$line = $this->questLine($data);
		
		if (!$line)
			return false;
		else
		{
			// decode the json
			if (!$json = json_decode($line, true))
				return false;
			
			foreach ($json as $quests)
			{
				if (stripslashes(strtolower($quests['name'])) == stripslashes(strtolower($name)))
				{
					return array(
						'name'			=>	$quests['name'],
						'search_name'	=>	$name,
						'itemid'		=>	$quests['id'],
						'lang'			=>	$this->lang
					);
				}
			}
			
			return false;
		}
	}
	
	private function questLine($data)
	{
		$parts = explode(chr(10), $data);
		foreach ($parts as $line)
		{
			if (strpos($line, "new Listview({template: 'quest', id: 'quests',") !== false)
			{
				$line = substr($line, strpos($line, 'data: [{') + 6);
				$line = str_replace('});', '', $line);
				return $line;
				break;
			}
		}
		
		return false;
	}
}
?>