<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_armory.php 4.3
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

/*
 * Battle.net Community Platform API Integration
 * Yawning <yawninglol at gmail dawt com>
 *
 * TODO: At some point in the futyre figure out how to get total achivements available in game.
 * TODO: At some point in the future figure out if we can filter guild roster info.
 *
 *           DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
 *                   Version 2, December 2004
 *
 * Copyright (C) 2004 Sam Hocevar <sam@hocevar.net>
 *
 * Everyone is permitted to copy and distribute verbatim or modified
 * copies of this license document, and changing it is allowed as long
 * as the name is changed.
 *
 *           DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
 *  TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION
 *
 * 0. You just DO WHAT THE FUCK YOU WANT TO. 
 *
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 *
 */

/**
 * Armory Module
 * @package Wowhead Tooltips
 * @extends wowhead
 */
class wowhead_armory extends wowhead
{
	/**
	 * Armory Region
	 * @var string $region
	 */
	private $region;
	
	/**
	 * Armory Realm
	 * @var string $realm
	 */
	private $realm;
	
	/**
	 * Config Module
	 * @var object $config
	 */
	public $config;
	
	/**
	 * Patterns Module
	 * @var object $patterns
	 */
	public $patterns;
	
	/**
	 * Default Language (Dummy Var)
	 * @var string $lang
	 */
	public $lang = 'en';
	
	/**
	 * Language Packs
	 * @var object $language
	 */
	public $language;
	
	public $lastdownload = 0;
	public $timeout = 5;
	public $retries = 5;
	
	/**
	 * Show Armory Icons
	 * @var bool $icons
	 */
	private $icons;
	
	/**
	 * Show Class Icon
	 * @var bool $class_icon
	 */
	private $class_icon;
	
	/**
	 * Show Race Icons
	 * @var bool $race_icon
	 */
	private $race_icon;
	
	/**
	 * Icon URL
	 * var string $icon_url
	 */
	private $icon_url;
	
	/**
	 * Armory Character Cache Time
	 * @var int $char_cache
	 */
	private $char_cache;
	
	/**
	 * Armory Character URL
	 * @var string $char_url
	 */
	private $char_url;
	
	/**
	 * Character Data From Armory
	 * @var array $char_data
	 */
	private $char_data = array();
	
	/**
	 * Character Stats
	 * @var array $stats
	 */
	private $stats = array();
	
	/**
	 * Base URL for Icons
	 * @var string $images_battle_net_base_url
	 */
	private $images_battle_net_base_url;
	
	/**
	 * Main Spec Tree for Talents
	 * @var int $main_spec
	 */
	private $main_spec;
	
	/**
	 * Unix Timestamp
	 * @var int $now
	 */
	private $now;
	
	/**
	 * Average Item Level
	 * @var int $item_level
	 */
	private $item_level;
	
	/**
	 * Show Guild Rank
	 * @var bool $show_rank
	 */
	private $show_rank;
	
	/**
	 * Determines What Output is Produced
	 * @var string $type
	 */
	private $type = 'armory';
	
	/**
	 * Date Format For date();
	 * @var string $date_format
	 */
	private $date_format;
	
	/**
	 * Time Format for date();
	 * @var string $time_format
	 */
	private $time_format;
	
	/**
	 * Stats Config to Show
	 * @var array $stats_conf
	 */
	protected $stats_conf = array (
		/* --base stats-- */
		'stamina' => false,
		'strength' => false,
		'intellect' => false,
		'agility' => false,
		'spirit' => false,
		'armor' => false,
		/*  --Mastery-- */
		'mastery' => false,
		/*   -- spell damage--   */
		'spell_power' => false,
		'spell_crit' => false,
		'mana_regen' => false,
		'mana_regen_cast' => false,
		'spell_hit' => false,
		'penetration' => false,
		/*   -- melee damage--   */
		'melee_main_dmg' => false,
		'melee_main_speed' => false,
		'melee_main_dps' => false,
		'melee_off_dmg' => false,
		'melee_off_speed' => false,
		'melee_off_dps' => false,
		'melee_power' => false,
		'melee_hit' => false,
		'melee_crit' => false,
		'melee_expertise' => false,
		/*   -- ranged damage--   */
		'ranged_power' => false,
		'ranged_dmg' => false,
		'ranged_speed' => false,
		'ranged_dps' => false,
		'ranged_crit' => false,
		'ranged_hit' => false,
                /*  --Haste--  */
                'haste_rating' => false,
		/*  --defenses--  */
		'dodge' => false,
		'parry' => false,
		'block' => false,
		'resilience' => false,
		/*  --resistances--  */
		'arcane_resist' => false,
		'fire_resist' => false,
		'frost_resist' => false,
		'shadow_resist' => false,
		'nature_resist' => false,
		'holy_resist' => false,
	);
	
