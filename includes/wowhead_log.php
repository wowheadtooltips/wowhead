<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_log.php 4.1
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

class wowhead_log
{
	private $sql;
	private $connected = false;

	public function __construct()
	{
		$this->sql = new wowhead_sql(WHP_DB_HOST, WHP_DB_NAME, WHP_DB_USER, WHP_DB_PASS);
		$this->connected = $this->sql->connected;

		if (!$this->connected)
			return false;
	}
	
	public function close()
	{
		$this->sql->close();
		$this->connected = false;	
	}
	
	public function logError($error, $module)
	{
		$stamp = time();
		$page = $this->pageURL();
		$error = addslashes($error);
		$query_text = "INSERT INTO `" . WHP_DB_PREFIX . "log` (id, stamp, entry, module, page) VALUES (null, {$stamp}, '{$error}', '{$module}', '{$page}')";
		if (!$this->sql->query($query_text))
		{
			$error = $this->sql->error();
			$error = $error['message'];
			echo 'Failed to add ' . $info['name'] . ' to the cache. ' . $error . '<br/><br/>' . $query_text;
			return false;
		}
	}
	
	private function pageURL()
	{
		$pageURL = (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
}
?>
