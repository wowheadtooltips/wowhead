<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_itemset.php 4.0
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

class wowhead_itemset extends wowhead
{
	// variables
	public $lang;
	public $patterns;
	public $language;
	public $config;
	private $itemset = array();
	private $itemset_items = array();
	private $heroic = 0;	// heroic itemset

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
	* Parses itemset bbcode
	* @access public
	**/
	public function parse($name, $args = array())
	{
		if (trim($name) == '')
			return false;

		$this->lang = (!array_key_exists('lang', $args)) ? $this->config->lang : $args['lang'];
		$this->heroic = (array_key_exists('heroic', $args)) ? 1 : 0;
		$this->language->loadLanguage($this->lang);
		$cache = new wowhead_cache();
		if (!$result = $cache->getItemset($name, $this->lang, $this->heroic))
		{
			$success = (is_numeric($name)) ? $this->getItemsetByID($name) : $this->getItemsetByName($name);
			
			// queries failed
			if (!$success)
				return $this->notFound('itemset', $name);
			
			$cache->saveItemset($this->itemset, $this->itemset_items);
			$cache->close();
			return $this->toHTML();
		}
		else
		{
			$this->itemset = $result;
			$this->itemset_items = $cache->getItemsetReagents($this->itemset['setid']);
			$cache->close();
			return $this->toHTML();
		}
	}
	
	private function getItemsetByName($name)
	{
		if (trim($name) == '')
			return false;
		
		$data = $this->readURL($name, 'itemset', false);
		
		if (!$data)
			return false;
			
		if (preg_match('#Location: \/itemset=([\-0-9]{1,10})#s', $data, $match))
		{
			// since it redirected to a new page, we must pull that data
			$data = $this->readURL($match[1], 'itemset', false);
			
			// pull the properly formatted name
			$nameline = $this->nameLine($data);
			
			if (!preg_match("/name: '(.+?)'\};/", $nameline, $linematch))
				return false;	
			
			$this->itemset = array(
				'setid'			=>	$match[1],
				'name'			=>	stripslashes($linematch[1]),
				'search_name'	=>	$name,
				'lang'			=>	$this->lang
			);
			
			// now time to pull the items
			while (preg_match('#<span class="q([0-6]{1})"><a href="\/item=(.+?)">(.+?)</a></span>#s', $data, $items))
			{
				$this->itemset_items[] = array(
					'setid'		=>	$match[1],
					'itemid'	=>	$items[2],
					'name'		=>	stripslashes($items[3]),
					'quality'	=>	$items[1],
					'icon'		=>	'http://static.wowhead.com/images/wow/icons/small/' . $this->getItemIcon($items[2])
				);
				$data = str_replace($items[0], '', $data);
			}
			
			if (sizeof($this->itemset) == 0 || sizeof($this->itemset_items) == 0)
				return false;
			else
				return true;
		}
		
		// get the data line
		$line = $this->summaryLine($data);

		if (!$line)
			return false;
		else
		{
			// decode the json result
			if (!$json = json_decode($line, true))
				return false;

			foreach ($json as $itemset)
			{
				// strip the first character from the name
				$itemset['name'] = substr($itemset['name'], 1);
				
				if (stripslashes(strtolower($itemset['name'])) == stripslashes(strtolower($name)))
				{
					if ($this->heroic && array_key_exists('heroic', $itemset))
					{
						$this->itemset = array(
							'setid'			=>	$itemset['id'],
							'name'			=>	stripslashes($itemset['name']) . ' (Heroic)',
							'heroic'		=>	1,
							'search_name'	=>	$name,
							'lang'			=>	$this->lang
						);
						
						foreach ($itemset['pieces'] as $piece)
						{
							$xml_data = $this->readURL($piece, 'item');
							
							if (!$xml = @simplexml_load_string($xml_data, 'SimpleXMLElement', LIBXML_NOCDATA))
								return false;
							
							$this->itemset_items[] = array(
								'setid'		=>	$itemset['id'],
								'itemid'	=>	$piece,
								'name'		=>	(string)$xml->item->name,
								'quality'	=>	(int)$xml->item->quality['id'],
								'icon'		=>	'http://static.wowhead.com/images/wow/icons/small/' . strtolower((string)$xml->item->icon) . '.jpg'
							);
							unset($xml_data, $xml);
						}
						
						if (sizeof($this->itemset) == 0 || sizeof($this->itemset_items) == 0)
							return false;
						else
							return true;
					}
					elseif (!$this->heroic)
					{
						$this->itemset = array(
							'setid'			=>	$itemset['id'],
							'name'			=>	stripslashes($itemset['name']),
							'search_name'	=>	$name,
							'lang'			=>	$this->lang
						);
						
						foreach ($itemset['pieces'] as $piece)
						{
							$xml_data = $this->readURL($piece, 'item');
							
							if (!$xml = @simplexml_load_string($xml_data, 'SimpleXMLElement', LIBXML_NOCDATA))
								return false;
							
							$this->itemset_items[] = array(
								'setid'		=>	$itemset['id'],
								'itemid'	=>	$piece,
								'name'		=>	(string)$xml->item->name,
								'quality'	=>	(int)$xml->item->quality['id'],
								'icon'		=>	'http://static.wowhead.com/images/wow/icons/small/' . strtolower((string)$xml->item->icon) . '.jpg'
							);
							unset($xml_data, $xml);
						}
						
						if (sizeof($this->itemset) == 0 || sizeof($this->itemset_items) == 0)
							return false;
						else
							return true;						
					}
				}
			}
		}
	}
	
