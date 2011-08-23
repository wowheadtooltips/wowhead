<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_npc.php 4.0
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

class wowhead_npc extends wowhead
{
	public $lang;
	public $patterns;
	public $language;
	public $image_path;
	public $wowhead_zone_maps = 'http://static.wowhead.com/images/maps/enus/normal/';
	public $config;

	public function __construct()
	{
		$this->patterns = new wowhead_patterns();
		$this->language = new wowhead_language();
		$this->images_path = dirname(__FILE__) . '/../images/zones/';
		$this->config = new wowhead_config();
		$this->config->loadConfig();
	}
	
	public function close()
	{
		unset($this->lang, $this->language, $this->patterns, $this->config);	
	}

	public function parse($name, $args = array())
	{
		if (trim($name) == '')
			return false;

		$this->lang = (!array_key_exists('lang', $args)) ? $this->config->lang : $args['lang'];
		$this->language->loadLanguage($this->lang);
		$cache = new wowhead_cache();

		if (!$result = $cache->getNPC($name, $this->lang))
		{
			// not found in cache

			$result = (is_numeric($name)) ? $this->getNPCByID($name) : $this->getNPCByName($name);
			if (!$result)
			{
				// not found
				$cache->close();
				return $this->notFound($this->language->words['npc'], $name);
			}
			else
			{
				// see if they want to display a map as well
				if (array_key_exists('map', $args))
				{
					if (!$sql_map = $cache->getZone($args['map']['name'], $this->lang))
					{
						$mapinfo = (is_numeric($args['map']['name'])) ? $this->getMapByID($args['map']['name']) : $this->getMapByName($args['map']['name']);
						if (!$mapinfo)
						{
							$cache->close();
							return $this->_notFound($this->language->words['zone'], $name);
						}
						else
						{
							if (!file_exists($this->images_path . $mapinfo['map']) && $this->config->transfer_map == true)
							{
								// file doesn't exist, so transfer it locally
								$this->transferImage($mapinfo['map']);	
							}
							$cache->saveZone($mapinfo);
							$mapinfo['x'] = $args['map']['x'];
							$mapinfo['y'] = $args['map']['y'];
							$cache->saveNPC($result);
							$cache->close();
							return $this->mapHTML($result, $mapinfo);
						}
					}
					else
					{
						if (!file_exists($this->images_path . $sql_map['map']) && $this->config->transfer_map == true)
						{
							// file doesn't exist, so transfer it locally
							$this->transferImage($sql_map['map']);	
						}
						$sql_map['x'] = $args['map']['x'];
						$sql_map['y'] = $args['map']['y'];
						$cache->saveNPC($result);
						$cache->close();
						return $this->mapHTML($result, $sql_map);	
					}
				}
				else
				{
					// found, save it and display
					$cache->saveNPC($result);
					$cache->close();
					return $this->generateHTML($result, 'npc');
				}
			}
		}
		else
		{
			if (array_key_exists('map', $args))
			{
				$mapinfo = $cache->getZone($args['map']['name'], $this->lang);
				if (!file_exists($this->images_path . $mapinfo['map']) && WHP_TRANSFER_MAP == true)
				{
					// file doesn't exist, so transfer it locally
					$this->transferImage($mapinfo['map']);	
				}
				$mapinfo['x'] = $args['map']['x'];
				$mapinfo['y'] = $args['map']['y'];
				$cache->close();
				return $this->mapHTML($result, $mapinfo);
			}
			else
			{
				$cache->close();
				return $this->generateHTML($result, 'npc');
			}
		}
	}
	
	private function getNPCByName($name)
	{
		if (trim($name) == '')
			return false;
		
		$data = $this->readURL($name, 'npc', false);

		if (!$data)
			return false;
		
		// make sure it didn't redirect
		if (preg_match('#Location: \/npc=([0-9]{1,10})#s', $data, $match))
		{
			return array(
				'name'			=>	ucwords(strtolower($name)),
				'search_name'	=>	$name,
				'npcid'		=>	$match[1],
				'lang'			=>	$this->lang
			);	
		}
		
		$line = $this->npcLine($data);
		if (!$line)
			return false;
		else
		{
			// go go json!
			if (!$json = json_decode($line, true))
				return false;
			
			foreach ($json as $npc)
			{
				if (stripslashes(strtolower($npc['name'])) == stripslashes(strtolower($name)))
				{
					return array(
						'name'			=>	stripslashes($npc['name']),
						'search_name'	=>	$name,
						'npcid'		=>	$npc['id'],
						'lang'			=>	$this->lang
					);
				}
			}
			return false;
		}
	}
	
