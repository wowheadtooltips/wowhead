<?php
/**
* Wowhead Tooltips - Installation Script
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

require_once(dirname(__FILE__) . '/../config.php');

if (!defined('WHP_DB_HOST') || !defined('WHP_DB_USER') || !defined('WHP_DB_PASS') || !defined('WHP_DB_NAME') || !defined('WHP_DB_PREFIX'))
	die('[Wowhead Tooltips]  The script has detected that Wowhead Tooltips is not installed.  You must run the install script instead.');
if (!file_exists(dirname(__FILE__) . '/update/sql_changes.sql') || !is_readable(dirname(__FILE__) . '/update/sql_changes.sql'))
	die('[Wowhead Tooltips]  The file "./update/sql_changes.sql" must exist and be readable by this script in order to complete the update.');

$conn = mysql_connect(WHP_DB_HOST, WHP_DB_USER, WHP_DB_PASS) or die(mysql_error());
mysql_select_db(WHP_DB_NAME, $conn) or die(mysql_error());

// read the contents of the SQL changes file
$contents = @file_get_contents(dirname(__FILE__) . '/update/sql_changes.sql');
if (!$contents || trim($contents) == '')
	die('[Wowhead Tooltips]  Failed to read the contents of "sql_changes.sql", refresh this page, or you may have to run the installation script instead.');
$chunks = explode(';', $contents);
$success = true;
foreach ($chunks as $query_text)
{
	if (trim($query_text) != '')
	{
		// replaces {PREFIX} with the table prefix
		$query_text = str_replace('{PREFIX}', WHP_DB_PREFIX, $query_text);
		$query = mysql_query($query_text . ';', $conn);
		
		if (!$query)
		{
			echo 'Failed to execute SQL query: <strong>' . $query_text . '</strong>.<br />';
			$success = false;
		}
	}
}

if ($success)
	echo 'The SQL tables have been updated successfully.';
else
	echo 'Some of the SQL queries failed, please refresh the page and try again.';

mysql_close($conn);
?>