<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_stats.php 4.3
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/
class wowhead_stats extends wowhead
{
	public $lang;
	public $patterns;
	public $config;
	public $language;
	
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
	
	public function parse($name, $args = array())
	{
		if (trim($name) == '')
			return false;
		
		$this->lang = (!array_key_exists('lang', $args)) ? $this->config->lang : $args['lang'];
		$this->language->loadLanguage($this->lang);
		$cache = new wowhead_cache();
		
		if (!$result = $cache->getStatistic($name, $this->lang))
		{
			$result = (is_numeric($name)) ? $this->getStatsByID($name) : $this->getStatsByName($name);
			if (!$result)
			{
				$cache->close();
				return $this->notFound($this->language->words['stats'], $name);
			}
			else
			{
				$cache->saveStatistic($result);
				$cache->close();
				return $this->generateHTML($result, 'stats');
			}
		}
		else
		{
			$cache->close();
			return $this->generateHTML($result, 'stats');
		}
	}
	
	private function getStatsByName($name)
	{
		if (trim($name) == '')
			return false;
		$data = $this->readURL($name, 'stats', false);
		if (!$data)
			return false;
		if (preg_match('#Location: \/statistic=(.+?)\n#s', $data, $match))
		{
			// we need to change search_name back to the name
			$result = $this->getStatsByID((int)$match[1]);
			$result['search_name'] = $name;
			return $result;
		}
		
		$line = $this->statisticLine($data);
		if (!$line || !$json = json_decode($line, true))
			return false;
		else
		{
			foreach ($json as $statistic)
			{
				if (strtolower(stripslashes($name)) == strtolower(stripslashes($statistic['name'])))
				{
					return array(
						'id'			=>	(int)$statistic['id'],
						'name'			=>	(string)$statistic['name'],
						'search_name'	=>	$name,
						'lang'			=>	$this->lang
					);
				}
			}
			return false;
		}
	}
	
	private function getStatsByID($id)
	{
		if (trim($id) == '' || !is_numeric($id))
			return false;
		$data = $this->readURL($id, 'stats', false);
		if ($data == '$WowheadPower.registerStatistic')
			return false;
		else
		{
			switch ($this->lang)
			{
				case 'de':
					$str = 'dede';
					break;
				case 'fr':
					$str = 'frfr';
					break;
				case 'es':
					$str = 'eses';
					break;
				case 'en':
				default:
					$str = 'enus';
					break;
			}
			if (preg_match('#name_' . $str . ': \'(.+?)\',#s', $data, $match))
			{
				return array(
					'name'			=>	stripslashes($match[1]),
					'id'			=>	$id,
					'search_name'	=>	$id,
					'lang'			=>	$this->lang
				);
			}
			else
			{
				return false;
			}
		}
	}
	
	private function statisticLine($data)
	{
		$parts = explode(chr(10), $data);
		foreach ($parts as $line)
		{
			if (strpos($line, "new Listview({template: 'achievement', id: 'statistics',") !== false)
			{
				$line = substr($line, strpos($line, 'data: [{') + 6);
				$line = str_replace('});', '', $line);
				return $line;				
			}
		}
		return false;
	}
}
?>
