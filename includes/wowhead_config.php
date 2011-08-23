<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_config.php 4.0
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

require_once(dirname(__FILE__) . '/wowhead_sql.php');

/**
 * Wowhead Config Module
 * @package Wowhead Tooltips
 */
class wowhead_config
{
	/**
	 * MySQL Connection
	 * @var object $sql
	 */
	private $sql;
	
	/**
	 * Connection Boolean
	 * @var bool $connected
	 */
	private $connected = false;
	
	/**
	 * MySQL Table Prefix
	 * @var string $prefix
	 */
	private $prefix;
	
	/**
	 * MySQL Table Names
	 * @var array $tables
	 */
	public $tables = array(		// sql table names
		'achievement',
		'armory',
		'class',
		'config',
		'craftable',
		'craftable_reagent',
		'craftable_spell',
		'currency',
		'enchant',
		'enchant_reagent',
		'event',
		'faction',
		'gearlist',
		'guild',
		'item',
		'itemset',
		'itemset_reagent',
		'log',
		'npc',
		'object',
		'pet',
		'profession',
		'quest',
		'race',
		'recruit',
		'rss',
		'spell',
		'stats',
		//'talents',
		'title',
		'talent_names',
		'zones'
	);
	
	/**
	 * Default configuration settings
	 * @var array $defaults
	 */
	public $defaults = array(
		'lang'					=>	'en',
		'external_css'			=>	'false',
		'item_show_icon'		=>	'false',
		'spell_show_icon'		=>	'false',
		'achievement_show_icon'	=>	'false',
		'profile_region'		=>	'us',
		'profile_realm'			=>	'bleeding hollow',
		'armory_region'			=>	'us',
		'armory_realm'			=>	'bleeding hollow',
		'armory_date_format'	=>	'Y-m-d',
		'armory_time_format'	=>	'h:i:s A',
		'armory_item_level'		=>	'false',
		'armory_char_cache'		=>	'60 * 60 * 6',
		'armory_guild_cache'	=>	'60 * 60 * 24',
		'armory_image_url'		=>	'http://www.yoururl.com/wowhead/',
		'armory_icons'			=>	'true',
		'armory_class_icon'		=>	'true',
		'armory_race_icon'		=>	'true',
		'recruit_region'		=>	'us',
		'recruit_realm'			=>	'bleeding hollow',
		'recruit_cache'			=>	'60 * 60 * 6',
		'max_parses'			=>	'0',
		'event_cache'			=>	'60 * 60 * 24 * 14',
		'race_gender'			=>	'female',
		'log_errors'			=>	'true',
		'transfer_map'			=>	'true',
		'qualities'				=>	'',
		// guild rank settings
		'armory_show_rank'		=>	'true',
		'armory_rank_0'			=>	'Guild Master',
		'armory_rank_1'			=>	'Raid Leader',
		'armory_rank_2'			=>	'Officer',
		'armory_rank_3'			=>	'O-Alt',
		'armory_rank_4'			=>	'Raider',
		'armory_rank_5'			=>	'Member',
		'armory_rank_6'			=>	'Recruit',
		'armory_rank_7'			=>	'Alt',
		'armory_rank_8'			=>	'Friend',
		'armory_rank_9'			=>	'Timeout',
		// module settings
		'achievement'			=>	'true',
		'armory'				=>	'true',
		'class'					=>	'true',
		'craft'					=>	'true',
		'currency'				=>	'true',
		'enchant'				=>	'true',
		'event'					=>	'true',
		'faction'				=>	'true',
		'guild'					=>	'true',
		'item'					=>	'true',
		'itemico'				=>	'true',
		'itemset'				=>	'true',
		'npc'					=>	'true',
		'object'				=>	'true',
		'pet'					=>	'true',
		'prof'					=>	'true',
		'profile'				=>	'true',
		'quest'					=>	'true',
		'race'					=>	'true',
		'recruit'				=>	'true',
		'spell'					=>	'true',
		'stats'					=>	'true',
		//'talents'				=>	'true',
		'title'					=>	'true',
		'zone'					=>	'true'
	);
	
