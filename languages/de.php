<?php
/**
* Wowhead Tooltips - German Language Pack
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

$lang_array = array(
	// general errors
	'notfound'			=>	'{type} "{name}" Nicht gefunden.',
	'curl_fail'			=>	'URL konnt nicht gefunden werden, weder mit cURL, oder fopen',	// failed to get XML
	'faction_fail'		=>	'Faction {name} ist mit diesem Skript unvereinbar.',			// incompatible tooltip
	
	// types
	'achievement'		=>	'Erfolg',
	'item'				=>	'Item',
	'item_icon'			=>	'Item',
	'itemset'			=>	'Itemset',
	'spell'				=>	'Zauber',
	'quest'				=>	'Quest',
	'profile'			=>	'Profile',
	'craft'				=>	'Berufe',
	'npc'				=>	'NPC',
	'zone'				=>	'Zone',
	'object'			=>	'Object',
	'faction'			=>	'Faction',
	'enchant'			=>	'Enchant',
	'title'				=>	'Titel',
	'event'				=>	'Veranstaltung',
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
	'armory_blocked'	=>	'Es gibt ein Problem.  Du wirst wahrscheinlich blockiert von Arsenal.',
	'char_not_found'	=>	'Character nicht gefunden.',
	'char_no_data'		=>	'Characterdaten nicht verfügbar.',
	'guild_not_found'	=>	'Es gibt ein Problem.  Die Gilde wurde nicht gefunden.',
	
	// misc area
	'achievements'		=>	'Erfolge',
	'achievement_pts'	=>	'Erfolgspunkte',
	'avg_ilevel'		=>	'Durchschnittliche ilvl',
	'lifetime_hk'		=>	'Lifetime HKs',
	
	// base stats
	'stamina'			=>	'Ausdauer',
	'intellect'			=>	'Intelligenz',
	'strength'			=>	'Stärke',
	'agility'			=>	'Beweglichkeit',
	'spirit'			=>	'Willenskraft',
	'armor'				=>	'Rüstung',
	
	// spell damage and crit
	'arcane_dmg'		=>	'Arkanschaden',
	'arcane_crit'		=>	'Arkane Crit Chance',
	'fire_dmg'			=>	'Feuerschaden',
	'fire_crit'			=>	'Feuer Critchance',
	'frost_dmg'			=>	'Frostschaden',
	'frost_crit'		=>	'Frost Critchance ',
	'shadow_dmg'		=>	'Schattenschaden',
	'shadow_crit'		=>	'Schatten Critchance ',
	'holy_dmg'			=>	'Heiligschaden',
	'holy_crit'			=>	'Heilig Critchance ',
	'nature_dmg'		=>	'Naturschaden',
	'nature_crit'		=>	'Natur Critchance ',
	
	// spell hit, haste, and penetration
	'spell_hit'			=>	'Zauber Trefferwertung',
	'haste'				=>	'Tempowertung',		// Zauber, Fernkampf oder Nahkampf
	'spell_pen'			=>	'Zauberdurchschalg',
	
	// melee shizzle
	'melee_main_dmg'	=>	'Nahkampf-Haupthand-Schaden ',
	'melee_main_dps'	=>	'Nahkampf-Haupthand-DpS ',
	'melee_main_speed'	=>	'Nahkampf-Haupthand-Geschwindigkeit ',
	'melee_off_dmg'		=>	'Nahkampf-Nebenhand-Schaden',
	'melee_off_dps'		=>	'Nahkampf-Nebenhand-DpS ',
	'melee_off_speed'	=>	'Nahkampf-Nebenhand-Geschwindigkeit ',
	'melee_power'		=>	'Nahkampf-Angriffskraft ',
	'melee_hit'			=>	'Nahkampf-Trefferwertung ',
	'melee_crit'		=>	'Nahkampf-Critchance ',
	'melee_expertise'	=>	'Waffenkundewertung ',
	
	// tanking stats
	'defense'			=>	'Verteidigung ',
	'parry_chance'		=>	'Pariere Chance',
	'dodge_chance'		=>	'Dodge Chance',
	'block_chance'		=>	'Block Chance',
	'resilience'		=>	'Abhärtung ',
	
	// healing and associated stats
	'healing'			=>	'Heilung',
	'mana_regen'		=>	'Mana Regen',
	'mana_regen_cast'	=>	'Mana Regen (im Kampf)',
	
	// ranged stats
	'ranged_dmg'		=>	'Fernkampf schaden',
	'ranged_dps'		=>	'Fernkampf DPS',
	'ranged_speed'		=>	'Fernkampf  Geschwindigkeit',
	'ranged_hit'		=>	'Fernkampf Trefferwertung',
	'ranged_crit'		=>	'Fernkampf Critchance',
	'ranged_power'		=>	'Fernkampf Angriffskraft ',
	
	// recruit
	'already_used'		=>	'Die Übersicht wurde schon mal verwendet, Bitte nur 1 mal pro Seite benutzen.',
	'invalid_xml'		=>	'Ungültige XML-Query zurückgegeben. Bitte versuchen Sie es erneut.',
	'untalented'		=>	'Zeichen versehen ist unbegabt.',
	'no_char_breakdown'	=>	'Character Talentbaum Aufteilung ist nicht vorhanden.',
	
	// gear slots
	'ammo'				=>	'Munition',
	'head'				=>	'Kopf',
	'neck'				=>	'Hals',
	'shoulder'			=>	'Schulter',
	'shirt'				=>	'Hemd',
	'chest' 			=> 	'Brust',
	'belt'				=> 	'Taille',
	'legs' 				=> 	'Beine',
	'feet' 				=> 	'Füße', 
	'wrist' 			=> 	'Handgelenke',
	'gloves' 			=> 	'Handschuhe',
	'ring1' 			=> 	'Ring 1',
	'ring2' 			=> 	'Ring 2',
	'trinket1' 			=> 	'Schmuckstück 1',
	'trinket2' 			=> 	'Schmuckstück 2',
	'back' 				=> 	'Rücken',
	'main_hand' 		=> 	'Waffenhand',
	'off_hand' 			=> 	'Schildhand/Nebenhand',
	'ranged' 			=> 	'Distanzwaffe',
	'tabard' 			=> 	'Wappenrock',
	
	// reputation
	'hated'				=>	'Hasserfüllt',
	'hostile'			=>	'Feindselig',
	'unfriendly'		=>	'Unfreundlich',
	'neutral'			=>	'Neutral',
	'friendly'			=>	'Freundlich',
	'honored'			=>	'Wohlwollend',
	'revered'			=>	'Respektvoll',
	'exalted'			=>	'Ehrfürchtig',
	
	/**
	 * Talent Tree Names
	 */
	// death knight
	'deathknight_1'		=>	'Brut',
	'deathknight_2'		=>	'Frost',
	'deathKnight_3'		=>	'Unheilig',

	// druid
	'druid_1'			=>	'Gleichgewicht',
	'druid_2'			=>	'Wilder Kampf',
	'druid_3'			=>	'Wiederherstellung',
	
	// hunter
	'hunter_1'			=>	'Tierherrschaft',
	'hunter_2'			=>	'Treffsicherheit',
	'hunter_3'			=>	'Überleben',
	
	// mage
	'mage_1'			=>	'Arkan',
	'mage_2'			=>	'Feuer',
	'mage_3'			=>	'Frost',
	
	// paladin
	'paladin_1'			=>	'Heilig',
	'paladin_2'			=>	'Schutz',
	'paladin_3'			=>	'Vergeltung',
	
	// priest
	'priest_1'			=>	'Disziplin',
	'priest_2'			=>	'Heilig',
	'priest_3'			=>	'Schatten',
	
	// rogue
	'rogue_1'			=>	'Meucheln',
	'rogue_2'			=>	'Kampf',
	'rogue_3'			=>	'Täuschung',
	
	// shaman
	'shaman_1'			=>	'Elementar',
	'shaman_2'			=>	'Verstärker',
	'shaman_3'			=>	'Wiederherstellung',
	
	// warlock
	'warlock_1'			=>	'Gebrechen',
	'warlock_2'			=>	'Dämonologie',
	'warlock_3'			=>	'Zerstörung',
	
	// warrior
	'warrior_1'			=>	'Waffen',
	'warrior_2'			=>	'Furor',
	'warrior_3'			=>	'Schutz',
);
?>
