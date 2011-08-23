<?php
/**
*
* @package Wowhead Tooltips
* @version language_pack.php 4.0
* @copyright (c) 2010 Adam Koch <http://wowhead-tooltips.com>
* @license http://www.wowhead-tooltips.com/downloads/terms-of-use/
*
*/
$l['recheck'] = 'Recheck';
$l['none'] = 'None';
$l['not_installed'] = 'Not Installed';
$l['installed'] = 'Installed';
$l['not_writable'] = 'Not Writable';
$l['writable'] = 'Writable';
$l['done'] = 'done';
$l['next'] = 'Next';
$l['error'] = 'Error';
$l['found'] = 'Found';
$l['not_found'] = 'Not Found';
$l['enabled'] = 'Enabled';
$l['disabled'] = 'Disabled';

$l['title'] = 'Wowhead Tooltips Installation Wizard';
$l['welcome'] = 'Welcome';
$l['license_agreement'] = 'License Agreement';
$l['check_requirements'] = 'Check Requirements';
$l['mysql_settings'] = 'MySQL Settings';
$l['create_tables'] = 'Create Tables';
$l['module_settings'] = 'Module Settings';
$l['general_settings'] = 'General Settings';
$l['item_settings'] = 'Item Settings';
$l['armory_settings'] = 'Armory Settings';
$l['write_configuration'] = 'Review Configuration';
$l['finished'] = 'Installation Complete';

$l['already_installed'] = "Wowhead Tooltips is already installed";
$l['whtt_already_installed'] = '<p>Wowhead Tooltips has already detected that is has been installed in this directory.</p>

<p>If you wish to continue with the installed rename <tt>config.php</tt> and refresh this page.</p>';

$l['welcome_step'] = '<p>Welcome to the installation wizard for Wowhead Tooltips {1}. This wizard will install and configure a copy of Wowhead Tooltips on your server.</p>
<p>Now that you\'ve uploaded the Wowhead Tooltips files the database and settings need to be created and imported. Below is an outline of what is going to be completed during installation.</p>
<ul>
	<li>Wowhead Tooltips requirements checked</li>
	<li>MySQL settings</li>
	<li>Create MySQL Tables</li>
	<li>General settings</li>
	<li>Module Settings</li>
	<li>Armory settings</li>
	<li>Review settings</li>
	<li>Write <tt>config.php</tt> file and save settings in MySQL</li>
</ul>
<p>After each step has successfully been completed, click Next to move on to the next step.</p>
<p>Click "Next" to view the Wowhead Tooltips license agreement.</p>';

$l['license_step'] = '<div class="license_agreement">
{1}
</div>
<p><strong>By clicking Next, you agree to the terms stated in the License Agreement above.</strong></p>';

// requirements checks
$l['req_step_top'] = '<p>Before you can install Wowhead Tooltips, we must check that you meet the minimum requirements for installation.</p>';
$l['req_step_reqtable'] = '<div class="border_wrapper">
			<div class="title">Requirements Check</div>
		<table class="general" cellspacing="0">
		<thead>
			<tr>
				<th colspan="2" class="first last">Requirements</th>
			</tr>
		</thead>
		<tbody>
		<tr class="first">
			<td class="first">PHP Version:</td>
			<td class="last alt_col">{1}</td>
		</tr>
		<tr class="alt_row">
			<td class="first">MySQL Installed:</td>
			<td class="last alt_col">{2}</td>
		<tr>
		<tr class="alt_row">
			<td class="first">SimpleXML Enabled:</td>
			<td class="last alt_col">{3}</td>
		<tr>
			<td class="first">Config File Writable:</td>
			<td class="last alt_col">{4}</td>
		</tr>
		<tr class="alt_row">
			<td class="first">Zones Directory Writable:</td>
			<td class="last alt_col">{5}</td>
		</tr>
		<tr class="alt_row">
			<td class="first">SQL Table Scheme Exists:</td>
			<td class="last alt_col">{6}</td>
		</tr>
		<tr class="alt_row">
			<td class="first">FOpen Allow URL:</td>
			<td class="last alt_col">{7}</td>
		</tr>
		<tr class="alt_row">
			<td class="first">CURL Enabled:</td>
			<td class="last alt_col">{8}</td>
		</tr>
		<tr class="alt_row">
			<td class="first">BCMath Enabled:</td>
			<td class="last alt_col">{9}</td>
		</tr>
		<tr class="alt_row">
			<td class="first">JSON Enabled:</td>
			<td class="last alt_col">{10}</td>
		</tr>
		</tbody>
		</table>
		</div>';
