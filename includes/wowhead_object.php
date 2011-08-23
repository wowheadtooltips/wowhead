<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_object.php 4.0
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

class wowhead_object extends wowhead
{
	public $lang;
	public $patterns;
	public $language;
	public $config;
	
	public function __construct()
	{
		$this->config = new wowhead_config();
		$this->config->loadConfig();
		$this->patterns = new wowhead_patterns();
		$this->language = new wowhead_language();	
	}
	
	public function close()
	{
		unset($this->lang, $this->patterns, $this->language, $this->config);	
	}
	
	public function parse($name, $args = array())
	{
		if (trim($name) == '')
			return false;

		$this->lang = (array_key_exists('lang', $args)) ? $args['lang'] : $this->config->lang;
		$this->language->loadLanguage($this->lang);
		$cache = new wowhead_cache();
		
		if (!$result = $cache->getObject($name, $this->lang))
		{
			$result = (is_numeric($name)) ? $this->getObjectByID($name) : $this->getObjectByName($name);
			if (!$result)
			{
				$cache->close();
				return $this->notFound($this->language->words['object'], $name);	
			}
			else
			{
				$cache->saveObject($result);
				$cache->close();
				return $this->generateHTML($result, 'object');
			}
		}
		else
		{
			$cache->close();
			return $this->generateHTML($result, 'object');	
		}
	}
	
	private function getObjectByName($name)
	{
		if (trim($name) == '')
			return false;
			
		$data = $this->readURL($name, 'object', false);
		
		// make sure it didn't redirect
		if (preg_match('#Location: \/object=([0-9]{1,10})#s', $data, $match))
		{
			return array(
				'name'			=>	ucwords(strtolower($name)),
				'search_name'	=>	$name,
				'itemid'		=>	$match[1],
				'type'			=>	'quest',
				'lang'			=>	$this->lang
			);	
		}
		
		$line = $this->objectLine($data);
		
		if (!$line)
			return false;
		else
		{
			if (!$json = json_decode($line, true))
				return false;
			
			foreach ($json as $objects)
			{
				if (stripslashes(strtolower($objects['name'])) == stripslashes(strtolower($name)))
				{
					return array(
						'name'			=>	stripslashes($objects['name']),
						'search_name'	=>	$name,
						'itemid'		=>	$objects['id'],
						'type'			=>	'object',
						'lang'			=>	$this->lang
					);	
				}
			}
			return false;
		}
	}
	
	private function objectLine($data)
	{
		$lines = explode(chr(10), $data);
		foreach ($lines as $line)
		{
			if (strpos($line, "new Listview({template: 'object', id: 'objects',") !== false)
			{
				$line = substr($line, strpos($line, 'data: [{') + 6);
				$line = str_replace('});', '', $line);
				return $line;
				break;
			}
		}
		return false;
	}
	
	private function getObjectByID($id)
	{
		$data = $this->readURL($id, 'object', false);
		
		if ($data == '$WowheadPower.registerObject(1337, 0, {});')
		{
			// aka not found
			return false;
		}
		else
		{
			// gets the object's name
			if (preg_match('#<b class="q">(.+?)</b>#s', $data, $match))
			{
				return array(
					'name'			=>	stripslashes($match[1]),
					'itemid'		=>	$id,
					'search_name'	=>	$id,
					'type'			=>	'object',
					'lang'			=>	$this->lang
				);
			}
			else
			{
				return false;
			}
		}
	}
}

?>
