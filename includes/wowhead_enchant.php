<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_enchant.php 4.0
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

/**
 * Enchant Module
 * @package Wowhead Tooltips
 * @extends wowhead
 */
class wowhead_enchant extends wowhead
{
	/**
	 * Default Language
	 * @var string $lang
	 */
	public $lang;
	
	/**
	 * Patterns Module
	 * @var object $patterns
	 */
	public $patterns;
	
	/**
	 * Language Packs
	 * @var object $language
	 */
	public $language;
	
	/**
	 * Configuration
	 * @var object $config
	 */
	public $config;
	
	/**
	 * Reagents Array for Generating HTML
	 * @var array $reagents
	 */
	private $reagents = array();
	
	/**
	 * Constructor
	 * @access public
	 * @return null
	 */
	public function __construct()
	{
		$this->patterns = new wowhead_patterns();
		$this->language = new wowhead_language();
		$this->config = new wowhead_config();
		$this->config->loadConfig();
	}
	
	/**
	 * Destructor (sort of)
	 * @access public
	 * @return null
	 */
	public function close()
	{
		unset($this->lang, $this->patterns, $this->language, $this->config);	
	}
	
	/**
	 * Parse Incoming Text
	 * @access public
	 * @param string $name
	 * @param array $args [optional]
	 * @return 
	 */
	public function parse($name, $args = array())
	{
		if (trim($name) == '')
			return false;
		
		// set up some preliminary stuff
		$this->lang = (array_key_exists('lang', $args)) ? $args['lang'] : $this->config->lang;
		$cache = new wowhead_cache();
		$this->language->loadLanguage($this->lang);
		
		if (!$result = $cache->getEnchant($name, $this->lang))
		{
			$result = (is_numeric($name)) ? $this->getEnchantByID($name) : $this->getEnchantByName($name);
			
			if (!$result || sizeof($this->reagents) == 0)
			{
				$cache->close();
				return $this->notFound($this->language->words['enchant'], $name);	
			}
			else
			{
				$cache->saveEnchant($result, $this->reagents);
				$cache->close();
				return $this->toHTML($result);	
			}
		}
		else
		{
			$this->reagents = $cache->getEnchantReagents($result['id'], $this->lang);
			$cache->close();
			return $this->toHTML($result);	
		}
	}
	
	/**
	 * Get Enchant By ID
	 * @access public
	 * @param int $id
	 * @return array
	 */
	private function getEnchantByID($id)
	{
		if (!is_numeric($id))
			return false;
			
		$data = $this->readURL($id, 'enchant', false);
		// failed to get data, or spell not found
		if (!$data || $data == '$WowheadPower.registerSpell')
			return false;
			
		// next we need to get the string so we can extract the name
		switch ($this->lang)
		{
			case 'de':	$str = 'dede'; break;	// german
			case 'fr':	$str = 'frfr'; break;	// french
			case 'en':	$str = 'enus'; break;	// english
			case 'ru':	$str = 'ruru'; break;	// russian
			case 'es':	$str = 'eses'; break;	// spanish
			default: break;	
		}
		
		if (!preg_match('#name_' . $str . ': \'(.+?)\',#s', $data, $match))
			return false;	// failed to pull the name
		else
		{
			$result = array(
				'id'			=>	(int)$id,
				'name'			=>	$match[1],
				'search_name'	=>	$id,
				'lang'			=>	$this->lang
			);

			// now pull the reagents
			while (preg_match('#<a href="/\?item=([0-9]{1,10})">(.+?)</a>(&nbsp;\([0-9]{1,2}\))?#s', $data, $match))
			{
				// extract each reagent
				$iData = $this->readURL($match[1], 'item');
				
				if (!$iData)
					return false;
					
				if (!$xml = simplexml_load_string($iData, 'SimpleXMLElement', LIBXML_NOCDATA))
					return false;
					
				$match[3] = (isset($match[3])) ? str_replace('&nbsp;(', '', str_replace(')', '', $match[3])) : '';
					
				$this->reagents[] = array(
					'id'		=>	(int)$xml->item['id'],
                    'name'		=>	(string)$xml->item->name,
                    'icon'		=>	'http://static.wowhead.com/images/wow/icons/tiny/' . strtolower($xml->item->icon) . '.gif',
                    'lang'		=>	$this->lang,
					'quality'	=>	(int)$xml->item->quality['id'],
					'quantity'	=>	(trim($match[3]) != '') ? (int)$match[3] : 1,
					'reagentof'	=>	(int)$result['id']
                );
				unset($xml);
				
				$data = str_replace($match[0], '', $data);
			}
			return $result;
		}
	}
	
