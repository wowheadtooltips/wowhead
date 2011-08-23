<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_guild.php 4.0
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

/*
 * Battle.net Community Platform API Integration
 * Yawning <yawninglol at gmail dawt com>
 *
 * TODO: Battlegroup information once the API actually returns it.
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
 * http://sam.zoy.org/wtfpl/COPYING for more details/*
 * Battle.net Community Platform API Integration
 */

class wowhead_guild extends wowhead
{
	/*
	 * Variables
	 */
	public $patterns;
	public $lang = 'en';	// dummy to prevent any errors
	public $language;
	public $config;
	public $lastdownload = 0;
	public $timeout = 5;
	public $retries = 5;
	private $realm;
	private $region;
	private $guild_cache;
	private $guild = array();
	private $now;
	private $time_format;
	private $date_format;
	private $gjson;
	
	private $faction = array (
		0 => 'Alliance',
		1 => 'Horde',
	);	
	private $class_ids = array (
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
	    22 => 	'worgen'
	);

	private $gender_ids = array(
	    'male',
	    'female'
	);
	
	public function __construct()
	{
		$this->config = new wowhead_config();
		$this->config->loadConfig();
		$this->patterns = new wowhead_patterns();
		$this->realm = $this->config->armory_realm;
		$this->region = $this->config->armory_region;
		$this->guild_cache = $this->config->armory_guild_cache;
		$this->date_format = $this->config->armory_date_format;
		$this->time_format = $this->config->armory_time_format;
		$this->lang = $this->config->lang;
		$this->language = new wowhead_language();
	}
	
	public function close()
	{
		unset($this->lang, $this->language, $this->patterns, $this->config);	
	}
	
