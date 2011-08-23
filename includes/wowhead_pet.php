<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_pet.php 4.3
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

class wowhead_pet extends wowhead
{
	public $lang;
	public $patterns;
	public $language;
	public $config;
	
	private $get_skills;
	private $skills = array();
	private $diet = array(
		1	=>	'Meat',
		3	=>	'Meat, Fish',
		14	=>	'Fish, Cheese, Bread',
		17	=>	'Meat, Fungus',
		28	=>	'Cheese, Bread, Fungus',
		34	=>	'Fish, Fruit',
		35	=>	'Meat, Fish, Fruit',
		49	=>	'Meat, Fungus, Fruit',
		56	=>	'Bread, Fungus, Fruit',
		58	=>	'Fish, Bread, Fungus, Fruit',
		60	=>	'Cheese, Bread, Fungus, Fruit',
		63	=>	'Meat, Fish, Cheese, Bread, Fungus, Fruit',
	);
	private $pet_type = array(
		0	=>	'Ferocity',
		1	=>	'Tenacity',
		2	=>	'Cunning',
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
		unset($this->lang, $this->config, $this->language, $this->patterns);
	}
	
	public function parse($name, $args = array())
	{
		if (trim($name) == '')
			return false;
		
		$cache = new wowhead_cache();
		$this->lang = (!array_key_exists('lang', $args)) ? $this->config->lang : $args['lang'];
		$this->language->load($this->lang);
		if (!$result = $cache->getPet($name, $this->lang))
		{
			$result = (is_numeric($name)) ? $this->getPetByID($name) : $this->getPetByName($name);
			if (!$result)
			{
				$cache->close();
				return $this->notFound($this->language->words['pet'], $name);
			}
			else
			{
				$cache->savePet($result);
				$cache->close();
				return $this->generateOutput($result);	// we have to use a custom HTML generator for this module
			}
		}
		else
		{
			$result['link'] = $this->generateLink($result['id'], 'pet');
			$cache->close();
			return $this->generateOutput($result);
		}
	}
	
	private function getPetByID($id)
	{
		if (trim($id) == '')
			return false;

		$data = $this->readURL($id, 'pet', false);
		if (!$data)
			return false;
		
		$line = $this->petLine($data);
		if (!$line)
			return false;

		if (!$json = json_decode($line, true))
			return false;
		else
		{
			foreach ($json as $pet)
			{
				if ($id == $pet['id'])
				{
					$result = array(
						'id'			=>	$pet['id'],
						'name'			=>	stripslashes($pet['name']),
						'search_name'	=>	stripslashes($id),
						'minlevel'		=>	$pet['minlevel'],
						'maxlevel'		=>	$pet['maxlevel'],
						'diet'			=>	(array_key_exists($pet['diet'], $this->diet)) ? $this->diet[$pet['diet']] : 'Error',
						'spells'		=>	$this->generateSkills($pet['spells']),
						'type'			=>	(array_key_exists($pet['type'], $this->pet_type)) ? $this->pet_type[$pet['type']] : 'Error',
						'tipicon'		=>	$pet['icon'],
						'lang'			=>	$this->lang
					);
				}
			}
			if (sizeof($result) == 0)
				return false;
			else
			{
				$result['tooltip'] = $this->generateTooltip($this->patterns->pattern('pet_tooltip'), $result['name'], $result['minlevel'] . '-' . $result['maxlevel'], $result['type'], $result['diet'], $result['spells']);
				$result['link'] = $this->generateLink($result['id'], 'pet');
				return $result;
			}
		}		
	}
	
	private function getPetByName($name)
	{
		if (trim($name) == '')
			return false;
		
		$data = $this->readURL($name, 'pet', false);
		if (!$data)
			return false;

		$line = $this->petLine($data);
		if (!$line)
			return false;

		if (!$json = json_decode($line, true))
			return false;
		else
		{
			foreach ($json as $pet)
			{
				if (strtolower(stripslashes($pet['name'])) == strtolower(stripslashes($name)))
				{
					$result = array(
						'id'			=>	$pet['id'],
						'name'			=>	stripslashes($pet['name']),
						'search_name'	=>	stripslashes($name),
						'minlevel'		=>	$pet['minlevel'],
						'maxlevel'		=>	$pet['maxlevel'],
						'diet'			=>	(array_key_exists($pet['diet'], $this->diet)) ? $this->diet[$pet['diet']] : 'Error',
						'spells'		=>	$this->generateSkills($pet['spells']),
						'type'			=>	(array_key_exists($pet['type'], $this->pet_type)) ? $this->pet_type[$pet['type']] : 'Error',
						'tipicon'		=>	$pet['icon'],
						'lang'			=>	$this->lang
					);
					break;
				}
			}
			if (sizeof($result) == 0)
				return false;
			else
			{
				$result['tooltip'] = $this->generateTooltip($this->patterns->pattern('pet_tooltip'), $result['name'], $result['minlevel'] . '-' . $result['maxlevel'], $result['type'], $result['diet'], $result['spells']);
				$result['link'] = $this->generateLink($result['id'], 'pet');
				return $result;
			}
		}
	}
	
	private function generateOutput($info)
	{
		$search = array('{icon}', '{name}', '{tipicon}', '{tooltip}', '{link}');
		$replace = array($info['icon'], $info['name'], $info['tipicon'], $info['tooltip'], $info['link']);
		$pattern = $this->patterns->pattern('pet');
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
	
	private function generateSkills($skills)
	{
		if (sizeof($skills) == 0)
			return 'Error';
		//$line = '&lt;div class=&quot;iconsmall&quot; style=&quot;background: url(%s) no-repeat scroll 4px 4px; top: 5px;&quot;&gt;&lt;div class=&quot;tile&quot;&gt;&lt;/div&gt;&lt;/div&gt;&lt;span style=&quot;height: 20px; display: inline; padding-top: 3px;&quot;&gt;%s&lt;/span&gt;';
		$line = '&lt;div class=&quot;icontiny&quot; style=&quot;padding-bottom: 5px;background-image: url(%s);&quot;&gt;&nbsp;%s&lt;/div&gt;';
		$out = '';
		foreach ($skills as $skill)
		{
			$data = $this->readURL($skill, 'spell', false);
			if (!$data)
				return 'Error';
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
			if (!preg_match('#name_' . $str . ': \'(.+?)\',#s', $data, $name))
				return 'Error';
			if (!preg_match('#icon: \'(.+?)\',#s', $data, $icon))
				return 'Error';
			$name = $name[1]; $icon = 'http://static.wowhead.com/images/wow/icons/small/' . strtolower($icon[1]) . '.jpg';
			$out .= sprintf($line, $icon, $name);
		}
		if (trim($out) == '')
			return 'Error';
		else
			return $out;
	}
	
	private function petLine($data)
	{
		$parts = explode(chr(10), $data);
		foreach ($parts as $line)
		{
			if (strpos($line, "new Listview({template: 'pet', id: 'hunter-pets',") !== false)
			{
				// format it for valid JSON
				$line = substr($line, strpos($line, 'data: [{') + 6);
				$line = str_replace('});', '', $line);
				$line = str_replace('spells', '"spells"', $line);
				return $line;
			}
		}
		return false;
	}
}
?>