	/**
	 * Names of the Config Variables
	 * @var array $config_vars
	 */
	private $config_vars = array(	// the names of the config vars
		'manage_user',
		'manage_pass',
		'lang',
		'external_css',
		'item_show_icon',
		'spell_show_icon',
		'achievement_show_icon',
		'profile_region',
		'profile_realm',
		'armory_region',
		'armory_realm',
		'armory_date_format',
		'armory_time_format',
		'armory_item_level',
		'armory_char_cache',
		'armory_guild_cache',
		'armory_image_url',
		'armory_icons',
		'armory_race_icon',
		'armory_class_icon',
		'armory_show_rank',
		'armory_rank_0',
		'armory_rank_1',
		'armory_rank_2',
		'armory_rank_3',
		'armory_rank_4',
		'armory_rank_5',
		'armory_rank_6',
		'armory_rank_7',
		'armory_rank_8',
		'armory_rank_9',
		'recruit_region',
		'recruit_realm',
		'recruit_cache',
		'max_parses',
		'event_cache',
		'race_gender',
		'transfer_map',
		'log_errors',
		'qualities',
		'achievement',
		'armory',
		'class',
		'craft',
		'config',
		'enchant',
		'event',
		'faction',
		'guild',
		'item',
		'itemico',
		'itemset',
		'npc',
		'object',
		'pet',
		'prof',
		'profile',
		'quest',
		'race',
		'recruit',
		'spell',
		'stats',
		//'talents',
		'title',
		'zone'
	);
	
	/**
	 * Module array
	 * @var array $modules
	 */
	public $modules = array();
	
	/**
	 * Constructor
	 * @param bool $newConnection [optional]
	 * @param array $settings [optional]
	 * @param string $prefix [optional]
	 * @return bool
	 */
	public function __construct($newConnection = false, $settings = array(), $prefix = 'wowhead_')
	{
		if (sizeof($settings) > 0)
			$this->sql = new wowhead_sql($settings['host'], $settings['db'], $settings['user'], $settings['pass']);
		else
			$this->sql = new wowhead_sql(WHP_DB_HOST, WHP_DB_NAME, WHP_DB_USER, WHP_DB_PASS);
			
		$this->connected = $this->sql->connected;
		
		if (!$this->connected)
			return false;
			
		if (!defined('WHP_DB_PREFIX'))
			define('WHP_DB_PREFIX', 'wowhead_');
		$this->prefix = ($prefix != 'wowhead_') ? $prefix : WHP_DB_PREFIX;
	}
	
	/**
	 * Destructor
	 * @return null
	 */
	public function close()
	{
		$this->connected = false;
		unset($this->sql);	
	}
	
	/**
	 * Clear the Config Vars
	 * @return null
	 */
	private function unloadConfig()
	{
		foreach ($this->config_vars as $var)
		{
			if (isset($this->$var))
				unset($this->$var);	
		}
	}
	
