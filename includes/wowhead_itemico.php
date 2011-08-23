<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_itemico.php 4.0
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

class wowhead_itemico extends wowhead
{
	public $lang;
	public $patterns;
	public $language;
	public $config;
	private $size = 'medium';
	private $heroic = false;

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
	* Parse Item Icons
	* @access public
	**/
	public function parse($name, $args = array())
	{
		if (trim($name) == '')
			return false;

		$this->size = (!array_key_exists('size', $args)) ? 'medium' : $args['size'];
		$this->lang = (!array_key_exists('lang', $args)) ? $this->config->lang : $args['lang'];
		$this->language->loadLanguage($this->lang);
		$this->heroic = (array_key_exists('heroic', $args)) ? 1 : 0;
		$cache = new wowhead_cache();

		if (!$result = $cache->getItemIcon($name, $this->heroic, $this->lang, $this->size))
		{
			$result = (is_numeric($name)) ? $this->getItemicoByID($name) : $this->getItemicoByName($name);
			if (!$result)
			{
				$cache->close();
				return $this->notFound($this->language->words['item'], $name);
			}
			else
			{
				$cache->saveItemIcon($result);
				$cache->close();
				if (array_key_exists('gems', $args) || array_key_exists('enchant', $args))
				{
					$enhance = $this->buildEnhancement($args);
					return $this->generateHTML($result, 'itemico', $this->size, '', $enhance);	
				}
				else
				{
					return $this->generateHTML($result, 'itemico', $this->size);
				}
			}
		}
		else
		{
			$cache->close();
			if (array_key_exists('gems', $args) || array_key_exists('enchant', $args))
			{
				$enhance = $this->buildEnhancement($args);
				return $this->generateHTML($result, 'itemico', $this->size, '', $enhance);	
			}
			else
			{
				return $this->generateHTML($result, 'itemico', $this->size);
			}
		}
	}
	
	private function getItemicoByName($name)
	{
		if (trim($name) == '')
			return false;
		
		$data = $this->readURL($name, 'item', false);
		if (!$data)
			return false;

		// for searches with only one result (aka redirect header)
		if (preg_match('#Location: \/item=([0-9]{1,10})#s', $data, $match))
			return $this->getItemicoByID($match[1], $name, $this->heroic);
		
		$line = $this->itemLine($data);
		
		if (!$line)
			return false;
		else
		{
			if (!$json = json_decode($line, true))
				return false;
				
			foreach ($json as $item)
			{
				if ($this->heroic && array_key_exists('heroic', $item))
					return $this->getItemicobyID($item['id'], $name);
				elseif (!$this->heroic)
					return $this->getItemicoByID($item['id'], $name);
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
	
	private function getItemicoByID($id, $search = '')
	{
		if (trim($id) == '')
			return false;
		
		$data = $this->readURL($id, 'item');
		if (!$data)
			return false;
		
		if (!$xml = @simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA))
			return false;
		else
		{
			return array(
				'name'			=>	stripslashes((string)$xml->item->name),
				'search_name'	=>	(trim($search) == '') ? $id : $search,
				'itemid'		=>	$id,
				'lang'			=>	$this->lang,
				'icon'			=>	($this->size != 'tiny') ? 'http://static.wowhead.com/images/wow/icons/' . $this->size . '/' . strtolower($xml->item->icon) . '.jpg' : 'http://static.wowhead.com/images/wow/icons/tiny/' . strtolower($xml->item->icon) . '.gif',
				'size'			=>	$this->size,
				'heroic'		=>	$this->heroic
			);
		}
	}
}
?>