	/**
	 * Determine Class from ID
	 * @var array $id_to_name
	 */
	private $id_to_name = array (
		'unknown',
		'warrior',
		'paladin',
		'hunter',
		'rogue',
		'priest',
		'deathknight',
		'shaman',
		'mage',
		'warlock',
		'(10)',
		'druid',
	);

	/**
	 * Determine Race from ID
	 * @var array $race_ids
	 */
	private $race_ids = array(
	    'unknown',
	    'human',
	    'orc',
	    'dwarf',
	    'nightelf',
	    'undead',
	    'tauren',
	    'gnome',
	    'troll',
	    'goblin',
	    'bloodelf',
	    'draenei',
	    22	=>	'worgen'
	);

	/**
	 * Determine Gender from ID
	 * @var array $gender_ids
	 */
	private $gender_ids = array(
	    'male',
	    'female'
	);	
	
	/**
	 * Character Tab SimpleXML
	 * @var object $ctab
	 */
	private $ctab;
	
	/**
	 * Character SimpleXML
	 * @var object $char
	 */
	private $char;
	
	/**
	 * Character Info SimpleXML
	 * @var object $cinfo
	 */
	private $cinfo;
	
	/**
	 * Character Info JSON (Battle.net response)
	 */
	private $cjson;
	
	/**
	 * Constructor
	 * @access public
	 * @param object $config
	 * return null
	 */
	public function __construct()
	{
		// we'll need these later
		$this->config = new wowhead_config();
		$this->config->loadConfig();
		$this->region = $this->config->armory_region;
		$this->realm = $this->config->armory_realm;
		$this->patterns = new wowhead_patterns;
		$this->icons = $this->config->armory_icons;
		$this->class_icon = $this->config->armory_class_icon;
		$this->race_icon = $this->config->armory_race_icon;
		$this->icon_url = $this->config->armory_image_url;
		$this->char_cache = (int)$this->config->armory_char_cache;
		$this->item_level = $this->config->armory_item_level;
		$this->show_rank = $this->config->armory_show_rank;
		$this->images_battle_net_base_url = "http://" . strtolower($this->region) . ".media.blizzard.com/wow/icons/";
		$this->date_format = $this->config->armory_date_format;
		$this->time_format = $this->config->armory_time_format;
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
	 * Parse Text
	 * @access public
	 * @param string $name
	 * @param array $args [optional]
	 * @return string
	 */
	public function parse($name, $args = array())
	{

		if (trim($name) == '')
			return false;
		
		$cache = new wowhead_cache();
		// they specified a realm/region
		if (array_key_exists('loc', $args))
		{
			$aLoc = explode(',', $args['loc']);
			$this->region = $aLoc[0];
			$this->realm = str_replace('+', ' ', $aLoc[1]);
		}
		
		// set the various options
		if (array_key_exists('lang', $args))		// set the language
			$this->lang = $args['lang'];	
		if (array_key_exists('noicons', $args))		// disable all icons
			$this->icons = false;
		if (array_key_exists('noclass', $args))		// disable class icon
			$this->class_icon = false;
		if (array_key_exists('norace', $args))		// disable race icon
			$this->race_icon = false;
		if (array_key_exists('gearlist', $args))	// include gearlist
			$this->type = 'armory_gearlist';
		if (array_key_exists('recruit', $args))		// include recruit info
			$this->type = 'armory_recruit';
		if (array_key_exists('rss', $args))			// include rss info
			$this->type = 'armory_rss';
		
		// load the language pack
		$this->language->loadLanguage($this->lang);	
		$this->char_url = $this->battleDotNetCharacterURL($name);
		
		if (WOWHEAD_DEBUG == true)
			print $this->char_url;
		
		$this->now = time();
		$uniquekey = $cache->generateKey($name, $this->realm, $this->region);
		$result = $cache->getArmory($uniquekey);

		$httpResp = null;
		if ($result != null) {
			if ((int) $result['cache'] > time() - (int)$this->char_cache) {
				// Don't bother checking for updates if the entry is relatively fresh.
				$cache->close();

				$html = $this->generateHTML(array(
					'realm'		=>	$this->realm,
					'region'	=>	$this->region,
					'name'		=>	$result['name'],
					'icons'		=>	$this->getIconsFromBattleNet($result['raceid'], $result['genderid'], $result['classid']),
					'link'		=>	$this->battleDotNetArmoryURL($result['name']),
					'class'		=>	'armory_tt_class_' . strtolower(str_replace(' ', '', $result['class'])),
					'image'		=>	$this->icon_url . 'images/wait.gif'
				), $this->type);

				return $html;
			}

			$httpResp = $this->getArmoryJSON($this->battleDotNetHost(), $this->battleDotNetCharacterRequest($name), $this->battleDotNetCharacterQuery(), (int) $result['cache']);
		} else {
			$httpResp = $this->getArmoryJSON($this->battleDotNetHost(), $this->battleDotNetCharacterRequest($name), $this->battleDotNetCharacterQuery(), 0);
		}

		// Failed to obtain a response.
		if ($httpResp == null) {
			$cache->close();
			return $this->generateError($this->language->words['battlenet_down']);
		} 

		$httpStatus = $httpResp['httpStatus'];
		if ($httpStatus == 304) {

			// NOT MODIFIED
			if ($result != null) {
				$cache->updateArmoryCacheTime($uniquekey);
				$cache->close();

				$html = $this->generateHTML(array(
					'realm'		=>	$this->realm,
					'region'	=>	$this->region,
					'name'		=>	$result['name'],
					'icons'		=>	$this->getIconsFromBattleNet($result['raceid'], $result['genderid'], $result['classid']),
					'link'		=>	$this->battleDotNetArmoryURL($result['name']),
					'class'		=>	'armory_tt_class_' . strtolower(str_replace(' ', '', $result['class'])),
					'image'		=>	$this->icon_url . 'images/wait.gif'
				), $this->type);

				return $html;
			} else {
				// Not modified result when there's no cached data.  This should never happen, and we're basically screwed when it does
				// since we have had a DB cache miss.
				$cache->close();

				return $this->generateError($this->language->words['battlenet_invalnotmod']);
			}
		} elseif ($httpStatus == 404) {
				$cache->close();
				return $this->generateError($this->language->words['char_not_found']);	
		} else {
			$this->cjson = json_decode($httpResp['response'],true);
			if ($this->cjson == NULL) {
				// Parse failed, battle.net is sending shit back at us.
				$cache->close();
				return $this->generateError($this->language->words['battlenet_invalresp']);
			}

			if (array_key_exists('status', $this->cjson) && $this->cjson['status'] == "nok") {
				// All error responses past this point will have a reason in the JSON.
				$cache->close();
				return $this->generateError($this->cjson['reason']);
			}

			//
			// Cache miss or data is updated.
			//
			
			// Get Character stats
			$this->stats = $this->generateStatsFromJSON();
			
			// Get Character talents
			$this->char_data['talents'] = $this->generateTalentsFromJSON();
			
			// Get Professions
			$this->char_data['prof'] = $this->generateProfessionsFromJSON();
			
			// Get Avatar
			$this->char_data['avatar'] = $this->generateAvatarFromJSON();
			
			// Generate achivements
			$this->char_data['achieve'] = $this->generateAchievementsFromJSON();
			
			// Generate pvp data
			$this->char_data['pvp'] = $this->generatePvPFromJSON();
			
			// Generate average item level
			if ($this->item_level == true)
				$this->char_data['itemlevel'] = $this->generateItemLevelFromJSON();
			
			$this->finalizeChar();
			
			// Dump the data in the cache yo!
			$cache->saveArmory(array(
				'uniquekey'	=>	$uniquekey,
				'name'		=>	$this->char_data['name'],
				'class'		=>	$this->char_data['class'],
				'genderid'	=>	$this->char_data['genderid'],
				'raceid'	=>	$this->char_data['raceid'],
				'classid'	=>	$this->char_data['classid'],
				'realm'		=>	$this->realm,
				'region'	=>	$this->region,
				'tooltip'	=>	$this->generateTooltip()
			));
			
			unset($httpResp); $cache->close();
			
			// Generate html
			$html = $this->generateHTML(array(
				'realm'		=>	$this->realm,
				'region'	=>	$this->region,
				'name'		=>	$this->char_data['name'],
				'icons'		=>	$this->getIconsFromBattleNet($this->char_data['raceid'], $this->char_data['genderid'], $this->char_data['classid']),
				'link'		=>	$this->battleDotNetArmoryURL($this->char_data['name']),
				'class'		=>	'armory_tt_class_' . strtolower(str_replace(' ', '', $this->char_data['class'])),
				'image'		=>	$this->icon_url . 'images/wait.gif'
			), $this->type);

			return $html;
		}
	}
	
	/**
	 * Generates Tooltip
	 * @access public
	 * @return string
	 */
	private function generateTooltip()
	{
		// now we'll actually build the tooltip
		
		// get the template from the patterns class
		$html = $this->patterns->pattern('armory_tooltip');
		
		// now time to replace everything, lol
		$html = str_replace('{avatar}', $this->char_data['avatar'], $html);
		$html = str_replace('{name}', $this->char_data['prefix'] . $this->char_data['name'] . $this->char_data['suffix'], $html);
		$html = str_replace('{guild}', $this->char_data['guild'], $html);
		$html = str_replace('{rank}', $this->char_data['rank'], $html);
		$html = str_replace('{level}', $this->char_data['level'], $html);
		$html = str_replace('{race}', $this->char_data['race'], $html);
		$html = str_replace('{class}', $this->char_data['class'], $html);
		$html = str_replace('{health}', $this->char_data['health']['value'], $html);
		$html = str_replace('{secondbar_class}', $this->char_data['secondbar']['class'], $html);
		$html = str_replace('{secondbar}', $this->char_data['secondbar']['value'], $html);
		$html = str_replace('{date}', date($this->date_format, $this->now), $html);
		$html = str_replace('{time}', date($this->time_format, $this->now), $html);
		
		// wildcards we had to write functions for
		$html = str_replace('{talents}', $this->generateTalentsHTML(), $html);
		$html = str_replace('{prof}', $this->generateProfessionsHTML(), $html);
		$html = str_replace('{misc}', $this->generateMiscHTML(), $html);
		$html = str_replace('{stats}', $this->generateStatsHTML(), $html);
		return $html;
	}
	
	/**
	 * Generates Misc Section of Tooltip
	 * @access public
	 * @return string
	 */
	private function generateMiscHTML()
	{
		$html = '';
		
		// Achievements
		// TODO: A detailed breakdown of the achivements would be nice, but not "let's pull down 140 KiB from Battle.net nice".  Figure out a way to filter that bullshit.
		if (false) {
		$html .= '
		<tr>
			<td class="armory_tt_misc_name">' . $this->language->words['achievements'] . ':</td>
			<td class="armory_tt_misc_value">&nbsp; ' . $this->char_data['achieve']['earned'] . '/' . $this->char_data['achieve']['total'] . '</td>
		</tr>
		<tr>
			<td class="armory_tt_misc_name">' . $this->language->words['achievement_pts'] . ':</td>
			<td class="armory_tt_misc_value">&nbsp; ' . $this->char_data['achieve']['points'] . '/' . $this->char_data['achieve']['totalpoints'] . '</td>
		</tr>
';
		} else {
		$html .= '
		<tr>
			<td class="armory_tt_misc_name">' . $this->language->words['achievement_pts'] . ':</td>
			<td class="armory_tt_misc_value">&nbsp; ' . $this->char_data['achieve']['points'] . '</td>
		</tr>
';
		}

		// Only Display Lifetime HKs if it's non-zero.
		if ($this->char_data['pvp']['totalHonorableKills'] > 0) {
			$html .= '
		<tr>
			<td class="armory_tt_misc_name">' . $this->language->words['lifetime_hk'] . ':</td>
			<td class="armory_tt_misc_value">&nbsp; ' . (string)$this->char_data['pvp']['totalHonorableKills'] . '</td>
		</tr>
';
		}

		if ($this->item_level == true)
		{
			$html .= '
		<tr>	
			<td class="armory_tt_misc_name">' . $this->language->words['avg_ilevel'] . ':</td>
			<td class="armory_tt_misc_value">&nbsp; ' . $this->char_data['itemlevel'] . '</td>
		</tr>
	';	
		}
		return $html;
	}
	
	/**
	 * Generate Professions Section of Tooltip
	 * @access public
	 * @return string
	 */
	private function generateProfessionsHTML()
	{
		$html = '';
		
		foreach ($this->char_data['prof'] as $p)
		{
			if ((int) $p['max'] == 0) {
				// Ugh, the API sometimes freaks out and returns 0 for the max.  Recover gracefully.
				$html .= '
		<tr>
			<td class="armory_tt_profession_name">
				<img src="' . $p['icon_url'] . '">
				' . $p['name'] . '
			</td>
		   	<td class="armory_tt_profession_skill">
				&nbsp; ' . $p['value'] . '
		   	</td>
		</tr>		
';
			} else {
			$html .= '
		<tr>
			<td class="armory_tt_profession_name">
				<img src="' . $p['icon_url'] . '">
				' . $p['name'] . '
			</td>
		   	<td class="armory_tt_profession_skill">
				&nbsp; ' . $p['value'] . '/' . $p['max'] . '
		   	</td>
		</tr>		
';
		}
		}
		
		return $html;
	}
	
	/**
	 * Generate Stats Section of Tooltip
	 * @access public
	 * @return string
	 */
	private function generateStatsHTML()
	{
		$html = '';
		
		foreach ($this->char_data['stats'] as $stat => $v)
		{
			$html .= '
                 <tr>
                   <td class="armory_tt_stat_' . $v['class'] . '">' . $v['field'] . ':</td>
                   <td class="armory_tt_stat_value">&nbsp; ' . $v['value'] . '</td>
                 </tr>';	
		}
		
		return $html;
	}
	
	/**
	 * Generate Talents Section of Tooltips
	 * @access public
	 * @return string
	 */
	private function generateTalentsHTML()
	{
		$html = '';
		
		foreach ($this->char_data['talents'] as $talent)
		{
			$strong = ($talent['active'] == true) ? '<strong>' : '';
			$slash_strong = ($talent['active'] == true) ? '</strong>' : '';
			$html .= '
<nobr>
<img src="' . $talent['icon_url'] . '">
<span class="armory_tt_talent_trees">' . $strong . $talent['tree'][1] . '/' . $talent['tree'][2] . '/' . $talent['tree'][3] . $slash_strong . '</span>
</nobr>';	
		}
		
		return $html;
	}

    /**
	 * Generate Item Level Section of Tooltip from JSON data
     * @access public
     * @return string
     */
	private function generateItemLevelFromJSON()
    {
		$jsona = $this->cjson['items'];

		return (string) $jsona['averageItemLevelEquipped'];
        }
        
	/**
	 * Generate Achievements Section of Tooltip from JSON data
	 * @access public
	 * @return array
	 */
	private function generateAchievementsFromJSON()
	{
		// Figure out a way to query this bullshit without pulling down ~140k of data per character.
		return array(
			'earned'		=>	0,
			'total'			=>	0,
			'points'		=>	(string)$this->cjson['achievementPoints'],
			'totalpoints'		=>	0,
		);	
    }  
	
	/**
	 * Generate PvP Section of Tooltip from JSON data
	 * @access public
	 * @return array
	 */
	private function generatePvPFromJSON()
	{
		return array(
			'totalHonorableKills'	=> (int)$this->cjson['pvp']['totalHonorableKills'],
		);	
	}
	
	/**
	 * Finalize the Character for Display (JSON sourced)
	 * @access public
	 * @return null
	 */
	private function finalizeChar()
	{
		$jsona = $this->cjson;
		$charClass = strtolower($this->id_to_name[(int)$jsona['class']]);

		// Get the required stats for the character's class
		require(dirname(__FILE__) . '/class_conf/' . str_replace(' ', '', $charClass) . '.php');
		
		foreach ($this->stats_conf as $stat => $value)
		{
			if ($value)
				$this->char_data['stats'][$stat] = $this->stats[$stat];	
		}

		$this->char_data['health'] = $this->stats['health'];
		$this->char_data['secondbar'] = $this->stats['secondbar'];

		// Class specific rage/energy for warriors and rogues
		if ($charClass == 'warrior')
			$this->char_data['secondbar']['class'] = 'power_rage';
		elseif ($charClass == 'rogue')
			$this->char_data['secondbar']['class'] = 'power_energy';

		// Can't forget the characters name, lol
		$this->char_data['name'] = (string)$jsona['name'];
			
		// Add the guild name
		$this->char_data['guild'] = '&nbsp;';
		if (array_key_exists('guild', $jsona)) {
			$guilda = $jsona['guild'];
			if (array_key_exists('name', $guilda)) {
				$this->char_data['raw_guild'] = (string)$guilda['name'];
			       $this->char_data['guild'] = '&lt;' . (string)$guilda['name'] . '&gt;';
			}
		}
		
		// Generate guild rank
		if ($this->show_rank == true && $this->char_data['guild'] != '&nbsp;') {
			$this->char_data['rank'] = $this->generateGuildRank();
		}
		else
			$this->char_data['rank'] = '';
		
		
		// Add prefix and suffix
		$this->char_data['prefix'] = '';
		$this->char_data['suffix'] = '';
		$titlesa = $jsona['titles'];
		foreach ($titlesa as $title) {
			if (array_key_exists('selected', $title) && (bool) $title['selected'] == true) {
				$titleString = $title['name'];
				if (substr($titleString, 0, 2) == '%s') $this->char_data['suffix'] = substr($titleString, 2);
				if (substr($titleString, -2) == '%s') $this->char_data['prefix'] = substr($titleString, 0, -2);
				break;
			}
		}

		// Level
		$this->char_data['level'] = (string)$jsona['level'];
		
		// Class
		$this->char_data['class'] = $charClass;
		
		// Race
		$this->char_data['race'] = (string)$this->raceIds[(int)$jsona['race']];
		
		// gender, race, and class id
		$this->char_data['genderid'] = (string)$jsona['gender'];
		$this->char_data['raceid'] = (string)$jsona['race'];
		$this->char_data['classid'] = (string)$jsona['class'];
	}
	
	/**
	 * Generates Guild Rank
	 * @access public
	 * @return string
	 */
	private function generateGuildRank()
	{
		// TODO: Support Caching this.
		$httpResp = $this->getArmoryJSON($this->battleDotNetHost(), $this->battleDotNetGuildRequest(), $this->battleDotNetGuildQuery(), 0);
		if ($httpResp == null) return '';
		
		$gjson = json_decode($httpResp['response'],true);
		if ($gjson == NULL || (array_key_exists('status', $gjson) && $gjson['status'] == "nok")) return '';

		// Ok, we have the guild roster.
		foreach ($gjson['members'] as $member)
		{
			if ($member['character']['name'] == $this->char_data['name']) {
				// We now have the character info, so now we need to get the rank title, according to the script's config
			$which = 'armory_rank_' . (string)$member['rank'];
			return $this->config->$which;	
		}
	}
	
		return '';
	}
	
	/**
	 * Enable Stats for a Character
	 * @access public
	 * @param array $stats
	 * @return null
	 */
	private function enable_stats($stats)
	{
		foreach ($stats as $stat) {
			$this->stats_conf[$stat] = true;
		}
	}
	
	/**
	 * Generate Avatar Based on JSON data
	 * @access public
	 * @return string
	 */
	private function generateAvatarFromJSON()
	{
		$avatar = "http://" . strtolower($this->region) . ".battle.net/static-render/" . strtolower($this->region) . "/" . (string)$this->cjson['thumbnail'];
		
		return $avatar;
	}
	
	/**
	 * Generate Professions from JSON data
	 * @access public
	 * @return array
	 */
	private function generateProfessionsFromJSON()
	{
		$jsona = $this->cjson['professions']['primary'];
		$prof = array(); $i = 0;
		
		foreach ($jsona as $skill)
		{
			$prof[$i] = array(
				'icon_url'	=>	$this->images_battle_net_base_url . '18/' . (string)$skill['icon'] . '.jpg',
				'value'		=>	(string)$skill['rank'],
				'max'		=>	(string)$skill['max'],
				'name'		=>	(string)$skill['name']
			);
			$i++;
		}
		
		return $prof;
	}
	
	/**
	 * Generate Talents from JSON data
	 * @access public
	 * @return array
	 */
	private function generateTalentsFromJSON()
	{
		$jsona = $this->cjson['talents'];
		$talents = array(); $i = 0;
		foreach ($jsona as $spec)
			{
			// Determine the main tree.
			$nrPts1 = (int) $spec['trees'][0]['total'];
			$nrPts2 = (int) $spec['trees'][1]['total'];
			$nrPts3 = (int) $spec['trees'][2]['total'];

			switch (max($nrPts1, $nrPts2, $nrPts3))
			{
			case $nrPts1: $main_tree = 1; break;
			case $nrPts2: $main_tree = 2; break;
			case $nrPts3: $main_tree = 3; break;
				default: break;	
			}
			
			if ((bool) $spec['selected'] == true) 
				$this->main_spec = $main_tree;	
			
			$talents[$i] = array(
				'icon_url'	=>	$this->images_battle_net_base_url . '18/' . (string)$spec['icon'] . '.jpg',
				'prim'		=>	(string)$spec['name'],
				'active'	=>	((bool) $spec['selected'] == true) ? 1 : 0,
				'tree'		=>	array(
					'main'	=>	$main_tree,
					'1'		=>	(string)$nrPts1,
					'2'		=>	(string)$nrPts2,
					'3'		=>	(string)$nrPts3
				)
			);
			$i++;
		}
		
		return $talents;
	}
	
	/**
	 * Generate stats from JSON data
	 * @access public
	 * @return array
	 */
	private function generateStatsFromJSON()
	{
		$jsona = $this->cjson['stats'];
		$stats = array();
		
		//
		// The battle.net Community Platform API is kind of shit, so it
		// doesn't give a convenient way to get base stats.  Oh well.
		//
		// I sent them an e-mail bitching about it, but they typically
		// don't respond so fuck them.
		//

		$this->addtostats($stats, 'stamina', $this->language->words['stamina'], (string)$jsona['sta'], 'primary');
	       	$this->addtostats($stats, 'intellect', $this->language->words['intellect'], (string)$jsona['int'], 'primary');
		$this->addtostats($stats, 'strength', $this->language->words['strength'], (string)$jsona['str'], 'primary');
		$this->addtostats($stats, 'agility', $this->language->words['agility'], (string)$jsona['agi'], 'primary');
		$this->addtostats($stats, 'spirit', $this->language->words['spirit'], (string)$jsona['spr'], 'primary');
		$this->addtostats($stats, 'armor', $this->language->words['armor'], (string)$jsona['armor'], 'primary');

		$this->addtostats($stats, 'health', 'Health', (string)$jsona['health'], 'health');
		$this->addtostats($stats, 'secondbar', 'Power', (string)$jsona['power'], 'power_mana');

		$this->addtoStats($stats, 'mastery', $this->language->words['mastery'], (string)$jsona['mastery'], 'generic');
		
		// spell power and crit
		$this->addtostats($stats, 'spell_power', $this->language->words['spell_power'], (string)$jsona['spellPower'], 'generic');
		$this->addtostats($stats, 'spell_crit', $this->language->words['spell_crit'], (string)$jsona['spellCrit'] . '%', 'generic');
		$this->addtostats($stats, 'spell_hit', $this->language->words['spell_hit'], (string)$jsona['spellHitPercent'] . '%', 'generic');
		$this->addtostats($stats, 'haste_rating', $this->language->words['haste'], (string)$jsona['hasteRating'], 'generic');
		$this->addtostats($stats, 'penetration', $this->language->words['spell_pen'], (string)$jsona['spellPen'], 'generic');

		$this->addtostats($stats, 'melee_main_dmg', $this->language->words['melee_main_dmg'], (string)$jsona['mainHandDmgMin'] . '-' . (string)$jsona['mainHandDmgMax'], 'melee_main_hand');
		$this->addtostats($stats, 'melee_main_speed', $this->language->words['melee_main_speed'], (string)$jsona['mainHandSpeed'], 'melee_main_hand');
		$this->addtostats($stats, 'melee_main_dps', $this->language->words['melee_main_dps'], (string)$jsona['mainHandDps'], 'melee_main_hand');
		
		$this->addtostats($stats, 'melee_off_dmg', $this->language->words['melee_off_dmg'], (string)$jsona['offHandDmgMin'] . '-' . (string)$jsona['offHandDmgMax'], 'melee_off_hand');
		$this->addtostats($stats, 'melee_off_speed', $this->language->words['melee_off_speed'], (string)$jsona['offHandSpeed'], 'melee_off_hand');
		$this->addtostats($stats, 'melee_off_dps', $this->language->words['melee_off_dps'], (string)$jsona['offHandDps'], 'melee_off_hand');


		$this->addtostats($stats, 'melee_power', $this->language->words['melee_power'], (string)$jsona['attackPower'], 'generic');
		$this->addtostats($stats, 'melee_hit', $this->language->words['melee_hit'], (string)$jsona['hitPercent'] . '%', 'generic');
		$this->addtostats($stats, 'melee_crit', $this->language->words['melee_crit'], (string)$jsona['crit'] . '%', 'generic');
		$this->addtostats($stats, 'melee_expertise', $this->language->words['melee_expertise'], (string)$jsona['mainHandExpertise'], 'generic');	// Fuck offhands
	
		$this->addtostats($stats, 'dodge', $this->language->words['dodge_chance'], (string)$jsona['dodge'] . '%', 'defensive');
		$this->addtostats($stats, 'block', $this->language->words['block_chance'], (string)$jsona['block'] . '%', 'defensive');
		$this->addtostats($stats, 'parry', $this->language->words['parry_chance'], (string)$jsona['parry'] . '%', 'defensive');
		$this->addtostats($stats, 'resilience', $this->language->words['resilience'], (string)$jsona['resil'], 'defensive');

		$this->addtostats($stats, 'mana_regen', $this->language->words['mana_regen'], (string)$jsona['mana5'], 'mana_regen');
		$this->addtostats($stats, 'mana_regen_cast', $this->language->words['mana_regen_cast'], (string)$jsona['mana5Combat'], 'mana_regen');

		$this->addtostats($stats, 'ranged_dmg', $this->language->words['ranged_dmg'], (string)$jsona['rangedDmgMin'] . '-' . (string)$jsona['rangedDmgMax'], 'ranged');
		$this->addtostats($stats, 'ranged_dps', $this->language->words['ranged_dps'], (string)$jsona['rangedDps'], 'ranged');
		$this->addtostats($stats, 'ranged_crit', $this->language->words['ranged_crit'], (string)$jsona['rangedCrit'] . '%', 'ranged');
		$this->addtostats($stats, 'ranged_hit', $this->language->words['ranged_hit'], (string)$jsona['rangedHitPercent'] . '%', 'ranged'); 
		$this->addtostats($stats, 'ranged_speed', $this->language->words['ranged_speed'], (string)$jsona['rangedSpeed'], 'ranged');
		$this->addtostats($stats, 'ranged_power', $this->language->words['ranged_power'], (string)$jsona['rangedAttackPower'], 'ranged'); 
		
		return $stats;
	}
	
	/**
	 * Add to Stats Array
	 * @access public
	 * @param array $stats
	 * @param string $index
	 * @param string $field
	 * @param string $value
	 * @param string $class
	 * @return null
	 */
	private function addtostats(&$stats, $index, $field, $value, $class)
	{
		$stats[$index] = array(
			'field'	=>	$field,
			'value'	=>	$value,
			'class'	=>	$class
		);
	}
	
	/**
	 * Gets Class and Race Icons
	 * @access public
	 * @param int $raceid
	 * @param int $genderid
	 * @param int $classid
	 * @return string
	 */
	private function getIconsFromBattleNet($raceid, $genderid, $classid)
	{
		$icons = '';
		// build the icon html
		if ($this->icons == true)
		{
			if ($this->race_icon)
				$icons .= '<img src="' . $this->images_battle_net_base_url . '18/race_' . $raceid . '_' . $genderid . '.jpg" alt="' . ucwords($this->gender_ids[$genderid]) . ' ' . ucwords($this->race_ids[$raceid]) . '" title="' . ucwords($this->gender_ids[$genderid]) . ' ' . ucwords($this->race_ids[$raceid]) . '" />&nbsp;';

			if ($this->class_icon)
				$icons .= '<img src="' . $this->images_battle_net_base_url . '18/class_' . $classid . '.jpg" title="' . ucwords($this->id_to_name[$classid]) . '" alt="' . ucwords($this->id_to_name[$classid]) . '" />&nbsp;';
		}
		return $icons;
	}
	
	private function battleDotNetArmoryURL($name)
	{
		return $this->battleDotNetURL() . 'wow/' . strtolower($this->region) . '/character/' . rawurlencode($this->realm) . '/' . rawurlencode($name) . '/advanced';
	}

	private function battleDotNetCharacterURL($name)
	{
		return $this->battleDotNetURL() . 'api/wow/character/' .  rawurlencode($this->realm) . '/' .  rawurlencode($name) . '?fields=items,talents,stats,professions,guild,titles';
	}
	
	private function battleDotNetURL()
	{
		return "http://" . $this->battleDotNetHosT() . "/";
	}

	private function battleDotNetHost()
	{
		return strtolower($this->region) . ".battle.net";
	}

	private function battleDotNetCharacterRequest($name)
	{
		$normRealm = rawurlencode($this->realm);
		
		// XXX: For some reason, signed requests with apostrophe's encoded as %27 fail to validate.
		$normRealm = str_replace("%27", "'", $normRealm);

		return '/api/wow/character/' .  $normRealm . '/' .  rawurlencode($name);
	}

	private function battleDotNetCharacterQuery()
	{
		return '?fields=items,talents,stats,professions,guild,titles,pvp';
	}

	private function battleDotNetGuildRequest()
	{
		$normRealm = rawurlencode($this->realm);
		
		// XXX: For some reason, signed requests with apostrophe's encoded as %27 fail to validate.
		$normRealm = str_replace("%27", "'", $normRealm);

		return '/api/wow/guild/' .  $normRealm . '/' . rawurlencode($this->char_data['raw_guild']);
	}
	
	private function battleDotNetGuildQuery()
	{
		return '?fields=members';
	}
}
?>
