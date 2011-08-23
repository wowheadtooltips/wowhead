<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_patterns.php 4.0
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

class wowhead_patterns
{
	// variable for each pattern
	private $patterns = array();

	public function __construct()
	{
		if (!$opendir = @opendir(dirname(__FILE__) . '/../patterns/'))
		{
			die('Failed to open templates directory.  Please make sure the permissions were set properly.');
		}
		else
		{
			while (false !== ($file = readdir($opendir)))
			{
				if (substr($file, strpos($file, '.') + 1) == 'html')
				{
					$filename = (strpos($file, 'php') !== false) ? str_replace('.php', '', $file) : str_replace('.html', '', $file);
					$this->patterns[$filename] = str_replace(chr(10), '', @file_get_contents(dirname(__FILE__) . '/../patterns/' . $file));
				}
			}
		}

	}

	public function pattern($name)
	{
		return $this->patterns[$name];
	}

	public function close()
	{
		unset($this->patterns);
	}
}
?>
