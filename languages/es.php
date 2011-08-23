<?php
/**
* Wowhead Tooltips - Spanish Language Pack
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

// Temporarily using the English language pack until I can get a native speaker to do the translations.
// Google's translator was giving mixed and inconsistent results.
$lang_array = array(
	// general errors
	'notfound'			=>	'{type} "{name}" not found.',
	'curl_fail'			=>	'Could not fetch URL, neither with cURL, nor fopen',	// failed to get XML
	'faction_fail'		=>	'Facción {name} es incompatible con este script.',		// incompatible tooltip
	
	// types
	'achievement'		=>	'Achievement',
	'item'				=>	'Item',
	'item_icon'			=>	'Item',
	'itemset'			=>	'Itemset',
	'spell'				=>	'Spell',
	'quest'				=>	'Quest',
	'profile'			=>	'Profile',
	'craft'				=>	'Craftable',
	'npc'				=>	'NPC',
	'zone'				=>	'Zona',
	'object'			=>	'Objeto',
	'faction'			=>	'Facción',
	'enchant'			=>	'Enchantment',
	'title'				=>	'Title',
	'event'				=>	'Evento',
	'pet'				=>	'Pet',
	'stats'				=>	'Statistic',
	'race'				=>	'Race',
	'class'				=>	'Class',
	'talents'			=>	'Talents',
	'currency'			=>	'Currency',
	'profession'		=>	'Profession',
	
	/*
	 * Armory Module
	 */
	// armory and guild
	'armory_blocked'	=>	'There was a problem.  You\'re most likely blocked by the armory.',
	'char_not_found'	=>	'Character not found.',
	'char_no_data'		=>	'Character data not available.',
	'guild_not_found'	=>	'There was an error.  Most likely guild was not found.',
	
	// misc area
	'achievements'		=>	'Achievements',
	'achievement_pts'	=>	'Achievement Points',
	'avg_ilevel'		=>	'Average iLvl',
	'lifetime_hk'		=>	'Lifetime HKs',
	
	// base stats
	'stamina'			=>	'Stamina',
	'intellect'			=>	'Intellect',
	'strength'			=>	'Strength',
	'agility'			=>	'Agility',
	'spirit'			=>	'Spirit',
	'armor'				=>	'Armor',
	
	// spell damage and crit
	'arcane_dmg'		=>	'Arcane Damage',
	'arcane_crit'		=>	'Arcane Crit Chance',
	'fire_dmg'			=>	'Fire Damage',
	'fire_crit'			=>	'Fire Crit Chance',
	'frost_dmg'			=>	'Frost Damage',
	'frost_crit'		=>	'Frost Crit Chance',
	'shadow_dmg'		=>	'Shadow Damage',
	'shadow_crit'		=>	'Shadow Crit Chance',
	'holy_dmg'			=>	'Holy Damage',
	'holy_crit'			=>	'Holy Crit Chance',
	'nature_dmg'		=>	'Nature Damage',
	'nature_crit'		=>	'Nature Crit Chance',
	
	// spell hit, haste, and penetration
	'spell_hit'			=>	'Spell Hit Chance',
	'haste'				=>	'Haste Rating',		// spell, ranged, or melee
	'spell_pen'			=>	'Spell Penetration',
	
	// melee shizzle
	'melee_main_dmg'	=>	'Melee Main Dmg',
	'melee_main_dps'	=>	'Melee Main DPS',
	'melee_main_speed'	=>	'Melee Main Speed',
	'melee_off_dmg'		=>	'Melee Off Dmg',
	'melee_off_dps'		=>	'Melee Off DPS',
	'melee_off_speed'	=>	'Melee Off Speed',
	'melee_power'		=>	'Melee Attack Power',
	'melee_hit'			=>	'Melee Hit Chance',
	'melee_crit'		=>	'Melee Crit Chance',
	'melee_expertise'	=>	'Melee Expertise',
	
	// tanking stats
	'defense'			=>	'Defense',
	'parry_chance'		=>	'Parry Chance',
	'dodge_chance'		=>	'Dodge Chance',
	'block_chance'		=>	'Block Chance',
	'resilience'		=>	'Resilience',
	
	// healing and associated stats
	'healing'			=>	'Healing',
	'mana_regen'		=>	'Mana Regen',
	'mana_regen_cast'	=>	'Mana Regen (Casting)',
	
	// ranged stats
	'ranged_dmg'		=>	'Ranged Damage',
	'ranged_dps'		=>	'Ranged DPS',
	'ranged_speed'		=>	'Ranged Speed',
	'ranged_hit'		=>	'Ranged Hit Chance',
	'ranged_crit'		=>	'Ranged Crit Chance',
	'ranged_power'		=>	'Ranged AP',
	
	// recruit
	'already_used'		=>	'Recruit tag already used on this page.  Only 1 per page.',
	'invalid_xml'		=>	'Query returned invalid XML.  Please try again.',
	'untalented'		=>	'Character provided is untalented.',
	'no_char_breakdown'	=>	'Character talent tree breakdown does not exist.',
	
	// gear slots
	'ammo'				=>	'Ammo',
	'head'				=>	'Head',
	'neck'				=>	'Neck',
	'shoulder'			=>	'Shoulder',
	'shirt'				=>	'Shirt',
	'chest'				=>	'Chest',
	'belt'				=>	'Belt',
	'legs'				=>	'Legs',
	'feet'				=>	'Feet',
	'wrist'				=>	'Wrist',
	'gloves'			=>	'Gloves',
	'ring1'				=>	'Ring 1',
	'ring2'				=>	'Ring 2',
	'trinket1'			=>	'Trinket 1',
	'trinket2'			=>	'Trinket 2',
	'back'				=>	'Back',
	'main_hand'			=>	'Main Hand',
	'off_hand'			=>	'Off Hand',
	'ranged'			=>	'Ranged',
	'tabard'			=>	'Tabard',
	
	// reputation
	'hated'				=>	'Hated',
	'hostile'			=>	'Hostile',
	'unfriendly'		=>	'Unfriendly',
	'neutral'			=>	'Neutral',
	'friendly'			=>	'Friendly',
	'honored'			=>	'Honored',
	'revered'			=>	'Revered',
	'exalted'			=>	'Exalted',
	
	/**
	 * Talent Tree Names
	 */
	// death knight
	'deathknight_1'		=>	'Blood',
	'deathknight_2'		=>	'Frost',
	'deathKnight_3'		=>	'Unholy',

	// druid
	'druid_1'			=>	'Balanced',
	'druid_2'			=>	'Feral',
	'druid_3'			=>	'Restoration',
	
	// hunter
	'hunter_1'			=>	'Beast Mastery',
	'hunter_2'			=>	'Marksmanship',
	'hunter_3'			=>	'Survival',
	
	// mage
	'mage_1'			=>	'Arcane',
	'mage_2'			=>	'Fire',
	'mage_3'			=>	'Frost',
	
	// paladin
	'paladin_1'			=>	'Holy',
	'paladin_2'			=>	'Protection',
	'paladin_3'			=>	'Retribution',
	
	// priest
	'priest_1'			=>	'Discipline',
	'priest_2'			=>	'Holy',
	'priest_3'			=>	'Shadow',
	
	// rogue
	'rogue_1'			=>	'Assassination',
	'rogue_2'			=>	'Combat',
	'rogue_3'			=>	'Subtlety',
	
	// shaman
	'shaman_1'			=>	'Elemental',
	'shaman_2'			=>	'Enhancement',
	'shaman_3'			=>	'Restoration',
	
	// warlock
	'warlock_1'			=>	'Affliction',
	'warlock_2'			=>	'Demonology',
	'warlock_3'			=>	'Destruction',
	
	// warrior
	'warrior_1'			=>	'Arms',
	'warrior_2'			=>	'Fury',
	'warrior_3'			=>	'Protection'
);
?>