	/**
	 * Load Config from MySQL
	 * @param bool $manage [optional]
	 * @return null
	 */
	public function loadConfig($manage = false)
	{
		if (!$this->connected)
			return false;
			
		$this->unloadConfig();	// first unload the config to make sure there's no conflicts
			
		$query_text = "SELECT * FROM `" . $this->prefix . "config` ORDER BY name ASC";
		$query = $this->sql->query($query_text);
		if ($this->sql->num_rows($this->sql->query_id) > 0)
		{
			while ($result = $this->sql->fetch_record($this->sql->query_id))
			{
				if ($result['name'] == 'qualities')
				{
					if (trim($result['setting']) == '')
						$this->$result['name'] = array();
					elseif (strpos($result['setting'], ',') === false)
						$this->$result['name'] = array((int)$result['setting']);
					else
						$this->$result['name'] = explode(',', $result['setting']);	
				}
				elseif ($result['setting'] == 'true')
					$this->$result['name'] = true;
				elseif ($result['setting'] == 'false')
					$this->$result['name'] = false;
				elseif (is_numeric($result['setting']))
					$this->$result['name'] = (int)$result['setting'];
				else
					$this->$result['name'] = $result['setting'];	
			}
	
			$this->sql->free_result($this->sql->query_id);
		}
		// we need to build the modules array
		if (isset($this->armory))
		{
			$this->modules = array(
				'armory'		=>	(bool)$this->armory,
				'achievement'	=>	(bool)$this->achievement,
				'class'			=>	(bool)$this->class,
				'craft'			=>	(bool)$this->craft,
				'currency'		=>	(bool)$this->currency,
				'enchant'		=>	(bool)$this->enchant,
				'event'			=>	(bool)$this->event,
				'faction'		=>	(bool)$this->faction,
				'guild'			=>	(bool)$this->guild,
				'item'			=>	(bool)$this->item,
				'itemico'		=>	(bool)$this->itemico,
				'itemset'		=>	(bool)$this->itemset,
				'npc'			=>	(bool)$this->npc,
				'object'		=>	(bool)$this->object,
				'pet'			=>	(bool)$this->pet,
				'prof'			=>	(bool)$this->prof,
				'profile'		=>	(bool)$this->profile,
				'quest'			=>	(bool)$this->quest,
				'race'			=>	(bool)$this->race,
				'recruit'		=>	(bool)$this->recruit,
				'spell'			=>	(bool)$this->spell,
				'stats'			=>	(bool)$this->stats,
				//'talents'		=>	(bool)$this->talents,
				'title'			=>	(bool)$this->title,
				'zone'			=>	(bool)$this->zone
			);
		}
		
		// convert the cache times to their mathematic result, if necessary
		if ($this->cacheNeedsEval($this->armory_char_cache))	// armory cache
			$this->armory_char_cache = $this->evalFormula($this->armory_char_cache);
		if ($this->cacheNeedsEval($this->armory_guild_cache))	// guild cache
			$this->armory_guild_cache = $this->evalFormula($this->armory_guild_cache);
		if ($this->cacheNeedsEval($this->recruit_cache))		// recruit cache
			$this->recruit_cache = $this->evalFormula($this->recruit_cache);
		if ($this->cacheNeedsEval($this->event_cache))			// event cache
			$this->event_cache = $this->evalFormula($this->event_cache);
		
		if ($manage == true)
		{
			// add the mysql version for shits and giggles
			$this->version['mysql'] = mysql_get_server_info($this->sql->db);
			
			// get the database size
			$this->mysql_dbsize = $this->getdbsize();
			
			// add the script and php version
			$this->version['script'] = WOWHEAD_VERSION;
			$this->version['php'] = PHP_VERSION;
			
			// add total entries
			$this->entries = $this->totalEntries();
			
			// number of SQL tables
			$this->num_tables = sizeof($this->tables);
		}
	}
	
	/**
	 * Add Setting to Config MySQL Table
	 * @param string $name
	 * @param string $setting
	 * @return bool
	 */
	public function addSetting($name, $setting)
	{
		if (!$this->connected)
			return false;
			
		$query_text = "INSERT INTO `" . $this->prefix . "config` VALUES ('{$name}', '" . addslashes($setting) . "') ON DUPLICATE KEY UPDATE setting='" . addslashes($setting) . "'";
		$query = $this->sql->query($query_text);
		if (!$query)
		{
			$error = $this->sql->error();
			$error = $error['message'];
			echo 'Failed to add setting ' . $name . ' to the config table.  ' . $error . '<br /><br />' . $query_text;
		}
	}
	