$l['req_step_reqcomplete'] = '<p><strong>Congratulations, you meet the requirements to run Wowhead Tooltips.</strong></p>
<p>Click Next to continue with the installation process.</p>';

$l['req_step_span_fail'] = '<span class="fail"><strong>{1}</strong></span>';
$l['req_step_span_pass'] = '<span class="pass">{1}</span>';

$l['req_step_error_box'] = '<p><strong>{1}</strong></p>';
$l['req_step_error_phpversion'] = 'Wowhead Tooltips requires PHP 5.0 or later to run. You currently have {1} installed.';
$l['req_step_error_mysql'] = 'Wowhead Tooltips requires MySQL to be installed and enabled.';
$l['req_step_error_simplexml'] = 'Wowhead Tooltips requires SimpleXML to be enabled in order to run.';
$l['req_step_error_configfile'] = 'The empty config file (<tt>./config.php</tt>) must be writable.  Please change permissions to 755 or 777, depending on your host.';
$l['req_step_error_zonesdir'] = 'The zone images directory (<tt>./images/zones/</tt>) must be writable.  Please change permissions to 755 or 777, depending on your host.';
$l['req_step_error_tablescheme'] = 'The SQL table scheme (<tt>./install/includes/table_scheme.sql</tt>) does not exist.  Please locate the file and try again.';
$l['req_step_error_fopenurl'] = 'The setting "allow_url_fopen" must be set to "on" in order for this script to work properly.  Talk to your host about changing it.';
$l['req_step_error_curlk'] = 'Your installation of PHP has the cURL extension disabled, you must enable it to use Wowhead Tooltips.';
$l['req_step_error_bcdiv'] = 'Your installation of PHP has the BCMath extension disabled, you must enable it to use Wowhead Tooltips.';
$l['req_step_error_json'] = 'Your installation of PHP had the JSON functions disabled, you must enable it to use Wowhead Tooltips.';
$l['req_step_error_tablelist'] = '<div class="error">
<h3>Error</h3>
<p>The requirements check failed due to the reasons below. Wowhead Tooltips installation cannot continue because you did not meet the requirements. Please correct the errors below and try again:</p>
{1}
</div>';

// mysql settings
$l['mysql_host'] = 'MySQL Host: <span style="font-size: smaller; font-style: italic; color: gray;  font-weight: normal;">Generally \'localhost\'</span>';
$l['mysql_user'] = 'MySQL Username:';
$l['mysql_pass'] = 'MySQL Password:';
$l['mysql_db'] = 'MySQL Database Name:';
$l['mysql_table_settings'] = 'Table Settings';
$l['mysql_table_prefix'] = 'Table Prefix:';
$l['mysql_step_config_db'] = '<p>It is now time to configure the database that Wowhead Tooltips will use as well as your cache. If you do not have this information, it can usually be obtained from your webhost.</p>';
$l['mysql_step_config_table'] = '<div class="border_wrapper">
<div class="title">MySQL Configuration <span style="font-size: smaller; font-style: italic; font-weight: normal;">All fields are required.</span></div>
<table class="general" cellspacing="0">
<tr>
	<th colspan="2" class="first last">MySQL Settings</th>
</tr>
{1}
</table>
</div>
<p style="font-style: italic; color: gray;  font-size: smaller;">NOTE:  If the tables already exist then they will be removed.</p>
<p>Once you\'ve checked these details are correct, click next to continue.</p>';