	public function parse($name, $args = array())
	{
		if (trim($name) == '')
			return false;
			
		// see if they specified a realm/region
		if (array_key_exists('loc', $args))
		{
			$aLoc = explode(',', $args['loc']);
			$this->region = $aLoc[0];
			$this->realm = $aLoc[1];
		}
		
		// set the language
		if (array_key_exists('lang', $args))
			$this->lang = $args['lang'];
		
		// load the language pack
		$this->language->loadLanguage($this->lang);
		
			if (WOWHEAD_DEBUG == true)
				print $this->guildURL($name);
			
		$cache = new wowhead_cache();
		$this->now = mktime();
		$uniqueKey = $cache->generateKey($name, $this->realm, $this->region);
		$result = $cache->getGuild($uniqueKey, $this->guild_cache);

		$httpResp = null;
		if ($result != null) {
			if ((int) $result['cache'] > time() - (int) $this->guild_cache) {
				// Don't bother checking for updates if the entry is relatively fresh.
				$cache->close();

				$html = $this->generateHTML(array(
					'name'		=>	ucwords($name),
					'region'	=>	$this->region,
					'realm'		=>	$this->realm,
					'link'		=>	$this->guildURL($name),
				), 'guild');

				return $html;
			}
			
			$httpResp = $this->getArmoryJSON($this->battleDotNetHost(), $this->battleDotNetGuildRequest($name), $this->battleDotNetGuildQuery(), (int) $result['cache']);
		} else {
			$httpResp = $this->getArmoryJSON($this->battleDotNetHost(), $this->battleDotNetGuildRequest($name), $this->battleDotNetGuildQuery(), 0);
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
				$cache->updateGuildCacheTime($uniquekey);
				$cache->close();

				$html = $this->generateHTML(array(
					'name'		=>	ucwords($name),
					'region'	=>	$this->region,
					'realm'		=>	$this->realm,
					'link'		=>	$this->guildURL($name),
				), 'guild');

				return $html;
			} else {
				// Not modified result when there's no cached data.  This should never happen, and we're basically screwed when it does
				// since we have had a DB cache miss.
				$cache->close();

				return $this->generateError($this->language->words['battlenet_invalnotmod']);
			}
		} elseif ($httpStatus == 404) {
			$cache->close();
			return $this->generateError($this->language->words['guild_not_found']);
		} else  {
			$this->gjson = json_decode($httpResp['response'],true);
			if ($this->gjson == NULL) {
				// Parse failed, battle.net is sending shit back at us.
				$cache->close();
				return $this->generateError($this->language->words['battlenet_invalresp']);
			}

			if (array_key_exists('status', $this->gjson) && $this->gjson['status'] == "nok") {
				// All error responses past this point will have a reason in the JSON.
				$cache->close();
				return $this->generateError($this->gjson['reason']);
			}

			//
			// Cache miss or data is updated.
			//

			// Generate guild info
			$this->guild = $this->generateInfo();
			
			// FIXME: This icon sucks, and I really should decode faction ID properly.
			$this->guild['icon'] = $this->battleDotNetURL() . 'wow/static/images/icons/' . (($this->gjson['side'] == 0) ? 'alliance.png' : 'horde.png');

			// Generate guild stats
			$this->guild['stats'] = $this->generateStats();
			
			// Generate tooltip
			$tooltip = $this->generateTooltip();
			
			// save to the cache
			$cache->saveGuild(array(
				'uniquekey'		=>	$cache->generateKey($name, $this->realm, $this->region),
				'name'			=>	$this->guild['name'],
				'realm'			=>	$this->realm,
				'region'		=>	$this->region,
				'tooltip'		=>	$tooltip
			));
			
			$cache->close();

			return $this->generateHTML(array(
				'name'		=>	ucwords($name),
				'region'	=>	$this->region,
				'realm'		=>	$this->realm,
				'link'		=>	$this->guildURL($name),
			), 'guild');
		}
	}
	
	private function generateTooltip()
	{
		$html = $this->patterns->pattern('armory_guild');
		
		$html = str_replace('{icon}', $this->guild['icon'], $html);
		$html = str_replace('{name}', $this->guild['name'], $html);
		$html = str_replace('{realm}', $this->guild['realm'], $html);
//		$html = str_replace('{battlegroup}', $this->guild['battlegroup'], $html);
		$html = str_replace('({battlegroup})', '', $html); // Strip out the pattern from the template for now.
		$html = str_replace('{count}', $this->guild['member_count'], $html);
		
		// stats
		$html = str_replace('{gender_stats}', $this->generateGenderHTML(), $html);
		$html = str_replace('{race_stats}', $this->generateRaceHTML(), $html);
		$html = str_replace('{class_stats}', $this->generateClassHTML(), $html);
		
		// date/time
		$html = str_replace('{date}', date($this->date_format, $this->now), $html);
		$html = str_replace('{time}', date($this->time_format, $this->now), $html);
		
		return $html;
	}
	
	private function generateGenderHTML()
	{
		$html = '';
		
		foreach ($this->guild['stats']['gender'] as $g => $v)
		{
			if ((int)$v != 0 && (int)$this->guild['member_count'] != 0)
			{
				$html .= '
             <tr>
               <td class="armory_tt_stat_primary">
                  ' . ucwords($g) . ':
               </td>
               <td class="armory_tt_stat_value">
                    &nbsp; ' . $v . '
					&nbsp; (' . (string)$this->percent($v, $this->guild['member_count']) . '%)
               </td>
             </tr>	
';
			}
		}
		
		return $html;
	}
	
	private function generateRaceHTML()
	{
		$html = '';
		
		foreach ($this->guild['stats']['race'] as $g => $v)
		{
			if ((int)$v != 0 && (int)$this->guild['member_count'] != 0)
			{
				$html .= '
             <tr>
               <td class="armory_tt_stat_primary">
                  ' . ucwords($g) . ':
               </td>
               <td class="armory_tt_stat_value">
                    &nbsp; ' . $v . '
					&nbsp; (' . (string)$this->percent($v, $this->guild['member_count']) . '%)
               </td>
             </tr>	
';
			}
		}
		
		return $html;		
	}
	
	private function generateClassHTML()
	{
		$html = '';
		
		foreach ($this->guild['stats']['class'] as $g => $v)
		{
			if ((int)$v != 0 && (int)$this->guild['member_count'] != 0)
			{
				$cname = ($g == 'deathknight') ? 'Death Knight' : ucwords($g);
				$html .= '
             <tr>
               <td class="armory_tt_stat_primary">
                  ' . $cname . ':
               </td>
               <td class="armory_tt_stat_value">
                    &nbsp; ' . $v . '
					&nbsp; (' . (string)$this->percent($v, $this->guild['member_count']) . '%)
               </td>
             </tr>	
';
			}
		}
		
		return $html;		
	}
	
	private function generateStats()
	{
		$gender = array();
		$race = array();
		$class = array();
		$guildMembers = $this->gjson['members'];
		
		// fill each array
		foreach ($this->class_ids as $c)
			$class[$c] = 0;	
		foreach ($this->race_ids as $r)
			$race[$r] = 0;
		foreach ($this->gender_ids as $g)
			$gender[$g] = 0;

		foreach ($guildMembers as $member)
		{
			$memberChar = $member['character'];
			if (array_key_exists((int)$memberChar['gender'], $this->gender_ids))
				$gender[$this->gender_ids[(int)$memberChar['gender']]]++;
			if (array_key_exists((int)$memberChar['race'], $this->race_ids))
				$race[$this->race_ids[(int)$memberChar['race']]]++;
			if (array_key_exists((int)$memberChar['class'], $this->class_ids))
				$class[$this->class_ids[(int)$memberChar['class']]]++;
		}
		
		return array(
			'gender'	=>	$gender,
			'race'		=>	$race,
			'class'		=>	$class
		);
	}
	
	private function generateInfo()
	{
		return array(
			'faction' => (string)$this->gjson['faction'],
			'name' => (string)$this->gjson['name'],
			'member_count' => (string)count($this->gjson['members']),
			// TODO: The api currently does not return battle group.
			'realm' => (string)$this->gjson['members'][0]['realm']
		);
	}
	
	/**
	* Generate the full armory url
	* @access private
	**/
	private function guildURL($name)
	{
		return $this->battleDotNetURL() . "wow/en/guild/" . rawurlencode($this->realm) . '/' . rawurlencode($name);
	}

	private function battleDotNetGuildRequest($name)
	{
		$normRealm = rawurlencode($this->realm);
		
		// XXX: For some reason, signed requests with apostrophe's encoded as %27 fail to validate.
		$normRealm = str_replace("%27", "'", $normRealm);

		return '/api/wow/guild/' .  $normRealm . '/' . rawurlencode($name);
		
	}

	private function battleDotNetGuildQuery()
	{
		return '?fields=members';
	}

	private function battleDotNetURL()
	{
		return "http://" . $this->battleDotNetHosT() . "/";
	}

	private function battleDotNetHost()
	{
		return strtolower($this->region) . ".battle.net";
	}

	private function percent($num_amount, $num_total) {
	    $count1 = $num_amount / $num_total;
	    $count2 = $count1 * 100;
	    $count = number_format($count2, 0);
	    return $count;
	}
}
?>
