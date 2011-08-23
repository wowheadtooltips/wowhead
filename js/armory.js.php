<?php
/**
* Wowhead Tooltips - JavaScript Stand-Alone
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

header('Content-Type: application/x-javascript', true);
$armory_self_dir = dirname(dirname($_SERVER['PHP_SELF']));
?>

var armory_self_dir = '<?php print $armory_self_dir; ?>';

<?php
foreach (array (
				'jquery-1.4.2.min.js',
				'jquery.cluetip.js') as $f) {
	print file_get_contents ($f) . chr(10);
}
?>


jQuery.noConflict();
jQuery(document).ready(function () {
	jQuery('.armory_tip').cluetip ({
		showTitle: false,
		dropShadow: false,
		tracking: true,
		fx: {
			open: 'fadeIn'
		}
	});
	
	jQuery('a.zone_tip').cluetip({
		showTitle: false,
		dropShadow: false,
		tracking: true,
		fx:
		{
			open: 'fadeIn'
		}
	});
	
	jQuery('a.faction_tip').cluetip({
		showTitle: false,
		dropShadow: false,
		tracking: true,
		width: '500px',
		fx:
		{
			open:	'fadeIn'
		}
	});
	
	jQuery('a.event_tip').cluetip({
		showTitle:	false,
		dropShadow:	false,
		tracking:	true,
		fx:
		{
			open:	'fadeIn'
		}
	});
	
	jQuery('a.toggle_enchant').click(function(e)
	{
		jQuery(e.target).next('div.enchantToggle').toggle();
	});
	
	jQuery('a.toggle_craft').click(function(e)
	{
		jQuery(e.target).next('div.craftToggle').toggle();
	});
	
	jQuery('a.toggle_itemset').click(function(e)
	{
		jQuery(e.target).next('div.itemsetToggle').toggle();
	});
	
	jQuery('a.toggle_currency').click(function(e)
	{
		jQuery(e.target).next('div.currencyToggle').toggle();
	});
	
	jQuery("div.gearListToggle").hide();
	
	jQuery('a.faction_rewards').click(function(e)
	{
		jQuery(e.target).next('div.factionToggle').toggle();
		if (jQuery(e.target).next('div.factionToggle').is(':visible'))
		{
			var title = jQuery(e.target).attr('title');
			var rn = title.split(':', 3); 
			jQuery.ajax({
				type: "GET",
				url: armory_self_dir + "/external/faction.php",
				data: "id=" + rn[0] + "&lang=" + rn[1] + "&mode=" + rn[2],
				cache: false,
				success: function(thedata) {
					jQuery(e.target).next('div.factionToggle').html(thedata);
				}
			});
		}
	});

	jQuery("a.armory_gearlist").click(function(e)
	{
		jQuery(e.target).next("div.gearListToggle").toggle();
		if( jQuery(e.target).next('div.gearListToggle').is(':visible') ) {
		    var title = jQuery(e.target).attr('title');
			var rn = title.split(':', 3);
			jQuery.ajax({
				type: "GET",
				url: armory_self_dir + "/external/gearlist.php",
				data: "region=" + rn[0] + "&realm=" + rn[1] + "&name=" + rn[2],
				cache: false,
				success: function(newdata){
					jQuery(e.target).next('div.gearListToggle').html(newdata);
				}
			});
		}
	});
	
	jQuery("a.armory_rss").click(function(e)
	{
		jQuery(e.target).next("div.rssToggle").toggle();
		if (jQuery(e.target).next("div.rssToggle").is(":visible"))
		{
			var title = jQuery(e.target).attr('title');
			var rn = title.split(':', 3);
			jQuery.ajax({
				type: "GET",
				url: armory_self_dir + "/external/rss.php",
				data: "region=" + rn[0] + "&realm=" + rn[1] + "&name=" + rn[2],
				cache: false,
				success: function(newdata)
				{
					jQuery(e.target).next("div.rssToggle").html(newdata);
				}
			});
		}
	});
	
	jQuery('a.armory_recruit').click(function()
	{
		jQuery('div.recruitToggle').toggle();
	});
	
	jQuery('#recruitSelect').change(function()
	{
	
		var title = jQuery('#recruitSelect').attr('title');
		var rn = title.split(':', 3);
		var value = jQuery('option:selected', this).val()
		if (value != 'null')
		{
			jQuery('div.recruitContainer').show();
			jQuery('div.recruitContainer').html('Please wait...gathering data...');
			jQuery.ajax({
				type:		'GET',
				url:		armory_self_dir + '/external/recruit.php',
				data:		'mode=' + value + '&region=' + rn[0] + '&realm=' + rn[1] + '&name=' + rn[2],
				cache:		false,
				success:	function(newdata) {
					jQuery('div.recruitContainer').html(newdata);
				}
			});
		}
		else
		{
			jQuery('div.recruitContainer').html('Please wait...gathering data...');
			jQuery('div.recruitContainer').hide();
		}
	});
	
	jQuery("input[name$='toggle_talents']").live('click', function()
	{
		if ( jQuery(this).val() == 1 )
		{
			jQuery('.talentSpecOne').show();
			jQuery('.talentSpecTwo').hide();
			
			// finally the glyphs
			jQuery('#glyphOne').show();
			jQuery('#glyphTwo').hide();
		}
		else if ( jQuery(this).val() == 2 )
		{
			jQuery('.talentSpecOne').hide();
			jQuery('.talentSpecTwo').show();

			// finally the glyphs
			jQuery('#glyphOne').hide();
			jQuery('#glyphTwo').show();	
		}
	});
});
