<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_cache.php 4.3
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

require_once(dirname(__FILE__) . '/wowhead_sql.php');

/**
 * Wowhead Cache Module
 * @package Wowhead Tooltips
 */
class wowhead_cache
{
 	/**
 	 * SQL Connection
 	 * @var object $sql
 	 */
	private $sql;
	
	/**
	 * Connected to SQL
	 * @var bool $connected
	 */
	private $connected = false;
	
	/**
	 * @var string $tables
	 */
	private $tables = array(		// sql table names
		'achievement',
		'armory',
		'class',
		'craftable',
		'craftable_reagent',
		'craftable_spell',
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
		'quest',
		'pet',
		'prof',
		'race',
		'recruit',
		'rss',
		'spell',
		'stats',
		'title',
		'talent_names',
		'zones'
	);

	/**
	 * constructor
	 * @access public
	 * @return null
	 */
	public function __construct()
	{
		$this->sql = new wowhead_sql(WHP_DB_HOST, WHP_DB_NAME, WHP_DB_USER, WHP_DB_PASS);
		$this->connected = $this->sql->connected;

		if (!$this->connected)
			return false;
	}
	
	/**
	 * Clears the cache
	 * @access public
	 * @return null
	 */
	public function clearCache()
	{
		if (!$this->connected)
			return false;
		
		foreach ($this->tables as $table)
		{
			$table = WHP_DB_PREFIX . $table;
			$query_text = "TRUNCATE TABLE `{$table}`";
			if (!$this->sql->query($query_text))
			{
				$error = $this->sql->error();
				echo 'Failed to clear table <tt>' . $table . '</tt>.<br />';
			}
			else
				echo 'Cleared table <tt>' . $table . '</tt>.<br />';
		}
	}

	/**
	 * Destructor
	 * @access public
	 * @return null
	 */
	public function close()
	{
		$this->connected = false;
		$this->sql->close();
		unset($this->sql);
	}

	/**
	 * Save Craftable
	 * @access public
	 * @param array $craft
	 * @param array $craft_spell
	 * @param array $craft_reagents [optional]
	 * @return bool
	 */
	public function saveCraftable($craft, $craft_spell, $craft_reagents = array())
	{
		if (!$this->connected || !is_array($craft) || !is_array($craft_spell))
			return false;

		// save the main craftable entry
		$query_text = "INSERT INTO " . WHP_DB_PREFIX . "craftable VALUES ('" . $craft['itemid'] . "', '" . addslashes($craft['name']) . "', '" . addslashes($craft['search_name']) . "', " . $craft['quality'] . ", '" . $craft['lang'] . "', '" . $craft['icon'] . "')";
		$result = $this->sql->query($query_text);
		if (!$result)
		{
			$error = $this->sql->error();
			echo 'Failed to add ' . $craft['name'] . ' to the cache. ' . $error['message'] . '<br/><br/>' . $query_text;
			return false;
		}


		// now save the spell used to create it
		$query_text = "INSERT INTO " . WHP_DB_PREFIX . "craftable_spell VALUES (" . $craft_spell['reagentof'] . ", " . $craft_spell['spellid'] . ", '" . addslashes($craft_spell['name']) . "')";
		$result = $this->sql->query($query_text);
		if (!$result)
		{
			$error = $this->sql->error();
			echo 'Failed to add ' . $craft['name'] . ' to the cache. ' . $error['message'] . '<br/><br/>' . $query_text;
			return false;
		}

		if (sizeof($craft_reagents) > 0)
		{
			// now save the reagents
			foreach ($craft_reagents as $reagent)
			{
				$itemid = $reagent['itemid'];
				$reagentof = $reagent['reagentof'];
				$name = addslashes($reagent['name']);
				$quantity = $reagent['quantity'];
				$quality = $reagent['quality'];
				$icon = $reagent['icon'];

				$query_text = "INSERT INTO " . WHP_DB_PREFIX . "craftable_reagent VALUES ($itemid, $reagentof, '$name', $quantity, $quality, '$icon')";
				$result = $this->sql->query($query_text);
				if (!$result)
				{
					$error = $this->sql->error();
					echo 'Failed to add ' . $craft['name'] . ' to the cache. ' . $error['message'] . '<br/><br/>' . $query_text;
					return false;
					break;
				}
			}
		}
	}
	
	/**
	 * Save Enchant
	 * @access public
	 * @param array $info
	 * @param array $reagents
	 * @return bool
	 */
	public function saveEnchant($info, $reagents)
	{
		if (sizeof($info) == 0 || sizeof($reagents) == 0 || !$this->connected)
			return false;
			
		$query_text = "INSERT INTO `" . WHP_DB_PREFIX . "enchant` VALUES (" . $info['id'] . ", '" . addslashes($info['name']) . "', '" . addslashes($info['search_name']) . "', '" . $info['lang'] . "')";
		$result = $this->sql->query($query_text);
		if (!$result)
		{
			$error = $this->sql->error();
			$error = $error['message'];
			echo 'Failed to add ' . $info['name'] . ' to the cache.  ' . $error . '<br /><br />' . $query_text;
			return false;
		}
		else
		{
			// now save the reagents
			foreach ($reagents as $reagent)
			{
				$query_text = "INSERT INTO `" . WHP_DB_PREFIX . "enchant_reagent` VALUES (" . $reagent['id'] . ", " . $info['id'] . ", '" . addslashes($reagent['name']) . "', " . $reagent['quantity'] . ", " . $reagent['quality'] . ", '" . addslashes($reagent['icon']) . "', '" . $reagent['lang'] . "')";
				$result = $this->sql->query($query_text);
				if (!$result)
				{
					$error = $this->sql->error();
					$error = $error['message'];
					echo 'Failed to add ' . $reagent['name'] . ' to the reagent cache.  ' . $error . '<br /><br />' . $query_text;
					return false;
					break;	
				}
			}
		}
	}