$l['mysql_step_error_config'] = '<div class="error">
<h3>Error</h3>
<p>There seems to be one or more errors with the database configuration information that you supplied:</p>
{1}
<p>Once the above errors are corrected, continue with the installation.</p>
</div>';
$l['mysql_step_error_noconnect'] = 'Could not connect to the database server at \'{1}\' with the supplied username and password. Are you sure the hostname and user details are correct?';
$l['mysql_step_error_nodbname'] = 'Could not select the database \'{1}\'. Are you sure it exists and the specified username and password have access to it?';
$l['mysql_step_error_nouser'] = 'No username for MySQL was provided.';
$l['mysql_step_error_nopass'] = 'No password for MySQL was provided.';
$l['mysql_step_error_nohost'] = 'No hostname for MySQL was provided.';
$l['mysql_step_error_nodb'] = 'No database name for MySQL was provided.';
$l['mysql_step_error_noprefix'] = 'No database prefix for MySQL was provided.';
$l['mysql_step_error_nomanageuser'] = 'No username for the manage settings script was provided.';
$l['mysql_step_error_nomanagepass'] = 'No password for the manage settings script was provided.';
$l['mysql_step_connected'] = '<p>Connection to the database and the database you selected was successful.</p>';

$l['mysql_table_create_header'] = '<h3>Creating new SQL tables.</h3>';
$l['mysql_table_delete_header'] = '<h3>Checking for previous SQL tables.</h3>';
$l['mysql_table_wrapper_start'] = '<ul>';
$l['mysql_table_wrapper_end'] = '</ul>';
$l['mysql_table_create_success'] = '<li>Successfully created <tt>{1}</tt>.</li>';
$l['mysql_table_create_fail'] = '<li>Failed to create <tt>{1}</tt>.</li>';
$l['mysql_table_delete_success'] = '<li>Successfully removed <tt>{1}</tt>.</li>';
$l['mysql_table_delete_fail'] = '<li>Failed to remove <tt>{1}</tt>.</li>';
$l['mysql_table_create_complete'] = '<p>SQL tables were successfully created, please click Next.</p>';

// general settings
$l['general_config_table'] = '<p>Now it is time to configure the script itself to your liking.</p>
		<div class="border_wrapper">
			<div class="title">General Configuration</div>
			<table class="general" cellspacing="0">
				<tbody>
				<tr>
					<th colspan="2" class="first last">Language Settings</th>
				</tr>
				<tr class="first">
					<td class="first"><label for="whp_lang">Default Language:</label></td>
					<td class="last alt_col">
						<select name="whp_lang">
							<option value="en" selected="selected">EN - English</option>
							<option value="es">ES - Spanish</option>
							<option value="fr">FR - French</option>
							<option value="de">DE - German</option>
							<option value="ru">RU - Russian</option>
						</select>
					</td>
				</tr>
				<tr>
					<th colspan="2" class="first last">Item settings</th>
				</tr>
				<tr>
					<td class="first"><label for="external_css">Use External CSS:</label></td>
					<td class="last alt_col">
						<input type="radio" name="external_css" value="true" /> Yes
						<input type="radio" name="external_css" value="false" checked="checked" /> No
					</td>
				</tr>
				<tr>
					<td class="first"><label for="qualities[]">Force Specific Qualities:</label></td>
					<td class="last alt_col">
						<select name="qualities[]" multiple="multiple" size="7">
							<option value="0">0 - Poor (Gray)</option>
							<option value="1">1 - Common (White)</option>
							<option value="2">2 - Uncommon (Green)</option>
							<option value="3">3 - Rare (Blue)</option>
							<option value="4">4 - Epic (Purple)</option>
							<option value="5">5 - Legendary (Orange)</option>
							<option value="6">6 - Artifact (Tan-ish)</option>
						</select>
						<div style="font-size: smaller; font-style: italic; color: gray; ">Ctrl+Click to select multiple.</div>
					</td>
				</tr>
				<tr>
					<th colspan="2" class="first last">Icon Settings</th>
				</tr>
				<tr>
					<td class="first"><label for="item_show_icon">Show Item Icon:</label></td>
					<td class="last alt_col">
						<input type="radio" name="item_show_icon" value="true" /> Yes
						<input type="radio" name="item_show_icon" value="false" checked="checked" /> No
					</td>
				</tr>
				<tr>
					<td class="first"><label for="spell_show_icon">Show Spell Icon:</label></td>
					<td class="last alt_col">
						<input type="radio" name="spell_show_icon" value="true" /> Yes
						<input type="radio" name="spell_show_icon" value="false" checked="checked" /> No
					</td>
				</tr>
				<tr>
					<td class="first"><label for="achievement_show_icon">Show Achievement Icon:</label></td>
					<td class="last alt_col">
						<input type="radio" name="achievement_show_icon" value="true" /> Yes
						<input type="radio" name="achievement_show_icon" value="false" checked="checked" /> No
					</td>
				</tr>
				<tr>
					<th colspan="2" class="first last">Profiler Settings</th>
				</tr>
				<tr>
					<td class="first"><label for="profile_region">Region:</label></td>
					<td class="last alt_col">
						<select name="profile_region">
							<option value="us" selected="selected">US - United States/North America</option>
							<option value="eu">EU - European</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="first"><label for="profile_realm">Realm:</label></td>
					<td class="last alt_col"><input type="text" class="text_input" name="profile_realm" value="Bleeding Hollow" /></td>
				</tr>
				<tr>
					<th colspan="2" class="first last">Miscellaneous</th>
				</tr>
				<tr>
					<td class="first"><label for="max_parses">Maximum Parses:</label></td>
					<td class="last alt_col">
						<input type="text" class="text_input" name="max_parses" value="0"/>
						<div style="font-size: smaller; font-style: italic; color: gray; ">0 for unlimited (default).</div>
					</td>
				</tr>
				<tr class="first">
					<td class="first"><label for="event_cache">Event Cache Time:</label> <span style="font-size: smaller; font-style: italic; color: gray;">In Seconds</span></td>
					<td class="last alt_col"><input type="text" class="text_input" name="event_cache" value="60 * 60 * 24 * 14"/></td>
				</tr>
				<tr>
					<td class="first"><label for="race_gender">Race Icon Gender:</label></td>
					<td class="last alt_col">
						<input type="radio" name="race_gender" value="male" /> Male
						<input type="radio" name="race_gender" value="female" checked="checked" /> Female
					</td>
				</tr>
				<tr>
					<td class="first"><label for="transfer_map">Transfer Zone Map:</label></td>
					<td class="last alt_col">
						<input type="radio" name="transfer_map" value="true" checked="checked" /> Yes
						<input type="radio" name="transfer_map" value="false" /> No
					</td>
				</tr>
				<tr>
					<td class="first"><label for="log_errors">Log Errors:</label></td>
					<td class="last alt_col">
						<input type="radio" name="log_errors" value="true" checked="checked" /> Yes
						<input type="radio" name="log_errors" value="false" /> No
					</td>
				</tr>
				<tr>
					<td class="first"><label for="WOWHEAD_DEBUG">Enable Debugging:</label></td>
					<td class="last alt_col">
						<input type="radio" name="WOWHEAD_DEBUG" value="true" /> Yes
						<input type="radio" name="WOWHEAD_DEBUG" value="false" checked="checked" /> No
					</td>
				</tr>
				</tbody>
			</table>
		</div>

	<p>Once you\'ve correctly entered the details above and are ready to proceed, click Next.</p>';
	
