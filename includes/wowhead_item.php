<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_item.php 4.0
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

class wowhead_item extends wowhead
{
	public $patterns;
	public $lang;
	public $language;
	public $config;
	public $type = 'item';
	private $show_icon = false;
	private $heroic = 0;	// for heroic items

	/**
	* Constructor
	* @access public
	**/
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

	/**
	* Parses Items
	* @access public
	**/
	public function parse($name, $args = array())
	{
		if (trim($name) == '')
			return false;

		$this->lang = (!array_key_exists('lang', $args)) ? $this->config->lang : $args['lang'];
		$this->heroic = (array_key_exists('heroic', $args)) ? 1 : 0;
		
		// load the language pack
		$this->language->loadLanguage($this->lang);
		
		$cache = new wowhead_cache();

        if ($this->config->item_show_icon == true || array_key_exists('icon', $args))
		{
		    $this->show_icon = true;
		    $this->type = 'item_icon';
		}
		
		if (!$result = $cache->getItem($name, $this->heroic, $this->lang, $this->show_icon))
		{
			$result = (is_numeric($name)) ? $this->getItemByID($name) : $this->getItemByName($name);
			if (!$result)
			{
				$cache->close();
				return $this->notFound($this->language->words['item'], $name);	
			}
			else
			{
				$cache->saveItem($result);	// save it to cache
				$cache->close();
				if (array_key_exists('gems', $args) || array_key_exists('enchant', $args))
				{
					$enhance = $this->buildEnhancement($args);
					return $this->generateHTML($result, $this->type, '', '', $enhance);
				}
				else
				{
					return $this->generateHTML($result, $this->type);
				}	
			}
		}
		else
		{
			$cache->close();
			if (array_key_exists('gems', $args) || array_key_exists('enchant', $args))
			{
				$enhance = $this->buildEnhancement($args);
				return $this->generateHTML($result, $this->type, '', '', $enhance);
			}
			else
			{
				return $this->generateHTML($result, $this->type);
			}
		}
	}
	
	private function getItemByName($name)
	{
		if (trim($name) == '')
			return false;
		
		$data = $this->readURL($name, 'item', false);
		
		if (!$data)
			return false;
		
		// for searches with only one result (aka redirect header)
		if (preg_match('#Location: \/item=([0-9]{1,10})#s', $data, $match))
			return $this->getItemByID($match[1], $name, $this->heroic);
		
		$line = $this->itemLine($data);
		
		if (!$line)
			return false;
		else
		{
			if (!$json = json_decode($line, true))
				return false;
				
			foreach ($json as $item)
			{
				// strip the first character, if necessary
				if (is_numeric(substr($item['name'], 0, 1)))
					$item['name'] = substr($item['name'], 1);
				
				if (strtolower(stripslashes($item['name'])) == strtolower(stripslashes($name)))
				{
					if ($this->heroic && array_key_exists('heroic', $item))
						return $this->getItembyID($item['id'], $name, true);
					elseif (!$this->heroic)
						return $this->getItemByID($item['id'], $name);
				}
			}
			return false;
		}
	}
	
	private function itemLine($data)
	{
		$parts = explode(chr(10), $data);
		foreach ($parts as $line)
		{
			if (strpos($line, "new Listview({template: 'item', id: 'items',") !== false)
			{
				// clean the line up to make it valid JSON
				$line = substr($line, strpos($line, 'data: [{') + 6);
				$line = str_replace('});', '', $line);
				return $line;	
			}
		}
		return false;
	}
	
	private function getItemByID($id, $search = '', $heroic = false)
	{
		if (trim($id) == '' || !is_numeric($id))
			return false;
		
		$data = $this->readURL($id, 'item');
		
		if (!$data)
			return false;
		
		// create the simplexml object
		if (!$xml = @simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA))
			return false;
		else
		{
			// build the result array
			$item = array(
				'name'			=>	stripslashes((string)$xml->item->name),
				'search_name'	=>	(trim($search) == '') ? $id : $search,
				'itemid'		=>	$id,
				'quality'		=>	(string)$xml->item->quality['id'],
				'lang'			=>	$this->lang
			);
			
			// if the item icon is required, add it
			if ($this->show_icon == true)
				$item['icon'] = 'http://static.wowhead.com/images/wow/icons/tiny/' . strtolower($xml->item->icon) . '.gif';
			if ($heroic)
				$item['heroic'] = 1;
			else
				$item['heroic'] = $this->heroic;
			
			return $item;	
		}
	}
}
?>
