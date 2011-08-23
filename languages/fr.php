<?php
/**
* Wowhead Tooltips - French Language Pack
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

// Translated By: Angitia from <Twisted> (http://www.twistedguild.info)
$lang_array = array(
   // general errors
   'notfound'			=>   '{type} "{name}" inconnu.',
   'curl_fail'         	=>   'Impossible d\'obtenir l\'URL, que ce soit avec cURL ou fopen',   // failed to get XML
   'faction_fail'		=>	 'Faction {name} est incompatible avec ce script.',	// incompatible tooltip
   
   // types
   'achievement'      	=>   'Hauts faits',
   'item'           	=>   'Objet',
   'item_icon'         	=>   'Objet',
   'itemset'         	=>   'Ensemble d\'objets',
   'spell'            	=>   'Sort',
   'quest'            	=>   'Quête',
   'profile'         	=>   'Fiche',
   'craft'            	=>   'Fabriqué',
   'npc'           		=>   'PNJ',
   'zone'				=>	 'Zone',
   'object'				=>	 'Objet',
   'faction'			=>	 'Faction',
   'enchant'			=>	 'Enchant',
   'title'				=>	 'Titre',
   'event'				=>	 'Événement',
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
   'armory_blocked'   	=>   'Une erreur a été rencontrée. Vous êtes très probablement bloqué par l\'armurerie.',
   'char_not_found'   	=>   'Personnage inconnu.',
   'char_no_data'      	=>   'Les données du personnage sont inaccessibles.',
   'guild_not_found'   	=>   'Une erreur a été rencontrée.  Il est très probable que cette guilde n\'existe pas.',
   
   // misc area
   'achievements'      	=>   'Hauts faits',
   'achievement_pts'   	=>   'Points de hauts faits',
   'avg_ilevel'      	=>   'Niveau d\'objet moyen',
   'lifetime_hk'		=>	'Lifetime HKs',
   
   // base stats
   'stamina'         	=>   'Endurance',
   'intellect'         	=>   'Intelligence',
   'strength'         	=>   'Force',
   'agility'         	=>   'Agilité',
   'spirit'         	=>   'Esprit',
   'armor'            	=>   'Armure',
   
   // spell damage and crit
   'arcane_dmg'      	=>   'Dégâts Arcane',
   'arcane_crit'      	=>   'Score de coup critique Arcane',
   'fire_dmg'         	=>   'Dégâts Feu',
   'fire_crit'         	=>   'Score de coup critique Feu',
   'frost_dmg'         	=>   'Dégâts Givre',
   'frost_crit'      	=>   'Score de coup critique Givre',
   'shadow_dmg'      	=>   'Dégâts Ombre',
   'shadow_crit'      	=>   'Score de coup critique Ombre',
   'holy_dmg'         	=>   'Dégâts Sacré',
   'holy_crit'         	=>   'Score de coup critique Sacré',
   'nature_dmg'      	=>   'Dégâts Nature',
   'nature_crit'      	=>   'Score de coup critique Nature',
   
   // spell hit, haste, and penetration
   'spell_hit'         	=>   'Score de toucher des sorts',
   'haste'            	=>   'Score de hâte',      // spell, ranged, or melee
   'spell_pen'         	=>   'Pénétration des sorts',
   
   // melee shizzle
   'melee_main_dmg'   	=>   'Dégâts en mêlée Main droite',
   'melee_main_dps'   	=>   'DPS en mêlée Main droite',
   'melee_main_speed'   =>   'Vitesse d\'attaque en mêlée Main droite',
   'melee_off_dmg'      =>   'Dégâts en mêlée Main gauche',
   'melee_off_dps'      =>   'DPS en mêlée Main gauche',
   'melee_off_speed'   	=>   'Vitesse d\'attaque en mêlée Main gauche',
   'melee_power'      	=>   'Puissance d\'attaque en mêlée',
   'melee_hit'         	=>   'Score de toucher en mêlée',
   'melee_crit'      	=>   'Score de coup critique en mêlée',
   'melee_expertise'   	=>   'Expertise en mêlée',
   
   // tanking stats
   'defense'         	=>   'Défense',
   'parry_chance'      	=>   'Score de parade',
   'dodge_chance'      	=>   'Score d\'esquive',
   'block_chance'      	=>   'Score de blocage',
   'resilience'      	=>   'Résilience',
   
   // healing and associated stats
   'healing'         	=>   'Soins',
   'mana_regen'      	=>   'Régénération de mana',
   'mana_regen_cast'   	=>   'Régénération de mana (Incantation)',
   
   // ranged stats
   'ranged_dmg'      	=>   'Dégâts à distance',
   'ranged_dps'      	=>   'DPS à distance',
   'ranged_speed'      	=>   'Vitesse d\'attaque à distance',
   'ranged_hit'      	=>   'Score de toucher à distance',
   'ranged_crit'      	=>   'Score de coup critique à distance',
   'ranged_power'      	=>   'Puissance d\'attaque à distance',
   
   // recruit
   'already_used'      	=>   'Un marqueur recrue est déjà présent sur cette page.  Un seul marqueur par page est autorisé.',
   'invalid_xml'      	=>   'La requête a renvoyé un XML invalide.  Essayez de nouveau.',
   'untalented'      	=>   'Le personnage fourni n\'a pas de talents séléctionnés.',
   'no_char_breakdown'	=>	 'Répartition des talents de caractères arbre n\'existe pas.',
   
   // gear slots
   'ammo'            	=>   'Projectiles',
   'head'            	=>   'Tête',
   'neck'            	=>   'Cou',
   'shoulder'         	=>   'Epaules',
   'shirt'            	=>   'Chemise',
   'chest'            	=>   'Torse',
   'belt'            	=>   'Ceinture',
   'legs'            	=>   'Jambes',
   'feet'            	=>   'Pieds',
   'wrist'            	=>   'Poignets',
   'gloves'         	=>   'Gants',
   'ring1'            	=>   'Anneau 1',
   'ring2'            	=>   'Anneau 2',
   'trinket1'         	=>   'Bijou 1',
   'trinket2'         	=>   'Bijou 2',
   'back'            	=>   'Dos',
   'main_hand'         	=>   'Main droite',
   'off_hand'         	=>   'Main gauche',
   'ranged'         	=>   'Armes à distance',
   'tabard'         	=>   'Tabard',
   
   // reputation
   'hated'            	=>   'Haï',
   'hostile'         	=>   'Hostile',
   'unfriendly'      	=>   'Inamical',
   'neutral'        	=>   'Neutre',
   'friendly'         	=>   'Amical',
   'honored'         	=>   'Honoré',
   'revered'         	=>   'Révéré',
   'exalted'         	=>   'Exalté',
   
   /**
    * Talent Tree Names
    */
   // death knight
   'deathknight_1'  	=>   'Sang',
   'deathknight_2'  	=>   'Givre',
   'deathKnight_3'		=>   'Impie',

   // druid
   'druid_1'      		=>   'Equilibre',
   'druid_2'      		=>   'Combat farouche',
   'druid_3'      		=>   'Restauration',
   
   // hunter
   'hunter_1'         	=>   'Maîtrise des bêtes',
   'hunter_2'  			=>   'Précision',
   'hunter_3'   		=>   'Survie',
   
   // mage
   'mage_1'      		=>   'Arcane',
   'mage_2'         	=>   'Feu',
   'mage_3'      		=>   'Givre',
   
   // paladin
   'paladin_1'     		=>   'Sacré',
   'paladin_2'      	=>   'Protection',
   'paladin_3'      	=>   'Vindicte',
   
   // priest
   'priest_1'      		=>   'Discipline',
   'priest_2'      		=>   'Sacré',
   'priest_3'      		=>   'Ombre',
   
   // rogue
   'rogue_1'         	=>   'Assassinat',
   'rogue_2'      		=>   'Combat',
   'rogue_3'   			=>   'Finesse',
   
   // shaman
   'shaman_1'   		=>   'Elémentaire',
   'shaman_2'   		=>   'Amélioration',
   'shaman_3'      		=>   'Restauration',
   
   // warlock
   'warlock_1'      	=>   'Affliction',
   'warlock_2'      	=>   'Démonologie',
   'warlock_3'   		=>   'Destruction',
   
   // warrior
   'warrior_1'      	=>   'Armes',
   'warrior_2'      	=>   'Fureur',
   'warrior_3'      	=>   'Protection'
);
?>