// module settings
$l['module_config_table'] = '<p>Now you must select which modules you want enabled.</p>
		<div class="border_wrapper">
			<div class="title">Module Configuration</div>
			<table class="general" cellspacing="0">
				<tbody>
				<tr>
					<th colspan="2" class="first last">Enabled Modules</th>
				</tr>
				{1}
				</tbody>
			</table>
		</div>

	<p>Once you\'ve correctly entered the details above and are ready to proceed, click Next.</p>';
$l['module_config_table_row'] = '
				<tr class="first">
					<td class="first"><label for="{2}">{1}</label></td>
					<td class="last alt_col">
						<input type="radio" name="{2}" value="true" checked="checked" /> Enabled
						<input type="radio" name="{2}" value="false" /> Disabled
					</td>
				</tr>';
				
// armory settings
$l['armory_config_table'] = '<p>Finally, configure the armory, guild, and recruit modules to your liking.</p>
		<div class="border_wrapper">
			<div class="title">Armory Configuration</div>
			<table class="general" cellspacing="0">
				<tbody>
				<tr>
					<th colspan="2" class="first last">General Settings</th>
				</tr>
				<tr class="first">
					<td class="first"><label for="armory_region">Region:</label></td>
					<td class="last alt_col">
						<select name="armory_region">
							<option value="us" selected="selected">US - United States/North America</option>
							<option value="eu">EU - European</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="first"><label for="armory_realm">Realm:</label></td>
					<td class="last alt_col"><input type="text" class="text_input" name="armory_realm" value="Bleeding Hollow" /></td>
				</tr>
				<tr>
					<td class="first"><label for="armory_date_format">Date Format:</label> <a href="http://www.php.net/function.date" target="_blank">?</a></td>
					<td class="last alt_col"><input type="text" class="text_input" name="armory_date_format" value="Y-m-d" /></td>
				</tr>
				<tr>
					<td class="first"><label for="armory_time_format">Time Format:</label> <a href="http://www.php.net/function.date" target="_blank">?</a></td>
					<td class="last alt_col"><input type="text" class="text_input" name="armory_time_format" value="h:i:s A" /></td>
				</tr>
				<tr class="alt_row last">
					<td class="first"><label for="armory_item_level">Average iLvL in Tooltip:</label></td>
					<td class="last alt_col">
						<input type="radio" name="armory_item_level" value="true" checked="checked" /> Yes
						<input type="radio" name="armory_item_level" value="false" /> No
					</td>
				</tr>
				<tr>
					<th colspan="2" class="first last">Guild Rank Settings</th>
				</tr>
				<tr class="first">
					<td class="first"><label for="armory_show_rank">Show Character Rank:*</label><br /><span style="font-size: smaller; font-style: italic; color: gray;">This will add an extra query to the <a href="http://wowarmory.com">Armory</a>.</span></td>
					<td class="last alt_col">
						<input type="radio" name="armory_show_rank" value="true" checked="checked" /> Yes
						<input type="radio" name="armory_show_rank" value="false" /> No
					</td>
				</tr>
				<tr class="first">
					<td class="first" valign="top"><label for="armory_rank_0">Rank 0:</label>  <span style="font-size: smaller; font-style: italic; color: gray;">Default: Guild Master</span></td>
					<td class="last alt_col"><input type="text" class="text_input" name="armory_rank_0" value="Guild Master" /></td>
				</tr>
				<tr class="first">
					<td class="first" valign="top"><label for="armory_rank_1">Rank 1:</label>  <span style="font-size: smaller; font-style: italic; color: gray;">Default: Raid Leader</span></td>
					<td class="last alt_col"><input type="text" class="text_input" name="armory_rank_1" value="Raid Leader" /></td>
				</tr>
				<tr class="first">
					<td class="first" valign="top"><label for="armory_rank_2">Rank 2:</label>  <span style="font-size: smaller; font-style: italic; color: gray;">Default: Officer</span></td>
					<td class="last alt_col"><input type="text" class="text_input" name="armory_rank_2" value="Officer" /></td>
				</tr>
				<tr class="first">
					<td class="first" valign="top"><label for="armory_rank_3">Rank 3:</label>  <span style="font-size: smaller; font-style: italic; color: gray;">Default: O-Alt</span></td>
					<td class="last alt_col"><input type="text" class="text_input" name="armory_rank_3" value="O-Alt" /></td>
				</tr>
				<tr class="first">
					<td class="first" valign="top"><label for="armory_rank_4">Rank 4:</label>  <span style="font-size: smaller; font-style: italic; color: gray;">Default: Raider</span></td>
					<td class="last alt_col"><input type="text" class="text_input" name="armory_rank_4" value="Raider" /></td>
				</tr>
				<tr class="first">
					<td class="first" valign="top"><label for="armory_rank_5">Rank 5:</label>  <span style="font-size: smaller; font-style: italic; color: gray;">Default: Member</span></td>
					<td class="last alt_col"><input type="text" class="text_input" name="armory_rank_5" value="Member" /></td>
				</tr>
				<tr class="first">
					<td class="first" valign="top"><label for="armory_rank_6">Rank 6:</label>  <span style="font-size: smaller; font-style: italic; color: gray;">Default: Recruit</span></td>
					<td class="last alt_col"><input type="text" class="text_input" name="armory_rank_6" value="Recruit" /></td>
				</tr>
				<tr class="first">
					<td class="first" valign="top"><label for="armory_rank_7">Rank 7:</label>  <span style="font-size: smaller; font-style: italic; color: gray;">Default: Alt</span></td>
					<td class="last alt_col"><input type="text" class="text_input" name="armory_rank_7" value="Alt" /></td>
				</tr>
				<tr class="first">
					<td class="first" valign="top"><label for="armory_rank_8">Rank 8:</label>  <span style="font-size: smaller; font-style: italic; color: gray;">Default: Friend</span></td>
					<td class="last alt_col"><input type="text" class="text_input" name="armory_rank_8" value="Friend" /></td>
				</tr>
				<tr class="first">
					<td class="first" valign="top"><label for="armory_rank_9">Rank 9:</label>  <span style="font-size: smaller; font-style: italic; color: gray;">Default: Timeout</span></td>
					<td class="last alt_col"><input type="text" class="text_input" name="armory_rank_9" value="Timeout" /></td>
				</tr>
				<tr>
					<th colspan="2" class="first last">Cache Settings</th>
				</tr>
				<tr class="first">
					<td class="first"><label for="armory_char_cache">Character Cache Time:</label> <span style="font-size: smaller; font-style: italic; color: gray;">In Seconds</span></td>
					<td class="last alt_col"><input type="text" class="text_input" name="armory_char_cache" value="60 * 60 * 6"/></td>
				</tr>
				<tr class="alt_row last">
					<td class="first"><label for="armory_guild_cache">Guild Cache Time:</label> <span style="font-size: smaller; font-style: italic; color: gray;">In Seconds</span></td>
					<td class="last alt_col"><input type="text" class="text_input" name="armory_guild_cache" value="60 * 60 * 24"/></td>
				</tr>
				<tr>
					<th colspan="2" class="first last">Image Settings</th>
				</tr>
				<tr class="first">
					<td class="first"><label for="armory_image_url">Image URI:*</label> <span style="font-size: smaller; font-style: italic; color: gray;">Will attempt to auto-detect.</span></td>
					<td class="last alt_col"><input type="text" class="text_input" name="armory_image_url" value="{1}" /></td>
				</tr>
				<tr>
					<td class="first"><label for="armory_icons">Show Armory Icons:</label></td>
					<td class="last alt_col">
						<input type="radio" name="armory_icons" value="true" checked="checked" /> Yes
						<input type="radio" name="armory_icons" value="false" /> No
					</td>
				</tr>
				<tr>
					<td class="first"><label for="armory_class_icon">Show Class Icon:</label></td>
					<td class="last alt_col">
						<input type="radio" name="armory_class_icon" value="true" checked="checked" /> Yes
						<input type="radio" name="armory_class_icon" value="false" /> No
					</td>
				</tr>
				<tr class="alt_row last">
					<td class="first"><label for="armory_race_icon">Show Race Icon:</label></td>
					<td class="last alt_col">
						<input type="radio" name="armory_race_icon" value="true" checked="checked" /> Yes
						<input type="radio" name="armory_race_icon" value="false" /> No
					</td>
				</tr>
				<tr>
					<th colspan="2" class="first last">Recruit Settings</th>
				</tr>
				<tr class="first">
					<td class="first"><label for="recruit_region">Region:</label></td>
					<td class="last alt_col">
						<select name="recruit_region">
							<option value="us" selected="selected">US - United States/North America</option>
							<option value="eu">EU - European</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="first"><label for="recruit_realm">Realm:</label></td>
					<td class="last alt_col"><input type="text" class="text_input" name="recruit_realm" value="Bleeding Hollow" /></td>
				</tr>
				<tr class="alt_row last">
					<td class="first"><label for="recruit_cache">Recruit Cache Time:</label> <span style="font-size: smaller; font-style: italic; color: gray;">In Seconds</span></td>
					<td class="last alt_col"><input type="text" class="text_input" name="recruit_cache" value="60 * 60 * 6"/></td>
				</tr>
				</tbody>
			</table>
		</div>
	<div style="padding-top: 5px; font-size: smaller; font-style: italic; color: gray; ">*Include the trailing slash if the auto-detect failed.</div>
	<p style="color: red; font-weight: bold;">NOTE:  Most settings on this page do not need to be changed (cache times), but make sure the settings are correct, they are not validated before being sent to the next step.  If you mess up you could cause the script to generate undesireable results or not work at all.</p>
	<p>After clicking Next you will be able to review the contents that will be put into <tt>config.php</tt> before it is saved.</p>';