	private function getNPCByID($id)
	{
		if (trim($id) == '' || !is_numeric($id))
			return false;
		
		$data = $this->readURL($id, 'npc', false);
		
		if (!$data)
			return false;
		
		// get the ID
		if (!preg_match('#\$WowheadPower.registerNpc\(([0-9]{1,10}), ([0-9]{1}), \{#s', $data, $id_match))
			return false;
		
		// now get the name
		if (!preg_match("#name_([a-z_]{4})\: '(.+?)',#s", $data, $name_match))
			return false;

		// now return the results!
		return array(
			'npcid'			=>	$id_match[1],
			'name'			=>	stripslashes($name_match[2]),
			'search_name'	=>	$id,
			'lang'			=>	$this->lang
		);
	}
	
	private function transferImage($image)
	{
		if (!is_writable($this->images_path))
			trigger_error('The directory for storing the zone images (' . $this->images_path . ') is not writable.  Please CHMOD to 0755 or 0777 and then try again,', E_USER_ERROR);	
		
		// make sure the image isn't already there
		if (!file_exists($this->images_path . $image))
		{
			if (!@file_put_contents($this->images_path . $image, @file_get_contents($this->wowhead_zone_maps . $image)))
				trigger_error('Failed to transfer the zone map to the local webserver.', E_USER_ERROR);
			else
				@chmod($this->images_path . $image, 0777);
		}
	}

	
	private function mapHTML($npc, $map)
	{
		$html = $this->patterns->pattern('npc_map');
		$html = str_replace('{link}', $this->generateLink($npc['npcid'], 'npc'), $html);
		$html = str_replace('{maplink}', $this->generateLink($map['id'], 'zone'), $html);
		$html = str_replace('{name}', $npc['name'], $html);
		$html = str_replace('{id}', $map['id'], $html);
		$html = str_replace('{lang}', $this->lang, $html);
		$html = str_replace('{pins}', $map['x'] . ',' . $map['y'], $html);
		return $html;
	}
	
	private function npcLine($data)
	{
		$parts = explode(chr(10), $data);
		foreach ($parts as $line)
		{
			if (strpos($line, "new Listview({template: 'npc', id: 'npcs',") !== false)
			{
				$line = substr($line, strpos($line, 'data: [{') + 6);
				$line = str_replace('});', '', $line);
				return $line;
				break;	
			}
		}
		return false;
	}

	private function getMapByID($id)
	{
		$data = $this->readURL($id, 'zone', false);
		if (!$data)
		{
			return false;
		}
		else
		{
			if (!preg_match('#name: \'(.+?)\'};#s', $this->nameLine($data), $match))
			{
				return false;	
			}
			else
			{
				return array(
					'id'			=>	$id,
					'name'			=>	stripslashes($match[1]),
					'search_name'	=>	$id,
					'map'			=>	$id . '.jpg',
					'lang'			=>	$this->lang
				);
			}
		}
	}
	
	private function getMapByName($name)
	{
		if (trim($name) == '')
			return false;
			
		$data = $this->readURL($name, 'zone', false);
		
		if (!$data)
			return false;
			
		// make sure it didn't redirect
		if (preg_match('#Location: \/zone=([0-9]{1,10})#s', $data, $match))
		{
			return array(
				'name'			=>	ucwords(strtolower($name)),
				'search_name'	=>	$name,
				'id'		=>	$match[1],
				'map'			=>	$match[1] . '.jpg',
				'lang'			=>	$this->lang
			);	
		}
		
		$line = $this->zoneLine($data);
		
		if (!$line)
			return false;
		else
		{
			if (!$json = json_decode($line, true))
				return false;
			
			foreach ($json as $zones)
			{
				if (stripslashes(strtolower($zones['name'])) == stripslashes(strtolower($name)))
				{
					return array(
						'name'			=>	stripslashes($zones['name']),
						'search_name'	=>	$name,
						'id'			=>	$zones['id'],
						'map'			=>	$zones['id'] . '.jpg',
						'lang'			=>	$this->lang
					);
				}
			}
			return false;
		}
	}
	
	private function zoneLine($data)
	{
		$parts = explode(chr(10), $data);
		foreach ($parts as $line)
		{
			if (strpos($line, "new Listview({template: 'zone', id: 'zones', name:") !== false)
			{
				$line = substr($line, strpos($line, 'data: [{') + 6);
				$line = str_replace('});', '', $line);
				return $line;
				break;	
			}
		}
		
		// if line isn't found then fail
		return false;
	}

	private function nameLine($data)
	{
		$parts = explode(chr(10), $data);
		foreach ($parts as $line)
		{	
			if (strpos($line, 'var g_pageInfo = {type:') !== false)
			{
				return $line;
				break;
			}
		}
		
		// returns false if line isn't found
		return false;
	}
}

?>
