<?php
/**
*
* @package Wowhead Tooltips
* @version wowhead_currency.php 4.4
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/
class wowhead_currency extends wowhead
{
	public $lang;
	public $patterns;
	public $language;
	public $config;	
	
	public function __construct()
	{
		$this->patterns = new wowhead_patterns();
		$this->language = new wowhead_language();
		$this->config = new wowhead_config();
		$this->config->loadConfig();
	}
	
	public function close()
	{
		unset($this->lang, $this->config, $this->language, $this->patterns);
	}
	
	public function parse($name, $args = array())
	{
		if (trim($name) == '')
			return false;
		
		$cache = new wowhead_cache();
		$this->lang = (!array_key_exists('lang', $args)) ? $this->config->lang : $args['lang'];
		$this->language->load($this->lang);
		
		if (!$result = $cache->getCurrency($name, $this->lang))
		{
			$result = (is_numeric($name)) ? $this->getCurrencyByID($name) : $this->getCurrencyByName($name);
			if (!$result)
			{
				$cache->close();
				return $this->notFound($this->language->words['currency'], $name);
			}
			else
			{
				$cache->saveCurrency($result);
				$cache->close();
				return $this->generateOutput($result);
			}
		}
		else
		{
			$result['link'] = $this->generateLink($result['id'], 'currency');
			$cache->close();
			return $this->generateOutput($result);	
		}	
	}
	
	private function getCurrencyByName($name)
	{
		if (trim($name) == '')
			return false;
		
		$data = $this->readURL($name, 'currency', false);
		if (!$data)
			return false;
		
		$line = $this->currencyLine($data);
		if (!$line)
			return false;

		if (!$json = json_decode($line, true))
			return false;
		else
		{
			foreach ($json as $currency)
			{
				if (strtolower(stripslashes($currency['name'])) == strtolower(stripslashes($name)))
				{
					return $this->getCurrencyByID($currency['id'], array(
						'id'			=>	$currency['id'],
						'name'			=>	$currency['name'],
						'search_name'	=>	$name,
						'icon'			=>	$currency['icon']
					));
				}
			}	
		}
		return false;	
	}
	
	private function getCurrencyByID($id, $result = array())
	{
		if (empty($id) || !is_numeric($id))
			return false;
		
		$data = $this->readURL($id, 'currency_query', false);
		if (!$data)
			return false;
		
		// grabbing by ID, need to get preliminary information first	
		if (sizeof($result) == 0)
		{
			$result = array(
				'id'			=>	$id,
				'name'			=>	$this->getName($data),
				'search_name'	=>	$id,
				'icon'			=>	'http://static.wowhead.com/images/wow/icons/small/'. $this->getIcon($data) . '.jpg',
				'tipicon'		=>	$this->getIcon($data)
			);
			if ($result['name'] == false || $result['icon'] == false)
				return false;
		}
		else
		{
			$result['icon'] = 'http://static.wowhead.com/images/wow/icons/small/' . $this->getIcon($data) . '.jpg';
			$result['tipicon'] = $this->getIcon($data);
		}
		
		// now we can continue
		if (!$info = $this->getInformation($data))
			return false;
		if (!$currency_for = $this->getCurrencyFor($data))
			return false;
		$result['lang'] = $this->lang;
		$result['tooltip'] = $this->generateTooltip($this->patterns->pattern('currency_tooltip'), $result['name'], $info);
		$result['currency_for'] = $currency_for;
		$result['link'] = $this->generateLink($result['id'], 'currency');
		return $result;
	}
	
	private function getCurrencyFor($data)
	{
		if (trim($data) == '')
			return false;
		$found = false;
		$lines = explode(chr(10), $data);
		foreach($lines as $line)
		{
			if (preg_match("#\[(.+?)\]=\{(.+?):'(.+?)',quality:(.+?)#s", $line))
			{
				$found = true;
				break;
			}
			else
				continue;	
		}
		if (!$found) { return false; }
		$items = explode(';', $line); $html = '';
		foreach($items as $item)
		{
			if (preg_match("#\[([0-9]{1,10})\]=\{(.+?):'(.+?)',quality:([0-9]{1}),icon:'(.+?)'#s", $item, $match))
			{
				$search = array(
					'{icon}',
					'{link}',
					'{qid}',
					'{name}',
				);
				$replace = array(
					'http://static.wowhead.com/images/wow/icons/small/' . strtolower($match[5]) . '.jpg',
					$this->generateLink($match[1], 'item'),
					$match[4],
					stripslashes($match[3]),
				);
				$html .= str_replace($search, $replace, $this->patterns->pattern('currency_item'));	
			}
		}
		return (trim($html) == '') ? false : $html;
	}
	
	private function getInformation($data)
	{
		if (trim($data) == '' || !preg_match('#Markup.printHtml\("(.+?)", "efhjkdsoicjx", \{ allow: Markup.CLASS_ADMIN, dbpage: true \}\);#s', $data, $match))
			return false;
		// strip any bbcode formatting
		return str_replace(
			array('[', ']', '\''),
			array('<', '>', ''),
			$match[1]
		);
	}
	
	private function getIcon($data)
	{
		if (trim($data) == '')
			return false;
		return (!preg_match("#Icon.create\('(.+?)', 1\)#s", $data, $match)) ? false : strtolower($match[1]);	
	}
	
	private function getName($data)
	{
		if (trim($data) == '')
			return false;
		return (!preg_match('#<title>(.+?) - Currency - World of Warcraft</title>#s', $data, $match)) ? false : $match[1];
	}

	private function currencyLine($data)
	{
		$lines = explode(chr(10), $data);
		foreach ($lines as $line)
		{
			if (strpos($line, "new Listview({template: 'currency', id: 'currency',") !== false)
			{
				// format it for valid JSON
				$line = substr($line, strpos($line, 'data: [{') + 6);
				$search = array('});', 'id', 'category', 'name', 'icon', "'", '\"');
				$replace = array('', '"id"', '"category"', '"name"', '"icon"', '"', "'");
				$line = str_replace($search, $replace, $line);
				return $line;
			}	
		}	
	}

	private function generateOutput($info)
	{
		$search = array('{icon}', '{name}', '{items}', '{tooltip}', '{link}', '{tipicon}');
		$replace = array($info['icon'], $info['name'], $info['currency_for'], $info['tooltip'], $info['link'], $info['tipicon']);
		$pattern = $this->patterns->pattern('currency');
		$pattern = str_replace($search, $replace, $pattern);
		$pattern = str_replace(chr(10), '', $pattern);
		return $pattern;
	}

	private function generateTooltip($pattern)
	{
		$arg_list = func_get_args();
		$num_args = sizeof($arg_list);
		for ($i = 0; $i < $num_args; $i++)
			$pattern = str_replace('{' . $i . '}', $arg_list[$i], $pattern);
		return $pattern;
	}
}
?>