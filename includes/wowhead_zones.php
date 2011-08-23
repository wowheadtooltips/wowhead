<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_zones.php 4.0
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

class wowhead_zones extends wowhead
{
	public $lang;
	public $patterns;
	public $language;
	public $config;
	private $image_url;
	private $image_path;
	private $wowhead_zone_maps = 'http://static.wowhead.com/images/wow/maps/%s/normal/';
	private $use_beta_maps = 'enus';
	
	public function __construct()
	{
		$this->config = new wowhead_config();
		$this->config->loadConfig();
		$this->patterns = new wowhead_patterns();
		$this->language = new wowhead_language();
		$this->images_url = $config->armory_image_url . 'images/zones/';
		$this->images_path = dirname(__FILE__) . '/../images/zones/';
	}
	
	public function close()
	{
		unset($this->lang, $this->patterns, $this->language, $this->config);	
	}
	
	public function parse($name, $args = array())
	{
		if (trim($name) == '')
			return false;
		$cache = new wowhead_cache();
		$this->lang = (!array_key_exists('lang', $args)) ? $this->config->lang : $args['lang'];
		$this->language->loadLanguage($this->lang);
		
		// change the zone map location depending on cataclysm
		$this->wowhead_zone_maps = (array_key_exists('beta', $args)) ? sprintf($this->wowhead_zone_maps, 'beta') : sprintf($this->wowhead_zone_maps, 'enus');
		$this->use_beta_maps = (array_key_exists('beta', $args)) ? 'beta' : 'enus';
		if ($this->use_beta_maps == 'beta')
			$this->lang = 'cata';
		
		// if the wowhead arg exists then format it into something the script can read
		if (array_key_exists('wowhead', $args))
		{
			$args['pins'] = $this->wowhead_map($args['wowhead']);
			unset($args['wowhead']);
		}
		
		if (!$result = $cache->getZone($name, $this->lang))
		{
			// method depends if the id or name is given
			$result = (is_numeric($name)) ? $this->getZoneByID($name) : $this->getZoneByName($name);
			
			if (!$result)
			{
				// not found
				$cache->close();
				return $this->notFound($this->language->words['zone'], $name);	
			}
			else
			{
				// transfer the zone map
				if ($this->config->transfer_map == true)
					$this->transferImage($result['map']);
					
				$cache->saveZone($result);
				$cache->close();
				return $this->toHTML($result, $args);
			}
		}
		else
		{
			// found in cache
			$cache->close();
			return $this->toHTML($result, $args);	
		}
	}
	
	private function toHTML($result, $args = array())
	{
		$html = $this->patterns->pattern('zone');
		$html = str_replace('{link}', $this->generateLink($result['id'], 'zone'), $html);
		$html = str_replace('{name}', $result['name'], $html);
		$html = str_replace('{id}', $result['id'], $html);
		$html = str_replace('{lang}', $result['lang'], $html);
		$html = str_replace('{beta}', $this->use_beta_maps, $html);
		if (array_key_exists('pins', $args))
			$html = str_replace('{pins}', $args['pins'], $html);
		return $html;
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
	
	private function getZoneByID($id)
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
	
	private function getZoneByName($name)
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
}
?>