	private function getItemsetByID($id)
	{
		if (trim($id) == '' || !is_numeric($id))
			return false;

		$data = $this->readURL($id, 'itemset', false);
		
		// pull the properly formatted name
		$nameline = $this->nameLine($data);
		
		if (!preg_match("/name: '(.+?)'\};/", $nameline, $linematch))
			return false;	
		
		$this->itemset = array(
			'setid'			=>	$id,
			'name'			=>	stripslashes($linematch[1]),
			'search_name'	=>	$name,
			'lang'			=>	$this->lang
		);
		
		// now time to pull the items
		while (preg_match('#<span class="q([0-6]{1})"><a href="\/item=(.+?)">(.+?)</a></span>#s', $data, $items))
		{
			$this->itemset_items[] = array(
				'setid'		=>	$id,
				'itemid'	=>	$items[2],
				'name'		=>	stripslashes($items[3]),
				'quality'	=>	$items[1],
				'icon'		=>	'http://static.wowhead.com/images/wow/icons/small/' . $this->getItemIcon($items[2])
			);
			$data = str_replace($items[0], '', $data);
		}		
		
		if (sizeof($this->itemset) == 0 || sizeof($this->itemset_items) == 0)
			return false;
		else
			return true;	
	}
	
	private function getItemIcon($id)
	{
		$xml_data = $this->readURL($id, 'item');
		if (!$xml = @simplexml_load_string($xml_data, 'SimpleXMLElement', LIBXML_NOCDATA))
			die('Failed to get the icon.');		
		return strtolower($xml->item->icon) . '.jpg';
	}

	/**
	* Generates HTML
	* @access private
	**/
	private function toHTML()
	{

		// generate item HTML first
		$item_html = ''; $set_html = $this->patterns->pattern('itemset');

		foreach ($this->itemset_items as $item)
		{
			$patt = $this->patterns->pattern('itemset_item');
			$search = array(
				'{link}'	=>	$this->generateLink($item['itemid'], 'item'),
				'{name}'	=>	stripslashes($item['name']),
				'{qid}'		=>	$item['quality'],
				'{icon}'	=>	$item['icon']
			);
			foreach ($search as $key => $value)
				$patt = str_replace($key, $value, $patt);
			$item_html .= $patt;
		}

		// now generate everything
		$set_html = str_replace('{link}', $this->generateLink($this->itemset['setid'], 'itemset'), $set_html);
		$set_html = str_replace('{name}', $this->itemset['name'], $set_html);
		$set_html = str_replace('{items}', $item_html, $set_html);

		return $set_html;
	}

	/**
	* Returns the summary line we need for getting itemset items
	* @access private
	**/
	private function summaryLine($data)
	{
		$parts = explode(chr(10), $data);
		foreach ($parts as $line)
		{
			if (strpos($line, "new Summary({id: 'itemset', template: 'itemset',") !== false)
			{
				$line = substr($line, strpos($line, 'data: [{') + 6);
				$line = str_replace('});', '', $line);
				return $line;
				break;
			}
			elseif (strpos($line, "new Listview({template: 'itemset', id: 'itemsets',") !== false)
			{
				$line = substr($line, strpos($line, 'data: [{') + 6);
				$line = str_replace('});', '', $line);
				return $line;
				break;	
			}
			elseif (strpos($line, "new Listview({template: 'itemset', id: 'item-sets',") !== false)
			{
				$line = substr($line, strpos($line, 'data: [{') + 6);
				$line = str_replace('});', '', $line);
				return $line;
				break;
			}
		}
		return false;
	}
	
	/**
	 * Gets the line which we use to get the name
	 * @access private 
	 **/
	private function nameLine($data)
	{
		$parts = explode(chr(10), $data);
		foreach ($parts as $line)
		{	
			if (strpos($line, 'var g_pageInfo = {type:') !== false)
			{
				return $line;
				break;
			}
		}
	}
}
?>