	/**
	 * Save Guild
	 * @access public
	 * @param array $info
	 * @return bool
	 */
	public function saveGuild($info)
	{
		if (sizeof($info) == 0 || !$this->connected)
			return false;
		$time = time();
		$query_text = "INSERT INTO `". WHP_DB_PREFIX . "guild` (
							`uniquekey`,
							`name`,
							`realm`,
							`region`,
							`tooltip`,
							`cache`
						)
						VALUES (
							'" . $info['uniquekey'] . "',
							'" . $info['name'] . "',
							'" . addslashes($info['realm']) . "',
							'" . $info['region'] . "',
							'" . addslashes($info['tooltip']) . "',
							UNIX_TIMESTAMP(NOW())
						)
						ON DUPLICATE KEY UPDATE
							tooltip='" . addslashes($info['tooltip']) . "',
							cache=$time";
		$result = $this->sql->query($query_text);
		if (!$result)
		{
			$error = $this->sql->error();
			$error = $error['message'];
			echo 'Failed to add ' . $info['name'] . ' to the cache. ' . $error . '<br/><br/>' . $query_text;
			return false;
		}
	}

	/**
	 * Save Armory
	 * @access public
	 * @param array $info
	 * @return bool
	 */
	public function saveArmory($info)
	{
		if (sizeof($info) == 0 || !$this->connected)
			return false;

		// unix timestamp for our cache
		$time = time();
		$query_text = "INSERT INTO `" . WHP_DB_PREFIX . "armory` (
							`uniquekey`,
							`name`,
							`class`,
							`raceid`,
							`classid`,
							`genderid`,
							`realm`,
							`region`,
							`tooltip`,
							`cache`
						)
						VALUES (
							'" . $info['uniquekey'] . "',
							'" . $info['name'] . "',
							'" . $info['class'] . "',
							" . $info['raceid'] . ",
							" . $info['classid'] . ",
							" . $info['genderid'] . ",
							'" . addslashes($info['realm']) . "',
							'" . $info['region'] . "',
							'" . addslashes($info['tooltip']) . "',
							$time
						)
						ON DUPLICATE KEY UPDATE
							tooltip='" . addslashes($info['tooltip']) . "',
							cache=$time";
		$result = $this->sql->query($query_text);

		if (!$result)
		{
			$error = $this->sql->error();
			$error = $error['message'];
			echo 'Failed to add ' . $info['name'] . ' to the cache. ' . $error . '<br/><br/>' . $query_text;
			return false;
		}
	}

	/**
	 * Save NPC
	 * @access public
	 * @param array $info
	 * @return bool
	 */
	public function saveNPC($info)
	{
		if (sizeof($info) == 0 || !$this->connected)
			return false;

		$query_text = "INSERT INTO " . WHP_DB_PREFIX . "npc VALUES (" . (int)$info['npcid'] . ", '" . addslashes($info['name']) . "', '" . addslashes($info['search_name']) . "', '" . $info['lang'] . "')";

		$result = $this->sql->query($query_text);

		if (!$result)
		{
			$error = $this->sql->error();
			$error = $error['message'];
			echo 'Failed to add ' . $info['name'] . ' to the cache. ' . $error . '<br/><br/>' . $query_text;
			return false;
		}
	}

	/**
	 * Save Zone
	 * @access public
	 * @param array $info
	 * @return bool
	 */	
	public function saveZone($info)
	{
		if (sizeof($info) == 0 || !$this->connected)
			return false;
			
		$query_text = "INSERT INTO `" . WHP_DB_PREFIX . "zones` VALUES (" . (int)$info['id'] . ", '" . addslashes($info['name']) . "', '" . addslashes($info['search_name']) . "', '" . addslashes($info['map']) . "', '" . $info['lang'] . "')";
		$result = $this->sql->query($query_text);
		if (!$result)
		{
			$error = $this->sql->error();
			$error = $error['message'];
			echo 'Failed to add ' . $info['name'] . ' to the cache.  ' . $error . '<br /><br />' . $query_text;
			return false;	
		}
	}

	/**
	 * Saves itemset
	 * @access public
	 * @param array $itemset
	 * @param array $items
	 * @return bool
	 */
	public function saveItemset($itemset, $items)
	{
		if (!$this->connected || !is_array($itemset) || !is_array($items))
			return false;

		$setid = $itemset['setid'];
		$name = $itemset['name'];
		$search_name = $itemset['search_name'];
		$lang = $itemset['lang'];
		$heroic = (array_key_exists('heroic', $itemset)) ? 1 : 0;

		// save the itemset first, then we'll handle each item
		$query_text = "INSERT INTO " . WHP_DB_PREFIX . "itemset VALUES ($setid, '" . addslashes($name) . "', $heroic, '" . addslashes($search_name) . "', '$lang')";

		$result = $this->sql->query($query_text);

		if (!$result)
		{
			$error = $this->sql->error();
			$error = $error['message'];
			echo 'Failed to add ' . $itemset['name'] . ' to the cache. ' . $error . '<br/><br/>' . $query_text;
			return false;
		}
		else
		{
			// success, now save the items

			// check to make sure the reagents aren't already in the database
			$check = $this->sql->query("SELECT itemid FROM " . WHP_DB_PREFIX . "itemset_reagent WHERE setid='$setid' AND name='" . addslashes($name) . "' LIMIT 1");

			if ($this->sql->num_rows($this->sql->query_id) == 0)
			{
				// not yet in the cache
				foreach ($items as $item)
				{
					$name = $item['name'];
					$itemid = $item['itemid'];
					$quality = $item['quality'];
					$icon = $item['icon'];
					$query_text = "INSERT INTO " . WHP_DB_PREFIX . "itemset_reagent VALUES ($setid, $itemid, '" . addslashes($name) . "', $quality, '" . addslashes($icon) . "')";
					$result = $this->sql->query($query_text);

					if (!$result)
					{
						$error = $this->sql->error();
						$error = $error['message'];
						echo 'Failed to add ' . $name . ' to the cache. ' . $error . '<br/><br/>' . $query_text;
						return false;
						break;
					}
				}
			}
		}
	}

	/**
	 * Generate Unique Key for Armory and Guild
	 * @access public
	 * @param string $name
	 * @param string $realm
	 * @param string $region
	 * @return bool
	 */
	public function generateKey($name, $realm, $region)
	{
		$name = strtolower(str_replace(' ', '', $name));
		$realm = strtolower(str_replace(' ', '', $realm));
		$region = strtolower($region);
		return md5($name . $realm . $region);
	}

	/**
	 * Grab Armory
	 * @access public
	 * @param string $uniquekey
	 * @param string $max_age
	 * @return array
	 */
	public function getArmory($uniquekey)
	{
		if ($this->connected == false)
			return false;

		$query_text = 'SELECT
							name,
							class,
							raceid,
							classid,
							genderid,
							tooltip,
							cache
						FROM
							' . WHP_DB_PREFIX . 'armory
						WHERE
							uniquekey=\'' . $uniquekey . '\'
						LIMIT 1';

		$this->sql->query($query_text);
		if ($this->sql->num_rows($this->sql->query_id) == 0)
		{
			$this->sql->free_result($this->sql->query_id);
			return false;
		}
		else
		{
			return $this->sql->fetch_record($this->sql->query_id);
		}
	}

	/**
	 * Update Armory Cache Time
	 * @access public
	 * $param string $uniquekey
	 */
	public function updateArmoryCacheTime($uniquekey)
	{
		if ($this->connected == false)
			return false;

		// unix timestamp for our cache
		$time = time();
		$query_text = "UPDATE " . WHP_DB_PREFIX . "armory
		       	SET cache = " . $time . "
			WHERE uniquekey = '" . $uniquekey . "';";
		$result = $this->sql->query($query_text);

		if (!$result)
		{
			$error = $this->sql->error();
			$error = $error['message'];
			echo 'Failed to update cache time for ' . $uniquekey . '. ' . $error . '<br/><br/>' . $query_text;
			return false;
		}
	}

	/**
	 * Grab Guild
	 * @access public
	 * @param string $uniquekey
	 * @param string $max_age
	 * @return array
	 */
	public function getGuild($uniquekey)
	{
		if ($this->connected == false)
			return false;
		$query_text = 'SELECT
							tooltip,
							cache
						FROM
							' . WHP_DB_PREFIX . 'guild
						WHERE
							uniquekey=\'' . $uniquekey . '\'
						LIMIT 1';

		$this->sql->query($query_text);
		if ($this->sql->num_rows($this->sql->query_id) == 0)
		{
			$this->sql->free_result($this->sql->query_id);
			return false;
		}
		else
		{
			return $this->sql->fetch_record($this->sql->query_id);
		}
	}
	
	/**
	 * Update Guild Cache Time
	 * @access public
	 * $param string $uniquekey
	 */
	public function updateGuildCacheTime($uniquekey)
	{
		if ($this->connected == false)
			return false;

		// unix timestamp for our cache
		$time = time();
		$query_text = "UPDATE " . WHP_DB_PREFIX . "guild
		       	SET cache = " . $time . "
			WHERE uniquekey = '" . $uniquekey . "';";
		$result = $this->sql->query($query_text);

		if (!$result)
		{
			$error = $this->sql->error();
			$error = $error['message'];
			echo 'Failed to update cache time for ' . $uniquekey . '. ' . $error . '<br/><br/>' . $query_text;
			return false;
		}
	}
	
	/**
	 * Grab Zone
	 * @access public
	 * @param string $name
	 * @param string $lang
	 * @param bool $external [optional]
	 * @return array
	 */
	public function getZone($name, $lang, $external = false)
	{
		if ($this->connected == false)
			return false;
		if (!$external)
		{
			$query_text = 'SELECT
								`id`,
								`name`,
								`map`,
								`lang`
							FROM
								`' . WHP_DB_PREFIX . 'zones`
							WHERE
								(
									search_name LIKE \'' . addslashes($name) . '\'
										OR
									name LIKE \'' . addslashes($name) . '\'
								)
								AND lang=\'' . $lang . '\'
							LIMIT 1';
		}
		else
		{
			$query_text = 'SELECT
								`id`,
								`name`,
								`map`,
								`lang`
							FROM
								`' . WHP_DB_PREFIX . 'zones`
							WHERE
								id=' . $name . '
									AND
								lang=\'' . $lang . '\'
							LIMIT 1';	
		}
		$this->sql->query($query_text);
		if ($this->sql->num_rows($this->sql->query_id) == 0)
		{
			$this->sql->free_result($this->sql->query_id);
			return false;
		}
		else
		{
			return $this->sql->fetch_record($this->sql->query_id);
		}
	}

	/**
	 * Grab NPC
	 * @access public
	 * @param string $name
	 * @param string $lang
	 * @return array
	 */
	public function getNPC($name, $lang)
	{
		if ($this->connected == false)
			return false;

		if (trim($lang) == '')
			$lang = WHP_LANG;

		$query_text = 'SELECT
							npcid,
							name
						FROM
							' . WHP_DB_PREFIX . 'npc
						WHERE
							(
								search_name LIKE \'' . addslashes($name) . '\'
									OR
								name LIKE \'' . addslashes($name) . '\'
									OR
								npcid LIKE \'' . addslashes($name) . '\'
							)
							AND lang=\'' . $lang . '\'
		';
		$this->sql->query($query_text);
		if ($this->sql->num_rows($this->sql->query_id) == 0)
		{
			$this->sql->free_result($this->sql->query_id);
			return false;
		}
		else
		{
			return $this->sql->fetch_record($this->sql->query_id);
		}
	}

	/**
	 * Grab Itemset
	 * @access public
	 * @param string $name
	 * @param string $lang
	 * @return array
	 */
	public function getItemset($name, $lang, $heroic = 0)
	{
		if ($this->connected == false)
			return false;

		if (trim($lang) == '')
			$lang = WHP_LANG;

		$query_text = 'SELECT
							setid,
							name
						FROM
							' . WHP_DB_PREFIX . 'itemset
						WHERE
							(
								search_name LIKE \'' . addslashes($name) . '\'
									OR
								setid LIKE \'' . addslashes($name) . '\'
									OR
								name LIKE \'' . addslashes($name) . '\'
							)
							AND lang=\'' . $lang . '\'
							AND heroic=' . $heroic . ' LIMIT 1';
		$this->sql->query($query_text);
		if ($this->sql->num_rows($this->sql->query_id) == 0)
		{
			// not found
			$this->sql->free_result($this->sql->query_id);
			return false;
		}
		else
		{
			return $this->sql->fetch_record($this->sql->query_id);
		}
	}

	/**
	 * Grab Craftable
	 * @access public
	 * @param string $name
	 * @param string $lang
	 * @return array
	 */
	public function getCraftable($name, $lang)
	{
		if ($this->connected == false)
			return false;
		if (trim($lang) == '')
			$lang = WHP_LANG;
		$name = addslashes($name);
		$query_text = "SELECT itemid, name, quality, icon FROM `" . WHP_DB_PREFIX . "craftable` WHERE (search_name LIKE '{$name}' OR itemid LIKE '{$name}' OR name LIKE '{$name}') AND lang='{$lang}'";
		$this->sql->query($query_text);
		if ($this->sql->num_rows($this->sql->query_id) == 0)
		{
			$this->sql->free_result($this->sql->query_id);
			return false;
		}
		else
			return $this->sql->fetch_record($this->sql->query_id);
	}

	/**
	 * Grab Craftable Spell
	 * @access public
	 * @param int $id
	 * @return array
	 */
	public function getCraftableSpell($id)
	{
		if (!$this->connected || trim($id) == '')
			return false;
		$this->sql->query("SELECT spellid, name FROM " . WHP_DB_PREFIX . "craftable_spell WHERE reagentof='$id'");
		if ($this->sql->num_rows($this->sql->query_id) == 0)
		{
			$this->sql->free_result($this->sql->query_id);
			return false;
		}
		else
			return $this->sql->fetch_record($this->sql->query_id);
	}
	
	/**
	 * Grab Enchant
	 * @access public
	 * @param string $name
	 * @param string $lang
	 * @return array
	 */
	public function getEnchant($name, $lang)
	{
		if (trim($name) == '' || !$this->connected)
			return false;
		$name = addslashes($name);
		if (!is_numeric($name))
			$query_text = "SELECT id, name FROM `" . WHP_DB_PREFIX . "enchant` WHERE (name LIKE '{$name}' OR search_name LIKE '{$name}') AND lang='{$lang}' LIMIT 1";
		else
			$query_text = "SELECT id, name FROM `" . WHP_DB_PREFIX . "enchant` WHERE id={$name} AND lang='{$lang}' LIMIT 1";
		$this->sql->query($query_text);
		return ($this->sql->num_rows($this->sql->query_id) == 0) ? false : $this->sql->fetch_record($this->sql->query_id);
	}
	
	/**
	 * Get Enchant Reagents
	 * @access public
	 * @param int $id
	 * @param string $lang
	 * @return array
	 */
	public function getEnchantReagents($id, $lang)
	{
		if (trim($id) == '' || !$this->connected)
			return false;
		$this->sql->query("SELECT id, name, quantity, quality, icon FROM `" . WHP_DB_PREFIX . "enchant_reagent` WHERE reagentof={$id} AND lang='{$lang}' ORDER BY name ASC");
		if ($this->sql->num_rows($this->sql->query_id) == 0)
		{
			$this->sql->free_result($this->sql->query_id);
			return false;	
		}
		else
		{
			$result = array();
			while ($temp = $this->sql->fetch_record($this->sql->query_id))
				$result[] = $temp;
			return $result;
		}
	}

	/**
	 * Grab Craftable Reagents
	 * @access public
	 * @param int $id
	 * @return array
	 */
	public function getCraftableReagents($id)
	{
		if (!$this->connected || trim($id) == '')
			return false;
		$result = array();
		$this->sql->query("SELECT itemid, name, quantity, quality, icon FROM " . WHP_DB_PREFIX . "craftable_reagent WHERE reagentof={$id} ORDER BY name ASC");
		if ($this->sql->num_rows($this->sql->query_id) == 0)
		{
			$this->sql->free_result($this->sql->query_id);
			return false;
		}
		else
		{
			while ($temp = $this->sql->fetch_record($this->sql->query_id))
				array_push($result, $temp);

			$this->sql->free_result($this->sql->query_id);
			return $result;
		}
	}

	/**
	 * Grab Itemset Reagents
	 * @access public
	 * @param int $id
	 * @return array
	 */
	public function getItemsetReagents($id)
	{
		if (!$this->connected)
			return false;
		$result = array();
		$query_text = "SELECT itemid, name, quality, icon FROM `" . WHP_DB_PREFIX . "itemset_reagent` WHERE setid={$id} ORDER BY name ASC";
		$this->sql->query($query_text);
		if ($this->sql->num_rows($this->sql->query_id) == 0)
		{
			$this->sql->free_result($this->sql->query_id);
			return false;
		}
		else
		{
			while ($temp = $this->sql->fetch_record($this->sql->query_id))
			{
				array_push($result, $temp);
			}
			$this->sql->free_result($this->sql->query_id);
			return $result;
		}
	}
	
	public function getObject($name, $lang)
	{
		if (trim($name) == '' || !$this->connected)
			return false;
		$name = addslashes($name);
		$query_text = "SELECT itemid, name FROM " . WHP_DB_PREFIX . "object WHERE (search_name LIKE '{$name}' AND lang='{$lang}') LIMIT 1";
		$this->sql->query($query_text);
		return ($this->sql->num_rows($this->sql->query_id) == 0) ? false : $this->sql->fetch_record($this->sql->query_id);	
	}
	
	public function saveObject($info)
	{
		if (sizeof($info) == 0 || !$this->connected)
			return false;
		$info = $this->prepareInfoArray($info);
		$query_text = "INSERT INTO `" . WHP_DB_PREFIX . "object` (itemid, name, search_name, lang) VALUES ({$info['itemid']}, '{$info['name']}', '{$info['search_name']}', '{$info['lang']}')";	
		if (!$this->sql->query($query_text))
		{
			$error = $this->sql->error();
			$error = $error['message'];
			echo 'Failed to add ' . $info['name'] . ' to the cache. ' . $error . '<br/><br/>' . $query_text;
			return false;
		}
	}
	
	public function getQuest($name, $lang)
	{
		if (trim($name) == '' || !$this->connected)
			return false;
		$name = addslashes($name);
		$query_text = "SELECT itemid, name FROM " . WHP_DB_PREFIX . "quest WHERE (search_name LIKE '{$name}' AND lang='{$lang}') LIMIT 1";
		$this->sql->query($query_text);
		return ($this->sql->num_rows($this->sql->query_id) == 0) ? false : $this->sql->fetch_record($this->sql->query_id);			
	}
	
	public function saveQuest($info)
	{
		if (sizeof($info) == 0 || !$this->connected)
			return false;
		$info = $this->prepareInfoArray($info);
		$query_text = "INSERT INTO `" . WHP_DB_PREFIX . "quest` (itemid, name, search_name, lang) VALUES ({$info['itemid']}, '{$info['name']}', '{$info['search_name']}', '{$info['lang']}')";
		if (!$this->sql->query($query_text))
		{
			$error = $this->sql->error();
			$error = $error['message'];
			echo 'Failed to add ' . $info['name'] . ' to the cache. ' . $error . '<br/><br/>' . $query_text;
			return false;
		}
	}
	
	public function saveAchievement($info)
	{
		if (sizeof($info) == 0 || !$this->connected)
			return false;
		$info = $this->prepareInfoArray($info);
		if (!array_key_exists('icon', $info)) { $info['icon'] = ''; }
		$query_text = "INSERT INTO `" . WHP_DB_PREFIX . "achievement` (itemid, name, search_name, icon, lang) VALUES ({$info['itemid']}, '{$info['name']}', '{$info['search_name']}', '{$info['icon']}', '{$info['lang']}')";
		if (!$this->sql->query($query_text))
		{
			$error = $this->sql->error();
			$error = $error['message'];
			echo 'Failed to add ' . $info['name'] . ' to the cache. ' . $error . '<br/><br/>' . $query_text;
			return false;
		}
	}

	public function getAchievement($name, $lang)
	{
		if (trim($name) == '' || !$this->connected)
			return false;
		$name = addslashes($name);
		$query_text = "SELECT itemid, name, icon FROM " . WHP_DB_PREFIX . "achievement WHERE (search_name LIKE '{$name}' AND lang='{$lang}') LIMIT 1";
		$this->sql->query($query_text);
		return ($this->sql->num_rows($this->sql->query_id) == 0) ? false : $this->sql->fetch_record($this->sql->query_id);
	}
	
	public function saveSpell($info)
	{
		if (sizeof($info) == 0 || !$this->connected)
			return false;
		$info = $this->prepareInfoArray($info);
		// make sure rank is set, even if it wasn't specified
		$info['rank'] = (!array_key_exists('rank', $info) || $info['rank'] == 0) ? 0 : $info['rank'];
		$query_text = "INSERT INTO `" . WHP_DB_PREFIX . "spell` (itemid, name, search_name, icon, rank, lang) VALUES ({$info['itemid']}, '{$info['name']}', '{$info['search_name']}', '{$info['icon']}', {$info['rank']}, '{$info['lang']}')";
		if (!$this->sql->query($query_text))
		{
			$error = $this->sql->error();
			$error = $error['message'];
			echo 'Failed to add ' . $info['name'] . ' to the cache. ' . $error . '<br/><br/>' . $query_text;
			return false;
		}
	}
	
	public function getSpell($name, $lang, $rank = 0)
	{
		if (trim($name) == '' || !$this->connected)
			return false;
		$name = addslashes($name);
		$query_text = "SELECT itemid, name, icon, rank FROM " . WHP_DB_PREFIX . "spell WHERE (search_name LIKE '{$name}' AND lang='{$lang}'";
		if ($rank != 0)
			$query_text .= " AND rank={$rank}) LIMIT 1";
		else
			$query_text .= ") LIMIT 1";
		$this->sql->query($query_text);
		return ($this->sql->num_rows($this->sql->query_id) == 0) ? false : $this->sql->fetch_record($this->sql->query_id);
	}
	
	public function saveStatistic($info)
	{
		if (sizeof($info) == 0 || !$this->connected)
			return false;
		$info = $this->prepareInfoArray($info);
		$query_text = "INSERT INTO `" . WHP_DB_PREFIX . "stats` (id, name, search_name, lang) VALUES ({$info['id']}, '{$info['name']}', '{$info['search_name']}', '{$info['lang']}')";
		if (!$this->sql->query($query_text))
		{
			$error = $this->sql->error();
			$error = $error['message'];
			echo 'Failed to add ' . $info['name'] . ' to the cache. ' . $error . '<br/><br/>' . $query_text;
			return false;
		}
	}
	
	public function getStatistic($name, $lang)
	{
		if (trim($name) == '' || !$this->connected)
			return false;
		$name = addslashes($name);
		$query_text = "SELECT id, name FROM " . WHP_DB_PREFIX . "stats WHERE (search_name LIKE '{$name}' AND lang='{$lang}') LIMIT 1";
		$this->sql->query($query_text);
		return ($this->sql->num_rows($this->sql->query_id) == 0) ? false : $this->sql->fetch_record($this->sql->query_id);
	}
	
	public function getItem($name, $heroic, $lang, $icon = false)
	{
		if (trim($name) == '' || !$this->connected)
			return false;
		$name = addslashes($name);
		$query_text = "SELECT itemid, name, quality, icon FROM " . WHP_DB_PREFIX . "item WHERE (search_name LIKE '{$name}' AND heroic={$heroic} AND lang='{$lang}'";
		$query_text .= (!$icon) ? ") LIMIT 1" : " AND icon != '') LIMIT 1";
		$this->sql->query($query_text);
		return ($this->sql->num_rows($this->sql->query_id) == 0) ? false : $this->sql->fetch_record($this->sql->query_id);
	}
	
	public function saveItem($info)
	{
		if (sizeof($info) == 0 || !$this->connected)
			return false;
		$info = $this->prepareInfoArray($info);
		$info['heroic'] = (array_key_exists('heroic', $info)) ? $info['heroic'] : 0;
		$info['icon'] = (array_key_exists('icon', $info)) ? $info['icon'] : '';
		$query_text = "INSERT INTO `" . WHP_DB_PREFIX . "item` (itemid, name, search_name, heroic, quality, icon, lang) VALUES ({$info['itemid']}, '{$info['name']}', '{$info['search_name']}', {$info['heroic']}, {$info['quality']}, '{$info['icon']}', '{$info['lang']}')";
		if (!$this->sql->query($query_text))
		{
			$error = $this->sql->error();
			$error = $error['message'];
			echo 'Failed to add ' . $info['name'] . ' to the cache. ' . $error . '<br/><br/>' . $query_text;
			return false;
		}
	}
	
	public function getPet($name, $lang)
	{
		if (trim($name) == '' || !$this->connected)
			return false;
		$name = addslashes($name);
		$query_text = "SELECT id, name, tipicon, tooltip FROM `" . WHP_DB_PREFIX . "pet` WHERE (search_name LIKE '{$name}' AND lang='{$lang}') LIMIT 1";
		$this->sql->query($query_text);
		return ($this->sql->num_rows($this->sql->query_id) == 0) ? false : $this->sql->fetch_record($this->sql->query_id);
	}
	
	public function savePet($info)
	{
		if (sizeof($info) == 0 || !$this->connected)
			return false;
		$info = $this->prepareInfoArray($info);
		$query_text = "INSERT INTO `" . WHP_DB_PREFIX . "pet` (id, name, search_name, tipicon, lang, tooltip) VALUES ({$info['id']}, '{$info['name']}', '{$info['search_name']}', '{$info['tipicon']}', '{$info['lang']}', '{$info['tooltip']}')";
		if (!$this->sql->query($query_text))
		{
			$error = $this->sql->error();
			$error = $error['message'];
			echo 'Failed to add ' . $info['name'] . ' to the cache. ' . $error . '<br/><br/>' . $query_text;
			return false;
		}
	}
	
	public function getCurrency($name, $lang)
	{
		if (trim($name) == '' || !$this->connected)
			return false;
		$name = addslashes($name);
		$query_text = "SELECT id, name, icon, currency_for, tooltip FROM `" . WHP_DB_PREFIX . "currency` WHERE (search_name LIKE '{$name}' AND lang='{$lang}') LIMIT 1";
		$this->sql->query($query_text);
		return ($this->sql->num_rows($this->sql->query_id) == 0) ? false : $this->sql->fetch_record($this->sql->query_id);	
	}
	
	public function saveCurrency($info)
	{
		if (sizeof($info) == 0 || !$this->connected)
			return false;
		$info = $this->prepareInfoArray($info);
		$query_text = "INSERT INTO `" . WHP_DB_PREFIX . "currency` (id, name, search_name, icon, lang, currency_for, tooltip) VALUES (%d, '%s', '%s', '%s', '%s', '%s', '%s')";
		$query_text = sprintf($query_text, $info['id'], $info['name'], $info['search_name'], $info['icon'], $info['lang'], $info['currency_for'], $info['tooltip']);
		if (!$this->sql->query($query_text))
		{
			$error = $this->sql->error();
			$error = $error['message'];
			echo 'Failed to add ' . $info['name'] . ' to the cache. ' . $error . '<br/><br/>' . $query_text;
			return false;
		}
	}
	
	public function getClass($name, $lang)
	{
		if (trim($name) == '' || !$this->connected)
			return false;
		$name = addslashes($name);
		$query_text = "SELECT id, name, search_name, icon, tooltip, lang FROM `" . WHP_DB_PREFIX . "class` WHERE (search_name LIKE '{$name}' AND lang='{$lang}') LIMIT 1";
		$this->sql->query($query_text);
		return ($this->sql->num_rows($this->sql->query_id) == 0) ? false : $this->sql->fetch_record($this->sql->query_id);
	}
	
	public function saveClass($info)
	{
		if (sizeof($info) == 0 || !$this->connected)
			return false;
		$info = $this->prepareInfoArray($info);
		$query_text = "INSERT INTO `" . WHP_DB_PREFIX . "class` (id, name, search_name, icon, tooltip, lang) VALUES ({$info['id']}, '{$info['name']}', '{$info['search_name']}', '{$info['icon']}', '{$info['tooltip']}', '{$info['lang']}')";
		if (!$this->sql->query($query_text))
		{
			$error = $this->sql->error();
			$error = $error['message'];
			echo 'Failed to add ' . $info['name'] . ' to the cache. ' . $error . '<br/><br/>' . $query_text;
			return false;
		}		
	}
	
	public function getRace($name, $lang)
	{
		if (trim($name) == '' || !$this->connected)
			return false;
		$name = addslashes($name);
		$query_text = "SELECT id, name, icon, tipicon, tooltip, lang FROM `" . WHP_DB_PREFIX . "race` WHERE (search_name LIKE '{$name}' AND lang='{$lang}') LIMIT 1";
		$this->sql->query($query_text);
		return ($this->sql->num_rows($this->sql->query_id) == 0) ? false : $this->sql->fetch_record($this->sql->query_id);
	}
	
	public function saveRace($info)
	{
		if (sizeof($info) == 0 || !$this->connected)
			return false;
		$info = $this->prepareInfoArray($info);
		$query_text = "INSERT INTO `" . WHP_DB_PREFIX . "race` (id, name, search_name, icon, tipicon, tooltip, lang) VALUES ({$info['id']}, '{$info['name']}', '{$info['search_name']}', '{$info['icon']}', '{$info['tipicon']}', '{$info['tooltip']}', '{$info['lang']}')";
		if (!$this->sql->query($query_text))
		{
			$error = $this->sql->error();
			$error = $error['message'];
			echo 'Failed to add ' . $info['name'] . ' to the cache. ' . $error . '<br/><br/>' . $query_text;
			return false;
		}		
	}
	
	public function getfaction($name, $lang)
	{
		if (trim($name) == '' || !$this->connected)
			return false;
		$name = addslashes($name);
		$query_text = "SELECT id, name, tooltip, lang FROM `" . WHP_DB_PREFIX . "faction` WHERE (search_name LIKE '{$name}' AND lang='{$lang}') LIMIT 1";
		$this->sql->query($query_text);
		return ($this->sql->num_rows($this->sql->query_id) == 0) ? false : $this->sql->fetch_record($this->sql->query_id);
	}
	
	public function saveFaction($info)
	{
		if (sizeof($info) == 0 || !$this->connected)
			return false;
		$info = $this->prepareInfoArray($info);
		$query_text = "INSERT INTO `" . WHP_DB_PREFIX . "faction` (id, name, search_name, tooltip, lang) VALUES ({$info['id']}, '{$info['name']}', '{$info['search_name']}', '{$info['tooltip']}', '{$info['lang']}')";
		if (!$this->sql->query($query_text))
		{
			$error = $this->sql->error();
			$error = $error['message'];
			echo 'Failed to add ' . $info['name'] . ' to the cache. ' . $error . '<br/><br/>' . $query_text;
			return false;
		}
	}
	
	public function getProfession($name, $lang)
	{
		if (trim($name) == '' || !$this->connected)
			return false;
		$name = addslashes($name);
		$query_text = "SELECT id, name, tooltip, lang FROM `" . WHP_DB_PREFIX . "profession` WHERE (search_name LIKE '{$name}' AND lang='{$lang}') LIMIT 1";
		$this->sql->query($query_text);
		return ($this->sql->num_rows($this->sql->query_id) == 0) ? false : $this->sql->fetch_record($this->sql->query_id);	
	}
	
	public function saveProfession($info)
	{
		if (sizeof($info) == 0 || !$this->connected)
			return false;
		$info = $this->prepareInfoArray($info);
		$query_text = "INSERT INTO `" . WHP_DB_PREFIX . "profession` (id, name, search_name, tooltip, lang) VALUES ({$info['id']}, '{$info['name']}', '{$info['search_name']}', '{$info['tooltip']}', '{$info['lang']}')";
		if (!$this->sql->query($query_text))
		{
			$error = $this->sql->error();
			$error = $error['message'];
			echo 'Failed to add ' . $info['name'] . ' to the cache. ' . $error . '<br/><br/>' . $query_text;
			return false;
		}	
	}

	public function getEvent($name, $lang, $max_age)
	{
		if (trim($name) == '' || !$this->connected)
			return false;
		$name = addslashes($name);
		$query_text = "SELECT id, name, tooltip, lang FROM `" . WHP_DB_PREFIX . "event` WHERE (search_name LIKE '{$name}' AND lang='{$lang}' AND cache > UNIX_TIMESTAMP(NOW()) - {$max_age}) LIMIT 1";
		$this->sql->query($query_text);
		return ($this->sql->num_rows($this->sql->query_id) == 0) ? false : $this->sql->fetch_record($this->sql->query_id);
	}
	
	public function saveEvent($info)
	{
		if (sizeof($info) == 0 || !$this->connected)
			return false;
		$info = $this->prepareInfoArray($info);
		$query_text = "INSERT INTO `" . WHP_DB_PREFIX . "event` (id, name, search_name, tooltip, lang, cache) VALUES ({$info['id']}, '{$info['name']}', '{$info['search_name']}', '{$info['tooltip']}', '{$info['lang']}', UNIX_TIMESTAMP(NOW())) ON DUPLICATE KEY UPDATE tooltip='{$info['tooltip']}',cache=UNIX_TIMESTAMP(NOW())";
		if (!$this->sql->query($query_text))
		{
			$error = $this->sql->error();
			$error = $error['message'];
			echo 'Failed to add ' . $info['name'] . ' to the cache. ' . $error . '<br/><br/>' . $query_text;
			return false;
		}
	}
	
	public function getTitle($name, $faction, $lang)
	{
		if (trim($name) == '' || !$this->connected)
			return false;
		$name = addslashes($name);
		$query_text = "SELECT pattern, achievement, faction, expansion, lang FROM `" . WHP_DB_PREFIX . "title` WHERE (search_name LIKE '{$name}' AND faction='{$faction}' AND lang='{$lang}') LIMIT 1";
		$this->sql->query($query_text);
		return ($this->sql->num_rows($this->sql->query_id) == 0) ? false : $this->sql->fetch_record($this->sql->query_id);
	}
	
	public function saveTitle($info)
	{
		if (sizeof($info) == 0 || !$this->connected)
			return false;
		$info = $this->prepareInfoArray($info);
		$query_text = "INSERT INTO `" . WHP_DB_PREFIX . "title` (pattern, achievement, search_name, faction, expansion, lang) VALUES ('{$info['pattern']}', {$info['achievement']}, '{$info['search_name']}', '{$info['faction']}', '{$info['expansion']}', '{$info['lang']}')";
		if (!$this->sql->query($query_text))
		{
			$error = $this->sql->error();
			$error = $error['message'];
			echo 'Failed to add ' . $info['name'] . ' to the cache. ' . $error . '<br/><br/>' . $query_text;
			return false;
		}
	}
	
	public function saveItemIcon($info)
	{
		if (sizeof($info) == 0 || !$this->connected)
			return false;
		$info = $this->prepareInfoArray($info);
		$query_text = "INSERT INTO `" . WHP_DB_PREFIX . "itemico` (itemid, name, search_name, heroic, icon, icon_size, lang) VALUES ({$info['itemid']}, '{$info['name']}', '{$info['search_name']}', {$info['heroic']}, '{$info['icon']}', '{$info['size']}', '{$info['lang']}')";	
		if (!$this->sql->query($query_text))
		{
			$error = $this->sql->error();
			$error = $error['message'];
			echo 'Failed to add ' . $info['name'] . ' to the cache. ' . $error . '<br/><br/>' . $query_text;
			return false;
		}		
	}
	
	public function getItemIcon($name, $heroic, $lang, $size)
	{
		if (trim($name) == '' || !$this->connected)
			return false;
		$name = addslashes($name);
		$query_text = "SELECT itemid, name, icon FROM " . WHP_DB_PREFIX . "itemico WHERE (search_name LIKE '{$name}' AND heroic={$heroic} AND lang='{$lang}' AND icon_size='{$size}') LIMIT 1";
		//die($query_text);
		$this->sql->query($query_text);
		return ($this->sql->num_rows($this->sql->query_id) == 0) ? false : $this->sql->fetch_record($this->sql->query_id);		
	}
	
	private function prepareInfoArray($array)
	{
		foreach ($array as $key => $value)
		{
			if ($key == 'name' || $key == 'search_name' || $key == 'icon' || $key == 'pattern' || $key == 'tooltip' || $key == 'tipicon' || $key == 'currency_for')
				$array[$key] = addslashes($value);
		}
		return $array;
	}
}
?>
