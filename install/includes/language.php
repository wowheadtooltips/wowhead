<?php
/**
* Wowhead Tooltips - Installation Language Class
* By: Adam "crackpot" Koch (support@wowhead-tooltips.com)
**/

/**
    Copyright (C) 2010  Adam Koch (email : support@wowhead-tooltips.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
**/

class language
{
	private $lang_pack;
	
	public function __construct()
	{
		$this->lang_pack = dirname(__FILE__) . '/language_pack.php';
		require_once($this->lang_pack);
		
		if (is_array($l))
		{
			foreach ($l as $key => $value)
			{
				if (!empty($key) && $key != $value)
					$this->$key = strval($value);
			}
		}
	}
	
	public function sprintf($string)
	{
		$arg_list = func_get_args();
		$num_args = count($arg_list);

		for($i = 1; $i < $num_args; $i++)
		{
			$string = str_replace('{'.$i.'}', $arg_list[$i], $string);
		}
		
		return $string;
	}
}
?>