<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_recruit.php 4.0
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

/**
 * Recruit Module
 * @package Wowhead Tooltips
 */
class wowhead_recruit extends wowhead
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
	 * Language Module
	 * @var object $language
	 */
	public $language;
	
	/**
	 * Config Module
	 * @var object $config
	 */
	public $config;
	
	/**
	 * Armory Realm
	 * @var string $realm
	 */
	private $realm;
	
	/**
	 * Armory Region
	 * @var string $region
	 */
	private $region;
	
	/**
	 * Constructor
	 * @access public
	 * @param object $config
	 * @return null
	 */
	public function __construct()
	{
		$this->config = new wowhead_config();
		$this->config->loadConfig();
		$this->realm = $this->config->recruit_realm;
		$this->region = $this->config->recruit_region;
		$this->lang = $this->config->lang;
		$this->language = new wowhead_language();
	}
	
	/**
	 * Destructor
	 * @return null
	 */
	public function close()
	{
		unset($this->lang, $this->language, $this->patterns, $this->config);	
	}
	
	/**
	 * Parsing Function
	 * @access public
	 * @param string $name
	 * @param array $args [optional]
	 * @return string
	 */
	public function parse($name, $args = array())
	{
		if (trim($name) == '')
			return false;
		
		if (!defined('WHP_RECRUIT_USED'))
		{
			$armory = new wowhead_armory();
			$args['recruit'] = true;
			define('WHP_RECRUIT_USED', true);
			return $armory->parse($name, $args);
		}
		else
		{
			$this->language->loadLanguage($this->lang);
			return $this->generateError($this->language->words['already_used'], 'recruit');	
		}
	}
}
?>