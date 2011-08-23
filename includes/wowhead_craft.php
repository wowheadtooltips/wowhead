<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_craft.php 4.0
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

/**
 * Craftable Module
 * @package Wowhead Tooltips
 * @extends wowhead
 */
class wowhead_craft extends wowhead
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
	 * Language Pack
	 * @var object $language
	 */
	public $language;
	
	/**
	 * Config Module
	 * @var object $config
	 */
	public $config;
	
	/**
	 * Created by Array
	 * @var array $createdby
	 */
	private $createdby = array();
	
	/**
	 * Holds Craftable Info
	 * @var array $craft
	 */
	private $craft = array();
	
	/**
	 * Holds Craftable Spell
	 * @var array $craft_spell
	 */
	private $craft_spell = array();
	
	/**
	 * Holds Craftable Reagents
	 * @var array $craft_reagents
	 */
	private $craft_reagents = array();
	
	/**
	 * List the Reagents?
	 * @var bool $nomats
	 */
	private $nomats = false;

	/**
	 * Constructor
	 * @access public
	 * @param object $config
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
	 * Destructor
	 * @access public
	 * @return null
	 */
	public function close()
	{
		unset($this->lang, $this->language, $this->patterns, $this->config);	
	}

	/**
	 * Parse Incoming data
	 * @access public
	 * @param string $name
	 * @param array $args [optional]
	 * @return string
	 */
	public function parse($name, $args = array())
	{
		if (trim($name) == '')
			return false;

		$this->lang = (!array_key_exists('lang', $args)) ? $this->config->lang : $args['lang'];
		$this->language->loadLanguage($this->lang);		// load the language pack
		$this->nomats = (!array_key_exists('nomats', $args)) ? false : $args['nomats'];
		$cache = new wowhead_cache();

		if (!$result = $cache->getCraftable($name, $this->lang))
		{
			$data = $this->readURL($name, 'craftable');

            // accounts for SimpleXML not being able to handle 3 parameters if you're using PHP 5.1 or below.
            if (!$this->allowSimpleXMLOptions())
            {
                $data = $this->_removeCData($data);
                $xml = simplexml_load_string($data, 'SimpleXMLElement');
            }
            else
            {
                $xml = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
            }

            if ($xml->error == '')
            {
                // build our craft array
                $this->craft = array(
                    'itemid'		=>	$xml->item['id'],
                    'name'			=>	$xml->item->name,
                    'search_name'	=>	$name,
                    'quality'		=>	$xml->item->quality['id'],
                    'lang'			=>	$this->lang,
                    'icon'			=>	'http://static.wowhead.com/images/wow/icons/tiny/' . strtolower($xml->item->icon) . '.gif'
                );

                // build spell craft array
                $this->craft_spell = array(
                    'reagentof'		=>	$xml->item['id'],
                    'spellid'		=>	$xml->item->createdBy->spell['id'],
                    'name'			=>	$xml->item->createdBy->spell['name']
                );
				
                if ($this->nomats == false)
                {
                    // build reagent array
                    foreach ($xml->item->createdBy->spell->reagent as $reagent)
                    {
                        array_push($this->craft_reagents, array(
                                'itemid'	=>	$reagent['id'],
                                'reagentof'	=>	$xml->item['id'],
                                'name'		=>	$reagent['name'],
                                'quantity'	=>	$reagent['count'],
                                'quality'	=>	$reagent['quality'],
                                'icon'		=>	'http://static.wowhead.com/images/wow/icons/tiny/' . strtolower($reagent['icon']) . '.gif'
                        ));
                    }
                }
            }
            else
            {
                $cache->close();
                return $this->notFound($this->language->words['craft'], $name);
            }

			if ($this->nomats == false)
			{
				$cache->saveCraftable($this->craft, $this->craft_spell, $this->craft_reagents);
			}
			else
			{
				$cache->saveCraftable($this->craft, $this->craft_spell);
			}
			unset($xml); $cache->close();
			return $this->_toHTML();
		}
		else
		{
			$this->craft = $result;
			$this->craft_spell = $cache->getCraftableSpell($this->craft['itemid']);
			if ($this->nomats == false)
				$this->craft_reagents = $cache->getCraftableReagents($this->craft['itemid']);
			$cache->close();
			return $this->_toHTML();
		}
	}

	/**
	 * Generates HTML for display
	 * @access private
	 * @return string
	 */
	private function _toHTML()
	{
		if ($this->nomats == false)
		{
			// generate spell html first
			$spell_html = $this->patterns->pattern('craftable_spell');
			$spell_html = str_replace('{link}', $this->generateLink($this->craft_spell['spellid'], 'spell'), $spell_html);
			$spell_html = str_replace('{name}', $this->craft_spell['name'], $spell_html);

			// generate reagent html now
			$reagent_html = '';
			foreach ($this->craft_reagents as $reagent)
			{
				$patt = $this->patterns->pattern('craftable_reagents');
				$search = array(
					'{link}'	=>	$this->generateLink($reagent['itemid'], 'item'),
					'{name}'	=>	stripslashes($reagent['name']),
					'{count}'	=>	$reagent['quantity'],
					'{qid}'		=>	$reagent['quality'],
					'{icon}'	=>	$reagent['icon']
				);

				foreach ($search as $key => $value)
					$patt = str_replace($key, $value, $patt);

				$reagent_html .= $patt;
			}

			// finally put it all together
			$craft_html = $this->patterns->pattern('craftable');
			$craft_html = str_replace('{spell}' , $spell_html, $craft_html);
			$craft_html = str_replace('{reagents}', $reagent_html, $craft_html);
			$craft_html = str_replace('{link}', $this->generateLink($this->craft['itemid'], 'item'), $craft_html);
			$craft_html = str_replace('{qid}', $this->craft['quality'], $craft_html);
			$craft_html = str_replace('{name}', stripslashes($this->craft['name']), $craft_html);
		}
		else
		{
			$craft_html = $this->patterns->pattern('craftable_nomats');
			$craft_html = str_replace('{link}', $this->generateLink($this->craft['itemid'], 'item'), $craft_html);
			$craft_html = str_replace('{qid}', $this->craft['quality'], $craft_html);
			$craft_html = str_replace('{name}', stripslashes($this->craft['name']), $craft_html);
			$craft_html = str_replace('{splink}', $this->generateLink($this->craft_spell['spellid'], 'spell'), $craft_html);
			$craft_html = str_replace('{spname}', stripslashes($this->craft_spell['name']), $craft_html);
		}
		return $craft_html;
	}
}
?>