// write configuration
$l['write_config_check'] = '<p>Before writing the configuration settings take a second to look them over and ensure they are correct.</p>
<div class="border_wrapper">
	<div class="title">Review Configuration Settings</div>
	<table class="general" cellspacing="0">
		<tbody>
			<tr>
				<th colspan="2" class="first last">MySQL Settings</th>
			</tr>
			{1}
			<tr>
				<th colspan="2" class="first last">Manage Settings Login Information</th>
			</tr>
			{7}
			<tr>
				<th colspan="2" class="first last">General Settings</th>
			</tr>
			{2}
			<tr>
				<th colspan="2" class="first last">Module Settings</th>
			</tr>
			{3}
			<tr>
				<th colspan="2" class="first last">Armory Settings</th>
			</tr>
			{4}
			<tr>
				<th colspan="2" class="first last">Recruit Settings</th>
			</tr>
			{5}
		</tbody>
	</table>
</div>
<textarea name="config_content" style="display: none;" locked="locked">{6}</textarea>
<p><strong>Click Next to finish the installation and write the configuration file.</strong></p>';
$l['write_config_row'] = '
<tr class="first">
	<td class="first">{1}</td>
	<td class="last alt_col"><tt>{2}</div></td>
</tr>';
$l['write_config_qualities'] = '<select name="qualities[]" multiple="multiple" size="7" locked="locked">
	<option value="0" {1}>0 - Poor (Gray)</option>
	<option value="1" {2}>1 - Common (White)</option>
	<option value="2" {3}>2 - Uncommon (Green)</option>
	<option value="3" {4}>3 - Rare (Blue)</option>
	<option value="4" {5}>4 - Epic (Purple)</option>
	<option value="5" {6}>5 - Legendary (Orange)</option>
	<option value="6" {7}>6 - Artifact (Tan-ish)</option>
