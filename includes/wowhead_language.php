<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_language.php 4.0
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

require_once(dirname(__FILE__) . '/wowhead.php');

class wowhead_language extends wowhead
{
	public $words = array();
	public $logs;
	private $langs = array();
	private $config;
	
	public function __construct()
	{
		$this->config = new wowhead_config();
		$this->config->loadConfig();
		$this->config->close();
		$lang_dir = dirname(__FILE__) . '/../languages/';
		
		if (!$open = opendir($lang_dir))
			trigger_error('Failed to open ' . $lang_dir . ' for reading.', E_USER_ERROR);
			
		while (false !== ($file = readdir($open)))
		{
			if ($file != 'index.php' && $file != 'lang_test.php' && substr($file, strpos($file, '.') + 1) == 'php')
			{
				$filename = substr($file, 0, strpos($file, '.'));
				require($lang_dir . $file);
				$this->langs[$filename] = $lang_array;
				
			}
		}
		
		closedir($open);
		
		$this->preparePacks();
	}
	
	// encodes the language packs in UTF-8, if they aren't already
	private function preparePacks()
	{
		// loop through each language pack
		foreach ($this->langs as $lang => $pack)
		{
			// loop through each phrase
			foreach ($pack as $key => $phrase)
			{
				if (!$this->isUTF8($phrase))
				{
					$this->langs[$lang][$key] = utf8_encode($phrase);	
				}
			}
		}
	}
	
	public function load($lang = '')
	{
		$this->loadLanguage($lang);
	}
	
	public function loadLanguage($lang = '')
	{
		if (trim($lang) == '')
			$lang = $this->config->lang;

		if (array_key_exists($lang, $this->langs))
			$this->words = $this->langs[$lang];
		else
			$this->words = $this->langs['en'];		// language pack not found, so default to english (en)
	}
}
?>
