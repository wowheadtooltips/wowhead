<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_event.php 4.3
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/

/**
 * Wowhead World Event Module
 * @package Wowhead Tooltips
 * @extends wowhead
 */
class wowhead_event extends wowhead
{
	public $lang;
	public $patterns;
	public $language;
	public $config;
	
	private $date_format;
	
	public function __construct()
	{
		$this->patterns = new wowhead_patterns();
		$this->language = new wowhead_language();
		$this->config = new wowhead_config();
		$this->config->loadConfig();
		
		// set the date/time format
		$this->date_format = $this->config->armory_date_format . ' ' . $this->config->armory_time_format;
	}

	public function close()
	{
		unset($this->lang, $this->language, $this->patterns, $this->config);	
	}
	
	public function parse($name, $args = array())
	{
		if (trim($name) == '')
			return false;
		
		// setup variables we'll use shortly
		$cache = new wowhead_cache();
		$this->lang = (!array_key_exists('lang', $args)) ? $this->config->lang : $args['lang'];
		$this->language->load($this->lang);
		if (!$result = $cache->getEvent($name, $this->lang, $this->config->event_cache))
		{
			// how we get the result depends on whether the name is a number or alphanumeric
			$result = (is_numeric($name)) ? $this->getEventByID($name) : $this->getEventByName($name);
			if (!$result)
			{
				// not found, or error
				$cache->close();
				return $this->notFound($this->language->words['event'], $name);
			}
			else
			{
				$cache->saveEvent($result);
				$cache->close();
				return $this->generateHTML($result, 'event');
			}
		}
		else
		{
			$cache->close();
			return $this->generateHTML($result, 'event');
		}
	}
	
	private function getEventByID($id)
	{
		if (trim($id) == '')
			return false;
		
		$data = $this->readURL($id, 'event', false);
		if (!$data)
			return false;
		
		$line = $this->eventLine($data);
		if (!$line)
			return false;
		
		if (!$json = @json_decode($line, true))
			return false;
		else
		{
			foreach ($json as $event)
			{
				if (strcmp($event['id'], $id) == 0)
				{
					$result = array(
						'id'			=>	$event['id'],
						'name'			=>	$event['name'],
						'search_name'	=>	$name,
						'lang'			=>	$this->lang,
						// and for the tooltip, converted to the armory date/time formated in the config
						'startDate'		=>	date($this->date_format, strtotime($event['startDate'])),
						'endDate'		=>	date($this->date_format, strtotime($event['endDate']))
					);				
				}
			}
			
			if (!isset($result) || sizeof($result) == 0)
				return false;
			else 
			{
				$result['tooltip'] = $this->generateTooltip(str_replace(chr(10), '', $this->patterns->pattern('event_tooltip')), $result['name'], $result['startDate'], $result['endDate']);
				return $result;
			}			
		}
	}	
	
	private function getEventByName($name)
	{
		if (trim($name) == '')
			return false;

		$data = $this->readURL($name, 'event', false);
		if (!$data)
			return false;
		
		$line = $this->eventLine($data);
		if (!$line)
			return false;

		if (!$json = @json_decode($line, true))
			return false;
		else
		{
			foreach ($json as $event)
			{
				if ($this->prepareCompare($event['name']) == $this->prepareCompare($name))
				{
					$result = array(
						'id'			=>	$event['id'],
						'name'			=>	$event['name'],
						'search_name'	=>	$name,
						'lang'			=>	$this->lang,
						// and for the tooltip, converted to the armory date/time formated in the config
						'startDate'		=>	date($this->date_format, strtotime($event['startDate'])),
						'endDate'		=>	date($this->date_format, strtotime($event['endDate']))
					);
					break;
				}
			}

			if (!isset($result) || sizeof($result) == 0)
				return false;
			else 
			{
				$result['tooltip'] = $this->generateTooltip(str_replace(chr(10), '', $this->patterns->pattern('event_tooltip')), str_replace("'", "", $result['name']), $result['startDate'], $result['endDate']);
				//var_dump($result['tooltip']); die;
				return $result;
			}
		}
	}
	
	private function generateTooltip($pattern)
	{
		$arg_list = func_get_args();
		$num_args = sizeof($arg_list);
		for ($i = 0; $i < $num_args; $i++)
			$pattern = str_replace('{' . $i . '}', $arg_list[$i], $pattern);
		return $pattern;
	}
	
	private function eventLine($data)
	{
		$parts = explode(chr(10), $data);
		foreach ($parts as $line)
		{
			if (strpos($line, "new Listview({template: 'holiday', id: 'holidays',") !== false)
			{
				// format it for valid JSON
				$line = substr($line, strpos($line, 'data: [{') + 6);
				$line = str_replace('});', '', $line);
				return $line;
			}
		}
		return false;
	}
	
	private function prepareCompare($in)
	{
		$chars = array(":", ";", " ", ",", ".", "'");
		$in = str_replace($chars, '', $in);
		return stripslashes(strtolower($in));
	}
}
?>