	/**
	 * Get Enchant By Name
	 * @access public
	 * @param string $name
	 * @return array
	 */
	private function getEnchantByName($name)
	{
		// make sure "enchant" is the first word.
		if (substr(strtolower($name), 0, 7) != 'enchant')
			$name = 'Enchant ' . $name;
			
		$data = $this->readURL($name, 'enchant', false);

		if (!$data)
			return false;

		// for searches with only one result (aka redirect header)
		if (preg_match('#Location: \/spell=([0-9]{1,10})#s', $data, $match))
		{
			// pull the enchant's page
			$spell_data = $this->readURL($match[1], 'spell', false, false, true);
			
			$result = array(
				'id'			=>	$match[1],
				'name'			=>	ucwords($name),
				'search_name'	=>	$name,
				'lang'			=>	$this->lang
			);
			
			if (!$spell_data)
				return false;
			
			// we need to get the reagents
			$lines = explode(chr(10), $spell_data);
			$reagent_list = array();
			foreach ($lines as $line)
			{
				if (preg_match('#<th align="right" id="iconlist-icon([1-9]{1})">#s', $line))
				{
					$reagent_list[] = $line;
				}
			}
			
			if (sizeof($reagent_list) == 0)
				return false;
			else
			{
				foreach ($reagent_list as $line)
				{
					preg_match('#<a href="\/item=([0-9]{1,10})">(.+?)</a></span>(.+?)?</td></tr>#s', $line, $match);
					$item_data = $this->readURL($match[1], 'item');
					if (!$item_data || !$xml = @simplexml_load_string($item_data, 'SimpleXMLElement', LIBXML_NOCDATA))
						return false;
					$this->reagents[] = array(
						'id'		=>	(int)$xml->item['id'],
	                    'name'		=>	(string)$xml->item->name,
	                    'icon'		=>	'http://static.wowhead.com/images/wow/icons/tiny/' . strtolower($xml->item->icon) . '.gif',
	                    'lang'		=>	$this->lang,
						'quality'	=>	(int)$xml->item->quality['id'],
						'quantity'	=>	(array_key_exists(3, $match)) ? (int)substr($match[3], strpos($match[3], '(') +1, 1) : 1,
						'reagentof'	=>	(int)$result['id']
	                );
	                unset($item_data, $xml);				
				}
				
				if (sizeof($this->reagents) == 0)
					return false;
				else
					return $result;
			}
		}
			
		$line = $this->enchantLine($data);

		if (!$json = json_decode($line, true))
		{
			return false;	
		}
		else
		{
			foreach ($json as $enchant)
			{
				if (substr($enchant['name'], 0, 1) == '@')
					$enchant['name'] = substr($enchant['name'], 1);
					
				if (stripslashes(strtolower($enchant['name'])) == stripslashes(strtolower($name)))
				{
					// build result array
					$result = array(
						'id'			=>	$enchant['id'],
						'name'			=>	$enchant['name'],
						'search_name'	=>	$name,
						'lang'			=>	$this->lang
					);
					// now extract the reagents
					foreach ($enchant['reagents'] as $item)
					{
						// loop through and extract each reagent, then query wowhead for the info we need
						$iData = $this->readURL($item[0], 'item');
						
						if (!$iData)
							return false;	// fail
							
						if (!$xml = @simplexml_load_string($iData, 'SimpleXMLElement', LIBXML_NOCDATA))
							return false;
						
						$this->reagents[] = array(
							'id'		=>	(int)$xml->item['id'],
		                    'name'		=>	(string)$xml->item->name,
		                    'icon'		=>	'http://static.wowhead.com/images/wow/icons/tiny/' . strtolower($xml->item->icon) . '.gif',
		                    'lang'		=>	$this->lang,
							'quality'	=>	(int)$xml->item->quality['id'],
							'quantity'	=>	(int)$item[1],
							'reagentof'	=>	(int)$result['id']
		                );
						unset($xml);
					}
					return $result;
				}
			}
			return false;
		}
	}
	
	/**
	 * Enchant Data from HTML
	 * @access public
	 * @param string $data
	 * @return string
	 */
	private function enchantLine($data)
	{
		$parts = explode(chr(10), $data);
		foreach ($parts as $line)
		{
			if (strpos($line, "new Listview({template: 'spell', id: 'professions',") !== false)
			{
				$line = substr($line, strpos($line, 'data: [{') + 6);
				$line = str_replace('});', '', $line);
				return $line;
				break;	
			}
		}
	}
	
	/**
	 * Generates HTML from Wildcards
	 * @access public
	 * @param array $enchant
	 * @return string
	 */
	private function toHTML($enchant)
	{
		$reagent_html = $html = '';
		// first build the reagents
		foreach ($this->reagents as $reagent)
		{
			$temp = $this->patterns->pattern('enchant_mats');
			$temp = str_replace('{link}', $this->generateLink($reagent['id'], 'item'), $temp);
			$temp = str_replace('{icon}', stripslashes($reagent['icon']), $temp);
			$temp = str_replace('{quality}', $reagent['quality'], $temp);
			$temp = str_replace('{count}', $reagent['quantity'], $temp);
			$temp = str_replace('{name}', $reagent['name'], $temp);
			$reagent_html .= $temp;
		}
		
		// now build the main html
		$html = $this->patterns->pattern('enchant');
		$html = str_replace('{link}', $this->generateLink($enchant['id'], 'spell'), $html);
		$html = str_replace('{name}', stripslashes($enchant['name']), $html);
		$html = str_replace('{mats}', $reagent_html, $html);
		
		return $html;
	}
}
?>