	/**
	 * Update MySQL Setting
	 * @param string $name
	 * @param string $setting
	 * @return bool
	 */
	public function updateSetting($name, $setting)
	{
		if (!$this->connected)
			return false;
			
		$query_text = "UPDATE `" . $this->prefix . "config` SET setting='" . addslashes($setting) . "' WHERE name='{$name}' LIMIT 1";
		$query = $this->sql->query($query_text);

		if (!$query)
			return false;
		else
			return true;
	}
	
	/**
	 * Add Multiple Settings at Once
	 * @param array $settings
	 * @return bool
	 */
	public function addMassSettings($settings)
	{
		if (!$this->connected)
			return false;
			
		foreach ($settings as $key => $value)
		{
			if ($key != 'wowhead_debug' && $key != 'mysql_host' && $key != 'mysql_user' &&
				$key != 'mysql_pass' && $key != 'mysql_db' && $key != 'mysql_prefix' && $key != 'action' &&
				$key != 'logged_user' && $key != 'logged_pass')
			{
				$query_text = "INSERT INTO `" . $this->prefix . "config` VALUES ('{$key}', '" . addslashes($value) . "') ON DUPLICATE KEY UPDATE setting='" . addslashes($value) . "'";
				$query = $this->sql->query($query_text);
				if (!$query)
				{
					$error = $this->sql->error();
					$error = $error['message'];
					echo 'Failed to add setting ' . $key . ' to the config table.  ' . $error . '<br /><br />' . $query_text;	
				}
			}
		}
	}
	
	/**
	 * Evaluates a mathematic formula stored in a variable.
	 * @param string $val
	 * @return int
	 */
	private function evalFormula($val)
	{
		eval("\$str = " . $val . ";");
		return $str;	
	}
	
	/**
	 * Determines if a cache value needs to be eval'd.
	 * @param string $val
	 * @return bool
	 */
	private function cacheNeedsEval($val)
	{
		if (strpos($val, '+') !== false)		// addition
			return true;
		elseif (strpos($val, '-') !== false)	// subtraction
			return true;
		elseif (strpos($val, '*') !== false)	// multiplication
			return true;
		elseif (strpos($val, '/') !== false)	// division
			return true;
		else									// not needed
			return false;
	}
	
	/**
	 * Get MySQL Database Size
	 * @return string
	 */
	private function getdbsize()
	{
		$dbsize = 0;
		$query_text = 'SHOW TABLE STATUS';
		$query = $this->sql->query($query_text);
		
		while ($row = $this->sql->fetch_record($this->sql->query_id))
		{
			$dbsize += ($row['Data_length'] + $row['Index_length']);
		}
		
		$this->sql->free_result($this->sql->query_id);
		return $this->formatfilesize($dbsize);
	}
	
	/**
	 * Format File Size
	 * @param int $size
	 * @return string
	 */
	private function formatfilesize($size)
	{
		$filesizename = array(" bytes", " kb", " mb", " gb", " tb", " pb", " eb", " zb", " yb");
	  	return round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i];
	}
	
	/**
	 * Get Total Entries in Database
	 * @return int
	 */
	private function totalEntries()
	{
		$total = 0;
		foreach ($this->tables as $table)
		{
			$query_text = "SELECT COUNT(*) FROM `" . WHP_DB_PREFIX . "$table`";
			$query = $this->sql->query($query_text);
			$result = $this->sql->fetch_record($this->sql->query_id);
			$total += (int)$result['COUNT(*)'];
			$this->sql->free_result($this->sql->query_id);
		}
		return $total;
	}
	
	/**
	 * Get Settings From Config Table
	 * @return array
	 */
	public function getSettings()
	{
		if (!$this->connected)
			return false;
		$settings = array();
		$query_text = "SELECT * FROM `" . WHP_DB_PREFIX . "config`";
		$query = $this->sql->query($query_text);
		while ($result = $this->sql->fetch_record($this->sql->query_id))
		{
			$settings[$result['name']] = $result['setting'];
		}
		
		$this->sql->free_result($query);
		return $settings;
	}
}
?>
