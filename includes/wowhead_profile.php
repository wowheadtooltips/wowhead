<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_profile.php 4.0
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

class wowhead_profile extends wowhead
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
		unset($this->lang, $this->language, $this->patterns, $this->config);	
	}

	public function parse($name, $args = array())
	{
		$this->lang = (array_key_exists('lang', $args)) ? $args['lang'] : $this->config->lang;
		$this->language->loadLanguage($this->lang);

		if (trim($name) == '' || !$this->isUTF8($name))
			return $this->notFound($this->language->words['profile'], $name);

		// see if they specified a region/realm/
		if (array_key_exists('loc', $args))
		{
			$aLoc = explode(',', $args['loc']);
		}
		$region = (array_key_exists('loc', $args)) ? $aLoc[0] : $this->config->profile_region;
		$realm = (array_key_exists('loc', $args)) ? str_replace(" ", "-", $aLoc[1]) : str_replace(" ", "-", $this->config->profile_realm);

		return $this->generateHTML(
			array(
				'link'	=>	$this->genProfileLink(strtolower($name), $region, $realm),
				'name'	=>	ucwords($name)
			), 'profile');
	}

	private function genProfileLink($name, $region = '', $realm = '')
	{
		if (trim($name) == '')
			return false;

		if (trim($region) != '' && trim($realm) != '')
		{
			return "http://www.wowhead.com/profile={$region}.{$realm}.{$name}";
		}
		else
		{
			return "http://www.wowhead.com/profile={$name}";
		}
	}
}

?>