</select>';
$l['config_contents'] = '&lt;?php
/**
* Wowhead Tooltips - Configuration File
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

/**
 * Automatically Generated on {date} by Installation Script.
 **/
// wowhead version
if (!defined("WOWHEAD_VERSION"))
	define("WOWHEAD_VERSION", "{7}");

// for debugging purposes
define("WOWHEAD_DEBUG", {1});

/**
* DATABASE SETTINGS
**/
	define("WHP_DB_HOST", "{2}");					// hostname (usually this doesn"t need to be changed)
	define("WHP_DB_USER", "{3}");			// username
	define("WHP_DB_PASS", "{4}");					// password
	define("WHP_DB_NAME", "{5}");		// database name
	define("WHP_DB_PREFIX", "{6}");
/**
* END DATABASE SETTINGS
**/
?>';

// finish installation
$l['finish_step_fail'] = '<p>Failed to write the configuration data to <tt>config.php</tt>.</p>';
$l['finish_step_pass'] = '<p>Congratulations, you have successfully installed <a href="http://wowhead-tooltips.com">Wowhead Tooltips</a> version {1}.</p>
<h3>What Do I Do Now?</h3>
<p style="text-indent: 20px;">Well now you would follow the correct <a href="http://wowhead-tooltips.com/install/">installation instructions</a> for the software that you\'re installing this script on.</p>
<h3>A Message From The Developer</h3>
<p style="text-indent: 20px;">I, Adam Koch, truly hope that you enjoy this script that I have worked so hard on for almost two years.  If you have any problems with it please feel free to contact me via the methods listed below.</p>
<h3>Contact Information</h3>
<ul>
<li>Script Website: <a href="http://www.wowhead-tooltips.com">http://www.wowhead-tooltips.com</a></li>
<li>Wiki: <a href="http://wiki.wowhead-tooltips.com">http://wiki.wowhead-tooltips.com</a></li>
<li>Personal Website (Blog): <a href="http://crackpot.ws">http://crackpot.ws</a></li>
<li>Personal E-Mail: <a href="mailto:admin@crackpot.ws">admin@crackpot.ws</a></li>
<li>Support E-Mail: <a href="mailto:support@wowhead-tooltips.com">support@wowhead-tooltips.com</a></li>
<li>Support Forums (Preferred): <a href="http://support.wowhead-tooltips.com">http://support.wowhead-tooltips.com</a></li>
<li>Twitter: <a href="http://twitter.com/wowheadtooltips">@wowheadtooltips</a></li>
</ul>
<p>If you like the script, please consider <a href="http://www.wowhead-tooltips.com/contact/donate/">donating</a> and/or adding your site to the <a href="http://www.wowhead-tooltips.com/tools/sitedb/">site database</a>.</p>
';
?>
